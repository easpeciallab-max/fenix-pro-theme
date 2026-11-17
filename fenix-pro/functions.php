<?php
/**
 * FENIX PRO EA · Theme functions
 *
 * @package fenix-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FENIX_VERSION', '1.0.0' );

/* --------------------------------------------------------------
 * Theme setup
 * -------------------------------------------------------------- */
function fenix_setup() {
	load_theme_textdomain( 'fenix-pro', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'elementor' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	register_nav_menus(
		array(
			'primary' => 'เมนูหลัก (Header)',
			'footer'  => 'เมนูท้ายเว็บ (Footer)',
		)
	);
}
add_action( 'after_setup_theme', 'fenix_setup' );

/* --------------------------------------------------------------
 * Styles & scripts
 * -------------------------------------------------------------- */
function fenix_assets() {
	$style_path    = get_stylesheet_directory() . '/style.css';
	$script_path   = get_template_directory() . '/assets/js/main.js';
	$style_version = file_exists( $style_path ) ? filemtime( $style_path ) : FENIX_VERSION;
	$script_version = file_exists( $script_path ) ? filemtime( $script_path ) : FENIX_VERSION;

	wp_enqueue_style(
		'fenix-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'fenix-style', get_stylesheet_uri(), array( 'fenix-fonts' ), $style_version );
	wp_enqueue_script( 'fenix-main', get_template_directory_uri() . '/assets/js/main.js', array(), $script_version, true );
	wp_localize_script(
		'fenix-main',
		'fenixLoadMore',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'fenix_load_more' ),
		)
	);
	if ( is_front_page() && ! is_paged() ) {
		$home_css = get_template_directory() . '/assets/css/home.css';
		$home_js  = get_template_directory() . '/assets/js/home.js';

		wp_enqueue_style(
			'fenix-home',
			get_template_directory_uri() . '/assets/css/home.css',
			array( 'fenix-style' ),
			file_exists( $home_css ) ? filemtime( $home_css ) : FENIX_VERSION
		);
		wp_enqueue_script(
			'fenix-home',
			get_template_directory_uri() . '/assets/js/home.js',
			array(),
			file_exists( $home_js ) ? filemtime( $home_js ) : FENIX_VERSION,
			true
		);
	}

	if ( is_page_template( 'template-links.php' ) ) {
		$go_css = get_template_directory() . '/assets/css/go.css';
		$go_js  = get_template_directory() . '/assets/js/go.js';

		wp_enqueue_style(
			'fenix-go',
			get_template_directory_uri() . '/assets/css/go.css',
			array( 'fenix-style' ),
			file_exists( $go_css ) ? filemtime( $go_css ) : FENIX_VERSION
		);
		wp_enqueue_script(
			'fenix-go',
			get_template_directory_uri() . '/assets/js/go.js',
			array(),
			file_exists( $go_js ) ? filemtime( $go_js ) : FENIX_VERSION,
			true
		);
	}

	if ( fenix_mod( 'ga_measurement_id' ) || fenix_mod( 'fb_pixel_id' ) ) {
		wp_localize_script(
			'fenix-main',
			'fenixTracking',
			array(
				'ga'    => fenix_mod( 'ga_measurement_id' ),
				'pixel' => fenix_mod( 'fb_pixel_id' ),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fenix_assets' );

/* --------------------------------------------------------------
 * Builder compatibility
 * -------------------------------------------------------------- */
function fenix_is_elementor_page( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

function fenix_elementor_data_has_widgets( $elements ) {
	if ( ! is_array( $elements ) ) {
		return false;
	}

	foreach ( $elements as $element ) {
		if ( ! empty( $element['widgetType'] ) ) {
			return true;
		}

		if ( ! empty( $element['elements'] ) && fenix_elementor_data_has_widgets( $element['elements'] ) ) {
			return true;
		}
	}

	return false;
}

function fenix_has_elementor_content( $post_id = null ) {
	if ( ! fenix_is_elementor_page( $post_id ) ) {
		return false;
	}

	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
	if ( empty( $elementor_data ) ) {
		return false;
	}

	$elements = json_decode( $elementor_data, true );

	return fenix_elementor_data_has_widgets( $elements );
}

function fenix_uses_elementor_page_template( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$template = $post_id ? get_page_template_slug( $post_id ) : '';

	return in_array(
		$template,
		array(
			'elementor_canvas',
			'elementor_header_footer',
			'template-elementor-canvas.php',
			'template-elementor-full-width.php',
		),
		true
	);
}

function fenix_body_classes( $classes ) {
	if ( is_page() && fenix_is_elementor_page( get_queried_object_id() ) ) {
		$classes[] = 'fenix-has-elementor';
	}

	if ( is_front_page() && ! is_paged() ) {
		$classes[] = 'fenix-home';
	}

	return $classes;
}
add_filter( 'body_class', 'fenix_body_classes' );

/* --------------------------------------------------------------
 * Default content (ทุกค่าแก้ได้ในหน้า "ปรับแต่ง / Customize")
 * -------------------------------------------------------------- */
function fenix_defaults() {
	static $d = null;
	if ( null !== $d ) {
		return $d;
	}

	$install_assets = get_template_directory_uri() . '/assets/img/install/';

	$d = array(
		/* ทั่วไป */
		'line_url'        => 'https://line.me/R/ti/p/@fenixpro',
		'facebook_url'    => 'https://www.facebook.com/fenixpro.trading',
		'instagram_url'   => 'https://www.instagram.com/fenixproea_th/',
		'tiktok_url'      => 'https://www.tiktok.com/@fenixpro.ea',
		'youtube_url'     => 'https://www.youtube.com/@FenixProExpertAdvisor',
		'contact_email'   => '',
		'show_float_line' => false,
		'float_line_text' => 'สอบถามทาง LINE',
		'show_language_switcher' => true,
		'language_fallback_items' => "th|🇹🇭|TH|ไทย\nen|🇬🇧|EN|English\nzh|🇨🇳|ZH|中文\nfr|🇫🇷|FR|Français\nde|🇩🇪|DE|Deutsch\nru|🇷🇺|RU|Русский\nja|🇯🇵|JA|日本語\nko|🇰🇷|KO|한국어",

		/* Mobile bottom bar */
		'show_mobile_nav'         => true,
		'mobile_nav_home_label'   => 'หน้าแรก',
		'mobile_nav_home_url'     => '/',
		'mobile_nav_test_label'   => 'ผลทดสอบ',
		'mobile_nav_test_url'     => '/backtest/',
		'mobile_nav_price_label'   => 'แพ็กเกจ',
		'mobile_nav_price_url'     => '/pricing/',
		'mobile_nav_dash_label'    => 'แดชบอร์ด',
		'mobile_nav_dash_url'      => '',
		'mobile_nav_line_label'    => 'ทัก LINE',

		/* แดชบอร์ดลูกค้า (ปุ่มบนแถบเมนู + เมนูลัดมือถือ) */
		'dashboard_url'       => 'https://dashboard.fenixpro-th.com/',
		'show_header_auth'    => true,
		'header_dash_label'   => 'แดชบอร์ด',

		/* SEO / แชร์ลิงก์ (Open Graph) */
		'og_default_image'       => '',
		'og_default_description' => 'FENIX PRO EA · ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5 เน้นวินัยและการบริหารความเสี่ยง',
		'search_console_verify'  => 'J1uZxPgUyDbO0lx3KNpfEfb8cpJ_zgpwMbcMOI-rzy8',
		'bing_verify'            => '',

		/* หน้า Link Hub (template-links.php) · ปลายทางยิงแอด สไตล์ Linktree */
		/* ===== หน้า /go/ ดีไซน์ใหม่ (อนุมัติ 16 พ.ย. 2026) ===== */
		'links_cover_wm'      => 'FENIX PRO',
		'links_cover_tape'    => 'EA for MT5',
		'links_risk'          => 'การเทรดมีความเสี่ยง อาจขาดทุนได้บางส่วนหรือทั้งหมด ผลในอดีตไม่ได้รับประกันผลในอนาคต ศึกษาก่อนใช้งานจริง',
		'links_steps_kicker'  => 'Getting started',
		'links_steps_sub'     => 'ทำตามลำดับจากขั้นที่ 1 ถึง 6 ติดตรงไหนทักทีมได้ตลอด',
		'links_shortcut_label' => 'ทางลัด',
		'links_shortcuts'     => "ดาวน์โหลด EA | #dl | download\nคู่มือติดตั้ง | #install | book\nVPS | #vps | server\nผลเทรดจริง | https://www.myfxbook.com/portfolio/eafn/12227282 | chart",
		'links_device_badge'  => 'อุปกรณ์ของคุณ',
		'links_dest_prefix'   => 'ไปที่',
		'links_btn1_sub'      => 'เปิดดูบน Myfxbook',
		'links_btn2_sub'      => 'เริ่มต้น FREE · PRO 6,990 บาท',
		'links_ea_note'       => "ใช้กับ MetaTrader 5 เท่านั้น\n1 ไฟล์ EA ต่อ 1 บัญชี",
		'links_algo_label'    => 'Algo Trading',
		'links_algo_strong'   => 'อย่าลืมเปิดปุ่มนี้',
		'links_algo_text'     => 'ถ้าปุ่มปิดอยู่ EA จะไม่ส่งคำสั่งเลย',
		'links_vps_labels'    => "Windows\nAndroid\niOS",
		'links_vps_why'       => "ไม่ต้องเปิดคอมทิ้งไว้\nไฟดับหรือเน็ตบ้านหลุดก็ไม่กระทบ",

		'links_title'      => 'FENIX PRO EA',
		'links_tagline'    => 'ระบบช่วยเทรดอัตโนมัติบน MetaTrader 5 เน้นบริหารความเสี่ยง มีทีมไทยดูแล',
		'links_badges'     => "3 | EA ในชุด
MT5 | เท่านั้น
ทีมไทยดูแล",
		'links_logo'       => '',
		'links_signup_label' => 'เปิดบัญชี MT5',
		'links_signup_url'   => 'https://portal.zaurix.com?ref=210028',
		'links_account_guide_label' => 'สอนเปิดบัญชี',
		'links_account_guide_url'   => '/open-mt5-account/',
		'links_mt5_download_label'  => 'วิธี Login เข้า Zaurix-Server',
		'links_mt5_download_url'    => '/mt5-login-zaurix-server/',
		'links_mt5_install_title'   => 'ติดตั้งแอป MT5 และ Login',
		'links_top_line_label'      => '',
		'links_steps_title'         => 'เริ่มใช้งานใน {n} ขั้นตอน',
		'links_step1_title'         => 'เปิดบัญชี Zaurix',
		'links_step1_desc'          => 'สมัครและยืนยันตัวตน (KYC) เพื่อรับ Login และ Password ของ MT5',
		'links_step2_desc'          => 'เลือกอุปกรณ์ที่ใช้ แล้ว Login ด้วยบัญชีจากขั้นที่ 1',
		'links_step3_title'         => 'ฝากเงินเข้าบัญชี',
		'links_step3_desc'          => 'ฝากเงินเข้าบัญชีเทรดก่อนเปิดใช้ EA ผ่านหน้า Portal ของ Zaurix',
		'links_deposit_label'       => 'เข้า Portal Zaurix',
		'links_deposit_url'         => 'https://portal.zaurix.com/',
		'links_deposit_guide_label' => 'คู่มือฝากเงิน',
		'links_deposit_guide_url'   => 'https://fenixpro-th.com/deposit-zaurix/',
		'links_step4_title'         => 'ดาวน์โหลด EA',
		'links_step4_desc'          => 'กดปุ่มดาวน์โหลดบนรูปของตัวที่ต้องการ',
		'links_step5_title'         => 'ติดตั้ง EA บน MT5',
		'links_step5_desc'          => 'วางไฟล์ EA ลงใน MT5 แล้วเปิด Algo Trading',
		'links_step6_title'         => 'ให้ EA ทำงาน 24 ชั่วโมงด้วย VPS',
		'links_step6_desc'          => 'เลือกคู่มือ VPS ตามอุปกรณ์ที่ใช้',
		'links_step6_badge'         => 'แนะนำ',
		'links_info_title'          => 'ข้อมูลก่อนตัดสินใจ',
		'links_help_title'          => 'ติดขั้นตอนไหน ทักทีมได้',
		'links_mt5_ios_label'       => 'iPhone',
		'links_mt5_ios_url'         => 'https://apps.apple.com/us/app/metatrader-5/id413251709',
		'links_mt5_android_label'   => 'Android',
		'links_mt5_android_url'     => 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5',
		'links_mt5_windows_label'   => 'Windows',
		'links_mt5_windows_url'     => 'https://www.metatrader5.com/en/download',
		'links_mt5_macos_label'     => 'macOS',
		'links_mt5_macos_url'       => '',
		'links_line_label' => 'ทักไลน์ปรึกษาทีมงานฟรี 24 ชั่วโมง',
		'links_openchat_label' => 'เข้ากลุ่ม FENIX OpenChat',
		'links_openchat_url'   => 'https://line.me/ti/g2/6O9XyBBtatI9LF_Z7N1dpbw7aOgE0IAl39m3bw?utm_source=invitation&utm_medium=link_copy&utm_campaign=default',
		'links_fast_enabled' => true,
		'links_fast_show'    => true,
		'links_fast_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-dual-v3.webp',
		'links_fast_url'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_DUAL_V6.3.zip',
		'links_fast_alt'     => 'ดาวน์โหลด FENIX DUAL Expert Advisor สำหรับ MetaTrader 5',
		'links_fast_version' => '',
		'links_plus_enabled' => true,
		'links_plus_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-plus-v3.webp',
		'links_plus_url'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_PLUS_V5.1.zip',
		'links_plus_alt'     => 'ดาวน์โหลด FENIX PLUS Expert Advisor สำหรับ MetaTrader 5',
		'links_plus_version_label' => 'อัปเดตล่าสุด',
		'links_plus_version'       => '',
		'links_card3_enabled' => true,
		'links_card3_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/link-download-fenix-btc-v3.webp',
		'links_card3_url'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/FENIX_BTC_V1.1.zip',
		'links_card3_alt'     => 'ดาวน์โหลด FENIX BTC Expert Advisor สำหรับ MetaTrader 5',
		'links_card3_version' => '',
		'links_download_label' => 'ดาวน์โหลด',
		'links_download_pos'   => 'bl',
		'links_version_pos'    => 'br',
		'links_feature_img'     => '',
		'links_feature_url'     => '',
		'links_feature_caption' => '',
		'links_feature2_img'     => '',
		'links_feature2_url'     => '',
		'links_feature2_caption' => '',
		'links_feature2_placeholder' => '1200 × 630',
		'links_btn1_label' => 'ดูผลเทรดจริง (Forward Test)',
		'links_btn1_url'   => 'https://www.myfxbook.com/portfolio/eafn/12227282',
		'links_btn2_label' => 'แพ็กเกจ & ราคา',
		'links_btn2_url'   => '/pricing/',
		'links_btn3_label' => '',
		'links_btn3_url'   => '',
		'links_btn4_label' => '',
		'links_btn4_url'   => '',
		'links_btn5_label' => '',
		'links_btn5_url'   => '',
		'links_btn6_label' => '',
		'links_btn6_url'   => '',
		'links_guide1_label' => 'คู่มือติดตั้ง FENIX PRO EA',
		'links_guide1_url'   => '/fenix-pro-ea-install-guide/',
		'links_guide2_label' => 'ใช้งาน VPS บน Windows',
		'links_guide2_url'   => '/vps-desktop-windows/',
		'links_guide3_label' => 'ใช้งาน VPS ผ่าน Android',
		'links_guide3_url'   => '/vps-android/',
		'links_guide4_label' => 'ใช้งาน VPS ผ่าน iPhone / iOS',
		'links_guide4_url'   => '/vps-iphone-ios/',
		'links_guide5_label' => 'คู่มือการใช้งาน FENIX DUAL 6.3 (PDF)',
		'links_guide5_url'   => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-dual-63-manual-th.pdf',
		'links_note'       => '',

		/* คุกกี้ / Consent + Tracking (โหลด tracking เฉพาะหลังกดยอมรับ) */
		'show_cookie_consent' => false,
		'cookie_consent_text' => 'เว็บไซต์นี้ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานและวิเคราะห์การเข้าชม คุณเลือกยอมรับหรือปฏิเสธคุกกี้ที่ไม่จำเป็นได้',
		'ga_measurement_id'   => '',
		'fb_pixel_id'         => '',

		/* Hero */
		'show_hero'      => true,
		'hero_badge'     => '',
		'hero_title'     => 'FENIX PRO EA',
		'hero_subtitle'  => 'ระบบช่วยเทรดอัตโนมัติ เพื่อการเทรดที่มีวินัยมากขึ้น',
		'hero_desc'      => 'ออกแบบมาเพื่อช่วยให้การเทรดเป็นระบบ ลดการตัดสินใจตามอารมณ์ พร้อมแนวคิดบริหารความเสี่ยงสำหรับผู้ใช้งาน MetaTrader 5',
		'hero_btn1_text' => 'สอบถามรายละเอียด',
		'hero_btn2_text' => 'รู้จักระบบ',
		'hero_note'      => '',
		'hero_image'     => '',
		'hero_visual'        => 'deck',
		'hero_deck_featured' => 'plus',
		'hero_deck_badge'    => '',
		'hero_deck_cta'      => 'ดูรายละเอียดและโหลด',
		'hero_deck_cta_mode' => 'page',

		/* --- หน้าแรกชุดใหม่ (พ.ย. 2026) --- */
		'highlight_versions' => true,

		'show_notus'   => false,
		'notus_kicker' => 'Before anything else',
		'notus_title'  => 'ก่อนอื่น ขอบอกให้ชัดว่าเราไม่ได้ทำอะไร',
		'notus_sub'    => 'ตลาดนี้มีคนทำเสียหายไว้เยอะ เราเลยขอเริ่มจากสิ่งที่เราไม่ทำ',
		'notus_items'  => "ไม่รับฝากเงิน — เงินอยู่ในบัญชีเทรดของคุณที่โบรกเกอร์ ไม่ผ่านมือเราสักบาท\nไม่เทรดแทนคุณ — คุณเป็นคนติดตั้ง เปิดและปิดระบบเองทั้งหมด\nไม่การันตีผลตอบแทน — ไม่มีใครการันตีตลาดได้ เราจึงไม่พูดแบบนั้น\nไม่มีระบบชวนคนรับคอมมิชชัน — เราขายซอฟต์แวร์ ไม่ได้ขายสายงาน\nไม่ใช่โบรกเกอร์ และไม่ใช่ที่ปรึกษาการลงทุน — เราทำเครื่องมือ ส่วนการตัดสินใจเป็นของคุณ",

		'about_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-ea-wat-kue-arai-laptop-klang-duek.jpg',
		'about_img_alt' => 'โน้ตบุ๊กเปิดจออยู่ลำพังในห้องมืดยามค่ำคืน แทนภาพ EA ที่ทำงานแทนคนเทรด',
		'about_flow1'   => 'เฝ้ากราฟตลอดเวลา',
		'about_flow2'   => 'เงื่อนไขครบ ส่งคำสั่ง',
		'about_flow3'   => 'ปิดไม้ตามกฎที่ตั้งไว้',

		'pain_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-pros-pha-terd-eng-ton-kham.jpg',
		'pain_img_alt' => 'เงาดำของชายนั่งก้มดูโทรศัพท์ที่โต๊ะทำงานยามค่ำ ข้างจอคอมพิวเตอร์ที่ยังเปิดอยู่',

		'steps_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-rerm-tid-tang-ea-mue-phim-keyboard.jpg',
		'steps_img_alt' => 'มือกำลังพิมพ์บนคีย์บอร์ดโน้ตบุ๊กในห้องมืด ขณะติดตั้งโปรแกรมเทรด',

		'features_img'     => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-vps-server-room-run-ea-24-chua-mong.jpg',
		'features_img_alt' => 'แถวเครื่องเซิร์ฟเวอร์ในห้องดาต้าเซ็นเตอร์ ส่องไฟสีส้ม สำหรับรัน EA ต่อเนื่อง 24 ชั่วโมง',

		'prof1_title' => 'มนุษย์เงินเดือนที่ไม่มีเวลาเฝ้าจอ',
		'prof1_desc'  => 'กราฟวิ่งตอนคุณอยู่ในที่ประชุมหรือกำลังหลับ ระบบทำงานต่อให้ตามแผนที่ตั้งไว้',
		'prof2_title' => 'คนที่เทรดเป็น แต่แพ้ใจตัวเอง',
		'prof2_desc'  => 'รู้ว่าควรตัดขาดทุนตรงไหน แต่พอถึงเวลาจริงกลับถือต่อ ระบบไม่ต่อรองกับตัวเอง',
		'prof3_title' => 'คนที่มีพอร์ตอยู่แล้ว อยากแบ่งส่วนให้ระบบทำ',
		'prof3_desc'  => 'ไม่ได้จะทิ้งวิธีเดิม แต่อยากมีอีกส่วนที่ทำงานตามกฎทุกวันโดยไม่ใช้อารมณ์',

		'perf_conditions' => 'ผลทดสอบย้อนหลังใน MT5 · XAUUSD.c โบรก ZAURIX LTD. · M1 · Every Tick Based On Real Ticks · Leverage 1:2000 · โหมดเดียว FENIX STANDARD ขั้นละ 1 ไม้ · ช่วง 15 ม.ค. – 3 มิ.ย. 2569 MT5 จำลองราคาจากแท่ง 1 นาที · ตัวกรองข่าวแดงไม่ได้ทำงาน (บัญชีจริงผลต่างจากนี้ได้) · ไม่มีการตัดขาดทุนอัตโนมัติ · ค่าของโหมดนี้คัดจากผลทดสอบบนช่วงเวลาเดียวกัน ผลช่วงนี้จึงดีกว่าที่ควรคาดหวังในช่วงเวลาอื่น',
		'verify_title'    => 'ตรวจสอบเราเองได้ยังไง',
		'verify_items'    => "กดลิงก์ Myfxbook ด้านบน แล้วดูว่าเป็นหน้าเว็บจริง ไม่ใช่ภาพถ่ายหน้าจอ\nดูว่ามีตรา Verified ครบสองอัน ทั้ง Track Record และ Trading Privileges\nเทียบค่า Gain กับ Absolute Gain ถ้าต่างกันมาก แปลว่ามีการฝาก-ถอนทำให้ตัวเลขเพี้ยน\nไล่ดูเดือนที่ขาดทุนด้วย ระบบที่มีแต่เดือนกำไรคือสิ่งที่ควรสงสัย\nดูชื่อเซิร์ฟเวอร์โบรกเกอร์ว่าเป็นโบรกจริง ไม่ใช่ชื่อ Other MT5",

		'show_devices' => true,
		'dev_kicker'   => 'Requirements',
		'dev_title'    => 'ใช้กับอะไรได้บ้าง และต้องเปิดเครื่องทิ้งไว้ไหม',
		'dev_sub'      => 'คำถามที่ถูกถามมากที่สุดก่อนเริ่มใช้งาน ตอบไว้ตรงนี้เลย',
		'dev1_title'   => 'คอมพิวเตอร์ (WINDOWS)',
		'dev1_note'    => 'รัน EA ได้เต็มรูปแบบ แต่ต้องเปิดเครื่องทิ้งไว้ ถ้าปิดเครื่องหรือเน็ตหลุด ระบบก็หยุดทำงาน',
		'dev1_badge'   => '',
		'dev2_title'   => 'VPS',
		'dev2_note'    => 'เครื่องเช่าที่เปิดตลอด 24 ชั่วโมง ไม่ต้องเปิดคอมเอง ไฟดับหรือเน็ตบ้านมีปัญหาก็ไม่กระทบ',
		'dev2_badge'   => 'แนะนำ',
		'dev3_title'   => 'มือถือ / แท็บเล็ต',
		'dev3_note'    => 'ดูสถานะพอร์ตและปิดไม้เองได้ แต่รัน EA ไม่ได้ — เป็นข้อจำกัดของ MT5 เอง ไม่ใช่ของเรา',
		'dev3_badge'   => '',
		'dev_req'      => 'ต้องใช้ MetaTrader 5 เท่านั้น (ไม่รองรับ MT4) · บัญชีต้องเปิด Algo Trading ได้ · ใช้ไฟล์ EA หนึ่งไฟล์ต่อหนึ่งบัญชี',
		'dev_img'      => 'https://fenixpro-th.com/wp-content/uploads/2026/11/fenix-mt5-upakorn-notebook-mue-thue.jpg',
		'dev_img_alt'  => 'โน้ตบุ๊กและสมาร์ตโฟนวางคู่กันบนโต๊ะไม้สีเข้ม แทนอุปกรณ์ที่ใช้กับ MetaTrader 5',
		'dev_btn_label'=> 'ไปหน้าดาวน์โหลด EA',
		'dev_btn_url'  => '/go/',
		'dev_btn2_label' => 'คู่มือติดตั้งทีละขั้น',
		'dev_btn2_url'   => '/fenix-pro-ea-install-guide/',

		'show_own'   => true,
		'own_kicker' => 'What stays yours',
		'own_title'  => 'สิ่งที่ยังเป็นของคุณทั้งหมด',
		'own_sub'    => 'เราเป็นแค่เครื่องมือที่ไปอยู่ในบัญชีของคุณ ไม่ได้เข้าไปถือครองอะไรเลย',
		'own_items'  => "บัญชีเทรดเป็นชื่อคุณ เราไม่มีรหัสผ่านและไม่ขอ\nปิดระบบเมื่อไหร่ก็ได้ กดปุ่มเดียวใน MT5\nค่าตั้งค่าทุกตัวแก้เองได้ ไม่ต้องขออนุญาตใคร\nฝากถอนทำผ่านโบรกเกอร์ของคุณโดยตรง ไม่ผ่านเรา",

		'company_name'    => '',
		'company_reg'     => '',
		'company_address' => '',
		'company_email'   => '',


		/* ปัญหานักเทรด */
		'show_pain'     => true,
		'pain_title'    => 'เทรดเองแล้วเจอปัญหาแบบนี้ไหม?',
		'pain_subtitle' => 'ไม่ใช่เพราะคุณไม่เก่ง แต่เพราะคนเราไม่ได้ถูกออกแบบมาให้เฝ้าจอทั้งวันโดยไม่ใช้อารมณ์',
		'pain1_title'   => 'ใช้อารมณ์ในการเข้าออเดอร์',
		'pain1_desc'    => 'กลัวตกรถ รีบเข้า รีบออก สุดท้ายไม่ทำตามแผนที่วางไว้',
		'pain2_title'   => 'ไม่มีเวลานั่งเฝ้าจอทั้งวัน',
		'pain2_desc'    => 'พลาดจังหวะสำคัญ เพราะต้องทำงานประจำหรือติดธุระ',
		'pain3_title'   => 'คุม Lot และความเสี่ยงไม่เป็นระบบ',
		'pain3_desc'    => 'บางครั้งเทรดใหญ่เกินไป จนพอร์ตแกว่งแรงเกินกว่าจะรับไหว',
		'pain4_title'   => 'ขาดวินัยในการทำตามแผน',
		'pain4_desc'    => 'วางแผนไว้ดี แต่พอกราฟวิ่งจริงกลับเปลี่ยนใจกลางทาง',

		/* FENIX PRO EA คืออะไร */
		'show_about'  => true,
		'about_title' => 'EA คืออะไร แล้วมันทำงานยังไง',
		'about_text'  => "EA (Expert Advisor) คือโปรแกรมเล็ก ๆ ที่ติดตั้งลงใน MetaTrader 5 ของคุณ แล้วคอยเฝ้ากราฟแทนคุณตลอดเวลา\n\nเมื่อเงื่อนไขที่ตั้งไว้ล่วงหน้าเกิดขึ้นครบ มันจะส่งคำสั่งตามแผนนั้นทันที ไม่ลังเล ไม่เปลี่ยนใจกลางทาง และไม่หลับ หน้าที่ของคุณคือเลือกแผนและกำหนดว่ารับความเสี่ยงได้แค่ไหน ส่วนหน้าที่ของ EA คือทำตามแผนนั้นอย่างเคร่งครัด",

		/* จุดเด่น */
		'show_features'     => true,
		'features_title'    => 'จุดเด่นของ FENIX PRO EA',
		'features_subtitle' => 'ออกแบบมาเพื่อให้การเทรดของคุณเป็นระบบ ตรวจสอบได้ และอยู่ในกรอบความเสี่ยงที่วางไว้',
		'feat1_title'       => 'ระบบช่วยเทรดอัตโนมัติ',
		'feat1_desc'        => 'เข้าและออกออเดอร์ตามเงื่อนไขที่กำหนดไว้ล่วงหน้า ไม่ใช่ตามอารมณ์',
		'feat2_title'       => 'รองรับ MetaTrader 5',
		'feat2_desc'        => 'พัฒนาด้วย MQL5 สำหรับผู้ใช้งานแพลตฟอร์ม MT5 โดยเฉพาะ',
		'feat3_title'       => 'Dashboard ดูง่าย',
		'feat3_desc'        => 'ติดตามสถานะระบบ กำไร/ขาดทุน และเงื่อนไขการทำงานได้ชัดเจนในหน้าจอเดียว',
		'feat4_title'       => 'แนวคิดบริหารความเสี่ยง',
		'feat4_desc'        => 'วางแผนเรื่อง Lot, Stop Loss และ Drawdown ได้ตามระดับความเสี่ยงที่เหมาะกับทุนของคุณ',
		'feat5_title'       => 'เหมาะกับคนไม่มีเวลาเฝ้าจอ',
		'feat5_desc'        => 'ให้ระบบช่วยทำงานตามแผนที่วางไว้ แม้ในเวลาที่คุณไม่อยู่หน้าจอ',
		'feat6_title'       => 'ปรับขนาดไม้ได้ตามทุน',
		'feat6_desc'        => 'เลือกตัวคูณ LOT ได้เองตั้งแต่เล็กสุดไปจนถึงใหญ่ ตามทุนและความเสี่ยงที่คุณรับได้',

		/* ภาพระบบ */
		'show_gallery'     => false,
		'gallery_title'    => 'หน้าตาระบบจริง',
		'gallery_subtitle' => 'Dashboard ออกแบบให้ดูง่าย เห็นสถานะระบบ เงื่อนไขหลัก และผลการทำงานในบัญชีได้ชัดเจน',
		'gallery_note'     => '',
		'gallery_img1'     => $install_assets . 'step-05.jpg',
		'gallery_cap1'     => 'Dashboard แสดงสถานะการทำงานของ FENIX PRO EA',
		'gallery_img2'     => $install_assets . 'step-06.jpg',
		'gallery_cap2'     => 'ตัวอย่างการตั้งค่าความเสี่ยงและ Lot Size',
		'gallery_img3'     => '',
		'gallery_cap3'     => 'หน้าต่างตั้งค่าหลักของระบบ',
		'gallery_img4'     => '',
		'gallery_cap4'     => 'ตัวอย่างสถานะการทำงาน Buy / Sell',

		/* ขั้นตอนใช้งาน */
		'show_steps'     => true,
		'steps_kicker'   => 'Workflow',
		'steps_title'    => 'เริ่มใช้งานใน 4 ขั้นตอน',
		'steps_subtitle' => 'เราแนะนำให้ผ่านทั้ง 4 ด่านนี้ตามลำดับ ไม่ควรข้าม',
		'step1_title'    => 'ติดตั้ง MT5',
		'step1_desc'     => 'เตรียมบัญชีเทรดและติดตั้งแพลตฟอร์ม MetaTrader 5 บนเครื่องหรือ VPS',
		'step2_title'    => 'ติดตั้ง FENIX PRO EA',
		'step2_desc'     => 'ทีมงานช่วยแนะนำการติดตั้งและตั้งค่าเบื้องต้นจนระบบพร้อมทำงาน',
		'step3_title'    => 'ลองบนบัญชีเดโมก่อน',
		'step3_desc'     => 'รันด้วยเงินสมมติจนเห็นกับตาว่าระบบทำงานยังไงในตลาดจริง ก่อนเอาเงินจริงลง',
		'step4_title'    => 'เริ่มจริงด้วยไม้เล็ก แล้วค่อยปรับ',
		'step4_desc'     => 'เปิดจริงด้วยขนาดเล็กที่สุดก่อน ติดตามผ่าน Dashboard แล้วค่อยปรับตามทุนที่รับไหว',

		/* ผลการทดสอบ */
		'show_perf'         => true,
		'perf_kicker'       => 'Evidence',
		'perf_title'        => 'ผลการทดสอบ และวิธีตรวจสอบเราเอง',
		'perf_subtitle'     => 'ตัวเลขทุกชุดบนเว็บนี้มีเงื่อนไขกำกับเสมอ ถ้าเจอตัวเลขที่ไม่บอกเงื่อนไข อย่าเพิ่งเชื่อ ไม่ว่าจะของใคร',
		'stat1_label'       => 'ช่วงเวลาทดสอบ',
		'stat1_value'       => 'ระบุช่วงเวลา',
		'stat2_label'       => 'คู่เงินที่ทดสอบ',
		'stat2_value'       => 'เช่น XAUUSD',
		'stat3_label'       => 'Timeframe',
		'stat3_value'       => 'เช่น M15',
		'stat4_label'       => 'ทุนเริ่มต้น',
		'stat4_value'       => 'ระบุทุนทดสอบ',
		'stat5_label'       => 'Max Drawdown',
		'stat5_value'       => 'ระบุ %',
		'stat6_label'       => 'จำนวนออเดอร์',
		'stat6_value'       => 'ระบุจำนวน',
		'perf_image'        => '',
		'perf_image_caption'=> 'กราฟผลการทดสอบระบบ (Backtest / Forward Test)',
		'perf_note'         => '',
		'perf_disclaimer'   => 'ผลการทดสอบใช้เพื่อประกอบการศึกษาเท่านั้น ผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต ผู้ใช้งานควรเข้าใจความเสี่ยงก่อนตัดสินใจใช้งานระบบ',

		/* เหมาะกับใคร */
		'show_fit'       => true,
		'fit_title'      => 'แบบไหนที่ FENIX PRO ช่วยได้จริง',
		'fit_subtitle'   => 'ถ้าคุณตรงกับข้อใดข้อหนึ่งข้างล่างนี้ ระบบน่าจะช่วยคุณได้',
		'fit_good_title' => 'เหมาะกับ',
		'fit_good_items' => "คนที่อยากเทรดอย่างเป็นระบบ มีแบบแผนชัดเจน\nคนที่ต้องการลดการใช้อารมณ์ในการเทรด\nคนที่เข้าใจว่าการเทรดมีความเสี่ยง\nคนที่มีเวลาเรียนรู้การตั้งค่าระบบ\nคนที่มองว่า EA คือเครื่องมือช่วย ไม่ใช่เครื่องการันตีกำไร",
		'fit_bad_title'  => 'ถ้าคุณเป็นแบบนี้ อย่าเพิ่งใช้',
		'fit_bad_items'  => "คนที่มีทุนน้อยกว่า 10,000 USD
คนที่หวังกำไรเร็วหรือรวยทางลัด\nคนที่รับความเสี่ยงและการขาดทุนไม่ได้\nคนที่ไม่ต้องการศึกษาการใช้งานเลย\nคนที่คิดว่า EA จะทำเงินให้ได้ตลอดเวลา\nคนที่ไม่ได้ใช้เงินเย็นในการเทรด",

		/* แพ็กเกจ */
		'show_pricing'     => true,
		'pricing_title'    => 'แพ็กเกจการใช้งาน',
		'pricing_subtitle' => 'เลือกแพ็กเกจที่เหมาะกับระดับการใช้งานของคุณ หรือทักมาปรึกษาทีมงานก่อนตัดสินใจได้',
		'pricing_mode'     => 'price',
		'pricing_btn_text' => 'สอบถามแพ็กเกจนี้',
		'pricing_note'     => 'ราคาและเงื่อนไขเป็นไปตามแพ็กเกจที่เลือก สอบถามรายละเอียดล่าสุดและความเหมาะสมกับบัญชีของคุณได้ทาง LINE',
		'pkg1_name'        => 'Starter',
		'pkg1_tag'         => 'สำหรับสอบถามข้อมูลและเริ่มประเมินความเหมาะสม',
		'pkg1_price'       => 'FREE',
		'pkg1_period'      => 'กดสอบถาม',
		'pkg1_features'    => "ปรึกษาความเหมาะสมก่อนเริ่มใช้งาน\nดูภาพรวมระบบและแนวทางตั้งค่าเบื้องต้น\nรับคำแนะนำเรื่องทุน โบรกเกอร์ และบัญชี MT5\nสอบถามรายละเอียดผ่าน LINE Official",
		'pkg1_featured'    => false,
		'pkg2_name'        => 'Pro',
		'pkg2_tag'         => 'สำหรับใช้งานจริงต่อเนื่อง',
		'pkg2_price'       => '6,990',
		'pkg2_period'      => 'บาท',
		'pkg2_features'    => "ใช้งาน FENIX PRO EA สำหรับบัญชี MT5\nคู่มือการติดตั้งและตั้งค่าอย่างเป็นขั้นตอน\nไฟล์ Preset และแนวทางตั้งค่าตามระดับความเสี่ยง\nอัปเดตระบบตามรอบเวอร์ชัน\nSupport ผ่าน LINE Official",
		'pkg2_featured'    => true,
		'pkg3_name'        => 'VIP',
		'pkg3_tag'         => 'สำหรับคนที่อยากให้ทีมดูแลใกล้ชิด',
		'pkg3_price'       => '9,990',
		'pkg3_period'      => 'บาท',
		'pkg3_features'    => "รวมสิทธิ์ใช้งาน FENIX PRO EA ตามแพ็กเกจ\nทีมงานช่วยติดตั้งบนเครื่องหรือ VPS\nช่วยตรวจการตั้งค่าเบื้องต้นก่อนเริ่มใช้งาน\nแนะนำแนวคิดบริหารความเสี่ยงให้เหมาะกับทุน\nSupport แบบใกล้ชิดผ่าน LINE Official",
		'pkg3_featured'    => false,

		/* รีวิว (ปิดไว้ก่อน จนกว่าจะมีรีวิวจริง) */
		'show_reviews'     => false,
		'reviews_title'    => 'เสียงจากผู้ใช้งานจริง',
		'reviews_subtitle' => 'รีวิวจากลูกค้าที่ใช้งาน FENIX PRO EA',
		'rev1_text'        => 'ทีมงานช่วยติดตั้งดีมาก อธิบายเข้าใจง่าย มือใหม่ก็เริ่มต้นได้',
		'rev1_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',
		'rev2_text'        => 'Dashboard ดูง่าย เหมาะกับคนที่อยากเทรดอย่างเป็นระบบ',
		'rev2_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',
		'rev3_text'        => 'ชอบที่ทีมงานอธิบายเรื่องความเสี่ยงก่อนเริ่มใช้งานจริง',
		'rev3_name'        => 'ตัวอย่างรีวิว · แทนที่ด้วยรีวิวจริง',

		/* FAQ */
		'show_faq'     => true,
		'faq_title'    => 'คำถามที่พบบ่อย',
		'faq_subtitle' => 'รวมคำตอบสำหรับคำถามที่ลูกค้าสอบถามเข้ามามากที่สุด',
		'faq1_q'       => 'FENIX PRO EA คืออะไร?',
		'faq1_a'       => 'ระบบช่วยเทรดอัตโนมัติ (Expert Advisor) ที่ทำงานตามเงื่อนไขที่กำหนดไว้ล่วงหน้า บนแพลตฟอร์ม MetaTrader 5',
		'faq2_q'       => 'EA การันตีกำไรไหม?',
		'faq2_a'       => 'ไม่การันตี ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์ที่ใช้งาน',
		'faq3_q'       => 'ต้องมีความรู้เทรดมาก่อนไหม?',
		'faq3_a'       => 'ควรมีพื้นฐานเรื่อง Lot, Stop Loss, Drawdown และความเสี่ยง เพื่อให้ตั้งค่าระบบได้เหมาะสมกับทุนของตัวเอง',
		'faq4_q'       => 'ใช้ทุนเริ่มต้นเท่าไร?',
		'faq4_a'       => 'ขึ้นอยู่กับระดับความเสี่ยงที่เลือกและเงื่อนไขของโบรกเกอร์ แนะนำให้ทักมาปรึกษาทีมงานเพื่อประเมินทุนที่เหมาะสมก่อนเริ่มใช้งาน',
		'faq5_q'       => 'ต้องเปิดคอมตลอดเวลาไหม?',
		'faq5_a'       => 'EA ทำงานบน MT5 จึงต้องเปิดเครื่องให้ระบบทำงานต่อเนื่อง หรือใช้บริการ VPS เพื่อให้ระบบทำงานได้ตลอด 24 ชั่วโมง',
		'faq6_q'       => 'รองรับบัญชีแบบไหนบ้าง?',
		'faq6_a'       => 'โดยทั่วไปรองรับบัญชีของโบรกเกอร์ที่ใช้ MetaTrader 5 สอบถามความเข้ากันได้กับโบรกเกอร์ของคุณได้ทาง LINE',
		'faq7_q'       => 'ติดตั้งเองได้ไหม?',
		'faq7_a'       => 'ได้ มีคู่มือประกอบการติดตั้ง และหากไม่สะดวก ทีมงานมีบริการช่วยติดตั้งและตั้งค่าให้จนระบบพร้อมใช้งาน',
		'faq8_q'       => 'การใช้ EA มีความเสี่ยงอะไรบ้าง?',
		'faq8_a'       => 'ความผันผวนของตลาด ข่าวแรง Slippage Spread ที่กว้างขึ้น การตั้งค่าที่ไม่เหมาะสม และการใช้ทุนเกินระดับความเสี่ยงที่รับได้ ทั้งหมดนี้คือสิ่งที่ควรเข้าใจก่อนเริ่มใช้ระบบ',
		'faq9_q'       => 'ถ้าขาดทุนติดกันหลายเดือน ทีมทำอะไร?',
		'faq9_a'       => 'เราจะแจ้งผู้ใช้งานผ่าน LINE ว่าเกิดอะไรขึ้น ทบทวนค่าตั้งค่ากับสภาพตลาดช่วงนั้น และถ้าจำเป็นต้องปรับหรือหยุดระบบชั่วคราว เราจะบอกตรง ๆ ไม่ปล่อยให้คุณเดาเอง',
		'faq10_q'      => 'Drawdown สูงสุดที่เคยเกิดคือเท่าไร?',
		'faq10_a'      => 'ดูตัวเลขล่าสุดของแต่ละตัวได้ที่หน้าผลทดสอบ ซึ่งระบุช่วงเวลา ทุนเริ่มต้น และโบรกเกอร์กำกับไว้ทุกครั้ง ถ้าอยากได้ตัวเลขของเวอร์ชันที่คุณจะใช้จริง ทักมาถามทีมงานได้เลย',

		/* คำเตือนความเสี่ยง */
		'show_risk'  => true,
		'risk_title' => 'คำเตือนความเสี่ยง',
		'risk_text'  => 'การเทรด Forex, Gold, CFD หรือสินทรัพย์ทางการเงินมีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ทั้งบางส่วนหรือทั้งหมดของเงินทุน ผลการทดสอบหรือผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต FENIX PRO EA เป็นเครื่องมือช่วยเทรดตามเงื่อนไขที่กำหนด ไม่ใช่ระบบรับประกันผลกำไร ผู้ใช้งานควรศึกษาข้อมูล เข้าใจข้อจำกัดของระบบ และบริหารความเสี่ยงให้เหมาะสมกับตนเองก่อนใช้งานจริง',

		/* CTA ปิดท้าย */
		'show_cta'     => true,
		'cta_title'    => 'พร้อมเริ่มเทรดอย่างเป็นระบบกับ FENIX PRO EA แล้วหรือยัง?',
		'cta_subtitle' => 'สอบถามรายละเอียด การติดตั้ง แพ็กเกจ และความเหมาะสมกับทุนของคุณได้ทาง LINE',
		'cta_btn_text' => 'ทัก LINE เพื่อขอรายละเอียด',

		/* Footer */
		'footer_kicker'       => 'FENIX PRO EA',
		'footer_cta_title'    => 'พร้อมเริ่มต้นใช้งาน FENIX PRO?',
		'footer_cta_text'     => 'สอบถามการติดตั้ง เงื่อนไขการใช้งาน และความเหมาะสมกับทุนของคุณได้ทาง LINE',
		'footer_line_text'    => 'ทัก LINE Official Account',
		'footer_facebook_text' => 'Facebook Page',
		'footer_email_text'    => 'Email Support',
		'footer_prep_title'    => 'ก่อนทัก LINE',
		'footer_prep_text'     => 'เตรียมข้อมูลสั้น ๆ เพื่อให้ทีมช่วยแนะนำได้ตรงขึ้น',
		'footer_prep_items'    => "ทุนที่ต้องการใช้กับระบบ\nโบรกเกอร์และประเภทบัญชี MT5\nเป้าหมาย: ติดตั้ง / สอบถามราคา / ตรวจความเหมาะสม\nช่วงเวลาที่สะดวกให้ทีมติดต่อกลับ",
		'footer_tagline'      => "FENIX PRO EA ถูกออกแบบให้เป็นผู้ช่วยจัดระบบการเทรดบน MetaTrader 5 สำหรับผู้ที่ต้องการลดการตัดสินใจตามอารมณ์ และให้การทำงานเป็นไปตามแผนที่กำหนดไว้อย่างมีวินัย\n\nแนวทางของระบบให้ความสำคัญกับการใช้งานภายใต้กรอบความเสี่ยงที่ชัดเจน ช่วยให้ผู้ใช้พิจารณาความเหมาะสมของทุน การตั้งค่า และเงื่อนไขการใช้งานก่อนเริ่มต้นจริง",
		'footer_risk_link'    => 'คำเตือนความเสี่ยง',
	);

	/* ===== เนื้อหาหน้าย่อย (multipage) ===== */
	$d = array_merge(
		$d,
		array(

			/* หน้าแรก · แถบไฮไลต์ */
			'show_highlight' => false,
			'highlight1'     => 'ทำงานบน METATRADER 5 เท่านั้น',
			'highlight2'     => 'ต้องรันต่อเนื่องบนคอมหรือ VPS',
			'highlight3'     => 'ตั้งค่าความเสี่ยงเองได้ทุกตัว',
			'highlight4'     => '',

			/* หน้าแรก · แถบสถานะ / Control center */
			'show_live_status'     => false,
			'live_status_kicker'   => 'Live System Flow',
			'live_status_items'    => "FENIX PRO EA บน MetaTrader 5\nRisk-first setup\nBacktest และ Forward Test\nตั้งค่าตามทุนและความเสี่ยง\nLINE Support ภาษาไทย",
			'show_control_center'  => false,
			'control_kicker'       => 'FENIX PRO EA Control Center',
			'control_title'        => 'ภาพรวมก่อนเริ่มใช้งาน FENIX PRO EA',
			'control_subtitle'     => 'ดูขั้นตอนสำคัญของระบบ ตั้งแต่ความพร้อมของ MetaTrader 5 การตั้งค่าความเสี่ยง ไปจนถึงการติดตามผลผ่าน Dashboard โดยไม่ต้องเดาเอง',
			'control_panel_title'  => 'System Readiness',
			'control_panel_status' => 'พร้อมประเมินความเหมาะสม',
			'control_badge'        => 'MT5',
			'control_panel_text'   => 'ก่อนเริ่มใช้งาน ทีมจะช่วยเช็กข้อมูลหลักที่ส่งผลต่อการตั้งค่า เช่น ประเภทบัญชี โบรกเกอร์ ทุนที่ใช้ และระดับความเสี่ยงที่รับได้',
			'control_metric1_label' => 'Platform',
			'control_metric1_value' => 'MetaTrader 5',
			'control_metric2_label' => 'Mode',
			'control_metric2_value' => 'EA Setup',
			'control_metric3_label' => 'Focus',
			'control_metric3_value' => 'Risk-first',
			'control_list_title'   => 'สิ่งที่ควรเตรียมก่อนเริ่ม',
			'control_list_items'   => "บัญชี MetaTrader 5 และโบรกเกอร์ที่ใช้งาน\nทุนที่ต้องการนำมาใช้กับระบบ\nระดับความเสี่ยงที่รับได้\nเป้าหมายการใช้งาน: ติดตั้ง / ทดลอง / ปรับพอร์ต",

			/* หน้าแรก · การ์ดนำทาง */
			'home_cards_title' => 'ดูข้อมูลเชิงลึกต่อ',
			'home_cards_sub'   => 'เราแยกข้อมูลเป็นหมวด เพื่อให้คุณศึกษาได้ละเอียดก่อนตัดสินใจ',
			'card1_title'      => 'ผล Backtest',
			'card1_desc'       => 'ผลการทดสอบย้อนหลังพร้อมเงื่อนไขการทดสอบที่ชัดเจน',
			'card1_url'        => '/backtest/',
			'card2_title'      => 'ผล Forward Test',
			'card2_desc'       => 'ผลการทดสอบบนบัญชีจริง/เดโม่แบบเรียลไทม์',
			'card2_url'        => '/forward-test/',
			'card3_title'      => 'แพ็กเกจ & ราคา',
			'card3_desc'       => 'เปรียบเทียบแพ็กเกจและสิ่งที่ได้รับในแต่ละระดับ',
			'card3_url'        => '/pricing/',
			'card4_title'      => 'วิธีติดตั้ง',
			'card4_desc'       => 'คู่มือติดตั้ง EA บน MT5 ทีละขั้นตอน',
			'card4_url'        => '/how-to-install/',
			'card5_title'      => 'คำเตือนความเสี่ยง',
			'card5_desc'       => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งานจริง',
			'card5_url'        => '/risk-disclosure/',

			/* หน้า Backtest */
			'backtest_sub'        => 'ผลการทดสอบย้อนหลัง (Historical Backtest)',
			'backtest_intro'      => 'Backtest คือการนำกลยุทธ์ของ EA มาทดสอบกับข้อมูลราคาในอดีต เพื่อดูพฤติกรรมของระบบภายใต้เงื่อนไขที่กำหนด ผลด้านล่างระบุพารามิเตอร์การทดสอบไว้อย่างชัดเจนเพื่อความโปร่งใส',
			'bt_stat1_label'      => 'ช่วงเวลาทดสอบ',
			'bt_stat1_value'      => 'ระบุช่วงเวลา',
			'bt_stat2_label'      => 'คู่เงิน / สินทรัพย์',
			'bt_stat2_value'      => 'เช่น XAUUSD',
			'bt_stat3_label'      => 'Timeframe',
			'bt_stat3_value'      => 'เช่น M15',
			'bt_stat4_label'      => 'ทุนเริ่มต้น',
			'bt_stat4_value'      => 'ระบุทุน',
			'bt_stat5_label'      => 'กำไรสุทธิ (Net Profit)',
			'bt_stat5_value'      => 'ระบุผล',
			'bt_stat6_label'      => 'Profit Factor',
			'bt_stat6_value'      => 'ระบุค่า',
			'bt_stat7_label'      => 'Max Drawdown',
			'bt_stat7_value'      => 'ระบุ %',
			'bt_stat8_label'      => 'จำนวนเทรดทั้งหมด',
			'bt_stat8_value'      => 'ระบุจำนวน',
			'backtest_img'        => '',
			'backtest_img_caption'=> 'กราฟ Equity / รายงานผล Backtest จาก MT5',
			'backtest_note'       => 'หมายเหตุ: ผลขึ้นอยู่กับคุณภาพข้อมูลราคา Spread, Commission และ Slippage ที่ใช้ในการทดสอบ',
			'backtest_disclaimer' => 'ผลการทดสอบย้อนหลังใช้เพื่อการศึกษาเท่านั้น ไม่ได้รับประกันผลลัพธ์ในอนาคต และไม่ใช่คำแนะนำในการลงทุน',

			/* หน้า Forward Test */
			'forward_sub'        => 'ผลเทรดจากบัญชีจริงที่ทีมงานรัน FENIX PLUS อยู่ ตรวจสอบเองได้ทุกไม้',
			'forward_intro'      => 'Forward Test คือการปล่อยให้ระบบทำงานกับตลาดจริงตามเวลาจริง ต่างจาก Backtest ที่เป็นการทดสอบย้อนหลังกับข้อมูลราคาในอดีต บัญชีที่เปิดให้ดูนี้เป็นบัญชีจริงบน MetaTrader 5 โบรกเกอร์ Zaurix ที่ทีมงานรัน FENIX PLUS อยู่ เชื่อมเข้ากับ Myfxbook ซึ่งเป็นผู้ให้บริการภายนอก ตัวเลขจึงถูกดึงจากบัญชีโดยตรงและอัปเดตเองตลอดเวลา ไม่ได้พิมพ์ด้วยมือ กดปุ่มด้านล่างเพื่อเปิดดูทั้งกำไร จำนวนไม้ และ Drawdown',
			'fw_stat1_label'     => 'ช่วงเวลาทดสอบ',
			'fw_stat1_value'     => '',
			'fw_stat2_label'     => 'ประเภทบัญชี',
			'fw_stat2_value'     => '',
			'fw_stat3_label'     => 'คู่เงิน / สินทรัพย์',
			'fw_stat3_value'     => '',
			'fw_stat4_label'     => 'ทุนเริ่มต้น',
			'fw_stat4_value'     => '',
			'fw_stat5_label'     => 'ผลตอบแทนสะสม',
			'fw_stat5_value'     => '',
			'fw_stat6_label'     => 'Max Drawdown',
			'fw_stat6_value'     => '',
			'forward_img'        => '',
			'forward_img_caption'=> 'ภาพผลการทดสอบจาก Myfxbook / FX Blue / MT5',
			'forward_link_label' => 'เปิดดูบัญชีจริงบน MYFXBOOK',
			'forward_link_url'   => 'https://www.myfxbook.com/portfolio/eafn/12227282',
			'forward_note'       => 'เราไม่คัดลอกตัวเลขมาพิมพ์ไว้ในหน้านี้ เพราะตัวเลขจะเก่าทันทีที่ตลาดขยับ ดูจากหน้า Myfxbook โดยตรงจะเห็นค่าล่าสุดเสมอ และอย่าดูแต่กำไร ให้ดู Drawdown กับส่วนต่างระหว่าง Balance กับ Equity ควบคู่กันทุกครั้ง · บัญชีนี้เป็นผลของ FENIX PLUS เท่านั้น ใช้แทนผลของ FENIX BTC หรือ FENIX DUAL ไม่ได้ และเป็นผลสะสมข้ามรุ่น ไม่ใช่ผลของรุ่นล่าสุดล้วน ๆ',
			'forward_disclaimer' => 'ผลของบัญชีนี้สะท้อนเฉพาะช่วงเวลาที่รันจริงเท่านั้น ผลในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต ระบบที่ใช้ไม่มีการตัดขาดทุนอัตโนมัติ การเทรดมีความเสี่ยงสูง อาจสูญเสียเงินทุนทั้งหมด ใช้เฉพาะเงินที่รับความเสี่ยงได้',

			/* หน้า How to Install */
			'install_sub'       => 'คู่มือติดตั้งและเริ่มใช้งานทีละขั้นตอน',
			'install_intro'     => 'ทำตามขั้นตอนด้านล่างเพื่อเริ่มใช้งาน FENIX PRO EA หากติดขั้นตอนไหน ทีมงานพร้อมช่วยเหลือผ่าน LINE',
			'install_req'       => "บัญชีเทรดของโบรกเกอร์ที่รองรับ MetaTrader 5\nโปรแกรม MetaTrader 5 (PC หรือ VPS)\nไฟล์ FENIX PRO EA ที่ได้รับหลังสั่งซื้อ\nแนะนำใช้ VPS เพื่อให้ระบบทำงานต่อเนื่อง 24 ชม.",
			'inst_step1_title'  => 'ติดตั้ง MetaTrader 5 / เตรียม VPS',
			'inst_step1_desc'   => 'ดาวน์โหลดและติดตั้ง MT5 จากโบรกเกอร์ของคุณ หากต้องการให้ระบบรันตลอด 24 ชม. แนะนำให้เช่า VPS แล้วติดตั้ง MT5 บน VPS แทนเครื่องส่วนตัว',
			'inst_step1_img'    => $install_assets . 'step-01.jpg',
			'inst_step2_title'  => 'เปิดโฟลเดอร์ Experts แล้วนำไฟล์ EA เข้า',
			'inst_step2_desc'   => 'ใน MT5 ไปที่เมนู File → Open Data Folder → MQL5 → Experts จากนั้นวางไฟล์ FENIX PRO EA ลงในโฟลเดอร์นี้ แล้วปิด-เปิด MT5 หรือกด Refresh',
			'inst_step2_img'    => $install_assets . 'step-02.jpg',
			'inst_step3_title'  => 'ลาก EA ขึ้นกราฟและตั้งค่า',
			'inst_step3_desc'   => 'เปิดกราฟคู่เงินที่ต้องการ แล้วลาก FENIX PRO EA จากหน้าต่าง Navigator ขึ้นกราฟ ตั้งค่าพารามิเตอร์ตามคำแนะนำ เช่น Lot และระดับความเสี่ยงให้เหมาะกับทุน',
			'inst_step3_img'    => $install_assets . 'step-03.jpg',
			'inst_step4_title'  => 'เปิด AutoTrading',
			'inst_step4_desc'   => 'กดปุ่ม AutoTrading (Algo Trading) ด้านบนให้เป็นสีเขียว และตรวจสอบว่ามีไอคอนหน้ายิ้มมุมขวาบนของกราฟ แสดงว่า EA พร้อมทำงาน',
			'inst_step4_img'    => $install_assets . 'step-04.jpg',
			'inst_step5_title'  => 'ตรวจสอบการทำงานผ่าน Dashboard',
			'inst_step5_desc'   => 'สังเกตสถานะระบบบนกราฟและแท็บ Experts/Journal ว่าทำงานปกติ ติดตามผลและเงื่อนไขการเทรดได้จาก Dashboard ของระบบ',
			'inst_step5_img'    => $install_assets . 'step-05.jpg',
			'inst_step6_title'  => 'ปรับความเสี่ยงให้เหมาะกับตัวเอง',
			'inst_step6_desc'   => 'ทบทวนการตั้งค่าความเสี่ยงเป็นระยะ ใช้เงินเย็น และปรับ Lot ให้สอดคล้องกับทุน เพื่อให้ Drawdown อยู่ในระดับที่รับได้',
			'inst_step6_img'    => $install_assets . 'step-06.jpg',
			'install_note'      => 'ต้องการให้ทีมงานช่วยติดตั้งให้? ทักมาทาง LINE ได้เลย',

			/* หน้า Pricing (เพิ่มเติม) */
			'pricing_sub'   => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ',
			'compare_title' => 'ตารางเปรียบเทียบแพ็กเกจ',
			'compare_rows'  => "รายการ | Starter | Pro | VIP\nจำนวนบัญชีที่ใช้ได้ | 1 | 1-2 | ตามตกลง\nไฟล์ Preset ตั้งค่าพร้อมใช้ | ✗ | ✓ | ✓\nทีมช่วยติดตั้ง / VPS | ✗ | ✗ | ✓\nอัปเดตระบบ | ✓ | ✓ | ✓\nระดับการ Support | พื้นฐาน | ส่วนตัว | ใกล้ชิด",

			/* หน้า Risk Disclosure */
			'riskpage_sub'     => 'ข้อมูลความเสี่ยงที่ควรอ่านก่อนเริ่มใช้งาน',
			'riskpage_intro'   => 'โปรดอ่านและทำความเข้าใจข้อมูลความเสี่ยงต่อไปนี้อย่างละเอียดก่อนตัดสินใจใช้งาน FENIX PRO EA หรือทำการเทรดใด ๆ เนื้อหานี้จัดทำขึ้นเพื่อให้ผู้ใช้งานเห็นข้อจำกัดของระบบ ความเสี่ยงของตลาด และความรับผิดชอบของผู้ใช้งานอย่างชัดเจน',
			'riskpage_image'   => $install_assets . 'step-06.jpg',
			'riskpage_image_caption' => 'ตัวอย่างแนวทางตั้งค่าความเสี่ยงและ Lot Size ให้เหมาะสมกับทุน',
			'rp_block1_title'  => 'ความเสี่ยงของการเทรด',
			'rp_block1_text'   => 'การเทรด Forex, ทองคำ, CFD และสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูงต่อเงินทุนของคุณ ราคาอาจเคลื่อนไหวผันผวนรุนแรงในระยะเวลาสั้น โดยเฉพาะช่วงข่าวสำคัญ สภาพคล่องต่ำ หรือภาวะตลาดผิดปกติ คุณอาจสูญเสียเงินลงทุนบางส่วนหรือทั้งหมด จึงควรใช้เฉพาะเงินเย็นที่พร้อมรับความเสี่ยงได้เท่านั้น',
			'rp_block2_title'  => 'ไม่มีการรับประกันผลกำไร',
			'rp_block2_text'   => 'FENIX PRO EA เป็นเครื่องมือช่วยเทรดที่ทำงานตามเงื่อนไขที่กำหนดไว้ ไม่ใช่ระบบที่รับประกันผลกำไร ไม่ใช่สัญญาว่าจะทำกำไรได้ทุกวัน และไม่สามารถป้องกันการขาดทุนได้ในทุกสภาวะตลาด ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง Spread, Commission, Slippage และเงื่อนไขของโบรกเกอร์ที่ใช้งาน',
			'rp_block3_title'  => 'ผลในอดีตไม่ได้บ่งบอกอนาคต',
			'rp_block3_text'   => 'ผลการทดสอบย้อนหลัง (Backtest) และผลการทดสอบบนบัญชีจริงหรือบัญชีเดโม่ (Forward Test) สะท้อนเฉพาะช่วงเวลาที่ทดสอบและเงื่อนไขที่ใช้ทดสอบเท่านั้น ไม่ได้รับประกันว่าผลในอนาคตจะเป็นไปในทิศทางเดียวกัน ตลาดสามารถเปลี่ยนแปลงได้ตลอดเวลา และระบบที่เคยทำงานได้ดีในอดีตอาจให้ผลลัพธ์แตกต่างออกไปในอนาคต',
			'rp_block4_title'  => 'ความรับผิดชอบของผู้ใช้งาน',
			'rp_block4_text'   => 'ผู้ใช้งานเป็นผู้รับผิดชอบการตัดสินใจเทรดและการตั้งค่าระบบด้วยตนเอง รวมถึงการเลือกบัญชี การเลือกโบรกเกอร์ การกำหนด Lot Size ระดับความเสี่ยง การเปิดปิดระบบ และการดูแลบัญชีของตนเอง ก่อนใช้งานจริงควรทดลอง ตั้งค่าด้วยความระมัดระวัง และตรวจสอบว่าเข้าใจผลกระทบของแต่ละพารามิเตอร์แล้ว',
			'rp_block5_title'  => 'ไม่ใช่คำแนะนำการลงทุน',
			'rp_block5_text'   => 'ข้อมูลทั้งหมดบนเว็บไซต์นี้จัดทำขึ้นเพื่อให้ข้อมูลเกี่ยวกับเครื่องมือและวิธีใช้งานเท่านั้น ไม่ถือเป็นคำแนะนำทางการเงิน การลงทุน กฎหมาย หรือภาษี ทีมงานไม่ได้ทำหน้าที่เป็นที่ปรึกษาการลงทุนส่วนบุคคล หากต้องการคำแนะนำเฉพาะบุคคล ควรปรึกษาผู้เชี่ยวชาญที่ได้รับอนุญาตตามกฎหมาย',
			'rp_block6_title'  => 'ความเสี่ยงด้านเทคนิคและการเชื่อมต่อ',
			'rp_block6_text'   => 'การใช้งาน EA จำเป็นต้องพึ่งพาแพลตฟอร์ม MetaTrader 5, อินเทอร์เน็ต, VPS หรือคอมพิวเตอร์ที่เปิดใช้งานระบบ รวมถึงเซิร์ฟเวอร์ของโบรกเกอร์ ปัญหาทางเทคนิค เช่น อินเทอร์เน็ตหลุด VPS ดับ โปรแกรมค้าง คำสั่งส่งไม่สำเร็จ หรือโบรกเกอร์มีข้อจำกัดบางอย่าง อาจส่งผลต่อการทำงานของระบบและผลการเทรด ผู้ใช้งานควรตรวจสอบระบบเป็นระยะและมีแผนรับมือความเสี่ยงเหล่านี้',
			'riskpage_updated' => 'ปรับปรุงล่าสุด: 3 กรกฎาคม 2026',
		)
	);

	/* ===== ส่วนเสริมความเชื่อใจหน้าแรก (ทีมงาน / ความมั่นใจ / Mid CTA / Pricing teaser / ลิงก์ผล verified) ===== */
	$d = array_merge(
		$d,
		array(

			/* ทีมงาน / ใครอยู่เบื้องหลัง (ปิดไว้ก่อน จนกว่าเจ้าของจะกรอกข้อมูลจริง) */
			'show_team'   => true,
			'team_kicker' => 'Who We Are',
			'team_title'  => 'ทีมที่อยู่เบื้องหลัง FENIX PRO EA',
			'team_text'   => "FENIX PRO EA พัฒนาและดูแลโดยทีมงานที่ติดตามตลาดและการเทรดบน MetaTrader 5 เราตั้งใจสร้างเครื่องมือที่ช่วยให้การเทรดเป็นระบบและตรวจสอบได้ พร้อมดูแลผู้ใช้งานผ่าน LINE หลังเริ่มใช้งานจริง",
			'team_points' => "ดูแลผู้ใช้งานผ่าน LINE ภาษาไทย\nให้ความสำคัญกับการบริหารความเสี่ยง\nอัปเดตระบบตามรอบเวอร์ชัน",
			'team_img'    => '',

			/* ความมั่นใจก่อนเริ่มใช้งาน */
			'show_assurance'     => false,
			'assurance_kicker'   => 'Before You Start',
			'assurance_title'    => 'สบายใจก่อนเริ่มใช้งาน',
			'assurance_subtitle' => 'เราอยากให้คุณเข้าใจระบบและมั่นใจก่อนตัดสินใจ ไม่ใช่เร่งให้รีบซื้อ',
			'assurance_items'    => "ทดลองแนวคิดระบบบนบัญชี Demo ก่อนได้\nมีทีมไทยช่วยแนะนำการติดตั้งจนระบบทำงานได้จริง\nสอบถามและปรึกษาทีมก่อนตัดสินใจได้\nอัปเดตระบบตามรอบเวอร์ชันอย่างต่อเนื่อง",

			/* Mid CTA (แถบทัก LINE คั่นกลางหน้า) */
			'show_mid_cta'  => true,
			'mid_cta_title' => 'ยังไม่แน่ใจว่าเหมาะกับทุนของคุณไหม?',
			'mid_cta_text'  => 'ทักมาให้ทีมช่วยประเมินความเหมาะสมก่อนตัดสินใจได้ ไม่มีข้อผูกมัด',

			/* Pricing teaser หน้าแรก (ใช้แพ็กเกจร่วมกับหมวด 10) */
			'show_pricing_home'  => true,
			'pricing_home_title' => 'แพ็กเกจการใช้งาน',
			'pricing_home_sub'   => 'เลือกระดับการดูแลที่เหมาะกับคุณ ดูรายละเอียดทั้งหมดได้ที่หน้าแพ็กเกจ',

			/* ปุ่มลิงก์ผลที่ตรวจสอบได้ (เช่น Myfxbook) บนหน้าแรก */
			'verified_link_label' => 'ดูผลแบบเรียลไทม์',
			'verified_link_url'   => 'https://www.myfxbook.com/portfolio/eafn/12227282',

			/* บทความล่าสุดบนหน้าแรก (ซ่อนอัตโนมัติเมื่อยังไม่มีบทความที่เผยแพร่) */
			'show_blog'      => true,
			'blog_kicker'    => 'Articles',
			'blog_title'     => 'บทความและความรู้',
			'blog_subtitle'  => 'รวมบทความเกี่ยวกับ EA การเทรดอัตโนมัติ และการบริหารความเสี่ยงบน MetaTrader 5',
			'blog_all_label' => 'ดูบทความทั้งหมด',

			/* ===== หน้าแรกดีไซน์ใหม่ (อนุมัติ 16 พ.ย. 2026) ===== */

			/* 01 · Hero */
			'hero_pill_tag'     => 'NEW',
			'hero_pill_prefix'  => 'เวอร์ชันล่าสุด',
			'hero_mini1_value'  => '3 ตัว',
			'hero_mini1_label'  => 'EA ในชุด',
			'hero_mini2_value'  => 'MT5',
			'hero_mini2_label'  => 'แพลตฟอร์มเดียว',
			'hero_mini3_value'  => '24 ชม.',
			'hero_mini3_label'  => 'เมื่อรันบน VPS',
			'hero_status_text'  => 'กำลังเฝ้ากราฟ',
			'hero_toast_title'  => 'เงื่อนไขครบ ส่งคำสั่ง',
			'hero_toast_text'   => 'ตามแผนที่ตั้งไว้ ไม่ลังเล',
			'hero_proof_top'    => 'ผลทดสอบย้อนหลัง 9 เดือน · PLUS 5.1',
			'hero_proof_value'  => '+513.3%',
			'hero_proof_bottom' => 'ทุน 10,000 USD · DRAWDOWN สูงสุด 46.49%',

			'features_kicker' => 'Key features',
			'pricing_kicker'  => 'Pricing',
			'explore_kicker'  => 'Explore',
			'blog_read_more'  => 'อ่านต่อ',

			/* 02 · EA ในชุด */
			'show_products'     => true,
			'products_kicker'   => 'Products',
			'products_title'    => 'EA ในชุด FENIX PRO',
			'products_sub'      => 'ทุกตัวทำงานบน MetaTrader 5 และตั้งค่าความเสี่ยงเองได้ทุกตัว ดูผลทดสอบของแต่ละเวอร์ชันก่อนเลือกใช้',
			'prod_badge_latest' => 'ล่าสุด',
			'prod_badge_other'  => 'เวอร์ชัน',
			'prod_plus_spec1'   => 'สไตล์ | Balanced Grid',
			'prod_plus_spec2'   => 'ผลทดสอบ | ทองคำ XAUUSD',
			'prod_fast_spec1'   => 'สไตล์ | Two-way Grid',
			'prod_fast_spec2'   => 'ตั้งค่า | เลือกคูณ Lot ได้',
			'prod_card3_spec1'  => 'สไตล์ | Bitcoin Grid',
			'prod_card3_spec2'  => 'ตลาด | Bitcoin',

			/* 03 · EA คืออะไร */
			'about_kicker'      => 'About',
			'about_bold'        => 'เลือกแผนและกำหนดว่ารับความเสี่ยงได้แค่ไหน',
			'about_chart_pair'  => 'XAUUSD',
			'about_chart_note'  => 'M15 · ภาพประกอบ',
			'about_chart_run'   => 'EA กำลังทำงาน',

			/* 04 · ปัญหา */
			'pain_kicker'    => 'The problem',
			'pain_day_title' => '24 ชั่วโมงของคุณ กับ 24 ชั่วโมงของ EA',
			'pain_day_you'   => "7 | หลับ\n2 | เดินทาง\n9 | ทำงาน · ประชุม · ติดธุระ\n2 | กิน[ข้าว]\n+ 2 | ดูจอ[ได้]\n2 | หลับ",
			'pain_day_ea'    => 'เฝ้ากราฟตลอดเวลา [— เมื่อเปิดเครื่องหรือรันบน VPS]',
			'pain_day_note1' => 'เวลาที่คุณดูจอได้จริงในหนึ่งวัน',
			'pain_day_note2' => 'เวลาที่ EA ทำงานตามแผน',
			'pain_chk_title' => 'ติ๊กข้อที่ตรงกับคุณ',
			'pain_chk_zero'  => 'ลองติ๊กข้อที่ตรงกับคุณ',
			'pain_chk_res'   => 'ติ๊กไป {n} จาก {total} ข้อ — ลองดูว่าการให้ระบบทำตามแผนต่างจากการเทรดเองตรงไหน',
			'pain_chk_btn'   => 'เทียบให้ดู',

			/* 05 · เทรดเอง vs ใช้ EA */
			'show_vs'         => true,
			'vs_kicker'       => 'Side by side',
			'vs_title'        => 'เทรดเอง vs ให้ FENIX PRO EA ทำตามแผน',
			'vs_sub'          => 'เทียบกันตรง ๆ ทีละเรื่อง รวมถึงเรื่องที่ EA เปลี่ยนให้ไม่ได้',
			'vs_left_label'   => 'เทรดเอง',
			'vs_right_label'  => 'ใช้ FENIX PRO EA',
			'vs_rows'         => "การเข้าออเดอร์ | กลัวตกรถ รีบเข้า รีบออก | เข้าและออกตามเงื่อนไขที่กำหนดไว้ล่วงหน้า\nเวลาเฝ้าจอ | ต้องนั่งดูเอง พลาดจังหวะตอนติดธุระ | ระบบเฝ้ากราฟให้ เมื่อเปิดเครื่องหรือใช้ VPS\nขนาดไม้ | บางครั้งเทรดใหญ่เกินไป | เลือกตัวคูณ Lot ได้ตามทุนที่รับได้\nวินัย | พอกราฟวิ่งจริงกลับเปลี่ยนใจกลางทาง | ทำตามแผนอย่างเคร่งครัด ไม่ต่อรองกับตัวเอง\nการติดตามผล | ต้องเปิด MT5 ไล่ดูเอง | ดูสถานะ กำไร/ขาดทุน ผ่าน Dashboard หน้าเดียว",
			'vs_risk_row'     => 'ความเสี่ยงของตลาด | มีอยู่เสมอ | ยังมีอยู่เหมือนเดิม — EA ไม่ได้ทำให้ความเสี่ยงหายไป',

			/* 06 · ขั้นตอน */
			'steps_hl'       => '3',
			'step1_pic'      => 'ติดตั้งเสร็จ · LOGIN แล้ว',
			'step2_pic'      => 'FENIX-PLUS.ex5',
			'step2_pic2'     => 'EA ติดบนกราฟแล้ว',
			'step3_pic'      => 'รันด้วยเงินสมมติก่อน',
			'step4_pic'      => 'ขนาดไม้: เริ่มจากเล็กสุด',

			/* 07 · จุดเด่น */
			'feat1_log'      => "02:14 | เงื่อนไขครบ | ส่งคำสั่งตามแผน\n09:41 | ถึงจุดปิดตามกฎ | ปิดไม้\n21:05 | เงื่อนไขยังไม่ครบ | รอต่อ",
			'feat4_chips'    => "Lot\nStop Loss\nDrawdown",
			'feat5_ring'     => '24 | ชั่วโมง · VPS',
			'feat6_lots'     => 'เล็กสุด | ตัวคูณ Lot | ใหญ่',

			/* 08 · Dashboard */
			'show_dash'   => true,
			'dash_kicker' => 'Dashboard',
			'dash_title'  => 'ดูทุกอย่างได้ในหน้าจอเดียว',
			'dash_sub'    => 'หลังติดตั้ง คุณติดตามบัญชี MT5 ทุกบัญชีผ่าน Dashboard ได้ทั้งบนคอมและมือถือ ไม่ต้องเปิด MT5 ไล่ดูเอง',
			'dash_host'   => 'dashboard.fenixpro-th.com',
			'dash_menu'   => "ภาพรวม\nบัญชี MT5\nเลือก EA\nปฏิทินผลเทรด\nออเดอร์\nประวัติและผลการเทรด\nปฏิทินข่าว\nการแจ้งเตือน\nตั้งค่า",
			'dash_items'  => "wallet | ยอดเงินและกำไร/ขาดทุน | รายวัน สัปดาห์ เดือน ของทุกบัญชีรวมกัน\ngauge | Drawdown เทียบเพดาน | เห็นทันทีว่าใกล้เพดานความเสี่ยงที่ตั้งไว้หรือยัง\nactivity | สถานะ EA แบบสด | รู้ว่า EA กำลังรันอยู่จริง และเชื่อมต่อ MT5 ปกติ\nshield | ตัวกรองข่าวแรง | ดูปฏิทินข่าวและสถานะ News Guard ในที่เดียว\nbell | การแจ้งเตือน + ปฏิทินผลเทรด | ย้อนดูผลทีละวัน และรับแจ้งเตือนเมื่อมีเหตุการณ์สำคัญ",
			'dash_news'   => 'ตัวกรองข่าวแรง (News Guard) | EA งดเปิดรอบใหม่ช่วงข่าวแรงตามปฏิทินข่าว',
			'dash_note'   => 'ตัวเลขในภาพเป็นตัวอย่างประกอบ ไม่ใช่ผลของบัญชีจริง',
			'dash_image'  => '',

			/* 09 · เหมาะกับใคร */
			'fit_kicker'   => 'Honest check',
			'fit_stamp'    => 'เหมาะ',
			'fit_ask_text' => 'ไม่แน่ใจว่าตัวเองอยู่กลุ่มไหน? ทัก LINE ให้ทีมช่วยประเมินก่อนตัดสินใจได้',
			'fit_ask_btn'  => 'ทัก LINE',

			/* 10 · ผลการทดสอบ */
			'ev_tab1'      => 'Backtest',
			'ev_tab2'      => 'Forward Test · เรียลไทม์',
			'ev_chips'     => "FENIX PLUS 5.1\nโหมด FENIX STANDARD\nทองคำ XAUUSD.c\nย้อนหลัง 9 เดือน",
			'ev_big'       => '+513.3%',
			'ev_sub'       => 'กำไรสุทธิ {b} เทียบเงินตั้งต้น 10,000 USD · 1 ม.ค. – 30 ก.ย. 2569',
			'ev_sub_bold'  => '+51,333.78',
			'ev_kpis'      => "เงินตั้งต้น | 10,000 | ทุนคิดไม้ 10,000\nProfit Factor | 2.30 | เดือนที่กำไร 9 / 9\nจำนวนไม้ | 2,028 | ยอดเงินสุดท้าย 61,334",
			'ev_risk_kpis' => "ติดลบลอยตัวสูงสุด (เป็นเงิน) | 9,507.42 | = 95% ของเงินตั้งต้น\nDrawdown สูงสุดจากยอดสูงสุด | 46.49% | ครั้งที่เงินในบัญชีลดจากยอดสูงสุดมากที่สุด",
			'ev_bars_title' => 'กำไรรายเดือน',
			'ev_bars_unit'  => '(% ของเงินตั้งต้น 10,000)',
			'ev_bars_note'  => 'พื้นเทา = เดือนที่ราคาเป็นแบบจำลองเกินครึ่งเดือน',
			'ev_bars'       => "ม.ค. | 42 | sim\nก.พ. | 49 | sim\nมี.ค. | 67 | sim\nเม.ย. | 52 | sim\nพ.ค. | 53 | sim\nมิ.ย. | 66\nก.ค. | 55\nส.ค. | 66\nก.ย. | 65",
			'ev_stamp'      => 'Verify yourself',
			'ev_fw_text'    => 'แท็บนี้ลิงก์ออกไปดูบัญชีจริงแบบเรียลไทม์บน Myfxbook ตรวจตรา Verified และเทียบค่า Gain กับ Absolute Gain ได้เองตามขั้นตอนด้านขวา',

			/* 11 · เสียงจากผู้ใช้งาน (เปิดเมื่อมีภาพแชตจริงที่ได้รับอนุญาต) */
			'show_rv'   => false,
			'rv_kicker' => 'From users',
			'rv_sub'    => 'ภาพแชตจากกลุ่มผู้ใช้งาน FENIX PRO EA ทาง LINE',
			'rv_group'  => 'กลุ่มผู้ใช้ FENIX PRO',
			'rv_img1'   => '',
			'rv_img2'   => '',
			'rv_img3'   => '',
			'rv_note'   => 'ใช้เฉพาะภาพแชตจริงที่ได้รับอนุญาตจากผู้ใช้งาน ผลของแต่ละคนขึ้นอยู่กับทุน การตั้งค่า และสภาวะตลาด',

			/* 12 · ใช้กับอะไรได้บ้าง */
			'dev1_items' => "+ รัน EA ได้เต็มรูปแบบ\n! ต้องเปิดเครื่องทิ้งไว้",
			'dev2_items' => "+ รัน EA ได้ตลอด 24 ชั่วโมง\n+ ไม่ต้องเปิดคอมเอง",
			'dev3_items' => "+ ดูสถานะพอร์ต / ปิดไม้เองได้\n- รัน EA ไม่ได้",

			/* 13 · ทีมงาน */
			'team_btn1'         => 'ทัก LINE ปรึกษาก่อนตัดสินใจ',
			'team_btn2'         => 'คัดลอกข้อความตัวอย่าง',
			'team_chat_head'    => 'FENIX PRO EA',
			'team_chat_sub'     => 'LINE Official Account',
			'team_sample_title' => 'ขอประเมินความเหมาะสมครับ',
			'team_sample_lines' => "ทุนที่ต้องการใช้: ____ USD\nโบรกและประเภทบัญชี MT5: ____\nเป้าหมาย: ติดตั้ง / สอบถามราคา / ตรวจความเหมาะสม\nเวลาที่สะดวกให้ติดต่อกลับ: ____",
			'team_copy_cap'     => 'ข้อความตัวอย่างสำหรับคัดลอกไปทัก',
			'team_copy_done'    => 'คัดลอกแล้ว',

			/* 14 · แพ็กเกจ */
			'pkg_featured_label' => 'แนะนำ',
			'pricing_more'       => 'ดูรายละเอียดแพ็กเกจทั้งหมด',

			/* 16 · คำถามที่พบบ่อย */
			'faq_kicker'    => 'FAQ',
			'faq_search_ph' => 'ค้นหาคำถาม เช่น ทุนเริ่มต้น, VPS',
			'faq_empty'     => 'ไม่พบคำถามที่ตรงกับคำค้นนี้ ทัก LINE ถามทีมงานได้เลย',
			'faq_ask_title' => 'ไม่เจอคำถามของคุณ?',
			'faq_ask_text'  => 'ทัก LINE ถามทีมงานได้โดยตรง',

			/* 18 · CTA ปิดท้าย + คำเตือน */
			'cta_btn2_text'   => 'ไปหน้าดาวน์โหลด EA',
			'cta_watermark'   => 'FENIX PRO',
			'risk_link_label' => 'อ่านคำเตือนความเสี่ยงฉบับเต็ม',
		)
	);

	return $d;
}

/**
 * ดึงเลขเวอร์ชันจากชื่อไฟล์ดาวน์โหลด เช่น FENIX_PLUS_V5.1.zip จะได้ 5.1
 * ใช้เป็นค่าสำรองเมื่อยังไม่ได้กรอกเลขเวอร์ชันเองใน Customizer
 */
function fenix_version_from_url( $url ) {
	$path = (string) parse_url( (string) $url, PHP_URL_PATH );
	$name = basename( $path );

	if ( preg_match( '/v\.?\s*(\d+(?:\.\d+)*)/i', $name, $match ) ) {
		return $match[1];
	}

	return '';
}

/**
 * การ์ดดาวน์โหลดในขั้นตอน "ดาวน์โหลด EA" เรียงตามเลขเวอร์ชันจากน้อยไปมาก
 * การ์ดที่ไม่มีเลขเวอร์ชันจะไปอยู่ท้ายสุดตามลำดับที่ตั้งไว้
 */
function fenix_download_cards() {
	$slots = array(
		'fast'  => 'lh-feature-fast',
		'plus'  => 'lh-feature-plus',
		'card3' => 'lh-feature-card3',
	);

	$cards = array();

	foreach ( $slots as $slot => $class ) {
		if ( ! fenix_mod( 'links_' . $slot . '_enabled' ) ) {
			continue;
		}

		if ( 'fast' === $slot && ! fenix_mod( 'links_fast_show' ) ) {
			continue;
		}

		$img = trim( (string) fenix_mod( 'links_' . $slot . '_img' ) );
		$url = trim( (string) fenix_mod( 'links_' . $slot . '_url' ) );

		if ( '' === $img || '' === $url ) {
			continue;
		}

		$override = trim( (string) fenix_mod( 'links_' . $slot . '_version' ) );
		$number   = '' !== $override ? $override : fenix_version_from_url( $url );

		$cards[] = array(
			'slot'     => $slot,
			'class'    => $class,
			'img'      => $img,
			'url'      => $url,
			'alt'      => fenix_mod( 'links_' . $slot . '_alt' ),
			'version'  => $override,
			'sort_key' => preg_match( '/^\d/', $number ) ? $number : '',
		);
	}

	usort(
		$cards,
		function ( $a, $b ) {
			if ( '' === $a['sort_key'] || '' === $b['sort_key'] ) {
				return ( '' === $a['sort_key'] ? 1 : 0 ) - ( '' === $b['sort_key'] ? 1 : 0 );
			}

			return version_compare( $a['sort_key'], $b['sort_key'] );
		}
	);

	return $cards;
}

/**
 * เมนูลัดด้านล่างบนมือถือ (บาร์ 5 ปุ่ม)
 * ปุ่มที่ 4 ชี้ไปแดชบอร์ดลูกค้า ถ้าไม่ได้กรอกลิงก์เฉพาะจะใช้ลิงก์แดชบอร์ดหลัก
 */
function fenix_mobile_nav_items() {
	$dash_url = trim( (string) fenix_mod( 'mobile_nav_dash_url' ) );

	if ( '' === $dash_url ) {
		$dash_url = trim( (string) fenix_mod( 'dashboard_url' ) );
	}

	return array(
		array(
			'label' => fenix_mod( 'mobile_nav_home_label' ),
			'url'   => fenix_link_url( fenix_mod( 'mobile_nav_home_url' ) ),
			'icon'  => 'home',
		),
		array(
			'label' => fenix_mod( 'mobile_nav_test_label' ),
			'url'   => fenix_link_url( fenix_mod( 'mobile_nav_test_url' ) ),
			'icon'  => 'chart',
		),
		array(
			'label' => fenix_mod( 'mobile_nav_price_label' ),
			'url'   => fenix_link_url( fenix_mod( 'mobile_nav_price_url' ) ),
			'icon'  => 'tag',
		),
		array(
			'label' => fenix_mod( 'mobile_nav_dash_label' ),
			'url'   => fenix_link_url( $dash_url ),
			'icon'  => 'gauge',
		),
		array(
			'label'        => fenix_mod( 'mobile_nav_line_label' ),
			'url'          => fenix_mod( 'line_url' ),
			'icon'         => 'line',
			'is_action'    => true,
			'target_blank' => true,
		),
	);
}

/**
 * ป้ายเลขเวอร์ชันใต้การ์ดดาวน์โหลด ใช้ร่วมกันทุกการ์ด
 * ใช้เลขที่กรอกใน Customizer ก่อน ถ้าเว้นว่างจะดึงจากชื่อไฟล์ให้เอง
 */
/**
 * มุมที่วางปุ่มดาวน์โหลดบนรูป · รับเฉพาะค่าที่ธีมรู้จัก.
 *
 * @param string $value ค่าที่กรอกมา.
 * @return string
 */
/**
 * ชื่อสินค้ากับเลขเวอร์ชันจากชื่อไฟล์ เช่น FENIX_PLUS_V5.1.zip -> FENIX PLUS / 5.1
 * ใช้ชื่อไฟล์เป็นแหล่งข้อมูล เพื่อให้เปลี่ยนไฟล์ EA แล้วหน้าแรกเปลี่ยนตามเอง
 */
/**
 * รูปประกอบของหัวข้อบนหน้าแรก · ไม่ใส่รูปก็ไม่แสดงอะไรเลย
 */
function fenix_section_figure( $img_key, $alt_key = '', $class = 'sec-figure' ) {
	$img = trim( (string) fenix_mod( $img_key ) );

	if ( '' === $img ) {
		return '';
	}

	$alt = '' !== $alt_key ? (string) fenix_mod( $alt_key ) : '';

	return '<figure class="' . esc_attr( $class ) . ' reveal">'
		. '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async">'
		. '</figure>';
}
/* --------------------------------------------------------------
 * หน้าคู่มือ (template-guide.php)
 * -------------------------------------------------------------- */

/**
 * ลำดับคู่มือทั้งชุด · เรียงตามที่ผู้ใช้งานต้องทำจริง
 * ใช้คุมทั้งเลขขั้น แถบบอกตำแหน่ง และปุ่มขั้นก่อนหน้า/ถัดไป
 */
/**
 * หน้าคู่มือ: เอาหัวเรื่องเดิมที่อยู่ในเนื้อหาออก เพราะเทมเพลตพิมพ์ชื่อหน้าให้แล้ว
 * ถ้าปล่อยไว้ หน้าเดียวจะมี h1 สองอัน และผู้อ่านเห็นชื่อเรื่องซ้ำสองรอบ
 */
function fenix_guide_drop_inner_title( $content ) {
	if ( ! is_page_template( 'template-guide.php' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	return fenix_strip_first_h1( $content );
}
add_filter( 'the_content', 'fenix_guide_drop_inner_title', 20 );

/**
 * ตัดแท็ก h1 ตัวแรกออกจากข้อความ (แยกไว้ต่างหากเพื่อให้เทสต์ได้)
 */
function fenix_strip_first_h1( $html ) {
	$out = preg_replace( '#<h1\b[^>]*>.*?</h1>\s*#su', '', (string) $html, 1 );

	return null === $out ? $html : $out;
}

function fenix_guides() {
	return array(
		'open-mt5-account' => array(
			'label' => 'เปิดบัญชี MT5',
			'short' => 'เปิดบัญชี',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'อีเมลและเบอร์โทรที่ใช้งานได้จริง',
				'บัตรประชาชนสำหรับยืนยันตัวตน',
			),
		),
		'mt5-login-zaurix-server' => array(
			'label' => 'ติดตั้ง MT5 และ LOGIN',
			'short' => 'ติดตั้ง MT5',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'Login และ Password ของบัญชีเทรดจากอีเมล',
				'ชื่อเซิร์ฟเวอร์ Zaurix-Server',
			),
		),
		'deposit-zaurix' => array(
			'label' => 'ฝากเงินเข้าบัญชี',
			'short' => 'ฝากเงิน',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'บัญชีธนาคารที่เป็นชื่อเดียวกับบัญชีเทรด',
				'เข้า Portal ของโบรกเกอร์ได้แล้ว',
			),
		),
		'fenix-pro-ea-install-guide' => array(
			'label' => 'ติดตั้ง EA บน MT5',
			'short' => 'ติดตั้ง EA',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'ไฟล์ EA ที่โหลดมาจากหน้าดาวน์โหลด',
				'MT5 ที่ติดตั้งและล็อกอินเรียบร้อยแล้ว',
			),
		),
		'fenix-dual-presets' => array(
			'label' => 'เลือกคูณ LOT ของ FENIX DUAL',
			'short' => 'คูณ LOT',
			'time'  => '5 นาที',
			'level' => 'ควรอ่านก่อนเปิดจริง',
			'prep'  => array(
				'รู้ว่าทุนในบัญชีมีเท่าไร',
				'ติดตั้ง FENIX DUAL บน MT5 แล้ว',
			),
		),
		'vps-desktop-windows' => array(
			'label' => 'VPS บน WINDOWS',
			'short' => 'VPS WINDOWS',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'IP Address, Username และ Password ของ VPS ที่ทีมงานส่งให้',
				'เครื่องคอมพิวเตอร์ที่ใช้ Windows',
			),
		),
		'vps-android' => array(
			'label' => 'VPS บน ANDROID',
			'short' => 'VPS ANDROID',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'IP Address, Username และ Password ของ VPS ที่ทีมงานส่งให้',
				'มือถือ Android ที่ต่ออินเทอร์เน็ตได้',
			),
		),
		'vps-iphone-ios' => array(
			'label' => 'VPS บน IPHONE',
			'short' => 'VPS IPHONE',
			'time'  => '10 นาที',
			'level' => 'มือใหม่ทำเองได้',
			'prep'  => array(
				'IP Address, Username และ Password ของ VPS ที่ทีมงานส่งให้',
				'iPhone หรือ iPad ที่ต่ออินเทอร์เน็ตได้',
			),
		),
	);
}

/**
 * ข้อมูลของคู่มือหน้าหนึ่ง พร้อมเลขขั้นและหน้าก่อนหน้า/ถัดไป
 */
function fenix_guide_meta( $slug ) {
	$guides = fenix_guides();
	$slugs  = array_keys( $guides );
	$pos    = array_search( $slug, $slugs, true );

	$meta = array(
		'slug'  => (string) $slug,
		'index' => 0,
		'total' => count( $slugs ),
		'time'  => '',
		'level' => '',
		'prep'  => array(),
		'prev'  => null,
		'next'  => null,
	);

	if ( false === $pos ) {
		return $meta;
	}

	$meta['index'] = $pos + 1;
	$meta['time']  = $guides[ $slug ]['time'];
	$meta['level'] = $guides[ $slug ]['level'];
	$meta['prep']  = $guides[ $slug ]['prep'];

	if ( $pos > 0 ) {
		$prev         = $slugs[ $pos - 1 ];
		$meta['prev'] = array( 'slug' => $prev, 'label' => $guides[ $prev ]['label'] );
	}

	if ( $pos < count( $slugs ) - 1 ) {
		$next         = $slugs[ $pos + 1 ];
		$meta['next'] = array( 'slug' => $next, 'label' => $guides[ $next ]['label'] );
	}

	return $meta;
}

/**
 * แถบบอกตำแหน่งด้านบนของหน้าคู่มือ
 * ขั้นที่ผ่านมาแล้วติดเครื่องหมายถูก ขั้นปัจจุบันเป็นสีส้ม
 */
function fenix_guide_rail( $current ) {
	$guides = fenix_guides();
	$pos    = array_search( $current, array_keys( $guides ), true );
	$out    = '<ol class="guide-rail" aria-label="ลำดับขั้นตอนทั้งหมด">';
	$i      = 0;

	foreach ( $guides as $slug => $guide ) {
		$i++;
		$class = '';
		$mark  = (string) $i;

		if ( false !== $pos && $i - 1 < $pos ) {
			$class = ' class="is-done"';
			$mark  = '&#10003;';
		} elseif ( $slug === $current ) {
			$class = ' class="is-now"';
		}

		$out .= '<li' . $class . '><a href="' . esc_url( home_url( '/' . $slug . '/' ) ) . '">'
			. '<b>' . $mark . '</b> ' . esc_html( $guide['short'] ) . '</a></li>';
	}

	return $out . '</ol>';
}
function fenix_product_from_url( $url ) {
	$name = basename( (string) parse_url( (string) $url, PHP_URL_PATH ) );
	$out  = array(
		'name'    => '',
		'version' => fenix_version_from_url( $url ),
	);

	if ( preg_match( '/^([A-Za-z0-9]+(?:_[A-Za-z0-9]+)*?)_v\.?\s*\d/i', $name, $match ) ) {
		$out['name'] = strtoupper( str_replace( '_', ' ', $match[1] ) );
	}

	return $out;
}

/**
 * การ์ดในสำรับหน้าแรก · ใช้รูปและลิงก์ชุดเดียวกับหน้าดาวน์โหลด
 * เปลี่ยนไฟล์ EA ที่หน้า /go/ แล้วหน้าแรกเปลี่ยนตามทันที ไม่ต้องตั้งค่าซ้ำ
 */
function fenix_hero_deck_cards() {
	$slots    = array( 'fast', 'plus', 'card3' );
	$featured = (string) fenix_mod( 'hero_deck_featured' );

	if ( in_array( $featured, $slots, true ) ) {
		$slots = array_merge( array( $featured ), array_values( array_diff( $slots, array( $featured ) ) ) );
	}

	$cards = array();

	foreach ( $slots as $slot ) {
		if ( ! fenix_mod( 'links_' . $slot . '_enabled' ) ) {
			continue;
		}
		if ( 'fast' === $slot && ! fenix_mod( 'links_fast_show' ) ) {
			continue;
		}

		$img = trim( (string) fenix_mod( 'links_' . $slot . '_img' ) );
		if ( '' === $img ) {
			continue;
		}

		$url     = trim( (string) fenix_mod( 'links_' . $slot . '_url' ) );
		$product = fenix_product_from_url( $url );
		$version = trim( (string) fenix_mod( 'links_' . $slot . '_version' ) );

		$cards[] = array(
			'slot'    => $slot,
			'img'     => $img,
			'file'    => $url,
			'alt'     => trim( (string) fenix_mod( 'links_' . $slot . '_alt' ) ),
			'name'    => $product['name'],
			'version' => '' !== $version ? $version : $product['version'],
		);
	}

	return $cards;
}

function fenix_sanitize_hero_visual( $value ) {
	return in_array( $value, array( 'deck', 'orb' ), true ) ? $value : 'deck';
}

function fenix_sanitize_deck_slot( $value ) {
	return in_array( $value, array( 'fast', 'plus', 'card3' ), true ) ? $value : 'plus';
}

function fenix_sanitize_deck_cta_mode( $value ) {
	return in_array( $value, array( 'page', 'file' ), true ) ? $value : 'page';
}
function fenix_sanitize_download_pos( $value ) {
	$allowed = array( 'bl', 'br', 'bc' );

	return in_array( $value, $allowed, true ) ? $value : 'bl';
}

/**
 * ที่วางป้ายเวอร์ชัน · บนรูปมุมขวาล่าง มุมซ้ายล่าง หรือใต้รูปแบบเดิม.
 *
 * @param string $value ค่าที่กรอกมา.
 * @return string
 */
function fenix_sanitize_version_pos( $value ) {
	$allowed = array( 'br', 'bl', 'under' );

	return in_array( $value, $allowed, true ) ? $value : 'br';
}

function fenix_download_version_badge( $url, $override, $pos = 'under' ) {
	$version = trim( (string) $override );

	if ( '' === $version ) {
		$version = fenix_version_from_url( $url );
	}

	if ( '' === $version ) {
		return '';
	}

	if ( preg_match( '/^\d/', $version ) ) {
		$version = 'V' . $version;
	}

	$label = trim( (string) fenix_mod( 'links_plus_version_label' ) );
	$class = 'lh-feature-ver';

	if ( in_array( $pos, array( 'br', 'bl' ), true ) ) {
		$class .= ' lh-feature-ver--on lh-feature-ver--' . $pos;
	}

	$html  = '<span class="' . esc_attr( $class ) . '">';

	if ( '' !== $label ) {
		$html .= '<span class="lh-ver-label">' . esc_html( $label ) . '</span>';
	}

	return $html . '<span class="lh-ver-num">' . esc_html( $version ) . '</span></span>';
}

/**
 * อ่านค่า theme mod พร้อม fallback เป็นค่าเริ่มต้น
 */
/* --------------------------------------------------------------
 * ตัวช่วยของหน้าแรก (ดีไซน์ที่อนุมัติ 16 พ.ย. 2026)
 * -------------------------------------------------------------- */

/**
 * แตกข้อมูลตารางจาก textarea ที่คั่นด้วย | บรรทัดละ 1 แถว
 * บรรทัดที่เริ่มด้วย ! คือแถวเตือน (คืนค่า flag = '!')
 */
function fenix_rows( $text, $cols = 0 ) {
	$rows = array();

	foreach ( fenix_lines( $text ) as $line ) {
		$flag = '';
		$line = trim( $line );

		if ( '' !== $line && in_array( $line[0], array( '!', '+', '-' ), true ) ) {
			$flag = $line[0];
			$line = trim( substr( $line, 1 ) );
		}

		$cells = array_map( 'trim', explode( '|', $line ) );

		if ( $cols > 0 ) {
			$cells = array_pad( array_slice( $cells, 0, $cols ), $cols, '' );
		}

		$rows[] = array(
			'flag'  => $flag,
			'cells' => $cells,
		);
	}

	return $rows;
}

/**
 * ข้อความที่ซ่อนบางคำบนมือถือ เขียนในวงเล็บเหลี่ยม เช่น กิน[ข้าว]
 * คืนค่า HTML ที่ escape แล้ว (ส่วนที่อยู่ในวงเล็บห่อด้วย span.hm)
 */
function fenix_hm( $text ) {
	$parts = preg_split( '/(\[[^\]]*\])/u', (string) $text, -1, PREG_SPLIT_DELIM_CAPTURE );
	$out   = '';

	foreach ( (array) $parts as $part ) {
		if ( '' === $part ) {
			continue;
		}
		if ( '[' === $part[0] ) {
			$out .= '<span class="hm">' . esc_html( trim( $part, '[]' ) ) . '</span>';
			continue;
		}
		$out .= esc_html( $part );
	}

	return $out;
}

/**
 * ชื่อ+เวอร์ชัน EA ทุกตัวในชุด เช่น "PLUS 5.1 · DUAL 6.3 · BTC 1.1"
 * ดึงจากไฟล์ที่ตั้งไว้หน้า /go/ จุดเดียว แก้ไฟล์แล้วหน้าแรกเปลี่ยนตาม
 * $full = true จะคงคำว่า FENIX ไว้ด้วย
 */
function fenix_home_versions( $full = false ) {
	$out = array();

	foreach ( fenix_hero_deck_cards() as $card ) {
		$name = $full ? $card['name'] : trim( preg_replace( '/^FENIX\s+/i', '', $card['name'] ) );

		if ( '' === $name ) {
			continue;
		}

		$out[] = '' !== $card['version'] ? $name . ' ' . $card['version'] : $name;
	}

	return $out;
}

/**
 * ชุดแท่งเทียนแบบล็อกค่า (ไม่สุ่มใหม่ทุกครั้ง) ตามต้นแบบที่อนุมัติ
 * drift รับเลขลำดับแท่ง คืนค่าการเปลี่ยนแปลงราคา
 */
function fenix_candle_series( $seed, $count, $drift ) {
	$state = (int) $seed;
	$price = 100.0;
	$out   = array();

	$rand = function () use ( &$state ) {
		$state = ( $state * 16807 ) % 2147483647;
		return $state / 2147483647;
	};

	for ( $i = 0; $i < $count; $i++ ) {
		$open  = $price;
		$close = $open + call_user_func( $drift, $i ) + ( $rand() - 0.5 ) * 2.4;
		$high  = max( $open, $close ) + $rand() * 1.3;
		$low   = min( $open, $close ) - $rand() * 1.3;

		$out[] = array(
			'o' => $open,
			'c' => $close,
			'h' => $high,
			'l' => $low,
		);

		$price = $close;
	}

	return $out;
}

/**
 * วาดแท่งเทียนเป็น SVG นิ่ง (ไม่ต้องรัน JS)
 * คืน array: svg = แท่งเทียน, x/y = ฟังก์ชันหาพิกัด, step = ความกว้างต่อแท่ง
 */
function fenix_candles( $series, $w, $h, $pad, $args = array() ) {
	$lows  = array();
	$highs = array();

	foreach ( $series as $candle ) {
		$lows[]  = $candle['l'];
		$highs[] = $candle['h'];
	}

	$lo    = min( $lows );
	$hi    = max( $highs );
	$span  = ( $hi - $lo ) > 0 ? ( $hi - $lo ) : 1;
	$inner = $h - $pad['t'] - $pad['b'];
	$step  = ( $w - $pad['l'] - $pad['r'] ) / max( 1, count( $series ) );

	$y = function ( $value ) use ( $pad, $hi, $span, $inner ) {
		return round( $pad['t'] + ( $hi - $value ) / $span * $inner, 2 );
	};

	$x = function ( $i ) use ( $pad, $step ) {
		return round( $pad['l'] + $i * $step + $step / 2, 2 );
	};

	$svg  = '';
	$wick = isset( $args['wick'] ) ? $args['wick'] : 1.5;

	if ( ! empty( $args['grid'] ) ) {
		for ( $g = 0; $g < 5; $g++ ) {
			$gy = round( $pad['t'] + $g * $inner / 4, 2 );
			$svg .= '<line x1="0" x2="' . $w . '" y1="' . $gy . '" y2="' . $gy . '" stroke="#202026" stroke-width="1"/>';
		}
	}

	foreach ( $series as $i => $candle ) {
		$up   = $candle['c'] >= $candle['o'];
		$col  = $up ? '#22C55E' : '#EF4444';
		$bw   = round( max( 2, $step * 0.56 ), 2 );
		$cx   = $x( $i );
		$top  = min( $y( $candle['o'] ), $y( $candle['c'] ) );
		$body = round( max( 1.5, abs( $y( $candle['o'] ) - $y( $candle['c'] ) ) ), 2 );

		$svg .= '<line x1="' . $cx . '" x2="' . $cx . '" y1="' . $y( $candle['h'] ) . '" y2="' . $y( $candle['l'] ) . '" stroke="' . $col . '" stroke-width="' . $wick . '"/>';
		$svg .= '<rect x="' . round( $cx - $bw / 2, 2 ) . '" y="' . $top . '" width="' . $bw . '" height="' . $body . '" rx="1" fill="' . $col . '"/>';
	}

	return array(
		'svg'  => $svg,
		'x'    => $x,
		'y'    => $y,
		'step' => $step,
	);
}
/* --------------------------------------------------------------
 * ตัวช่วยของหน้า /go/
 * -------------------------------------------------------------- */

/** ลิงก์ออกนอกเว็บหรือไม่ (ใช้เลือกไอคอนท้ายปุ่มและการเปิดแท็บใหม่) */
function fenix_go_is_external( $url, $home ) {
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );

	return $host && $host !== $home;
}

