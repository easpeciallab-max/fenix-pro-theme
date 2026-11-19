<?php
/**
 * ตรวจหน้าแรก (ดีไซน์ที่อนุมัติ 16 พ.ย. 2026)
 * Run with: php tests/home-sections.php
 */
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
function get_permalink( $p = 0 ) { return 'https://example.test/post/'; }
function get_bloginfo( $what = '' ) { return 'FENIX PRO EA'; }
function get_option( $name, $default = false ) { return $default; }
function wp_reset_postdata() {}
function get_queried_object_id() { return 0; }
function get_the_ID() { return 0; }
function get_post_meta( $id, $key, $single = false ) { return ''; }
function is_front_page() { return true; }
function is_paged() { return false; }
function wp_trim_words( $text, $n = 55, $more = '…' ) { return $text; }
function get_the_excerpt( $p = 0 ) { return ''; }

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

/* ---------- ลำดับส่วนทั้งหน้า ---------- */
$ids = array();
foreach ( $x->query( '//section[@id]' ) as $sec ) {
	$ids[] = $sec->getAttribute( 'id' );
}

$expected = array( 'hero', 'products', 'about', 'problem', 'compare', 'workflow', 'features', 'dashboard', 'fit', 'evidence', 'requirements', 'team', 'pricing', 'explore', 'faq', 'cta', 'risk' );
check( $expected === $ids, 'ส่วนบนหน้าแรกเรียงตามแบบที่อนุมัติ: ' . implode( ' > ', $ids ) );

check( 1 === $x->query( '//h1' )->length, 'มี h1 เดียวในหน้า' );
check( 1 === $x->query( '//main[contains(@class,"fxh")]' )->length, 'เนื้อหาทั้งหมดอยู่ใต้ main.fxh (สไตล์ไม่รั่วไปหน้าอื่น)' );

/* ---------- ส่วนที่ถูกตัดถาวรต้องไม่กลับมา (สเปกข้อ 1.7.1) ---------- */
foreach ( array( 'เราไม่ได้ทำอะไร', 'สิ่งที่เราไม่ทำ', 'สิ่งที่ยังเป็นของคุณ' ) as $gone ) {
	check( false === strpos( $html, $gone ), "ไม่มีข้อความจากส่วนที่ตัดทิ้ง: $gone" );
}

/* ---------- 01 Hero ---------- */
check( false !== strpos( $html, 'PLUS 5.1 · DUAL 6.3 · BTC 1.1' ), 'ป้ายเวอร์ชันสร้างจากไฟล์ที่ตั้งไว้หน้าดาวน์โหลด' );
check( 3 === $x->query( '//div[contains(@class,"fan")]/a[contains(@class,"cv")]' )->length, 'สำรับปกมีครบ 3 ใบ' );
check( 1 === $x->query( '//a[contains(@class,"cv c3")]' )->length, 'มีปกใบหน้าสุดหนึ่งใบ' );

$proof = $x->query( '//div[contains(@class,"proof")]' );
check( 1 === $proof->length, 'มีป้ายผลทดสอบในหน้าแรก' );
$proof_text = $proof->item( 0 )->textContent;
check( false !== strpos( $proof_text, 'ทุน' ) && false !== strpos( $proof_text, 'DRAWDOWN' ), 'ป้ายผลทดสอบมีเงื่อนไขทุนและ DRAWDOWN กำกับ (ห้ามตัดออก)' );
check( false !== strpos( $proof_text, '9 เดือน' ), 'ป้ายผลทดสอบบอกช่วงเวลาที่ทดสอบ' );

/* ---------- 02 สินค้า ---------- */
check( 3 === $x->query( '//div[contains(@class,"prod-g")]/div[@class="prod"]' )->length, 'การ์ด EA ครบ 3 ใบ' );
check( 1 === $x->query( '//div[contains(@class,"prod-g")]//a[contains(@class,"fx-btn-or")]' )->length, 'ในแถวการ์ด EA มีปุ่มส้มใบเดียว' );
check( 1 === $x->query( '//div[contains(@class,"prod")]//span[contains(@class,"fx-badge or")]' )->length, 'ป้ายส้ม "ล่าสุด" มีใบเดียว' );

