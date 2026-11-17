<?php
/**
 * Template Name: FENIX · หน้าคู่มือ (ใช้ร่วมกันทุกขั้นตอน)
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026 (หน้าคู่มือ ชุด 2)
 * ห่อเนื้อหาเดิมของแต่ละหน้าไว้ในโครงเดียวกัน: หัวหน้าเพจ · แถบ 8 ขั้น ·
 * กล่องด้านข้าง · เนื้อหา · ปุ่มขั้นก่อนหน้า/ถัดไป · กล่อง CTA
 *
 * เนื้อหาข้างในยังเป็นของเดิมทุกตัวอักษร ไม่ว่าจะเขียนด้วยตัวแก้ไขของ
 * WordPress หรือ Elementor ก็ตาม หน้าแค่เปลี่ยนสไตล์ที่ชี้ไปคลาสเดิม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$fenix_guide = fenix_guide_meta( get_post_field( 'post_name', get_the_ID() ) );
?>

<main id="main" class="fxp fxg">

	<section class="fx-phero ghero">
		<div class="slash" aria-hidden="true"><i></i><i></i><i></i></div>
		<div class="wrap">
			<span class="kick">
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

			<div class="gmeta">
				<?php if ( $fenix_guide['time'] ) : ?>
					<span><?php echo fenix_icon( 'clock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>ใช้เวลาประมาณ <?php echo esc_html( $fenix_guide['time'] ); ?></span>
				<?php endif; ?>
				<?php if ( $fenix_guide['level'] ) : ?>
					<span><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fenix_guide['level'] ); ?></span>
				<?php endif; ?>
				<span><?php echo fenix_icon( 'pulse', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>อัปเดต <?php echo esc_html( get_the_modified_date( 'j M Y' ) ); ?></span>
			</div>
		</div>
	</section>

	<div class="wrap">

		<?php echo fenix_guide_rail( $fenix_guide['slug'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

		<div class="gwrap">

			<aside class="gside">
				<?php if ( ! empty( $fenix_guide['prep'] ) ) : ?>
					<div class="gbox">
						<h2><?php echo fenix_icon( 'flask', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>สิ่งที่ต้องเตรียมก่อนเริ่ม</h2>
						<ul>
							<?php foreach ( $fenix_guide['prep'] as $fenix_item ) : ?>
								<li><span><?php echo esc_html( $fenix_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<nav class="gbox toc" data-guide-toc hidden aria-label="สารบัญในหน้านี้">
					<h2><?php echo fenix_icon( 'list', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>ในหน้านี้มีอะไรบ้าง</h2>
					<ol></ol>
				</nav>
			</aside>

			<article class="gmain">
				<div class="gc">
					<?php the_content(); ?>
				</div>

				<?php if ( $fenix_guide['prev'] || $fenix_guide['next'] ) : ?>
					<nav class="gnav" aria-label="ขั้นตอนก่อนหน้าและถัดไป">
						<?php if ( $fenix_guide['prev'] ) : ?>
							<a class="prev" href="<?php echo esc_url( home_url( '/' . $fenix_guide['prev']['slug'] . '/' ) ); ?>">
								<?php echo fenix_icon( 'arrow-l', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span>
									<small>ขั้นก่อนหน้า</small>
									<b><?php echo esc_html( $fenix_guide['prev']['label'] ); ?></b>
								</span>
							</a>
						<?php endif; ?>

						<?php if ( $fenix_guide['next'] ) : ?>
							<a class="next" href="<?php echo esc_url( home_url( '/' . $fenix_guide['next']['slug'] . '/' ) ); ?>">
								<span>
									<small>ขั้นถัดไป</small>
									<b><?php echo esc_html( $fenix_guide['next']['label'] ); ?></b>
								</span>
								<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>
			</article>

		</div>
	</div>

	<?php
	fenix_pages_cta(
		'ติดตรงไหนบอกได้',
		'ส่งภาพหน้าจอมาทาง LINE ทีมงานดูให้ว่าติดตรงขั้นไหน',
		fenix_mod( 'pages_cta_btn' ),
		trim( (string) fenix_mod( 'line_url' ) )
	);
	?>

</main>

<?php
get_footer();
