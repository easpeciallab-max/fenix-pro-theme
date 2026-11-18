<?php
/**
 * Template Name: FENIX · หน้ารวมบทความ
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * บทความเรื่องล่าสุดเป็นการ์ดใหญ่ ที่เหลือเรียงสามคอลัมน์ พร้อมปุ่มโหลดเพิ่มของเดิม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$bl_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) get_option( 'posts_per_page' ),
		'paged'               => 1,
		'ignore_sticky_posts' => true,
	)
);

$bl_count = '';
if ( $bl_query->found_posts ) {
	$bl_count = '<div class="meta"><span class="count"><b class="num">' . esc_html( number_format_i18n( $bl_query->found_posts ) ) . '</b>'
		. esc_html( fenix_mod( 'blog_count_label' ) ) . '</span></div>';
}
?>

<main id="main" class="fxp">

	<?php fenix_phero( get_the_title(), fenix_mod( 'blog_page_sub' ), fenix_mod( 'blog_kicker' ), $bl_count ); ?>

	<?php fenix_articles_body( $bl_query ); ?>

	<?php fenix_cluster_nav(); ?>

</main>

<?php
get_footer();
