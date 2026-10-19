<?php
/** Run with: php tests/dashboard-buttons.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; the theme code is the real one.
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

// ---- ปุ่มบนแถบเมนู ----
check( 'https://dashboard.fenixpro-th.com/' === fenix_mod( 'dashboard_url' ), 'Dashboard link ships as a default' );
check( true === fenix_mod( 'show_header_auth' ), 'The header button is on out of the box' );
check( 'แดชบอร์ด' === fenix_mod( 'header_dash_label' ), 'The button label is an editable setting, not hardcoded copy' );

$header = file_get_contents( get_template_directory() . '/header.php' );
check( 1 === substr_count( $header, 'header-auth-dash' ), 'The header renders exactly one dashboard button' );
check( false === strpos( $header, 'header_signup_label' ) && false === strpos( $header, 'header_login_label' ), 'The retired sign-up and log-in buttons are gone' );
check( false === strpos( $header, 'https://dashboard.fenixpro-th.com' ), 'The header template keeps the URL in a setting' );

// ---- เมนูลัดมือถือด้านล่าง ----
$items = fenix_mobile_nav_items();
check( 5 === count( $items ), 'The bottom bar still holds five buttons' );
$labels = array_column( $items, 'label' );
check( array( 'หน้าแรก', 'ผลทดสอบ', 'แพ็กเกจ', 'แดชบอร์ด', 'ทัก LINE' ) === $labels, 'Dashboard took the slot the install guide used to hold' );
check( 'gauge' === $items[3]['icon'], 'The dashboard button carries its own icon' );
check( 'https://dashboard.fenixpro-th.com/' === $items[3]['url'], 'Without its own link the button follows the dashboard setting' );

$theme_mods = array( 'mobile_nav_dash_url' => '/members/' );
$items = fenix_mobile_nav_items();
check( 'https://example.test/members/' === $items[3]['url'], 'A link typed for the button wins and slugs still expand' );

$theme_mods = array( 'dashboard_url' => 'https://dash.example.test/?a=1&b=2' );
$items = fenix_mobile_nav_items();
check( 'https://dash.example.test/?a=1&b=2' === $items[3]['url'], 'Query strings in the dashboard link survive' );

$theme_mods = array( 'mobile_nav_dash_label' => 'ห้องสมาชิก' );
$items = fenix_mobile_nav_items();
check( 'ห้องสมาชิก' === $items[3]['label'], 'The button label is editable' );
