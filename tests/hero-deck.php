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
$xpath = dom_of( $html );

$cards = $xpath->query( '//article[contains(@class, "deck-card")]' );
check( 3 === $cards->length, 'หน้าแรกแสดงการ์ดปกสามใบ' );
check( 1 === $xpath->query( '//div[@class="hero-deck"]' )->length, 'มีกล่องสำรับการ์ดหนึ่งชุด' );
check( 0 === $xpath->query( '//div[contains(@class, "hero-orb")]' )->length, 'วงแหวนโลโก้แบบเดิมไม่ถูกแสดงพร้อมกัน' );

$names = array();
foreach ( $cards as $card ) {
	$names[] = $card->getAttribute( 'data-name' );
}
check( array( 'FENIX PLUS', 'FENIX DUAL', 'FENIX BTC' ) === $names, 'ชื่อบนการ์ดอ่านมาจากชื่อไฟล์ EA โดยตรง และใบเด่นอยู่บนสุด' );

$versions = array();
foreach ( $cards as $card ) {
	$versions[] = $card->getAttribute( 'data-version' );
}
check( array( '5.1', '6.3', '1.1' ) === $versions, 'เลขเวอร์ชันอ่านมาจากชื่อไฟล์เช่นกัน' );

check( 1 === $xpath->query( '//article[contains(@class, "is-1")]' )->length, 'มีใบเดียวที่อยู่บนสุด' );
check( 3 === $xpath->query( '//button[contains(@class, "deck-dot")]' )->length, 'มีจุดบอกตำแหน่งครบทุกใบ' );
check( 1 === $xpath->query( '//button[contains(@class, "is-on")]' )->length, 'จุดที่ไฮไลต์มีจุดเดียว' );
check( 'ตัวเด่นตอนนี้' === trim( $xpath->query( '//span[contains(@class, "deck-badge")]' )->item( 0 )->textContent ), 'ป้ายบนใบเด่นใช้ข้อความจากค่าที่ตั้งไว้' );
check( 'FENIX PLUS' === trim( $xpath->query( '//span[contains(@class, "deck-name")]' )->item( 0 )->textContent ), 'ชื่อใต้สำรับตรงกับใบบนสุด' );

/* รูปใบแรกต้องโหลดก่อน ใบที่เหลือค่อยโหลดทีหลัง */
$imgs = $xpath->query( '//article[contains(@class, "deck-card")]//img' );
check( 'eager' === $imgs->item( 0 )->getAttribute( 'loading' ), 'ใบบนสุดโหลดทันที' );
check( 'lazy' === $imgs->item( 1 )->getAttribute( 'loading' ), 'ใบที่อยู่ข้างหลังโหลดทีหลัง ไม่ถ่วงหน้าแรก' );
check( '1200' === $imgs->item( 0 )->getAttribute( 'width' ), 'ภาพมีขนาดกำกับ กันหน้าเด้งตอนโหลด' );

/* ---- ปุ่มใต้การ์ด ---- */
$cta = $xpath->query( '//a[contains(@class, "deck-cta")]' )->item( 0 );
check( 'https://example.test/go/' === $cta->getAttribute( 'href' ), 'ค่าเริ่มต้น ปุ่มพาไปหน้าดาวน์โหลดที่มีคำอธิบายครบ' );

$xpath_file = dom_of( render_front( array( 'hero_deck_cta_mode' => 'file' ) ) );
$cta_file   = $xpath_file->query( '//a[contains(@class, "deck-cta")]' )->item( 0 );
check( fenix_mod( 'links_plus_url' ) === $cta_file->getAttribute( 'href' ), 'ถ้าเลือกโหลดไฟล์ตรง ปุ่มชี้ไปไฟล์ของใบบนสุด' );
check(
	fenix_mod( 'links_plus_url' ) === $xpath_file->query( '//article[contains(@class, "deck-card")]' )->item( 0 )->getAttribute( 'data-url' ),
	'ตัวการ์ดเองก็ชี้ไปไฟล์เดียวกัน'
);

/* ---- เลือกใบเด่นได้ ---- */
$xpath_btc = dom_of( render_front( array( 'hero_deck_featured' => 'card3' ) ) );
check(
	'FENIX BTC' === $xpath_btc->query( '//article[contains(@class, "deck-card")]' )->item( 0 )->getAttribute( 'data-name' ),
	'เปลี่ยนใบเด่นได้จากหลังบ้าน'
);

