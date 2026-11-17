<?php
/**
 * ตรวจหน้าบทความ + Footer ใหม่ ชุด 4 (ดีไซน์ที่อนุมัติ 17 พ.ย. 2026)
 *
 * งาน A: เทมเพลตหน้าบทความ ใช้กับบทความทุกเรื่อง
 * งาน B: Footer ใหม่ ใช้กับทุกหน้าของเว็บ
 *
 * Run with: php tests/post-footer-batch4.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods  = array();
$fx_content  = '';
$fx_title    = 'GRID EA คืออะไร? เข้าใจกลไกและความเสี่ยงก่อนนำไปใช้จริง';
$fx_thumb    = 'https://example.test/cover-1024x538.webp';
$fx_related  = 3;

function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function remove_action( ...$args ) {}
function apply_filters( $tag, $value ) {
	if ( 'the_content' === $tag ) {
		$value = fenix_collect_toc( $value );
		$value = fenix_wrap_content_tables( $value );
	}
	return $value;
}
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
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE ); }
function wp_kses_post( $value ) { return $value; }
function sanitize_html_class( $value ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $value ); }
function number_format_i18n( $number ) { return number_format( (float) $number ); }
function get_option( $name, $default = false ) { return $default; }
function get_queried_object_id() { return 0; }
function get_the_ID() { return 1; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function get_post_thumbnail_id( $post = 0 ) { return 7; }
function wp_get_attachment_image_url( $id, $size = '' ) { return ''; }
function get_page_by_path( $path ) { return (object) array( 'ID' => 2, 'slug' => $path ); }
function get_permalink( $post = 0 ) {
	if ( is_object( $post ) ) { return 'https://example.test/' . $post->slug . '/'; }
	return 'https://example.test/grid-ea-risks/';
}
function the_permalink() { echo esc_url( get_permalink() ); }
function post_class( $class = '' ) { echo 'class="post ' . esc_attr( $class ) . '"'; }
function get_bloginfo( $what = '' ) { return 'FENIX PRO EA'; }
function wp_footer() {}
function bloginfo( $what = '' ) { echo get_bloginfo( $what ); }
function fenix_has_seo_plugin_stub() {}
function get_header() {}
function get_footer() {}
function is_front_page() { return false; }
function is_paged() { return false; }
function is_category() { return false; }
function is_page() { return false; }
function is_single() { return true; }
function is_singular( $type = '' ) { return true; }
function is_main_query() { return true; }
function in_the_loop() { return true; }
function is_page_template( $template = '' ) { return false; }
function have_posts() { return ! isset( $GLOBALS['fx_done'] ); }
function the_post() { $GLOBALS['fx_done'] = true; }
function get_post_field( $field, $id = 0 ) { return 'grid-ea-risks'; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_title( $id = 0 ) { return $GLOBALS['fx_title']; }
function get_the_date( $format = '' ) { return 'c' === $format ? '2026-07-28T08:00:00+07:00' : '28 กรกฎาคม 2026'; }
function get_the_modified_date( $format = '' ) { return '16 พ.ย. 2026'; }
function get_the_author() { return 'FENIX PRO'; }
function get_the_category( $post = 0 ) { return array( (object) array( 'term_id' => 3, 'name' => 'EA BASICS' ) ); }
function get_category_link( $id ) { return 'https://example.test/category/ea-basics/'; }
function get_the_tag_list( $before = '', $sep = '', $after = '' ) { return ''; }
function wp_link_pages( $args = array() ) {}
function wp_reset_postdata() {}
function has_excerpt() { return false; }
function get_the_excerpt( $post = 0 ) { return ''; }
function has_post_thumbnail( $post = 0 ) { return '' !== $GLOBALS['fx_thumb']; }
function get_the_post_thumbnail_url( $post = null, $size = '' ) { return $GLOBALS['fx_thumb']; }
function the_post_thumbnail( $size = 'large', $attr = array() ) {
	$alt = isset( $attr['alt'] ) ? $attr['alt'] : '';
	echo '<img src="' . esc_url( $GLOBALS['fx_thumb'] ) . '" alt="' . esc_attr( $alt ) . '" width="1024" height="538">';
}
function the_content() { echo apply_filters( 'the_content', $GLOBALS['fx_content'] ); }
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

/** บทความที่เกี่ยวข้อง · การ์ดจำลอง */
class WP_Query {
	public $found_posts   = 0;
	public $max_num_pages = 0;
	public $i             = 0;
	public function __construct( $args = array() ) {}
	public function have_posts() { return $this->i < $GLOBALS['fx_related']; }
	public function the_post() { $this->i++; }
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

function render_post( $content, $mods = array() ) {
	global $theme_mods, $fx_content;
	unset( $GLOBALS['fx_done'] );
	$theme_mods = $mods;
	$fx_content = $content;
	ob_start();
	require get_template_directory() . '/single.php';
	return ob_get_clean();
}

function render_footer( $mods = array() ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/footer.php';
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
   เนื้อหาจำลอง · บทความรุ่นใหม่ และรุ่นเก่า
   ========================================================== */

$post_new = '
<p>Grid EA คือระบบที่แบ่งช่วงราคาออกเป็นหลายระดับ</p>
<p><!-- fenix-media-v3 --></p>
<nav aria-label="สารบัญ"><p><strong>สารบัญ</strong></p><ul><li><a href="#mechanism">Grid EA ทำงานอย่างไร</a></li></ul></nav>
<h2 id="mechanism">Grid EA ทำงานอย่างไร</h2>
<figure class="wp-block-image size-large"><img src="https://example.test/a.webp" alt="ภาพ"><figcaption class="wp-element-caption">ภาพประกอบ</figcaption></figure>
<p>สมมติระบบกำหนดระดับราคา</p>
<ul><li><strong>เก็บการแกว่งตัว</strong> เปิดซื้อด้านล่าง</li><li>เฉลี่ยราคาของสถานะ</li></ul>
<h3>Fixed Grid</h3>
<p>กำหนดระยะระหว่างแต่ละขั้นคงที่</p>
<table><thead><tr><th>หัวข้อ</th><th>รายละเอียด</th></tr></thead><tbody><tr><td>Grid</td><td>วางออเดอร์เป็นชั้น</td></tr></tbody></table>
<aside><p>ผลทดสอบย้อนหลังไม่รับประกันผลในอนาคต</p></aside>
<ol><li>ตรวจประวัติออเดอร์</li><li>ดู Margin Level</li></ol>
<pre><code>InpGridStep = 100</code></pre>
<p>ค่าที่ตั้งใน <code>InpGridStep</code> มีผลกับระยะ</p>
<p><small>ตรวจทานเนื้อหาล่าสุด: 24 กรกฎาคม 2026</small></p>
';

$post_old = '
<p class="wp-block-paragraph"><strong>สาเหตุหลักที่ทำให้ EA ได้รับความสนใจ เช่น</strong></p>
<p class="wp-block-paragraph">• ถ้าราคาแตะโซนที่กำหนด ให้เปิดออเดอร์<br>• ถ้ากำไรถึงเป้า ให้ปิด</p>
<h2>EA คืออะไร</h2>
<figure class="wp-block-table"><table class="has-fixed-layout"><tbody><tr><td>ข้อดี</td><td>ทำงานตามกฎ</td></tr></tbody></table></figure>
';

$mods = array(
	'line_url'       => 'https://line.me/R/ti/p/@fenixpro',
	'risk_text'      => 'การเทรดมีความเสี่ยงสูง',
	'show_mobile_nav' => true,
);

$html_new = render_post( $post_new, $mods );
$html_old = render_post( $post_old, $mods );
$x        = xp( $html_new );
$xo       = xp( $html_old );

/* ==========================================================
   1) โครงหน้าบทความ (ข้อ 2–3)
   ========================================================== */
check( 1 === $x->query( '//main[contains(@class,"fxp") and contains(@class,"fxa")]' )->length, 'หน้าบทความใช้ระบบดีไซน์ชุดเดียวกับหน้าภายใน' );
check( 1 === $x->query( '//header[contains(@class,"fx-phero") and contains(@class,"ahero")]' )->length, 'หัวบทความใช้หัวหน้าเพจแบบเดียวกับทั้งเว็บ' );
check( 1 === $x->query( '//header//div[@class="slash"]' )->length, 'หัวบทความมีลายเฉียงส้ม-ดำ' );
check( 1 === $x->query( '//header//div[contains(@class,"wrap") and contains(@class,"awrap")]' )->length, 'กรอบหัวบทความกว้างตามแบบ (awrap)' );

$crumb = $x->query( '//nav[@class="crumb"]' );
check( 1 === $crumb->length, 'มีลำดับหน้า' );
check( 'หน้าแรก' === plain( $x->query( '//nav[@class="crumb"]/a[1]' )->item( 0 )->textContent ), 'ลำดับหน้าเริ่มที่ "หน้าแรก"' );
check( 'EA BASICS' === plain( $x->query( '//nav[@class="crumb"]/a[2]' )->item( 0 )->textContent ), 'ลำดับหน้ามีชื่อหมวด' );
check( 1 === $x->query( '//nav[@class="crumb"]/span[@class="cur"]' )->length, 'ชื่อบทความในลำดับหน้าตัดเป็น "…" ได้ (span.cur)' );

check( 1 === $x->query( '//div[@class="ahead"]/div[@class="atext"]' )->length, 'หัวบทความแบ่งเป็นคอลัมน์ตัวหนังสือ' );
check( 1 === $x->query( '//div[@class="ahead"]/figure[@class="acover"]' )->length, 'รูปหน้าปกย้ายขึ้นมาอยู่ในหัวบทความ' );
check( 0 === $x->query( '//figure[contains(@class,"article-thumb")]' )->length, 'ไม่มีรูปหน้าปกซ้ำนอกหัวบทความ' );
check( 1 === $x->query( '//figure[@class="acover"]/img[@width="1024"]' )->length, 'รูปหน้าปกใช้ขนาด large เหมือนเดิม' );
check( 1 === $x->query( '//a[@class="acat"]' )->length, 'มีป้ายหมวดแคปซูลส้ม' );
check( 1 === $x->query( '//h1[@class="article-title"]' )->length, 'หัวข้อบทความเป็น H1 ตัวเดียว' );
check( 1 === $x->query( '//div[@class="ameta"]/span/time' )->length, 'มีวันที่เผยแพร่' );
check( false !== strpos( plain( $html_new ), 'เข้าชม' ), 'มียอดเข้าชมตามเดิม' );

check( 1 === $x->query( '//div[@class="abody"]//div[@class="agrid"]' )->length, 'เนื้อหาซ้าย-สารบัญขวา อยู่ในตารางเดียวกัน' );
check( 1 === $x->query( '//aside[@class="aside-col"]' )->length, 'มีคอลัมน์สารบัญ' );
check( 1 === $x->query( '//div[@class="amain"]/div[@class="entry-content"]' )->length, 'เนื้อหาบทความอยู่ในคอลัมน์ซ้าย' );

/* ==========================================================
   2) กล่องสารบัญของธีม
   ========================================================== */
$toc = $x->query( '//details[contains(@class,"toc") and contains(@class,"atoc")]' );
check( 1 === $toc->length, 'กล่องสารบัญเป็น <details> ตามแบบ' );
check( $toc->item( 0 )->hasAttribute( 'open' ), 'กล่องสารบัญกางไว้ในโค้ด (บนคอมจึงกางเสมอ)' );
check( 1 === $x->query( '//details[contains(@class,"atoc")]/summary' )->length, 'กล่องสารบัญมีหัวกล่องกดได้' );
check( 1 === $x->query( '//details[contains(@class,"atoc")]/summary/span[@class="chev"]' )->length, 'หัวกล่องมีปุ่มลูกศรสำหรับมือถือ' );
check( 1 === $x->query( '//details[contains(@class,"atoc")]/ol' )->length, 'รายการสารบัญเป็น <ol> เพื่อให้ใส่เลขด้วย CSS counter ได้' );
check( $x->query( '//li[contains(@class,"toc-l2")]' )->length >= 1, 'หัวข้อ H2 เข้าสารบัญ (toc-l2)' );
check( $x->query( '//li[contains(@class,"toc-l3")]' )->length >= 1, 'หัวข้อ H3 เข้าสารบัญ (toc-l3)' );
check( 0 === substr_count( $html_new, '<nav class="toc"' ), 'ไม่มีสารบัญแบบเดิมหลงเหลือ' );

$toc_links = $x->query( '//details[contains(@class,"atoc")]//a/@href' );
$ids       = array();
foreach ( $x->query( '//div[@class="entry-content"]//*[self::h2 or self::h3]/@id' ) as $attr ) {
	$ids[] = $attr->nodeValue;
}
$missing = 0;
foreach ( $toc_links as $href ) {
	if ( ! in_array( ltrim( $href->nodeValue, '#' ), $ids, true ) ) {
		$missing++;
	}
}
check( 0 === $missing && count( $ids ) > 0, 'ทุกลิงก์ในสารบัญชี้ไปหัวข้อที่มีจริงในบทความ' );
check( count( $ids ) === count( array_unique( $ids ) ), 'ไอดีของหัวข้อไม่ซ้ำกัน' );

/* ==========================================================
   3) เนื้อหาบทความ · ห้ามแก้ HTML หรือข้อความ
   ========================================================== */
$body = $x->query( '//div[@class="entry-content"]' )->item( 0 );
$inner = '';
foreach ( $body->childNodes as $node ) {
	$inner .= $body->ownerDocument->saveHTML( $node );
}

foreach ( array(
	'Grid EA คือระบบที่แบ่งช่วงราคาออกเป็นหลายระดับ',
	'สมมติระบบกำหนดระดับราคา',
	'เก็บการแกว่งตัว',
	'ตรวจทานเนื้อหาล่าสุด: 24 กรกฎาคม 2026',
	'InpGridStep = 100',
) as $needle ) {
	check( false !== strpos( plain( $inner ), $needle ), 'ข้อความในบทความคงเดิม: ' . mb_substr( $needle, 0, 28 ) );
}

check( false !== strpos( $inner, '<aside>' ), 'กล่องคำเตือนในบทความยังเป็น <aside> เหมือนเดิม' );
check( false !== strpos( $inner, '<nav' ), 'สารบัญย่อในเนื้อหายังอยู่ครบ' );
check( false !== strpos( $inner, 'wp-block-image' ), 'รูปในเนื้อหายังอยู่ครบ' );
check( false !== strpos( $inner, 'fenix-media-v3' ), 'คอมเมนต์ในเนื้อหาไม่ถูกลบ (ซ่อนด้วย CSS)' );
check( false !== strpos( $inner, '<figcaption' ), 'คำบรรยายใต้รูปยังอยู่' );
check( false !== strpos( $inner, '<div class="table-scroll"><table' ), 'ตารางถูกห่อด้วยกรอบเลื่อนด้านข้าง' );
check( false !== strpos( $inner, '<pre>' ) && false !== strpos( $inner, '<code>' ), 'บล็อกโค้ดยังอยู่' );

/* บทความรุ่นเก่า */
$body_old = $xo->query( '//div[@class="entry-content"]' )->item( 0 );
$inner_old = '';
foreach ( $body_old->childNodes as $node ) {
	$inner_old .= $body_old->ownerDocument->saveHTML( $node );
}
check( false !== strpos( $inner_old, '• ถ้าราคาแตะโซนที่กำหนด' ), 'บทความรุ่นเก่า · ย่อหน้าที่ขึ้นต้นด้วย • ไม่ถูกแปลงเป็นรายการ' );
check( false !== strpos( $inner_old, 'wp-block-table' ), 'บทความรุ่นเก่า · ตารางแบบบล็อกยังอยู่' );
check( 1 === $xo->query( '//details[contains(@class,"atoc")]' )->length, 'บทความรุ่นเก่ายังมีกล่องสารบัญของธีม' );

/* ==========================================================
   4) ท้ายบทความ
   ========================================================== */
check( 1 === $x->query( '//div[@class="article-share"]' )->length, 'มีแถวปุ่มแชร์' );
check( 'แชร์บทความนี้' === plain( $x->query( '//span[@class="article-share-label"]' )->item( 0 )->textContent ), 'ข้อความ "แชร์บทความนี้" คงเดิม' );
check( 1 === $x->query( '//a[contains(@class,"share-line")]' )->length, 'มีปุ่มแชร์ LINE' );
check( 1 === $x->query( '//a[contains(@class,"share-fb")]' )->length, 'มีปุ่มแชร์ Facebook' );
check( 1 === $x->query( '//button[contains(@class,"share-copy")]' )->length, 'มีปุ่มคัดลอกลิงก์' );
check( false !== strpos( $html_new, 'social-plugins.line.me/lineit/share?url=' ), 'ลิงก์แชร์ LINE เหมือนเดิม' );
check( false !== strpos( $html_new, 'facebook.com/sharer/sharer.php?u=' ), 'ลิงก์แชร์ Facebook เหมือนเดิม' );

check( 1 === $x->query( '//div[@class="article-disclaimer"]' )->length, 'มีกล่องคำเตือนความเสี่ยงท้ายบทความ' );
check( false !== strpos( plain( $html_new ), 'อ่านคำเตือนฉบับเต็ม' ), 'ลิงก์ "อ่านคำเตือนฉบับเต็ม" ยังอยู่' );
check( false !== strpos( $html_new, '/risk-disclosure/' ), 'ลิงก์คำเตือนชี้ไปหน้าคำเตือนความเสี่ยง' );

$cta = $x->query( '//section[contains(@class,"acta")]//div[@class="fx-cta"]' );
check( 1 === $cta->length, 'กล่อง CTA ใช้แบบเดียวกับหน้าอื่น' );
check( 1 === $x->query( '//section[contains(@class,"acta")]//a[contains(@class,"fx-btn-black")]' )->length, 'ปุ่มใน CTA เป็นปุ่มดำบนกล่องส้ม' );
check( false !== strpos( plain( $html_new ), 'มีคำถาม? ทักมาคุยกับเราได้เลย' ), 'ข้อความ CTA เดิม' );
check( 0 === substr_count( $html_new, 'cta--slim' ), 'ไม่มี CTA แบบเดิมหลงเหลือ' );

check( 1 === $x->query( '//section[contains(@class,"related-section")]' )->length, 'มีช่วงบทความที่เกี่ยวข้อง' );
check( 1 === $x->query( '//h2[@class="h2"]' )->length, 'หัวข้อ "บทความที่เกี่ยวข้อง" ใช้ class h2 ตามแบบ' );
check( 2 === $x->query( '//button[contains(@class,"rail-btn")]' )->length, 'มีปุ่มเลื่อนซ้ายขวา 2 ปุ่ม' );
check( 3 === $x->query( '//a[contains(@class,"post-card")]' )->length, 'ทั้งการ์ดเป็นลิงก์' );
check( 3 === $x->query( '//a[contains(@class,"post-card")]/img' )->length, 'การ์ดมีรูปครบ' );
check( 3 === $x->query( '//a[contains(@class,"post-card")]//span[@class="post-meta"]' )->length, 'การ์ดมีวันที่' );
check( 0 === $x->query( '//a[@class="post-card-thumb"]' )->length, 'ไม่มีโครงการ์ดแบบเดิมหลงเหลือ' );

/* ไม่มีอะไรลอยทับแถบเมนูล่างบนมือถือ */
check( 0 === substr_count( $html_new, 'reading-progress' ), 'ไม่มีแถบความคืบหน้าแบบเดิมลอยอยู่บนหน้า' );

/* ==========================================================
   5) Footer ใหม่ (ข้อ 6)
   ========================================================== */
$footer_mods = array(
	'line_url'          => 'https://line.me/R/ti/p/@fenixpro',
	'show_mobile_nav'   => true,
	'show_cookie_consent' => false,
	'facebook_url'      => 'https://www.facebook.com/fenixpro.trading',
	'instagram_url'     => 'https://www.instagram.com/fenixproea_th/',
	'tiktok_url'        => 'https://www.tiktok.com/@fenixpro.ea',
	'youtube_url'       => 'https://www.youtube.com/@FenixProExpertAdvisor',
);
$footer = render_footer( $footer_mods );
$xf     = xp( $footer );

check( 1 === $xf->query( '//footer[contains(@class,"site-footer")]' )->length, 'Footer ยังใช้ class เดิมของธีม' );
check( 1 === $xf->query( '//footer/div[@class="wrap"]' )->length, 'Footer ใช้กรอบ .wrap ตามแบบ' );
check( 1 === $xf->query( '//div[@class="footer-cta"]/div[@class="fslash"]' )->length, 'กล่องชวนทัก LINE มีลายเฉียง' );
check( 3 === $xf->query( '//div[@class="fslash"]/i' )->length, 'ลายเฉียงมี 3 แถบ' );
check( 1 === $xf->query( '//div[@class="footer-cta"]/div[@class="footer-cta-text"]' )->length, 'ข้อความในกล่องชวนทักถูกครอบตามแบบ' );
check( 1 === $xf->query( '//div[@class="footer-cta-text"]/a[contains(@class,"footer-primary")]' )->length, 'ปุ่ม LINE อยู่ใต้คำอธิบาย' );
check( false !== strpos( plain( $footer ), 'พร้อมเริ่มต้นใช้งาน FENIX PRO?' ), 'หัวข้อใน footer เป็นข้อความเดิม' );
check( false !== strpos( plain( $footer ), 'ทัก LINE Official Account' ), 'ปุ่ม LINE ใช้ข้อความเดิม' );

check( 1 === $xf->query( '//ol[@class="footer-prep-list"]' )->length, 'รายการ "ก่อนทัก LINE" เปลี่ยนเป็น <ol> เพื่อใช้เลข 1-4' );
check( 0 === $xf->query( '//ul[@class="footer-prep-list"]' )->length, 'ไม่มีรายการแบบเดิมหลงเหลือ' );
check( 4 === $xf->query( '//ol[@class="footer-prep-list"]/li' )->length, 'รายการ "ก่อนทัก LINE" ครบ 4 ข้อ' );
check( 0 === $xf->query( '//ol[@class="footer-prep-list"]//svg' )->length, 'ไม่มีไอคอนติ๊กในรายการแล้ว' );
check( false !== strpos( plain( $footer ), 'ทุนที่ต้องการใช้กับระบบ' ), 'ข้อความในรายการเดิมครบ' );

check( 1 === $xf->query( '//div[@class="footer-brand"]//a[@class="brand"]' )->length, 'โลโก้และชื่อแบรนด์ยังอยู่' );
check( 4 === $xf->query( '//div[contains(@class,"footer-social")]/a[@class="social-link"]' )->length, 'ไอคอนโซเชียลครบ 4 ตัว' );
check( 2 === $xf->query( '//div[@class="footer-tagline"]/p' )->length, 'คำอธิบายแบรนด์ 2 ย่อหน้าเหมือนเดิม' );
check( 4 === $xf->query( '//nav[@class="footer-legal"]/a' )->length, 'ลิงก์ด้านล่างครบ 4 ตัว' );
check( false !== strpos( plain( $footer ), 'สงวนลิขสิทธิ์' ), 'บรรทัดลิขสิทธิ์ยังอยู่' );
check( 1 === $xf->query( '//nav[contains(@class,"mobile-app-nav")]' )->length, 'แถบเมนูล่างบนมือถือยังเป็นของเดิม' );

/* ==========================================================
   6) CSS ตามสเปก
   ========================================================== */
$css = file_get_contents( get_template_directory() . '/assets/css/post.css' );

check( false === strpos( $css, 'position: fixed' ), 'post.css ไม่มีอะไรตรึงจอ จึงไม่บังแถบเมนูล่างบนมือถือ' );
check( false === stripos( $css, 'linear-gradient' ) && false === stripos( $css, 'backdrop-filter' ), 'ไม่มีไล่สีหรือกระจกฝ้าตามกฎรวม' );
check( false === stripos( $css, 'overflow-wrap: anywhere' ), 'ไม่ใช้ overflow-wrap: anywhere ที่ทำให้คำขาดกลางคำ' );

preg_match( '/\.fxp\.fxa \.entry-content :is\(th, td\)\s*\{([^}]*)\}/', $css, $m_td );
check( ! empty( $m_td ) && preg_match( '/word-break:\s*normal/', $m_td[1] ) && preg_match( '/overflow-wrap:\s*normal/', $m_td[1] ), 'เซลล์ตารางไม่ตัดคำกลางคำ' );

preg_match( '/\.fxp\.fxa \.entry-content table:has\(tr > :nth-child\(4\)\)\s*\{([^}]*)\}/', $css, $m_min );
check( ! empty( $m_min ) && preg_match( '/min-width:\s*620px/', $m_min[1] ), 'ตาราง 4 คอลัมน์ขึ้นไปกว้างอย่างน้อย 620px แล้วเลื่อนในกรอบ' );

preg_match( '/\.fxp\.fxa \.entry-content p:empty\s*\{([^}]*)\}/', $css, $m_empty );
check( ! empty( $m_empty ) && preg_match( '/display:\s*none/', $m_empty[1] ), 'ย่อหน้าว่างไม่กินที่' );

preg_match( '/\.fxp\.fxa \.acover img\s*\{([^}]*)\}/', $css, $m_cover );
check( ! empty( $m_cover ) && preg_match( '/height:\s*auto/', $m_cover[1] ), 'รูปหน้าปกไม่ยืด' );

preg_match( '/\.fxp\.fxa \.entry-content h2::before\s*\{([^}]*)\}/', $css, $m_bar );
check( ! empty( $m_bar ) && preg_match( '/background:\s*var\(--or\)/', $m_bar[1] ), 'หัวข้อ H2 มีขีดส้มด้านบน' );

preg_match( '/\.fxp\.fxa \.entry-content pre\s*\{([^}]*)\}/', $css, $m_pre );
check( ! empty( $m_pre ) && preg_match( '/font:\s*500 14\.5px\/1\.75/', $m_pre[1] ), 'บล็อกโค้ดกำหนดฟอนต์และระยะบรรทัดเอง' );

check( false !== strpos( $css, '.fxp.fxa .fx-phero .slash' ) && false !== strpos( $css, 'right: -114px' ), 'บนมือถือลายเฉียงไม่ชิดหัวข้อ' );
check( false !== strpos( $css, 'min-width: 761px' ), 'บนคอมกล่องสารบัญกดพับไม่ได้' );

preg_match( '/\.fxp\.fxa \.aside-col\s*\{([^}]*)\}/', $css, $m_side );
check( ! empty( $m_side ) && preg_match( '/position:\s*sticky/', $m_side[1] ), 'กล่องสารบัญติดจอตอนเลื่อน' );

preg_match( '/\.fxp\.fxa \.atoc\s*\{([^}]*)\}/', $css, $m_toc );
check( ! empty( $m_toc ) && preg_match( '/max-height:\s*calc\(100vh - 48px\)/', $m_toc[1] ) && preg_match( '/overflow:\s*auto/', $m_toc[1] ), 'สารบัญยาวเกินจอให้เลื่อนในกล่อง' );

$fcss = file_get_contents( get_template_directory() . '/assets/css/footer.css' );
check( false === strpos( $fcss, 'position: fixed' ), 'footer.css ไม่มีอะไรตรึงจอ' );
check( false === stripos( $fcss, 'linear-gradient' ) && false === stripos( $fcss, 'backdrop-filter' ), 'footer ไม่มีไล่สีหรือกระจกฝ้า' );

preg_match( '/\.site-footer \.footer-cta \{([^}]*)\}/', $fcss, $m_fcta );
check( ! empty( $m_fcta ) && preg_match( '/background:\s*var\(--surface\)/', $m_fcta[1] ), 'กล่องชวนทักใน footer เป็นพื้นเทาเข้ม ไม่ใช่สีส้ม' );

preg_match( '/\.site-footer \.footer-prep-list li::before\s*\{([^}]*)\}/', $fcss, $m_num );
check( ! empty( $m_num ) && preg_match( '/content:\s*counter\(fp\)/', $m_num[1] ), 'เลข 1-4 ใน footer มาจาก CSS counter' );

check( false !== strpos( $fcss, 'min-width: 761px) and (max-width: 1420px' ), 'เผื่อที่ให้ปุ่มลอย LINE ไม่ทับลิงก์ใน footer' );

preg_match( '/\.site-footer \.footer-primary \{([^}]*)\}/', $fcss, $m_fbtn );
check( ! empty( $m_fbtn ) && preg_match( '/color:\s*#FFFFFF/i', $m_fbtn[1] ), 'ปุ่ม LINE ใน footer ใช้ตัวหนังสือสีขาว' );

/* ==========================================================
   7) ธีมต่อสายไฟล์ให้ครบ
   ========================================================== */
$fn = file_get_contents( get_template_directory() . '/functions.php' );
check( false !== strpos( $fn, "assets/css/post.css" ), 'functions.php โหลด post.css ให้หน้าบทความ' );
check( false !== strpos( $fn, "assets/js/post.js" ), 'functions.php โหลด post.js ให้หน้าบทความ' );
check( false !== strpos( $fn, "assets/css/footer.css" ), 'functions.php โหลด footer.css ให้ทุกหน้า' );
check( substr_count( $fn, "is_singular( 'post' )" ) >= 3, 'หน้าบทความได้ทั้งสไตล์ ชุดที่ 1 และ class ของ body' );
check( false !== strpos( $fn, "'post_cta_title'" ) && false !== strpos( $fn, "'post_cta_text'" ), 'ข้อความ CTA ของหน้าบทความแก้ได้ใน Customizer' );

$cz = file_get_contents( get_template_directory() . '/inc/customizer.php' );
check( false !== strpos( $cz, "'post_cta_title'" ) && false !== strpos( $cz, "'post_cta_text'" ), 'Customizer มีช่องกรอก CTA ของหน้าบทความ' );

$js = file_get_contents( get_template_directory() . '/assets/js/post.js' );
check( false === strpos( $js, 'localStorage' ), 'สคริปต์หน้าบทความไม่บันทึกอะไรลงเครื่องผู้ใช้' );
check( false !== strpos( $js, 'max-width: 760px' ), 'พับกล่องสารบัญเฉพาะบนมือถือ' );
check( preg_match( '/addEventListener\(\s*.change./', $js ), 'จอกว้างข้ามเส้น 760px ทีหลัง กล่องสารบัญกางกลับให้เอง' );

echo "\n";
if ( $fails ) {
	echo "ไม่ผ่าน $fails ข้อ · ผ่าน $passes ข้อ\n";
	exit( 1 );
}
echo "ครบทุกข้อ · ผ่าน $passes ข้อ\n";
