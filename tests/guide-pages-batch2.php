<?php
/**
 * ตรวจหน้าคู่มือ ชุด 2 (ดีไซน์ที่อนุมัติ 17 พ.ย. 2026)
 * 8 หน้า: เปิดบัญชี · ติดตั้ง MT5 · ฝากเงิน · ติดตั้ง EA · คูณ LOT ·
 * VPS WINDOWS · VPS ANDROID · VPS IPHONE
 *
 * Run with: php tests/guide-pages-batch2.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();
$fx_slug    = '';
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
function esc_html__( $value, $domain = '' ) { return esc_html( $value ); }
function _x( $text, $context = '', $domain = '' ) { return $text; }
function wp_strip_all_tags( $value ) { return trim( strip_tags( (string) $value ) ); }
function sanitize_html_class( $value ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $value ); }
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
function is_page_template( $template = '' ) { return in_array( 'template-guide.php', (array) $template, true ); }
function have_posts() { return true; }
function the_post() {}
function get_post_field( $field, $id = 0 ) { return $GLOBALS['fx_slug']; }
function the_title() { echo esc_html( get_the_title() ); }
function get_the_title( $id = 0 ) { return 'หัวข้อคู่มือ ' . $GLOBALS['fx_slug']; }
function get_the_modified_date( $format = '' ) { return '16 พ.ย. 2026'; }
function has_excerpt() { return false; }
function get_the_excerpt( $post = 0 ) { return ''; }
function the_content() { echo fenix_guide_strip_inline_css( $GLOBALS['fx_content'] ); }
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

function render_guide( $slug, $content = '' ) {
	global $fx_slug, $fx_content, $theme_mods;
	$fx_slug    = $slug;
	$fx_content = $content;
	$theme_mods = array();
	ob_start();
	require get_template_directory() . '/template-guide.php';
	return ob_get_clean();
}

function xp( $html ) {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8"><div id="t">' . $html . '</div>' );
	libxml_clear_errors();
	return new DOMXPath( $dom );
}

$slugs = array_keys( fenix_guides() );

/* ==========================================================
   1) ลำดับ 8 ขั้น ต้องตรงกับสเปกข้อ 6
   ========================================================== */
$expected = array(
	'open-mt5-account',
	'mt5-login-zaurix-server',
	'deposit-zaurix',
	'fenix-pro-ea-install-guide',
	'fenix-dual-presets',
	'vps-desktop-windows',
	'vps-android',
	'vps-iphone-ios',
);
check( $expected === $slugs, 'ลำดับคู่มือ 8 ขั้นตรงกับสเปก: ' . implode( ' > ', $slugs ) );

/* ==========================================================
   2) โครงหน้าของทุกหน้า
   ========================================================== */
$html = array();

foreach ( $slugs as $slug ) {
	$html[ $slug ] = render_guide( $slug );
	$x             = xp( $html[ $slug ] );

	check( 1 === $x->query( '//main[@id="main" and contains(@class,"fxp") and contains(@class,"fxg")]' )->length, "$slug · เนื้อหาอยู่ใต้ main.fxp.fxg (ใช้ชุดสไตล์เดียวกับชุด 1)" );
	check( 1 === $x->query( '//h1' )->length, "$slug · มี h1 เดียวในหน้า" );
	check( 1 === $x->query( '//section[contains(@class,"fx-phero") and contains(@class,"ghero")]' )->length, "$slug · มีหัวหน้าเพจแบบคู่มือ" );
	check( 1 === $x->query( '//ol[@class="rail"]' )->length, "$slug · มีแถบ 8 ขั้น" );
	check( 8 === $x->query( '//ol[@class="rail"]/li' )->length, "$slug · แถบขั้นครบ 8 ช่อง" );
	check( 1 === $x->query( '//div[@class="gwrap"]/aside[@class="gside"]' )->length, "$slug · มีกล่องด้านข้าง" );
	check( 1 === $x->query( '//article[@class="gmain"]/div[@class="gc"]' )->length, "$slug · เนื้อหาอยู่ในคอลัมน์หลัก" );
	check( 1 === $x->query( '//nav[contains(@class,"toc")][@data-guide-toc]' )->length, "$slug · มีกล่องสารบัญ" );
	check( 1 === $x->query( '//div[contains(@class,"fx-cta")]' )->length, "$slug · มีกล่อง CTA ปิดท้าย" );
	check( 1 === $x->query( '//div[contains(@class,"fx-cta")]//a[contains(@href,"line")]' )->length, "$slug · กล่อง CTA มีปุ่มทัก LINE" );
	check( 0 === $x->query( '//*[contains(@style,"position:fixed") or contains(@style,"position: fixed")]' )->length, "$slug · ไม่มีอะไรลอยทับแถบเมนูล่างบนมือถือ" );

	/* ป้ายบอกขั้น */
	check( false !== strpos( $html[ $slug ], 'จาก 8' ), "$slug · ป้ายบอกว่าเป็นขั้นที่เท่าไรจาก 8" );
}

