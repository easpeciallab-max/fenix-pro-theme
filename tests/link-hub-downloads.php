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

function card_with_class( $cards, $class ) {
	foreach ( $cards as $card ) {
		if ( false !== strpos( ' ' . $card->getAttribute( 'class' ) . ' ', ' ' . $class . ' ' ) ) {
			return $card;
		}
	}
	return null;
}

$legacy = array(
	'links_feature_img' => 'https://example.test/old-pro.png',
	'links_feature_url' => 'https://example.test/old-pro.zip',
	'links_feature2_img' => 'https://example.test/old-mt.png',
	'links_feature2_url' => 'https://example.test/old-mt.zip',
);

list( $html, $cards ) = render_downloads( $legacy );
check( 2 === $cards->length && null !== card_with_class( $cards, 'lh-feature-fast' ) && null !== card_with_class( $cards, 'lh-feature-plus' ), 'FAST replaces both legacy cards even with saved Customizer values; PLUS follows' );
check( false !== strpos( $cards->item( 0 )->getAttribute( 'class' ), 'lh-feature-fast' ) && false !== strpos( $cards->item( 1 )->getAttribute( 'class' ), 'lh-feature-plus' ), 'PLUS card renders directly after FAST' );
$plus       = card_with_class( $cards, 'lh-feature-plus' );
$plus_link  = $plus->getElementsByTagName( 'a' )->item( 0 );
$plus_image = $plus->getElementsByTagName( 'img' )->item( 0 );
check( 'https://example.test/wp-content/themes/fenix-pro/assets/downloads/FENIX_PLUS.zip' === $plus_link->getAttribute( 'href' ), 'PLUS links to the supplied ZIP' );
check( $plus_link->hasAttribute( 'download' ), 'PLUS link requests a file download' );
check( 'https://example.test/wp-content/themes/fenix-pro/assets/img/link-download-fenix-plus.webp' === $plus_image->getAttribute( 'src' ), 'PLUS uses the approved banner' );
check( is_file( get_template_directory() . '/assets/downloads/FENIX_PLUS.zip' ) && is_file( get_template_directory() . '/assets/img/link-download-fenix-plus.webp' ), 'PLUS default assets exist in the theme' );
$fast  = card_with_class( $cards, 'lh-feature-fast' );
$link  = $fast->getElementsByTagName( 'a' )->item( 0 );
$image = $fast->getElementsByTagName( 'img' )->item( 0 );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/09/FENIX_PRO_V3.2.zip' === $link->getAttribute( 'href' ), 'FAST links to the supplied ZIP' );
check( $link->hasAttribute( 'download' ), 'FAST link requests a file download' );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/09/link-download-fenix-pro-v32.webp' === $image->getAttribute( 'src' ), 'FAST uses the approved banner' );
check( false === strpos( $html, 'old-pro.' ) && false === strpos( $html, 'old-mt.' ), 'Legacy assets are not emitted while FAST is enabled' );

list( $html, $cards ) = render_downloads( array_merge( $legacy, array( 'links_fast_enabled' => false ) ) );
check( 3 === $cards->length && null === card_with_class( $cards, 'lh-feature-fast' ), 'Disabling FAST restores the two legacy cards (PLUS stays independent)' );
check( false !== strpos( $html, 'old-pro.zip' ) && false !== strpos( $html, 'old-mt.zip' ), 'Restored cards retain their saved links' );

list( $html, $cards ) = render_downloads( array_merge( $legacy, array( 'links_fast_show' => false ) ) );
check( 1 === $cards->length && null !== card_with_class( $cards, 'lh-feature-plus' ) && false === strpos( $html, 'FENIX_PRO_V3.2.zip' ) && false === strpos( $html, 'old-pro.' ), 'Hiding FAST shows only PLUS and does not bring back legacy cards' );

list( $html, $cards ) = render_downloads( array( 'links_plus_enabled' => false ) );
check( null === card_with_class( $cards, 'lh-feature-plus' ) && false === strpos( $html, 'FENIX_PLUS.zip' ), 'Disabling PLUS hides its card and ZIP link' );

