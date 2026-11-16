<?php
/** Run with: php tests/home-sections.php */
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
function get_header() {}
function get_footer() {}
function have_posts() { return false; }
function the_post() {}
function the_content() {}
function post_class() { return ''; }
function get_permalink() { return 'https://example.test/post/'; }
function get_bloginfo( $what = '' ) { return 'FENIX PRO EA'; }
function get_option( $name, $default = false ) { return $default; }
function wp_reset_postdata() {}
function get_queried_object_id() { return 0; }
function get_the_ID() { return 0; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function nl2br_x( $v ) { return $v; }

if ( ! function_exists( 'mb_strpos' ) ) {
	function mb_strpos( $haystack, $needle, $offset = 0, $encoding = null ) { return strpos( $haystack, $needle, $offset ); }
}
if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $string, $encoding = null ) { return preg_match_all( '/./u', (string) $string ); }
}
if ( ! function_exists( 'mb_substr' ) ) {
	function mb_substr( $string, $start, $length = null, $encoding = null ) {
		return implode( '', array_slice( preg_split( '//u', (string) $string, -1, PREG_SPLIT_NO_EMPTY ), $start, $length ) );
	}
}

class WP_Query {
	public function __construct( $args = array() ) {}
	public function have_posts() { return false; }
	public function the_post() {}
}

require get_template_directory() . '/functions.php';

function check( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
	echo "PASS: $message\n";
}

function render_front( $mods = array() ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/front-page.php';

	return ob_get_clean();
}

function xp( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	libxml_clear_errors();

	return new DOMXPath( $dom );
}

$html = render_front();
$x    = xp( $html );

/* ---- ลำดับหัวข้อ ---- */
$ids = array();
foreach ( $x->query( '//section[@id]' ) as $sec ) {
	$ids[] = $sec->getAttribute( 'id' );
}

$expected = array( 'hero', 'notus', 'about', 'pain', 'how-it-works', 'features', 'fit', 'performance', 'devices', 'own', 'team', 'pricing', 'explore', 'faq', 'risk', 'cta' );
check( $expected === $ids, 'หัวข้อบนหน้าแรกเรียงตามแผนใหม่: ' . implode( ' > ', $ids ) );

/* ---- หัวข้อที่ยุบแล้วต้องไม่โผล่ ---- */
foreach ( array( 'system', 'assurance', 'screenshots', 'reviews' ) as $gone ) {
	check( ! in_array( $gone, $ids, true ), "หัวข้อ #$gone ถูกยุบแล้ว ไม่แสดงบนหน้าแรก" );
}

/* ---- แถบข้อเท็จจริง ---- */
$chips = $x->query( '//section[contains(@class,"highlight-bar")]//li' );
check( 4 === $chips->length, 'แถบข้อเท็จจริงมี 4 ชิป (3 ข้อความ + เวอร์ชันอัตโนมัติ)' );
check(
	false !== strpos( $chips->item( 3 )->textContent, 'PLUS 5.1' ) && false !== strpos( $chips->item( 3 )->textContent, 'BTC 1.1' ),
	'ชิปสุดท้ายดึงเลขเวอร์ชันมาจากชื่อไฟล์ EA จริง'
);

/* ---- เราไม่ทำอะไร ---- */
$notus = $x->query( '//section[@id="notus"]//ul[contains(@class,"notus-list")]/li' );
check( 5 === $notus->length, 'หัวข้อ "เราไม่ทำอะไร" มี 5 ข้อ' );
check( 5 === $x->query( '//section[@id="notus"]//li/span[contains(@class,"notus-body")]/strong' )->length, 'ทุกข้อมีหัวข้อหนาและคำอธิบายแยกกัน' );

/* ---- EA คืออะไร ---- */
check( 1 === $x->query( '//section[@id="about"]//figure[contains(@class,"sec-figure")]' )->length, 'หัวข้อ EA คืออะไร มีรูปประกอบ' );
check( 3 === $x->query( '//section[@id="about"]//ol[contains(@class,"ea-flow")]/li' )->length, 'มีไดอะแกรมการทำงาน 3 ขั้น' );

/* ---- รูปประกอบของหัวข้ออื่น ---- */
foreach ( array( 'pain', 'how-it-works', 'features' ) as $sec ) {
	check( 1 === $x->query( '//section[@id="' . $sec . '"]//figure[contains(@class,"sec-figure")]' )->length, "หัวข้อ #$sec มีรูปประกอบ" );
}