/** แอตทริบิวต์เปิดแท็บใหม่สำหรับลิงก์นอกเว็บ */
function fenix_go_target( $external ) {
	return $external ? ' target="_blank" rel="noopener"' : '';
}

/** บรรทัดบอกปลายทางใต้ปุ่มที่ออกไปเว็บอื่น */
function fenix_go_dest( $url, $prefix ) {
	$host = wp_parse_url( (string) $url, PHP_URL_HOST );

	if ( ! $host || '' === $prefix ) {
		return '';
	}

	return '<span class="dest">' . fenix_icon( 'lock', 'i' ) . esc_html( $prefix ) . ' <span class="keep-case">' . esc_html( $host ) . '</span></span>';
}

function fenix_mod( $key ) {
	$defaults = fenix_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( $key, $default );

	/*
	 * Launch copy migration:
	 * WordPress stores Customizer values in the database. If an old default was
	 * saved before launch, changing fenix_defaults() alone will not update it.
	 * These fallbacks only replace the original starter copy/placeholders; real
	 * custom values remain untouched.
	 */
	$stale_values = array(
		'facebook_url'    => array(
			'https://www.facebook.com/fenixpro.ea',
		),
		'instagram_url'   => array(
			'https://www.instagram.com/fenixpro.ea/',
			'https://www.instagram.com/fenixpro.th/',
		),
		'links_step4_desc' => array(
			'กดที่รูปเพื่อดาวน์โหลดไฟล์ EA',
			'กดที่รูปหรือปุ่มด้านล่างเพื่อดาวน์โหลดไฟล์ EA',
		),
		'links_signup_url' => array(
			'/pricing/',
			'https://fenixpro-th.com/pricing/',
		),
		'links_btn1_url' => array(
			'/forward-test/',
			'https://fenixpro-th.com/forward-test/',
			'https://www.myfxbook.com/members/speccub/fenix-smart-core/12163940/dTX1kOz5MZwCABISlzuH',
			'https://www.myfxbook.com/portfolio/eajw/12171161',
		),
		'pricing_subtitle' => 'เลือกแพ็กเกจที่เหมาะกับการใช้งานของคุณ หรือทักมาปรึกษาก่อนตัดสินใจได้',
		'pricing_mode'     => 'contact',
		'pricing_note'     => 'ราคาและเงื่อนไขอาจมีการเปลี่ยนแปลง สอบถามรายละเอียดล่าสุดได้ทาง LINE',
		'pkg1_tag'         => 'สำหรับเริ่มต้นทดลองใช้งาน',
		'pkg1_price'       => 'X,XXX',
		'pkg1_period'      => 'บาท / ปี',
		'pkg1_features'    => "ใช้งานได้ 1 บัญชีเทรด\nคู่มือการติดตั้งและตั้งค่า\nอัปเดตระบบตามรอบเวอร์ชัน\nSupport เบื้องต้นผ่าน LINE",
		'pkg2_price'       => 'X,XXX',
		'pkg2_period'      => 'บาท / ปี',
		'pkg2_features'    => "ใช้งานได้ 1 ถึง 2 บัญชีเทรด\nไฟล์ Preset ตั้งค่าพร้อมใช้\nอัปเดตระบบต่อเนื่อง\nSupport ส่วนตัวผ่าน LINE",
		'pkg3_price'       => 'X,XXX',
		'pkg3_period'      => 'บาท / ปี',
		'pkg3_features'    => "ทีมงานช่วยติดตั้งบนเครื่อง / VPS\nสอนตั้งค่าและแนวคิดบริหารความเสี่ยง\nติดตามผลในช่วงเริ่มต้นใช้งาน\nSupport ส่วนตัวแบบใกล้ชิด",
		'links_mt5_install_title' => 'ขั้นตอนแรก · ติดตั้งแอป MT5',
		'links_btn4_label' => 'บทความ & ความรู้ EA',
		'links_btn4_url'   => '/articles/',
		'links_note'       => 'การเทรด Forex/CFD มีความเสี่ยงสูง อาจขาดทุนได้ · FENIX PRO EA เป็นเครื่องมือช่วยเทรด ไม่ใช่การรับประกันผลกำไร',
		'links_signup_label' => 'สมัครใช้งาน FENIX PRO EA',
		'risk_text'        => 'การเทรด Forex, Gold, CFD หรือสินทรัพย์ทางการเงินมีความเสี่ยงสูง ผู้ใช้งานอาจขาดทุนได้ ผลการทดสอบหรือผลลัพธ์ในอดีตไม่ได้รับประกันผลลัพธ์ในอนาคต FENIX PRO EA เป็นเครื่องมือช่วยเทรดตามเงื่อนไขที่กำหนด ไม่ใช่การรับประกันผลกำไร ผู้ใช้งานควรศึกษาข้อมูลและบริหารความเสี่ยงให้เหมาะสมกับตนเองก่อนใช้งานจริง',
		'riskpage_intro'   => 'โปรดอ่านและทำความเข้าใจข้อมูลความเสี่ยงต่อไปนี้อย่างละเอียดก่อนตัดสินใจใช้งาน FENIX PRO EA หรือทำการเทรดใด ๆ',
		'rp_block1_text'   => 'การเทรด Forex, ทองคำ, CFD และสินทรัพย์ทางการเงินอื่น ๆ มีความเสี่ยงสูงต่อเงินทุนของคุณ ราคาอาจเคลื่อนไหวผันผวนรุนแรง คุณอาจสูญเสียเงินลงทุนบางส่วนหรือทั้งหมด จึงควรใช้เฉพาะเงินเย็นที่พร้อมรับความเสี่ยงได้',
		'rp_block2_text'   => 'FENIX PRO EA เป็นเครื่องมือช่วยเทรดที่ทำงานตามเงื่อนไขที่กำหนดไว้ ไม่ใช่ระบบที่รับประกันผลกำไร ผลการเทรดขึ้นอยู่กับสภาวะตลาด การตั้งค่า ทุน ระดับความเสี่ยง และเงื่อนไขของโบรกเกอร์',
		'rp_block3_text'   => 'ผลการทดสอบย้อนหลัง (Backtest) และผลการทดสอบบนบัญชีจริง/เดโม่ (Forward Test) สะท้อนเฉพาะช่วงเวลาที่ทดสอบเท่านั้น ไม่ได้รับประกันว่าผลในอนาคตจะเป็นไปในทิศทางเดียวกัน',
		'rp_block4_text'   => 'ผู้ใช้งานเป็นผู้รับผิดชอบการตัดสินใจเทรดและการตั้งค่าระบบด้วยตนเอง รวมถึงการบริหารความเสี่ยง การเลือกโบรกเกอร์ และการดูแลบัญชีของตนเอง',
		'rp_block5_text'   => 'ข้อมูลทั้งหมดบนเว็บไซต์นี้จัดทำขึ้นเพื่อให้ข้อมูลเกี่ยวกับเครื่องมือเท่านั้น ไม่ถือเป็นคำแนะนำทางการเงินหรือการลงทุน หากต้องการคำแนะนำเฉพาะบุคคล ควรปรึกษาผู้เชี่ยวชาญที่ได้รับอนุญาต',
		'riskpage_updated' => 'ปรับปรุงล่าสุด: ระบุวันที่',
	);

	if ( array_key_exists( $key, $stale_values ) ) {
		$stale_value = $stale_values[ $key ];
		$is_stale    = is_array( $stale_value )
			? in_array( (string) $value, array_map( 'strval', $stale_value ), true )
			: (string) $value === (string) $stale_value;

		if ( $is_stale ) {
			return $default;
		}
	}

	if ( 'search_console_verify' === $key && '' === trim( (string) $value ) ) {
		return $default;
	}

	if ( 'line_url' === $key && in_array( trim( (string) $value ), array( '', '#' ), true ) ) {
		return $default;
	}

	if ( in_array( $key, array( 'facebook_url', 'instagram_url', 'tiktok_url', 'youtube_url' ), true ) && in_array( trim( (string) $value ), array( '', '#' ), true ) ) {
		return $default;
	}

	return $value;
}