/* ---- ปิดการ์ดในหน้าดาวน์โหลดแล้วหน้าแรกต้องหายตาม ---- */
$xpath_off = dom_of( render_front( array( 'links_card3_enabled' => false ) ) );
check( 2 === $xpath_off->query( '//article[contains(@class, "deck-card")]' )->length, 'ปิดการ์ดใบไหนในหน้าดาวน์โหลด หน้าแรกก็ไม่แสดงใบนั้น' );
check( 2 === $xpath_off->query( '//button[contains(@class, "deck-dot")]' )->length, 'จุดบอกตำแหน่งลดตามจำนวนการ์ด' );

/* ---- สลับกลับไปใช้โลโก้วงแหวนได้ ---- */
$xpath_orb = dom_of( render_front( array( 'hero_visual' => 'orb' ) ) );
check( 1 === $xpath_orb->query( '//div[contains(@class, "hero-orb")]' )->length, 'เลือกกลับไปใช้โลโก้วงแหวนแบบเดิมได้' );
check( 0 === $xpath_orb->query( '//div[@class="hero-deck"]' )->length, 'เมื่อใช้โลโก้วงแหวน สำรับการ์ดไม่ถูกแสดง' );

/* ---- ใส่ภาพ Hero เองแล้วภาพนั้นต้องชนะทุกอย่าง ---- */
$xpath_photo = dom_of( render_front( array( 'hero_image' => 'https://example.test/hero.webp' ) ) );
check( 1 === $xpath_photo->query( '//figure[contains(@class, "hero-frame")]' )->length, 'ถ้าเจ้าของใส่ภาพ Hero เอง จะใช้ภาพนั้น' );
check( 0 === $xpath_photo->query( '//div[@class="hero-deck"]' )->length, 'ภาพ Hero มาแทนสำรับการ์ด ไม่แสดงซ้อนกัน' );

/* ---- ป้ายและปุ่มซ่อนได้ ---- */
$xpath_bare = dom_of( render_front( array( 'hero_deck_badge' => '', 'hero_deck_cta' => '' ) ) );
check( 0 === $xpath_bare->query( '//span[contains(@class, "deck-badge")]' )->length, 'เว้นว่างแล้วป้ายหายไป' );
check( 0 === $xpath_bare->query( '//a[contains(@class, "deck-cta")]' )->length, 'เว้นว่างแล้วปุ่มใต้การ์ดหายไป' );

/* ---- ค่าที่กรอกต้องถูก escape ---- */
$xpath_evil = dom_of( render_front( array( 'hero_deck_badge' => '<script>x</script>' ) ) );
check( false === strpos( render_front( array( 'hero_deck_badge' => '<script>x</script>' ) ), '<script>x</script>' ), 'ข้อความจากหลังบ้านถูก escape ก่อนแสดง' );

/* ---- ตัวตรวจค่า ---- */
check( 'deck' === fenix_sanitize_hero_visual( 'อะไรก็ไม่รู้' ), 'ค่ารูปฝั่งขวาที่ไม่รู้จักถูกตีกลับเป็นค่าเริ่มต้น' );
check( 'plus' === fenix_sanitize_deck_slot( 'card9' ), 'ชื่อใบเด่นที่ไม่มีจริงถูกตีกลับเป็นค่าเริ่มต้น' );
check( 'page' === fenix_sanitize_deck_cta_mode( 'ftp' ), 'ปลายทางปุ่มที่ไม่รู้จักถูกตีกลับเป็นหน้าดาวน์โหลด' );

/* ---- อ่านชื่อไฟล์ ---- */
$parsed = fenix_product_from_url( 'https://a.test/x/FENIX_POWER_V4.2.zip' );
check( 'FENIX POWER' === $parsed['name'] && '4.2' === $parsed['version'], 'อ่านชื่อกับเวอร์ชันจากไฟล์ตัวอื่นได้ด้วย' );

$plain = fenix_product_from_url( 'https://a.test/go/' );
check( '' === $plain['name'] && '' === $plain['version'], 'ลิงก์ที่ไม่ใช่ไฟล์ EA ไม่ถูกเดาชื่อมั่ว' );
