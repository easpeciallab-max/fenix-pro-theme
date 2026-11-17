<?php
/**
 * ตรวจหน้า /go/ (ดีไซน์ที่อนุมัติ 16 พ.ย. 2026)
 * Run with: php tests/link-hub-downloads.php
 */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; use the real theme and template.
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
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function language_attributes() { echo 'lang="th"'; }
function bloginfo( $key ) { echo 'UTF-8'; }
function body_class( $classes ) { echo 'class="' . esc_attr( $classes ) . '"'; }
function wp_head() {}
function wp_body_open() {}
function wp_footer() {}

require get_template_directory() . '/functions.php';

function check( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
	echo "PASS: $message\n";
}

function render_go( $mods = array() ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/template-links.php';
	$html = ob_get_clean();

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	libxml_clear_errors();
	$xpath = new DOMXPath( $dom );

	return array( $html, $xpath, $xpath->query( '//a[contains(concat(" ", normalize-space(@class), " "), " ea ")]' ) );
}

/** ดึง src ของรูปในการ์ดใบที่ n */
function card_src( $cards, $i ) {
	$img = $cards->item( $i )->getElementsByTagName( 'img' )->item( 0 );

	return $img ? $img->getAttribute( 'src' ) : '';
}

list( $html, $x, $cards ) = render_go();

/* ---------- โครงหน้า ---------- */
check( 1 === substr_count( $html, '<!DOCTYPE html>' ), 'หน้า /go/ เป็นหน้าเดี่ยว วาดเอกสารเองทั้งหน้า' );
check( false !== strpos( $html, 'link-hub-page' ), 'ยังมี body class เดิม เผื่อปลั๊กอินอ้างถึง' );
check( 1 === $x->query( '//div[@class="fxgo"]' )->length, 'เนื้อหาอยู่ใต้ .fxgo สไตล์ไม่รั่วไปหน้าอื่น' );
check( 0 === $x->query( '//header[contains(@class,"site-header")]' )->length && 0 === $x->query( '//footer[contains(@class,"site-footer")]' )->length, 'ไม่มี header และ footer ของเว็บ' );
check( 1 === $x->query( '//h1' )->length, 'มี h1 เดียว' );

$steps = array();
foreach ( $x->query( '//div[contains(concat(" ", normalize-space(@class), " "), " fx-step ")]' ) as $step ) {
	$steps[] = $step->getAttribute( 'id' );
}
check( array( 'account', 'mt5', 'deposit', 'dl', 'install', 'vps' ) === $steps, 'ขั้นตอนเรียง 6 ขั้นตามแบบ: ' . implode( ' > ', $steps ) );
check( false !== strpos( $html, 'เริ่มใช้งานใน 6 ขั้นตอน' ), 'หัวข้อบอกจำนวนขั้นตอนถูกต้อง' );

/* ---------- การ์ดดาวน์โหลด EA ---------- */
check( 3 === $cards->length, 'การ์ด EA ครบ 3 ใบ' );
check( false !== strpos( card_src( $cards, 0 ), 'btc' ), 'ใบแรกคือ FENIX BTC (เรียงตามเลขเวอร์ชันจากน้อยไปมาก)' );
check( false !== strpos( card_src( $cards, 1 ), 'plus' ), 'ใบที่สองคือ FENIX PLUS' );
check( false !== strpos( card_src( $cards, 2 ), 'dual' ), 'ใบที่สามคือ FENIX DUAL' );

$links = array();
foreach ( $cards as $card ) {
	$links[] = $card->getAttribute( 'href' );
}
check(
	array(
		'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_BTC_V1.1.zip',
		'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_PLUS_V5.1.zip',
		'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_DUAL_V6.3.zip',
	) === $links,
	'การ์ดทุกใบชี้ไปไฟล์ ZIP ที่ตั้งไว้'
);

$downloads = 0;
foreach ( $cards as $card ) {
	if ( $card->hasAttribute( 'download' ) ) {
		$downloads++;
	}
}
check( 3 === $downloads, 'กดการ์ดแล้วดาวน์โหลดไฟล์ ไม่ใช่เปิดหน้าใหม่' );