/* ---------- 04 ติ๊กปัญหา ---------- */
$chk = $x->query( '//button[@data-chk-item]' );
check( 4 === $chk->length, 'การ์ดติ๊กปัญหามี 4 ใบ' );
$pressed = 0;
foreach ( $chk as $node ) {
	if ( 'true' === $node->getAttribute( 'aria-pressed' ) ) {
		$pressed++;
	}
}
check( 0 === $pressed, 'เปิดหน้ามายังไม่มีข้อไหนถูกติ๊ก' );
check( false === strpos( $html, 'ตัวอย่างการติ๊ก' ), 'ไม่มีป้าย "ตัวอย่างการติ๊ก" แล้ว (ของจริงกดได้)' );
check( 1 === $x->query( '//span[@data-chk-out]' )->length, 'มีแถบสรุปจำนวนข้อที่ติ๊ก' );

/* ---------- 05 ตารางเทียบ ---------- */
$rows = $x->query( '//div[contains(@class,"vs-row")]' );
check( 6 === $rows->length, 'ตารางเทียบมี 6 แถว' );
check( 1 === $x->query( '//div[contains(@class,"vs-row") and contains(@class,"risk")]' )->length, 'มีแถวความเสี่ยงของตลาด 1 แถว' );

/* แถวความเสี่ยงต้องถูกต่อท้ายเสมอ แม้เจ้าของลบออกจากช่องแถวเทียบ */
$x2 = xp( render_front( array( 'vs_rows' => "ก | ข | ค" ) ) );
check( 1 === $x2->query( '//div[contains(@class,"vs-row") and contains(@class,"risk")]' )->length, 'ลบแถวอื่นหมด แถวความเสี่ยงยังอยู่' );

/* ---------- 06 ขั้นตอน ---------- */
check( 4 === $x->query( '//div[contains(@class,"wf-g")]/div[contains(@class,"wf")]' )->length, 'ขั้นตอนครบ 4 ขั้น' );
check( 1 === $x->query( '//div[contains(@class,"wf") and contains(@class,"hl")]' )->length, 'มีขั้นที่ถูกเน้นหนึ่งขั้น' );
check( false === strpos( $html, 'สามด่าน' ), 'แก้คำผิดเดิมแล้ว ไม่มีคำว่า "สามด่าน"' );
check( false !== strpos( $html, 'ทั้ง 4 ด่าน' ), 'ใช้ข้อความ "ทั้ง 4 ด่าน" ตามที่สเปกกำหนด' );

/* ---------- 07 จุดเด่น ---------- */
check( 6 === $x->query( '//div[contains(@class,"bento")]/div[contains(@class,"bt")]' )->length, 'กล่องจุดเด่นครบ 6 กล่อง' );
check( 2 === $x->query( '//div[contains(@class,"bt") and contains(@class,"w2")]' )->length, 'กล่องใหญ่ 2 กล่อง' );

/* ---------- 08 Dashboard ---------- */
check( 5 === $x->query( '//div[contains(@class,"feat-l")]/div' )->length, 'รายการความสามารถของ DASHBOARD ครบ 5 ข้อ' );
check( false !== strpos( $html, 'ตัวเลขในภาพเป็นตัวอย่างประกอบ' ), 'หมายเหตุว่าตัวเลขเป็นตัวอย่างยังอยู่' );
check( 1 === $x->query( '//span[contains(@class,"keep-case")]' )->length, 'ที่อยู่เว็บในภาพจำลองไม่ถูกบังคับเป็นตัวพิมพ์ใหญ่' );

