<?php
/**
 * ตรวจหน้าภายในชุด 1 (ดีไซน์ที่อนุมัติ 17 พ.ย. 2026)
 * /backtest/ · /forward-test/ · /pricing/ · /about/ · /articles/ + หน้าหมวดหมู่
 *
 * Run with: php tests/pages-batch1.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();
$fx_title   = '';
$fx_content = '';

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
/* ภาษาไทยนับความยาวเป็นตัวอักษร ไม่ใช่คำ — จำลองให้เหมือนเว็บจริง */
function _x( $text, $context = '', $domain = '' ) { return 'words' === $text ? 'characters_excluding_spaces' : $text; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_html_class( $value ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $value ); }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function wp_json_encode( $value ) { return json_encode( $value, JSON_UNESCAPED_UNICODE ); }
function number_format_i18n( $number ) { return number_format( (float) $number ); }
function get_option( $name, $default = false ) { return 'posts_per_page' === $name ? 12 : $default; }
function get_queried_object_id() { return 0; }
function get_the_ID() { return 0; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function get_post_thumbnail_id( $post = 0 ) { return 0; }
function wp_get_attachment_image_url( $id, $size = '' ) { return ''; }
function get_header() {}
function get_footer() {}
function wp_reset_postdata() { $GLOBALS['fx_i'] = -1; }
function wp_trim_words( $text, $count = 55, $more = '…' ) {
	/* WordPress ภาษาไทยตัดตามจำนวนตัวอักษร */
	$text = trim( (string) $text );
	$seen = preg_split( '//u', str_replace( ' ', '', $text ), -1, PREG_SPLIT_NO_EMPTY );

	if ( count( $seen ) <= $count ) {
		return $text;
	}

	$chars = preg_split( '//u', $text, -1, PREG_SPLIT_NO_EMPTY );
	$out   = '';
	$kept  = 0;

	foreach ( $chars as $char ) {
		if ( $kept >= $count ) {
			break;
		}
		$out .= $char;
		if ( ' ' !== $char ) {
			$kept++;
		}
	}

	return $out . $more;
}
function is_front_page() { return false; }
function is_paged() { return false; }

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

/* ---------- บทความตัวอย่างสำหรับหน้ารวมบทความ ---------- */
$GLOBALS['fx_posts'] = array();
for ( $fx_n = 1; $fx_n <= 5; $fx_n++ ) {
	$GLOBALS['fx_posts'][] = array(
		'title' => 'บทความทดสอบเรื่องที่ ' . $fx_n,
		'date'  => ( 10 + $fx_n ) . ' พฤศจิกายน 2026',
		'cat'   => 'EA Trust',
		'ex'    => 'คำโปรยของบทความทดสอบเรื่องที่ ' . $fx_n . ' อย่าเริ่มเงินจริงทันทีหลังได้ไฟล์ ควรตรวจการส่งมอบ ยืนยันเวอร์ชันและสิทธิ์การใช้งาน ทดสอบบนบัญชีทดลอง แล้วจัดเก็บเอกสารให้กู้ระบบคืนได้เสมอ',
	);
}
$GLOBALS['fx_i'] = -1;

/**
 * คิวรีหลักของ WordPress (หน้าหมวดหมู่ใช้ตัวนี้)
 */
class WP_Query {
	public $found_posts   = 86;
	public $max_num_pages = 8;
	public function __construct( $args = array() ) {}
	public function have_posts() { return $GLOBALS['fx_i'] < count( $GLOBALS['fx_posts'] ) - 1; }
	public function the_post() { $GLOBALS['fx_i']++; }
}

function fx_cur() { return $GLOBALS['fx_posts'][ max( 0, $GLOBALS['fx_i'] ) ]; }
function have_posts() { return true; }
function the_post() {}
function the_content() { echo $GLOBALS['fx_content']; }
function get_the_content() { return $GLOBALS['fx_content']; }
function the_permalink() { echo 'https://example.test/articles/sample/'; }
function the_title() { echo esc_html( fx_cur()['title'] ); }
function get_the_title( $id = 0 ) {
	return $GLOBALS['fx_i'] >= 0 ? fx_cur()['title'] : $GLOBALS['fx_title'];
}
function get_the_date( $format = '' ) { return fx_cur()['date']; }
function get_the_excerpt( $post = 0 ) { return fx_cur()['ex']; }
function has_post_thumbnail( $post = 0 ) { return true; }
function get_the_post_thumbnail_url( $post = null, $size = '' ) { return 'https://example.test/cover.webp'; }
function get_the_category( $post = 0 ) { return array( (object) array( 'name' => fx_cur()['cat'] ) ); }
function single_cat_title( $prefix = '', $display = true ) { return 'EA TRUST'; }
function category_description() { return 'คำอธิบายหมวดหมู่'; }
function get_queried_object() { return (object) array( 'slug' => 'ea-trust', 'count' => 12 ); }

require get_template_directory() . '/functions.php';

$fails = 0;
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

function render( $template, $title = '', $content = '', $mods = array() ) {
	global $theme_mods, $fx_title, $fx_content;
	$theme_mods      = $mods;
	$fx_title        = $title;
	$fx_content      = $content;
	$GLOBALS['fx_i']        = -1;
	$GLOBALS['wp_query']    = new WP_Query();
	ob_start();
	require get_template_directory() . '/' . $template;
	$out             = ob_get_clean();
	$GLOBALS['fx_i'] = -1;
	return $out;
}

function xp( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8"><div id="t">' . $html . '</div>' );
	libxml_clear_errors();
	return new DOMXPath( $dom );
}

/** ข้อความล้วนแบบบีบช่องว่าง ใช้เทียบว่าเนื้อหาเดิมอยู่ครบ */
function plain( $html ) {
	$text = html_entity_decode( strip_tags( (string) $html ), ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', str_replace( "\xc2\xa0", ' ', $text ) ) );
}

/* ==========================================================
   เนื้อหาจำลอง · โครงเดียวกับที่เจ้าของเขียนไว้ในตัวแก้ไข
   ========================================================== */

$content_backtest = '
<p>หน้านี้รวมผลทดสอบย้อนหลังของ EA ทุกตัว</p>
<h2>FENIX BTC 1.1 · ทองคำ (BTCUSD)</h2>
<p>ผลทดสอบย้อนหลังของ FENIX BTC 1.1 บนบัญชีทดสอบ</p>
<figure class="wp-block-image size-large"><img src="https://example.test/btc.webp" alt="สรุปผล BTC"></figure>
<figure class="wp-block-table"><table>
<thead><tr><th>รายการ</th><th>LOT0.01</th><th>LOT0.02</th></tr></thead>
<tbody>
<tr><td>ช่วงเวลาทดสอบ</td><td>2017-2025</td><td>2017-2025</td></tr>
<tr><td>กำไรสุทธิ</td><td>24,170.59</td><td>48,341.18</td></tr>
<tr><td>เฉลี่ยต่อเดือน</td><td>241.70</td><td>483.41</td></tr>
<tr><td>DD สูงสุด</td><td>12.4%</td><td>12.4%</td></tr>
<tr><td>ยอดเงินสุดท้าย</td><td>27,170.59</td><td>—</td></tr>
</tbody></table></figure>
<p>เงื่อนไขที่ใช้ทดสอบ: ทุนเริ่มต้น 3,000 USD ช่วงปี 2017-2025 โบรกเกอร์ ZAURIX</p>
<p><strong>⚠ คำเตือน: ผลทดสอบย้อนหลังไม่ได้รับประกันผลลัพธ์ในอนาคต</strong></p>
<ul><li><a href="https://example.test/files/btc-lot001.pdf">รายงาน LOT0.01 (PDF)</a></li>
<li><a href="https://example.test/files/btc-lot002.pdf">รายงาน LOT0.02 (PDF)</a></li></ul>
<h2>FENIX PLUS 5.1 · ทองคำ (XAUUSD)</h2>
<p>ผลทดสอบย้อนหลังของ FENIX PLUS 5.1 แบบ STANDARD</p>
<figure class="wp-block-image"><img src="https://example.test/plus-a.webp" alt="กราฟ PLUS A"></figure>
<figure class="wp-block-image"><img src="https://example.test/plus-b.webp" alt="กราฟ PLUS B"></figure>
<figure class="wp-block-table"><table><tbody>
<tr><td>กำไรสุทธิ</td><td>61,355.78</td></tr>
<tr><td>DD ที่แย่สุด</td><td>44.44%</td></tr>
</tbody></table></figure>
<p><strong>⚠ คำเตือน: ความเสี่ยงสูง ใช้เฉพาะเงินที่รับความเสี่ยงได้</strong></p>
<h2>FENIX DUAL 6.3 · ทองคำ (XAUUSD)</h2>
<p>ผลทดสอบย้อนหลังของ FENIX DUAL 6.3</p>
<figure class="wp-block-table"><table><tbody>
<tr><td>กำไรสุทธิ</td><td>199,052.86</td></tr>
<tr><td>เฉลี่ยต่อเดือน</td><td>คิดตามค่าคูณ</td></tr>
</tbody></table></figure>
<p>ดูตัวคูณ LOT ที่เหมาะกับทุนของคุณได้ที่หน้า <a href="https://example.test/fenix-dual-presets/">เลือกคูณ LOT</a></p>
';

$content_about = '
<h2>เราคือใคร</h2>
<p>FENIX PRO EA คือระบบช่วยเทรดอัตโนมัติบน MetaTrader 5</p>
<p>แนวทางของเราไม่ได้มอง EA เป็นเครื่องมือวิเศษหรือระบบที่ไม่มีวันขาดทุน</p>
<h2>ทำไมเราสร้าง FENIX PRO</h2>
<p>ปัญหาที่เราเห็นบ่อยคือ เทรดเดอร์จำนวนมากไม่ได้ขาดทุนเพียงเพราะกลยุทธ์ไม่ดี</p>
<p>FENIX PRO EA จึงถูกวางแนวคิดให้ช่วยจัดระเบียบการเข้าออกออเดอร์</p>
<h2>สิ่งที่เรายึดถือ</h2>
<ul>
<li>ความโปร่งใส: เราไม่สัญญาผลกำไรเกินจริง</li>
<li>วินัยก่อนผลลัพธ์: เราเชื่อว่าการอยู่รอดในตลาดระยะยาวสำคัญกว่า</li>
<li>Risk-first setup: การตั้งค่าระบบควรเริ่มจากทุน</li>
<li>การดูแลผู้ใช้: เราให้ความสำคัญกับคู่มือและการติดตั้ง</li>
</ul>
<h2>ทำไมต้องเลือก FENIX PRO EA</h2>
<ul>
<li>ออกแบบสำหรับการใช้งานบน MetaTrader 5</li>
<li>มีคู่มือประกอบการติดตั้งและการใช้งาน</li>
<li>มีทีมช่วยเหลือผ่าน LINE</li>
</ul>
<h2>สิ่งที่เราไม่ทำ</h2>
<ul>
<li>ไม่รับประกันผลกำไรหรือผลตอบแทนในอนาคต</li>
<li>ไม่แนะนำให้ใช้เงินจำเป็นมาเทรด</li>
</ul>
<h2>คำเตือนความเสี่ยง</h2>
<p>การเทรด FOREX, GOLD, CFD มีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ทั้งหมดของเงินทุน</p>
<h2>ติดต่อเรา</h2>
<p>สนใจสอบถามรายละเอียด ทักทีมงานได้ทาง LINE Official Account:&nbsp;<a href="https://line.me/R/ti/p/@fenixpro">https://line.me/R/ti/p/@fenixpro</a></p>
<p>ปรับปรุงล่าสุด: 3 กรกฎาคม 2026</p>
';

/* ==========================================================
   1) ร่วมกันทุกหน้า
   ========================================================== */

$pages = array(
	'template-backtest.php' => array( 'Backtest', $content_backtest ),
	'template-forward.php'  => array( 'Forward Test', '' ),
	'template-pricing.php'  => array( 'แพ็กเกจและราคา', '' ),
	'template-about.php'    => array( 'เกี่ยวกับเรา', $content_about ),
	'template-blog.php'     => array( 'บทความ', '' ),
	'category.php'          => array( '', '' ),
);

$html = array();

foreach ( $pages as $tpl => $args ) {
	$html[ $tpl ] = render( $tpl, $args[0], $args[1] );
	$x            = xp( $html[ $tpl ] );

	check( 1 === $x->query( '//main[@id="main" and contains(@class,"fxp")]' )->length, "$tpl · เนื้อหาอยู่ใต้ main.fxp (สไตล์ไม่รั่วไปหน้าอื่น)" );
	check( 1 === $x->query( '//h1' )->length, "$tpl · มี h1 เดียวในหน้า" );
	check( 0 === $x->query( '//*[contains(@style,"position:fixed") or contains(@style,"position: fixed")]' )->length, "$tpl · ไม่มีอะไรลอยทับแถบเมนูล่างบนมือถือ" );
}

/* ---------- ปุ่มตามกติกาของเจ้าของ (อ่านจาก CSS จริง) ---------- */
$css = file_get_contents( get_template_directory() . '/assets/css/pages.css' );

check( false === strpos( $css, 'position: fixed' ) && false === strpos( $css, 'position: sticky' ),
	'pages.css ไม่มีอะไรตรึงจอ จึงไม่บังแถบเมนูล่างบนมือถือ' );

preg_match( '/--or:\s*(#[0-9A-Fa-f]{6})/', $css, $m_token );
check( ! empty( $m_token ) && 0 === strcasecmp( $m_token[1], '#FF6A00' ), 'สีส้มของหน้าภายในคือ #FF6A00' );

preg_match( '/\.fxp \.fx-btn-or\s*\{([^}]*)\}/', $css, $m_or );

/* ตาราง: เส้นสีเดียวกันทั้งตาราง (ไม่เอาสีตัวหนังสือมาเป็นสีเส้น) และมุมตรง */
preg_match( '/\.fxp :is\(\.table-scroll, \.wp-block-table\) :is\(th, td\)\s*\{([^}]*)\}/', $css, $m_tb );
check( ! empty( $m_tb ) && preg_match( '/border-right:\s*1px solid var\(--line\)/', $m_tb[1] ), 'เส้นตารางใช้สีเส้นมาตรฐาน ไม่ใช่สีตัวหนังสือ' );
check( ! empty( $m_tb ) && preg_match( '/border-left:\s*0/', $m_tb[1] ) && preg_match( '/border-bottom:\s*0/', $m_tb[1] ), 'ตัดเส้นที่ซ้อนกันออก เหลือเส้นละชุด' );

preg_match( '/\.fxp \.dtable\s*\{([^}]*)\}/', $css, $m_dt );
check( ! empty( $m_dt ) && false === strpos( $m_dt[1], 'border-radius' ), 'ตารางตัวเลขเป็นมุมตรง ไม่ตัดเส้น' );
check( false === strpos( $css, 'border-radius: 20px 0 0 0' ), 'ตารางเปรียบเทียบในหน้าราคาเป็นมุมตรงเหมือนกัน' );
check( ! empty( $m_or ) && preg_match( '/background:[^;]*(var\(--or\)|#FF6A00)/i', $m_or[1] ), 'ปุ่มหลักใช้พื้นส้ม #FF6A00' );
check( ! empty( $m_or ) && preg_match( '/color:\s*#FFFFFF/i', $m_or[1] ), 'ปุ่มหลักใช้ตัวหนังสือสีขาว' );

preg_match( '/\.fxp \.fx-btn-out\s*\{([^}]*)\}/', $css, $m_out );
check( ! empty( $m_out ) && false !== strpos( $m_out[1], 'transparent' ), 'ปุ่มรองพื้นหลังใส' );
check( ! empty( $m_out ) && preg_match( '/color:\s*#FFFFFF/i', $m_out[1] ), 'ปุ่มรองใช้ตัวหนังสือสีขาว' );
check( ! empty( $m_out ) && preg_match( '/(box-shadow|border)[^;]*#FFFFFF/i', $m_out[1] ), 'ปุ่มรองมีขอบสีขาว' );

check( false !== strpos( $css, 'overflow-wrap: break-word' ), 'ข้อความยาวอย่าง URL ตัดบรรทัดได้ ไม่ล้นกล่องบนมือถือ' );

/* ---------- functions.php ต่อสายให้ครบ ---------- */
$fn = file_get_contents( get_template_directory() . '/functions.php' );
check( false !== strpos( $fn, "require get_template_directory() . '/inc/pages.php';" ), 'functions.php เรียกใช้ inc/pages.php' );

foreach ( array( 'template-backtest.php', 'template-forward.php', 'template-pricing.php', 'template-about.php', 'template-blog.php' ) as $tpl ) {
	check( substr_count( $fn, "'" . $tpl . "'" ) >= 2, "functions.php โหลด CSS และใส่คลาสให้ $tpl" );
}
check( substr_count( $fn, 'is_category()' ) >= 2, 'หน้าหมวดหมู่ได้ทั้ง CSS และคลาสของหน้าภายใน' );

/* ==========================================================
   2) /backtest/ · เนื้อหาเดิมต้องอยู่ครบทุกตัวอักษร
   ========================================================== */

$bt = $html['template-backtest.php'];
$x  = xp( $bt );

$ids = array();
foreach ( $x->query( '//section[@id]' ) as $sec ) {
	$ids[] = $sec->getAttribute( 'id' );
}
check( array( 'btc', 'plus', 'dual' ) === $ids, 'BACKTEST · ช่วงของ EA เรียงตามเนื้อหาเดิม: ' . implode( ' > ', $ids ) );

$jump = $x->query( '//div[@class="jump"]/a' );
check( 3 === $jump->length, 'BACKTEST · ปุ่มกระโดดครบตามจำนวน EA' );
$targets = array();
foreach ( $jump as $link ) {
	$targets[] = ltrim( $link->getAttribute( 'href' ), '#' );
}
check( $targets === $ids, 'BACKTEST · ปุ่มกระโดดชี้ไปช่วงที่มีจริงทุกปุ่ม' );

/* ข้อความเดิมอยู่ครบ */
$src     = xp( '<div>' . $content_backtest . '</div>' );
$missing = array();
foreach ( $src->query( '//p|//li|//td|//th|//h2' ) as $node ) {
	$text = plain( $node->textContent );
	if ( '' === $text ) {
		continue;
	}
	/* สัญลักษณ์ ⚠ ถูกแทนด้วยไอคอนตามแบบ ข้อความที่เหลือต้องอยู่ครบ */
	$text = trim( str_replace( '⚠', '', $text ) );
	if ( false === strpos( plain( $bt ), $text ) ) {
		$missing[] = $text;
	}
}
check( ! $missing, 'BACKTEST · ข้อความเดิมอยู่ครบทุกบรรทัด' . ( $missing ? ' (ขาด: ' . implode( ' / ', array_slice( $missing, 0, 3 ) ) . ')' : '' ) );

check( 2 === $x->query( '//img[contains(@src,"plus-")]' )->length, 'BACKTEST · รูปเดิมอยู่ครบ ไม่ถูกตัด' );
check( 1 === $x->query( '//div[@class="g2"]' )->length, 'BACKTEST · รูปสองใบติดกันจัดเป็นคู่ตามแบบ' );
check( 1 === $x->query( '//div[@class="g-sum"]' )->length, 'BACKTEST · ช่วงที่มีรูปสรุปวางรูปคู่กับตาราง' );

check( 1 === $x->query( '//div[contains(@class,"note") and contains(@class,"cond")]' )->length, 'BACKTEST · ย่อหน้าเงื่อนไขกลายเป็นกล่องเงื่อนไข' );
$warn = $x->query( '//div[contains(@class,"note") and contains(@class,"warn")]' );
check( 2 === $warn->length, 'BACKTEST · คำเตือนทุกข้อยังอยู่ครบ' );
check( false === strpos( $bt, '⚠' ), 'BACKTEST · ใช้ไอคอนแทนสัญลักษณ์ ⚠ ตามแบบ' );
check( $warn->length && false !== strpos( plain( $warn->item( 0 )->textContent ), 'คำเตือน: ผลทดสอบย้อนหลังไม่ได้รับประกัน' ), 'BACKTEST · ข้อความคำเตือนไม่ถูกตัดทิ้ง' );

$reports = $x->query( '//div[@class="reports"]/a' );
check( 2 === $reports->length, 'BACKTEST · ลิงก์รายงาน PDF กลายเป็นปุ่มครบทุกไฟล์' );
foreach ( $reports as $link ) {
	check( false !== strpos( $link->getAttribute( 'class' ), 'fx-btn-out' ), 'BACKTEST · ปุ่มรายงานเป็นปุ่มรอง: ' . plain( $link->textContent ) );
}

/* ตาราง */
check( 3 === $x->query( '//table[contains(@class,"dtable")]' )->length, 'BACKTEST · ตารางเดิมทุกตารางได้รูปแบบใหม่' );

$cell = function ( $label, $class ) use ( $x ) {
	$nodes = $x->query( '//tr[td[1][normalize-space()="' . $label . '"]]/td[2]' );
	return $nodes->length && false !== strpos( $nodes->item( 0 )->getAttribute( 'class' ), $class );
};
check( $cell( 'กำไรสุทธิ', 'g' ), 'BACKTEST · ช่องกำไรสุทธิใช้สีเขียว' );
check( $cell( 'เฉลี่ยต่อเดือน', 'b' ), 'BACKTEST · ช่องเฉลี่ยต่อเดือนใช้สีฟ้า' );
check( $cell( 'DD สูงสุด', 'r' ), 'BACKTEST · ช่อง DD ใช้สีแดง' );
check( $cell( 'DD ที่แย่สุด', 'r' ), 'BACKTEST · ช่อง DD ที่แย่สุดใช้สีแดง' );

$blank = $x->query( '//tr[td[1][normalize-space()="ยอดเงินสุดท้าย"]]/td[3]' );
check( $blank->length && false !== strpos( $blank->item( 0 )->getAttribute( 'class' ), 'm' ), 'BACKTEST · ช่องที่ไม่มีค่าใช้สีจาง' );
$calc = $x->query( '//td[contains(text(),"คิดตามค่าคูณ")]' );
check( $calc->length && false !== strpos( $calc->item( 0 )->getAttribute( 'class' ), 'm' ), 'BACKTEST · ช่อง "คิดตามค่าคูณ" ใช้สีจาง ไม่ใช่สีตัวเลข' );
check( 0 === $x->query( '//tr[td[1][normalize-space()="ช่วงเวลาทดสอบ"]]/td[2][@class]' )->length, 'BACKTEST · แถวที่ไม่ใช่ตัวเลขผลลัพธ์ไม่ถูกใส่สี' );

/* กล่องชวนเลือกคูณ LOT */
check( 1 === $x->query( '//div[@class="lotcta"]' )->length, 'BACKTEST · ย่อหน้าชวนเลือกคูณ LOT กลายเป็นกล่องปุ่ม' );
check( 1 === $x->query( '//div[@class="lotcta"]//a[contains(@class,"fx-btn-or") and contains(@href,"fenix-dual-presets")]' )->length,
	'BACKTEST · ปุ่มเลือกคูณ LOT ใช้ลิงก์เดิมจากเนื้อหา' );

/* ==========================================================
   3) /about/
   ========================================================== */

$ab = $html['template-about.php'];
$x  = xp( $ab );

$src     = xp( '<div>' . $content_about . '</div>' );
$missing = array();
$ab_plain = plain( $ab );
foreach ( $src->query( '//p|//li|//h2' ) as $node ) {
	$text = plain( $node->textContent );
	if ( '' === $text || false !== strpos( $ab_plain, $text ) ) {
		continue;
	}

	/* การ์ด "สิ่งที่เรายึดถือ" แยกหัวข้อกับคำอธิบายออกจากกันตามแบบ ทั้งสองส่วนต้องอยู่ครบ */
	$cut = strpos( $text, ':' );
	if ( false !== $cut ) {
		$head = trim( substr( $text, 0, $cut ) );
		$rest = trim( substr( $text, $cut + 1 ) );
		if ( '' !== $head && '' !== $rest && false !== strpos( $ab_plain, $head ) && false !== strpos( $ab_plain, $rest ) ) {
			continue;
		}
	}

	$missing[] = $text;
}
check( ! $missing, 'เกี่ยวกับเรา · ข้อความเดิมอยู่ครบทุกบรรทัด' . ( $missing ? ' (ขาด: ' . implode( ' / ', array_slice( $missing, 0, 3 ) ) . ')' : '' ) );

check( 1 === $x->query( '//div[@class="who"]' )->length, 'เกี่ยวกับเรา · ช่วงแรกเป็นข้อความคู่กับภาพ' );
check( 1 === $x->query( '//div[@class="who"]//figure[contains(@class,"fx-shot")]' )->length, 'เกี่ยวกับเรา · ภาพแบนเนอร์อยู่ในช่วงแรก' );

$ab_img = $x->query( '//div[@class="who"]//figure//img' );
check( $ab_img->length && '' !== $ab_img->item( 0 )->getAttribute( 'width' ) && '' !== $ab_img->item( 0 )->getAttribute( 'height' ),
	'เกี่ยวกับเรา · ภาพแบนเนอร์บอกขนาดไว้ หน้าจึงไม่เด้งตอนรูปโหลด' );
check( array( 1024, 538 ) === fenix_pages_img_size( 'https://example.test/a-1024x538.webp' ), 'ขนาดรูปอ่านจากชื่อไฟล์ย่อของ WordPress ได้' );
check( array( 1200, 630 ) === fenix_pages_img_size( 'https://example.test/a.webp' ), 'รูปที่ไม่บอกขนาดในชื่อไฟล์ใช้สัดส่วนสำรอง' );
check( 1 === $x->query( '//div[@class="why"]' )->length, 'เกี่ยวกับเรา · ช่วงเหตุผลเป็นหัวข้อคู่กับย่อหน้า' );
check( 1 === $x->query( '//div[@class="why"]//p[@class="q"]' )->length, 'เกี่ยวกับเรา · ย่อหน้าแรกของช่วงเหตุผลเป็นข้อความเน้น' );

$vals = $x->query( '//div[@class="vals"]/div[@class="val"]' );
check( 4 === $vals->length, 'เกี่ยวกับเรา · การ์ดสิ่งที่ยึดถือครบ 4 ใบ' );
$heads = array();
foreach ( $vals as $val ) {
	$h3      = $val->getElementsByTagName( 'h3' );
	$heads[] = $h3->length ? plain( $h3->item( 0 )->textContent ) : '';
}
check( array( 'ความโปร่งใส', 'วินัยก่อนผลลัพธ์', 'Risk-first setup', 'การดูแลผู้ใช้' ) === $heads,
	'เกี่ยวกับเรา · หัวการ์ดตัดตรงเครื่องหมาย : พอดี ไม่ขาดกลางคำ: ' . implode( ' / ', $heads ) );

check( 1 === $x->query( '//div[@class="two"]/div[contains(@class,"fx-card") and not(contains(@class,"stopcard"))]' )->length, 'เกี่ยวกับเรา · การ์ดเหตุผลอยู่ซ้าย' );
check( 1 === $x->query( '//div[@class="two"]/div[contains(@class,"stopcard")]' )->length, 'เกี่ยวกับเรา · การ์ดสิ่งที่เราไม่ทำอยู่ขวาคู่กัน' );
check( 2 === $x->query( '//div[contains(@class,"stopcard")]//span[contains(@class,"no")]' )->length, 'เกี่ยวกับเรา · ทุกข้อของสิ่งที่เราไม่ทำมีเครื่องหมายกำกับ' );

$risk = $x->query( '//div[contains(@class,"riskcard")]' );
check( 1 === $risk->length, 'เกี่ยวกับเรา · กล่องคำเตือนความเสี่ยงยังอยู่' );
check( $risk->length && false !== strpos( plain( $risk->item( 0 )->textContent ), 'อาจขาดทุนได้ทั้งหมดของเงินทุน' ), 'เกี่ยวกับเรา · ข้อความคำเตือนความเสี่ยงไม่ถูกตัด' );

check( 1 === $x->query( '//div[contains(@class,"fx-cta")]//h2[normalize-space()="ติดต่อเรา"]' )->length, 'เกี่ยวกับเรา · ติดต่อเราเป็นกล่องปิดท้าย' );
check( 1 === $x->query( '//p[@class="upd"]' )->length, 'เกี่ยวกับเรา · บรรทัดปรับปรุงล่าสุดแยกออกมาใต้กล่อง' );
check( 1 === $x->query( '//div[contains(@class,"fx-cta")]//a[contains(@class,"keep-case") and contains(@href,"line.me")]' )->length,
	'เกี่ยวกับเรา · ลิงก์ที่เป็น URL คงตัวพิมพ์เล็กไว้' );

/* ==========================================================
   4) /forward-test/
   ========================================================== */

$fwd      = $html['template-forward.php'];
$x        = xp( $fwd );
$defaults = fenix_defaults();

check( 1 === $x->query( '//a[contains(@class,"fx-btn-or") and contains(@href,"myfxbook")]' )->length, 'FORWARD · ปุ่มหลักเปิดบัญชีจริงบน MYFXBOOK' );
check( false !== strpos( plain( $fwd ), plain( $defaults['forward_intro'] ) ), 'FORWARD · คำอธิบายเดิมยังอยู่ครบ' );
check( false !== strpos( plain( $fwd ), plain( $defaults['forward_note'] ) ), 'FORWARD · หมายเหตุเดิมยังอยู่ครบ' );
check( false !== strpos( plain( $fwd ), plain( $defaults['forward_disclaimer'] ) ), 'FORWARD · คำเตือนความเสี่ยงยังอยู่ครบ' );
check( 1 === $x->query( '//div[contains(@class,"note") and contains(@class,"warn")]' )->length, 'FORWARD · คำเตือนแสดงเป็นกล่องเตือน' );
check( 1 === $x->query( '//div[contains(@class,"fx-cta")]//a[contains(@href,"line")]' )->length, 'FORWARD · กล่องปิดท้ายมีปุ่มทัก LINE' );

/* ---- ข้อที่ย้ายมาจาก tests/forward-page.php เดิม ---- */
check( false !== strpos( $fwd, 'myfxbook.com/portfolio/eafn/12227282' ), 'FORWARD · ปุ่มชี้ไปบัญชีจริงของเรา' );
check( false !== strpos( $fwd, 'Backtest' ), 'FORWARD · อธิบายว่า FORWARD TEST ต่างจาก BACKTEST อย่างไร' );
check( false !== strpos( $fwd, 'Drawdown' ), 'FORWARD · บอกให้ดู DRAWDOWN ไม่ใช่ดูแต่กำไร' );
check( false !== strpos( $fwd, 'ไม่มีการตัดขาดทุนอัตโนมัติ' ), 'FORWARD · คำเตือนบอกตรง ๆ ว่าไม่มีการตัดขาดทุน' );
check( false !== strpos( $fwd, 'FENIX PLUS' ), 'FORWARD · บอกชัดว่าบัญชีนี้รัน FENIX PLUS' );
check( false !== strpos( $fwd, 'FENIX BTC' ) && false !== strpos( $fwd, 'FENIX DUAL' ), 'FORWARD · เตือนว่าใช้แทนผลของอีกสองตัวไม่ได้' );
check( 1 === substr_count( $fwd, 'line.me' ), 'FORWARD · มีทางติดต่อทีมงานท้ายหน้าจุดเดียว' );
check( false === strpos( $fwd, 'ระบุ' ), 'FORWARD · ไม่มีคำว่า "ระบุ..." ค้างอยู่บนหน้า' );
check( false === strpos( $fwd, 'เช่น XAUUSD' ), 'FORWARD · ไม่มีตัวอย่างค่าที่ไม่ใช่ข้อมูลจริง' );
check( false === strpos( $fwd, 'shot-placeholder' ), 'FORWARD · ไม่มีกรอบรูปเปล่า' );

$fwd_esc = render( 'template-forward.php', 'Forward Test', '', array( 'forward_intro' => 'Real <b>' ) );
check( false !== strpos( $fwd_esc, 'Real &lt;b&gt;' ), 'FORWARD · ค่าที่กรอกถูก escape' );

$fwd_img = render( 'template-forward.php', 'Forward Test', '', array( 'forward_img' => 'https://example.test/fw.png', 'forward_img_caption' => 'กราฟยอดเงินบัญชีจริง' ) );
check( false !== strpos( $fwd_img, 'fw.png' ) && false !== strpos( $fwd_img, 'กราฟยอดเงินบัญชีจริง' ), 'FORWARD · ใส่รูปได้เมื่อมีรูปจริงให้ลง' );
check( false === strpos( $fwd_img, 'class="bw"' ), 'FORWARD · พอมีรูปจริงแล้วภาพประกอบกรอบเบราว์เซอร์หลีกให้' );

/* ==========================================================
   5) /pricing/
   ========================================================== */

$pr = $html['template-pricing.php'];
$x  = xp( $pr );

check( 3 === $x->query( '//div[contains(@class,"price-g")]/div[contains(@class,"tk")]' )->length, 'แพ็กเกจ · การ์ดครบ 3 ใบ' );
check( 1 === $x->query( '//div[contains(@class,"tk") and contains(@class,"hl")]' )->length, 'แพ็กเกจ · มีใบแนะนำใบเดียว' );
check( 1 === $x->query( '//div[contains(@class,"price-g")]//a[contains(@class,"fx-btn-or")]' )->length, 'แพ็กเกจ · ปุ่มส้มมีใบเดียว (ใบที่แนะนำ)' );
check( 2 === $x->query( '//div[contains(@class,"price-g")]//a[contains(@class,"fx-btn-out")]' )->length, 'แพ็กเกจ · ใบที่เหลือใช้ปุ่มรองพื้นใส' );

foreach ( array( 1, 2, 3 ) as $slot ) {
	$price = trim( (string) $defaults[ 'pkg' . $slot . '_price' ] );
	if ( '' !== $price ) {
		check( false !== strpos( $pr, esc_html( $price ) ), "แพ็กเกจ · แสดงราคาของแพ็กเกจที่ $slot (กฎหมายไทยบังคับให้แสดงราคา)" );
	}
}

check( 1 === $x->query( '//table[contains(@class,"cmp")]' )->length, 'แพ็กเกจ · มีตารางเปรียบเทียบ' );
check( $x->query( '//table[contains(@class,"cmp")]//th[contains(@class,"pro")]' )->length >= 1, 'แพ็กเกจ · คอลัมน์ PRO ถูกเน้นในตาราง' );
check( $x->query( '//table[contains(@class,"cmp")]//span[contains(@class,"dot")]' )->length > 0, 'แพ็กเกจ · ช่องมี/ไม่มีใช้เครื่องหมายแทนข้อความ' );
check( $x->query( '//span[@class="sr-only"]' )->length > 0, 'แพ็กเกจ · เครื่องหมายในตารางมีข้อความสำหรับโปรแกรมอ่านหน้าจอ' );

$pr_contact = render( 'template-pricing.php', 'แพ็กเกจและราคา', '', array( 'pricing_mode' => 'contact' ) );
check( false !== strpos( $pr_contact, 'สอบถาม' ), 'แพ็กเกจ · โหมดสอบถามราคายังทำงานเหมือนเดิม' );

/* ==========================================================
   6) /articles/ และหน้าหมวดหมู่
   ========================================================== */

$bl = $html['template-blog.php'];
$x  = xp( $bl );

check( 1 === $x->query( '//article[@class="feat"]' )->length, 'บทความ · เรื่องล่าสุดเป็นการ์ดใหญ่ใบเดียว' );
check( 4 === $x->query( '//div[@data-posts-grid]/article[@class="art"]' )->length, 'บทความ · เรื่องที่เหลือเรียงในกริด' );
check( 1 === $x->query( '//button[contains(@class,"load-more-btn") and @data-card="art"]' )->length, 'บทความ · ปุ่มโหลดเพิ่มบอกให้ใช้การ์ดแบบใหม่' );
check( 1 === $x->query( '//button[contains(@class,"load-more-btn") and contains(@class,"fx-btn-out")]' )->length, 'บทความ · ปุ่มโหลดเพิ่มเป็นปุ่มรอง' );
check( false !== strpos( $bl, 'class="count"' ), 'บทความ · แสดงจำนวนบทความทั้งหมด' );

/* คำโปรยต้องยาวพอตามแบบ · ภาษาไทยนับเป็นตัวอักษร ถ้าใช้จำนวน "คำ" ตรง ๆ จะถูกตัดเหลือไม่กี่ตัวอักษร */
$feat_ex = $x->query( '//article[@class="feat"]//p' );
$art_ex  = $x->query( '//article[@class="art"]//p' );
check( $feat_ex->length && mb_strlen( plain( $feat_ex->item( 0 )->textContent ) ) > 90, 'บทความ · คำโปรยของการ์ดใหญ่ยาวตามแบบ ไม่ถูกตัดสั้นเกินไป' );
check( $art_ex->length && mb_strlen( plain( $art_ex->item( 0 )->textContent ) ) > 90, 'บทความ · คำโปรยของการ์ดเล็กยาวตามแบบ ไม่ถูกตัดสั้นเกินไป' );

$js = file_get_contents( get_template_directory() . '/assets/js/main.js' );
check( false !== strpos( $js, "data.append('card'" ), 'โหลดเพิ่ม · ส่งชนิดการ์ดไปกับคำขอ' );
check( false !== strpos( $js, '[data-posts-grid]' ), 'โหลดเพิ่ม · ต่อท้ายกริดของหน้าบทความแบบใหม่' );
check( false !== strpos( $fn, "'art' === \$card" ), 'โหลดเพิ่ม · ฝั่งเซิร์ฟเวอร์วาดการ์ดแบบใหม่ให้' );

$cat = $html['category.php'];
$x   = xp( $cat );
check( 1 === $x->query( '//article[@class="feat"]' )->length, 'หน้าหมวดหมู่ · ใช้แบบเดียวกับหน้ารวมบทความ' );
check( false !== strpos( $cat, 'EA TRUST' ), 'หน้าหมวดหมู่ · ใช้ชื่อหมวดหมู่เป็นหัวเรื่อง' );
check( false !== strpos( $cat, 'คำอธิบายหมวดหมู่' ), 'หน้าหมวดหมู่ · ใช้คำอธิบายหมวดหมู่เป็นคำโปรย' );
check( false !== strpos( $cat, 'category_name' ), 'หน้าหมวดหมู่ · ปุ่มโหลดเพิ่มดึงเฉพาะหมวดหมู่นั้น' );

/* ==========================================================
   7) ห้ามมีคำโฆษณาเกินจริง
   ========================================================== */

$all = implode( "\n", $html );
foreach ( array( 'การันตีกำไร', 'รับประกันกำไร', 'ไม่มีความเสี่ยง', 'รวยเร็ว', 'กำไรแน่นอน' ) as $banned ) {
	check( false === strpos( $all, $banned ), "ไม่มีคำโฆษณาเกินจริง: $banned" );
}

echo "\n";
if ( $fails ) {
	fwrite( STDERR, "ไม่ผ่าน $fails ข้อ (ผ่าน $passes ข้อ)\n" );
	exit( 1 );
}
echo "ครบทุกข้อ · ผ่าน $passes ข้อ\n";