/**
 * แปลง textarea เป็น array รายการ (บรรทัดละ 1 รายการ)
 */
function fenix_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$lines = array_map( 'trim', $lines );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * ตรวจว่าค่ายังเป็น placeholder (ยังไม่กรอกจริง) หรือไม่
 * ใช้ซ่อนสถิติที่ยังขึ้นต้นด้วย "ระบุ" / "เช่น" ไม่ให้หน้าแรกดูเหมือนยังทำไม่เสร็จ
 */
function fenix_is_placeholder( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return true;
	}
	foreach ( array( 'ระบุ', 'เช่น' ) as $needle ) {
		if ( 0 === mb_strpos( $value, $needle ) ) {
			return true;
		}
	}
	return false;
}

/**
 * แปลงลิงก์ที่ตั้งค่าได้ ให้รองรับทั้ง URL เต็ม, anchor และ slug ภายในเว็บ
 */
function fenix_link_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '#';
	}

	if ( '#' === $url || 0 === strpos( $url, '#' ) || preg_match( '#^(https?:)?//#i', $url ) || preg_match( '#^(mailto|tel):#i', $url ) ) {
		return $url;
	}

	return home_url( '/' . ltrim( $url, '/' ) );
}

/**
 * Hotfix: แก้ลิงก์ภายในบทความที่เคยหลุดเป็นโดเมน chatgpt.com ตอน render
 * ต้นฉบับใน WordPress editor ยังควรแก้ในฐานข้อมูลภายหลังเพื่อความสะอาด
 */