/* ==========================================================
   3) สถานะของแถบ 8 ขั้น คิดจากหน้าที่เปิดอยู่เท่านั้น
   ========================================================== */
$x = xp( $html['deposit-zaurix'] );
$state = array();
foreach ( $x->query( '//ol[@class="rail"]/li' ) as $li ) {
	$state[] = $li->getAttribute( 'class' );
}
check( array( 'done', 'done', 'now', '', '', '', '', '' ) === $state, 'ขั้นที่ 3 · ขั้นก่อนหน้าเป็น done ขั้นปัจจุบันเป็น now ที่เหลือว่าง' );

$first = xp( $html['open-mt5-account'] );
$state = array();
foreach ( $first->query( '//ol[@class="rail"]/li' ) as $li ) {
	$state[] = $li->getAttribute( 'class' );
}
check( 'now' === $state[0] && '' === $state[7], 'ขั้นแรก · ไม่มีขั้นไหนถูกทำเครื่องหมายว่าผ่านแล้ว' );

$last = xp( $html['vps-iphone-ios'] );
$done = 0;
foreach ( $last->query( '//ol[@class="rail"]/li' ) as $li ) {
	if ( 'done' === $li->getAttribute( 'class' ) ) {
		$done++;
	}
}
check( 7 === $done, 'ขั้นสุดท้าย · ขั้นก่อนหน้าทั้ง 7 เป็นสีเขียว' );

/* ลิงก์ของแต่ละช่องชี้ไปหน้าคู่มือนั้นจริง */
$links = array();
foreach ( $x->query( '//ol[@class="rail"]/li/a' ) as $link ) {
	$links[] = $link->getAttribute( 'href' );
}
$want = array();
foreach ( $slugs as $slug ) {
	$want[] = 'https://example.test/' . $slug . '/';
}
check( $want === $links, 'ทุกช่องในแถบขั้นลิงก์ไปหน้าคู่มือของขั้นนั้น' );

/* ==========================================================
   4) ปุ่มขั้นก่อนหน้า / ขั้นถัดไป
   ========================================================== */
$x = xp( $html['open-mt5-account'] );
check( 0 === $x->query( '//nav[@class="gnav"]/a[@class="prev"]' )->length, 'หน้าแรกของชุดไม่มีปุ่มขั้นก่อนหน้า' );
check( 1 === $x->query( '//nav[@class="gnav"]/a[@class="next"]' )->length, 'หน้าแรกของชุดมีปุ่มขั้นถัดไป' );

$x = xp( $html['vps-iphone-ios'] );
check( 1 === $x->query( '//nav[@class="gnav"]/a[@class="prev"]' )->length, 'หน้าสุดท้ายมีปุ่มขั้นก่อนหน้า' );
check( 0 === $x->query( '//nav[@class="gnav"]/a[@class="next"]' )->length, 'หน้าสุดท้ายไม่มีปุ่มขั้นถัดไป' );

