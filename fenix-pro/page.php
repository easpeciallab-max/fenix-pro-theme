<?php
/**
 * Static page
 *
 * @package fenix-pro
 */

get_header();

$fenix_is_elementor = fenix_is_elementor_page();
?>

<main id="main"<?php echo $fenix_is_elementor ? ' class="elementor-page-shell elementor-page-shell--auto"' : ''; ?>>
	<?php if ( $fenix_is_elementor ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'elementor-entry' ); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>

	<?php else : ?>

	<div class="entry">
		<div class="container-narrow">

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>

					<header class="entry-header">
						<h1><?php the_title(); ?></h1>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

				</article>
			<?php endwhile; ?>

		</div>
	</div>
	<?php endif; ?>
</main>

<?php
get_footer();