function fenix_fix_chatgpt_internal_links( $content ) {
	if ( '' === (string) $content ) {
		return $content;
	}

	$site_url = rtrim( home_url(), '/' );

	return str_replace(
		array(
			'https://chatgpt.com',
			'http://chatgpt.com',
			'https:\/\/chatgpt.com',
			'http:\/\/chatgpt.com',
		),
		array(
			$site_url,
			$site_url,
			str_replace( '/', '\/', $site_url ),
			str_replace( '/', '\/', $site_url ),
		),
		$content
	);
}
add_filter( 'the_content', 'fenix_fix_chatgpt_internal_links', 1 );
add_filter( 'the_excerpt', 'fenix_fix_chatgpt_internal_links', 1 );
add_filter( 'the_content_feed', 'fenix_fix_chatgpt_internal_links', 1 );
add_filter( 'the_excerpt_rss', 'fenix_fix_chatgpt_internal_links', 1 );
add_filter( 'wp_nav_menu_items', 'fenix_fix_chatgpt_internal_links', 1 );
add_filter( 'widget_text', 'fenix_fix_chatgpt_internal_links', 1 );

/**
 * URL โลโก้ (ใช้โลโก้ที่อัปโหลดเอง ถ้าไม่มีใช้โลโก้ที่ฝังมากับธีม)
 */