$x = xp( $html['fenix-dual-presets'] );
$prev = $x->query( '//nav[@class="gnav"]/a[@class="prev"]' );
$next = $x->query( '//nav[@class="gnav"]/a[@class="next"]' );
check( 1 === $prev->length && 1 === $next->length, 'หน้ากลางมีปุ่มครบทั้งสองข้าง' );
check( $prev->length && false !== strpos( $prev->item( 0 )->getAttribute( 'href' ), 'fenix-pro-ea-install-guide' ), 'ปุ่มขั้นก่อนหน้าไปหน้าที่ถูกต้อง' );
check( $next->length && false !== strpos( $next->item( 0 )->getAttribute( 'href' ), 'vps-desktop-windows' ), 'ปุ่มขั้นถัดไปไปหน้าที่ถูกต้อง' );

$guides = fenix_guides();
check( $prev->length && false !== strpos( $prev->item( 0 )->textContent, $guides['fenix-pro-ea-install-guide']['label'] ), 'ชื่อขั้นในปุ่มใช้ข้อความเดิม' );

/* ==========================================================
   5) กล่องด้านข้าง · สิ่งที่ต้องเตรียม
   ========================================================== */
$x = xp( $html['open-mt5-account'] );
$prep = $x->query( '//aside[@class="gside"]/div[contains(@class,"gbox")][1]//li' );
check( count( $guides['open-mt5-account']['prep'] ) === $prep->length, 'กล่องสิ่งที่ต้องเตรียมแสดงครบทุกข้อ' );

/* ==========================================================
   6) ไม่มีระบบติ๊กหรือบันทึกความคืบหน้า (สเปกข้อ 0)
   ========================================================== */
$all = implode( "\n", $html );
foreach ( array( 'data-chk', 'localStorage', 'aria-pressed', 'progress' ) as $banned ) {
	check( false === strpos( $all, $banned ), "ไม่มีระบบติ๊กหรือบันทึกความคืบหน้า: $banned" );
}

$guide_js = file_get_contents( get_template_directory() . '/assets/js/guide.js' );
check( false === strpos( $guide_js, 'localStorage' ), 'สคริปต์หน้าคู่มือไม่บันทึกอะไรลงเครื่องผู้ใช้' );
check( false !== strpos( $guide_js, 'scrollLeft' ), 'บนมือถือเลื่อนแถบขั้นให้เห็นขั้นปัจจุบันตั้งแต่เปิดหน้า' );

$main_js = file_get_contents( get_template_directory() . '/assets/js/main.js' );
check( false === strpos( $main_js, 'is-reading' ), 'สารบัญไม่ติดตามว่าอ่านถึงข้อไหนแล้ว' );

/* ==========================================================
   7) <style> เดิมของ widget ต้องถูกถอดออก ข้อความต้องอยู่ครบ
   ========================================================== */
$sample = '<div class="fenix-vps-x"><style>.fenix-vps-x .step{background:#111}</style>'
	. '<div class="topbar"></div><main class="page"><header class="guide-head">'
	. '<div class="brand">FENIX PRO</div><p class="lead">คำโปรยเดิมของหน้า</p></header>'
	. '<section class="step"><div class="step-head"><div class="num">1</div><div><h2>หัวข้อขั้นที่ 1</h2>'
	. '<p>คำอธิบายเดิม</p></div></div><figure class="shot"><figcaption>คำบรรยายภาพ</figcaption>'
	. '<div class="img-wrap"><img src="https://example.test/a.webp" alt="ภาพ">'
	. '<div class="highlight" style="left:5%;top:10%;width:20%;height:8%;"></div></div>'
	. '<div class="callout">ข้อความเตือนเดิม</div></figure></section></main></div>';

