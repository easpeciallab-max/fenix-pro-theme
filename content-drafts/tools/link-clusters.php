<?php
/**
 * วางลิงก์ขึ้นเสาหลักให้บทลูก และสร้างส่วน "อ่านต่อในกลุ่มนี้" ให้เสาหลัก
 * ใช้ไฟล์เนื้อหาที่ดึงมาจาก REST แล้วเขียน payload ของโพสต์ที่ต้องแก้
 */
$dir = $argv[1];
$clusters = json_decode( file_get_contents( 'content-drafts/clusters.json' ), true );

$posts = array();
foreach ( glob( "$dir/content-*.json" ) as $file ) {
	$j = json_decode( file_get_contents( $file ), true );
	if ( ! is_array( $j ) ) { continue; }
	foreach ( $j as $r ) {
		if ( empty( $r['slug'] ) ) { continue; }
		$posts[ $r['slug'] ] = array(
			'id'      => $r['id'],
			'title'   => html_entity_decode( $r['title']['raw'], ENT_QUOTES, 'UTF-8' ),
			'content' => $r['content']['raw'],
		);
	}
}

$url = function ( $slug ) { return "https://fenixpro-th.com/$slug/"; };

/* แทรกบล็อกก่อนหัวข้อแหล่งข้อมูล ถ้าไม่มีก็ก่อนคำถามที่พบบ่อย ถ้าไม่มีอีกก็ต่อท้าย */
$insert = function ( $content, $block ) {
	foreach ( array( '<h2 id="sources"', '<h2 id="faq"' ) as $marker ) {
		$pos = stripos( $content, $marker );
		if ( false !== $pos ) {
			return substr( $content, 0, $pos ) . $block . "\n\n" . substr( $content, $pos );
		}
	}
	return rtrim( $content ) . "\n\n" . $block;
};

$changed = array();
$stats   = array( 'up' => 0, 'up_skip' => 0, 'down' => 0, 'missing' => 0 );

/* ---------- 1) ลิงก์ขึ้นเสาหลัก ---------- */
foreach ( $clusters['pillars'] as $pillar => $info ) {
	foreach ( $info['children'] as $child ) {
		if ( ! isset( $posts[ $child ] ) ) { $stats['missing']++; continue; }
		$c = $posts[ $child ]['content'];
		if ( false !== strpos( $c, $url( $pillar ) ) ) { $stats['up_skip']++; continue; }
		$para  = '<p class="cluster-up">ภาพรวมทั้งกลุ่มอยู่ที่ <a href="' . $url( $pillar ) . '">' . $info['anchor'] . '</a></p>';
		$block = false !== strpos( $c, '<!-- wp:' ) ? '<!-- wp:paragraph {"className":"cluster-up"} -->' . $para . '<!-- /wp:paragraph -->' : $para;
		$posts[ $child ]['content'] = $insert( $c, $block );
		$changed[ $child ] = true;
		$stats['up']++;
	}
}

/* ---------- 2) ส่วนอ่านต่อในกลุ่มของเสาหลัก ---------- */
foreach ( $clusters['pillars'] as $pillar => $info ) {
	if ( ! isset( $posts[ $pillar ] ) || ! $info['children'] ) { continue; }
	$c0    = $posts[ $pillar ]['content'];
	$items = '';
	foreach ( $info['children'] as $child ) {
		if ( ! isset( $posts[ $child ] ) ) { continue; }
		$items .= '<li><a href="' . $url( $child ) . '">' . htmlspecialchars( $posts[ $child ]['title'], ENT_QUOTES, 'UTF-8' ) . '</a></li>' . "\n";
	}
	if ( '' === $items ) { continue; }

	$wrap  = false !== strpos( $c0, '<!-- wp:' );
	$block = ( $wrap ? "<!-- wp:html -->
" : "" ) . "<!-- fenix-cluster-links -->
"
		. '<h2 id="cluster">อ่านต่อในกลุ่มนี้</h2>' . "\n"
		. '<ul class="cluster-list">' . "\n" . $items . '</ul>' . "\n"
		. "<!-- /fenix-cluster-links -->" . ( $wrap ? "
<!-- /wp:html -->" : "" );

	$c = $posts[ $pillar ]['content'];
	if ( false !== strpos( $c, '<!-- fenix-cluster-links -->' ) ) {
		$new = preg_replace( '#<!-- fenix-cluster-links -->.*?<!-- /fenix-cluster-links -->#s', $block, $c );
	} else {
		$new = $insert( $c, $block );
	}
	if ( $new !== $c ) {
		$posts[ $pillar ]['content'] = $new;
		$changed[ $pillar ] = true;
		$stats['down']++;
	}
}

@mkdir( "$dir/cluster", 0777, true );
foreach ( array_keys( $changed ) as $slug ) {
	file_put_contents(
		"$dir/cluster/" . $posts[ $slug ]['id'] . '.json',
		json_encode( array( 'content' => $posts[ $slug ]['content'] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}

echo "เติมลิงก์ขึ้นเสา {$stats['up']} บท (มีอยู่แล้ว {$stats['up_skip']}) · ทำส่วนอ่านต่อให้เสาหลัก {$stats['down']} บท · ต้องอัปเดตรวม " . count( $changed ) . " โพสต์\n";
