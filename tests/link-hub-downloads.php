<?php
/** Run with: php tests/link-hub-downloads.php */
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

function render_downloads( $mods ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/template-links.php';
	$html = ob_get_clean();
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( $html );
	libxml_clear_errors();
	$xpath = new DOMXPath( $dom );
	$cards = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " lh-feature ")]' );
	return array( $html, $cards );
}

$legacy = array(
	'links_feature_img' => 'https://example.test/old-pro.png',
	'links_feature_url' => 'https://example.test/old-pro.zip',
	'links_feature2_img' => 'https://example.test/old-mt.png',
	'links_feature2_url' => 'https://example.test/old-mt.zip',
);

list( $html, $cards ) = render_downloads( $legacy );
check( 1 === $cards->length, 'FAST replaces both legacy cards even with saved Customizer values' );
$link = $cards->item( 0 )->getElementsByTagName( 'a' )->item( 0 );
$image = $cards->item( 0 )->getElementsByTagName( 'img' )->item( 0 );
check( 'https://example.test/wp-content/themes/fenix-pro/assets/downloads/FENIX_Fast_V4.0.zip' === $link->getAttribute( 'href' ), 'FAST links to the supplied ZIP' );
check( $link->hasAttribute( 'download' ), 'FAST link requests a file download' );
check( 'https://example.test/wp-content/themes/fenix-pro/assets/img/link-download-fenix-fast.webp' === $image->getAttribute( 'src' ), 'FAST uses the approved banner' );
check( false === strpos( $html, 'old-pro.' ) && false === strpos( $html, 'old-mt.' ), 'Legacy assets are not emitted while FAST is enabled' );

list( $html, $cards ) = render_downloads( array_merge( $legacy, array( 'links_fast_enabled' => false ) ) );
check( 2 === $cards->length, 'Disabling FAST restores the two legacy cards' );
check( false !== strpos( $html, 'old-pro.zip' ) && false !== strpos( $html, 'old-mt.zip' ), 'Restored cards retain their saved links' );

list( $html, $cards ) = render_downloads( array(
	'links_fast_img' => 'https://example.test/custom.webp',
	'links_fast_url' => 'https://example.test/package.zip?v=2&source=go',
	'links_fast_alt' => 'FENIX FAST "Download" <test>',
) );
$link = $cards->item( 0 )->getElementsByTagName( 'a' )->item( 0 );
$image = $cards->item( 0 )->getElementsByTagName( 'img' )->item( 0 );
check( 'https://example.test/package.zip?v=2&source=go' === $link->getAttribute( 'href' ), 'Customizer download URLs survive escaping' );
check( 'https://example.test/custom.webp' === $image->getAttribute( 'src' ), 'Customizer banner overrides the default' );
check( 'FENIX FAST "Download" <test>' === $image->getAttribute( 'alt' ) && false !== strpos( $html, '&lt;test&gt;' ), 'Image alt text is configurable and escaped' );
