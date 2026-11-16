<?php
/**
 * Template Name: FENIX · หน้าคู่มือ (ใช้ร่วมกันทุกขั้นตอน)
 *
 * ห่อเนื้อหาเดิมของแต่ละหน้าไว้ในโครงเดียวกัน: หัวเรื่อง · แถบบอกขั้น ·
 * สิ่งที่ต้องเตรียม · เนื้อหา · ปุ่มขั้นก่อนหน้า/ถัดไป · ปุ่มทัก LINE
 * เนื้อหาข้างในยังเป็นของเดิมทุกตัวอักษร ไม่ว่าจะเขียนด้วยตัวแก้ไขของ
 * WordPress หรือ Elementor ก็ตาม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$fenix_guide = fenix_guide_meta( get_post_field( 'post_name', get_the_ID() ) );
/* ใช้เฉพาะคำโปรยที่เจ้าของเขียนเอง ไม่เอาคำโปรยอัตโนมัติที่ตัดมาจากเนื้อหา */
$fenix_sub = has_excerpt() ? trim( (string) get_the_excerpt() ) : '';
?>

<header class="page-hero guide-hero">
	<div class="container container-narrow">
		<span class="kicker">
			<?php
			if ( $fenix_guide['index'] ) {
				/* translators: 1: ขั้นที่เท่าไร 2: ทั้งหมดกี่ขั้น */
				printf( esc_html__( 'คู่มือ · ขั้นที่ %1$d จาก %2$d', 'fenix-pro' ), (int) $fenix_guide['index'], (int) $fenix_guide['total'] );
			} else {
				echo 'คู่มือ';
			}
			?>
		</span>
		<h1><?php the_title(); ?></h1>

		<?php if ( '' !== $fenix_sub ) : ?>
			<p><?php echo esc_html( $fenix_sub ); ?></p>
		<?php endif; ?>

		<div class="guide-meta">
			<?php if ( $fenix_guide['time'] ) : ?>
				<span><?php echo fenix_icon( 'clock', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>ใช้เวลาประมาณ <?php echo esc_html( $fenix_guide['time'] ); ?></span>
			<?php endif; ?>
			<?php if ( $fenix_guide['level'] ) : ?>
				<span><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fenix_guide['level'] ); ?></span>
			<?php endif; ?>
			<span><?php echo fenix_icon( 'pulse', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>อัปเดต <?php echo esc_html( get_the_modified_date( 'j M Y' ) ); ?></span>
		</div>
	</div>
</header>

<main id="main">

<section class="section guide-section">
	<div class="container container-narrow">

		<?php echo fenix_guide_rail( $fenix_guide['slug'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

		<?php if ( ! empty( $fenix_guide['prep'] ) ) : ?>
			<div class="prep-box reveal">
				<h2><?php echo fenix_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>สิ่งที่ต้องเตรียมก่อนเริ่ม</h2>
				<ul>
					<?php foreach ( $fenix_guide['prep'] as $fenix_item ) : ?>
						<li><?php echo fenix_icon( 'check', 'icon icon-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $fenix_item ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="guide-content">
			<?php the_content(); ?>
		</div>

		<?php if ( $fenix_guide['prev'] || $fenix_guide['next'] ) : ?>
			<nav class="guide-nav" aria-label="ขั้นตอนก่อนหน้าและถัดไป">
				<?php if ( $fenix_guide['prev'] ) : ?>
					<a class="prev" href="<?php echo esc_url( home_url( '/' . $fenix_guide['prev']['slug'] . '/' ) ); ?>">
						<span>&larr; ขั้นก่อนหน้า</span>
						<strong><?php echo esc_html( $fenix_guide['prev']['label'] ); ?></strong>
					</a>
				<?php else : ?>
					<span></span>
				<?php endif; ?>

				<?php if ( $fenix_guide['next'] ) : ?>
					<a class="next" href="<?php echo esc_url( home_url( '/' . $fenix_guide['next']['slug'] . '/' ) ); ?>">
						<span>ขั้นถัดไป &rarr;</span>
						<strong><?php echo esc_html( $fenix_guide['next']['label'] ); ?></strong>
					</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<?php fenix_line_cta( 'ติดตรงไหนบอกได้', 'ส่งภาพหน้าจอมาทาง LINE ทีมงานดูให้ว่าติดตรงขั้นไหน' ); ?>

</main>

<?php
get_footer();
