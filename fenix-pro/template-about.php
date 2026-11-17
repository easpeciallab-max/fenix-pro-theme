<?php
/**
 * Template Name: FENIX · หน้าเกี่ยวกับเรา
 *
 * ดีไซน์ที่เจ้าของอนุมัติเมื่อ 17 พ.ย. 2026
 * เนื้อหายังอยู่ในตัวแก้ไขของ WordPress ทุกตัวอักษร
 * เทมเพลตนี้อ่านหัวข้อ h2 ของหน้าแล้วจัดวางใหม่ตามแบบ
 * หัวข้อไหนรูปแบบไม่ตรงกับที่รู้จัก จะแสดงเป็นข้อความธรรมดาตามเดิม
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$ab_dom   = fenix_pages_dom( apply_filters( 'the_content', get_the_content() ) );
$ab_parts = $ab_dom ? fenix_pages_split( $ab_dom[1] ) : array( 'sections' => array() );
$ab_line  = trim( (string) fenix_mod( 'line_url' ) );
$ab_title = get_the_title();

/* แยกชนิดของแต่ละหัวข้อจากรูปแบบเนื้อหา ไม่ผูกกับลำดับอย่างเดียว */
$ab_blocks = array();

foreach ( $ab_parts['sections'] as $ab_sec ) {
	$ab_items = array();
	$ab_paras = array();

	foreach ( $ab_sec['nodes'] as $ab_node ) {
		if ( in_array( $ab_node->nodeName, array( 'ul', 'ol' ), true ) ) {
			foreach ( $ab_node->getElementsByTagName( 'li' ) as $ab_li ) {
				$ab_items[] = fenix_pages_inner( $ab_li );
			}
			continue;
		}

		if ( 'p' === $ab_node->nodeName ) {
			$ab_paras[] = fenix_pages_inner( $ab_node );
		}
	}

	$ab_kind = 'prose';

	if ( false !== strpos( $ab_sec['title'], 'คำเตือน' ) ) {
		$ab_kind = 'risk';
	} elseif ( false !== strpos( $ab_sec['title'], 'ติดต่อ' ) ) {
		$ab_kind = 'contact';
	} elseif ( $ab_items ) {
		$ab_colon = 0;
		foreach ( $ab_items as $ab_item ) {
			if ( false !== strpos( wp_strip_all_tags( $ab_item ), ':' ) ) {
				$ab_colon++;
			}
		}

		if ( $ab_colon === count( $ab_items ) ) {
			$ab_kind = 'values';
		} elseif ( false !== strpos( $ab_sec['title'], 'ไม่ทำ' ) ) {
			$ab_kind = 'stop';
		} else {
			$ab_kind = 'list';
		}
	}

	$ab_blocks[] = array(
		'kind'  => $ab_kind,
		'title' => $ab_sec['title'],
		'paras' => $ab_paras,
		'items' => $ab_items,
	);
}

$ab_prose = 0;
$ab_pairs = array();

/* จับคู่ "ทำไมต้องเลือก" กับ "สิ่งที่เราไม่ทำ" ไว้แถวเดียวกัน */
foreach ( $ab_blocks as $ab_i => $ab_b ) {
	if ( 'list' === $ab_b['kind'] && isset( $ab_blocks[ $ab_i + 1 ] ) && 'stop' === $ab_blocks[ $ab_i + 1 ]['kind'] ) {
		$ab_pairs[ $ab_i ] = $ab_i + 1;
	}
}
$ab_skip = array_flip( $ab_pairs );
?>

