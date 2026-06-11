<?php
/**
 * Single post
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
						<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

				</article>

				<nav class="nav-links" aria-label="บทความก่อนหน้า/ถัดไป">
					<?php previous_post_link( '%link', '&larr; %title' ); ?>
					<?php next_post_link( '%link', '%title &rarr;' ); ?>
				</nav>

			<?php endwhile; ?>

		</div>
	</div>
</main>

<?php
get_footer();
