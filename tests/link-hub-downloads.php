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
check( 3 === $cards->length, 'BTC, PLUS and DUAL all ship by default' );
check( false !== strpos( $cards->item( 0 )->getAttribute( 'class' ), 'lh-feature-card3' )
	&& false !== strpos( $cards->item( 1 )->getAttribute( 'class' ), 'lh-feature-plus' )
	&& false !== strpos( $cards->item( 2 )->getAttribute( 'class' ), 'lh-feature-fast' ), 'Cards run in version order: BTC 1.1, PLUS 5.1, DUAL 6.3' );
$btc       = card_with_class( $cards, 'lh-feature-card3' );
$btc_link  = $btc->getElementsByTagName( 'a' )->item( 0 );
$btc_image = $btc->getElementsByTagName( 'img' )->item( 0 );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_BTC_V1.1.zip' === $btc_link->getAttribute( 'href' ), 'BTC links to the supplied ZIP' );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-btc-v3.webp' === $btc_image->getAttribute( 'src' ), 'BTC uses the approved banner' );
check( 1 === preg_match( '~lh-feature-card3.*?lh-ver-num">V1\.1</span>~s', $html ), 'BTC carries its own version badge' );
$dual       = card_with_class( $cards, 'lh-feature-fast' );
$dual_link  = $dual->getElementsByTagName( 'a' )->item( 0 );
$dual_image = $dual->getElementsByTagName( 'img' )->item( 0 );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/10/FENIX_DUAL_V6.3.zip' === $dual_link->getAttribute( 'href' ), 'DUAL links to the supplied ZIP' );
check( $dual_link->hasAttribute( 'download' ), 'DUAL link requests a file download' );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-dual-v3.webp' === $dual_image->getAttribute( 'src' ), 'DUAL uses the approved banner' );
check( 1 === preg_match( '~lh-feature-fast.*?lh-ver-num">V6\.3</span>~s', $html ), 'DUAL carries its own version badge' );
$plus       = card_with_class( $cards, 'lh-feature-plus' );
$plus_link  = $plus->getElementsByTagName( 'a' )->item( 0 );
$plus_image = $plus->getElementsByTagName( 'img' )->item( 0 );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_PLUS_V5.1.zip' === $plus_link->getAttribute( 'href' ), 'PLUS links to the supplied ZIP' );
check( $plus_link->hasAttribute( 'download' ), 'PLUS link requests a file download' );
check( 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-plus-v3.webp' === $plus_image->getAttribute( 'src' ), 'PLUS uses the approved banner' );
check( false !== strpos( $html, '<span class="lh-ver-num">V5.1</span>' ), 'Version badge reads the number from the download file name' );
check( false !== strpos( $html, 'เวอร์ชันล่าสุด' ), 'Version badge keeps its Thai prefix' );
check( false === strpos( $html, 'FENIX_PRO_V3.8.zip' ) && false === strpos( $html, 'link-download-fenix-pro-v32.webp' ), 'The retired PRO download is not referenced anywhere' );
check( false === strpos( $html, 'old-pro.' ) && false === strpos( $html, 'old-mt.' ), 'Legacy assets are not emitted while FAST is enabled' );

list( $html, $cards ) = render_downloads( array_merge( $legacy, array( 'links_fast_enabled' => false ) ) );
check( 4 === $cards->length && null === card_with_class( $cards, 'lh-feature-fast' ), 'Disabling the DUAL slot restores the two legacy cards (the others stay independent)' );
check( false !== strpos( $html, 'old-pro.zip' ) && false !== strpos( $html, 'old-mt.zip' ), 'Restored cards retain their saved links' );

list( $html, $cards ) = render_downloads( array_merge( $legacy, array( 'links_fast_show' => false ) ) );
check( 2 === $cards->length && null === card_with_class( $cards, 'lh-feature-fast' ) && null !== card_with_class( $cards, 'lh-feature-plus' ) && false === strpos( $html, 'old-pro.' ), 'Hiding the DUAL card leaves the rest and does not bring back legacy cards' );

list( $html, $cards ) = render_downloads( array( 'links_plus_enabled' => false ) );
check( null === card_with_class( $cards, 'lh-feature-plus' ) && false === strpos( $html, 'FENIX_PLUS_V5.1.zip' ) && 2 === $cards->length, 'Disabling PLUS hides its card and ZIP link' );

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

list( $html, $cards ) = render_downloads( array( 'links_plus_version' => '6.1 BETA' ) );
check( false !== strpos( $html, '<span class="lh-ver-num">V6.1 BETA</span>' ), 'A version typed in the Customizer wins over the file name' );

list( $html, $cards ) = render_downloads( array( 'links_plus_version' => 'รุ่นทดสอบ <b>', 'links_plus_version_label' => '' ) );
check( false !== strpos( $html, 'รุ่นทดสอบ &lt;b&gt;' ) && false === strpos( $html, 'lh-ver-label' ), 'Free-text versions are escaped and the prefix can be emptied' );

list( $html, $cards ) = render_downloads( array(
	'links_plus_url'  => 'https://example.test/fenix-plus.zip',
	'links_fast_url'  => 'https://example.test/fenix-dual.zip',
	'links_card3_url' => 'https://example.test/fenix-btc.zip',
) );
check( false === strpos( $html, 'lh-feature-ver' ), 'No badge when the file name carries no version and none is typed' );

list( $html, $cards ) = render_downloads( array( 'links_fast_version' => '7.0 RC' ) );
check( 1 === preg_match( '~lh-feature-fast.*?lh-ver-num">V7\.0 RC</span>~s', $html ), 'DUAL takes its own typed version' );
check( false !== strpos( $html, '<span class="lh-ver-num">V5.1</span>' ), 'Typing a DUAL version leaves the PLUS badge alone' );

list( $html, $cards ) = render_downloads( array( 'links_card3_enabled' => false ) );
check( 2 === $cards->length && null === card_with_class( $cards, 'lh-feature-card3' ), 'Turning the third card off leaves the other two' );

list( $html, $cards ) = render_downloads( array( 'links_card3_version' => '9.0' ) );
check( false !== strpos( $cards->item( 2 )->getAttribute( 'class' ), 'lh-feature-card3' ), 'Raising a card version moves it down the list' );

list( $html, $cards ) = render_downloads( array( 'links_card3_url' => 'https://example.test/no-version.zip' ) );
check( false !== strpos( $cards->item( 2 )->getAttribute( 'class' ), 'lh-feature-card3' ), 'A card with no version number sits last' );

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
check( array( 'https://apps.apple.com/us/app/metatrader-5/id413251709', 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5', 'https://www.metatrader5.com/en/download' ) === $hrefs && $blank, 'MT5 step has iPhone, Android, Windows tiles (new tab), macOS hidden' );
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

// ---- ปุ่มดาวน์โหลดบนรูป ----
list( $html, $cards ) = render_downloads( array() );
check( 3 === substr_count( $html, 'lh-dl-chip ' ), 'Every download card carries its own download button' );
foreach ( array( 'lh-feature-card3' => 'FENIX_BTC_V1.1.zip', 'lh-feature-plus' => 'FENIX_PLUS_V5.1.zip', 'lh-feature-fast' => 'FENIX_DUAL_V6.3.zip' ) as $lh_class => $lh_file ) {
	$card  = card_with_class( $cards, $lh_class );
	$frame = $card->getElementsByTagName( 'a' )->item( 0 );
	$chips = array();
	foreach ( $frame->getElementsByTagName( 'span' ) as $span ) {
		if ( false !== strpos( $span->getAttribute( 'class' ), 'lh-dl-chip' ) ) {
			$chips[] = $span;
		}
	}
	check( 1 === count( $chips ), "$lh_class has exactly one download button" );
	check( false !== strpos( $frame->getAttribute( 'href' ), $lh_file ) && $frame->hasAttribute( 'download' ), "The $lh_class button sits inside the link to that card's own file" );
	check( false !== strpos( $chips[0]->textContent, 'ดาวน์โหลด' ), 'The ' . $lh_class . ' button says what it does' );
	check( 1 === $card->getElementsByTagName( 'a' )->length, "$lh_class keeps a single link, so the button cannot be nested in another" );
}

check( 3 === substr_count( $html, 'lh-dl-chip--bl' ), 'Buttons sit bottom left out of the box' );

list( $html, $cards ) = render_downloads( array( 'links_download_pos' => 'br' ) );
check( 3 === substr_count( $html, 'lh-dl-chip--br' ) && false === strpos( $html, 'lh-dl-chip--bl' ), 'The corner is a Customizer choice' );

list( $html, $cards ) = render_downloads( array( 'links_download_pos' => 'bc' ) );
check( 3 === substr_count( $html, 'lh-dl-chip--bc' ), 'Bottom centre is offered too' );

list( $html, $cards ) = render_downloads( array( 'links_download_pos' => 'top"><script>' ) );
check( 3 === substr_count( $html, 'lh-dl-chip--bl' ) && false === strpos( $html, '<script>' ), 'A position the theme does not know falls back to bottom left' );

list( $html, $cards ) = render_downloads( array( 'links_download_label' => 'เก็บไฟล์ลงเครื่อง' ) );
check( 3 === substr_count( $html, 'เก็บไฟล์ลงเครื่อง' ) && false === strpos( $html, '>ดาวน์โหลด<' ), 'The button wording comes from the Customizer' );

list( $html, $cards ) = render_downloads( array( 'links_download_label' => 'Download <b>' ) );
check( false !== strpos( $html, 'Download &lt;b&gt;' ), 'The button label is escaped' );

list( $html, $cards ) = render_downloads( array( 'links_download_label' => '  ' ) );
check( false === strpos( $html, 'lh-dl-chip' ) && 3 === $cards->length, 'Emptying the label hides the buttons and leaves the cards alone' );

list( $html, $cards ) = render_downloads( array( 'links_card3_enabled' => false ) );
check( 2 === substr_count( $html, 'lh-dl-chip ' ) && false === strpos( $html, 'FENIX_BTC_V1.1.zip' ), 'A hidden card takes its download button with it' );

list( $html, $cards ) = render_downloads( array() );
check( false !== strpos( $html, 'กดปุ่มดาวน์โหลดบนรูปของตัวที่ต้องการ' ), 'Step 4 points at the button on the image' );

foreach ( array( 'กดที่รูปเพื่อดาวน์โหลดไฟล์ EA', 'กดที่รูปหรือปุ่มด้านล่างเพื่อดาวน์โหลดไฟล์ EA' ) as $lh_old ) {
	list( $html, $cards ) = render_downloads( array( 'links_step4_desc' => $lh_old ) );
	check( false !== strpos( $html, 'กดปุ่มดาวน์โหลดบนรูปของตัวที่ต้องการ' ) && false === strpos( $html, '>' . $lh_old . '<' ), 'Older saved step 4 wording migrates: ' . $lh_old );
}
// ---- ป้ายเลขเวอร์ชันบนรูป ----
list( $html, $cards ) = render_downloads( array() );
check( 3 === substr_count( $html, 'lh-feature-ver--br' ), 'Version badges sit bottom right on the cover out of the box' );
$frame = card_with_class( $cards, 'lh-feature-card3' )->getElementsByTagName( 'a' )->item( 0 );
$inside = false;
foreach ( $frame->getElementsByTagName( 'span' ) as $span ) {
	if ( false !== strpos( $span->getAttribute( 'class' ), 'lh-feature-ver' ) ) {
		$inside = true;
	}
}
check( $inside, 'The badge rides inside the cover link, so the whole card still downloads' );
check( 1 === preg_match( '~lh-feature-ver--on[^>]*>.*?lh-ver-num">V1\.1</span>~s', $html ), 'The overlaid badge keeps label and number together' );

list( $html, $cards ) = render_downloads( array( 'links_version_pos' => 'bl' ) );
check( 3 === substr_count( $html, 'lh-feature-ver--bl' ) && false === strpos( $html, 'lh-feature-ver--br' ), 'The badge corner is a Customizer choice' );

list( $html, $cards ) = render_downloads( array( 'links_version_pos' => 'under' ) );
check( false === strpos( $html, 'lh-feature-ver--on' ) && 3 === substr_count( $html, 'class="lh-feature-ver"' ), 'Choosing under the cover brings the old row back' );
$card  = card_with_class( $cards, 'lh-feature-card3' );
$frame = $card->getElementsByTagName( 'a' )->item( 0 );
$inside = false;
foreach ( $frame->getElementsByTagName( 'span' ) as $span ) {
	if ( false !== strpos( $span->getAttribute( 'class' ), 'lh-feature-ver' ) ) {
		$inside = true;
	}
}
check( ! $inside && false !== strpos( $card->textContent, 'V1.1' ), 'Under the cover the badge leaves the link but stays on the card' );

list( $html, $cards ) = render_downloads( array( 'links_version_pos' => 'top"><script>' ) );
check( 3 === substr_count( $html, 'lh-feature-ver--br' ) && false === strpos( $html, '<script>' ), 'A badge position the theme does not know falls back to bottom right' );

list( $html, $cards ) = render_downloads( array( 'links_version_pos' => 'br', 'links_download_pos' => 'br' ) );
check( 3 === substr_count( $html, 'lh-dl-chip--br' ) && 3 === substr_count( $html, 'lh-feature-ver--br' ), 'Both can share a corner if that is what the owner picks' );

list( $html, $cards ) = render_downloads( array( 'links_card3_version' => '', 'links_card3_url' => 'https://example.test/fenix-btc.zip' ) );
check( 2 === substr_count( $html, 'lh-feature-ver--on' ), 'A file with no version in its name still gets no badge' );