check( 3 === $x->query( '//span[contains(@class,"ver")]' )->length, 'ป้ายเวอร์ชันครบทุกใบ' );
check( false !== strpos( $html, '>V1.1</b>' ) && false !== strpos( $html, '>V5.1</b>' ) && false !== strpos( $html, '>V6.3</b>' ), 'เลขเวอร์ชันบนป้ายอ่านมาจากชื่อไฟล์' );
check( 3 === substr_count( $html, 'อัปเดตล่าสุด' ), 'ป้ายเวอร์ชันมีคำนำหน้าภาษาไทยครบทุกใบ' );
check( 3 === $x->query( '//span[contains(@class,"dl")]' )->length, 'ปุ่มดาวน์โหลดบนรูปครบทุกใบ' );
check( false === strpos( $html, 'FENIX_PRO_V3.8.zip' ), 'ไฟล์ PRO ที่เลิกใช้แล้วไม่ถูกอ้างถึง' );

/* เวอร์ชันที่พิมพ์เองชนะชื่อไฟล์ */
list( $html2 ) = render_go( array( 'links_card3_version' => 'V6.1 BETA' ) );
check( false !== strpos( $html2, '>V6.1 BETA</b>' ), 'เวอร์ชันที่พิมพ์เองใน Customizer ชนะเลขจากชื่อไฟล์' );

/* ไฟล์ที่ไม่มีเลขเวอร์ชัน ไม่ต้องมีป้าย */
list( $html3, $x3 ) = render_go(
	array(
		'links_card3_url'  => 'https://example.test/fenix.zip',
		'links_plus_url'   => 'https://example.test/fenix.zip',
		'links_fast_url'   => 'https://example.test/fenix.zip',
	)
);
check( 0 === $x3->query( '//span[contains(@class,"ver")]' )->length, 'ไฟล์ที่ไม่มีเลขเวอร์ชัน ไม่ขึ้นป้าย' );

/* ปิดการ์ดใบไหน หน้าก็ลดตาม */
list( $html4, $x4, $cards4 ) = render_go( array( 'links_plus_enabled' => false ) );
check( 2 === $cards4->length && false === strpos( $html4, 'FENIX_PLUS_V5.1.zip' ), 'ปิดการ์ด PLUS แล้วทั้งการ์ดและลิงก์หายไป' );

/* ข้อความ alt แก้ได้และถูก escape */
list( $html5, $x5, $cards5 ) = render_go( array( 'links_plus_alt' => 'FENIX PLUS "Download" <test>' ) );
$alt = $cards5->item( 1 )->getElementsByTagName( 'img' )->item( 0 )->getAttribute( 'alt' );
check( 'FENIX PLUS "Download" <test>' === $alt && false !== strpos( $html5, 'PLUS &quot;Download&quot; &lt;test&gt;' ), 'คำอธิบายรูปแก้ได้และถูก escape' );

/* ---------- ลิงก์ตามตารางในสเปก ---------- */
$must = array(
	'https://portal.zaurix.com?ref=210028'                                         => 'ลิงก์เปิดบัญชีคงรหัสแนะนำ ref=210028',
	'https://line.me/R/ti/p/@fenixpro'                                             => 'ปุ่ม LINE ใช้ลิงก์เดิม',
	'https://line.me/ti/g2/6O9XyBBtatI9LF_Z7N1dpbw7aOgE0IAl39m3bw'                 => 'ปุ่ม OPENCHAT ใช้ลิงก์เดิม',
	'https://apps.apple.com/us/app/metatrader-5/id413251709'                       => 'ลิงก์แอป MT5 บน iPhone',
	'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5'     => 'ลิงก์แอป MT5 บน Android',
	'https://www.metatrader5.com/en/download'                                      => 'ลิงก์โหลด MT5 บน Windows',
	'https://portal.zaurix.com/'                                                   => 'ลิงก์เข้า PORTAL ZAURIX',
	'https://www.myfxbook.com/portfolio/eafn/12227282'                             => 'ลิงก์ผลเทรดจริงบน MYFXBOOK',
	'https://example.test/pricing/'                                                => 'ลิงก์หน้าแพ็กเกจ',
	'https://example.test/open-mt5-account/'                                       => 'ลิงก์สอนเปิดบัญชี',
	'https://example.test/mt5-login-zaurix-server/'                                => 'ลิงก์วิธี LOGIN เข้า ZAURIX-SERVER',
	'https://fenixpro-th.com/deposit-zaurix/'                                      => 'ลิงก์คู่มือฝากเงิน',
	'https://example.test/fenix-pro-ea-install-guide/'                             => 'ลิงก์คู่มือติดตั้ง EA',
	'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-dual-63-manual-th.pdf' => 'ลิงก์คู่มือ FENIX DUAL (PDF)',
	'https://example.test/vps-desktop-windows/'                                    => 'ลิงก์คู่มือ VPS บน Windows',
	'https://example.test/vps-android/'                                            => 'ลิงก์คู่มือ VPS บน Android',
	'https://example.test/vps-iphone-ios/'                                         => 'ลิงก์คู่มือ VPS บน iOS',
);
foreach ( $must as $url => $message ) {
	check( false !== strpos( $html, esc_attr( $url ) ), $message );
}

