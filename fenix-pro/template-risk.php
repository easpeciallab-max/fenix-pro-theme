<?php
/**
 * Template Name: FENIX · หน้า Risk Disclosure
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026 (หน้ากฎหมาย ชุด 3)
 * ข้อความทุกจุดอ่านจาก Customizer เหมือนเดิม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$rk_title = get_the_title();
$rk_line  = trim( (string) fenix_mod( 'line_url' ) );
?>

<main id="main" class="fxp fxl">

	<?php fenix_phero( $rk_title ? $rk_title : 'คำเตือนความเสี่ยง', fenix_mod( 'riskpage_sub' ), 'Risk Disclosure' ); ?>

	<section class="sec">
		<div class="wrap lwrap">

			<?php if ( fenix_mod( 'riskpage_intro' ) ) : ?>
				<div class="rintro">
					<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p><?php echo esc_html( fenix_mod( 'riskpage_intro' ) ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( fenix_mod( 'riskpage_image' ) ) : ?>
				<figure class="fx-shot rfig">
					<img src="<?php echo esc_url( fenix_mod( 'riskpage_image' ) ); ?>" alt="<?php echo esc_attr( fenix_mod( 'riskpage_image_caption' ) ); ?>" loading="lazy" decoding="async">
					<?php if ( fenix_mod( 'riskpage_image_caption' ) ) : ?>
						<figcaption><?php echo esc_html( fenix_mod( 'riskpage_image_caption' ) ); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

			<div class="rgrid">
				<?php
				$rk_n = 0;
				for ( $rk_i = 1; $rk_i <= 6; $rk_i++ ) :
					$rk_head = fenix_mod( 'rp_block' . $rk_i . '_title' );
					$rk_text = fenix_mod( 'rp_block' . $rk_i . '_text' );

					if ( ! $rk_head && ! $rk_text ) {
						continue;
					}

					$rk_n++;
					?>
					<article class="rcard">
						<span class="n num"><?php echo esc_html( str_pad( (string) $rk_n, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h2><?php echo esc_html( $rk_head ); ?></h2>
						<p><?php echo nl2br( esc_html( $rk_text ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					</article>
				<?php endfor; ?>
			</div>

			<?php if ( fenix_mod( 'riskpage_updated' ) ) : ?>
				<div class="rfoot">
					<span class="updated">
						<?php echo fenix_icon( 'clock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'riskpage_updated' ) ); ?>
					</span>
				</div>
			<?php endif; ?>

		</div>
	</section>

	<?php
	fenix_pages_cta(
		'มีคำถามเรื่องความเสี่ยง?',
		'ทักมาสอบถามทีมงานก่อนตัดสินใจใช้งานได้ทาง LINE',
		fenix_mod( 'pages_cta_btn' ),
		$rk_line
	);
	?>

</main>

<?php
get_footer();
