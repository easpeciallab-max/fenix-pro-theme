<?php
/**
 * ลงบทความทีละชุดตามคิวในแผน
 *
 *   php content-drafts/tools/publish-batch.php --work=<โฟลเดอร์ทำงาน> --slugs=a,b,c [--dry]
 *
 * ทำสามอย่าง
 *   1) สร้าง payload ของแต่ละบทความจากไฟล์ร่าง + แผนที่รูป (images.json)
 *   2) พิมพ์คำสั่ง curl ที่ต้องรันเพื่อส่งขึ้นเว็บ (สคริปต์นี้ไม่ต่อเน็ตเอง เพราะ PHP เครื่องนี้ไม่มี curl/openssl)
 *   3) เมื่อส่งขึ้นเว็บแล้ว ใช้ --done=<slug>:<id> เพื่อบันทึกสถานะลง plan-queue.json
 *
 * ข้อมูลบทความ (ชื่อเรื่อง วันที่ หมวดหมู่ คีย์เวิร์ด) อ่านจาก content-drafts/plan-queue.json
 */

$args = array();
foreach ( array_slice( $argv, 1 ) as $a ) {
	if ( preg_match( '/^--([a-z]+)=(.*)$/s', $a, $m ) ) {
		$args[ $m[1] ] = $m[2];
	} elseif ( preg_match( '/^--([a-z]+)$/', $a, $m ) ) {
		$args[ $m[1] ] = true;
	}
}

$root  = dirname( __DIR__ );
$queue = json_decode( file_get_contents( "$root/plan-queue.json" ), true );

/* ---------- บันทึกสถานะหลังลงเสร็จ ---------- */
if ( ! empty( $args['done'] ) ) {
	$changed = 0;
	foreach ( explode( ',', $args['done'] ) as $pair ) {
		list( $slug, $id ) = array_pad( explode( ':', $pair ), 2, '' );
		foreach ( $queue as &$row ) {
			if ( $row['slug'] === $slug ) {
				$row['status'] = 'done';
				$row['post_id'] = (int) $id;
				$changed++;
			}
		}
		unset( $row );
	}
	file_put_contents( "$root/plan-queue.json", json_encode( $queue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
	$left = count( array_filter( $queue, function ( $r ) { return 'done' !== $r['status']; } ) );
	echo "บันทึกแล้ว $changed บท · เหลืออีก $left บท\n";
	exit( 0 );
}

/* ---------- แสดงคิวถัดไป ---------- */
if ( ! empty( $args['next'] ) ) {
	$n = (int) $args['next'];
	$i = 0;
	foreach ( $queue as $row ) {
		if ( 'pending' !== $row['status'] ) {
			continue;
		}
		echo sprintf( "%s | %s | cat%d | %s | %s | %s\n", $row['n'], substr( $row['date'], 0, 10 ), $row['cat'], $row['slug'], $row['kw'], $row['title'] );
		if ( ++$i >= $n ) {
			break;
		}
	}
	exit( 0 );
}

/* ---------- สร้าง payload ---------- */
$work  = isset( $args['work'] ) ? $args['work'] : '';
$slugs = isset( $args['slugs'] ) ? explode( ',', $args['slugs'] ) : array();

if ( ! $work || ! $slugs ) {
	fwrite( STDERR, "ใช้: --work=<โฟลเดอร์> --slugs=a,b,c | --next=6 | --done=slug:id\n" );
	exit( 1 );
}

$images = json_decode( file_get_contents( "$work/images.json" ), true );
$php    = escapeshellarg( PHP_BINARY );
$fails  = 0;

foreach ( $slugs as $slug ) {
	$row = null;
	foreach ( $queue as $r ) {
		if ( $r['slug'] === $slug ) {
			$row = $r;
			break;
		}
	}
	if ( ! $row ) {
		fwrite( STDERR, "ไม่มี $slug ในแผน\n" );
		$fails++;
		continue;
	}

	$draft = glob( "$root/articles/{$row['n']}-*.html" );
	if ( ! $draft ) {
		fwrite( STDERR, "ไม่มีไฟล์ร่างของ $slug (ลำดับ {$row['n']})\n" );
		$fails++;
		continue;
	}

	/* รูปปก = ช่องรูปแรกของบทความ */
	preg_match( '/<!--\s*fenix-image:\s*(\{.*?\})\s*-->/s', file_get_contents( $draft[0] ), $m );
	$first = $m ? json_decode( $m[1], true ) : null;
	$cover = $first && ! empty( $first['file'] ) ? $first['file'] : '';

	if ( $cover && empty( $images[ $cover ] ) ) {
		fwrite( STDERR, "ยังไม่มีรูปปกของ $slug ($cover)\n" );
		$fails++;
		continue;
	}

	$title = preg_replace( '/^\*\*\[หลัก\]\*\*\s*/u', '', $row['title'] );
	$cmd   = sprintf(
		'%s %s --build %s --images=%s --title=%s --slug=%s --date=%s --cat=%d --status=future --keyword=%s --cover=%s --out=%s',
		$php,
		escapeshellarg( __DIR__ . '/build-post.php' ),
		escapeshellarg( $draft[0] ),
		escapeshellarg( "$work/images.json" ),
		escapeshellarg( $title ),
		escapeshellarg( $slug ),
		escapeshellarg( $row['date'] ),
		$row['cat'],
		escapeshellarg( $row['kw'] ),
		escapeshellarg( $cover ),
		escapeshellarg( "$work/post-$slug.json" )
	);

	$out = array();
	$rc  = 0;
	exec( $cmd . ' 2>&1', $out, $rc );
	echo ( 0 === $rc ? 'ok   ' : 'พลาด ' ) . $slug . ' · ' . implode( ' ', $out ) . "\n";
	if ( 0 !== $rc ) {
		$fails++;
	}
}

echo $fails ? "\nมีปัญหา $fails บท\n" : "\nสร้าง payload ครบ · ส่งขึ้นเว็บด้วย curl ได้เลย\n";
exit( $fails ? 1 : 0 );