function fenix_logo_url() {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return get_template_directory_uri() . '/assets/img/logo.png';
}

/**
 * ALT ของภาพเด่นจาก Media Library พร้อม fallback เป็นชื่อบทความ
 *
 * @param int $post_id Post ID. Defaults to the current post.
 * @return string
 */
function fenix_featured_image_alt( $post_id = 0 ) {
	$post_id      = $post_id ? absint( $post_id ) : get_the_ID();
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$alt          = $thumbnail_id ? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) : '';

	if ( '' === $alt ) {
		$alt = get_the_title( $post_id );
	}

	return $alt;
}

/**
 * เมนูสำรอง กรณียังไม่ได้สร้างเมนูใน WordPress
 * ชี้ไปยังหน้าย่อยตาม slug ที่แนะนำ (ปรับเมนูจริงได้ที่ รูปแบบ → เมนู)
 */
function fenix_menu_tree() {
	return array(
		array( 'url' => '/', 'label' => 'หน้าแรก' ),
		array( 'url' => '/go/', 'label' => 'ดาวน์โหลด EA' ),
		array(
			'url'      => '/backtest/',
			'label'    => 'ผลทดสอบ',
			'children' => array(
				array( 'url' => '/backtest/', 'label' => 'ผล BACKTEST' ),
				array( 'url' => '/forward-test/', 'label' => 'ผล FORWARD TEST' ),
			),
		),
		array(
			'url'      => '/fenix-pro-ea-install-guide/',
			'label'    => 'วิธีใช้งาน',
			'children' => array(
				array( 'url' => '/open-mt5-account/', 'label' => 'เปิดบัญชี MT5' ),
				array( 'url' => '/mt5-login-zaurix-server/', 'label' => 'ติดตั้ง MT5 และ LOGIN' ),
				array( 'url' => '/deposit-zaurix/', 'label' => 'ฝากเงินเข้าบัญชี' ),
				array( 'url' => '/fenix-pro-ea-install-guide/', 'label' => 'ติดตั้ง EA บน MT5' ),
				array( 'url' => '/fenix-dual-presets/', 'label' => 'เลือกคูณ LOT ของ FENIX DUAL' ),
				array( 'url' => '/vps-desktop-windows/', 'label' => 'VPS บน WINDOWS' ),
				array( 'url' => '/vps-android/', 'label' => 'VPS บน ANDROID' ),
				array( 'url' => '/vps-iphone-ios/', 'label' => 'VPS บน IPHONE' ),
			),
		),
		array( 'url' => '/pricing/', 'label' => 'แพ็กเกจ' ),
		array( 'url' => '/articles/', 'label' => 'บทความ' ),
	);
}

