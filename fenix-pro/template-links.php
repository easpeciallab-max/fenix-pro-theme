<?php
/**
 * Template Name: FENIX · หน้า Link Hub (ยิงแอด)
 *
 * หน้า /go/ · ปลายทางยิงแอด (Facebook/Google/TikTok Ads)
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 16 พ.ย. 2026
 *
 * เป็นหน้าแบบ standalone: ไม่มีเมนูบนและฟุตเตอร์ของเว็บ แต่ยังเรียก
 * wp_head() / wp_body_open() / wp_footer() ครบ เพื่อให้ Facebook Pixel
 * และปลั๊กอินอื่นทำงานเหมือนเดิม
 *
 * โครงหน้า: คอลัมน์ซ้าย (โปรไฟล์ · ติดต่อ · ข้อมูลก่อนตัดสินใจ · โซเชียล · คำเตือน)
 * และคอลัมน์ขวา (หัวข้อ · ทางลัด · 6 ขั้นตอน)
 * ข้อความ ปุ่ม และลิงก์ทั้งหมดแก้ได้ที่ ปรับแต่ง → FENIX PRO → หมวด "หน้า Link Hub"
 *
 * @package fenix-pro
 */

$lh_logo = trim( (string) fenix_mod( 'links_logo' ) );
if ( '' === $lh_logo ) {
	$lh_logo = fenix_logo_url();
}

$lh_line = trim( (string) fenix_mod( 'line_url' ) );
$lh_home = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

/* ---------- ช่องเลือกอุปกรณ์ ขั้นที่ 2 ---------- */
$lh_mt5_apps = array();
foreach ( array(
	'ios'     => array( 'apple', 'ios' ),
	'android' => array( 'android', 'android' ),
	'windows' => array( 'windows', 'win' ),
	'macos'   => array( 'macos', 'mac' ),
) as $lh_os => $lh_meta ) {
	$lh_os_url   = trim( (string) fenix_mod( 'links_mt5_' . $lh_os . '_url' ) );
	$lh_os_label = trim( (string) fenix_mod( 'links_mt5_' . $lh_os . '_label' ) );

	if ( '' === $lh_os_url || '#' === $lh_os_url || '' === $lh_os_label ) {
		continue;
	}

	$lh_mt5_apps[] = array(
		'url'    => $lh_os_url,
		'label'  => $lh_os_label,
		'icon'   => $lh_meta[0],
		'device' => $lh_meta[1],
	);
}

/* ---------- คู่มือ VPS ขั้นที่ 6 (ชื่อสั้นบนช่อง · ลิงก์จากคู่มือเดิม) ---------- */
$lh_vps_short = fenix_lines( fenix_mod( 'links_vps_labels' ) );
$lh_vps_meta  = array(
	2 => array( 'windows', 'win' ),
	3 => array( 'android', 'android' ),
	4 => array( 'apple', 'ios' ),
);
$lh_vps = array();
foreach ( $lh_vps_meta as $lh_i => $lh_meta ) {
	$lh_url = trim( (string) fenix_mod( 'links_guide' . $lh_i . '_url' ) );

	if ( '' === $lh_url || '#' === $lh_url ) {
		continue;
	}

	$lh_n     = count( $lh_vps );
	$lh_vps[] = array(
		'url'    => $lh_url,
		'label'  => isset( $lh_vps_short[ $lh_n ] ) ? $lh_vps_short[ $lh_n ] : fenix_mod( 'links_guide' . $lh_i . '_label' ),
		'icon'   => $lh_meta[0],
		'device' => $lh_meta[1],
	);
}

/* ---------- การ์ดดาวน์โหลด EA (เรียงตามเลขเวอร์ชันจากน้อยไปมาก) ---------- */
$lh_cards    = fenix_download_cards();
$lh_dl_label = trim( (string) fenix_mod( 'links_download_label' ) );
$lh_ver_lbl  = trim( (string) fenix_mod( 'links_plus_version_label' ) );

/* ---------- ทางลัด · ชิปข้อเท็จจริง ---------- */
$lh_shortcuts = fenix_rows( fenix_mod( 'links_shortcuts' ), 3 );
$lh_facts     = fenix_rows( fenix_mod( 'links_badges' ), 2 );

$lh_yours = trim( (string) fenix_mod( 'links_device_badge' ) );
$lh_dest  = trim( (string) fenix_mod( 'links_dest_prefix' ) );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'link-hub-page' ); ?>>
<?php wp_body_open(); ?>