$out = render_guide( 'vps-desktop-windows', $sample );
check( false === strpos( $out, '<style' ), 'สไตล์เดิมของ widget ถูกถอดออกก่อนแสดงผล' );
check( false === strpos( $out, 'background:#111' ), 'ไม่มีสีเดิมของ widget หลงเหลือ' );

foreach ( array( 'FENIX PRO', 'คำโปรยเดิมของหน้า', 'หัวข้อขั้นที่ 1', 'คำอธิบายเดิม', 'คำบรรยายภาพ', 'ข้อความเตือนเดิม' ) as $keep ) {
	check( false !== strpos( $out, $keep ), "ข้อความเดิมยังอยู่ครบ: $keep" );
}
check( false !== strpos( $out, 'a.webp' ), 'รูปเดิมยังอยู่' );
check( false !== strpos( $out, 'left:5%;top:10%;width:20%;height:8%;' ), 'ตำแหน่งไฮไลต์บนภาพใช้ค่าเดิมจากหน้า' );

/* ==========================================================
   8) สไตล์ตามกติกาของเจ้าของ
   ========================================================== */
$css = file_get_contents( get_template_directory() . '/assets/css/guide.css' );

check( false === strpos( $css, 'position: fixed' ), 'guide.css ไม่มีอะไรตรึงจอ จึงไม่บังแถบเมนูล่างบนมือถือ' );

/* ตารางในคู่มือ: มุมตรง · หัวข้อช่วงต้องไม่ชิดกล่องที่ตามมา */
preg_match( '/\.fxg \.gc table\s*\{([^}]*)\}/', $css, $m_gt );
check( ! empty( $m_gt ) && false === strpos( $m_gt[1], 'border-radius' ), 'ตารางในคู่มือเป็นมุมตรง ไม่ตัดเส้น' );

preg_match( '/\.fxg \.gc :is\(\.zag-section-title, \.mt5g-group-title\) \+ \*\s*\{([^}]*)\}/', $css, $m_st );
check( ! empty( $m_st ) && preg_match( '/margin-top:\s*(\d+)px/', $m_st[1], $m_stv ) && (int) $m_stv[1] >= 16, 'หัวข้อช่วงเว้นระยะจากกล่องที่ตามมา' );

preg_match( '/\.fxg \.gc \* \+ :is\(\.zag-steps,[^)]*\)\s*\{([^}]*)\}/', $css, $m_sw );
check( ! empty( $m_sw ) && preg_match( '/margin-top:\s*(\d+)px/', $m_sw[1], $m_swv ) && (int) $m_swv[1] >= 16, 'กล่องรวมขั้นตอนเว้นระยะจากของที่อยู่ข้างบน' );
check( false === stripos( $css, 'linear-gradient' ) && false === stripos( $css, 'backdrop-filter' ), 'ไม่มีไล่สีหรือกระจกฝ้าตามสเปกข้อ 1.1' );

preg_match( '/\.fxg \.gc \.mt5g-store-btn\.mt5g-download\s*\{([^}]*)\}/', $css, $m_or );
check( ! empty( $m_or ) && preg_match( '/background:\s*var\(--or\)/', $m_or[1] ), 'ปุ่มดาวน์โหลดเป็นปุ่มหลักพื้นส้ม' );
check( ! empty( $m_or ) && preg_match( '/color:\s*#FFFFFF/i', $m_or[1] ), 'ปุ่มดาวน์โหลดใช้ตัวหนังสือสีขาว' );

preg_match( '/\.fxg \.gc :is\(\.zag-btn, \.mt5g-store-btn\)\s*\{([^}]*)\}/', $css, $m_out );
check( ! empty( $m_out ) && false !== strpos( $m_out[1], 'background: transparent' ), 'ปุ่มในเนื้อหาเป็นปุ่มรองพื้นใส' );
check( ! empty( $m_out ) && preg_match( '/box-shadow:\s*inset 0 0 0 1\.5px #FFFFFF/i', $m_out[1] ), 'ปุ่มรองมีขอบขาว 1.5px' );

