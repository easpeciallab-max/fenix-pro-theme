<?php
/**
 * Template Name: FENIX · หน้า Forward Test
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * ข้อความทุกจุดอ่านจาก Customizer เหมือนเดิม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$fw_title = get_the_title();
$fw_url   = trim( (string) fenix_mod( 'forward_link_url' ) );
$fw_host  = $fw_url ? wp_parse_url( $fw_url, PHP_URL_HOST ) : '';
$fw_line  = trim( (string) fenix_mod( 'line_url' ) );
?>

<main id="main" class="fxp">

	<?php fenix_phero( $fw_title ? $fw_title : 'Forward Test', fenix_mod( 'forward_sub' ), fenix_mod( 'forward_kicker' ) ); ?>

	<section class="sec">
		<div class="wrap">
			<div class="ft">
				<div>
					<?php if ( fenix_mod( 'forward_intro' ) ) : ?>
						<p class="intro"><?php echo esc_html( fenix_mod( 'forward_intro' ) ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $fw_url && fenix_mod( 'forward_link_label' ) ) : ?>
						<div class="acts">
							<a class="fx-btn fx-btn-or" href="<?php echo esc_url( $fw_url ); ?>" target="_blank" rel="noopener nofollow">
								<?php echo fenix_icon( 'eye', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'forward_link_label' ) ); ?>
								<?php echo fenix_icon( 'ext', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
							<?php if ( $fw_host ) : ?>
								<span class="dest">
									<?php echo fenix_icon( 'lock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php echo esc_html( fenix_mod( 'links_dest_prefix' ) ); ?>
									<span class="keep-case"><?php echo esc_html( $fw_host ); ?></span>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php $fw_img = trim( (string) fenix_mod( 'forward_img' ) ); ?>
				<?php if ( '' !== $fw_img ) : ?>
					<figure class="fx-shot">
						<img src="<?php echo esc_url( $fw_img ); ?>" alt="<?php echo esc_attr( fenix_mod( 'forward_img_caption' ) ); ?>" loading="lazy" decoding="async">
						<?php if ( fenix_mod( 'forward_img_caption' ) ) : ?>
							<figcaption><?php echo esc_html( fenix_mod( 'forward_img_caption' ) ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php else : ?>
					<?php /* ภาพประกอบกรอบเบราว์เซอร์ · ตั้งใจไม่ใส่ตัวเลข ตามข้อความในหน้า */ ?>
					<div class="bw" aria-hidden="true">
						<div class="bar">
							<i></i><i></i><i></i>
							<span class="url">
								<?php echo fenix_icon( 'lock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span class="keep-case"><?php echo esc_html( preg_replace( '~^https?://~', '', $fw_url ) ); ?></span>
							</span>
						</div>
						<div class="bd">
							<div class="row"><span class="sk"></span><span class="sk"></span><span class="sk"></span></div>
							<div class="chart"><span><?php echo fenix_icon( 'eye', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span></div>
							<div class="lines">
								<span class="sk" style="width:90%"></span>
								<span class="sk" style="width:70%"></span>
								<span class="sk" style="width:80%"></span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ( fenix_mod( 'forward_note' ) || fenix_mod( 'forward_disclaimer' ) ) : ?>
		<section class="sec tight alt">
			<div class="wrap">
				<div class="notes">
					<?php if ( fenix_mod( 'forward_note' ) ) : ?>
						<div class="note">
							<?php echo fenix_icon( 'info', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( fenix_mod( 'forward_note' ) ); ?></span>
						</div>
					<?php endif; ?>

					<?php if ( fenix_mod( 'forward_disclaimer' ) ) : ?>
						<div class="note warn">
							<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span><?php echo esc_html( fenix_mod( 'forward_disclaimer' ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php fenix_pages_cta( fenix_mod( 'fw_cta_title' ), fenix_mod( 'fw_cta_text' ), fenix_mod( 'pages_cta_btn' ), $fw_line ); ?>

</main>

<?php
get_footer();
