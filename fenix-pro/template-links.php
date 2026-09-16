<?php
/**
 * Template Name: FENIX · หน้า Link Hub (ยิงแอด)
 *
 * หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด (Facebook/Google/TikTok Ads)
 * เป็นหน้าแบบ standalone: ไม่มีเมนูบน/ฟุตเตอร์เว็บ
 * โครงหน้า: ขั้นตอนเริ่มใช้งานแบบมีเลขกำกับ → ข้อมูลก่อนตัดสินใจ → ติดขั้นตอนไหน ทักทีม
 * ขั้นตอนที่ไม่มีปุ่มหรือเนื้อหาจะถูกซ่อน และเลขขั้นตอนจะเรียงใหม่อัตโนมัติ
 * แก้ข้อความ/ปุ่ม/ลิงก์ทั้งหมดได้ที่ ปรับแต่ง → FENIX PRO → หมวด "หน้า Link Hub"
 *
 * @package fenix-pro
 */

$lh_logo = fenix_mod( 'links_logo' );
if ( ! $lh_logo ) {
	$lh_logo = fenix_logo_url();
}

$lh_title  = fenix_mod( 'links_title' );
$lh_tag    = fenix_mod( 'links_tagline' );
$lh_badges = fenix_lines( fenix_mod( 'links_badges' ) );
$lh_note   = fenix_mod( 'links_note' );
$lh_line   = fenix_mod( 'line_url' );
$lh_signup_url   = trim( (string) fenix_mod( 'links_signup_url' ) );
$lh_signup_label = trim( (string) fenix_mod( 'links_signup_label' ) );
$lh_account_guide_url   = trim( (string) fenix_mod( 'links_account_guide_url' ) );
$lh_account_guide_label = trim( (string) fenix_mod( 'links_account_guide_label' ) );
$lh_mt5_download_url    = trim( (string) fenix_mod( 'links_mt5_download_url' ) );
$lh_mt5_download_label  = trim( (string) fenix_mod( 'links_mt5_download_label' ) );
$lh_openchat_url   = trim( (string) fenix_mod( 'links_openchat_url' ) );
$lh_openchat_label = trim( (string) fenix_mod( 'links_openchat_label' ) );
$lh_deposit_url         = trim( (string) fenix_mod( 'links_deposit_url' ) );
$lh_deposit_label       = trim( (string) fenix_mod( 'links_deposit_label' ) );
$lh_deposit_guide_url   = trim( (string) fenix_mod( 'links_deposit_guide_url' ) );
$lh_deposit_guide_label = trim( (string) fenix_mod( 'links_deposit_guide_label' ) );
$lh_top_line_label      = trim( (string) fenix_mod( 'links_top_line_label' ) );

if ( '' === $lh_account_guide_url || '#' === $lh_account_guide_url ) {
	$lh_account_guide_url = '/open-mt5-account/';
}

if ( '' === $lh_mt5_download_url || '#' === $lh_mt5_download_url ) {
	$lh_mt5_download_url = '/mt5-login-zaurix-server/';
}
if ( in_array( $lh_mt5_download_label, array( '', 'โหลด MT5', 'คู่มือติดตั้งและเข้า MT5', 'คู่มือติดตั้ง MT5' ), true ) ) {
	$lh_mt5_download_label = 'วิธี Login เข้า Zaurix-Server';
}
if ( '#' === $lh_deposit_url ) {
	$lh_deposit_url = '';
}
if ( '#' === $lh_deposit_guide_url ) {
	$lh_deposit_guide_url = '';
}

/* ไอคอนประกอบปุ่มตามลำดับ (ตกแต่ง · เปลี่ยนความหมายปุ่มได้โดยไม่ผูกกับไอคอน) */
$lh_btn_icons = array( 1 => 'chart', 2 => 'tag', 3 => 'download', 4 => 'layout', 5 => 'arrow', 6 => 'arrow' );
$lh_guide_icons = array( 1 => 'download', 2 => 'windows', 3 => 'android', 4 => 'apple', 5 => 'macos' );

