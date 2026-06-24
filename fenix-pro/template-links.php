<?php
/**
 * Template Name: FENIX · หน้า Link Hub (ยิงแอด)
 *
 * หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด (Facebook/Google/TikTok Ads)
 * เป็นหน้าแบบ standalone: ไม่มีเมนูบน/ฟุตเตอร์เว็บ เพื่อโฟกัสปุ่มเดียว (ทักไลน์)
 * วิธีใช้: สร้างเพจใหม่ เลือกเทมเพลตนี้ ตั้ง slug เช่น go แล้วใช้เป็นลิงก์ในแอด
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

/* ไอคอนประกอบปุ่มตามลำดับ (ตกแต่ง · เปลี่ยนความหมายปุ่มได้โดยไม่ผูกกับไอคอน) */
$lh_btn_icons = array( 1 => 'chart', 2 => 'tag', 3 => 'download', 4 => 'layout', 5 => 'arrow', 6 => 'arrow' );

$lh_socials = array(
	'facebook'  => array( fenix_mod( 'facebook_url' ), 'Facebook' ),
	'instagram' => array( fenix_mod( 'instagram_url' ), 'Instagram' ),
	'tiktok'    => array( fenix_mod( 'tiktok_url' ), 'TikTok' ),
	'youtube'   => array( fenix_mod( 'youtube_url' ), 'YouTube' ),
);
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

		<a class="lh-btn lh-btn-line" href="<?php echo esc_url( $lh_line ); ?>" target="_blank" rel="noopener">
			<span class="lh-ic"><?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="lh-lbl"><?php echo esc_html( fenix_mod( 'links_line_label' ) ); ?></span>
		</a>

		<?php
		for ( $lh_i = 1; $lh_i <= 6; $lh_i++ ) :
			$lh_label = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_label' ) );
			$lh_url   = trim( (string) fenix_mod( 'links_btn' . $lh_i . '_url' ) );
			if ( '' === $lh_label || '' === $lh_url ) {
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

		$lh_socials = array_filter(
			$lh_socials,
			function ( $lh_s ) {
				return '' !== trim( (string) $lh_s[0] );
			}
		);
		if ( $lh_socials ) :
			?>
			<div class="lh-socials">
				<?php foreach ( $lh_socials as $lh_name => $lh_s ) : ?>
					<a class="lh-soc" href="<?php echo esc_url( $lh_s[0] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $lh_s[1] ); ?>">
						<?php echo fenix_icon( $lh_name ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lh_note ) : ?>
			<p class="lh-note"><?php echo esc_html( $lh_note ); ?></p>
		<?php endif; ?>

	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
