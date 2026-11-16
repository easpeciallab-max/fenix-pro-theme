<?php
/** Run with: php tests/guide-pages.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( ...$args ) {}
function get_template_directory() { return dirname( __DIR__ ) . '/fenix-pro'; }
function get_template_directory_uri() { return 'https://example.test/wp-content/themes/fenix-pro'; }
function get_theme_mod( $key, $default = false ) {
	global $theme_mods;
	return array_key_exists( $key, $theme_mods ) ? $theme_mods[ $key ] : $default;
}
function home_url( $path = '' ) { return 'https://example.test' . $path; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return esc_attr( $value ); }

require get_template_directory() . '/functions.php';

function check( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
	echo "PASS: $message\n";
}

$guides = fenix_guides();
$slugs  = array_keys( $guides );

check( 8 === count( $guides ), 'มีคู่มือครบ 8 ขั้นตอน' );

/* ---- ทุกหน้าในเมนูต้องอยู่ในลำดับคู่มือ และไม่มีหน้าแปลกปลอม ---- */
$menu_slugs = array();
foreach ( fenix_menu_tree() as $item ) {
	if ( empty( $item['children'] ) || 'วิธีใช้งาน' !== $item['label'] ) {
		continue;
	}
	foreach ( $item['children'] as $child ) {
		$menu_slugs[] = trim( $child['url'], '/' );
	}
}
sort( $menu_slugs );
$sorted = $slugs;
sort( $sorted );
check( $menu_slugs === $sorted, 'ลำดับคู่มือตรงกับหน้าที่อยู่ในเมนู "วิธีใช้งาน" ครบทุกหน้า' );

/* ---- ข้อมูลครบทุกหน้า ---- */
$missing = array();
foreach ( $guides as $slug => $guide ) {
	foreach ( array( 'label', 'short', 'time', 'level' ) as $key ) {
		if ( '' === trim( (string) $guide[ $key ] ) ) {
			$missing[] = "$slug.$key";
		}
	}
	if ( count( $guide['prep'] ) < 1 ) {
		$missing[] = "$slug.prep";
	}
	if ( preg_match_all( '/./u', $guide['short'] ) > 14 ) {
		$missing[] = "$slug.short ยาวเกินไปสำหรับแถบบอกตำแหน่ง";
	}
}
check( array() === $missing, 'ทุกหน้ามีเวลา ระดับ ชื่อย่อ และสิ่งที่ต้องเตรียม' );

/* ---- เลขขั้นและหน้าก่อนหน้า/ถัดไป ---- */
$first = fenix_guide_meta( $slugs[0] );
check( 1 === $first['index'] && 8 === $first['total'], 'หน้าแรกคือขั้นที่ 1 จาก 8' );
check( null === $first['prev'], 'หน้าแรกไม่มีปุ่มขั้นก่อนหน้า' );
check( $slugs[1] === $first['next']['slug'], 'หน้าแรกชี้ไปขั้นที่ 2 ถูกต้อง' );

$last = fenix_guide_meta( $slugs[7] );
check( 8 === $last['index'], 'หน้าสุดท้ายคือขั้นที่ 8' );
check( null === $last['next'], 'หน้าสุดท้ายไม่มีปุ่มขั้นถัดไป' );
check( $slugs[6] === $last['prev']['slug'], 'หน้าสุดท้ายชี้กลับขั้นที่ 7 ถูกต้อง' );

$mid = fenix_guide_meta( 'vps-android' );
check( 'vps-desktop-windows' === $mid['prev']['slug'] && 'vps-iphone-ios' === $mid['next']['slug'], 'หน้ากลางเชื่อมหน้าก่อนหน้าและถัดไปถูกคู่' );
check( 2 === count( $mid['prep'] ), 'หน้า VPS ANDROID มีสิ่งที่ต้องเตรียม 2 ข้อ' );

/* ---- หน้าที่ไม่ได้อยู่ในชุดคู่มือ ---- */
$other = fenix_guide_meta( 'pricing' );
check( 0 === $other['index'] && array() === $other['prep'] && null === $other['prev'], 'หน้าที่ไม่ใช่คู่มือไม่ถูกใส่เลขขั้นหรือปุ่มข้ามหน้า' );

/* ---- แถบบอกตำแหน่ง ---- */
$rail = fenix_guide_rail( 'vps-android' );
$dom  = new DOMDocument();
libxml_use_internal_errors( true );
$dom->loadHTML( '<?xml encoding="UTF-8">' . $rail );
libxml_clear_errors();
$xpath = new DOMXPath( $dom );

check( 8 === $xpath->query( '//ol[@class="guide-rail"]/li' )->length, 'แถบบอกตำแหน่งแสดงครบทั้ง 8 ขั้น' );
check( 1 === $xpath->query( '//li[@class="is-now"]' )->length, 'มีขั้นเดียวที่ถูกทำเครื่องหมายว่ากำลังอ่านอยู่' );
check( 6 === $xpath->query( '//li[@class="is-done"]' )->length, 'ขั้นที่ผ่านมาแล้ว 6 ขั้นติดเครื่องหมายถูก' );
check( 8 === $xpath->query( '//a[starts-with(@href,"https://example.test/")]' )->length, 'ทุกขั้นในแถบกดข้ามไปได้' );

$rail_home = fenix_guide_rail( 'open-mt5-account' );
check( false === strpos( $rail_home, 'is-done' ), 'อยู่ขั้นแรก ยังไม่มีขั้นไหนถูกทำเครื่องหมายว่าผ่านแล้ว' );

/* ---- เทมเพลตมีชื่อให้เลือกใน WordPress ---- */
$tpl = file_get_contents( get_template_directory() . '/template-guide.php' );
check( false !== strpos( $tpl, 'Template Name:' ), 'เทมเพลตหน้าคู่มือมีชื่อให้เลือกใน Page Attributes' );
check( false !== strpos( $tpl, 'the_content()' ), 'เทมเพลตแสดงเนื้อหาเดิมของหน้า ไม่ได้เขียนทับ' );
check( false !== strpos( $tpl, 'fenix_line_cta' ), 'ทุกหน้าคู่มือจบด้วยปุ่มทัก LINE' );
