<?php
/**
 * Template Name: FENIX · หน้า Backtest
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * เนื้อหายังอยู่ในตัวแก้ไขของ WordPress เหมือนเดิมทุกตัวอักษร
 * เทมเพลตนี้แค่จัดวางใหม่: หัวหน้าเพจ ปุ่มกระโดด และช่วงของ EA แต่ละตัว
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$bt_content = fenix_pages_dom( apply_filters( 'the_content', get_the_content() ) );
$bt_intro   = '';
$bt_parts   = array(
	'intro'    => array(),
	'sections' => array(),
);

if ( $bt_content ) {
	$bt_parts = fenix_pages_split( $bt_content[1] );

	foreach ( $bt_parts['intro'] as $bt_node ) {
		if ( 'p' === $bt_node->nodeName && '' === $bt_intro ) {
			$bt_intro = fenix_pages_text( $bt_node );
		}
	}
}

if ( '' === $bt_intro ) {
	$bt_intro = (string) fenix_mod( 'backtest_sub' );
}

/* รหัสช่วงของ EA แต่ละตัว ใช้กับปุ่มกระโดด */
$bt_ids = array();
foreach ( $bt_parts['sections'] as $bt_i => $bt_sec ) {
	$bt_slug = 'ea-' . ( $bt_i + 1 );

	foreach ( array( 'btc' => 'BTC', 'plus' => 'PLUS', 'dual' => 'DUAL' ) as $bt_key => $bt_needle ) {
		if ( false !== stripos( $bt_sec['title'], $bt_needle ) ) {
			$bt_slug = $bt_key;
			break;
		}
	}

	$bt_ids[ $bt_i ] = $bt_slug;
}

/* ปุ่มกระโดดในหัวหน้าเพจ · ชื่อปุ่มตัดเอาเฉพาะชื่อ EA ก่อนเครื่องหมาย · */
$bt_jump = '';
foreach ( $bt_parts['sections'] as $bt_i => $bt_sec ) {
	$bt_label = trim( (string) preg_replace( '/\s*·.*$/u', '', $bt_sec['title'] ) );
	$bt_jump .= '<a href="#' . esc_attr( $bt_ids[ $bt_i ] ) . '"><b>' . (int) ( $bt_i + 1 ) . '</b>'
		. esc_html( $bt_label ) . fenix_icon( 'down', 'i' ) . '</a>';
}

if ( '' !== $bt_jump ) {
	$bt_jump = '<div class="jump">' . $bt_jump . '</div>';
}

$bt_title = get_the_title();
?>

<main id="main" class="fxp">

	<?php fenix_phero( $bt_title ? $bt_title : 'Backtest', $bt_intro, '', $bt_jump ); ?>

	<?php
	foreach ( $bt_parts['sections'] as $bt_i => $bt_sec ) :
		$bt_desc = fenix_pages_first_p( $bt_sec['nodes'] );
		?>
		<section class="sec<?php echo 1 === $bt_i % 2 ? ' alt' : ''; ?>" id="<?php echo esc_attr( $bt_ids[ $bt_i ] ); ?>">
			<div class="wrap">
				<div class="eahead">
					<span class="no num"><?php echo (int) ( $bt_i + 1 ); ?></span>
					<div>
						<h2><?php echo esc_html( $bt_sec['title'] ); ?></h2>
						<?php if ( '' !== $bt_desc ) : ?>
							<p><?php echo $bt_desc; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
						<?php endif; ?>
					</div>
				</div>

				<?php echo fenix_backtest_section( $bt_sec['nodes'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
	<?php endforeach; ?>

	<?php if ( ! $bt_parts['sections'] ) : ?>
		<section class="sec">
			<div class="wrap"><?php the_content(); ?></div>
		</section>
	<?php endif; ?>

</main>

<?php
get_footer();