/**
 * path ของลิงก์ในรูปแบบเดียวกัน เช่น https://a.com/go/?x=1 -> /go/
 */
function fenix_menu_path( $url ) {
	$path = wp_parse_url( (string) $url, PHP_URL_PATH );
	$path = trim( (string) $path, '/' );

	return '' === $path ? '/' : '/' . $path . '/';
}

/**
 * ไอคอนและชื่อสั้นของแต่ละหน้า ใช้ตอนเมนูมือถือแสดงเป็นกริด
 * คีย์เป็น path จึงใช้ได้ทั้งเมนูสำรองและเมนูที่ตั้งเองใน WordPress
 */
function fenix_menu_meta( $url ) {
	$map = array(
		'/'                            => array( 'home', 'หน้าแรก' ),
		'/go/'                         => array( 'download', 'ดาวน์โหลด' ),
		'/backtest/'                   => array( 'chart', 'BACKTEST' ),
		'/forward-test/'               => array( 'pulse', 'FORWARD' ),
		'/pricing/'                    => array( 'tag', 'แพ็กเกจ' ),
		'/articles/'                   => array( 'guide', 'บทความ' ),
		'/open-mt5-account/'           => array( 'account', 'เปิดบัญชี' ),
		'/mt5-login-zaurix-server/'    => array( 'layout', 'ติดตั้ง MT5' ),
		'/deposit-zaurix/'             => array( 'wallet', 'ฝากเงิน' ),
		'/fenix-pro-ea-install-guide/' => array( 'cpu', 'ติดตั้ง EA' ),
		'/fenix-dual-presets/'         => array( 'sliders', 'คูณ LOT' ),
		'/vps-desktop-windows/'        => array( 'windows', 'VPS WIN' ),
		'/vps-android/'                => array( 'android', 'ANDROID' ),
		'/vps-iphone-ios/'             => array( 'apple', 'IPHONE' ),
	);

	$path = fenix_menu_path( $url );

	return isset( $map[ $path ] ) ? $map[ $path ] : array( '', '' );
}

/**
 * หน้านี้คือหน้าที่กำลังเปิดอยู่หรือไม่ (ใช้ไฮไลต์ช่องในเมนู)
 */
function fenix_menu_is_current( $url ) {
	$request = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	return fenix_menu_path( $url ) === fenix_menu_path( $request );
}

/**
 * ลิงก์หนึ่งช่องในเมนู
 * $is_tile = true สำหรับช่องที่มีไอคอน (หัวข้อที่มีเมนูย่อยจะกลายเป็นชื่อหมวดแทน)
 */
function fenix_menu_link( $url, $label, $is_tile ) {
	list( $icon, $short ) = fenix_menu_meta( $url );
	$has_icon = $is_tile && '' !== $icon;

	$attrs = '';
	if ( $has_icon && fenix_menu_is_current( $url ) ) {
		$attrs .= ' aria-current="page"';
	}
	if ( $has_icon && $short !== $label ) {
		$attrs .= ' aria-label="' . esc_attr( $label ) . '"';
	}

	$out = '<a href="' . esc_url( home_url( $url ) ) . '"' . $attrs . '>';

	if ( $has_icon ) {
		$out .= '<span class="nav-ic">' . fenix_icon( $icon ) . '</span>';
	}

	$out .= '<span class="nav-label">' . esc_html( $label ) . '</span>';

	if ( $has_icon ) {
		$out .= '<span class="nav-label-short">' . esc_html( $short ) . '</span>';
	}

	return $out . '</a>';
}

