<?php
/**
 * Single post · หน้าบทความ (ดีไซน์ชุด 4 อนุมัติ 17 พ.ย. 2026)
 *
 * ใช้กับบทความทุกเรื่อง ทั้งรุ่นใหม่และรุ่นเก่า
 * เนื้อหาบทความเป็น HTML ของ WordPress เอง เทมเพลตนี้ไม่แตะเนื้อหาสักตัวอักษร
 * รูปแบบของเนื้อหาทำด้วย CSS ล้วนใน assets/css/post.css
 *
 * @package fenix-pro
 */

get_header();

while ( have_posts() ) :
	the_post();

	$fenix_cats  = get_the_category();
	$fenix_cat   = ! empty( $fenix_cats ) ? $fenix_cats[0] : null;
	$fenix_thumb = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	$fenix_line  = fenix_mod( 'line_url' );
	?>

	<main id="main" class="fxp fxa">
		<article <?php post_class( 'single-article' ); ?>>

			<header class="fx-phero ahero">
				<div class="slash" aria-hidden="true"><i></i><i></i><i></i></div>
				<div class="wrap awrap">
					<nav class="crumb" aria-label="เส้นทางนำทาง">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">หน้าแรก</a>
						<?php if ( $fenix_cat ) : ?>
							<?php echo fenix_icon( 'chev-r', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<a href="<?php echo esc_url( get_category_link( $fenix_cat->term_id ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
						<?php endif; ?>
						<?php echo fenix_icon( 'chev-r', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="cur"><?php the_title(); ?></span>
					</nav>

					<div class="ahead">
						<div class="atext">
							<?php if ( $fenix_cat ) : ?>
								<a class="acat" href="<?php echo esc_url( get_category_link( $fenix_cat->term_id ) ); ?>"><?php echo esc_html( $fenix_cat->name ); ?></a>
							<?php endif; ?>

							<h1 class="article-title"><?php the_title(); ?></h1>

							<div class="ameta">
								<span>
									<?php echo fenix_icon( 'cal', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</span>
								<span>
									<?php echo fenix_icon( 'eye', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php echo esc_html( 'เข้าชม ' . number_format_i18n( fenix_get_post_views( get_the_ID() ) ) . ' ครั้ง' ); ?>
								</span>
							</div>
						</div>

						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="acover"><?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( fenix_featured_image_alt() ) ) ); ?></figure>
						<?php endif; ?>
					</div>
				</div>
			</header>

			<div class="abody">
				<div class="wrap awrap">
					<?php
					$GLOBALS['fenix_toc'] = array();
					$fenix_content        = apply_filters( 'the_content', get_the_content() );
					$fenix_content        = str_replace( ']]>', ']]&gt;', $fenix_content );
					$fenix_toc            = isset( $GLOBALS['fenix_toc'] ) ? $GLOBALS['fenix_toc'] : array();
					?>

					<div class="agrid">
						<?php if ( count( $fenix_toc ) >= 1 ) : ?>
							<aside class="aside-col">
								<details class="toc atoc" open data-post-toc>
									<summary>
										<?php echo fenix_icon( 'list', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										สารบัญ
										<span class="chev"><?php echo fenix_icon( 'chev-r', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									</summary>
									<ol>
										<?php foreach ( $fenix_toc as $fenix_h ) : ?>
											<li class="toc-l<?php echo esc_attr( $fenix_h['level'] ); ?>"><a href="#<?php echo esc_attr( $fenix_h['id'] ); ?>"><?php echo esc_html( $fenix_h['text'] ); ?></a></li>
										<?php endforeach; ?>
									</ol>
								</details>
							</aside>
						<?php endif; ?>

						<div class="amain">
							<div class="entry-content">
								<?php echo $fenix_content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>

							<div class="article-share">
								<span class="article-share-label">แชร์บทความนี้</span>
								<a class="share-btn share-line" href="<?php echo esc_url( 'https://social-plugins.line.me/lineit/share?url=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener" aria-label="แชร์ไป LINE"><?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<a class="share-btn share-fb" href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ); ?>" target="_blank" rel="noopener" aria-label="แชร์ไป Facebook"><?php echo fenix_icon( 'facebook', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
								<button class="share-btn share-copy" type="button" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="คัดลอกลิงก์"><?php echo fenix_icon( 'link', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
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
								<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<p>
									<?php echo esc_html( fenix_mod( 'risk_text' ) ); ?>
									<a href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">อ่านคำเตือนฉบับเต็ม</a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>

		</article>

		<?php
		fenix_pages_cta(
			fenix_mod( 'post_cta_title' ),
			fenix_mod( 'post_cta_text' ),
			fenix_mod( 'pages_cta_btn' ),
			$fenix_line,
			'line',
			'acta'
		);
		?>

		<?php
		if ( $fenix_cat ) :
			$fenix_related = new WP_Query(
				array(
					'category__in'        => array( $fenix_cat->term_id ),
					'post__not_in'        => array( get_the_ID() ),
					'posts_per_page'      => 9,
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
				)
			);
			if ( $fenix_related->have_posts() ) :
				?>
				<section class="sec alt related-section" aria-label="บทความที่เกี่ยวข้อง">
					<div class="wrap">
						<div class="related-head">
							<h2 class="h2">บทความที่เกี่ยวข้อง</h2>
							<div class="rail-nav">
								<button type="button" class="rail-btn rail-prev" data-dir="-1" aria-label="เลื่อนซ้าย"><?php echo fenix_icon( 'arrow-l', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
								<button type="button" class="rail-btn rail-next" data-dir="1" aria-label="เลื่อนขวา"><?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
							</div>
						</div>
						<div class="related-rail">
							<?php
							while ( $fenix_related->have_posts() ) :
								$fenix_related->the_post();
								?>
								<a class="post-card" href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( fenix_featured_image_alt() ) ) ); ?>
									<?php endif; ?>
									<div class="post-card-body">
										<span class="post-meta">
											<?php echo fenix_icon( 'cal', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
											<?php echo esc_html( get_the_date() ); ?>
										</span>
										<h3><?php the_title(); ?></h3>
									</div>
								</a>
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
	if ( ! fenix_has_seo_plugin() ) :
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
	echo '<script type="application/ld+json">' . wp_json_encode( $fenix_schema, JSON_UNESCAPED_UNICODE ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput
	endif;

	fenix_breadcrumb_jsonld(
		array_values(
			array_filter(
				array(
					array( 'name' => 'หน้าแรก', 'url' => home_url( '/' ) ),
					$fenix_cat ? array( 'name' => $fenix_cat->name, 'url' => get_category_link( $fenix_cat->term_id ) ) : null,
					array( 'name' => get_the_title(), 'url' => get_permalink() ),
				)
			)
		)
	);
	?>

	<?php
endwhile;

get_footer();
