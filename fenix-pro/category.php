<?php
/**
 * หน้าหมวดหมู่บทความ · ใช้แบบเดียวกับหน้ารวมบทความ
 * (ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026)
 *
 * @package fenix-pro
 */

get_header();

$cat_obj   = get_queried_object();
$cat_title = single_cat_title( '', false );
$cat_sub   = trim( (string) category_description() );
$cat_sub   = '' !== $cat_sub ? wp_strip_all_tags( $cat_sub ) : (string) fenix_mod( 'blog_page_sub' );

$cat_count = '';
if ( isset( $cat_obj->count ) && $cat_obj->count ) {
	$cat_count = '<div class="meta"><span class="count"><b class="num">' . esc_html( number_format_i18n( $cat_obj->count ) ) . '</b>'
		. esc_html( fenix_mod( 'blog_count_label' ) ) . '</span></div>';
}
?>

<main id="main" class="fxp">

	<?php fenix_phero( $cat_title, $cat_sub, fenix_mod( 'blog_kicker' ), $cat_count ); ?>

	<?php
	global $wp_query;
	fenix_articles_body( $wp_query, isset( $cat_obj->slug ) ? array( 'category_name' => $cat_obj->slug ) : array() );
	?>

</main>

<?php
get_footer();
