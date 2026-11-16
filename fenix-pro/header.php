<?php
/**
 * Header
 *
 * @package fenix-pro
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main">ข้ามไปยังเนื้อหา</a>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
<header class="site-header" id="top">
	<div class="container header-inner">

		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="brand-logo" src="<?php echo esc_url( fenix_logo_url() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="46" height="46">
			<span class="brand-name">FENIX <em>PRO</em><small>EA&nbsp;for&nbsp;MT5</small></span>
		</a>

		<?php $fenix_dash_url = trim( (string) fenix_mod( 'dashboard_url' ) ); ?>

		<nav class="site-nav" id="site-nav" aria-label="เมนูหลัก">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'fallback_cb'    => 'fenix_fallback_menu',
					'items_wrap'     => '<ul id="%1$s" class="%2$s"><li class="nav-group-label">เมนูหลัก</li>%3$s</ul>',
					'depth'          => 2,
				)
			);
			?>

			<div class="nav-foot">
				<?php fenix_social_row( 'nav-social' ); ?>

				<div class="nav-extra">
					<?php fenix_language_switcher(); ?>
				</div>
			</div>
		</nav>

		<?php if ( fenix_mod( 'show_header_auth' ) && $fenix_dash_url ) : ?>
			<a class="btn btn-fire header-dash" href="<?php echo esc_url( $fenix_dash_url ); ?>">
				<?php echo fenix_icon( 'gauge', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span><?php echo esc_html( fenix_mod( 'header_dash_label' ) ); ?></span>
			</a>
		<?php endif; ?>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="เปิด/ปิดเมนู">
			<span></span><span></span><span></span>
		</button>

	</div>
</header>
<?php endif; ?>