$lh_socials = array(
	'facebook'  => array( fenix_mod( 'facebook_url' ), 'Facebook' ),
	'instagram' => array( fenix_mod( 'instagram_url' ), 'Instagram' ),
	'tiktok'    => array( fenix_mod( 'tiktok_url' ), 'TikTok' ),
	'youtube'   => array( fenix_mod( 'youtube_url' ), 'YouTube' ),
);

/* แอป MT5 ตามอุปกรณ์ (ขั้นตอนติดตั้ง MT5) */
$lh_mt5_apps = array();
foreach ( array( 'ios' => 'apple', 'android' => 'android', 'windows' => 'windows', 'macos' => 'macos' ) as $lh_os => $lh_os_icon ) {
	$lh_os_url   = trim( (string) fenix_mod( 'links_mt5_' . $lh_os . '_url' ) );
	$lh_os_label = trim( (string) fenix_mod( 'links_mt5_' . $lh_os . '_label' ) );
	if ( '' !== $lh_os_url && '#' !== $lh_os_url && '' !== $lh_os_label ) {
		$lh_mt5_apps[] = array(
			'url'   => $lh_os_url,
			'label' => $lh_os_label,
			'icon'  => $lh_os_icon,
		);
	}
}

/* คู่มือ: แยกคู่มือติดตั้ง EA ออกจากคู่มือ VPS */
$lh_guides = array();
for ( $lh_i = 1; $lh_i <= 5; $lh_i++ ) {
	$lh_label = trim( (string) fenix_mod( 'links_guide' . $lh_i . '_label' ) );
	$lh_url   = trim( (string) fenix_mod( 'links_guide' . $lh_i . '_url' ) );
	if ( '' === $lh_label ) {
		continue;
	}
	$lh_guides[] = array(
		'label' => $lh_label,
		'url'   => $lh_url,
		'icon'  => isset( $lh_guide_icons[ $lh_i ] ) ? $lh_guide_icons[ $lh_i ] : 'arrow',
	);
}
$lh_setup_guides = array();
$lh_vps_guides   = array();
foreach ( $lh_guides as $lh_guide ) {
	$lh_guide_url_norm = strtolower( rtrim( $lh_guide['url'], '/' ) );
	if ( false !== strpos( $lh_guide_url_norm, 'vps-' ) || false !== stripos( $lh_guide['label'], 'VPS' ) ) {
		$lh_vps_guides[] = $lh_guide;
	} else {
		$lh_setup_guides[] = $lh_guide;
	}
}

/*
 * เก็บเนื้อหาแต่ละขั้นตอนไว้ก่อน (output buffer) ขั้นไหนว่างจะไม่แสดง
 * แล้วจึงใส่เลขขั้นตอนตามลำดับที่เหลือจริง
 */
$lh_steps = array();

