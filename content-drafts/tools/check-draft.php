<?php
/**
 * ตรวจไฟล์ร่างบทความก่อนขึ้นเว็บ
 *
 * php content-drafts/tools/check-draft.php content-drafts/articles/051-xxx.html [...]
 *
 * ตรวจตามคู่มือ content-drafts/writing-brief.md
 * ไม่ยุ่งกับเว็บจริง อ่านไฟล์อย่างเดียว
 */


/* PHP บนเครื่องนี้ไม่มี mbstring · ใส่ตัวสำรองไว้ให้ใช้ได้เหมือนกัน */
if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $s, $e = null ) { return (int) preg_match_all( '/./u', (string) $s ); }
}
if ( ! function_exists( 'mb_strpos' ) ) {
	function mb_strpos( $h, $n, $o = 0, $e = null ) { return strpos( (string) $h, (string) $n, $o ); }
}
$files = array_slice( $argv, 1 );
if ( ! $files ) {
	fwrite( STDERR, "ใช้: php check-draft.php <ไฟล์ร่าง> [ไฟล์ร่าง...]\n" );
	exit( 1 );
}

/** คำที่ห้ามใช้เด็ดขาดในบทความ */
$banned = array(
	'การันตี', 'รับประกันกำไร', 'รับประกันผลกำไร', 'ชัวร์', 'รวยเร็ว',
	'ไม่มีความเสี่ยง', 'ไม่ขาดทุน', 'กำไรแน่นอน', 'ปลอดภัย 100%', 'เสี่ยง 0',
);

$total_fail = 0;

