<?php
/**
 * ตรวจหน้ากฎหมาย ชุด 3 (ดีไซน์ที่อนุมัติ 17 พ.ย. 2026)
 * /risk-disclosure/ · /privacy-policy/ · /terms/
 *
 * Run with: php tests/legal-pages-batch3.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();
$fx_tpl     = 'template-legal.php';
$fx_title   = '';
$fx_content = '';
$fx_thumb   = '';

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
function _x( $text, $context = '', $domain = '' ) { return $text; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE ); }
function number_format_i18n( $number ) { return number_format( (float) $number ); }
function get_option( $name, $default = false ) { return $default; }
function get_queried_object_id() { return 0; }
function get_the_ID() { return 1; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function get_post_thumbnail_id( $post = 0 ) { return 0; }
function wp_get_attachment_image_url( $id, $size = '' ) { return ''; }
function get_header() {}
function get_footer() {}
function is_front_page() { return false; }
function is_paged() { return false; }
function is_category() { return false; }
function is_main_query() { return true; }
function in_the_loop() { return true; }
function is_page_template( $template = '' ) { return in_array( $GLOBALS['fx_tpl'], (array) $template, true ); }
function have_posts() { return true; }
function the_post() {}
function get_post_field( $field, $id = 0 ) { return 'x'; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_title( $id = 0 ) { return $GLOBALS['fx_title']; }
function get_the_modified_date( $format = '' ) { return '16 พ.ย. 2026'; }
function has_excerpt() { return false; }
function get_the_excerpt( $post = 0 ) { return ''; }
function has_post_thumbnail( $post = 0 ) { return '' !== $GLOBALS['fx_thumb']; }
function get_the_post_thumbnail_url( $post = null, $size = '' ) { return $GLOBALS['fx_thumb']; }
function the_content() { echo $GLOBALS['fx_content']; }
function get_the_content() { return $GLOBALS['fx_content']; }
function wp_trim_words( $text, $count = 55, $more = '…' ) { return $text; }

if ( ! function_exists( 'mb_strpos' ) ) {
	function mb_strpos( $haystack, $needle, $offset = 0, $encoding = null ) { return strpos( $haystack, $needle, $offset ); }
}
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
	public $found_posts   = 0;
	public $max_num_pages = 0;
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
	echo "PASS: $message\n";
}

function render_legal( $template, $title, $content = '', $thumb = '', $mods = array() ) {
	global $theme_mods, $fx_tpl, $fx_title, $fx_content, $fx_thumb;
	$theme_mods = $mods;
	$fx_tpl     = $template;
	$fx_title   = $title;
	$fx_content = $content;
	$fx_thumb   = $thumb;
	ob_start();
	require get_template_directory() . '/' . $template;
	return ob_get_clean();
}

function xp( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8"><div id="t">' . $html . '</div>' );
	libxml_clear_errors();
	return new DOMXPath( $dom );
}

function plain( $html ) {
	$text = html_entity_decode( strip_tags( (string) $html ), ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', str_replace( "\xc2\xa0", ' ', $text ) ) );
}

/* ==========================================================
   เนื้อหาจำลอง · โครงเดียวกับที่เจ้าของเขียนไว้ในตัวแก้ไข
   ========================================================== */

$doc_privacy = '
<p>เว็บไซต์ FENIX PRO EA (ต่อไปนี้เรียกว่า “เรา”) ให้ความสำคัญกับการคุ้มครองข้อมูลส่วนบุคคลของคุณ</p>
<p>ผู้ควบคุมข้อมูลส่วนบุคคล: FENIX PRO EA<br>ช่องทางติดต่อ: <a href="mailto:a@b.test">a@b.test</a> · LINE: <a href="https://line.me/R/ti/p/@fenixpro">@fenixpro</a></p>
<p>มีผลบังคับใช้: 2026</p>
<h2>ข้อมูลที่เราเก็บรวบรวม</h2>
<p>ข้อมูลที่คุณให้เราโดยตรง: ชื่อหรือชื่อที่ใช้ติดต่อ</p>
<h2>วัตถุประสงค์ในการใช้ข้อมูล</h2>
<ul><li>เพื่อติดต่อกลับ ตอบคำถาม</li><li>เพื่อดูแลและปรับปรุงเว็บไซต์</li></ul>
<h2>ติดต่อเรา</h2>
<p>มีคำถามเกี่ยวกับนโยบาย ติดต่อ FENIX PRO EA</p>
';

