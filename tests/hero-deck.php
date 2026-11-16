<?php
/** Run with: php tests/hero-deck.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; the template is the real one.
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

class WP_Query {
	public function __construct( $args = array() ) {}
	public function have_posts() { return false; }
	public function the_post() {}
}

// PHP ที่ใช้รันเทสต์เครื่องนี้ไม่ได้เปิด mbstring ส่วนบนเซิร์ฟเวอร์จริงมีให้อยู่แล้ว
if ( ! function_exists( 'mb_strpos' ) ) {
	function mb_strpos( $haystack, $needle, $offset = 0, $encoding = null ) { return strpos( $haystack, $needle, $offset ); }
}
if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $string, $encoding = null ) { return preg_match_all( '/./u', (string) $string ); }
}
if ( ! function_exists( 'mb_substr' ) ) {
	function mb_substr( $string, $start, $length = null, $encoding = null ) {
		$chars = preg_split( '//u', (string) $string, -1, PREG_SPLIT_NO_EMPTY );
		$slice = array_slice( $chars, $start, $length );

		return implode( '', $slice );
	}
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

function dom_of( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	libxml_clear_errors();

	return new DOMXPath( $dom );
}

/* ---- ค่าเริ่มต้น: สำรับการ์ดสามใบ ---- */
$html  = render_front();
/* ---- ค่าเริ่มต้น: ปก EA สามใบวางเหลื่อมเป็นพัด ---- */
$html  = render_front();
$xpath = dom_of( $html );

$cards = $xpath->query( '//div[contains(@class,"fan")]/a[contains(@class,"cv")]' );
check( 3 === $cards->length, 'หน้าแรกแสดงปก EA สามใบ' );
check( 1 === $xpath->query( '//div[contains(@class,"fan")]' )->length, 'มีกล่องสำรับปกหนึ่งชุด' );

$slots = array();
foreach ( $cards as $card ) {
	$slots[] = trim( str_replace( 'cv', '', $card->getAttribute( 'class' ) ) );
}
check( array( 'c1', 'c2', 'c3' ) === $slots, 'วางปกครบทั้งสามตำแหน่ง (ซ้ายหลัง · ขวาหลัง · หน้าสุด)' );

$imgs = $xpath->query( '//div[contains(@class,"fan")]//img' );
check( 3 === $imgs->length, 'ปกทุกใบมีรูป' );
$alts = 0;
foreach ( $imgs as $img ) {
	if ( '' !== trim( $img->getAttribute( 'alt' ) ) ) {
		$alts++;
	}
}
check( 3 === $alts, 'ปกทุกใบมีคำอธิบายรูป (alt)' );
check( 'eager' === $imgs->item( 2 )->getAttribute( 'loading' ), 'ปกใบหน้าสุดโหลดก่อน ไม่ทำให้จอแรกช้า' );

/* ---- ใบหน้าสุดเปลี่ยนตามที่เลือกในหน้าปรับแต่ง ---- */
$front = $xpath->query( '//a[contains(@class,"cv c3")]//img' )->item( 0 );
check( false !== strpos( $front->getAttribute( 'src' ), 'plus' ), 'ค่าเริ่มต้น ปก FENIX PLUS อยู่หน้าสุด' );

$xpath_btc = dom_of( render_front( array( 'hero_deck_featured' => 'card3' ) ) );
$front_btc = $xpath_btc->query( '//a[contains(@class,"cv c3")]//img' )->item( 0 );
check( false !== strpos( $front_btc->getAttribute( 'src' ), 'btc' ), 'เลือกใบที่สามแล้วปก FENIX BTC ย้ายมาอยู่หน้าสุด' );

/* ---- ลิงก์ของปก ---- */
$href = $xpath->query( '//a[contains(@class,"cv c3")]' )->item( 0 )->getAttribute( 'href' );
check( 'https://example.test/go/' === $href, 'ค่าเริ่มต้น กดปกแล้วไปหน้าดาวน์โหลด' );

$xpath_file = dom_of( render_front( array( 'hero_deck_cta_mode' => 'file' ) ) );
$href_file  = $xpath_file->query( '//a[contains(@class,"cv c3")]' )->item( 0 )->getAttribute( 'href' );
check( fenix_mod( 'links_plus_url' ) === $href_file, 'ตั้งเป็นโหลดไฟล์ทันที กดปกแล้วได้ไฟล์ของใบนั้น' );

/* ---- ปิดการ์ดในหน้าดาวน์โหลด หน้าแรกต้องลดตาม ---- */
$xpath_off = dom_of( render_front( array( 'links_card3_enabled' => false ) ) );
check( 2 === $xpath_off->query( '//div[contains(@class,"fan")]/a[contains(@class,"cv")]' )->length, 'ปิดการ์ดใบไหนในหน้าดาวน์โหลด หน้าแรกก็ไม่แสดงใบนั้น' );
check( 2 === $xpath_off->query( '//div[contains(@class,"prod-g")]/div[@class="prod"]' )->length, 'การ์ด EA ในส่วนสินค้าลดลงตามกัน' );

/* ---- ป้ายลอยบนสำรับ ---- */
$status = $xpath->query( '//div[contains(@class,"fchip status")]' )->item( 0 );
check( null !== $status, 'มีป้ายสถานะลอยบนสำรับ' );
check( false !== strpos( $status->textContent, 'FENIX PLUS 5.1' ), 'ป้ายสถานะใช้ชื่อและเวอร์ชันของใบหน้าสุด' );

$toast = $xpath->query( '//div[contains(@class,"fchip toast")]' );
check( 1 === $toast->length, 'มีป้าย toast บนสำรับ (ซ่อนเองบนมือถือด้วย CSS)' );

/* ---- เทปวิ่งดึงชื่อและเวอร์ชันจากที่เดียวกัน ---- */
$tape = $xpath->query( '//div[contains(@class,"tape t1")]' )->item( 0 );
check( null !== $tape, 'มีเทปเส้นแรก' );
check( false !== strpos( $tape->textContent, 'FENIX PLUS 5.1' ), 'เทปเส้นแรกบอกชื่อและเวอร์ชัน EA' );
check( substr_count( $tape->textContent, 'FENIX PLUS 5.1' ) >= 2, 'ข้อความในเทปพิมพ์ซ้ำเพื่อให้วิ่งวนต่อเนื่อง' );

/* ---- เวอร์ชันมาจากชื่อไฟล์จุดเดียว แก้ไฟล์แล้วเปลี่ยนตามทั้งหน้า ---- */
$changed = dom_of( render_front( array( 'links_plus_url' => 'https://fenixpro-th.com/wp-content/uploads/2027/01/FENIX_PLUS_V9.9.zip' ) ) );
check( false !== strpos( $changed->query( '//span[contains(@class,"pill-note")]' )->item( 0 )->textContent, 'PLUS 9.9' ), 'เปลี่ยนไฟล์ EA แล้วป้ายเวอร์ชันบนหน้าแรกเปลี่ยนตาม' );

echo "\nครบทุกข้อ\n";