list( $html, $cards ) = render_downloads( array( 'links_plus_alt' => 'FENIX PLUS "Download" <test>' ) );
$plus_image = card_with_class( $cards, 'lh-feature-plus' )->getElementsByTagName( 'img' )->item( 0 );
check( 'FENIX PLUS "Download" <test>' === $plus_image->getAttribute( 'alt' ) && false !== strpos( $html, 'PLUS &quot;Download&quot; &lt;test&gt;' ), 'PLUS alt text is configurable and escaped' );

list( $html, $cards ) = render_downloads( array(
	'links_fast_img' => 'https://example.test/custom.webp',
	'links_fast_url' => 'https://example.test/package.zip?v=2&source=go',
	'links_fast_alt' => 'FENIX FAST "Download" <test>',
) );
$link  = card_with_class( $cards, 'lh-feature-fast' )->getElementsByTagName( 'a' )->item( 0 );
$image = card_with_class( $cards, 'lh-feature-fast' )->getElementsByTagName( 'img' )->item( 0 );
check( 'https://example.test/package.zip?v=2&source=go' === $link->getAttribute( 'href' ), 'Customizer download URLs survive escaping' );
check( 'https://example.test/custom.webp' === $image->getAttribute( 'src' ), 'Customizer banner overrides the default' );
check( 'FENIX FAST "Download" <test>' === $image->getAttribute( 'alt' ) && false !== strpos( $html, '&lt;test&gt;' ), 'Image alt text is configurable and escaped' );

// ---- Numbered start-up journey on /go/ ----
function render_page( $mods ) {
	global $theme_mods;
	$theme_mods = $mods;
	ob_start();
	require get_template_directory() . '/template-links.php';
	$html = ob_get_clean();
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="UTF-8">' . $html );
	libxml_clear_errors();
	return array( $html, new DOMXPath( $dom ) );
}

function step_keys( $xpath ) {
	$keys = array();
	foreach ( $xpath->query( '//ol[@class="lh-steps"]/li' ) as $li ) {
		preg_match( '/lh-step--([a-z0-9]+)/', $li->getAttribute( 'class' ), $m );
		$keys[] = $m[1] . ':' . trim( $xpath->query( './/span[@class="lh-step-num"]', $li )->item( 0 )->textContent );
	}
	return $keys;
}