$doc_terms = '
<p>ยินดีต้อนรับสู่ FENIX PRO EA การเข้าใช้งานเว็บไซต์และบริการของเราถือว่าคุณยอมรับเงื่อนไขต่อไปนี้</p>
<p>ผู้ให้บริการ: FENIX PRO EA<br>ติดต่อ: <a href="mailto:a@b.test">a@b.test</a> · LINE: <a href="https://line.me/R/ti/p/@fenixpro">@fenixpro</a><br>มีผลบังคับใช้: 2026</p>
<h2>1. การยอมรับเงื่อนไข</h2>
<p>การเข้าชมเว็บไซต์ถือว่าผู้ใช้งานได้อ่านแล้ว</p>
<h2>2. ลักษณะของบริการ</h2>
<p>FENIX PRO EA เป็นระบบช่วยเทรดอัตโนมัติ</p>
<h2>11. ช่องทางติดต่อ</h2>
<p>หากมีคำถามเกี่ยวกับเงื่อนไขการใช้บริการ</p>
<p><strong>ปรับปรุงล่าสุด: 3 กรกฎาคม 2026</strong></p>
';

$thumb = 'https://example.test/cover.webp';

/* ==========================================================
   1) ร่วมกันทุกหน้า
   ========================================================== */
$html = array(
	'risk'    => render_legal( 'template-risk.php', 'คำเตือนความเสี่ยง' ),
	'privacy' => render_legal( 'template-legal.php', 'นโยบายความเป็นส่วนตัว', $doc_privacy, $thumb ),
	'terms'   => render_legal( 'template-legal.php', 'เงื่อนไขการใช้บริการ', $doc_terms, $thumb ),
);

foreach ( $html as $key => $out ) {
	$x = xp( $out );
	check( 1 === $x->query( '//main[@id="main" and contains(@class,"fxp") and contains(@class,"fxl")]' )->length, "$key · เนื้อหาอยู่ใต้ main.fxp.fxl (ใช้ชุดสไตล์เดียวกับชุด 1)" );
	check( 1 === $x->query( '//h1' )->length, "$key · มี h1 เดียวในหน้า" );
	check( 1 === $x->query( '//nav[@class="crumb"]' )->length, "$key · มีลำดับหน้า หน้าแรก › ชื่อหน้า" );
	check( 1 === $x->query( '//nav[@class="crumb"]/a[@href="https://example.test/"]' )->length, "$key · ลำดับหน้าลิงก์กลับหน้าแรก" );
	check( 1 === $x->query( '//section[contains(@class,"fx-phero")]//div[@class="slash"]' )->length, "$key · มีลายเฉียงที่หัวหน้าเพจ" );
	check( 0 === $x->query( '//*[contains(@style,"position:fixed") or contains(@style,"position: fixed")]' )->length, "$key · ไม่มีอะไรลอยทับแถบเมนูล่างบนมือถือ" );
}

/* ==========================================================
   2) คำเตือนความเสี่ยง
   ========================================================== */
$x = xp( $html['risk'] );
$d = fenix_defaults();

check( 1 === $x->query( '//span[@class="eyebrow"]' )->length, 'คำเตือนความเสี่ยง · มีป้าย Risk Disclosure เหนือหัวข้อ' );
check( false !== strpos( $html['risk'], 'Risk Disclosure' ), 'คำเตือนความเสี่ยง · ป้ายใช้ข้อความเดิม' );
check( 1 === $x->query( '//p[@class="sub"]' )->length, 'คำเตือนความเสี่ยง · มีคำโปรยใต้หัวข้อ' );

check( 1 === $x->query( '//div[@class="rintro"]' )->length, 'คำเตือนความเสี่ยง · มีกล่องเกริ่น' );
check( false !== strpos( plain( $html['risk'] ), plain( $d['riskpage_intro'] ) ), 'คำเตือนความเสี่ยง · ข้อความเกริ่นเดิมอยู่ครบ' );