<main id="main" class="fxp">

	<?php fenix_phero( $ab_title ? $ab_title : 'เกี่ยวกับเรา', fenix_mod( 'about_page_sub' ), fenix_mod( 'about_page_kicker' ) ); ?>

	<?php
	$ab_alt = false;

	foreach ( $ab_blocks as $ab_i => $ab_b ) :
		if ( isset( $ab_skip[ $ab_i ] ) ) {
			continue;
		}

		$ab_alt = ! $ab_alt;
		?>
		<section class="sec<?php echo $ab_alt ? '' : ' alt'; ?>">
			<div class="wrap">

				<?php if ( 'prose' === $ab_b['kind'] ) : ?>
					<?php
					$ab_prose++;
					$ab_banner = 1 === $ab_prose ? trim( (string) fenix_mod( 'about_banner' ) ) : '';
					?>
					<?php if ( 1 === $ab_prose ) : ?>
						<div class="who">
							<div>
								<h2 class="h2"><?php echo esc_html( $ab_b['title'] ); ?></h2>
								<?php foreach ( $ab_b['paras'] as $ab_p ) : ?>
									<p><?php echo $ab_p; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
								<?php endforeach; ?>
							</div>

							<?php if ( '' !== $ab_banner ) : ?>
								<figure class="fx-shot">
									<img src="<?php echo esc_url( $ab_banner ); ?>" alt="<?php echo esc_attr( fenix_mod( 'about_banner_alt' ) ); ?>" width="1024" height="538" loading="lazy" decoding="async">
								</figure>
							<?php else : ?>
								<span></span>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="why">
							<h2 class="h2" style="margin-top:0"><?php echo esc_html( $ab_b['title'] ); ?></h2>
							<div>
								<?php foreach ( $ab_b['paras'] as $ab_pi => $ab_p ) : ?>
									<?php if ( 0 === $ab_pi ) : ?>
										<p class="q"><?php echo $ab_p; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
									<?php else : ?>
										<p><?php echo $ab_p; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

				<?php elseif ( 'values' === $ab_b['kind'] ) : ?>
					<div class="shead">
						<h2 class="h2"><?php echo esc_html( $ab_b['title'] ); ?></h2>
					</div>
					<div class="vals">
						<?php
						$ab_icons = array( 'eye', 'shield', 'gauge', 'headset' );
						foreach ( $ab_b['items'] as $ab_n => $ab_item ) :
							$ab_plain = wp_strip_all_tags( $ab_item );
							$ab_cut   = strpos( $ab_plain, ':' );
							$ab_head  = false !== $ab_cut ? trim( substr( $ab_plain, 0, $ab_cut ) ) : $ab_plain;
							$ab_rest  = false !== $ab_cut ? trim( substr( $ab_plain, $ab_cut + 1 ) ) : '';
							?>
							<div class="val">
								<span class="ico"><?php echo fenix_icon( isset( $ab_icons[ $ab_n ] ) ? $ab_icons[ $ab_n ] : 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<h3><?php echo esc_html( $ab_head ); ?></h3>
								<?php if ( '' !== $ab_rest ) : ?>
									<p><?php echo esc_html( $ab_rest ); ?></p>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>

				<?php elseif ( 'list' === $ab_b['kind'] ) : ?>
					<div class="two">
						<div class="fx-card">
							<h2><?php echo esc_html( $ab_b['title'] ); ?></h2>
							<ul class="lst">
								<?php foreach ( $ab_b['items'] as $ab_item ) : ?>
									<li>
										<span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<span><?php echo $ab_item; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>

						<?php if ( isset( $ab_pairs[ $ab_i ] ) ) : ?>
							<?php $ab_stop = $ab_blocks[ $ab_pairs[ $ab_i ] ]; ?>
							<div class="fx-card stopcard">
								<h2><?php echo esc_html( $ab_stop['title'] ); ?></h2>
								<ul class="lst">
									<?php foreach ( $ab_stop['items'] as $ab_item ) : ?>
										<li>
											<span class="dot no"><?php echo fenix_icon( 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
											<span><?php echo $ab_item; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>

				<?php elseif ( 'stop' === $ab_b['kind'] ) : ?>
					<div class="fx-card stopcard">
						<h2><?php echo esc_html( $ab_b['title'] ); ?></h2>
						<ul class="lst">
							<?php foreach ( $ab_b['items'] as $ab_item ) : ?>
								<li>
									<span class="dot no"><?php echo fenix_icon( 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<span><?php echo $ab_item; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

				<?php elseif ( 'risk' === $ab_b['kind'] ) : ?>
					<div class="fx-card riskcard">
						<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<div>
							<h2><?php echo esc_html( $ab_b['title'] ); ?></h2>
							<?php foreach ( $ab_b['paras'] as $ab_p ) : ?>
								<p><?php echo $ab_p; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php endforeach; ?>
						</div>
					</div>

				<?php else : ?>
					<?php
					/* ติดต่อเรา · ย่อหน้าสุดท้ายที่ขึ้นต้นว่า "ปรับปรุงล่าสุด" แยกไปไว้ใต้กล่อง */
					$ab_body = array();
					$ab_upd  = '';

					foreach ( $ab_b['paras'] as $ab_p ) {
						if ( 0 === strpos( wp_strip_all_tags( $ab_p ), 'ปรับปรุงล่าสุด' ) ) {
							$ab_upd = wp_strip_all_tags( $ab_p );
							continue;
						}
						$ab_body[] = $ab_p;
					}
					?>
					<div class="fx-cta">
						<div class="wm" aria-hidden="true"><?php echo esc_html( fenix_mod( 'cta_watermark' ) ); ?></div>
						<div>
							<h2><?php echo esc_html( $ab_b['title'] ); ?></h2>
							<?php foreach ( $ab_body as $ab_p ) : ?>
								<p><?php echo $ab_p; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
							<?php endforeach; ?>
						</div>
						<?php if ( '' !== $ab_line ) : ?>
							<a class="fx-btn fx-btn-black" href="<?php echo esc_url( $ab_line ); ?>" target="_blank" rel="noopener">
								<?php echo fenix_icon( 'line', 'i fill lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'pages_cta_btn' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $ab_upd ) : ?>
						<p class="upd"><?php echo esc_html( $ab_upd ); ?></p>
					<?php endif; ?>
				<?php endif; ?>

			</div>
		</section>
	<?php endforeach; ?>

	<?php if ( ! $ab_blocks ) : ?>
		<section class="sec"><div class="wrap"><?php the_content(); ?></div></section>
	<?php endif; ?>

</main>

<?php
get_footer();