list( $html, $xpath ) = render_page( array() );
check( array( 'account:1', 'mt5:2', 'deposit:3', 'download:4', 'install:5', 'vps:6' ) === step_keys( $xpath ), 'Journey shows 6 numbered steps in order: account, MT5, deposit, download, install EA, VPS' );
check( false !== strpos( $html, 'เริ่มใช้งานใน 6 ขั้นตอน' ), 'Steps heading shows the live step count' );
$mt5_items = $xpath->query( '//li[contains(@class, "lh-step--mt5")]//div[contains(@class, "lh-mt5-grid")]/a' );
$hrefs = array();
$blank = true;
foreach ( $mt5_items as $a ) {
	$hrefs[] = $a->getAttribute( 'href' );
	$blank   = $blank && '_blank' === $a->getAttribute( 'target' ) && false !== strpos( $a->getAttribute( 'rel' ), 'noopener' );
}
check( array( 'https://apps.apple.com/us/app/metatrader-5/id413251709', 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5', 'https://download.mql5.com/cdn/web/metaquotes.software.corp/mt5/mt5setup.exe' ) === $hrefs && $blank, 'MT5 step has iPhone, Android, Windows tiles (new tab), macOS hidden' );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--mt5")]//a[@href="https://example.test/mt5-login-zaurix-server/"]' )->length && false !== strpos( $html, 'วิธี Login เข้า Zaurix-Server' ), 'MT5 step links the Zaurix login guide with the new label' );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--account")]//a[contains(@class, "lh-btn-signup")]' )->length && 1 === $xpath->query( '//li[contains(@class, "lh-step--account")]//a[@href="https://example.test/open-mt5-account/"]' )->length, 'Account step holds the signup button and the account guide' );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--deposit")]//a[@href="https://portal.zaurix.com/" and @target="_blank"]' )->length && false === strpos( $html, 'คู่มือฝากเงิน' ), 'Deposit step links the Zaurix portal; empty deposit guide stays hidden' );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--download")]//div[contains(@class, "lh-feature-plus")]' )->length, 'Download step contains the FENIX PLUS card' );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--install")]//a[@href="https://example.test/fenix-pro-ea-install-guide/"]' )->length && 3 === $xpath->query( '//li[contains(@class, "lh-step--vps")]//a[contains(@class, "lh-vps-item")]' )->length, 'Install step has the EA guide; VPS step has 3 guides' );
check( false !== strpos( $html, 'lh-step-badge">แนะนำ' ), 'VPS step shows its recommended badge' );
check( 0 === $xpath->query( '//a[contains(@class, "lh-top-line")]' )->length, 'Short LINE link is hidden by default (help group already sits on top)' );
check( strpos( $html, 'lh-group--help' ) < strpos( $html, 'lh-journey' ) && strpos( $html, 'lh-journey' ) < strpos( $html, 'lh-group--info' ) && 1 === $xpath->query( '//section[contains(@class, "lh-group--help")]//a[contains(@class, "lh-btn-line")]' )->length && 1 === $xpath->query( '//section[contains(@class, "lh-group--help")]//a[contains(@class, "lh-btn-openchat")]' )->length, 'Help group (LINE, OpenChat) sits above the steps; info group comes after' );
check( 1 === substr_count( $html, 'lh-btn lh-btn-line' ), 'LINE button renders only once' );

list( $html, $xpath ) = render_page( array( 'links_top_line_label' => 'Need help?' ) );
check( 1 === $xpath->query( '//a[contains(@class, "lh-top-line")]' )->length, 'Short LINE link appears when its label is filled' );
check( 1 === $xpath->query( '//section[contains(@class, "lh-group--info")]//a[contains(@href, "myfxbook")]' )->length && 1 === $xpath->query( '//section[contains(@class, "lh-group--info")]//a[@href="https://example.test/pricing/"]' )->length, 'Info group holds live results and pricing' );

list( $html, $xpath ) = render_page( array( 'links_deposit_url' => '' ) );
check( array( 'account:1', 'mt5:2', 'download:3', 'install:4', 'vps:5' ) === step_keys( $xpath ) && false !== strpos( $html, 'เริ่มใช้งานใน 5 ขั้นตอน' ), 'Empty deposit step is hidden and the remaining steps renumber' );

list( $html, $xpath ) = render_page( array( 'links_deposit_guide_url' => '/zaurix-deposit/' ) );
check( 1 === $xpath->query( '//li[contains(@class, "lh-step--deposit")]//a[@href="https://example.test/zaurix-deposit/"]' )->length, 'Filling the deposit guide link shows its button' );

list( $html, $xpath ) = render_page( array( 'links_mt5_macos_url' => 'https://example.test/mt5-mac.pkg', 'links_mt5_install_title' => 'ขั้นตอนแรก · ติดตั้งแอป MT5', 'links_mt5_download_label' => 'คู่มือติดตั้ง MT5' ) );
check( 4 === $xpath->query( '//div[contains(@class, "lh-mt5-grid--4")]/a' )->length, 'Filling the macOS link adds a fourth MT5 tile' );
check( false !== strpos( $html, 'ติดตั้งแอป MT5 และ Login' ) && false === strpos( $html, 'ขั้นตอนแรก · ติดตั้งแอป MT5' ) && false !== strpos( $html, 'วิธี Login เข้า Zaurix-Server' ), 'Old saved MT5 title and guide label migrate to the new wording' );

list( $html, $xpath ) = render_page( array( 'links_mt5_ios_url' => '', 'links_mt5_android_url' => '#', 'links_mt5_windows_url' => '', 'links_step1_title' => 'Open <test>' ) );
check( 0 === $xpath->query( '//div[contains(@class, "lh-mt5-grid")]' )->length && in_array( 'mt5:2', step_keys( $xpath ), true ), 'Without app links the MT5 step keeps its login guide button' );
check( false !== strpos( $html, 'Open &lt;test&gt;' ), 'Step titles are escaped' );