function fenix_fallback_menu() {
	echo '<ul class="nav-list">';
	echo '<li class="nav-group-label">เมนูหลัก</li>';

	foreach ( fenix_menu_tree() as $item ) {
		$has_children = ! empty( $item['children'] );
		echo '<li' . ( $has_children ? ' class="menu-item-has-children"' : '' ) . '>';
		echo fenix_menu_link( $item['url'], $item['label'], ! $has_children ); // phpcs:ignore WordPress.Security.EscapeOutput

		if ( $has_children ) {
			echo '<ul class="sub-menu">';
			foreach ( $item['children'] as $child ) {
				echo '<li>' . fenix_menu_link( $child['url'], $child['label'], true ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</ul>';
		}

		echo '</li>';
	}

	echo '</ul>';
}
function fenix_guide_menu_extra_items() {
	$items  = '<li class="menu-item menu-item-fenix-open-account"><a href="' . esc_url( home_url( '/open-mt5-account/' ) ) . '">สอนเปิดบัญชี MT5</a></li>';
	$items .= '<li class="menu-item menu-item-fenix-mt5-login"><a href="' . esc_url( home_url( '/mt5-login-zaurix-server/' ) ) . '">ติดตั้ง MT5 และ Login Zaurix-Server</a></li>';

	return $items;
}

function fenix_add_guides_to_primary_menu( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	if ( false !== strpos( $items, '/open-mt5-account/' ) && false !== strpos( $items, '/mt5-login-zaurix-server/' ) ) {
		return $items;
	}

	$guide_pos = stripos( $items, '/fenix-pro-ea-install-guide/' );
	if ( false === $guide_pos ) {
		return $items;
	}

	$submenu_pos = stripos( $items, '<ul class="sub-menu"', $guide_pos );
	if ( false === $submenu_pos ) {
		return $items;
	}

	$submenu_end = stripos( $items, '</ul>', $submenu_pos );
	if ( false === $submenu_end ) {
		return $items;
	}

	$extra = '';
	if ( false === strpos( $items, '/open-mt5-account/' ) ) {
		$extra .= '<li class="menu-item menu-item-fenix-open-account"><a href="' . esc_url( home_url( '/open-mt5-account/' ) ) . '">สอนเปิดบัญชี MT5</a></li>';
	}
	if ( false === strpos( $items, '/mt5-login-zaurix-server/' ) ) {
		$extra .= '<li class="menu-item menu-item-fenix-mt5-login"><a href="' . esc_url( home_url( '/mt5-login-zaurix-server/' ) ) . '">ติดตั้ง MT5 และ Login Zaurix-Server</a></li>';
	}

	if ( '' === $extra ) {
		return $items;
	}

	return substr( $items, 0, $submenu_end ) . $extra . substr( $items, $submenu_end );
}
add_filter( 'wp_nav_menu_items', 'fenix_add_guides_to_primary_menu', 9, 2 );

/**
 * Keep the ad/contact link hub visible in the primary menu even when a custom
 * WordPress menu is assigned and the fallback menu is not used.
 */
function fenix_add_contact_to_primary_menu( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$go_url = home_url( '/go/' );
	if ( false !== strpos( $items, $go_url ) || false !== strpos( $items, esc_url( $go_url ) ) || false !== strpos( $items, '/go/' ) ) {
		return $items;
	}

	return $items . '<li class="menu-item menu-item-fenix-contact"><a href="' . esc_url( $go_url ) . '">ติดต่อ</a></li>';
}
add_filter( 'wp_nav_menu_items', 'fenix_add_contact_to_primary_menu', 10, 2 );

/**
 * Language switcher slot.
 *
 * This prefers multilingual plugins for real translated URLs, hreflang, SEO,
 * and Elementor compatibility. The manual fallback is only a visible starter
 * until a plugin such as TranslatePress, Polylang, or WPML owns translations.
 */
function fenix_language_switcher() {
	if ( ! fenix_mod( 'show_language_switcher' ) ) {
		return;
	}

	$plugin_markup = '';
	$plugin_class  = '';

	if ( shortcode_exists( 'gtranslate' ) ) {
		$plugin_markup = do_shortcode( '[gtranslate]' );
		$plugin_class  = ' language-switcher--gtranslate';
	} elseif ( shortcode_exists( 'language-switcher' ) ) {
		$plugin_markup = do_shortcode( '[language-switcher]' );
	} elseif ( function_exists( 'pll_the_languages' ) ) {
		$plugin_markup = pll_the_languages(
			array(
				'echo'          => 0,
				'show_flags'    => 0,
				'show_names'    => 1,
				'hide_if_empty' => 0,
			)
		);
	} elseif ( function_exists( 'icl_get_languages' ) ) {
		$wpml_languages = icl_get_languages( 'skip_missing=0&orderby=code' );

		if ( is_array( $wpml_languages ) && $wpml_languages ) {
			$plugin_markup = '<ul class="language-switcher-list">';
			foreach ( $wpml_languages as $language ) {
				if ( empty( $language['url'] ) || empty( $language['native_name'] ) ) {
					continue;
				}

				$plugin_markup .= sprintf(
					'<li><a class="%1$s" href="%2$s">%3$s</a></li>',
					! empty( $language['active'] ) ? 'is-active' : '',
					esc_url( $language['url'] ),
					esc_html( $language['native_name'] )
				);
			}
			$plugin_markup .= '</ul>';
		}
	}

	if ( $plugin_markup ) {
		echo '<div class="language-switcher language-switcher--plugin' . esc_attr( $plugin_class ) . '" aria-label="' . esc_attr__( 'Language switcher', 'fenix-pro' ) . '">';
		echo wp_kses_post( $plugin_markup );
		echo '</div>';
		return;
	}

	$languages = array();
	foreach ( fenix_lines( fenix_mod( 'language_fallback_items' ) ) as $language_line ) {
		$parts = array_map( 'trim', explode( '|', $language_line ) );
		if ( count( $parts ) < 3 ) {
			continue;
		}

		$code = sanitize_key( $parts[0] );
		if ( ! $code ) {
			continue;
		}

		if ( count( $parts ) >= 4 ) {
			$flag  = $parts[1];
			$short = $parts[2];
			$label = $parts[3];
		} else {
			$flag  = '';
			$short = $parts[1];
			$label = $parts[2];
		}

		$languages[ $code ] = array(
			'flag'  => $flag,
			'short' => $short,
			'label' => $label,
		);
	}

	if ( ! $languages ) {
		$languages = array(
			'th' => array(
				'flag'  => '🇹🇭',
				'short' => 'TH',
				'label' => 'ไทย',
			),
			'en' => array(
				'flag'  => '🇬🇧',
				'short' => 'EN',
				'label' => 'English',
			),
		);
	}

	$current = substr( get_locale(), 0, 2 );
	if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current = sanitize_key( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( ! isset( $languages[ $current ] ) ) {
		$current = 'th';
	}
	?>
	<details class="language-switcher language-switcher--fallback">
		<summary aria-label="<?php echo esc_attr__( 'Choose language', 'fenix-pro' ); ?>">
			<span class="language-switcher-current">
				<?php if ( ! empty( $languages[ $current ]['flag'] ) ) : ?>
					<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $languages[ $current ]['flag'] ); ?></span>
				<?php endif; ?>
				<span class="language-switcher-code"><?php echo esc_html( $languages[ $current ]['short'] ); ?></span>
			</span>
		</summary>
		<div class="language-switcher-menu">
			<?php foreach ( $languages as $code => $language ) : ?>
				<a class="<?php echo esc_attr( $code === $current ? 'is-active' : '' ); ?>" href="<?php echo esc_url( add_query_arg( 'lang', $code ) ); ?>">
					<?php if ( ! empty( $language['flag'] ) ) : ?>
						<span class="language-switcher-flag" aria-hidden="true"><?php echo esc_html( $language['flag'] ); ?></span>
					<?php endif; ?>
					<span class="language-switcher-code"><?php echo esc_html( $language['short'] ); ?></span>
					<small><?php echo esc_html( $language['label'] ); ?></small>
				</a>
			<?php endforeach; ?>
		</div>
	</details>
	<?php
}

/**
 * หัวหน้าเพจ (page hero) ใช้ร่วมกันทุกหน้าย่อย
 */
function fenix_page_hero( $kicker, $title, $subtitle = '' ) {
	?>
	<section class="phero">
		<div class="phero-bg" aria-hidden="true">
			<span class="ember ember-a"></span>
			<span class="ember ember-b"></span>
		</div>
		<div class="container phero-inner reveal">
			<?php if ( $kicker ) : ?>
				<span class="kicker"><?php echo esc_html( $kicker ); ?></span>
			<?php endif; ?>
			<h1 class="phero-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="phero-sub"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * แถบ CTA ทักไลน์ ใช้ปิดท้ายหน้าย่อย
 */
function fenix_line_cta( $title = '', $sub = '' ) {
	$title = $title ? $title : 'มีคำถาม? ทักมาคุยกับเราได้เลย';
	$sub   = $sub ? $sub : 'สอบถามรายละเอียด การติดตั้ง และความเหมาะสมกับทุนของคุณได้ทาง LINE';
	?>
	<section class="section cta cta--slim" id="cta">
		<div class="cta-bg" aria-hidden="true"><span class="ember ember-c"></span></div>
		<div class="container container-narrow">
			<div class="cta-inner reveal">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo esc_html( $sub ); ?></p>
				<a class="btn btn-line btn-lg" href="<?php echo esc_url( fenix_mod( 'line_url' ) ); ?>" target="_blank" rel="noopener">
					<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					ทัก LINE เพื่อสอบถาม
				</a>
			</div>
		</div>
	</section>
	<?php
}

/* --------------------------------------------------------------
 * Inline SVG icons
 * -------------------------------------------------------------- */
function fenix_icon( $name, $class = 'icon' ) {
	$svg = array(
		'flame'    => '<path d="M12 2c1 4-3 5.5-3 9a3 3 0 0 0 6 0c0-1.2-.6-2.2-1.2-3.1C16.5 9.4 19 11.6 19 15a7 7 0 0 1-14 0c0-5 5-7.5 7-13z"/>',
		'pulse'    => '<path d="M3 12h4l2.5-6 4 12L16 12h5"/>',
		'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
		'gauge'    => '<path d="M4.5 19a9 9 0 1 1 15 0"/><path d="M12 13l4-4"/><circle cx="12" cy="14" r="1.6"/>',
		'flag'     => '<path d="M5 21V4"/><path d="M5 5h12l-2.5 3.5L17 12H5"/>',
		'home'     => '<path d="M4 11.5 12 5l8 6.5"/><path d="M6.5 10.5V20h11v-9.5"/><path d="M10 20v-5h4v5"/>',
		'chart'    => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 15l3-3 2.4 2.4L17.5 9"/><path d="M15 9h2.5v2.5"/>',
		'account'  => '<circle cx="9" cy="8" r="3"/><path d="M3.8 19c.7-3 2.8-4.6 5.2-4.6 1.4 0 2.7.5 3.7 1.5"/><circle cx="17" cy="16.5" r="4"/><path d="M17 14.5v4M15 16.5h4"/>',
		'guide'    => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21V5.5z"/><path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21V5.5z"/><path d="M7 7h1.6M7 10h1.6M15.4 7H17M15.4 10H17"/>',
		'tag'      => '<path d="M20 12.5 12.5 20 4 11.5V4h7.5L20 12.5z"/><circle cx="8.2" cy="8.2" r="0.8"/>',
		'cpu'      => '<rect x="6" y="6" width="12" height="12" rx="2"/><rect x="10" y="10" width="4" height="4"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/>',
		'candles'  => '<path d="M7 6v3M7 15v3M7 9h0a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 7 15h0a1.5 1.5 0 0 1-1.5-1.5v-3A1.5 1.5 0 0 1 7 9zM17 3v3M17 13v4M17 6h0a1.5 1.5 0 0 1 1.5 1.5v4A1.5 1.5 0 0 1 17 13h0a1.5 1.5 0 0 1-1.5-1.5v-4A1.5 1.5 0 0 1 17 6z"/><path d="M3 21h18"/>',
		'layout'   => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 9v11"/>',
		'shield'   => '<path d="M12 3l7 3v5c0 4.6-3 8.4-7 10-4-1.6-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
		'moon'     => '<path d="M20 14.5A8 8 0 1 1 9.5 4 6.5 6.5 0 0 0 20 14.5z"/>',
		'headset'  => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="3" y="13" width="4" height="6" rx="1.6"/><rect x="17" y="13" width="4" height="6" rx="1.6"/><path d="M19 19a3 3 0 0 1-3 3h-3"/>',
		'check'    => '<path d="M4 12.5l5 5L20 6.5"/>',
		'x'        => '<path d="M6 6l12 12M18 6L6 18"/>',
		'warn'     => '<path d="M12 3.5l9.5 16.5h-19L12 3.5z"/><path d="M12 10v4.2"/><circle cx="12" cy="17" r="0.4"/>',
		'chat'     => '<path d="M21 12a8 8 0 0 1-8 8c-1.2 0-2.4-.25-3.4-.7L4 21l1.4-4.2A8 8 0 1 1 21 12z"/><path d="M8.5 11h.01M12 11h.01M15.5 11h.01"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'quote'    => '<path d="M7.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C10.5 7 9 5.5 7 5M17.5 11c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V9.5C20.5 7 19 5.5 17 5"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'download' => '<path d="M12 4v10M7.5 10.5L12 15l4.5-4.5"/><path d="M5 19h14"/>',
		'wallet'   => '<path d="M4 7.8h13.4a2 2 0 0 1 2 2v6.4a2 2 0 0 1-2 2H5.6A1.6 1.6 0 0 1 4 16.6z"/><path d="M4 7.8V6.4A1.9 1.9 0 0 1 5.9 4.5H16"/><circle cx="16.2" cy="13" r="1"/>',
		'sliders'  => '<path d="M4 8.5h16M4 15.5h16"/><circle cx="10" cy="8.5" r="2.1"/><circle cx="15" cy="15.5" r="2.1"/>',
		'link'     => '<path d="M9.5 14.5l5-5"/><path d="M11.5 6.5l1-1a4 4 0 0 1 5.7 5.7l-2 2"/><path d="M12.5 17.5l-1 1a4 4 0 0 1-5.7-5.7l2-2"/>',
		'windows'  => '<path d="M3.5 5.5 10.5 4.5v6.8h-7z"/><path d="M13 4.1 20.5 3v8.3H13z"/><path d="M3.5 12.7h7v6.8l-7-1z"/><path d="M13 12.7h7.5V21L13 19.9z"/>',
		'android'  => '<path d="M8 8.5h8a3 3 0 0 1 3 3V17a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5.5a3 3 0 0 1 3-3z"/><path d="M8.5 5 7 2.8M15.5 5 17 2.8"/><path d="M9 19v2M15 19v2M3 12v4M21 12v4"/><path d="M9.2 12h.01M14.8 12h.01"/>',
		'apple'    => '<path d="M16.6 13.1c0-2.1 1.7-3.1 1.8-3.2-1-1.5-2.5-1.7-3-1.7-1.3-.1-2.5.7-3.1.7-.7 0-1.7-.7-2.8-.7-1.5 0-2.9.9-3.6 2.2-1.6 2.7-.4 6.6 1.1 8.8.7 1.1 1.6 2.3 2.8 2.2 1.1 0 1.5-.7 2.8-.7 1.3 0 1.7.7 2.8.7 1.2 0 1.9-1.1 2.6-2.2.8-1.2 1.1-2.3 1.2-2.4-.1 0-2.6-1-2.6-3.7z"/><path d="M14.7 5.9c.6-.8 1-1.8.9-2.9-.9.1-1.9.6-2.5 1.3-.6.7-1.1 1.7-.9 2.8.9.1 1.9-.5 2.5-1.2z"/>',
		'macos'    => '<rect x="4" y="4.5" width="16" height="11" rx="1.8"/><path d="M8.5 19.5h7"/><path d="M12 15.5v4"/><path d="M9.8 9.7c-.45 0-.82-.37-.82-.83 0-.45.37-.82.82-.82"/><path d="M14.2 9.7c.45 0 .82-.37.82-.83 0-.45-.37-.82-.82-.82"/><path d="M9.9 12.1c1.2.75 3 .75 4.2 0"/>',
		'users'    => '<path d="M16.5 19.5c0-2.1-1.8-3.8-4.5-3.8s-4.5 1.7-4.5 3.8"/><circle cx="12" cy="9" r="3.2"/><path d="M20.5 18.5c0-1.7-1.2-3-3-3.4"/><path d="M16.8 6.3a2.6 2.6 0 0 1 0 5.1"/><path d="M3.5 18.5c0-1.7 1.2-3 3-3.4"/><path d="M7.2 6.3a2.6 2.6 0 0 0 0 5.1"/>',

		'chev-r'   => '<path d="m9 6 6 6-6 6"/>',
		'ext'      => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
		'book'     => '<path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2z"/><path d="M22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z"/>',
		'server'   => '<rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><path d="M6 6h.01M6 18h.01"/>',
		'play'     => '<circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4z"/>',
		'user-plus' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/>',
		'chev'     => '<path d="m6 9 6 6 6-6"/>',
		'down'     => '<path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>',
		'user'     => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
		'zap'      => '<path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/>',
		'eye'      => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
		'activity' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
		'plus'     => '<path d="M5 12h14M12 5v14"/>',
		'minus'    => '<path d="M5 12h14"/>',
		'heart'    => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
		'pie'      => '<path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>',
		'brief'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
		'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'file'     => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>',
		'image'    => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.09-3.09a2 2 0 0 0-2.82 0L6 21"/>',
		'copy'     => '<rect x="8" y="8" width="14" height="14" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
		'spark'    => '<path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/>',
		'lock'     => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
		'plusbox'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/>',
		'list'     => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
		'cal'      => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'bell'     => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
	);

	// แบรนด์ไอคอน LINE (โลโก้จริง) · เป็น path แบบ fill ไม่ใช่ stroke จึง render แยก.
	if ( 'line' === $name ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.348 0 .63.283.63.63 0 .344-.282.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>';
	}

	// แบรนด์ไอคอนแบบ fill (Facebook / Instagram / TikTok / YouTube) trusted.
	$fenix_brand = array(
		'facebook'  => '<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>',
		'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.311.975.975 1.249 2.242 1.311 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.311 3.608-.975.975-2.242 1.249-3.608 1.311-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.311-.975-.975-1.249-2.242-1.311-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.336-2.633 1.311-3.608.975-.975 2.242-1.249 3.608-1.311 1.266-.058 1.646-.07 4.85-.07M12 0C8.741 0 8.332.015 7.052.073 5.197.158 3.355.673 2.014 2.014.673 3.355.158 5.197.073 7.052.015 8.332 0 8.741 0 12c0 3.259.015 3.668.073 4.948.085 1.855.6 3.697 1.941 5.038 1.341 1.341 3.183 1.856 5.038 1.941C8.332 23.985 8.741 24 12 24s3.668-.015 4.948-.073c1.855-.085 3.697-.6 5.038-1.941 1.341-1.341 1.856-3.183 1.941-5.038.058-1.28.073-1.689.073-4.948s-.015-3.668-.073-4.948c-.085-1.855-.6-3.697-1.941-5.038C20.645.673 18.803.158 16.948.073 15.668.015 15.259 0 12 0z"/><path d="M12 5.838A6.162 6.162 0 1 0 18.162 12 6.162 6.162 0 0 0 12 5.838zM12 16a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/><circle cx="18.406" cy="5.594" r="1.44"/>',
		'tiktok'    => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.08-.14 1.62.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>',
		'youtube'   => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
	);
	if ( isset( $fenix_brand[ $name ] ) ) {
		return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true" focusable="false">' . $fenix_brand[ $name ] . '</svg>';
	}

	if ( ! isset( $svg[ $name ] ) ) {
		return '';
	}

	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $svg[ $name ] . '</svg>';
}

/**
 * แถวไอคอนโซเชียล (ใช้ทั้ง footer และเมนูมือถือ)
 * ลิงก์ที่ยังไม่กรอกจะชี้ # ไว้ก่อน เจ้าของค่อยใส่ทีหลังผ่าน Customizer
 */
function fenix_social_row( $class = 'social-row' ) {
	$fenix_socials = array(
		'facebook'  => array( fenix_mod( 'facebook_url' ), 'Facebook' ),
		'instagram' => array( fenix_mod( 'instagram_url' ), 'Instagram' ),
		'tiktok'    => array( fenix_mod( 'tiktok_url' ), 'TikTok' ),
		'youtube'   => array( fenix_mod( 'youtube_url' ), 'YouTube' ),
	);
	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $fenix_socials as $fenix_icon_name => $fenix_s ) {
		$fenix_url   = trim( (string) $fenix_s[0] );
		$fenix_href  = '' !== $fenix_url ? $fenix_url : '#';
		$fenix_blank = '' !== $fenix_url ? ' target="_blank" rel="noopener"' : '';
		echo '<a class="social-link" href="' . esc_url( $fenix_href ) . '"' . $fenix_blank . ' aria-label="' . esc_attr( $fenix_s[1] ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput
		echo fenix_icon( $fenix_icon_name ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</a>';
	}
	echo '</div>';
}

/* --------------------------------------------------------------
 * Post views (ตัวนับยอดเข้าชมบทความ)
 * -------------------------------------------------------------- */
function fenix_get_post_views( $post_id ) {
	return (int) get_post_meta( $post_id, 'fenix_views', true );
}

function fenix_increment_post_views( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	update_post_meta( $post_id, 'fenix_views', fenix_get_post_views( $post_id ) + 1 );
}

/* นับเฉพาะผู้เข้าชมหน้าบทความเดี่ยว (ข้ามแอดมิน เพื่อไม่ให้ตัวเลขเพี้ยน)
   หมายเหตุ: ถ้าใช้ปลั๊กแคชหน้า ตัวเลขอาจนับไม่ครบทุกครั้ง */
add_action(
	'wp_head',
	function () {
		if ( is_singular( 'post' ) && ! current_user_can( 'edit_posts' ) ) {
			fenix_increment_post_views( get_queried_object_id() );
		}
	}
);

/* --------------------------------------------------------------
 * Open Graph / Twitter meta (รูปแชร์ LINE/Facebook)
 * ปิดอัตโนมัติถ้ามีปลั๊ก SEO (Yoast/Rank Math) เพื่อไม่ให้แท็กซ้ำ
 * -------------------------------------------------------------- */
function fenix_open_graph() {
	$site        = get_bloginfo( 'name' );
	$default_img = fenix_mod( 'og_default_image' );
	$img_alt     = '';
	if ( ! $default_img ) {
		$default_img = fenix_logo_url();
	}

	if ( is_singular() ) {
		$title = get_the_title();
		$desc  = has_excerpt() ? get_the_excerpt() : wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), fenix_excerpt_length( 160, 40 ), '…' );
		$url   = get_permalink();
		$img   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( ! $img ) {
			$img = $default_img;
		}
		$type    = is_singular( 'post' ) ? 'article' : 'website';
		$img_alt = fenix_featured_image_alt( get_the_ID() );
	} else {
		$title = is_front_page() ? $site : wp_strip_all_tags( wp_get_document_title() );
		$desc  = fenix_mod( 'og_default_description' );
		$url   = home_url( '/' );
		$img   = $default_img;
		$type  = 'website';
	}

	if ( '' === $img_alt ) {
		$img_alt = $title;
	}

	$desc = trim( (string) $desc );
	if ( '' === $desc ) {
		$desc = trim( (string) fenix_mod( 'og_default_description' ) );
	}

	if ( '' !== $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}

	$tags = array(
		'og:site_name'   => $site,
		'og:locale'      => get_locale(),
		'og:type'        => $type,
		'og:title'       => $title,
		'og:description' => $desc,
		'og:url'         => $url,
		'og:image'       => $img,
	);

	foreach ( $tags as $property => $value ) {
		if ( '' === (string) $value ) {
			continue;
		}
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $value ) );
	}

	if ( '' !== (string) $img ) {
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
	}

	if ( 'article' === $type ) {
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( '' !== $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( '' !== (string) $img ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_attr( $img ) );
		printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $img_alt ) );
	}
}
if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	add_action( 'wp_head', 'fenix_open_graph', 5 );
}