/* ---------- ปุ่มและป้าย ---------- */
check( 3 === $x->query( '//a[contains(@class,"fx-btn-or")]' )->length, 'แต่ละขั้นมีปุ่มหลักสีส้มอย่างมากหนึ่งปุ่ม (รวม 3 ปุ่มทั้งหน้า)' );
check( 0 === $x->query( '//a[contains(@class,"fx-btn-or") and contains(@style,"color")]' )->length, 'ปุ่มส้มไม่ถูกใส่สีตัวหนังสือทับในหน้า' );

$css = file_get_contents( get_template_directory() . '/assets/css/go.css' );
$at    = strpos( $css, '.fxgo .fx-btn-or {' );
$block = false !== $at ? substr( $css, $at, strpos( $css, '}', $at ) - $at ) : '';
check( false !== strpos( $block, 'color: #FFFFFF' ), 'ปุ่มหลักสีส้มใช้ตัวหนังสือสีขาว' );

$at    = strpos( $css, '.fxgo .fx-btn-dark {' );
$block = false !== $at ? substr( $css, $at, strpos( $css, '}', $at ) - $at ) : '';
check( false !== strpos( $block, 'background: transparent' ) && false !== strpos( $block, 'inset 0 0 0 1.5px #FFFFFF' ) && false !== strpos( $block, 'color: #FFFFFF' ), 'ปุ่มรองพื้นใส ขอบขาว 1.5px ตัวหนังสือขาว' );

check( false !== strpos( $html, 'แนะนำ' ), 'ขั้นที่ 6 มีป้าย "แนะนำ"' );
check( false !== strpos( $html, '>PDF<' ), 'ปุ่มคู่มือ PDF มีป้ายบอกว่าเป็นไฟล์ PDF' );

/* ---------- ช่องเลือกอุปกรณ์ ---------- */
$mt5_tiles = $x->query( '//div[@id="mt5"]//div[contains(@class,"dev")]/a' );
check( 3 === $mt5_tiles->length, 'ขั้นที่ 2 มีช่องอุปกรณ์ 3 ช่อง' );
$devices = array();
$blank   = true;
foreach ( $mt5_tiles as $tile ) {
	$devices[] = $tile->getAttribute( 'data-device' );
	if ( '_blank' !== $tile->getAttribute( 'target' ) ) {
		$blank = false;
	}
}
check( array( 'ios', 'android', 'win' ) === $devices && $blank, 'ช่องอุปกรณ์เรียง iPhone · Android · Windows และเปิดแท็บใหม่' );

$vps_tiles = $x->query( '//div[@id="vps"]//div[contains(@class,"dev")]/a' );
$vps_dev   = array();
foreach ( $vps_tiles as $tile ) {
	$vps_dev[] = $tile->getAttribute( 'data-device' );
}
check( array( 'win', 'android', 'ios' ) === $vps_dev, 'ช่อง VPS เรียง Windows · Android · iOS' );
check( false !== strpos( $html, '>Windows</span>' ) && false !== strpos( $html, '>iOS</span>' ), 'ช่อง VPS ใช้ชื่อสั้นตามแบบ' );
check( 6 === substr_count( $html, 'data-yours="อุปกรณ์ของคุณ"' ), 'ทุกช่องอุปกรณ์พร้อมรับป้าย "อุปกรณ์ของคุณ"' );
$plain = 0;
foreach ( $x->query( '//div[contains(@class,"dev")]/a/span[@class="keep-case"]' ) as $span ) {
	$plain++;
}
check( 6 === $plain, 'ชื่ออุปกรณ์ทั้ง 6 ช่องไม่ถูกบังคับเป็นตัวพิมพ์ใหญ่' );

