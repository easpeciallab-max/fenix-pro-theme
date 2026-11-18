<?php
/**
 * ตรวจหน้าเครื่องคำนวณขนาดล็อต + FAQPage JSON-LD ของบทความ
 *
 * Run with: php tests/lot-calculator.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();
$fx_content = '';
$fx_title   = 'เครื่องคำนวณขนาดล็อต (Lot Size Calculator)';

function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( ...$args ) {}
function apply_filters( $tag, $value ) { return $value; }
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
function esc_html__( $value, $domain = '' ) { return esc_html( $value ); }
function esc_attr__( $value, $domain = '' ) { return esc_attr( $value ); }
function _x( $text, $context = '', $domain = '' ) { return $text; }
function __( $text, $domain = '' ) { return $text; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags | JSON_UNESCAPED_UNICODE ); }
function wp_kses_post( $value ) { return $value; }
function sanitize_html_class( $value ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $value ); }
function number_format_i18n( $number ) { return number_format( (float) $number ); }
function get_option( $name, $default = false ) { return $default; }
function get_queried_object_id() { return 9; }
function get_the_ID() { return 9; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function get_post_thumbnail_id( $post = 0 ) { return 0; }
function wp_get_attachment_image_url( $id, $size = '' ) { return ''; }
function get_page_by_path( $path ) { return (object) array( 'ID' => 2, 'slug' => $path ); }
function get_permalink( $post = 0 ) { return 'https://example.test/lot-size-calculator/'; }
function the_permalink() { echo esc_url( get_permalink() ); }
function post_class( $class = '' ) { echo 'class="page"'; }
function get_bloginfo( $what = '' ) { return 'FENIX'; }
function bloginfo( $what = '' ) { echo get_bloginfo( $what ); }
function wp_footer() {}
function get_header() {}
function get_footer() {}
function is_front_page() { return false; }
function is_paged() { return false; }
function is_category() { return false; }
function is_page() { return true; }
function is_single() { return false; }
function is_singular( $type = '' ) { return 'post' === $type ? ! empty( $GLOBALS['fx_is_post'] ) : true; }
function is_main_query() { return true; }
function in_the_loop() { return true; }
function is_page_template( $template = '' ) {
	$want = (array) $template;
	return in_array( 'template-calculator.php', $want, true );
}
function have_posts() { return ! isset( $GLOBALS['fx_done'] ); }
function the_post() { $GLOBALS['fx_done'] = true; }
function get_post_field( $field, $id = 0 ) { return $GLOBALS['fx_content']; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_title( $id = 0 ) { return $GLOBALS['fx_title']; }
function get_the_date( $format = '' ) { return '18 พฤศจิกายน 2026'; }
function get_the_modified_date( $format = '' ) { return '18 พ.ย. 2026'; }
function get_the_author() { return 'FENIX'; }
function get_the_category( $post = 0 ) { return array(); }
function get_category_link( $id ) { return 'https://example.test/category/x/'; }
function wp_reset_postdata() {}
function has_post_thumbnail( $post = 0 ) { return false; }
function get_the_post_thumbnail_url( $post = null, $size = '' ) { return ''; }
function the_content() { echo $GLOBALS['fx_content']; }
function get_the_content() { return $GLOBALS['fx_content']; }
function wp_trim_words( $text, $count = 55, $more = '…' ) { return $text; }

if ( ! function_exists( 'mb_strlen' ) ) {
	function mb_strlen( $string, $encoding = null ) { return preg_match_all( '/./u', (string) $string ); }
}
if ( ! function_exists( 'mb_substr' ) ) {
	function mb_substr( $string, $start, $length = null, $encoding = null ) {
		$chars = preg_split( '//u', (string) $string, -1, PREG_SPLIT_NO_EMPTY );
		return implode( '', null === $length ? array_slice( $chars, $start ) : array_slice( $chars, $start, $length ) );
	}
}

class WP_Query {
	public $found_posts = 0;
	public function __construct( $args = array() ) {}
	public function have_posts() { return false; }
	public function the_post() {}
}

require get_template_directory() . '/functions.php';

$fails  = 0;
$passes = 0;

function check( $condition, $message ) {
	global $fails, $passes;
	if ( ! $condition ) {
		$fails++;
		fwrite( STDERR, "FAIL: $message\n" );
		return;
	}
	$passes++;
	echo "  ok   $message\n";
}

/* ---------- 1. เทมเพลตหน้าเครื่องคำนวณ ---------- */
echo "=== เทมเพลตหน้าเครื่องคำนวณ ===\n";

$GLOBALS['fx_content'] = '<p>คำอธิบายวิธีคิดที่เจ้าของเขียนเองในตัวแก้ไข</p>';

ob_start();
require get_template_directory() . '/template-calculator.php';
$html = ob_get_clean();

check( false !== strpos( $html, 'class="fxp fxcalc"' ), 'main ใช้คลาส fxp fxcalc' );
check( false !== strpos( $html, 'id="fx-calc"' ), 'มีฟอร์ม id=fx-calc' );
check( false !== strpos( $html, 'id="fx-calc-out"' ), 'มีกล่องผลลัพธ์ id=fx-calc-out' );
check( false !== strpos( $html, 'aria-live="polite"' ), 'กล่องผลลัพธ์ประกาศค่าที่เปลี่ยนให้โปรแกรมอ่านหน้าจอ' );