$fig = $x->query( '//figure[contains(@class,"fx-shot") and contains(@class,"rfig")]' );
check( 1 === $fig->length, 'คำเตือนความเสี่ยง · มีกรอบรูปแนวทางตั้งค่าความเสี่ยง' );
$fig_img = $x->query( '//figure[contains(@class,"rfig")]/img' );
check( $fig_img->length && $fig_img->item( 0 )->getAttribute( 'src' ) === esc_url( $d['riskpage_image'] ), 'คำเตือนความเสี่ยง · ใช้รูปที่ตั้งไว้ในหน้านั้น ไม่ได้เขียนทับ' );
check( 1 === $x->query( '//figure[contains(@class,"rfig")]/figcaption' )->length, 'คำเตือนความเสี่ยง · มีคำบรรยายใต้รูป' );

$cards = $x->query( '//div[@class="rgrid"]/article[@class="rcard"]' );
check( 6 === $cards->length, 'คำเตือนความเสี่ยง · การ์ดครบ 6 ใบ' );

$nums = array();
foreach ( $x->query( '//article[@class="rcard"]/span[contains(@class,"n")]' ) as $num ) {
	$nums[] = $num->textContent;
}
check( array( '01', '02', '03', '04', '05', '06' ) === $nums, 'คำเตือนความเสี่ยง · เลขข้อเป็น 01-06' );

foreach ( array( 1, 2, 3, 4, 5, 6 ) as $slot ) {
	$title = trim( (string) $d[ 'rp_block' . $slot . '_title' ] );
	if ( '' !== $title ) {
		check( false !== strpos( plain( $html['risk'] ), plain( $title ) ), "คำเตือนความเสี่ยง · หัวข้อการ์ดที่ $slot อยู่ครบ" );
	}
}

check( 1 === $x->query( '//div[@class="rfoot"]/span[@class="updated"]' )->length, 'คำเตือนความเสี่ยง · มีป้ายวันที่ปรับปรุง' );
check( 1 === $x->query( '//div[contains(@class,"fx-cta")]' )->length, 'คำเตือนความเสี่ยง · มีกล่อง CTA สีส้ม' );
check( 1 === $x->query( '//div[contains(@class,"fx-cta")]//a[contains(@class,"fx-btn-black") and contains(@href,"line")]' )->length, 'คำเตือนความเสี่ยง · ปุ่ม CTA เป็นปุ่มดำทัก LINE' );
check( false !== strpos( $html['risk'], 'มีคำถามเรื่องความเสี่ยง?' ), 'คำเตือนความเสี่ยง · หัวข้อ CTA ตามแบบ' );

/* ==========================================================
   3) หน้าเอกสาร · นโยบายความเป็นส่วนตัว
   ========================================================== */
$x = xp( $html['privacy'] );

check( 1 === $x->query( '//div[@class="dgrid"]/aside[@class="dside"]' )->length, 'นโยบายฯ · กล่องสารบัญอยู่ในคอลัมน์ของตัวเอง' );
check( 1 === $x->query( '//article[@class="dmain"]' )->length, 'นโยบายฯ · เนื้อหาอยู่ในคอลัมน์หลัก' );
check( 1 === $x->query( '//figure[@class="dthumb"]/img' )->length, 'นโยบายฯ · มีรูปแบนเนอร์หน้าปก' );
check( 1 === $x->query( '//p[@class="dlead"]' )->length, 'นโยบายฯ · ย่อหน้าแรกเป็นคำนำ' );
check( 3 === $x->query( '//div[@class="dinfo"]/div' )->length, 'นโยบายฯ · การ์ดข้อมูลเอกสารมี 3 แถว' );
check( 3 === $x->query( '//div[@class="dinfo"]//b' )->length, 'นโยบายฯ · ป้ายหน้าเครื่องหมาย : เป็นตัวหนา' );

$secs = $x->query( '//section[@class="dsec"]' );
check( 3 === $secs->length, 'นโยบายฯ · แยกช่วงตามหัวข้อครบทุกข้อ' );
check( 3 === $x->query( '//details[contains(@class,"toc")]//ol/li' )->length, 'นโยบายฯ · สารบัญมีครบทุกหัวข้อ' );

/* สารบัญต้องชี้ไปหัวข้อที่มีจริง */
$ids = array();
foreach ( $x->query( '//section[@class="dsec"]/h2' ) as $head ) {
	$ids[] = $head->getAttribute( 'id' );
}
$links = array();
foreach ( $x->query( '//details[contains(@class,"toc")]//ol/li/a' ) as $link ) {
	$links[] = ltrim( $link->getAttribute( 'href' ), '#' );
}
check( $ids === $links && ! in_array( '', $ids, true ), 'นโยบายฯ · ทุกรายการในสารบัญชี้ไปหัวข้อที่มีจริง' );