/* ภาพหน้าจอจริงจะมาแทนภาพจำลอง */
$x3 = xp( render_front( array( 'dash_image' => 'https://example.test/shot.png' ) ) );
check( 1 === $x3->query( '//figure[contains(@class,"dash-shot")]' )->length, 'ใส่ภาพหน้าจอจริงแล้วใช้ภาพแทนภาพจำลอง' );
check( 0 === $x3->query( '//div[contains(@class,"browser")]' )->length, 'ใส่ภาพจริงแล้วไม่แสดงภาพจำลองซ้ำ' );

/* ---------- 09 เหมาะกับใคร ---------- */
check( 3 === $x->query( '//div[@class="pers"]' )->length, 'กลุ่มที่เหมาะมี 3 กลุ่ม' );
check( 6 === $x->query( '//div[contains(@class,"stop")]//li' )->length, 'รายการ "อย่าเพิ่งใช้" มี 6 ข้อ' );
check( false !== strpos( $html, 'คนที่มีทุนน้อยกว่า 10,000 USD' ), 'ข้อความเตือนเรื่องทุนขั้นต่ำยังอยู่' );

/* ---------- 10 ผลทดสอบ ---------- */
check( 2 === $x->query( '//div[@data-tabs]/button' )->length, 'มีแท็บ BACKTEST และ FORWARD TEST' );
$digits = array();
foreach ( $x->query( '//span[@data-digit]' ) as $node ) {
	$digits[] = $node->getAttribute( 'data-digit' );
}
check( array( '5', '1', '3', '3' ) === $digits, 'ตัวเลขพลิกตรงกับค่าที่ตั้งไว้ (+513.3%)' );
check( 2 === $x->query( '//div[contains(@class,"kpi") and contains(@class,"risk")]' )->length, 'ช่อง KPI ความเสี่ยงแสดงครบ 2 ช่อง' );
check( 9 === $x->query( '//div[@data-bars]/div' )->length, 'กราฟกำไรรายเดือน 9 เดือน' );
check( 5 === $x->query( '//div[@data-bars]/div[contains(@class,"sim")]' )->length, 'เดือนที่ราคาเป็นแบบจำลองมีพื้นเทา 5 เดือน' );
check( 1 === $x->query( '//div[contains(@class,"condbox")]' )->length, 'กล่องเงื่อนไขการทดสอบยังอยู่' );
check( false !== strpos( $html, 'ผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต' ), 'คำเตือนผลการทดสอบยังอยู่' );
check( 5 === $x->query( '//div[contains(@class,"fx-steps")]/div' )->length, 'ขั้นตอนตรวจสอบเราเอง 5 ข้อ' );

/* ---------- 11 ไม่มีส่วนรีวิวแล้ว (เจ้าของสั่งถอดออก 19 พ.ย. 2569) ---------- */
check( 0 === $x->query( '//section[@id="reviews"]' )->length, 'ไม่มีส่วนรีวิวบนหน้าแรก' );
check( false === strpos( $html, 'phone2' ), 'ไม่มีโครงกรอบโทรศัพท์ของรีวิวหลงเหลือ' );

/* ---------- 12 อุปกรณ์ ---------- */
check( 3 === $x->query( '//div[contains(@class,"req-g")]/div[contains(@class,"req")]' )->length, 'การ์ดอุปกรณ์ครบ 3 ใบ' );
check( 1 === $x->query( '//div[contains(@class,"req") and contains(@class,"hl")]' )->length, 'การ์ด VPS ถูกทำเป็นตัวแนะนำ' );
check( 3 === $x->query( '//div[contains(@class,"req-foot")]//span[not(@class)]' )->length, 'แถบข้อกำหนดแยกเป็น 3 ข้อ' );

