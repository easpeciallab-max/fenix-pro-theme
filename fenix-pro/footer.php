<?php
/**
 * Footer
 *
 * @package fenix-pro
 */

$fenix_line  = fenix_mod( 'line_url' );
$fenix_fb    = fenix_mod( 'facebook_url' );
$fenix_email = fenix_mod( 'contact_email' );
$fenix_intro = fenix_lines( fenix_mod( 'footer_tagline' ) );
$fenix_trust = fenix_lines( fenix_mod( 'footer_trust_items' ) );
$fenix_prep  = fenix_lines( fenix_mod( 'footer_prep_items' ) );
?>

<footer class="site-footer" id="contact">
	<div class="container">

		<div class="footer-cta">
			<div>
				<p class="footer-kicker"><?php echo esc_html( fenix_mod( 'footer_kicker' ) ); ?></p>
				<h2><?php echo esc_html( fenix_mod( 'footer_cta_title' ) ); ?></h2>
				<p><?php echo esc_html( fenix_mod( 'footer_cta_text' ) ); ?></p>
			</div>
			<a class="footer-primary" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
				<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( fenix_mod( 'footer_line_text' ) ); ?></span>
			</a>
		</div>

		<div class="footer-main">

			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img class="brand-logo" src="<?php echo esc_url( fenix_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="64" height="64">
					<span class="brand-name">FENIX <em>PRO</em><small>EA&nbsp;for&nbsp;MT5</small></span>
				</a>
				<?php if ( $fenix_intro ) : ?>
				<div class="footer-tagline">
					<?php foreach ( $fenix_intro as $fenix_paragraph ) : ?>
					<p><?php echo esc_html( $fenix_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $fenix_trust ) : ?>
				<ul class="footer-trust">
					<?php foreach ( $fenix_trust as $fenix_item ) : ?>
					<li><?php echo esc_html( $fenix_item ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

			<div class="footer-prep">
				<h3 class="footer-head"><?php echo esc_html( fenix_mod( 'footer_prep_title' ) ); ?></h3>
				<p><?php echo esc_html( fenix_mod( 'footer_prep_text' ) ); ?></p>

				<?php if ( $fenix_prep ) : ?>
				<ul class="footer-prep-list">
					<?php foreach ( $fenix_prep as $fenix_item ) : ?>
					<li>
						<?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( $fenix_item ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>

		</div>

		<div class="footer-bottom">
			<p class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — สงวนลิขสิทธิ์</p>
			<?php if ( $fenix_fb || $fenix_email ) : ?>
			<div class="footer-links-mini">
				<?php if ( $fenix_fb ) : ?>
				<a href="<?php echo esc_url( $fenix_fb ); ?>" target="_blank" rel="noopener"><?php echo esc_html( fenix_mod( 'footer_facebook_text' ) ); ?></a>
				<?php endif; ?>
				<?php if ( $fenix_email ) : ?>
				<a class="keep-case" href="<?php echo esc_url( 'mailto:' . $fenix_email ); ?>"><?php echo esc_html( $fenix_email ); ?></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<a class="footer-legal" href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>"><?php echo esc_html( fenix_mod( 'footer_risk_link' ) ); ?></a>
		</div>

	</div>
</footer>

<?php if ( fenix_mod( 'show_float_line' ) ) : ?>
<a class="float-line" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( fenix_mod( 'float_line_text' ) ); ?>">
	<?php echo fenix_icon( 'line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<span><?php echo esc_html( fenix_mod( 'float_line_text' ) ); ?></span>
</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