check( 1 === $x->query( '//details[contains(@class,"toc")][@open]' )->length, 'นโยบายฯ · กล่องสารบัญกางไว้ในโค้ด (คอมจึงกางแม้ไม่มี JS)' );
check( 1 === $x->query( '//details[contains(@class,"toc")]//summary/span[@class="chev"]' )->length, 'นโยบายฯ · มีปุ่มลูกศรสำหรับพับบนมือถือ' );

/* ข้อความเดิมอยู่ครบทุกตัวอักษร รวมเครื่องหมายคำพูดโค้ง */
check( false !== strpos( $html['privacy'], '“เรา”' ), 'นโยบายฯ · เครื่องหมายคำพูดโค้ง “เรา” ไม่ถูกเปลี่ยน' );

/* <br> ในย่อหน้าข้อมูลเอกสารคือการขึ้นแถวใหม่ ตรวจทีละแถว */
$src     = xp( '<div>' . preg_replace( '~<br\s*/?>~i', ' | ', $doc_privacy ) . '</div>' );
$out     = plain( $html['privacy'] );
$missing = array();
foreach ( $src->query( '//p|//li|//h2' ) as $node ) {
	$text = plain( $node->textContent );
	if ( '' === $text ) {
		continue;
	}
	foreach ( preg_split( '/\s*[|·]\s*/u', $text ) as $part ) {
		$part = trim( $part );
		if ( '' !== $part && false === strpos( $out, $part ) ) {
			$missing[] = $part;
		}
	}
}
check( ! $missing, 'นโยบายฯ · ข้อความเดิมอยู่ครบทุกบรรทัด' . ( $missing ? ' (ขาด: ' . implode( ' / ', array_slice( $missing, 0, 3 ) ) . ')' : '' ) );

/* ==========================================================
   4) หน้าเอกสาร · เงื่อนไขการใช้บริการ
   ========================================================== */
$x = xp( $html['terms'] );

check( 3 === $x->query( '//section[@class="dsec"]/h2/span[@class="n"]' )->length, 'เงื่อนไขฯ · เลขหัวข้อถูกห่อไว้ให้เป็นสีส้มทุกข้อ' );

$heads = array();
foreach ( $x->query( '//section[@class="dsec"]/h2' ) as $head ) {
	$heads[] = plain( $head->textContent );
}
check( array( '1. การยอมรับเงื่อนไข', '2. ลักษณะของบริการ', '11. ช่องทางติดต่อ' ) === $heads, 'เงื่อนไขฯ · ข้อความหัวข้อไม่เปลี่ยน แค่ห่อเลขไว้' );

$labels = array();
foreach ( $x->query( '//details[contains(@class,"toc")]//ol/li/a' ) as $link ) {
	$labels[] = plain( $link->textContent );
}
check( array( '1 การยอมรับเงื่อนไข', '2 ลักษณะของบริการ', '3 ช่องทางติดต่อ' ) === $labels, 'เงื่อนไขฯ · สารบัญใช้เลขในกล่อง ไม่ซ้ำกับเลขในหัวข้อ' );

check( 1 === $x->query( '//article[@class="dmain"]/span[@class="updated"]' )->length, 'เงื่อนไขฯ · ปิดท้ายด้วยป้ายวันที่ปรับปรุง' );
check( false === strpos( $html['terms'], '<p><strong>ปรับปรุงล่าสุด' ), 'เงื่อนไขฯ · ย่อหน้าวันที่ปรับปรุงไม่ถูกแสดงซ้ำ' );
check( 3 === $x->query( '//div[@class="dinfo"]/div' )->length, 'เงื่อนไขฯ · การ์ดข้อมูลเอกสารแยกครบ 3 แถว' );

$src     = xp( '<div>' . preg_replace( '~<br\s*/?>~i', ' | ', $doc_terms ) . '</div>' );
$out     = plain( $html['terms'] );
$missing = array();
foreach ( $src->query( '//p|//li|//h2' ) as $node ) {
	$text = plain( $node->textContent );
	if ( '' === $text ) {
		continue;
	}
	foreach ( preg_split( '/\s*[|·]\s*/u', $text ) as $part ) {
		$part = trim( $part );
		if ( '' !== $part && false === strpos( $out, $part ) ) {
			$missing[] = $part;
		}
	}
}
check( ! $missing, 'เงื่อนไขฯ · ข้อความเดิมอยู่ครบทุกบรรทัด' . ( $missing ? ' (ขาด: ' . implode( ' / ', array_slice( $missing, 0, 3 ) ) . ')' : '' ) );