/* ---------- 14 แพ็กเกจ ---------- */
check( 3 === $x->query( '//div[contains(@class,"price-g")]/div[contains(@class,"tk")]' )->length, 'การ์ดแพ็กเกจครบ 3 ใบ' );
check( 1 === $x->query( '//div[contains(@class,"tk") and contains(@class,"hl")]' )->length, 'มีแพ็กเกจแนะนำหนึ่งใบ' );
check( false !== strpos( $html, '6,990' ) && false !== strpos( $html, '9,990' ), 'ราคาแสดงบนหน้าแรก (ตามข้อกำหนดการแสดงราคา)' );

/* ---------- 16 FAQ ---------- */
check( 10 === $x->query( '//div[@data-faq]/div[contains(@class,"qa")]' )->length, 'คำถามที่พบบ่อย 10 ข้อ' );
check( 1 === $x->query( '//div[contains(@class,"qa") and contains(@class,"open")]' )->length, 'เปิดไว้ข้อเดียวตอนโหลดหน้า' );
check( 1 === $x->query( '//input[@data-faq-search]' )->length, 'มีช่องค้นหาคำถาม' );

/* ---------- 18 CTA + คำเตือน ---------- */
check( 1 === $x->query( '//section[@id="risk"]//div[contains(@class,"riskbox")]' )->length, 'กล่องคำเตือนความเสี่ยงอยู่ท้ายหน้าแรกเสมอ' );
check( false !== strpos( $html, 'risk-disclosure' ), 'มีลิงก์ไปคำเตือนความเสี่ยงฉบับเต็ม' );

/* ---------- คำต้องห้ามทางการตลาด ---------- */
foreach ( array( 'ปลอดภัย 100%', 'ไม่มีวันแตก', 'กำไรแน่นอน', 'รวยเร็ว', 'ได้กำไรทุกวัน' ) as $banned ) {
	check( false === strpos( $html, $banned ), "ไม่มีคำต้องห้าม: $banned" );
}

/* เรื่องการการันตีกำไร ต้องตอบปฏิเสธชัดเจนและมีคำเตือนครบ */
check( false !== strpos( $html, 'ไม่การันตี' ), 'คำถามเรื่องการันตีกำไร ตอบว่าไม่การันตี' );
check( false !== strpos( $html, 'ไม่ใช่ระบบรับประกันผลกำไร' ), 'คำเตือนย้ำว่าไม่ใช่ระบบรับประกันผลกำไร' );

/* ---------- ปุ่มสีส้มต้องใช้ตัวหนังสือสีขาวเสมอ ---------- */
$css = file_get_contents( get_template_directory() . '/assets/css/home.css' );
foreach ( array( '.fxh .fx-btn-or {', '.fxh .fx-btn-or:hover {' ) as $rule ) {
	$at    = strpos( $css, $rule );
	$block = false !== $at ? substr( $css, $at, strpos( $css, '}', $at ) - $at ) : '';
	check( '' !== $block && false !== strpos( $block, '#FFFFFF' ), "ปุ่มสีส้มใช้ตัวหนังสือสีขาว ($rule)" );
	check( false === strpos( $block, 'var(--on-or)' ), "ปุ่มสีส้มไม่ใช้ตัวหนังสือสีเข้ม ($rule)" );
}

/* ปุ่มรอง · พื้นใส ขอบขาว ตัวหนังสือขาว */
$at    = strpos( $css, '.fxh .fx-btn-dark {' );
$block = false !== $at ? substr( $css, $at, strpos( $css, '}', $at ) - $at ) : '';
check( false !== strpos( $block, 'background: transparent' ), 'ปุ่มรองพื้นใส' );
check( false !== strpos( $block, 'inset 0 0 0 2px #FFFFFF' ), 'ปุ่มรองมีขอบสีขาว' );
check( false !== strpos( $block, 'color: #FFFFFF' ), 'ปุ่มรองใช้ตัวหนังสือสีขาว' );