foreach ( array( 'cf-symbol', 'cf-contract', 'cf-balance', 'cf-risk', 'cf-sl', 'cf-price', 'cf-leverage', 'cf-minlot' ) as $field ) {
	check( false !== strpos( $html, 'id="' . $field . '"' ), "มีช่องกรอก $field" );
	check( false !== strpos( $html, 'for="' . $field . '"' ), "ช่อง $field มี label ผูกไว้" );
}

foreach ( array( 'lots', 'risk', 'loss', 'perpoint', 'margin', 'marginpct', 'warn' ) as $out ) {
	check( false !== strpos( $html, 'data-out="' . $out . '"' ), "มีช่องผลลัพธ์ $out" );
}

check( false !== strpos( $html, 'การเทรดมีความเสี่ยงที่จะสูญเงินลงทุน' ), 'มีคำเตือนความเสี่ยงในหน้า' );
check( false !== strpos( $html, 'ไม่มีการส่งตัวเลขออกไปที่ไหน' ), 'บอกผู้ใช้ว่าคำนวณในเครื่องตัวเอง' );
check( false !== strpos( $html, 'คำอธิบายวิธีคิดที่เจ้าของเขียนเองในตัวแก้ไข' ), 'เนื้อหาจากตัวแก้ไขของ WordPress ยังแสดงครบ' );
$calc_tpl = file_get_contents( get_template_directory() . '/template-calculator.php' );
check( false === strpos( $calc_tpl, 'FENIX PRO' ) && false === strpos( $calc_tpl, 'FENIX DUAL' ), 'ไม่มีชื่อรุ่นสินค้าเขียนตายในเทมเพลต' );

/* ---------- 2. ไฟล์ที่ต้องมี ---------- */
echo "\n=== ไฟล์ประกอบ ===\n";
check( file_exists( get_template_directory() . '/assets/css/calc.css' ), 'มีไฟล์ calc.css' );
check( file_exists( get_template_directory() . '/assets/js/calc.js' ), 'มีไฟล์ calc.js' );

$calc_css = file_get_contents( get_template_directory() . '/assets/css/calc.css' );
check( false !== strpos( $calc_css, '.fxp.fxcalc' ), 'สไตล์ทั้งหมดอยู่ใต้ .fxp.fxcalc' );
check( false !== strpos( $calc_css, '@media (max-width: 560px)' ), 'มีสไตล์สำหรับจอมือถือ' );

$calc_js = file_get_contents( get_template_directory() . '/assets/js/calc.js' );
check( false === strpos( $calc_js, 'fetch(' ) && false === strpos( $calc_js, 'XMLHttpRequest' ), 'สคริปต์ไม่ส่งข้อมูลออกนอกเครื่อง' );

/* ---------- 3. FAQPage JSON-LD ---------- */
echo "\n=== FAQPage JSON-LD ===\n";

$GLOBALS['fx_is_post'] = true;
$GLOBALS['fx_content'] = '<p>เนื้อหา</p>'
	. '<h2 id="faq">คำถามที่พบบ่อย</h2>'
	. '<h3>บอทเทรดคืออะไร</h3><p>โปรแกรมที่ส่งคำสั่งแทนคนตามเงื่อนไขที่เขียนไว้</p>'
	. '<h3>ใช้ทุนเท่าไร</h3><p>ขึ้นกับขนาดสัญญาและระยะที่ระบบต้องทนได้</p>'
	. '<h2 id="sources">แหล่งข้อมูล</h2><h3>ไม่ควรถูกนับ</h3><p>ลิงก์</p>';

ob_start();
fenix_faq_jsonld();
$faq = ob_get_clean();

check( false !== strpos( $faq, '"@type":"FAQPage"' ), 'มี FAQPage JSON-LD' );
$data = json_decode( preg_replace( '#</?script[^>]*>#', '', $faq ), true );
check( is_array( $data ) && isset( $data['mainEntity'] ), 'JSON อ่านได้' );
check( isset( $data['mainEntity'] ) && 2 === count( $data['mainEntity'] ), 'เก็บคำถามเฉพาะในส่วน FAQ 2 ข้อ (ไม่เลยไปส่วนแหล่งข้อมูล)' );
check( isset( $data['mainEntity'][0]['name'] ) && 'บอทเทรดคืออะไร' === $data['mainEntity'][0]['name'], 'คำถามข้อแรกถูกต้อง' );
check( isset( $data['mainEntity'][0]['acceptedAnswer']['text'] ) && false !== strpos( $data['mainEntity'][0]['acceptedAnswer']['text'], 'ส่งคำสั่งแทนคน' ), 'คำตอบข้อแรกถูกต้อง' );

/* บทความที่ไม่มีส่วน FAQ ต้องไม่ปล่อย schema */
$GLOBALS['fx_content'] = '<p>บทความธรรมดา</p><h2 id="x">หัวข้อ</h2><p>เนื้อหา</p>';
ob_start();
fenix_faq_jsonld();
$none = ob_get_clean();
check( '' === trim( $none ), 'บทความที่ไม่มีส่วนคำถามที่พบบ่อย ไม่ปล่อย schema' );

/* มีคำถามข้อเดียวก็ไม่ปล่อย */
$GLOBALS['fx_content'] = '<h2 id="faq">คำถามที่พบบ่อย</h2><h3>ข้อเดียว</h3><p>คำตอบ</p>';
ob_start();
fenix_faq_jsonld();
$one = ob_get_clean();
check( '' === trim( $one ), 'มีคำถามข้อเดียวไม่ปล่อย schema' );

echo "\nผ่าน $passes ข้อ · ไม่ผ่าน $fails ข้อ\n";
exit( $fails ? 1 : 0 );
