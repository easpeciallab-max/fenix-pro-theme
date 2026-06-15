<?php
/**
 * Single post — บทความ (ออกแบบสำหรับคอนเทนต์ SEO)
 *
 * @package fenix-pro
 */

get_header();

while ( have_posts() ) :
	the_post();

	$fenix_cats    = get_the_category();
	$fenix_cat     = ! empty( $fenix_cats ) ? $fenix_cats[0] : null;
	$fenix_reading = max( 1, (int) ceil( mb_strlen( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ) ) / 400 ) );
	$fenix_thumb   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	?>

	<main id="main">
		<article <?php post_class( 'single-article' ); ?>>

			<header class="article-hero">
				<div class="container container-narrow">
					<nav class="breadcrumb" aria-label="เส้นทางนำทาง">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
						<?php if ( $fenix_cat ) : ?>
							<span class="breadcrumb-sep" aria-hidden="true">›</span>
							<a href="<?php echo esc_url( get_category_link( $fenix_cat->term_id ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
						<?php endif; ?>
						<span class="breadcrumb-sep" aria-hidden="true">›</span>
						<span class="breadcrumb-current"><?php the_title(); ?></span>
					</nav>

					<?php if ( $fenix_cat ) : ?>
						<a class="article-cat" href="<?php echo esc_url( get_category_link( $fenix_cat->term_id ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
					<?php endif; ?>

					<h1 class="article-title"><?php the_title(); ?></h1>

					<div class="article-meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<span class="article-meta-sep" aria-hidden="true">·</span>
						<span><?php echo esc_html( 'อ่าน ' . $fenix_reading . ' นาที' ); ?></span>
						<span class="article-meta-sep" aria-hidden="true">·</span>
						<span><?php echo esc_html( 'เข้าชม ' . number_format_i18n( fenix_get_post_views( get_the_ID() ) ) . ' ครั้ง' ); ?></span>
					</div>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="container container-narrow">
					<figure class="article-thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
				</div>
			<?php endif; ?>

			<div class="container container-narrow">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">หน้า: ',
						'after'  => '</div>',
					)
				);

				$fenix_tags = get_the_tag_list( '<ul class="article-tags"><li>', '</li><li>', '</li></ul>' );
				if ( $fenix_tags ) {
					echo wp_kses_post( $fenix_tags );
				}
				?>

				<div class="article-disclaimer">
					<?php echo fenix_icon( 'warn', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<p>
						<?php echo esc_html( fenix_mod( 'footer_risk' ) ); ?>
						<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">อ่านคำเตือนฉบับเต็ม</a>
					</p>
				</div>
			</div>

		</article>

		<?php fenix_line_cta(); ?>

		<?php
		if ( $fenix_cat ) :
			$fenix_related = new WP_Query(
				array(
					'category__in'        => array( $fenix_cat->term_id ),
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 3,
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $fenix_related->have_posts() ) :
				?>
				<section class="section section-alt related-section" aria-label="บทความที่เกี่ยวข้อง">
					<div class="container">
						<div class="sec-head"><h2>บทความที่เกี่ยวข้อง</h2></div>
						<div class="related-rail">
							<?php
							while ( $fenix_related->have_posts() ) :
								$fenix_related->the_post();
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
									</div>
								</article>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</div>
				</section>
				<?php
			endif;
		endif;
		?>

	</main>

	<?php
	$fenix_schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => fenix_logo_url(),
			),
		),
		'mainEntityOfPage' => get_permalink(),
	);
	if ( $fenix_thumb ) {
		$fenix_schema['image'] = $fenix_thumb;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $fenix_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
	?>

	<?php
endwhile;

get_footer();
