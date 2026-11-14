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
$top = $xpath->query( '//ul[@class="nav-list"]/li' );
check( 6 === $top->length, 'เมนูหลักเหลือ 6 หัวข้อ' );

$labels = array();
foreach ( $top as $li ) {
	$labels[] = trim( $li->getElementsByTagName( 'a' )->item( 0 )->textContent );
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
