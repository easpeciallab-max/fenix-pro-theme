#!/usr/bin/env bash
# อัปรูปของบทความชุดหนึ่งขึ้น WordPress แล้วรวมเข้าแผนที่รูปกลาง
#
#   bash content-drafts/tools/upload-images.sh <โฟลเดอร์ที่มีไฟล์ .webp> <โฟลเดอร์ทำงาน>
#
# โฟลเดอร์ทำงานต้องมี images.json (แผนที่รูปสะสม) หรือจะสร้างใหม่ให้
# alt ของแต่ละรูปดึงจากช่องรูปในไฟล์ร่างโดยอัตโนมัติ (ต้องส่ง ALT_JSON มาให้)
#
# ต้องมี ~/.fenix-wp.env (ดู memory: wp-api-access)
set -u

SRC="${1:?ต้องบอกโฟลเดอร์รูป}"
WORK="${2:?ต้องบอกโฟลเดอร์ทำงาน}"
ALT_JSON="${3:-$WORK/alts.json}"
PHP="/c/Users/THANAWUT HR/AppData/Local/Microsoft/WinGet/Packages/PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe/php.exe"

cd ~ || exit 1
set -a
. <(sed -e 's/^\xEF\xBB\xBF//' .fenix-wp.env | tr -d '\r' | sed -E "s/^([A-Z_]+)=(.*)$/\1='\2'/")
set +a
AUTH=$(printf '%s:%s' "$WP_USER" "$WP_APP_PASSWORD" | base64 -w0)

cd "$SRC" || exit 1
IDS=""
for f in *.webp; do
  resp=$(curl -s -X POST "https://fenixpro-th.com/wp-json/wp/v2/media?probe=$RANDOM" \
    -H "X-Authorization: Basic $AUTH" \
    -H "Content-Disposition: attachment; filename=\"$f\"" \
    -H "Content-Type: image/webp" --data-binary "@$f")
  id=$(printf '%s' "$resp" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)
  if [ -z "$id" ]; then
    echo "อัปไม่ขึ้น: $f"
    continue
  fi
  IDS="$IDS,$id"
  printf '%s ' "$id"
done
echo
IDS="${IDS#,}"
[ -z "$IDS" ] && { echo "ไม่มีรูปที่อัปสำเร็จ"; exit 1; }

curl -s "https://fenixpro-th.com/wp-json/wp/v2/media?include=$IDS&per_page=50&_fields=id,source_url,media_details&probe=$RANDOM" \
  -H "X-Authorization: Basic $AUTH" -o "$WORK/media-new.json"

"$PHP" -r '
$work = $argv[1];
$alts = file_exists( $argv[2] ) ? json_decode( file_get_contents( $argv[2] ), true ) : array();
$map  = file_exists( "$work/images.json" ) ? json_decode( file_get_contents( "$work/images.json" ), true ) : array();
@mkdir( "$work/payload" );
foreach ( json_decode( file_get_contents( "$work/media-new.json" ), true ) as $it ) {
	$file = basename( $it["source_url"] );
	$map[ $file ] = array( "id" => $it["id"], "url" => $it["source_url"], "width" => $it["media_details"]["width"], "height" => $it["media_details"]["height"] );
	file_put_contents( "$work/payload/{$it["id"]}.json", json_encode( array(
		"alt_text" => isset( $alts[ $file ] ) ? $alts[ $file ] : "",
		"title"    => preg_replace( "/\.webp$/", "", $file ),
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	echo $it["id"] . "\n";
}
file_put_contents( "$work/images.json", json_encode( $map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
fwrite( STDERR, "แผนที่รูปรวม " . count( $map ) . " ไฟล์\n" );
' "$WORK" "$ALT_JSON" | while read -r id; do
  code=$(curl -s -o /dev/null -w '%{http_code}' -X POST "https://fenixpro-th.com/wp-json/wp/v2/media/$id?probe=$RANDOM" \
    -H "X-Authorization: Basic $AUTH" -H 'Content-Type: application/json' --data-binary "@$WORK/payload/$id.json")
  printf '%s:%s ' "$id" "$code"
done
echo
