<?php
/**
 * Footer
 *
 * @package fenix-pro
 */

$fenix_line  = fenix_mod( 'line_url' );
$fenix_fb    = fenix_mod( 'facebook_url' );
$fenix_email = fenix_mod( 'contact_email' );
?>

<footer class="site-footer" id="contact">
	<div class="container">

		<div class="footer-grid">

			<div class="footer-brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img class="brand-logo" src="<?php echo esc_url( fenix_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="52" height="52">
					<span class="brand-name">FENIX <em>PRO</em><small>EA&nbsp;for&nbsp;MT5</small></span>
				</a>
				<p class="footer-tagline"><?php echo esc_html( fenix_mod( 'footer_tagline' ) ); ?></p>
			</div>

			<div class="footer-col">
				<h3 class="footer-head">ลิงก์ด่วน</h3>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-links',
						'fallback_cb'    => 'fenix_fallback_menu',
						'depth'          => 1,
					)
				);
				?>
			</div>

			<div class="footer-col">
				<h3 class="footer-head">ติดต่อเรา</h3>
				<ul class="footer-contact">
					<li>
						<a class="contact-line" href="<?php echo esc_url( $fenix_line ); ?>" target="_blank" rel="noopener">
							<?php echo fenix_icon( 'line', 'icon icon-line' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span>LINE Official Account</span>
						</a>
					</li>
					<?php if ( $fenix_fb ) : ?>
					<li>
						<a href="<?php echo esc_url( $fenix_fb ); ?>" target="_blank" rel="noopener">
							<?php echo fenix_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span>Facebook Page</span>
						</a>
					</li>
					<?php endif; ?>
					<?php if ( $fenix_email ) : ?>
					<li>
						<a href="<?php echo esc_url( 'mailto:' . $fenix_email ); ?>">
							<?php echo fenix_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<span class="keep-case"><?php echo esc_html( $fenix_email ); ?></span>
						</a>
					</li>
					<?php endif; ?>
				</ul>
			</div>

		</div>

		<div class="footer-disclaimer">
			<?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p><?php echo esc_html( fenix_mod( 'footer_risk' ) ); ?></p>
		</div>

		<div class="footer-bottom">
			<p class="footer-copy">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> — สงวนลิขสิทธิ์</p>
			<a class="footer-top" href="#top" aria-label="กลับขึ้นด้านบน">
				กลับขึ้นด้านบน
				<?php echo fenix_icon( 'arrow', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
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