// ขั้นตอน: เปิดบัญชี.
ob_start();
if ( $lh_signup_url && $lh_signup_label ) :
	?>
	<a class="lh-btn lh-btn-signup" href="<?php echo esc_url( fenix_link_url( $lh_signup_url ) ); ?>">
		<span class="lh-ic lh-account-ic"><?php echo fenix_icon( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-signup-copy">
			<span class="lh-lbl"><?php echo esc_html( $lh_signup_label ); ?></span>
		</span>
		<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
	</a>
	<?php
endif;
if ( $lh_account_guide_url && $lh_account_guide_label ) :
	?>
	<a class="lh-mini-btn" href="<?php echo esc_url( fenix_link_url( $lh_account_guide_url ) ); ?>">
		<span class="lh-ic"><?php echo fenix_icon( 'guide' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo esc_html( $lh_account_guide_label ); ?></span>
	</a>
	<?php
endif;
$lh_steps[] = array( 'key' => 'account', 'n' => 1, 'body' => ob_get_clean() );

// ขั้นตอน: ติดตั้ง MT5 และ Login.
ob_start();
if ( $lh_mt5_apps ) :
	?>
	<div class="lh-vps-grid lh-mt5-grid lh-mt5-grid--<?php echo esc_attr( (string) count( $lh_mt5_apps ) ); ?>">
		<?php foreach ( $lh_mt5_apps as $lh_app ) : ?>
			<a class="lh-vps-item lh-mt5-item" href="<?php echo esc_url( fenix_link_url( $lh_app['url'] ) ); ?>" target="_blank" rel="noopener">
				<span class="lh-vps-ic"><?php echo fenix_icon( $lh_app['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-vps-lbl"><?php echo esc_html( $lh_app['label'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
endif;
if ( $lh_mt5_download_url && $lh_mt5_download_label ) :
	?>
	<a class="lh-mini-btn" href="<?php echo esc_url( fenix_link_url( $lh_mt5_download_url ) ); ?>">
		<span class="lh-ic"><?php echo fenix_icon( 'guide' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo esc_html( $lh_mt5_download_label ); ?></span>
	</a>
	<?php
endif;
$lh_steps[] = array( 'key' => 'mt5', 'n' => 2, 'body' => ob_get_clean() );

// ขั้นตอน: ฝากเงิน.
ob_start();
if ( $lh_deposit_url && $lh_deposit_label ) :
	?>
	<a class="lh-btn lh-btn-deposit" href="<?php echo esc_url( fenix_link_url( $lh_deposit_url ) ); ?>" target="_blank" rel="noopener">
		<span class="lh-ic"><?php echo fenix_icon( 'account' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-lbl"><?php echo esc_html( $lh_deposit_label ); ?></span>
		<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
	</a>
	<?php
endif;
if ( $lh_deposit_guide_url && $lh_deposit_guide_label ) :
	?>
	<a class="lh-mini-btn" href="<?php echo esc_url( fenix_link_url( $lh_deposit_guide_url ) ); ?>">
		<span class="lh-ic"><?php echo fenix_icon( 'guide' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span><?php echo esc_html( $lh_deposit_guide_label ); ?></span>
	</a>
	<?php
endif;
$lh_steps[] = array( 'key' => 'deposit', 'n' => 3, 'body' => ob_get_clean() );

// ขั้นตอน: ดาวน์โหลด EA (FAST / การ์ดเดิม / PLUS).
ob_start();
if ( fenix_mod( 'links_fast_enabled' ) ) :
	$lh_fast_img = trim( (string) fenix_mod( 'links_fast_img' ) );
	$lh_fast_url = trim( (string) fenix_mod( 'links_fast_url' ) );
	if ( fenix_mod( 'links_fast_show' ) && $lh_fast_img && $lh_fast_url ) :
		?>
		<div class="lh-feature lh-feature-fast">
			<a class="lh-feature-frame" href="<?php echo esc_url( fenix_link_url( $lh_fast_url ) ); ?>" download>
				<img src="<?php echo esc_url( $lh_fast_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'links_fast_alt' ) ); ?>" width="1200" height="631" loading="lazy">
			</a>
		</div>
		<?php
	endif;
else :
	$lh_default_feat_img = get_template_directory_uri() . '/assets/img/link-download-fenix-pro-ea.png';
	$lh_default_feat_url = get_template_directory_uri() . '/assets/downloads/fenix-pro-ea.zip';
	$lh_feat_img         = trim( (string) fenix_mod( 'links_feature_img' ) );
	$lh_feat_placeholder = preg_match( '~(?:placehold|placeholder|dummyimage|1200[^/?#]*(?:x|×|-|_)[^/?#]*630)~i', $lh_feat_img );
	if ( '' === $lh_feat_img || $lh_feat_placeholder ) {
		$lh_feat_img = $lh_default_feat_img;
	}
	if ( $lh_feat_img ) :
		$lh_feat_url     = trim( (string) fenix_mod( 'links_feature_url' ) );
		$lh_feat_caption = fenix_mod( 'links_feature_caption' );
		if ( '' === $lh_feat_url || fenix_mod( 'line_url' ) === $lh_feat_url ) {
			$lh_feat_url = $lh_default_feat_url;
		}
		$lh_feat_has_url = ( '' !== $lh_feat_url );
		?>
		<div class="lh-feature">
			<?php if ( $lh_feat_has_url ) : ?>
			<a class="lh-feature-frame" href="<?php echo esc_url( fenix_link_url( $lh_feat_url ) ); ?>">
			<?php else : ?>
			<span class="lh-feature-frame">
			<?php endif; ?>
				<img src="<?php echo esc_url( $lh_feat_img ); ?>" alt="<?php echo esc_attr( $lh_feat_caption ? $lh_feat_caption : $lh_title ); ?>" loading="lazy">
			<?php if ( $lh_feat_has_url ) : ?>
			</a>
			<?php else : ?>
			</span>
			<?php endif; ?>
			<?php if ( $lh_feat_caption ) : ?>
				<span class="lh-feature-cap"><?php echo esc_html( $lh_feat_caption ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	endif;

	$lh_feat2_img     = trim( (string) fenix_mod( 'links_feature2_img' ) );
	$lh_feat2_url     = trim( (string) fenix_mod( 'links_feature2_url' ) );
	$lh_feat2_caption = fenix_mod( 'links_feature2_caption' );
	$lh_feat2_has_url = ( '' !== $lh_feat2_url );
	?>
	<div class="lh-feature lh-feature-secondary">
		<?php if ( $lh_feat2_has_url ) : ?>
		<a class="lh-feature-frame" href="<?php echo esc_url( fenix_link_url( $lh_feat2_url ) ); ?>">
		<?php else : ?>
		<span class="lh-feature-frame">
		<?php endif; ?>
			<?php if ( $lh_feat2_img ) : ?>
				<img src="<?php echo esc_url( $lh_feat2_img ); ?>" alt="<?php echo esc_attr( $lh_feat2_caption ? $lh_feat2_caption : $lh_title ); ?>" loading="lazy">
			<?php else : ?>
				<span class="lh-feature-placeholder"><?php echo esc_html( fenix_mod( 'links_feature2_placeholder' ) ); ?></span>
			<?php endif; ?>
		<?php if ( $lh_feat2_has_url ) : ?>
		</a>
		<?php else : ?>
		</span>
		<?php endif; ?>
		<?php if ( $lh_feat2_caption ) : ?>
			<span class="lh-feature-cap"><?php echo esc_html( $lh_feat2_caption ); ?></span>
		<?php endif; ?>
	</div>
	<?php
endif;
if ( fenix_mod( 'links_plus_enabled' ) ) :
	$lh_plus_img = trim( (string) fenix_mod( 'links_plus_img' ) );
	$lh_plus_url = trim( (string) fenix_mod( 'links_plus_url' ) );
	if ( $lh_plus_img && $lh_plus_url ) :
		?>
		<div class="lh-feature lh-feature-plus">
			<a class="lh-feature-frame" href="<?php echo esc_url( fenix_link_url( $lh_plus_url ) ); ?>" download>
				<img src="<?php echo esc_url( $lh_plus_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'links_plus_alt' ) ); ?>" width="1200" height="675" loading="lazy">
			</a>
		</div>
		<?php
	endif;
endif;
$lh_steps[] = array( 'key' => 'download', 'n' => 4, 'body' => ob_get_clean() );

// ขั้นตอน: ติดตั้ง EA บน MT5.
ob_start();
foreach ( $lh_setup_guides as $lh_guide ) :
	?>
	<a class="lh-btn" href="<?php echo esc_url( fenix_link_url( $lh_guide['url'] ) ); ?>">
		<span class="lh-ic"><?php echo fenix_icon( $lh_guide['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-lbl"><?php echo esc_html( $lh_guide['label'] ); ?></span>
		<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
	</a>
	<?php
endforeach;
$lh_steps[] = array( 'key' => 'install', 'n' => 5, 'body' => ob_get_clean() );

// ขั้นตอน: VPS.
ob_start();
if ( $lh_vps_guides ) :
	?>
	<div class="lh-vps-grid">
		<?php foreach ( $lh_vps_guides as $lh_guide ) : ?>
			<?php
			$lh_vps_label = $lh_guide['label'];
			if ( false !== stripos( $lh_vps_label, 'Windows' ) ) {
				$lh_vps_label = 'Windows';
			} elseif ( false !== stripos( $lh_vps_label, 'Android' ) ) {
				$lh_vps_label = 'Android';
			} elseif ( false !== stripos( $lh_vps_label, 'iPhone' ) || false !== stripos( $lh_vps_label, 'iOS' ) ) {
				$lh_vps_label = 'iOS';
			} elseif ( false !== stripos( $lh_vps_label, 'macOS' ) || false !== stripos( $lh_vps_label, 'Mac' ) ) {
				$lh_vps_label     = 'macOS';
				$lh_guide['icon'] = 'macos';
			}
			$lh_vps_has_url = ( '' !== $lh_guide['url'] && '#' !== $lh_guide['url'] );
			?>
			<?php if ( $lh_vps_has_url ) : ?>
			<a class="lh-vps-item" href="<?php echo esc_url( fenix_link_url( $lh_guide['url'] ) ); ?>">
			<?php else : ?>
			<span class="lh-vps-item lh-vps-item-pending" aria-disabled="true">
			<?php endif; ?>
				<span class="lh-vps-ic"><?php echo fenix_icon( $lh_guide['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-vps-lbl"><?php echo esc_html( $lh_vps_label ); ?></span>
			<?php if ( $lh_vps_has_url ) : ?>
			</a>
			<?php else : ?>
			</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php
endif;
$lh_steps[] = array( 'key' => 'vps', 'n' => 6, 'body' => ob_get_clean() );

$lh_steps = array_values(
	array_filter(
		$lh_steps,
		function ( $lh_step ) {
			return '' !== trim( $lh_step['body'] );
		}
	)
);

// กลุ่ม: ข้อมูลก่อนตัดสินใจ (ปุ่ม 1 ถึง 6).
ob_start();
for ( $lh_i = 1; $lh_i <= 6; $lh_i++ ) :
	$lh_label = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_label' ) );
	$lh_url   = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_url' ) );
	if ( '' === $lh_label || '' === $lh_url ) {
		continue;
	}
	$lh_url_norm = strtolower( rtrim( $lh_url, '/' ) );
	if ( 'วิธีติดตั้ง EA บน MT5' === $lh_label || '/how-to-install' === $lh_url_norm ) {
		continue;
	}
	if ( '/articles' === $lh_url_norm || false !== strpos( $lh_label, 'บทความ' ) ) {
		continue;
	}
	$lh_icon = isset( $lh_btn_icons[ $lh_i ] ) ? $lh_btn_icons[ $lh_i ] : 'arrow';
	?>
	<a class="lh-btn" href="<?php echo esc_url( fenix_link_url( $lh_url ) ); ?>">
		<span class="lh-ic"><?php echo fenix_icon( $lh_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<span class="lh-lbl"><?php echo esc_html( $lh_label ); ?></span>
		<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
	</a>
	<?php
endfor;
$lh_info_body = ob_get_clean();

$lh_steps_title = str_replace( '{n}', (string) count( $lh_steps ), trim( (string) fenix_mod( 'links_steps_title' ) ) );
$lh_info_title  = trim( (string) fenix_mod( 'links_info_title' ) );
$lh_help_title  = trim( (string) fenix_mod( 'links_help_title' ) );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'link-hub-page' ); ?>>
<?php wp_body_open(); ?>

<main class="link-hub">
	<div class="lh-card">

		<div class="lh-logo">
			<?php if ( $lh_logo ) : ?>
				<img src="<?php echo esc_url( $lh_logo ); ?>" alt="<?php echo esc_attr( $lh_title ); ?>" width="84" height="84">
			<?php endif; ?>
		</div>

		<h1 class="lh-title"><?php echo esc_html( $lh_title ); ?></h1>

		<?php if ( $lh_tag ) : ?>
			<p class="lh-tagline"><?php echo nl2br( esc_html( $lh_tag ) ); ?></p>
		<?php endif; ?>

		<?php if ( $lh_badges ) : ?>
			<ul class="lh-badges">
				<?php foreach ( $lh_badges as $lh_b ) : ?>
					<li><?php echo esc_html( $lh_b ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $lh_top_line_label && $lh_line ) : ?>
			<a class="lh-top-line" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener">
				<span class="lh-ic"><?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span><?php echo esc_html( $lh_top_line_label ); ?></span>
				<span class="lh-ar" aria-hidden="true">&rsaquo;</span>
			</a>
		<?php endif; ?>

		<?php if ( $lh_steps ) : ?>
			<section class="lh-journey"<?php echo $lh_steps_title ? ' aria-label="' . esc_attr( $lh_steps_title ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( $lh_steps_title ) : ?>
					<h2 class="lh-steps-title"><?php echo esc_html( $lh_steps_title ); ?></h2>
				<?php endif; ?>
				<ol class="lh-steps">
					<?php foreach ( $lh_steps as $lh_idx => $lh_step ) : ?>
						<?php
						$lh_step_title = trim( (string) fenix_mod( 2 === $lh_step['n'] ? 'links_mt5_install_title' : 'links_step' . $lh_step['n'] . '_title' ) );
						$lh_step_desc  = trim( (string) fenix_mod( 'links_step' . $lh_step['n'] . '_desc' ) );
						$lh_step_badge = trim( (string) fenix_mod( 'links_step' . $lh_step['n'] . '_badge' ) );
						?>
						<li class="lh-step lh-step--<?php echo esc_attr( $lh_step['key'] ); ?>">
							<span class="lh-step-num" aria-hidden="true"><?php echo esc_html( (string) ( $lh_idx + 1 ) ); ?></span>
							<div class="lh-step-head">
								<h3 class="lh-step-title">
									<span class="screen-reader-text"><?php echo esc_html( 'ขั้นที่ ' . ( $lh_idx + 1 ) . ' ' ); ?></span><?php echo esc_html( $lh_step_title ); ?>
									<?php if ( $lh_step_badge ) : ?>
										<span class="lh-step-badge"><?php echo esc_html( $lh_step_badge ); ?></span>
									<?php endif; ?>
								</h3>
								<?php if ( $lh_step_desc ) : ?>
									<p class="lh-step-desc"><?php echo esc_html( $lh_step_desc ); ?></p>
								<?php endif; ?>
							</div>
							<div class="lh-step-body">
								<?php echo $lh_step['body']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>
		<?php endif; ?>

		<?php if ( '' !== trim( $lh_info_body ) ) : ?>
			<section class="lh-group lh-group--info"<?php echo $lh_info_title ? ' aria-label="' . esc_attr( $lh_info_title ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php if ( $lh_info_title ) : ?>
					<h2 class="lh-section-title"><?php echo esc_html( $lh_info_title ); ?></h2>
				<?php endif; ?>
				<?php echo $lh_info_body; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</section>
		<?php endif; ?>

		<section class="lh-group lh-group--help"<?php echo $lh_help_title ? ' aria-label="' . esc_attr( $lh_help_title ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
			<?php if ( $lh_help_title ) : ?>
				<h2 class="lh-section-title"><?php echo esc_html( $lh_help_title ); ?></h2>
			<?php endif; ?>

			<a class="lh-btn lh-btn-line" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener">
				<span class="lh-ic"><?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<span class="lh-lbl"><?php echo esc_html( fenix_mod( 'links_line_label' ) ); ?></span>
			</a>

			<?php if ( $lh_openchat_url && $lh_openchat_label ) : ?>
				<a class="lh-btn lh-btn-openchat" href="<?php echo esc_url( $lh_openchat_url ); ?>" target="_blank" rel="noopener">
					<span class="lh-ic"><?php echo fenix_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="lh-lbl"><?php echo esc_html( $lh_openchat_label ); ?></span>
				</a>
			<?php endif; ?>
		</section>

		<div class="lh-socials">
			<?php foreach ( $lh_socials as $lh_name => $lh_s ) : ?>
				<?php
				$lh_surl   = trim( (string) $lh_s[0] );
				$lh_shref  = '' !== $lh_surl ? $lh_surl : '#';
				$lh_sblank = '' !== $lh_surl ? ' target="_blank" rel="noopener"' : '';
				?>
				<a class="lh-soc lh-soc--<?php echo esc_attr( $lh_name ); ?>" href="<?php echo esc_url( $lh_shref ); ?>"<?php echo $lh_sblank; // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( $lh_s[1] ); ?>">
					<?php echo fenix_icon( $lh_name ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endforeach; ?>
		</div>

		<?php if ( $lh_note ) : ?>
			<p class="lh-note"><?php echo esc_html( $lh_note ); ?></p>
		<?php endif; ?>

	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