preg_match( '/\.fxg \.gnav a\.next\s*\{([^}]*)\}/', $css, $m_next );
check( ! empty( $m_next ) && preg_match( '/background:\s*var\(--or\)/', $m_next[1] ) && preg_match( '/color:\s*#FFFFFF/i', $m_next[1] ), 'ปุ่มขั้นถัดไปเป็นปุ่มหลักส้มตัวหนังสือขาว' );

/* ---------- ทุกคลาสเดิมในตารางแปลง (สเปกข้อ 4) ต้องมีสไตล์ใหม่ ---------- */
$legacy = array(
	'zag-hero', 'mt5g-hero', 'zag-lead', 'mt5g-lead', 'lead', 'summary', 'zag-summary-card',
	'zag-btn', 'mt5g-store-btn', 'zag-num', 'mt5g-num', 'num', 'zag-nav', 'mt5g-nav',
	'zag-hero-grid', 'mt5g-device-grid', 'mt5g-install-grid', 'mt5g-store-btns', 'mt5g-zoom-grid',
	'mt5g-trouble-grid', 'grid', 'zag-step', 'mt5g-step', 'step', 'step-head', 'zag-text',
	'mt5g-text', 'zag-kicker', 'mt5g-kicker', 'zag-check', 'mt5g-check', 'checklist', 'zag-tip',
	'mt5g-mini-note', 'mt5g-note', 'zag-shot', 'mt5g-shot', 'shot', 'wp-block-image', 'mt5g-zoom',
	'img-wrap', 'mt5g-zoom-caption', 'zag-ring', 'mt5g-ring', 'highlight', 'zag-callout',
	'mt5g-callout', 'callout', 'zag-arrow', 'mt5g-arrow', 'zag-final-card', 'mt5g-device-card',
	'mt5g-store-card', 'mt5g-store-top', 'mt5g-app-icon', 'mt5g-device-ic', 'mt5g-download-ic',
	'zag-copybox', 'mt5g-group-title', 'mt5g-trouble-card', 'brand', 'topbar', 'guide-head',
	'wp-block-table', 'table-scroll', 'wp-element-caption',
);

$missing = array();
foreach ( $legacy as $class ) {
	if ( false === strpos( $css, '.' . $class ) ) {
		$missing[] = $class;
	}
}
check( ! $missing, 'ทุกคลาสเดิมในตารางแปลงมีสไตล์ใหม่' . ( $missing ? ' (ขาด: ' . implode( ', ', $missing ) . ')' : '' ) );

/* ==========================================================
   9) ธีมต่อสายไฟล์ให้ครบ
   ========================================================== */
$fn = file_get_contents( get_template_directory() . '/functions.php' );
check( false !== strpos( $fn, "assets/css/guide.css" ), 'functions.php โหลด guide.css ให้หน้าคู่มือ' );
check( false !== strpos( $fn, "assets/js/guide.js" ), 'functions.php โหลด guide.js ให้หน้าคู่มือ' );
check( false !== strpos( $fn, "'fenix-guide'" ), 'หน้าคู่มือได้คลาสของตัวเองไว้ใช้กับสไตล์' );
check( false !== strpos( $fn, 'fenix_guide_strip_inline_css' ), 'functions.php ถอด <style> เดิมของหน้าคู่มือ' );
check( false !== strpos( $fn, "'arrow-l'" ), 'มีไอคอนลูกศรซ้ายสำหรับปุ่มขั้นก่อนหน้า' );

echo "\n";
if ( $fails ) {
	fwrite( STDERR, "ไม่ผ่าน $fails ข้อ (ผ่าน $passes ข้อ)\n" );
	exit( 1 );
}
echo "ครบทุกข้อ · ผ่าน $passes ข้อ\n";
