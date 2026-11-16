<?php
/** Run with: php tests/primary-menu.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; the menu itself is the real one.
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
function wp_unslash( $value ) { return is_string( $value ) ? stripslashes( $value ) : $value; }

require get_template_directory() . '/functions.php';

function check( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: $message\n" );
		exit( 1 );
	}
	echo "PASS: $message\n";
}

ob_start();
fenix_fallback_menu();
$html = ob_get_clean();

$dom = new DOMDocument();
libxml_use_internal_errors( true );
$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
libxml_clear_errors();
$xpath = new DOMXPath( $dom );

// ---- โครงเมนู ----
$top = $xpath->query( '//ul[@class="nav-list"]/li[a]' );
check( 6 === $top->length, 'เมนูหลักเหลือ 6 หัวข้อ' );

$labels = array();
foreach ( $top as $li ) {
	$first    = $li->getElementsByTagName( 'a' )->item( 0 );
	$labels[] = trim( $xpath->query( './/span[@class="nav-label"]', $first )->item( 0 )->textContent );
}
check( array( 'หน้าแรก', 'ดาวน์โหลด EA', 'ผลทดสอบ', 'วิธีใช้งาน', 'แพ็กเกจ', 'บทความ' ) === $labels, 'หัวข้อเรียงตามลำดับที่ตกลงไว้' );

// ---- หัวข้อที่มีเมนูย่อย ----
$parents = $xpath->query( '//li[contains(@class, "menu-item-has-children")]' );
check( 2 === $parents->length, 'มีเมนูย่อยสองชุด: ผลทดสอบ และ วิธีใช้งาน' );
check( 2 === $xpath->query( '//li[contains(@class, "menu-item-has-children")][1]/ul[@class="sub-menu"]/li' )->length, 'ผลทดสอบ มีสองหน้า' );
check( 8 === $xpath->query( '//li[contains(@class, "menu-item-has-children")][2]/ul[@class="sub-menu"]/li' )->length, 'วิธีใช้งาน มีแปดหน้า' );

// ---- หัวข้อแม่ต้องไม่พาไปหน้าที่ยังว่าง ----
$results_parent = $parents->item( 0 )->getElementsByTagName( 'a' )->item( 0 );
check( 'https://example.test/backtest/' === $results_parent->getAttribute( 'href' ), 'กดหัวข้อผลทดสอบแล้วไปหน้า Backtest ไม่ใช่หน้า Forward Test ที่ยังว่าง' );

// ---- หน้าที่เคยหลุดจากเมนู ----
foreach ( array( '/deposit-zaurix/', '/fenix-dual-presets/', '/go/' ) as $path ) {
	check( 1 === $xpath->query( '//a[@href="https://example.test' . $path . '"]' )->length, "หน้า $path เข้าถึงได้จากเมนูแล้ว" );
}

// ---- ทุกลิงก์ต้องเป็นของเราเองและไม่ซ้ำกันเกินจำเป็น ----
$urls = array();
foreach ( $xpath->query( '//a' ) as $a ) {
	$urls[] = $a->getAttribute( 'href' );
}
check( count( $urls ) === count( array_filter( $urls, function ( $u ) { return 0 === strpos( $u, 'https://example.test/' ); } ) ), 'ทุกลิงก์ในเมนูชี้ภายในเว็บ' );
check( 2 === count( array_keys( $urls, 'https://example.test/backtest/' ) ), 'หน้า Backtest ปรากฏสองครั้งพอดี (หัวข้อแม่กับเมนูย่อย)' );

// ---- ไม่เหลือป้ายเก่าที่ชวนสับสน ----
check( false === strpos( $html, 'ติดต่อ' ), 'ป้าย "ติดต่อ" ที่เคยชี้ไปหน้าดาวน์โหลดถูกเปลี่ยนชื่อแล้ว' );
check( false === strpos( $html, 'คู่มือการใช้งาน' ), 'ป้ายเมนูยาว ๆ ถูกย่อแล้ว' );

// ---- ลิงก์ในเมนูต้องเป็นหน้าที่มีอยู่จริงในธีม ----
$known = array( '/', '/go/', '/backtest/', '/forward-test/', '/fenix-pro-ea-install-guide/', '/open-mt5-account/', '/mt5-login-zaurix-server/', '/deposit-zaurix/', '/fenix-dual-presets/', '/vps-desktop-windows/', '/vps-android/', '/vps-iphone-ios/', '/pricing/', '/articles/' );
$bad = array();
foreach ( fenix_menu_tree() as $item ) {
	$all = array( $item );
	if ( ! empty( $item['children'] ) ) { $all = array_merge( $all, $item['children'] ); }
	foreach ( $all as $one ) {
		if ( ! in_array( $one['url'], $known, true ) ) { $bad[] = $one['url']; }
	}
}
check( array() === $bad, 'ทุกปลายทางในเมนูเป็น slug ที่เว็บมีจริง' );

// ---- เมนูมือถือแบบกริด: หัวข้อหมวด ไอคอน และชื่อสั้น ----
$group = $xpath->query( '//li[@class="nav-group-label"]' );
check( 1 === $group->length, 'มีหัวข้อหมวดของช่องเมนูหลัก' );
check( 'เมนูหลัก' === trim( $group->item( 0 )->textContent ), 'หัวข้อหมวดแรกชื่อ "เมนูหลัก"' );
check( 0 === $group->item( 0 )->getElementsByTagName( 'a' )->length, 'หัวข้อหมวดเป็นแค่ข้อความ ไม่ใช่ลิงก์' );

// ช่องที่กดได้ = ลิงก์ที่ไม่ใช่หัวข้อที่มีเมนูย่อย
$tiles = $xpath->query( '//a[span[@class="nav-ic"]]' );
check( 14 === $tiles->length, 'ช่องในกริดครบ 14 ช่อง (หน้าเว็บทั้งหมดที่อยู่ในเมนู)' );

$sections = $xpath->query( '//li[contains(@class, "menu-item-has-children")]/a' );
foreach ( $sections as $a ) {
	check( 0 === $xpath->query( './/span[@class="nav-ic"]', $a )->length, 'หัวข้อ "' . trim( $a->textContent ) . '" เป็นชื่อหมวด ไม่มีไอคอน' );
}

$too_long = array();
foreach ( $tiles as $a ) {
	$icon  = $xpath->query( './/span[@class="nav-ic"]/*', $a );
	$short = $xpath->query( './/span[@class="nav-label-short"]', $a );
	check( 1 === $icon->length && 'svg' === $icon->item( 0 )->nodeName, 'ช่อง "' . trim( $short->item( 0 )->textContent ) . '" มีไอคอนจริง' );
	$text = trim( $short->item( 0 )->textContent );
	if ( preg_match_all( '/./u', $text ) > 12 ) {
		$too_long[] = $text;
	}
}
check( array() === $too_long, 'ชื่อสั้นทุกช่องสั้นพอจะอยู่ในกริด 4 ช่องโดยไม่ล้น' );

// ชื่อเต็มยังอยู่ให้โปรแกรมอ่านหน้าจอใช้
$deposit = $xpath->query( '//a[@href="https://example.test/deposit-zaurix/"]' )->item( 0 );
check( 'ฝากเงิน' === trim( $xpath->query( './/span[@class="nav-label-short"]', $deposit )->item( 0 )->textContent ), 'ช่องฝากเงินใช้ชื่อสั้น' );
check( 'ฝากเงินเข้าบัญชี' === $deposit->getAttribute( 'aria-label' ), 'ชื่อเต็มยังติดอยู่ที่ลิงก์สำหรับโปรแกรมอ่านหน้าจอ' );

// ---- ไฮไลต์หน้าที่กำลังเปิดอยู่ ----
$_SERVER['REQUEST_URI'] = '/go/?utm_source=line';
ob_start();
fenix_fallback_menu();
$on_go = ob_get_clean();

$dom2 = new DOMDocument();
libxml_use_internal_errors( true );
$dom2->loadHTML( '<?xml encoding="UTF-8">' . $on_go );
libxml_clear_errors();
$xpath2  = new DOMXPath( $dom2 );
$current = $xpath2->query( '//a[@aria-current="page"]' );
check( 1 === $current->length, 'มีช่องเดียวที่ถูกทำเครื่องหมายว่าเป็นหน้าปัจจุบัน' );
check( 'https://example.test/go/' === $current->item( 0 )->getAttribute( 'href' ), 'ช่องที่ไฮไลต์คือหน้าที่เปิดอยู่จริง แม้ URL จะมีพารามิเตอร์ต่อท้าย' );