/* ==========================================================
   5) สไตล์ตามกติกาของเจ้าของ
   ========================================================== */
$css = file_get_contents( get_template_directory() . '/assets/css/legal.css' );

check( false === strpos( $css, 'position: fixed' ), 'legal.css ไม่มีอะไรตรึงจอ จึงไม่บังแถบเมนูล่างบนมือถือ' );
check( false === stripos( $css, 'linear-gradient' ) && false === stripos( $css, 'backdrop-filter' ), 'ไม่มีไล่สีหรือกระจกฝ้าตามสเปกข้อ 1.1' );
check( preg_match( '/\.fxl \.dthumb img \{[^}]*height:\s*auto/s', $css ), 'รูปแบนเนอร์ใช้ height: auto จึงไม่ยืด' );
check( preg_match( '/\.fxl \.fx-phero \.slash \{\s*right:\s*-114px/s', $css ), 'บนมือถือลายเฉียงเลื่อนออกไปที่ -114px ไม่ชิดหัวข้อ' );
check( preg_match( '/\.fxl \.dside \{[^}]*position:\s*sticky/s', $css ), 'กล่องสารบัญติดอยู่กับจอตอนเลื่อน' );
check( preg_match( '/\.fxl \.rintro \{[^}]*box-shadow:\s*inset 4px 0 0 var\(--yellow\)/s', $css ), 'กล่องเกริ่นมีแถบเหลืองด้านซ้าย' );
check( false !== strpos( $css, 'min-width: 761px' ), 'บนคอมกล่องสารบัญกดพับไม่ได้' );

$js = file_get_contents( get_template_directory() . '/assets/js/legal.js' );
check( false === strpos( $js, 'localStorage' ), 'สคริปต์หน้ากฎหมายไม่บันทึกอะไรลงเครื่องผู้ใช้' );
check( false !== strpos( $js, 'max-width: 760px' ), 'พับกล่องสารบัญเฉพาะบนมือถือ' );
check( preg_match( '/addEventListener\(\s*.change./', $js ), 'จอกว้างข้ามเส้น 760px ทีหลัง กล่องสารบัญกางกลับให้เอง' );

/* ==========================================================
   6) ธีมต่อสายไฟล์ให้ครบ
   ========================================================== */
$fn = file_get_contents( get_template_directory() . '/functions.php' );
check( false !== strpos( $fn, "require get_template_directory() . '/inc/legal.php';" ), 'functions.php เรียกใช้ inc/legal.php' );
check( false !== strpos( $fn, 'assets/css/legal.css' ), 'functions.php โหลด legal.css ให้หน้ากฎหมาย' );
check( false !== strpos( $fn, 'assets/js/legal.js' ), 'functions.php โหลด legal.js ให้หน้ากฎหมาย' );
check( substr_count( $fn, "'template-legal.php'" ) >= 3, 'functions.php ต่อสายเทมเพลตเอกสารครบทุกจุด' );
check( substr_count( $fn, "'template-risk.php'" ) >= 3, 'functions.php ต่อสายเทมเพลตคำเตือนความเสี่ยงครบทุกจุด' );

$tpl = file_get_contents( get_template_directory() . '/template-legal.php' );
check( false !== strpos( $tpl, 'Template Name:' ), 'เทมเพลตเอกสารมีชื่อให้เลือกใน Page Attributes' );

/* ==========================================================
   7) ห้ามมีคำโฆษณาเกินจริง
   ========================================================== */
$all = implode( "\n", $html );
foreach ( array( 'การันตีกำไร', 'รับประกันกำไร', 'ไม่มีความเสี่ยง' ) as $banned ) {
	check( false === strpos( $all, $banned ), "ไม่มีคำโฆษณาเกินจริง: $banned" );
}

echo "\n";
if ( $fails ) {
	fwrite( STDERR, "ไม่ผ่าน $fails ข้อ (ผ่าน $passes ข้อ)\n" );
	exit( 1 );
}
echo "ครบทุกข้อ · ผ่าน $passes ข้อ\n";
