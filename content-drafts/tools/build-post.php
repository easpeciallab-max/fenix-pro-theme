<?php
/**
 * เตรียมข้อมูลบทความจากไฟล์ร่าง เพื่อส่งขึ้น WordPress
 *
 * โหมดอ่านช่องรูป:
 *   php build-post.php --slots <draft.html>
 *   -> พิมพ์ JSON ของช่องรูปทุกจุด (file / alt / caption / search)
 *
 * โหมดสร้างเนื้อหาสุดท้าย:
 *   php build-post.php --build <draft.html> --images=<images.json> --title="..." --slug=... --date=... --cat=7 [--cover=<file>]
 *   -> พิมพ์ JSON พร้อมส่งให้ REST ของ WordPress (เขียนลงไฟล์แล้วใช้ curl --data-binary @ไฟล์)
 *
 * images.json = { "<ชื่อไฟล์ในช่องรูป>": { "url": "...", "id": 123, "width": 1200, "height": 800 } }
 *
 * ไม่ต่อเน็ตเอง (PHP บนเครื่องนี้ไม่มี curl/openssl) การอัปโหลดทำด้วย curl ข้างนอก
 */

if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $s, $e = null ) { return (int) preg_match_all( '/./u', (string) $s ); }
}

$args = array();
foreach ( array_slice( $argv, 1 ) as $a ) {
	if ( preg_match( '/^--([a-z]+)=(.*)$/s', $a, $m ) ) {
		$args[ $m[1] ] = $m[2];
	} elseif ( preg_match( '/^--([a-z]+)$/', $a, $m ) ) {
		$args[ $m[1] ] = true;
	} else {
		$args['_'][] = $a;
	}
}

$draft = isset( $args['_'][0] ) ? $args['_'][0] : '';
if ( ! $draft || ! file_exists( $draft ) ) {
	fwrite( STDERR, "ไม่พบไฟล์ร่าง\n" );
	exit( 1 );
}

$raw   = file_get_contents( $draft );
$front = array();
$body  = $raw;
if ( preg_match( '/^---\R(.*?)\R---\R(.*)$/s', $raw, $m ) ) {
	$body = $m[2];
	foreach ( preg_split( '/\R/', $m[1] ) as $line ) {
		if ( preg_match( '/^([a-z_]+):\s*(.*)$/', $line, $kv ) ) {
			$front[ $kv[1] ] = trim( $kv[2] );
		}
	}
}

preg_match_all( '/<!--\s*fenix-image:\s*(\{.*?\})\s*-->/s', $body, $slots, PREG_SET_ORDER );

/* ---------- โหมดอ่านช่องรูป ---------- */
if ( isset( $args['slots'] ) ) {
	$out = array();
	foreach ( $slots as $s ) {
		$d = json_decode( $s[1], true );
		if ( is_array( $d ) ) {
			$out[] = $d;
		}
	}
	echo json_encode(
		array(
			'draft'     => basename( $draft ),
			'cover_alt' => isset( $front['cover_alt'] ) ? $front['cover_alt'] : '',
			'slots'     => $out,
		),
		JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
	) . "\n";
	exit( 0 );
}

/* ---------- โหมดสร้างเนื้อหาสุดท้าย ---------- */
if ( empty( $args['images'] ) || ! file_exists( $args['images'] ) ) {
	fwrite( STDERR, "ต้องมี --images=<images.json>\n" );
	exit( 1 );
}

$images = json_decode( file_get_contents( $args['images'] ), true );
if ( ! is_array( $images ) ) {
	fwrite( STDERR, "images.json อ่านไม่ได้\n" );
	exit( 1 );
}

$missing = array();
$content = preg_replace_callback(
	'/<!--\s*fenix-image:\s*(\{.*?\})\s*-->/s',
	function ( $m ) use ( $images, &$missing ) {
		$d = json_decode( $m[1], true );
		if ( ! is_array( $d ) || empty( $d['file'] ) ) {
			return '';
		}
		$key = $d['file'];
		if ( empty( $images[ $key ]['url'] ) ) {
			$missing[] = $key;
			return '';
		}
		$img = $images[ $key ];
		$fig = '<figure class="wp-block-image size-large"><img src="' . htmlspecialchars( $img['url'], ENT_QUOTES, 'UTF-8' ) . '"'
			. ' alt="' . htmlspecialchars( $d['alt'], ENT_QUOTES, 'UTF-8' ) . '"'
			. ( isset( $img['id'] ) ? ' class="wp-image-' . (int) $img['id'] . '"' : '' )
			. ( isset( $img['width'] ) ? ' width="' . (int) $img['width'] . '" height="' . (int) $img['height'] . '"' : '' )
			. ' loading="lazy" decoding="async">';
		if ( ! empty( $d['caption'] ) ) {
			$fig .= '<figcaption class="wp-element-caption">' . htmlspecialchars( $d['caption'], ENT_QUOTES, 'UTF-8' ) . '</figcaption>';
		}
		return $fig . '</figure>';
	},
	$body
);

if ( $missing ) {
	fwrite( STDERR, 'ยังไม่มีรูปสำหรับ: ' . implode( ', ', $missing ) . "\n" );
	exit( 1 );
}

$content = trim( $content );

$payload = array(
	'title'   => isset( $args['title'] ) ? $args['title'] : '',
	'slug'    => isset( $args['slug'] ) ? $args['slug'] : '',
	'content' => $content,
	'excerpt' => isset( $front['excerpt'] ) ? $front['excerpt'] : '',
	'status'  => isset( $args['status'] ) ? $args['status'] : 'future',
	'meta'    => array(
		'_yoast_wpseo_title'    => isset( $front['seo_title'] ) ? $front['seo_title'] . ' %%sep%% %%sitename%%' : '',
		'_yoast_wpseo_metadesc' => isset( $front['meta_description'] ) ? $front['meta_description'] : '',
	),
);

if ( ! empty( $args['date'] ) ) {
	$payload['date'] = $args['date'];
}
if ( ! empty( $args['cat'] ) ) {
	$payload['categories'] = array_map( 'intval', explode( ',', $args['cat'] ) );
}
if ( ! empty( $args['cover'] ) && ! empty( $images[ $args['cover'] ]['id'] ) ) {
	$payload['featured_media'] = (int) $images[ $args['cover'] ]['id'];
}
if ( ! empty( $args['keyword'] ) ) {
	$payload['meta']['_yoast_wpseo_focuskw'] = $args['keyword'];
}

$out = isset( $args['out'] ) ? $args['out'] : '';
$json = json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

if ( $out ) {
	file_put_contents( $out, $json );
	echo 'เขียน ' . basename( $out ) . ' · ' . strlen( $json ) . " bytes · รูป " . count( $images ) . " ไฟล์\n";
	exit( 0 );
}

echo $json;
