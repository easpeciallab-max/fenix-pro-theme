<?php
/** Run with: php tests/dashboard-line-button.php */
error_reporting( E_ALL );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

define( 'ABSPATH', dirname( __DIR__ ) . '/' );
$theme_mods = array();

// Only WordPress infrastructure is stubbed; the filter itself is the real one.
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

// มาร์กอัปจริงที่ปลั๊กอินพ่นออกมาในหน้า /backtest/
$plugin_output = '<div class="fenixp-cta"><a class="fenixp-btn fenixp-btn-alt fenixp-disabled">Download Report</a>'
	. '<a class="fenixp-btn" href="https://www.myfxbook.com/members/speccub/fenix-smart-core/12163940/dTX1kOz5MZwCABISlzuH?utm_source=chatgpt.com">สอบถามทาง LINE</a></div>';

$fixed = fenix_fix_dashboard_line_button( $plugin_output, 'fenix_dashboard_pro' );
check( false !== strpos( $fixed, '<a class="fenixp-btn" href="https://line.me/R/ti/p/@fenixpro">สอบถามทาง LINE</a>' ), 'LINE button points at the LINE link from the Customizer' );
check( false === strpos( $fixed, 'myfxbook' ), 'The stale Myfxbook URL is gone from the button' );
check( false !== strpos( $fixed, 'fenixp-btn-alt fenixp-disabled">Download Report' ), 'The rest of the dashboard markup is untouched' );

$theme_mods = array( 'line_url' => 'https://line.me/R/ti/p/@example' );
$fixed = fenix_fix_dashboard_line_button( $plugin_output, 'fenix_dashboard_pro' );
check( false !== strpos( $fixed, 'href="https://line.me/R/ti/p/@example"' ), 'A LINE link saved in the Customizer wins' );

$theme_mods = array( 'line_url' => '' );
$fixed = fenix_fix_dashboard_line_button( $plugin_output, 'fenix_dashboard_pro' );
check( false !== strpos( $fixed, 'href="https://line.me/R/ti/p/@fenixpro"' ), 'An emptied LINE field falls back to the theme default, never to the old URL' );

$theme_mods = array();
check( $plugin_output === fenix_fix_dashboard_line_button( $plugin_output, 'gallery' ), 'Other shortcodes are not touched' );

$other = '<a class="fenixp-btn" href="https://www.myfxbook.com/portfolio/eafn/12227282">ดูผลเทรดจริง</a>';
check( $other === fenix_fix_dashboard_line_button( $other, 'fenix_dashboard_pro' ), 'Buttons that really are results links keep their URL' );