/* --------------------------------------------------------------
 * Structured data: Organization + WebSite (ทั้งเว็บ) + FAQPage (หน้าแรก)
 * ปิดอัตโนมัติถ้ามีปลั๊ก SEO
 * -------------------------------------------------------------- */
function fenix_schema_jsonld() {
	$blocks = array();

	$org = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
		'logo'     => fenix_logo_url(),
	);
	$fenix_same = array();
	if ( fenix_mod( 'facebook_url' ) ) {
		$fenix_same[] = fenix_mod( 'facebook_url' );
	}
	if ( fenix_mod( 'instagram_url' ) ) {
		$fenix_same[] = fenix_mod( 'instagram_url' );
	}
	$fenix_line_url = fenix_mod( 'line_url' );
	if ( $fenix_line_url && preg_match( '#^https?://#i', $fenix_line_url ) ) {
		$fenix_same[] = $fenix_line_url;
	}
	if ( $fenix_same ) {
		$org['sameAs'] = $fenix_same;
	}
	$blocks[] = $org;

	$blocks[] = array(
		'@context' => 'https://schema.org',
		'@type'    => 'WebSite',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( is_front_page() ) {
		$faqs = array();
		for ( $i = 1; $i <= 10; $i++ ) {
			$q = fenix_mod( 'faq' . $i . '_q' );
			$a = fenix_mod( 'faq' . $i . '_a' );
			if ( $q && $a ) {
				$faqs[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $q ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $a ),
					),
				);
			}
		}
		if ( $faqs ) {
			$blocks[] = array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $faqs,
			);
		}

		$fenix_app = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'SoftwareApplication',
			'name'                => fenix_mod( 'hero_title' ),
			'applicationCategory' => 'FinanceApplication',
			'operatingSystem'     => 'MetaTrader 5',
			'url'                 => home_url( '/' ),
			'publisher'           => array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
			),
		);
		$fenix_app_desc = trim( (string) fenix_mod( 'og_default_description' ) );
		if ( '' !== $fenix_app_desc ) {
			$fenix_app['description'] = $fenix_app_desc;
		}
		$blocks[] = $fenix_app;
	}

	foreach ( $blocks as $fenix_block ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $fenix_block, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
	add_action( 'wp_head', 'fenix_schema_jsonld', 6 );
}

/* --------------------------------------------------------------
 * SEO เสริม: canonical (archive), robots, sitemap, verification,
 * preconnect ฟอนต์ และ BreadcrumbList helper
 * -------------------------------------------------------------- */
function fenix_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

/* preconnect ฟอนต์ Google ลด RTT ให้โหลดเร็วขึ้นบนมือถือ */
function fenix_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = 'https://fonts.googleapis.com';
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'fenix_resource_hints', 10, 2 );

/* meta ยืนยันความเป็นเจ้าของเว็บ (Google Search Console / Bing) แสดงเมื่อกรอกเท่านั้น */
function fenix_verification_meta() {
	$gsc  = trim( (string) fenix_mod( 'search_console_verify' ) );
	$bing = trim( (string) fenix_mod( 'bing_verify' ) );
	if ( '' !== $gsc ) {
		printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $gsc ) );
	}
	if ( '' !== $bing ) {
		printf( '<meta name="msvalidate.01" content="%s">' . "\n", esc_attr( $bing ) );
	}
}
add_action( 'wp_head', 'fenix_verification_meta', 1 );

/* canonical สำหรับหน้า archive (หมวด/แท็ก) ที่ WP core ไม่ออกให้ ปิดถ้ามีปลั๊ก SEO */
function fenix_archive_canonical() {
	if ( fenix_has_seo_plugin() ) {
		return;
	}
	$url = '';
	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$url = $link;
			}
		}
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_post_type() );
	}
	if ( ! $url ) {
		return;
	}
	$paged = max( (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged > 1 ) {
		$url = trailingslashit( $url ) . 'page/' . $paged . '/';
	}
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'fenix_archive_canonical' );

/* noindex หน้าบาง/ซ้ำ (ค้นหา, ผู้เขียน, วันที่) ไม่ให้แย่ง crawl budget */
function fenix_robots_noindex( $robots ) {
	if ( is_search() || is_author() || is_date() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'fenix_robots_noindex' );

/* ชี้ sitemap ใน robots.txt (เฉพาะตอนไม่มีปลั๊ก SEO ที่จัดการ sitemap เอง) */
function fenix_robots_txt( $output, $public ) {
	if ( '1' === (string) $public && ! fenix_has_seo_plugin() ) {
		$output .= 'Sitemap: ' . esc_url( home_url( '/wp-sitemap.xml' ) ) . "\n";
	}
	return $output;
}
add_filter( 'robots_txt', 'fenix_robots_txt', 10, 2 );

/* BreadcrumbList JSON-LD เรียกจาก single.php / page.php ปิดถ้ามีปลั๊ก SEO */
function fenix_breadcrumb_jsonld( $items ) {
	if ( fenix_has_seo_plugin() || empty( $items ) ) {
		return;
	}
	$list = array();
	$pos  = 1;
	foreach ( $items as $it ) {
		$entry = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => wp_strip_all_tags( $it['name'] ),
		);
		if ( ! empty( $it['url'] ) ) {
			$entry['item'] = $it['url'];
		}
		$list[] = $entry;
		$pos++;
	}
	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
}

/* ห่อ <table> ในเนื้อหาโพสต์ด้วย div ที่เลื่อนแนวนอนได้ (กันตารางล้นจอมือถือ) */
function fenix_wrap_content_tables( $content ) {
	if ( ! is_singular() || false === strpos( $content, '<table' ) ) {
		return $content;
	}
	$content = str_replace( '<table', '<div class="table-scroll"><table', $content );
	$content = str_replace( '</table>', '</table></div>', $content );
	return $content;
}
add_filter( 'the_content', 'fenix_wrap_content_tables', 20 );

/* --------------------------------------------------------------
 * Table of Contents · เก็บหัวข้อ H2/H3 จากเนื้อหาบทความ + ใส่ id ให้ลิงก์
 * ($GLOBALS['fenix_toc'] ถูกเติมตอน the_content ถูกประมวลผล)
 * -------------------------------------------------------------- */
function fenix_collect_toc( $content ) {
	if ( ! ( is_singular( 'post' ) && is_main_query() && in_the_loop() ) ) {
		return $content;
	}

	$GLOBALS['fenix_toc'] = array();
	$index                = 0;

	return preg_replace_callback(
		'/<(h[23])([^>]*)>(.*?)<\/\1>/is',
		function ( $matches ) use ( &$index ) {
			$index++;
			$tag   = strtolower( $matches[1] );
			$attrs = $matches[2];
			$inner = $matches[3];

			if ( preg_match( '/\bid=["\']([^"\']+)["\']/', $attrs, $id_match ) ) {
				$id = $id_match[1];
			} else {
				$id     = 'toc-' . $index;
				$attrs .= ' id="' . $id . '"';
			}

			$GLOBALS['fenix_toc'][] = array(
				'level' => (int) substr( $tag, 1 ),
				'text'  => trim( wp_strip_all_tags( $inner ) ),
				'id'    => $id,
			);

			return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'fenix_collect_toc', 20 );

/* ตัดคำนำหน้า "หมวดหมู่:" / "ป้ายกำกับ:" ออกจากหัวข้อหน้า archive */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/* --------------------------------------------------------------
 * การ์ดบทความ (ใช้ร่วมกันที่ index และ AJAX โหลดเพิ่ม)
 * -------------------------------------------------------------- */
function fenix_post_card() {
	$cats = get_the_category();
	$cat  = ! empty( $cats ) ? $cats[0] : null;
	?>
	<article <?php post_class( 'post-card' ); ?>>
		<?php if ( has_post_thumbnail() ) : ?>
			<a class="post-card-thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( fenix_featured_image_alt() ) ) ); ?>
				<?php if ( $cat ) : ?>
					<span class="post-card-cat"><?php echo esc_html( $cat->name ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="post-card-body">
			<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
			<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), fenix_excerpt_length() ) ); ?></p>
			<span class="post-card-more">อ่านต่อ <?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</div>
	</article>
	<?php
}

/* --------------------------------------------------------------
 * AJAX โหลดบทความเพิ่ม (ปุ่ม "โหลดเพิ่ม")
 * -------------------------------------------------------------- */
function fenix_load_more() {
	check_ajax_referer( 'fenix_load_more', 'nonce' );

	$page = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;

	$incoming = array();
	if ( isset( $_POST['query'] ) ) {
		$decoded = json_decode( wp_unslash( $_POST['query'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $decoded ) ) {
			$incoming = $decoded;
		}
	}

	// รับเฉพาะ query var ที่หน้าเว็บส่งมาได้จริง + sanitize ทีละค่า (กัน inject meta_query/tax_query หนัก ๆ)
	$query = array();
	if ( isset( $incoming['category_name'] ) ) {
		$query['category_name'] = sanitize_text_field( $incoming['category_name'] );
	}
	if ( isset( $incoming['cat'] ) ) {
		$query['cat'] = (int) $incoming['cat'];
	}
	if ( isset( $incoming['tag'] ) ) {
		$query['tag'] = sanitize_text_field( $incoming['tag'] );
	}
	if ( isset( $incoming['author'] ) ) {
		$query['author'] = (int) $incoming['author'];
	}
	if ( isset( $incoming['author_name'] ) ) {
		$query['author_name'] = sanitize_text_field( $incoming['author_name'] );
	}
	if ( isset( $incoming['s'] ) ) {
		$query['s'] = sanitize_text_field( $incoming['s'] );
	}

	// บังคับค่าที่ปลอดภัย ไม่ให้ฝั่ง client กำหนดเอง
	$query['paged']               = $page;
	$query['post_type']           = 'post';
	$query['post_status']         = 'publish';
	$query['posts_per_page']      = (int) get_option( 'posts_per_page' );
	$query['ignore_sticky_posts'] = true;

	$loop = new WP_Query( $query );
	if ( $loop->have_posts() ) {
		while ( $loop->have_posts() ) {
			$loop->the_post();
			fenix_post_card();
		}
	}
	wp_reset_postdata();
	wp_die();
}
add_action( 'wp_ajax_fenix_load_more', 'fenix_load_more' );
add_action( 'wp_ajax_nopriv_fenix_load_more', 'fenix_load_more' );

/* --------------------------------------------------------------
 * Production security hardening
 * -------------------------------------------------------------- */

/**
 * Disable comments and pingbacks even if an individual post is misconfigured.
 */
function fenix_disable_discussion_support() {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'trackbacks' );
	remove_post_type_support( 'page', 'comments' );
	remove_post_type_support( 'page', 'trackbacks' );
}
add_action( 'init', 'fenix_disable_discussion_support', 100 );

/**
 * เปิดให้แก้หัวข้อ SEO ของ Yoast ผ่าน REST ได้ (คนที่แก้โพสต์ได้เท่านั้น)
 *
 * Yoast เก็บค่าพวกนี้เป็น meta ที่ซ่อนไว้ ทำให้แก้จากนอกหน้าจอ WordPress ไม่ได้
 * ลงทะเบียนตรงนี้เพื่อให้ตรวจและเติมให้ครบทุกหน้าได้โดยไม่ต้องไล่เปิดทีละหน้า
 */
/**
 * ความยาวบทคัดย่อที่เหมาะกับภาษาที่ใช้
 *
 * WordPress ภาษาไทยนับ "คำ" เป็นตัวอักษร เลข 22 ที่เคยใส่ไว้จึงกลายเป็น 22 ตัวอักษร
 * ตัดกลางคำจนอ่านไม่รู้เรื่อง ฟังก์ชันนี้คืนค่าที่เหมาะกับแต่ละแบบ
 *
 * @param int $chars จำนวนตัวอักษรสำหรับภาษาที่นับเป็นตัวอักษร.
 * @param int $words จำนวนคำสำหรับภาษาที่นับเป็นคำ.
 * @return int
 */
function fenix_excerpt_length( $chars = 120, $words = 22 ) {
	$type = _x( 'words', 'Word count type. Do not translate!' );

	return ( 'words' === $type ) ? $words : $chars;
}

function fenix_register_seo_meta() {
	$fields = array( '_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_focuskw', '_yoast_wpseo_canonical', '_elementor_element_cache', '_elementor_css' );

	foreach ( array( 'post', 'page' ) as $type ) {
		foreach ( $fields as $field ) {
			register_post_meta(
				$type,
				$field,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'fenix_register_seo_meta' );
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

/**
 * Remove comment management links from the dashboard.
 */
function fenix_remove_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'fenix_remove_comments_admin_menu', 100 );

function fenix_remove_comments_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'fenix_remove_comments_admin_bar', 100 );

/**
 * Disable XML-RPC and remove its public discovery links.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'xmlrpc_methods', '__return_empty_array' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/**
 * กู้ header Authorization บนโฮสต์ที่ตัดทิ้ง (CGI/FastCGI/LiteSpeed)
 * เพื่อให้ Application Passwords ใช้กับ REST API ได้ · ไม่มีผลถ้า PHP เห็น header อยู่แล้ว
 */
function fenix_restore_authorization_header() {
	if ( ! empty( $_SERVER['HTTP_AUTHORIZATION'] ) || ! empty( $_SERVER['PHP_AUTH_USER'] ) ) {
		return;
	}

	$header = '';
	if ( ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
		$header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	} elseif ( function_exists( 'apache_request_headers' ) ) {
		$headers = apache_request_headers();
		if ( is_array( $headers ) ) {
			foreach ( $headers as $name => $value ) {
				if ( 'authorization' === strtolower( $name ) ) {
					$header = $value;
					break;
				}
			}
		}
	}

	// ทางเลือกสุดท้าย: ไคลเอนต์ส่งมาในชื่อ X-Authorization (โฮสต์บางเจ้าตัดเฉพาะชื่อ Authorization).
	if ( '' === $header && ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ) ) {
		$header = $_SERVER['HTTP_X_AUTHORIZATION']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	if ( '' === $header ) {
		return;
	}

	$_SERVER['HTTP_AUTHORIZATION'] = $header;
	if ( function_exists( 'wp_populate_basic_auth_from_authorization_header' ) ) {
		wp_populate_basic_auth_from_authorization_header();
	}
}
fenix_restore_authorization_header();

/**
 * จุดตรวจว่า header Authorization มาถึง PHP หรือไม่ · คืนเฉพาะ true/false ไม่มีข้อมูลลับ
 * GET /wp-json/fenix/v1/authcheck
 */
function fenix_register_authcheck_route() {
	register_rest_route(
		'fenix/v1',
		'/authcheck',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'fenix_authcheck_response',
		)
	);
}
add_action( 'rest_api_init', 'fenix_register_authcheck_route' );

function fenix_authcheck_response() {
	$apache_keys = array();
	if ( function_exists( 'apache_request_headers' ) ) {
		$apache_keys = array_map( 'strtolower', array_keys( (array) apache_request_headers() ) );
	}

	return array(
		'http_authorization'     => ! empty( $_SERVER['HTTP_AUTHORIZATION'] ),
		'redirect_authorization' => ! empty( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ),
		'x_authorization'        => ! empty( $_SERVER['HTTP_X_AUTHORIZATION'] ),
		'apache_authorization'   => in_array( 'authorization', $apache_keys, true ),
		'php_auth_user_set'      => isset( $_SERVER['PHP_AUTH_USER'] ),
		'logged_in_user_id'      => get_current_user_id(),
	);
}

/**
 * REST สำหรับผู้ดูแล: อ่าน/แก้ค่า Customizer (theme_mod) เฉพาะ key ที่มีใน fenix_defaults()
 * GET  /wp-json/fenix/v1/mods                      → ค่าปัจจุบันทุก key
 * POST /wp-json/fenix/v1/mods {"key":"value",...}  → บันทึก (ส่ง null = ล้างกลับเป็นค่าเริ่มต้น)
 * ต้องมีสิทธิ์ edit_theme_options (ผู้ดูแลระบบ) เท่านั้น
 */
function fenix_register_mods_route() {
	register_rest_route(
		'fenix/v1',
		'/mods',
		array(
			array(
				'methods'             => 'GET',
				'permission_callback' => 'fenix_mods_permission',
				'callback'            => 'fenix_mods_get',
			),
			array(
				'methods'             => 'POST',
				'permission_callback' => 'fenix_mods_permission',
				'callback'            => 'fenix_mods_update',
			),
		)
	);
}
add_action( 'rest_api_init', 'fenix_register_mods_route' );

function fenix_mods_permission() {
	return current_user_can( 'edit_theme_options' );
}

function fenix_mods_get() {
	$out = array();
	foreach ( array_keys( fenix_defaults() ) as $key ) {
		$out[ $key ] = fenix_mod( $key );
	}
	return $out;
}

function fenix_mods_sanitize( $key, $value, $default ) {
	if ( 'pricing_mode' === $key && function_exists( 'fenix_sanitize_pricing_mode' ) ) {
		return fenix_sanitize_pricing_mode( $value );
	}
	if ( is_bool( $default ) ) {
		return in_array( $value, array( true, 1, '1', 'true', 'on' ), true );
	}
	if ( preg_match( '/(_url|_img|_image|_logo)$/', $key ) ) {
		return esc_url_raw( (string) $value );
	}
	return sanitize_textarea_field( (string) $value );
}

function fenix_mods_update( WP_REST_Request $request ) {
	$defaults = fenix_defaults();
	$data     = $request->get_json_params();

	if ( ! is_array( $data ) || empty( $data ) ) {
		return new WP_Error( 'fenix_mods_empty', 'ส่ง JSON object {"key":"value"} อย่างน้อย 1 รายการ', array( 'status' => 400 ) );
	}

	$saved    = array();
	$rejected = array();
	foreach ( $data as $key => $value ) {
		if ( ! array_key_exists( $key, $defaults ) ) {
			$rejected[] = $key;
			continue;
		}
		if ( null === $value ) {
			remove_theme_mod( $key );
		} else {
			set_theme_mod( $key, fenix_mods_sanitize( $key, $value, $defaults[ $key ] ) );
		}
		$saved[ $key ] = fenix_mod( $key );
	}

	return array(
		'saved'    => $saved,
		'rejected' => $rejected,
	);
}

/**
 * Block the public REST user directory while keeping it available to editors.
 *
 * @param mixed           $result  Response to replace, or null.
 * @param WP_REST_Server  $server  REST server instance.
 * @param WP_REST_Request $request Current REST request.
 * @return mixed
 */
function fenix_restrict_public_user_rest_api( $result, $server, $request ) {
	unset( $server );

	if ( is_user_logged_in() ) {
		return $result;
	}

	if ( 0 === strpos( $request->get_route(), '/wp/v2/users' ) ) {
		return new WP_Error(
			'rest_user_directory_forbidden',
			__( 'The user directory is not public.', 'fenix-pro' ),
			array( 'status' => 403 )
		);
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'fenix_restrict_public_user_rest_api', 10, 3 );

/**
 * Remove unnecessary disclosure headers and disable sensitive browser APIs.
 *
 * @param array $headers Response headers.
 * @return array
 */
function fenix_harden_response_headers( $headers ) {
	unset( $headers['X-Pingback'] );
	$headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';
	return $headers;
}
add_filter( 'wp_headers', 'fenix_harden_response_headers', 20 );

function fenix_remove_powered_by_header() {
	if ( function_exists( 'header_remove' ) ) {
		header_remove( 'X-Powered-By' );
	}
}
add_action( 'send_headers', 'fenix_remove_powered_by_header', 100 );

/* --------------------------------------------------------------
 * Customizer
 * -------------------------------------------------------------- */
require get_template_directory() . '/inc/customizer.php';

/**
 * ปุ่ม "สอบถามทาง LINE" ใต้แดชบอร์ดหน้า Backtest มาจากปลั๊กอิน FENIX Performance
 * Dashboard Pro ซึ่งฝัง URL ของ Myfxbook ตัวเก่าไว้ผิด ลูกค้ากดแล้วหลุดออกจากเว็บ
 * ตัวกรองนี้บังคับให้ปุ่มชี้กลับไปที่ LINE ตามค่าใน Customizer จนกว่าจะแก้ที่ปลั๊กอิน
 */
function fenix_fix_dashboard_line_button( $output, $tag ) {
	if ( 'fenix_dashboard_pro' !== $tag || '' === trim( (string) $output ) ) {
		return $output;
	}

	$line_url = trim( (string) fenix_mod( 'line_url' ) );
	if ( '' === $line_url ) {
		return $output;
	}

	$fixed = preg_replace(
		'~(<a\b[^>]*?)href="[^"]*"([^>]*>\s*สอบถามทาง\s*LINE\s*</a>)~u',
		'$1href="' . esc_url( $line_url ) . '"$2',
		$output
	);

	return null === $fixed ? $output : $fixed;
}
add_filter( 'do_shortcode_tag', 'fenix_fix_dashboard_line_button', 10, 2 );