$imgs = $x->query( '//figure[contains(@class,"sec-figure")]//img' );
$no_alt = 0;
foreach ( $imgs as $img ) {
	if ( '' === trim( $img->getAttribute( 'alt' ) ) ) { $no_alt++; }
	if ( 'lazy' !== $img->getAttribute( 'loading' ) ) { $no_alt++; }
}
check( 0 === $no_alt, 'รูปประกอบทุกใบมีคำอธิบาย ALT และโหลดแบบ lazy' );

/* ---- การ์ดโปรไฟล์ + กล่องอย่าเพิ่งใช้ ---- */
check( 3 === $x->query( '//section[@id="fit"]//article[contains(@class,"profile-card")]' )->length, 'เหมาะกับใครใช้การ์ดโปรไฟล์ 3 ใบ' );
check( 0 === $x->query( '//section[@id="fit"]//div[contains(@class,"fit-good")]' )->length, 'เลิกใช้ตารางเหมาะ/ไม่เหมาะแบบสองช่องแล้ว' );
check( 1 === $x->query( '//section[@id="fit"]//div[contains(@class,"fit-bad")]' )->length, 'ยังเก็บกล่อง "อย่าเพิ่งใช้" ไว้ตามเดิม' );

/* ---- หลักฐาน + วิธีตรวจสอบ ---- */
check( 5 === $x->query( '//div[contains(@class,"verify-box")]//ol/li' )->length, 'กล่องวิธีตรวจสอบมี 5 ข้อ' );
check( 1 === $x->query( '//section[@id="performance"]//a[contains(@class,"btn-ghost")]' )->length, 'ยังมีปุ่มไปดูผลจริงบน Myfxbook' );

/* ---- อุปกรณ์ ---- */
$devs = $x->query( '//section[@id="devices"]//article[contains(@class,"dev-card")]' );
check( 3 === $devs->length, 'หัวข้ออุปกรณ์มี 3 การ์ด' );
check( 1 === $x->query( '//section[@id="devices"]//span[contains(@class,"dev-badge")]' )->length, 'มีป้ายแนะนำบนการ์ดเดียว' );
check( false !== strpos( $devs->item( 2 )->textContent, 'รัน EA ไม่ได้' ), 'การ์ดมือถือบอกชัดว่ารัน EA ไม่ได้' );
check( 1 === $x->query( '//section[@id="devices"]//p[contains(@class,"dev-req")]' )->length, 'มีบรรทัดความต้องการระบบ' );

/* ---- สิ่งที่ยังเป็นของคุณ ---- */
check( 4 === $x->query( '//section[@id="own"]//ul[contains(@class,"own-list")]/li' )->length, 'หัวข้อสิ่งที่ยังเป็นของคุณมี 4 ข้อ' );

/* ---- ตัวตนบริษัท: ซ่อนเมื่อยังไม่กรอก ---- */
check( 0 === $x->query( '//ul[contains(@class,"identity-list")]' )->length, 'ยังไม่กรอกข้อมูลบริษัท จึงไม่แสดงแถบตัวตน' );

$x2 = xp( render_front( array( 'company_name' => 'บริษัท ทดสอบ จำกัด', 'company_reg' => '0123456789012' ) ) );
check( 1 === $x2->query( '//ul[contains(@class,"identity-list")]' )->length, 'กรอกชื่อบริษัทแล้วแถบตัวตนโผล่ขึ้นมา' );
check( 2 === $x2->query( '//ul[contains(@class,"identity-list")]/li' )->length, 'แสดงเฉพาะช่องที่กรอกจริง' );

/* ---- FAQ เรื่องขาดทุน ---- */
$faq = $x->query( '//section[@id="faq"]//details' );
check( 10 === $faq->length, 'FAQ เพิ่มเป็น 10 ข้อ' );
check( false !== strpos( $html, 'Drawdown สูงสุดที่เคยเกิด' ), 'มีคำถามเรื่อง Drawdown สูงสุด' );
check( false !== strpos( $html, 'ขาดทุนติดกันหลายเดือน' ), 'มีคำถามว่าถ้าขาดทุนติดกันทีมทำอะไร' );

/* ---- ปิดหัวข้อใหม่ได้ ---- */
$x3 = xp( render_front( array( 'show_notus' => false, 'show_devices' => false, 'show_own' => false ) ) );
foreach ( array( 'notus', 'devices', 'own' ) as $sec ) {
	check( 0 === $x3->query( '//section[@id="' . $sec . '"]' )->length, "ปิดหัวข้อ #$sec จากหลังบ้านได้" );
}

/* ---- ค่าที่กรอกต้องถูก escape ---- */
$evil = render_front( array( 'notus_title' => '<script>x</script>' ) );
check( false === strpos( $evil, '<script>x</script>' ), 'ข้อความจากหลังบ้านถูก escape ก่อนแสดง' );