/* ตรวจอุปกรณ์ต้องทำฝั่งผู้ใช้ เพราะหน้านี้ถูกแคช */
$js = file_get_contents( get_template_directory() . '/assets/js/go.js' );
check( false !== strpos( $js, 'navigator.userAgent' ), 'ตรวจอุปกรณ์จาก userAgent' );
check( false !== strpos( $js, 'iPhone' ) && false !== strpos( $js, 'Android' ) && false !== strpos( $js, 'Windows NT' ), 'ตรวจครบทั้ง iPhone, Android และ Windows' );
check( false === strpos( $html, 'class="me"' ) && false === strpos( $html, ' me"' ), 'ฝั่งเซิร์ฟเวอร์ไม่ไฮไลต์อุปกรณ์ไว้ล่วงหน้า (หน้าถูกแคช)' );

/* เติมลิงก์ macOS แล้วได้ช่องที่สี่ */
list( $html6, $x6 ) = render_go( array( 'links_mt5_macos_url' => 'https://example.test/mt5-mac' ) );
check( 4 === $x6->query( '//div[@id="mt5"]//div[contains(@class,"dev")]/a' )->length, 'เติมลิงก์ macOS แล้วมีช่องที่สี่' );

/* ---------- บรรทัดบอกปลายทาง ---------- */
check( 2 === substr_count( $html, 'class="dest"' ), 'ปุ่มที่ออกไปเว็บอื่นมีบรรทัดบอกปลายทาง 2 จุด' );
check( 2 === substr_count( $html, '>portal.zaurix.com<' ), 'บรรทัดปลายทางดึงชื่อเว็บจากลิงก์เอง' );

/* ---------- ทางลัด ---------- */
$short = $x->query( '//div[contains(@class,"short")]/a' );
check( 4 === $short->length, 'ปุ่มทางลัดมี 4 ปุ่ม' );
$targets = array();
foreach ( $short as $link ) {
	$targets[] = $link->getAttribute( 'href' );
}
check( array( '#dl', '#install', '#vps' ) === array_slice( $targets, 0, 3 ), 'ทางลัดสามปุ่มแรกเลื่อนลงไปยังขั้นในหน้านี้' );
check( false !== strpos( $targets[3], 'myfxbook' ), 'ทางลัดผลเทรดจริงลิงก์ออกไป MYFXBOOK' );

/* ---------- ส่วนที่เพิ่มใหม่ ---------- */
check( 1 === $x->query( '//div[contains(@class,"risk")]' )->length, 'มีกล่องคำเตือนความเสี่ยง' );
check( 3 === $x->query( '//div[contains(@class,"facts")]/span' )->length, 'ชิปข้อเท็จจริงใต้ชื่อครบ 3 อัน' );
check( 4 === $x->query( '//div[contains(@class,"soc")]/a' )->length, 'ไอคอนโซเชียลครบ 4 อัน' );
check( 1 === $x->query( '//div[contains(@class,"algo")]' )->length, 'ขั้นที่ 5 มีภาพจำลองปุ่ม ALGO TRADING' );
check( 2 === $x->query( '//div[contains(@class,"ea-note")]/span' )->length, 'บรรทัดเงื่อนไขใต้การ์ด EA ครบ 2 ข้อ' );
check( 2 === $x->query( '//div[contains(@class,"vps-why")]/span' )->length, 'บรรทัดประโยชน์ของ VPS ครบ 2 ข้อ' );

/* ---------- ไม่มีระบบติ๊กหรือจำความคืบหน้า (ตามขอบเขตงาน) ---------- */
foreach ( array( 'localStorage', 'sessionStorage', 'checkbox', 'aria-pressed' ) as $banned ) {
	check( false === strpos( $html, $banned ) && false === strpos( $js, $banned ), "ไม่มีระบบติ๊กหรือบันทึกความคืบหน้า ($banned)" );
}

/* ---------- Facebook Pixel และปลั๊กอินอื่นต้องยังทำงาน ---------- */
$tpl = file_get_contents( get_template_directory() . '/template-links.php' );
foreach ( array( 'wp_head()', 'wp_body_open()', 'wp_footer()' ) as $hook ) {
	check( false !== strpos( $tpl, $hook ), "เรียก $hook ครบ (Facebook Pixel ยังทำงาน)" );
}

/* ---------- รูปทุกใบต้องมี alt ---------- */
$no_alt = 0;
foreach ( $x->query( '//img' ) as $img ) {
	if ( ! $img->hasAttribute( 'alt' ) ) {
		$no_alt++;
	}
}
check( 0 === $no_alt, 'รูปทุกใบมี alt' );

echo "\nครบทุกข้อ\n";
