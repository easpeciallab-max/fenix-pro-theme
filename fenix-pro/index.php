<?php
/**
 * Blog index / archive
 *
 * @package fenix-pro
 */

get_header();
?>

<main id="main">

	<div class="page-hero">
		<div class="container">
			<h1>
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} elseif ( is_archive() ) {
					the_archive_title();
				} elseif ( is_search() ) {
					echo 'ผลการค้นหา: ' . esc_html( get_search_query() );
				} else {
					echo 'บทความ';
				}
				?>
			</h1>
			<p class="muted">ความรู้เรื่อง EA, MT5 และการบริหารความเสี่ยง</p>
		</div>
	</div>

	<div class="posts-wrap">
		<div class="container">

			<?php if ( have_posts() ) : ?>

				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<article <?php post_class( 'post-card' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<a class="post-card-thumb" href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium_large' ); ?>
								</a>
							<?php endif; ?>
							<div class="post-card-body">
								<span class="post-meta"><?php echo esc_html( get_the_date() ); ?></span>
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							</div>
						</article>
					<?php endwhile; ?>
				</div>

				<div class="pagination">
					<?php
					echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
						array(
							'prev_text' => '&larr; ก่อนหน้า',
							'next_text' => 'ถัดไป &rarr;',
						)
					);
					?>
				</div>

			<?php else : ?>

				<p class="no-posts">ยังไม่มีบทความในขณะนี้</p>

			<?php endif; ?>

		</div>
	</div>

</main>

<?php
get_footer();