<div class="fxgo">
<div class="fx-page">

	<?php /* ============ คอลัมน์ซ้าย ============ */ ?>
	<aside class="left">

		<section class="fx-card profile">
			<div class="cover" aria-hidden="true">
				<?php if ( fenix_mod( 'links_cover_wm' ) ) : ?>
					<div class="wm"><?php echo esc_html( fenix_mod( 'links_cover_wm' ) ); ?></div>
				<?php endif; ?>
				<?php if ( fenix_mod( 'links_cover_tape' ) ) : ?>
					<div class="tape"><?php echo esc_html( fenix_mod( 'links_cover_tape' ) ); ?></div>
				<?php endif; ?>
			</div>

			<div class="avatar">
				<img src="<?php echo esc_url( $lh_logo ); ?>" alt="<?php echo esc_attr( fenix_mod( 'links_title' ) ); ?>" width="108" height="108">
			</div>

			<h1><?php echo esc_html( fenix_mod( 'links_title' ) ); ?></h1>

			<?php if ( fenix_mod( 'links_tagline' ) ) : ?>
				<p class="tag"><?php echo esc_html( fenix_mod( 'links_tagline' ) ); ?></p>
			<?php endif; ?>

			<?php if ( $lh_facts ) : ?>
				<div class="facts">
					<?php foreach ( $lh_facts as $lh_fact ) : ?>
						<span>
							<?php if ( '' !== $lh_fact['cells'][1] ) : ?>
								<b><?php echo esc_html( $lh_fact['cells'][0] ); ?></b><?php echo esc_html( $lh_fact['cells'][1] ); ?>
							<?php else : ?>
								<?php echo esc_html( $lh_fact['cells'][0] ); ?>
							<?php endif; ?>
						</span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>

		<section class="fx-card contact">
			<?php if ( fenix_mod( 'links_help_title' ) ) : ?>
				<div class="hd"><span class="eyebrow"><?php echo esc_html( fenix_mod( 'links_help_title' ) ); ?></span></div>
			<?php endif; ?>

			<?php if ( '' !== $lh_line && fenix_mod( 'links_line_label' ) ) : ?>
				<a class="fx-btn fx-btn-line split" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener">
					<span class="bi"><?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php echo esc_html( fenix_mod( 'links_line_label' ) ); ?>
					<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endif; ?>

			<?php
			$lh_oc_url   = trim( (string) fenix_mod( 'links_openchat_url' ) );
			$lh_oc_label = trim( (string) fenix_mod( 'links_openchat_label' ) );
			if ( '' !== $lh_oc_url && '' !== $lh_oc_label ) :
				?>
				<a class="fx-btn fx-btn-dark split" href="<?php echo esc_url( $lh_oc_url ); ?>" target="_blank" rel="noopener">
					<span class="bi" style="color:var(--line-green)"><?php echo fenix_icon( 'users', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php echo esc_html( $lh_oc_label ); ?>
					<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			<?php endif; ?>
		</section>

		<?php
		$lh_rows = array();
		foreach ( array( 1 => 'chart', 2 => 'tag' ) as $lh_i => $lh_icon ) {
			$lh_label = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_label' ) );
			$lh_url   = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_url' ) );

			if ( '' === $lh_label || '' === $lh_url ) {
				continue;
			}

			$lh_rows[] = array(
				'label' => $lh_label,
				'sub'   => trim( (string) fenix_mod( 'links_btn' . $lh_i . '_sub' ) ),
				'url'   => $lh_url,
				'icon'  => $lh_icon,
			);
		}
		?>
		<?php if ( $lh_rows ) : ?>
			<section class="fx-card info">
				<?php if ( fenix_mod( 'links_info_title' ) ) : ?>
					<h3><?php echo esc_html( fenix_mod( 'links_info_title' ) ); ?></h3>
				<?php endif; ?>

				<?php
				foreach ( $lh_rows as $lh_row ) :
					$lh_ext = fenix_go_is_external( fenix_link_url( $lh_row['url'] ), $lh_home );
					?>
					<a class="lrow" href="<?php echo esc_url( fenix_link_url( $lh_row['url'] ) ); ?>"<?php echo fenix_go_target( $lh_ext ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="ic"><?php echo fenix_icon( $lh_row['icon'], 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div>
							<b><?php echo esc_html( $lh_row['label'] ); ?></b>
							<?php if ( '' !== $lh_row['sub'] ) : ?>
								<small><?php echo esc_html( $lh_row['sub'] ); ?></small>
							<?php endif; ?>
						</div>
						<?php echo fenix_icon( $lh_ext ? 'ext' : 'chev-r', 'i chev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>

		<?php
		$lh_socials = array(
			'facebook'  => array( fenix_mod( 'facebook_url' ), 'Facebook' ),
			'instagram' => array( fenix_mod( 'instagram_url' ), 'Instagram' ),
			'tiktok'    => array( fenix_mod( 'tiktok_url' ), 'TikTok' ),
			'youtube'   => array( fenix_mod( 'youtube_url' ), 'YouTube' ),
		);
		$lh_socials = array_filter(
			$lh_socials,
			function ( $lh_s ) {
				return '' !== trim( (string) $lh_s[0] );
			}
		);
		?>
		<?php if ( $lh_socials ) : ?>
			<div class="soc">
				<?php foreach ( $lh_socials as $lh_name => $lh_s ) : ?>
					<a href="<?php echo esc_url( $lh_s[0] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $lh_s[1] ); ?>">
						<?php echo fenix_icon( $lh_name, 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( fenix_mod( 'links_risk' ) ) : ?>
			<div class="risk">
				<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( fenix_mod( 'links_risk' ) ); ?></span>
			</div>
		<?php endif; ?>
	</aside>

	<?php /* ============ คอลัมน์ขวา ============ */ ?>
	<main class="main">

		<div class="mhead">
			<?php if ( fenix_mod( 'links_steps_kicker' ) ) : ?>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'links_steps_kicker' ) ); ?></span>
			<?php endif; ?>
			<h2><?php echo esc_html( str_replace( '{n}', '6', (string) fenix_mod( 'links_steps_title' ) ) ); ?></h2>
			<?php if ( fenix_mod( 'links_steps_sub' ) ) : ?>
				<p><?php echo esc_html( fenix_mod( 'links_steps_sub' ) ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $lh_shortcuts ) : ?>
			<div class="short">
				<?php if ( fenix_mod( 'links_shortcut_label' ) ) : ?>
					<span class="lb"><?php echo esc_html( fenix_mod( 'links_shortcut_label' ) ); ?></span>
				<?php endif; ?>

				<?php
				foreach ( $lh_shortcuts as $lh_sc ) :
					$lh_sc_url = $lh_sc['cells'][1];
					$lh_ext    = fenix_go_is_external( $lh_sc_url, $lh_home );
					?>
					<a href="<?php echo esc_url( 0 === strpos( $lh_sc_url, '#' ) ? $lh_sc_url : fenix_link_url( $lh_sc_url ) ); ?>"<?php echo fenix_go_target( $lh_ext ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo fenix_icon( $lh_sc['cells'][2], 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( $lh_sc['cells'][0] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="fx-steps">

			<?php /* ---------- ขั้นที่ 1 · เปิดบัญชี ---------- */ ?>
			<div class="fx-step" id="account">
				<div class="node num">1</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">ขั้นที่ 1</span>
							<h3><?php echo esc_html( fenix_mod( 'links_step1_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step1_desc' ) ); ?></p>
						</div>
					</div>
					<div class="sb">
						<?php
						$lh_su_url   = trim( (string) fenix_mod( 'links_signup_url' ) );
						$lh_su_label = trim( (string) fenix_mod( 'links_signup_label' ) );
						if ( '' !== $lh_su_url && '' !== $lh_su_label ) :
							$lh_ext = fenix_go_is_external( $lh_su_url, $lh_home );
							?>
							<a class="fx-btn fx-btn-or split" href="<?php echo esc_url( $lh_su_url ); ?>"<?php echo fenix_go_target( $lh_ext ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
								<span class="bi"><?php echo fenix_icon( 'user-plus', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_su_label ); ?>
								<?php echo fenix_icon( $lh_ext ? 'ext' : 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
							<?php echo fenix_go_dest( $lh_su_url, $lh_dest ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>

						<?php
						$lh_ag_url   = trim( (string) fenix_mod( 'links_account_guide_url' ) );
						$lh_ag_label = trim( (string) fenix_mod( 'links_account_guide_label' ) );
						if ( '' !== $lh_ag_url && '' !== $lh_ag_label ) :
							?>
							<a class="fx-btn fx-btn-dark split" href="<?php echo esc_url( fenix_link_url( $lh_ag_url ) ); ?>">
								<span class="bi"><?php echo fenix_icon( 'book', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_ag_label ); ?>
								<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php /* ---------- ขั้นที่ 2 · ติดตั้ง MT5 ---------- */ ?>
			<div class="fx-step" id="mt5">
				<div class="node num">2</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">ขั้นที่ 2</span>
							<h3><?php echo esc_html( fenix_mod( 'links_mt5_install_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step2_desc' ) ); ?></p>
						</div>
					</div>
					<div class="sb">
						<?php if ( $lh_mt5_apps ) : ?>
							<div class="dev">
								<?php foreach ( $lh_mt5_apps as $lh_app ) : ?>
									<a href="<?php echo esc_url( fenix_link_url( $lh_app['url'] ) ); ?>" target="_blank" rel="noopener"
										data-device="<?php echo esc_attr( $lh_app['device'] ); ?>"
										data-yours="<?php echo esc_attr( $lh_yours ); ?>">
										<?php echo fenix_icon( $lh_app['icon'], in_array( $lh_app['icon'], array( 'apple', 'windows' ), true ) ? 'i fill' : 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<span class="keep-case"><?php echo esc_html( $lh_app['label'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php
						$lh_lg_url   = trim( (string) fenix_mod( 'links_mt5_download_url' ) );
						$lh_lg_label = trim( (string) fenix_mod( 'links_mt5_download_label' ) );
						if ( '' !== $lh_lg_url && '' !== $lh_lg_label ) :
							?>
							<a class="fx-btn fx-btn-dark split" href="<?php echo esc_url( fenix_link_url( $lh_lg_url ) ); ?>">
								<span class="bi"><?php echo fenix_icon( 'book', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_lg_label ); ?>
								<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php /* ---------- ขั้นที่ 3 · ฝากเงิน ---------- */ ?>
			<div class="fx-step" id="deposit">
				<div class="node num">3</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">ขั้นที่ 3</span>
							<h3><?php echo esc_html( fenix_mod( 'links_step3_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step3_desc' ) ); ?></p>
						</div>
					</div>
					<div class="sb">
						<?php
						$lh_dp_url   = trim( (string) fenix_mod( 'links_deposit_url' ) );
						$lh_dp_label = trim( (string) fenix_mod( 'links_deposit_label' ) );
						if ( '' !== $lh_dp_url && '#' !== $lh_dp_url && '' !== $lh_dp_label ) :
							$lh_ext = fenix_go_is_external( $lh_dp_url, $lh_home );
							?>
							<a class="fx-btn fx-btn-or split" href="<?php echo esc_url( $lh_dp_url ); ?>"<?php echo fenix_go_target( $lh_ext ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
								<span class="bi"><?php echo fenix_icon( 'wallet', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_dp_label ); ?>
								<?php echo fenix_icon( $lh_ext ? 'ext' : 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
							<?php echo fenix_go_dest( $lh_dp_url, $lh_dest ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endif; ?>

						<?php
						$lh_dg_url   = trim( (string) fenix_mod( 'links_deposit_guide_url' ) );
						$lh_dg_label = trim( (string) fenix_mod( 'links_deposit_guide_label' ) );
						if ( '' !== $lh_dg_url && '#' !== $lh_dg_url && '' !== $lh_dg_label ) :
							?>
							<a class="fx-btn fx-btn-dark split" href="<?php echo esc_url( fenix_link_url( $lh_dg_url ) ); ?>">
								<span class="bi"><?php echo fenix_icon( 'book', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_dg_label ); ?>
								<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php /* ---------- ขั้นที่ 4 · ดาวน์โหลด EA ---------- */ ?>
			<div class="fx-step" id="dl">
				<div class="node num">4</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">ขั้นที่ 4</span>
							<h3><?php echo esc_html( fenix_mod( 'links_step4_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step4_desc' ) ); ?></p>
						</div>
					</div>

					<?php if ( $lh_cards ) : ?>
						<div class="eas">
							<?php
							foreach ( $lh_cards as $lh_card ) :
								$lh_ver = trim( (string) $lh_card['version'] );
								if ( '' === $lh_ver ) {
									$lh_ver = fenix_version_from_url( $lh_card['url'] );
								}
								if ( '' !== $lh_ver && preg_match( '/^\d/', $lh_ver ) ) {
									$lh_ver = 'V' . $lh_ver;
								}
								?>
								<a class="ea" href="<?php echo esc_url( $lh_card['url'] ); ?>" download>
									<img src="<?php echo esc_url( $lh_card['img'] ); ?>" alt="<?php echo esc_attr( $lh_card['alt'] ); ?>" width="1200" height="675" loading="lazy" decoding="async">
									<span class="ov">
										<?php if ( '' !== $lh_dl_label ) : ?>
											<span class="dl"><?php echo fenix_icon( 'download', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $lh_dl_label ); ?></span>
										<?php endif; ?>
										<?php if ( '' !== $lh_ver ) : ?>
											<span class="ver"><?php echo esc_html( $lh_ver_lbl ); ?><b class="num"><?php echo esc_html( $lh_ver ); ?></b></span>
										<?php endif; ?>
									</span>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php $lh_ea_note = fenix_lines( fenix_mod( 'links_ea_note' ) ); ?>
					<?php if ( $lh_ea_note ) : ?>
						<div class="ea-note">
							<?php foreach ( $lh_ea_note as $lh_item ) : ?>
								<span><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $lh_item ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<?php /* ---------- ขั้นที่ 5 · ติดตั้ง EA ---------- */ ?>
			<div class="fx-step" id="install">
				<div class="node num">5</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">ขั้นที่ 5</span>
							<h3><?php echo esc_html( fenix_mod( 'links_step5_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step5_desc' ) ); ?></p>
						</div>
					</div>

					<?php if ( fenix_mod( 'links_algo_label' ) ) : ?>
						<div class="algo" aria-hidden="true">
							<div class="tb">
								<?php echo fenix_icon( 'play', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'links_algo_label' ) ); ?>
								<span class="sw"></span>
							</div>
							<p><b><?php echo esc_html( fenix_mod( 'links_algo_strong' ) ); ?></b> <?php echo esc_html( fenix_mod( 'links_algo_text' ) ); ?></p>
						</div>
					<?php endif; ?>

					<div class="sb">
						<?php
						$lh_g1_url   = trim( (string) fenix_mod( 'links_guide1_url' ) );
						$lh_g1_label = trim( (string) fenix_mod( 'links_guide1_label' ) );
						if ( '' !== $lh_g1_url && '' !== $lh_g1_label ) :
							?>
							<a class="fx-btn fx-btn-or split" href="<?php echo esc_url( fenix_link_url( $lh_g1_url ) ); ?>">
								<span class="bi"><?php echo fenix_icon( 'book', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_g1_label ); ?>
								<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>

						<?php
						$lh_g5_url   = trim( (string) fenix_mod( 'links_guide5_url' ) );
						$lh_g5_label = trim( (string) fenix_mod( 'links_guide5_label' ) );
						if ( '' !== $lh_g5_url && '' !== $lh_g5_label ) :
							$lh_is_pdf = (bool) preg_match( '/\.pdf($|\?)/i', $lh_g5_url );
							$lh_label  = $lh_is_pdf ? trim( preg_replace( '/\s*\(PDF\)\s*$/i', '', $lh_g5_label ) ) : $lh_g5_label;
							?>
							<a class="fx-btn fx-btn-dark split" href="<?php echo esc_url( fenix_link_url( $lh_g5_url ) ); ?>"<?php echo $lh_is_pdf ? ' target="_blank" rel="noopener"' : ''; ?>>
								<span class="bi"><?php echo fenix_icon( 'file', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<?php echo esc_html( $lh_label ); ?>
								<?php if ( $lh_is_pdf ) : ?>
									<span class="fx-badge dark" style="margin-left:4px">PDF</span>
								<?php endif; ?>
								<?php echo fenix_icon( 'chev-r', 'i end' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php /* ---------- ขั้นที่ 6 · VPS ---------- */ ?>
			<div class="fx-step" id="vps">
				<div class="node num">6</div>
				<div class="scard">
					<div class="sh">
						<div class="t">
							<span class="sn">
								ขั้นที่ 6
								<?php if ( fenix_mod( 'links_step6_badge' ) ) : ?>
									<span class="fx-badge or"><?php echo esc_html( fenix_mod( 'links_step6_badge' ) ); ?></span>
								<?php endif; ?>
							</span>
							<h3><?php echo esc_html( fenix_mod( 'links_step6_title' ) ); ?></h3>
							<p><?php echo esc_html( fenix_mod( 'links_step6_desc' ) ); ?></p>
						</div>
					</div>

					<?php if ( $lh_vps ) : ?>
						<div class="sb">
							<div class="dev">
								<?php foreach ( $lh_vps as $lh_item ) : ?>
									<a href="<?php echo esc_url( fenix_link_url( $lh_item['url'] ) ); ?>"
										data-device="<?php echo esc_attr( $lh_item['device'] ); ?>"
										data-yours="<?php echo esc_attr( $lh_yours ); ?>">
										<?php echo fenix_icon( $lh_item['icon'], in_array( $lh_item['icon'], array( 'apple', 'windows' ), true ) ? 'i fill' : 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<span class="keep-case"><?php echo esc_html( $lh_item['label'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php $lh_why = fenix_lines( fenix_mod( 'links_vps_why' ) ); ?>
					<?php if ( $lh_why ) : ?>
						<div class="vps-why">
							<?php foreach ( $lh_why as $lh_item ) : ?>
								<span><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $lh_item ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>

		</div>
	</main>

</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