foreach ( $files as $file ) {
	echo "\n=== " . basename( $file ) . " ===\n";

	if ( ! file_exists( $file ) ) {
		echo "FAIL: ไม่พบไฟล์\n";
		$total_fail++;
		continue;
	}

	$raw  = file_get_contents( $file );
	$fail = 0;

	$say = function ( $ok, $msg ) use ( &$fail ) {
		if ( $ok ) {
			echo "  ok   $msg\n";
			return;
		}
		echo "  FAIL $msg\n";
		$fail++;
	};

	/* ---------- front-matter ---------- */
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

	$say( ! empty( $front ), 'มี front-matter' );
	foreach ( array( 'seo_title', 'meta_description', 'excerpt', 'cover_alt' ) as $key ) {
		$say( ! empty( $front[ $key ] ), "front-matter มี $key" );
	}

	if ( ! empty( $front['seo_title'] ) ) {
		$len = mb_strlen( $front['seo_title'], 'UTF-8' );
		$say( $len <= 60, "seo_title ยาว $len ตัวอักษร (ไม่เกิน 60)" );
	}
	if ( ! empty( $front['meta_description'] ) ) {
		$len = mb_strlen( $front['meta_description'], 'UTF-8' );
		$say( $len >= 110 && $len <= 160, "meta_description ยาว $len ตัวอักษร (110-160)" );
	}

	/* ---------- โครงเนื้อหา ---------- */
	$text  = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( strip_tags( $body ), ENT_QUOTES, 'UTF-8' ) ) );
	$chars = mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
	/* ภาษาไทยนับคำยาก ใช้ตัวอักษรหารด้วย 5 เป็นค่าประมาณจำนวนคำ */
	$words = (int) round( $chars / 5 );
	$say( $words >= 1200, "ความยาวประมาณ $words คำ (อย่างน้อย 1,200)" );

	preg_match_all( '/<h2[^>]*>/i', $body, $h2 );
	$say( count( $h2[0] ) >= 5, 'มีหัวข้อ H2 ' . count( $h2[0] ) . ' หัวข้อ (อย่างน้อย 5)' );

	preg_match_all( '/<h2[^>]*\sid="([^"]+)"/i', $body, $ids );
	$say( count( $ids[1] ) === count( $h2[0] ), 'หัวข้อ H2 ทุกอันมี id' );
	$say( count( $ids[1] ) === count( array_unique( $ids[1] ) ), 'id ของหัวข้อไม่ซ้ำกัน' );

	$say( false !== stripos( $body, '<nav' ), 'มีสารบัญในเนื้อหา' );

	/* ลิงก์ในสารบัญต้องชี้ไปหัวข้อที่มีจริง */
	if ( preg_match( '~<nav[^>]*>(.*?)</nav>~is', $body, $navm ) ) {
		preg_match_all( '/href="#([^"]+)"/', $navm[1], $anchors );
		$missing = array_diff( $anchors[1], $ids[1] );
		$say( empty( $missing ), 'ลิงก์ในสารบัญชี้ไปหัวข้อที่มีจริงทุกอัน' . ( $missing ? ' (หาย: ' . implode( ', ', $missing ) . ')' : '' ) );
	}

	$say( false !== stripos( $body, '<aside' ), 'มีกล่องเตือน <aside>' );
	$say( false !== stripos( $body, '<table' ), 'มีตารางอย่างน้อย 1 ตาราง' );
	$say( false !== strpos( $body, 'คำเตือนความเสี่ยง' ), 'มีย่อหน้าคำเตือนความเสี่ยง' );
	$say( false !== strpos( $body, 'risk-disclosure' ), 'ลิงก์ไปหน้าคำเตือนความเสี่ยงฉบับเต็ม' );
	$say( (bool) preg_match( '/<small>\s*ตรวจทานล่าสุด/u', $body ), 'มีบรรทัดตรวจทานล่าสุด' );
	$say( false !== strpos( $body, 'แหล่งข้อมูล' ), 'มีหัวข้อแหล่งข้อมูล' );

	/* ---------- รูป ---------- */
	preg_match_all( '/<!--\s*fenix-image:\s*(\{.*?\})\s*-->/s', $body, $slots );
	$n_slots = count( $slots[1] );
	$say( $n_slots >= 2 && $n_slots <= 4, "ช่องรูป $n_slots จุด (2-4 จุด)" );

	$bad_json = 0;
	$files_in = array();
	foreach ( $slots[1] as $json ) {
		$data = json_decode( $json, true );
		if ( ! is_array( $data ) || empty( $data['file'] ) || empty( $data['alt'] ) || empty( $data['search'] ) ) {
			$bad_json++;
			continue;
		}
		$files_in[] = $data['file'];
	}
	$say( 0 === $bad_json, 'ข้อมูลช่องรูปครบ (file / alt / search)' );
	$say( count( $files_in ) === count( array_unique( $files_in ) ), 'ชื่อไฟล์รูปไม่ซ้ำกัน' );
	$say( false === stripos( $body, '<img' ) && false === stripos( $body, '<figure' ), 'ไม่มีแท็ก img หรือ figure เขียนเอง' );

	/* ---------- ลิงก์ ---------- */
	preg_match_all( '~href="https://fenixpro-th\.com/([^"#]*)"~', $body, $internal );
	$paths    = array_map( function ( $p ) { return trim( $p, '/' ); }, $internal[1] );
	$money    = array_intersect( $paths, array( 'go', 'pricing', 'backtest', 'forward-test' ) );
	$articles = array_filter( $paths, function ( $p ) {
		return '' !== $p && ! in_array( $p, array( 'go', 'pricing', 'backtest', 'forward-test', 'risk-disclosure', 'about', 'privacy-policy', 'terms' ), true );
	} );
	$say( count( array_unique( $articles ) ) >= 3, 'ลิงก์ไปบทความอื่นในเว็บ ' . count( array_unique( $articles ) ) . ' เรื่อง (อย่างน้อย 3)' );
	$say( count( $money ) >= 1, 'มีลิงก์ไปหน้าขายอย่างน้อย 1 ลิงก์' );

	preg_match_all( '~<a\s[^>]*href="(https?://(?!fenixpro-th\.com)[^"]+)"[^>]*>~i', $body, $external, PREG_SET_ORDER );
	$say( count( $external ) >= 3, 'แหล่งข้อมูลภายนอก ' . count( $external ) . ' ลิงก์ (อย่างน้อย 3)' );

	$no_rel = array();
	foreach ( $external as $tag ) {
		if ( false === stripos( $tag[0], 'nofollow' ) || false === stripos( $tag[0], 'noopener' ) ) {
			$no_rel[] = $tag[1];
		}
	}
	$say( empty( $no_rel ), 'ลิงก์ออกนอกเว็บใส่ rel="nofollow noopener" ครบ' . ( $no_rel ? ' (ขาด: ' . implode( ' ', array_slice( $no_rel, 0, 2 ) ) . ')' : '' ) );

	/* ---------- คำต้องห้าม ---------- */
	/* คำพวกนี้ใช้ได้ถ้าอยู่ในรูปปฏิเสธ เช่น "ไม่ได้รับประกันผลกำไร" ซึ่งเป็นคำเตือน ไม่ใช่คำโฆษณา */
	$hits = array();
	foreach ( $banned as $word ) {
		$offset = 0;
		while ( false !== ( $pos = strpos( $text, $word, $offset ) ) ) {
			$before = substr( $text, max( 0, $pos - 90 ), min( 90, $pos ) );
			/* substr อาจตัดกลางตัวอักษรไทย ตัดไบต์ที่ค้างทิ้งก่อน ไม่งั้น regex แบบ /u ใช้ไม่ได้ */
			$before = preg_replace( '/^[\x80-\xBF]+/', '', $before );
			if ( ! preg_match( '/(ไม่|ไม่ได้|ไม่มี|ไม่ใช่|ห้าม|อย่า)\s*[^\s]{0,12}$/u', $before ) ) {
				$hits[] = $word;
				break;
			}
			$offset = $pos + strlen( $word );
		}
	}
	$say( empty( $hits ), 'ไม่มีคำโฆษณาเกินจริง' . ( $hits ? ' (เจอ: ' . implode( ', ', $hits ) . ')' : '' ) );

	/* ตัวเลขผลงานของ FENIX ห้ามอยู่ในบทความชุดนี้ */
	$fenix_numbers = preg_match_all( '/FENIX[^.]{0,80}?[+\-]?\d[\d,.]*\s*(%|USD|USC)/u', $text, $fm );
	$say( 0 === $fenix_numbers, 'ไม่มีตัวเลขผลงานของ FENIX ในเนื้อหา' . ( $fenix_numbers ? ' (เจอ: ' . implode( ' / ', array_slice( $fm[0], 0, 3 ) ) . ')' : '' ) );

	echo $fail ? "  -> ไม่ผ่าน $fail ข้อ\n" : "  -> ผ่านทุกข้อ\n";
	$total_fail += $fail;
}

echo "\n" . ( $total_fail ? "รวมไม่ผ่าน $total_fail ข้อ\n" : "ผ่านทุกไฟล์\n" );
exit( $total_fail ? 1 : 0 );
