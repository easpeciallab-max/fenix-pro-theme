<?php
/**
 * Static page
 *
 * @package fenix-pro
 */

get_header();
?>

<main id="main">
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
</main>

<?php
get_footer();