/* ยกเว้นบนการ์ดแพ็กเกจพื้นขาว ที่ต้องเป็นปุ่มทึบ ไม่งั้นมองไม่เห็น */
$at    = strpos( $css, '.fxh .tk.hl .fx-btn-dark {' );
$block = false !== $at ? substr( $css, $at, strpos( $css, '}', $at ) - $at ) : '';
check( false !== strpos( $block, 'box-shadow: none' ), 'ปุ่มรองบนการ์ดพื้นขาวไม่ใช้ขอบขาว' );

/* ---------- รูปทุกใบต้องมี alt ---------- */
$no_alt = 0;
foreach ( $x->query( '//img' ) as $img ) {
	if ( ! $img->hasAttribute( 'alt' ) ) {
		$no_alt++;
	}
}
check( 0 === $no_alt, 'รูปทุกใบมี alt' );

/* ---------- ตัวช่วยแยกข้อมูลตาราง ---------- */
$rows = fenix_rows( "ก | ข | ค\n! ง | จ | ฉ\n+ ช | ซ", 3 );
check( 3 === count( $rows ), 'fenix_rows อ่านครบทุกบรรทัด' );
check( '' === $rows[0]['flag'] && array( 'ก', 'ข', 'ค' ) === $rows[0]['cells'], 'fenix_rows แยกช่องด้วย | ได้ถูกต้อง' );
check( '!' === $rows[1]['flag'], 'fenix_rows อ่านเครื่องหมายเตือนหน้าบรรทัดได้' );
check( array( 'ช', 'ซ', '' ) === $rows[2]['cells'], 'fenix_rows เติมช่องที่ขาดให้ครบจำนวน' );

$hm = fenix_hm( 'กิน[ข้าว]' );
check( false !== strpos( $hm, '<span class="hm">ข้าว</span>' ), 'fenix_hm ซ่อนคำในวงเล็บเหลี่ยมบนมือถือ' );
check( '&lt;b&gt;' === fenix_hm( '<b>' ), 'fenix_hm หนี HTML ที่ผู้ใช้พิมพ์เข้ามา' );

/* ---------- กราฟแท่งเทียนวาดจากค่าที่ล็อกไว้ (ผลเหมือนเดิมทุกครั้ง) ---------- */
$a = fenix_candle_series( 11, 10, function ( $i ) { return 0.5; } );
$b = fenix_candle_series( 11, 10, function ( $i ) { return 0.5; } );
check( $a === $b, 'ชุดแท่งเทียนให้ผลเดิมทุกครั้ง ไม่สุ่มใหม่' );

$g = fenix_candles( $a, 640, 320, array( 'l' => 10, 'r' => 10, 't' => 46, 'b' => 20 ), array( 'grid' => 1 ) );
check( 10 === substr_count( $g['svg'], '<rect' ), 'วาดแท่งเทียนครบทุกแท่ง' );
check( false !== strpos( $g['svg'], '#22C55E' ) || false !== strpos( $g['svg'], '#EF4444' ), 'แท่งเทียนใช้สีขึ้น/ลงตามที่กำหนด' );

/* ---------- ปิด section แล้วต้องไม่พัง ---------- */
$off = array(
	'show_hero' => false, 'show_products' => false, 'show_about' => false, 'show_pain' => false,
	'show_vs' => false, 'show_steps' => false, 'show_features' => false, 'show_dash' => false,
	'show_fit' => false, 'show_perf' => false, 'show_devices' => false, 'show_team' => false,
	'show_pricing_home' => false, 'show_faq' => false, 'show_blog' => false, 'show_cta' => false,
);
$x6 = xp( render_front( $off ) );
check( 1 === $x6->query( '//h1' )->length, 'ปิดทุกส่วนแล้วยังมี h1 สำหรับ SEO' );
check( 1 === $x6->query( '//div[contains(@class,"riskbox")]' )->length, 'ปิดทุกส่วนแล้วคำเตือนความเสี่ยงยังอยู่' );

echo "\nครบทุกข้อ\n";
