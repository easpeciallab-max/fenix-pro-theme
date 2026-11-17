<?php
/**
 * Template Name: FENIX · หน้า Pricing
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * การ์ดแพ็กเกจทรงตั๋ว + ตารางเปรียบเทียบ + กล่อง CTA
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$pr_title   = get_the_title();
$pr_line    = trim( (string) fenix_mod( 'line_url' ) );
$pr_contact = 'contact' === fenix_mod( 'pricing_mode' );
$pr_rows    = fenix_rows( fenix_mod( 'compare_rows' ) );
?>

<main id="main" class="fxp">

	<?php fenix_phero( $pr_title ? $pr_title : 'แพ็กเกจและราคา', fenix_mod( 'pricing_sub' ), fenix_mod( 'pricing_kicker' ) ); ?>

	<section class="sec">
		<div class="wrap">
			<div class="shead">
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'packages_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'pricing_title' ) ); ?></h2>
				<?php if ( fenix_mod( 'pricing_subtitle' ) ) : ?>
					<p class="fx-lead"><?php echo esc_html( fenix_mod( 'pricing_subtitle' ) ); ?></p>
				<?php endif; ?>
			</div>

			<div class="price-g">
				<?php
				for ( $pr_i = 1; $pr_i <= 3; $pr_i++ ) :
					$pr_name = trim( (string) fenix_mod( 'pkg' . $pr_i . '_name' ) );
					if ( '' === $pr_name ) {
						continue;
					}
					$pr_best = (bool) fenix_mod( 'pkg' . $pr_i . '_featured' );
					?>
					<div class="tk<?php echo $pr_best ? ' hl' : ''; ?>">
						<div class="tkh">
							<div class="nm">
								<h3><?php echo esc_html( $pr_name ); ?></h3>
								<?php if ( $pr_best ) : ?>
									<span class="fx-badge or"><?php echo esc_html( fenix_mod( 'pkg_featured_label' ) ); ?></span>
								<?php endif; ?>
							</div>

							<?php if ( fenix_mod( 'pkg' . $pr_i . '_tag' ) ) : ?>
								<p class="for"><?php echo esc_html( fenix_mod( 'pkg' . $pr_i . '_tag' ) ); ?></p>
							<?php endif; ?>

							<div class="pp">
								<?php if ( $pr_contact ) : ?>
									<b>สอบถาม</b>
								<?php else : ?>
									<b class="num"><?php echo esc_html( fenix_mod( 'pkg' . $pr_i . '_price' ) ); ?></b>
									<span><?php echo esc_html( fenix_mod( 'pkg' . $pr_i . '_period' ) ); ?></span>
								<?php endif; ?>
							</div>
						</div>

						<div class="perf" aria-hidden="true"></div>

						<div class="bt">
							<?php $pr_feat = fenix_lines( fenix_mod( 'pkg' . $pr_i . '_features' ) ); ?>
							<?php if ( $pr_feat ) : ?>
								<ul>
									<?php foreach ( $pr_feat as $pr_item ) : ?>
										<li>
											<span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
											<span><?php echo esc_html( $pr_item ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( '' !== $pr_line ) : ?>
								<a class="fx-btn <?php echo $pr_best ? 'fx-btn-or' : 'fx-btn-out'; ?> fx-btn-full" href="<?php echo esc_url( $pr_line ); ?>" target="_blank" rel="noopener">
									<?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php echo esc_html( fenix_mod( 'pricing_btn_text' ) ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endfor; ?>
			</div>

			<?php if ( fenix_mod( 'pricing_note' ) ) : ?>
				<div class="note" style="margin-top:24px">
					<?php echo fenix_icon( 'info', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span><?php echo esc_html( fenix_mod( 'pricing_note' ) ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( count( $pr_rows ) > 1 ) : ?>
		<section class="sec alt">
			<div class="wrap">
				<div class="shead">
					<span class="eyebrow"><?php echo esc_html( fenix_mod( 'compare_kicker' ) ); ?></span>
					<h2 class="h2"><?php echo esc_html( fenix_mod( 'compare_title' ) ); ?></h2>
				</div>

				<?php
				$pr_head = array_shift( $pr_rows );
				$pr_cols = count( $pr_head['cells'] );
				$pr_pro  = -1;

				foreach ( $pr_head['cells'] as $pr_c => $pr_label ) {
					if ( 0 === strcasecmp( trim( $pr_label ), 'pro' ) ) {
						$pr_pro = $pr_c;
					}
				}
				?>
				<div class="cmp-wrap">
					<table class="cmp">
						<thead>
							<tr>
								<?php foreach ( $pr_head['cells'] as $pr_c => $pr_label ) : ?>
									<th<?php echo $pr_c === $pr_pro ? ' class="pro"' : ''; ?>><?php echo esc_html( $pr_label ); ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $pr_rows as $pr_row ) : ?>
								<tr>
									<?php
									$pr_cells = array_pad( array_slice( $pr_row['cells'], 0, $pr_cols ), $pr_cols, '' );
									foreach ( $pr_cells as $pr_c => $pr_cell ) :
										$pr_cell = trim( $pr_cell );
										?>
										<td<?php echo $pr_c === $pr_pro ? ' class="pro"' : ''; ?>>
											<?php if ( 0 === $pr_c ) : ?>
												<?php echo esc_html( $pr_cell ); ?>
											<?php elseif ( '✓' === $pr_cell ) : ?>
												<span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
												<span class="sr-only">มี</span>
											<?php elseif ( '✗' === $pr_cell || '✕' === $pr_cell ) : ?>
												<span class="dot no"><?php echo fenix_icon( 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
												<span class="sr-only">ไม่มี</span>
											<?php else : ?>
												<span class="txt"><?php echo esc_html( $pr_cell ); ?></span>
											<?php endif; ?>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php fenix_pages_cta( fenix_mod( 'pr_cta_title' ), fenix_mod( 'pr_cta_text' ), fenix_mod( 'pages_cta_btn' ), $pr_line ); ?>

</main>

<?php
get_footer();
