<?php
/**
 * Template Name: FENIX · หน้าเอกสาร (นโยบาย / เงื่อนไข)
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026 (หน้ากฎหมาย ชุด 3)
 * เนื้อหายังอยู่ในตัวแก้ไขของ WordPress ทุกตัวอักษร เทมเพลตนี้แค่จัดวางใหม่:
 * รูปหน้าปก · คำนำ · การ์ดข้อมูลเอกสาร · หัวข้อคั่นเส้น · กล่องสารบัญด้านขวา
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$lg_title = get_the_title();
$lg_doc   = fenix_legal_parse( apply_filters( 'the_content', get_the_content() ) );
$lg_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : '';
?>

<main id="main" class="fxp fxl">

	<?php fenix_phero( $lg_title ); ?>

	<section class="sec">
		<div class="wrap">

			<?php if ( $lg_doc && $lg_doc['sections'] ) : ?>
				<div class="dgrid">

					<?php fenix_legal_toc( $lg_doc['sections'] ); ?>

					<article class="dmain">

						<?php if ( '' !== $lg_thumb ) : ?>
							<figure class="dthumb">
								<img src="<?php echo esc_url( $lg_thumb ); ?>" alt="<?php echo esc_attr( fenix_featured_image_alt() ); ?>" width="1024" height="538" loading="eager" decoding="async">
							</figure>
						<?php endif; ?>

						<?php if ( '' !== $lg_doc['lead'] ) : ?>
							<p class="dlead"><?php echo $lg_doc['lead']; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>

						<?php fenix_legal_info( $lg_doc['info'] ); ?>

						<?php foreach ( $lg_doc['sections'] as $lg_sec ) : ?>
							<section class="dsec">
								<h2 id="<?php echo esc_attr( $lg_sec['id'] ); ?>"><?php echo fenix_legal_heading( $lg_sec['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
								<?php echo $lg_sec['body']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</section>
						<?php endforeach; ?>

						<?php if ( '' !== $lg_doc['updated'] ) : ?>
							<span class="updated">
								<?php echo fenix_icon( 'clock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( $lg_doc['updated'] ); ?>
							</span>
						<?php endif; ?>

					</article>
				</div>

			<?php else : ?>
				<div class="dmain"><?php the_content(); ?></div>
			<?php endif; ?>

		</div>
	</section>

</main>

<?php
get_footer();
