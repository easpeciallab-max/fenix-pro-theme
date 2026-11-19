<?php
/**
 * Front page · FENIX PRO EA
 * หน้าแรกตามดีไซน์ที่เจ้าของอนุมัติเมื่อ 16 พ.ย. 2026
 *
 * โครง 18 ส่วน: Hero · EA ในชุด · EA คืออะไร · ปัญหา · เทรดเอง vs EA ·
 * ขั้นตอน · จุดเด่น · Dashboard · เหมาะกับใคร · ผลทดสอบ · เสียงผู้ใช้ ·
 * อุปกรณ์ · ทีมงาน · แพ็กเกจ · ดูข้อมูลต่อ · FAQ · บทความ · CTA + คำเตือน
 *
 * Header / Footer / แถบเมนูล่างบนมือถือ ใช้ของเดิมทั้งหมด ไม่แก้
 * ข้อความทุกจุดอ่านจาก Customizer (fenix_mod) ไม่ hardcode
 *
 * @package fenix-pro
 */

get_header();

if ( fenix_has_elementor_content() && fenix_uses_elementor_page_template() ) :
	?>
	<main id="main" class="elementor-page-shell elementor-page-shell--front">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class( 'elementor-entry' ); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	</main>
	<?php
	get_footer();
	return;
endif;

$fx_line    = trim( (string) fenix_mod( 'line_url' ) );
$fx_logo    = fenix_logo_url();
$fx_go      = home_url( '/go/' );
$fx_deck    = fenix_hero_deck_cards();
$fx_vers    = fenix_home_versions();
$fx_vers_tx = $fx_vers ? trim( (string) fenix_mod( 'hero_pill_prefix' ) ) . ' ' . implode( ' · ', $fx_vers ) : '';
?>

<main id="main" class="fxh">

<?php /* ============ 01 · HERO ============ */ ?>
<?php if ( fenix_mod( 'show_hero' ) ) : ?>
<section class="fx-hero" id="hero">
	<div class="wrap">
		<div class="hero-g">
			<div>
				<?php if ( '' !== $fx_vers_tx ) : ?>
					<span class="pill-note"><b><?php echo esc_html( fenix_mod( 'hero_pill_tag' ) ); ?></b><?php echo esc_html( $fx_vers_tx ); ?></span>
				<?php endif; ?>

				<?php
				$fx_title = trim( (string) fenix_mod( 'hero_title' ) );
				$fx_cut   = mb_strpos( $fx_title, ' ' );
				$fx_line1 = false !== $fx_cut ? mb_substr( $fx_title, 0, $fx_cut ) : $fx_title;
				$fx_line2 = false !== $fx_cut ? mb_substr( $fx_title, $fx_cut + 1 ) : '';
				?>
				<h1>
					<span><?php echo esc_html( $fx_line1 ); ?></span>
					<?php if ( '' !== $fx_line2 ) : ?>
						<span class="or"><?php echo esc_html( $fx_line2 ); ?></span>
					<?php endif; ?>
				</h1>

				<p class="tagline"><?php echo esc_html( fenix_mod( 'hero_subtitle' ) ); ?></p>
				<p class="fx-lead"><?php echo esc_html( fenix_mod( 'hero_desc' ) ); ?></p>

				<div class="hero-cta">
					<?php if ( '' !== $fx_line ) : ?>
						<a class="fx-btn fx-btn-or" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
							<?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'hero_btn1_text' ) ); ?>
						</a>
					<?php endif; ?>
					<a class="fx-btn fx-btn-dark" href="#about">
						<?php echo esc_html( fenix_mod( 'hero_btn2_text' ) ); ?>
						<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</div>

				<div class="hero-mini">
					<?php for ( $fx_i = 1; $fx_i <= 3; $fx_i++ ) : ?>
						<?php
						$fx_mv = trim( (string) fenix_mod( 'hero_mini' . $fx_i . '_value' ) );
						$fx_ml = trim( (string) fenix_mod( 'hero_mini' . $fx_i . '_label' ) );
						if ( '' === $fx_mv ) {
							continue;
						}
						?>
						<div><b><?php echo esc_html( $fx_mv ); ?></b><?php echo esc_html( $fx_ml ); ?></div>
					<?php endfor; ?>
				</div>
			</div>

			<?php if ( $fx_deck ) : ?>
				<?php
				/* ปกวางเหลื่อมเป็นพัด · ใบหน้าสุดคือตัวที่เลือกเป็นตัวเด่นในหน้า /go/ */
				$fx_front = $fx_deck[0];
				$fx_back  = array_slice( $fx_deck, 1 );
				$fx_mode  = 'file' === fenix_mod( 'hero_deck_cta_mode' ) ? 'file' : 'page';
				$fx_slots = array( 'c1', 'c2' );
				?>
				<div class="fan">
					<?php foreach ( array_reverse( $fx_back ) as $fx_i => $fx_card ) : ?>
						<?php
						$fx_href = 'file' === $fx_mode && '' !== $fx_card['file'] ? $fx_card['file'] : $fx_go;
						$fx_pos  = isset( $fx_slots[ $fx_i ] ) ? $fx_slots[ $fx_i ] : 'c2';
						?>
						<a class="cv <?php echo esc_attr( $fx_pos ); ?>" href="<?php echo esc_url( $fx_href ); ?>">
							<img src="<?php echo esc_url( $fx_card['img'] ); ?>" alt="<?php echo esc_attr( $fx_card['alt'] ); ?>" width="1200" height="675" loading="lazy" decoding="async">
						</a>
					<?php endforeach; ?>

					<a class="cv c3" href="<?php echo esc_url( 'file' === $fx_mode && '' !== $fx_front['file'] ? $fx_front['file'] : $fx_go ); ?>">
						<img src="<?php echo esc_url( $fx_front['img'] ); ?>" alt="<?php echo esc_attr( $fx_front['alt'] ); ?>" width="1200" height="675" loading="eager" fetchpriority="high" decoding="async">
					</a>

					<?php if ( fenix_mod( 'hero_status_text' ) ) : ?>
						<div class="fchip status">
							<span class="live-dot"></span>
							<?php echo esc_html( trim( $fx_front['name'] . ' ' . $fx_front['version'] ) . ' · ' . fenix_mod( 'hero_status_text' ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( fenix_mod( 'hero_toast_title' ) ) : ?>
						<div class="fchip toast">
							<span class="ico"><?php echo fenix_icon( 'zap', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<div>
								<b><?php echo esc_html( fenix_mod( 'hero_toast_title' ) ); ?></b>
								<?php echo esc_html( fenix_mod( 'hero_toast_text' ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( fenix_mod( 'hero_proof_value' ) ) : ?>
						<div class="fchip proof">
							<small><?php echo esc_html( fenix_mod( 'hero_proof_top' ) ); ?></small>
							<div class="big num"><?php echo esc_html( fenix_mod( 'hero_proof_value' ) ); ?></div>
							<small><?php echo esc_html( fenix_mod( 'hero_proof_bottom' ) ); ?></small>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php
	/* เทปพาดเฉียง 2 เส้นวิ่งสวนทางกัน · ข้อความชุดเดียวพิมพ์ซ้ำเพื่อให้วนต่อเนื่อง */
	$fx_tape1 = fenix_home_versions( true );
	$fx_tape2 = array();
	for ( $fx_i = 1; $fx_i <= 4; $fx_i++ ) {
		$fx_h = trim( (string) fenix_mod( 'highlight' . $fx_i ) );
		if ( '' !== $fx_h ) {
			$fx_tape2[] = $fx_h;
		}
	}
	if ( '' !== $fx_vers_tx ) {
		$fx_tape2[] = $fx_vers_tx;
	}
	$fx_spark = fenix_icon( 'spark', 'i fill' );
	?>
	<?php if ( $fx_tape1 || $fx_tape2 ) : ?>
	<div class="tapes" aria-hidden="true">
		<?php
		foreach ( array(
			't1' => array( implode( ' · ', $fx_tape1 ) ),
			't2' => $fx_tape2,
		) as $fx_cls => $fx_items ) :
			$fx_items = array_filter( $fx_items );
			if ( ! $fx_items ) {
				continue;
			}
			?>
			<div class="tape <?php echo esc_attr( $fx_cls ); ?>">
				<div class="tape-track">
					<?php for ( $fx_r = 0; $fx_r < 2; $fx_r++ ) : ?>
						<?php foreach ( $fx_items as $fx_item ) : ?>
							<span><?php echo esc_html( $fx_item ); ?></span><?php echo $fx_spark; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php endforeach; ?>
					<?php endfor; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</section>
<?php else : ?>
	<h1 class="sr-only"><?php echo esc_html( fenix_mod( 'hero_title' ) ? fenix_mod( 'hero_title' ) : get_bloginfo( 'name' ) ); ?></h1>
<?php endif; ?>


<?php /* ============ 02 · EA ในชุด FENIX PRO ============ */ ?>
<?php if ( fenix_mod( 'show_products' ) && $fx_deck ) : ?>
<section class="sec alt" id="products">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'products_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'products_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'products_sub' ) ); ?></p>
		</div>

		<div class="prod-g up">
			<?php
			$fx_mode = 'file' === fenix_mod( 'hero_deck_cta_mode' ) ? 'file' : 'page';
			$fx_cta  = trim( (string) fenix_mod( 'hero_deck_cta' ) );
			foreach ( $fx_deck as $fx_i => $fx_card ) :
				$fx_href = 'file' === $fx_mode && '' !== $fx_card['file'] ? $fx_card['file'] : $fx_go;
				?>
				<div class="prod">
					<img src="<?php echo esc_url( $fx_card['img'] ); ?>" alt="<?php echo esc_attr( $fx_card['alt'] ); ?>" width="1200" height="675" loading="lazy" decoding="async">
					<div class="b">
						<div class="nm">
							<b><?php echo esc_html( $fx_card['name'] ); ?></b>
							<?php if ( '' !== $fx_card['version'] ) : ?>
								<span class="fx-badge <?php echo 0 === $fx_i ? 'or' : 'dark'; ?>">
									<?php echo esc_html( ( 0 === $fx_i ? fenix_mod( 'prod_badge_latest' ) : fenix_mod( 'prod_badge_other' ) ) . ' ' . $fx_card['version'] ); ?>
								</span>
							<?php endif; ?>
						</div>

						<div class="spec">
							<?php
							foreach ( array( 1, 2 ) as $fx_n ) :
								$fx_spec = fenix_rows( fenix_mod( 'prod_' . $fx_card['slot'] . '_spec' . $fx_n ), 2 );
								if ( ! $fx_spec ) {
									continue;
								}
								?>
								<div><?php echo esc_html( $fx_spec[0]['cells'][0] ); ?><b><?php echo esc_html( $fx_spec[0]['cells'][1] ); ?></b></div>
							<?php endforeach; ?>
						</div>

						<?php if ( '' !== $fx_cta ) : ?>
							<a class="fx-btn <?php echo 0 === $fx_i ? 'fx-btn-or' : 'fx-btn-dark'; ?> fx-btn-full" href="<?php echo esc_url( $fx_href ); ?>">
								<?php echo fenix_icon( 'download', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( $fx_cta ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 03 · EA คืออะไร แล้วมันทำงานยังไง ============ */ ?>
<?php if ( fenix_mod( 'show_about' ) ) : ?>
<section class="sec" id="about">
	<div class="wrap">
		<div class="about-g up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'about_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'about_title' ) ); ?></h2>

				<?php
				$fx_bold = trim( (string) fenix_mod( 'about_bold' ) );
				$fx_p    = 0;
				foreach ( fenix_lines( fenix_mod( 'about_text' ) ) as $fx_para ) :
					$fx_html = esc_html( $fx_para );
					if ( '' !== $fx_bold ) {
						$fx_html = str_replace( esc_html( $fx_bold ), '<b>' . esc_html( $fx_bold ) . '</b>', $fx_html );
					}
					$fx_p++;
					?>
					<p class="fx-lead"<?php echo 1 === $fx_p ? ' style="margin-top:24px"' : ''; ?>><?php echo $fx_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
				<?php endforeach; ?>
			</div>

			<div class="chartbox">
				<div class="cb-head">
					<span class="pair"><?php echo esc_html( fenix_mod( 'about_chart_pair' ) ); ?> <small><?php echo esc_html( fenix_mod( 'about_chart_note' ) ); ?></small></span>
					<span class="run"><span class="live-dot"></span><?php echo esc_html( fenix_mod( 'about_chart_run' ) ); ?></span>
				</div>

				<?php
				/* กราฟแท่งเทียน · วาดเป็น SVG นิ่งจากค่าที่ล็อกไว้ ไม่ต้องรัน JS */
				$fx_w   = 640;
				$fx_h   = 320;
				$fx_pad = array(
					'l' => 10,
					'r' => 10,
					't' => 46,
					'b' => 20,
				);
				$fx_ser = fenix_candle_series(
					11,
					46,
					function ( $i ) {
						if ( $i < 22 ) {
							return -0.32;
						}
						return $i < 28 ? 0 : 0.55;
					}
				);
				$fx_g  = fenix_candles( $fx_ser, $fx_w, $fx_h, $fx_pad, array( 'grid' => 1 ) );
				$fx_gx = $fx_g['x'];
				$fx_gy = $fx_g['y'];
				$fx_ei = 28;
				$fx_xi = 41;
				$fx_ep = $fx_gy( $fx_ser[ $fx_ei ]['c'] );
				$fx_xp = $fx_gy( $fx_ser[ $fx_xi ]['c'] );
				$fx_svg  = $fx_g['svg'];
				$fx_svg .= '<rect x="' . ( $fx_gx( 0 ) - 6 ) . '" y="' . ( $fx_pad['t'] - 30 ) . '" width="' . ( $fx_gx( $fx_ei - 1 ) - $fx_gx( 0 ) + 12 ) . '" height="' . ( $fx_h - $fx_pad['t'] - $fx_pad['b'] + 30 ) . '" rx="12" fill="none" stroke="#FF6A00" stroke-width="2" stroke-dasharray="6 6"/>';
				$fx_svg .= '<line x1="' . $fx_gx( $fx_ei ) . '" x2="' . $fx_gx( $fx_xi ) . '" y1="' . $fx_ep . '" y2="' . $fx_ep . '" stroke="#F4F4F5" stroke-width="1.5" stroke-dasharray="5 5"/>';
				$fx_svg .= '<line x1="' . $fx_gx( $fx_ei ) . '" x2="' . $fx_gx( $fx_xi ) . '" y1="' . $fx_ep . '" y2="' . $fx_xp . '" stroke="#FF6A00" stroke-width="2.5"/>';
				$fx_svg .= '<circle cx="' . $fx_gx( $fx_ei ) . '" cy="' . $fx_ep . '" r="9" fill="#FF6A00" stroke="#131317" stroke-width="4"/>';
				$fx_svg .= '<circle cx="' . $fx_gx( $fx_xi ) . '" cy="' . $fx_xp . '" r="9" fill="#F4F4F5" stroke="#131317" stroke-width="4"/>';

				$fx_flow = array(
					fenix_mod( 'about_flow1' ),
					fenix_mod( 'about_flow2' ),
					fenix_mod( 'about_flow3' ),
				);
				?>
				<div class="cb-area">
					<svg viewBox="0 0 <?php echo (int) $fx_w; ?> <?php echo (int) $fx_h; ?>" role="img" aria-label="<?php echo esc_attr( fenix_mod( 'about_chart_pair' ) . ' ' . fenix_mod( 'about_chart_note' ) ); ?>">
						<?php echo $fx_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</svg>
					<span class="co" style="left:<?php echo esc_attr( round( $fx_gx( 10 ) / $fx_w * 100, 2 ) ); ?>%;top:<?php echo esc_attr( round( ( $fx_pad['t'] - 16 ) / $fx_h * 100, 2 ) ); ?>%"><b>1</b><?php echo esc_html( $fx_flow[0] ); ?></span>
					<span class="co" style="left:<?php echo esc_attr( round( $fx_gx( $fx_ei ) / $fx_w * 100, 2 ) ); ?>%;top:<?php echo esc_attr( round( ( $fx_ep + 52 ) / $fx_h * 100, 2 ) ); ?>%"><b>2</b><?php echo esc_html( $fx_flow[1] ); ?></span>
					<span class="co dk" style="left:<?php echo esc_attr( round( min( $fx_gx( $fx_xi ), $fx_w - 90 ) / $fx_w * 100, 2 ) ); ?>%;top:<?php echo esc_attr( round( ( $fx_xp - 6 ) / $fx_h * 100, 2 ) ); ?>%"><b>3</b><?php echo esc_html( $fx_flow[2] ); ?></span>
				</div>

				<div class="cb-steps">
					<?php foreach ( $fx_flow as $fx_i => $fx_item ) : ?>
						<div><b><?php echo (int) ( $fx_i + 1 ); ?></b><?php echo esc_html( $fx_item ); ?></div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 04 · เทรดเองแล้วเจอปัญหาแบบนี้ไหม ============ */ ?>
<?php if ( fenix_mod( 'show_pain' ) ) : ?>
<section class="sec alt" id="problem">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'pain_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'pain_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'pain_subtitle' ) ); ?></p>
		</div>

		<?php $fx_you = fenix_rows( fenix_mod( 'pain_day_you' ), 2 ); ?>
		<?php if ( $fx_you ) : ?>
		<div class="fx-card day up">
			<div class="day-h">
				<b><?php echo esc_html( fenix_mod( 'pain_day_title' ) ); ?></b>
				<span class="illus-note">ภาพประกอบ</span>
			</div>

			<div class="ruler" aria-hidden="true">
				<span></span>
				<?php foreach ( array( '00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00' ) as $fx_t ) : ?>
					<span><?php echo esc_html( $fx_t ); ?></span>
				<?php endforeach; ?>
				<span></span>
			</div>

			<div class="lane">
				<span class="who">คุณ</span>
				<div class="segs">
					<?php foreach ( $fx_you as $fx_seg ) : ?>
						<span class="seg<?php echo '+' === $fx_seg['flag'] ? ' free' : ''; ?>" style="flex:<?php echo esc_attr( (float) $fx_seg['cells'][0] ); ?>"><?php echo fenix_hm( $fx_seg['cells'][1] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="lane">
				<span class="who or">EA</span>
				<div class="segs">
					<span class="seg ea" style="flex:24">
						<?php echo fenix_icon( 'eye', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo fenix_hm( fenix_mod( 'pain_day_ea' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</span>
				</div>
			</div>

			<div class="day-note">
				<span><i style="background:var(--t1)"></i><?php echo esc_html( fenix_mod( 'pain_day_note1' ) ); ?></span>
				<span><i style="background:var(--or)"></i><?php echo esc_html( fenix_mod( 'pain_day_note2' ) ); ?></span>
			</div>
		</div>
		<?php endif; ?>

		<?php
		$fx_chk = array();
		for ( $fx_i = 1; $fx_i <= 4; $fx_i++ ) {
			$fx_t = trim( (string) fenix_mod( 'pain' . $fx_i . '_title' ) );
			if ( '' !== $fx_t ) {
				$fx_chk[] = array( $fx_t, fenix_mod( 'pain' . $fx_i . '_desc' ) );
			}
		}
		?>
		<?php if ( $fx_chk ) : ?>
			<div class="chk-h up">
				<b><?php echo esc_html( fenix_mod( 'pain_chk_title' ) ); ?></b>
			</div>

			<div class="chk-g up" data-chk>
				<?php foreach ( $fx_chk as $fx_item ) : ?>
					<button type="button" class="chk" data-chk-item aria-pressed="false">
						<span class="box"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span>
							<h4><?php echo esc_html( $fx_item[0] ); ?></h4>
							<p><?php echo esc_html( $fx_item[1] ); ?></p>
						</span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="chk-res up">
				<span data-chk-out
					data-tpl="<?php echo esc_attr( fenix_mod( 'pain_chk_res' ) ); ?>"
					data-zero="<?php echo esc_attr( fenix_mod( 'pain_chk_zero' ) ); ?>"><?php echo esc_html( fenix_mod( 'pain_chk_zero' ) ); ?></span>
				<a class="fx-btn fx-btn-or" href="#compare">
					<?php echo esc_html( fenix_mod( 'pain_chk_btn' ) ); ?>
					<?php echo fenix_icon( 'down', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 05 · เทรดเอง vs ให้ FENIX PRO EA ทำตามแผน ============ */ ?>
<?php
$fx_vs = fenix_rows( fenix_mod( 'vs_rows' ), 3 );
$fx_vr = fenix_rows( fenix_mod( 'vs_risk_row' ), 3 );
?>
<?php if ( fenix_mod( 'show_vs' ) && $fx_vs ) : ?>
<section class="sec" id="compare">
	<div class="wrap">
		<div class="shead center up">
			<span class="eyebrow"><?php echo esc_html( fenix_mod( 'vs_kicker' ) ); ?></span>
			<h2 class="h2"><?php echo esc_html( fenix_mod( 'vs_title' ) ); ?></h2>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'vs_sub' ) ); ?></p>
		</div>

		<div class="vs up">
			<div class="vs-head">
				<div class="side me">
					<span class="ico"><?php echo fenix_icon( 'user', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php echo esc_html( fenix_mod( 'vs_left_label' ) ); ?>
				</div>
				<div class="vs-c"><span>VS</span></div>
				<div class="side fx">
					<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="52" height="52" loading="lazy" decoding="async">
					<?php echo esc_html( fenix_mod( 'vs_right_label' ) ); ?>
				</div>
			</div>

			<?php
			/* แถว "ความเสี่ยงของตลาด" ต้องอยู่เสมอ (สเปกข้อ 1.7 และรูป 05 ข้อ 5) */
			$fx_all = $fx_vs;
			if ( $fx_vr ) {
				$fx_vr[0]['flag'] = '!';
				$fx_all[]         = $fx_vr[0];
			}
			foreach ( $fx_all as $fx_row ) :
				$fx_warn = '!' === $fx_row['flag'];
				?>
				<div class="vs-row<?php echo $fx_warn ? ' risk' : ''; ?>">
					<div class="l">
						<span class="dot <?php echo $fx_warn ? 'warn' : 'no'; ?>"><?php echo fenix_icon( $fx_warn ? 'warn' : 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php echo esc_html( $fx_row['cells'][1] ); ?>
					</div>
					<div class="k"><?php echo esc_html( $fx_row['cells'][0] ); ?></div>
					<div class="r">
						<span class="dot <?php echo $fx_warn ? 'warn' : 'ok'; ?>"><?php echo fenix_icon( $fx_warn ? 'warn' : 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php echo esc_html( $fx_row['cells'][2] ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 06 · เริ่มใช้งานใน 4 ขั้นตอน ============ */ ?>
<?php if ( fenix_mod( 'show_steps' ) ) : ?>
<section class="sec alt" id="workflow">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'steps_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'steps_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'steps_subtitle' ) ); ?></p>
		</div>

		<div class="wf-g up">
			<?php
			$fx_hl = (int) fenix_mod( 'steps_hl' );
			for ( $fx_i = 1; $fx_i <= 4; $fx_i++ ) :
				$fx_t = trim( (string) fenix_mod( 'step' . $fx_i . '_title' ) );
				if ( '' === $fx_t ) {
					continue;
				}
				?>
				<div class="wf<?php echo $fx_i === $fx_hl ? ' hl' : ''; ?>">
					<div class="ill" aria-hidden="true">
						<?php if ( 1 === $fx_i ) : ?>
							<div class="win">
								<div class="tb"><i></i><i></i><i></i><span>MetaTrader 5</span></div>
								<div class="bd">
									<div class="pbar"><span></span></div>
									<div class="ok">
										<span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<?php echo esc_html( fenix_mod( 'step1_pic' ) ); ?>
									</div>
								</div>
							</div>
						<?php elseif ( 2 === $fx_i ) : ?>
							<div class="flow2">
								<span class="filechip"><?php echo fenix_icon( 'file', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'step2_pic' ) ); ?></span>
								<?php echo fenix_icon( 'down', 'i arr' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<span class="eaon"><span class="live-dot"></span><?php echo esc_html( fenix_mod( 'step2_pic2' ) ); ?></span>
							</div>
						<?php elseif ( 3 === $fx_i ) : ?>
							<div style="width:100%">
								<div class="seg2"><span class="on">DEMO</span><span>REAL</span></div>
								<div class="ill-cap"><?php echo esc_html( fenix_mod( 'step3_pic' ) ); ?></div>
							</div>
						<?php else : ?>
							<div class="slider">
								<span class="val"><?php echo esc_html( fenix_mod( 'step4_pic' ) ); ?></span>
								<div class="tr"><span></span><b></b></div>
								<div class="lb"><span>เล็ก</span><span>ใหญ่</span></div>
							</div>
						<?php endif; ?>
					</div>
					<div class="n">0<?php echo (int) $fx_i; ?></div>
					<h4><?php echo esc_html( $fx_t ); ?></h4>
					<p><?php echo esc_html( fenix_mod( 'step' . $fx_i . '_desc' ) ); ?></p>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 07 · จุดเด่นของ FENIX PRO EA ============ */ ?>
<?php if ( fenix_mod( 'show_features' ) ) : ?>
<section class="sec" id="features">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'features_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'features_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'features_subtitle' ) ); ?></p>
		</div>

		<div class="bento up">
			<div class="bt w2">
				<div class="vis" aria-hidden="true">
					<div class="autolog">
						<div class="hd">AUTO TRADING<span class="toggle"></span></div>
						<?php foreach ( fenix_rows( fenix_mod( 'feat1_log' ), 3 ) as $fx_log ) : ?>
							<div class="ln">
								<time><?php echo esc_html( $fx_log['cells'][0] ); ?></time>
								<span class="d"></span>
								<span><b><?php echo esc_html( $fx_log['cells'][1] ); ?></b> · <?php echo esc_html( $fx_log['cells'][2] ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<h4><?php echo esc_html( fenix_mod( 'feat1_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'feat1_desc' ) ); ?></p>
			</div>

			<div class="bt">
				<div class="vis" aria-hidden="true"><div class="mt5big">MT<span>5</span></div></div>
				<h4><?php echo esc_html( fenix_mod( 'feat2_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'feat2_desc' ) ); ?></p>
			</div>

			<div class="bt">
				<div class="vis" aria-hidden="true">
					<div class="mini-ui">
						<div class="row">
							<div class="t">วันนี้<b class="g">+ ●●●</b></div>
							<div class="t">DD<b>●●%</b></div>
						</div>
						<div class="t inline"><span class="live-dot"></span><b>EA กำลังรัน</b></div>
						<div class="sk" style="width:80%"></div>
						<div class="sk" style="width:55%"></div>
					</div>
				</div>
				<h4><?php echo esc_html( fenix_mod( 'feat3_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'feat3_desc' ) ); ?></p>
			</div>

			<div class="bt">
				<div class="vis col" aria-hidden="true">
					<svg class="gauge" viewBox="0 0 200 112">
						<path d="M16 100a84 84 0 0 1 56-79" stroke="#22C55E" stroke-width="18" fill="none"/>
						<path d="M78 19a84 84 0 0 1 50 2" stroke="#FACC15" stroke-width="18" fill="none"/>
						<path d="M134 23a84 84 0 0 1 50 77" stroke="#EF4444" stroke-width="18" fill="none"/>
						<line x1="100" y1="100" x2="52" y2="48" stroke="#F4F4F5" stroke-width="6" stroke-linecap="round"/>
						<circle cx="100" cy="100" r="11" fill="#F4F4F5"/>
					</svg>
					<div class="gauge-lb">
						<?php foreach ( fenix_lines( fenix_mod( 'feat4_chips' ) ) as $fx_chip ) : ?>
							<span class="fx-badge dark"><?php echo esc_html( $fx_chip ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
				<h4><?php echo esc_html( fenix_mod( 'feat4_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'feat4_desc' ) ); ?></p>
			</div>

			<div class="bt">
				<div class="vis" aria-hidden="true">
					<?php $fx_ring = fenix_rows( fenix_mod( 'feat5_ring' ), 2 ); ?>
					<div class="ring">
						<svg viewBox="0 0 150 150" width="150" height="150">
							<circle cx="75" cy="75" r="62" stroke="#2A2A31" stroke-width="14" fill="none"/>
							<circle cx="75" cy="75" r="62" stroke="#FF6A00" stroke-width="14" fill="none" stroke-dasharray="389.6" stroke-dashoffset="0"/>
						</svg>
						<div class="c">
							<div>
								<b><?php echo esc_html( $fx_ring ? $fx_ring[0]['cells'][0] : '' ); ?></b>
								<small><?php echo esc_html( $fx_ring ? $fx_ring[0]['cells'][1] : '' ); ?></small>
							</div>
						</div>
					</div>
				</div>
				<h4><?php echo esc_html( fenix_mod( 'feat5_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'feat5_desc' ) ); ?></p>
			</div>

			<div class="bt w2 split">
				<div>
					<h4><?php echo esc_html( fenix_mod( 'feat6_title' ) ); ?></h4>
					<p style="margin-top:10px"><?php echo esc_html( fenix_mod( 'feat6_desc' ) ); ?></p>
				</div>
				<div class="vis col" aria-hidden="true">
					<div class="lots">
						<span class="on" style="height:22%"></span>
						<span style="height:38%"></span>
						<span style="height:54%"></span>
						<span style="height:72%"></span>
						<span style="height:100%"></span>
					</div>
					<?php $fx_lots = fenix_rows( fenix_mod( 'feat6_lots' ), 3 ); ?>
					<div class="lots-lb">
						<?php foreach ( ( $fx_lots ? $fx_lots[0]['cells'] : array() ) as $fx_lb ) : ?>
							<span><?php echo esc_html( $fx_lb ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 08 · ดูทุกอย่างได้ในหน้าจอเดียว ============ */ ?>
<?php if ( fenix_mod( 'show_dash' ) ) : ?>
<section class="sec alt" id="dashboard">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'dash_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'dash_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'dash_sub' ) ); ?></p>
		</div>

		<div class="dash-g up">
			<div class="stage">
				<?php
				$fx_shot   = trim( (string) fenix_mod( 'dash_image' ) );
				$fx_shot_m = trim( (string) fenix_mod( 'dash_image_m' ) );
				?>

				<?php if ( '' !== $fx_shot ) : ?>
					<figure class="dash-shot<?php echo '' !== $fx_shot_m ? ' has-m' : ''; ?>">
						<picture>
							<?php if ( '' !== $fx_shot_m ) : ?>
								<source media="(max-width: 760px)" srcset="<?php echo esc_url( $fx_shot_m ); ?>">
							<?php endif; ?>
							<img src="<?php echo esc_url( $fx_shot ); ?>" alt="<?php echo esc_attr( fenix_mod( 'dash_title' ) ); ?>" loading="lazy" decoding="async">
						</picture>
					</figure>
				<?php else : ?>
					<div class="browser" aria-hidden="true">
						<div class="bbar">
							<i></i><i></i><i></i>
							<span class="url"><?php echo fenix_icon( 'lock', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="keep-case"><?php echo esc_html( fenix_mod( 'dash_host' ) ); ?></span></span>
						</div>
						<div class="db">
							<div class="db-side">
								<?php
								$fx_menu_icons = array( 'home', 'plusbox', 'cpu', 'chart', 'list', 'clock', 'cal', 'bell', 'sliders' );
								foreach ( fenix_lines( fenix_mod( 'dash_menu' ) ) as $fx_i => $fx_item ) :
									?>
									<span<?php echo 0 === $fx_i ? ' class="on"' : ''; ?>>
										<?php echo fenix_icon( isset( $fx_menu_icons[ $fx_i ] ) ? $fx_menu_icons[ $fx_i ] : 'list', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<?php echo esc_html( $fx_item ); ?>
									</span>
								<?php endforeach; ?>
							</div>

							<div class="db-main">
								<div class="db-row">
									<div class="db-card db-bal">
										<span>ยอดเงินรวม · ภาพตัวอย่าง</span>
										<b class="num">12,480.00<small>USC</small></b>
										<div class="db-k">
											<div>กำไรวันนี้<b class="num">+86.40</b></div>
											<div>สัปดาห์นี้<b class="num">+312.75</b></div>
											<div>เดือนนี้<b class="num">+1,042.10</b></div>
											<div>DD สูงสุด<b class="w num">8.20%</b></div>
										</div>
									</div>
									<div class="db-card db-chart">
										<div class="hd">XAUUSD<div class="tf"><span>M15</span><span class="on">H1</span><span>H4</span></div></div>
										<?php
										$fx_dser = fenix_candle_series(
											5,
											36,
											function ( $i ) {
												return $i < 18 ? 0.35 : -0.25;
											}
										);
										$fx_dg = fenix_candles(
											$fx_dser,
											300,
											150,
											array(
												'l' => 4,
												'r' => 4,
												't' => 8,
												'b' => 8,
											),
											array(
												'grid' => 1,
												'wick' => 1.2,
											)
										);
										?>
										<div class="plot"><svg viewBox="0 0 300 150"><?php echo $fx_dg['svg']; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg></div>
									</div>
								</div>

								<div class="db-card">
									<div class="db-st">
										<div><span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>เชื่อมต่อ MT5 ปกติ</div>
										<div><span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>EA ทำงานอยู่</div>
										<div><span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>ไม่มีออเดอร์ค้าง</div>
									</div>
								</div>

								<?php $fx_news = fenix_rows( fenix_mod( 'dash_news' ), 2 ); ?>
								<?php if ( $fx_news ) : ?>
									<div class="db-card db-news">
										<span class="ico"><?php echo fenix_icon( 'shield', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
										<div><?php echo esc_html( $fx_news[0]['cells'][0] ); ?><small><?php echo esc_html( $fx_news[0]['cells'][1] ); ?></small></div>
										<span class="toggle"></span>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="phone" aria-hidden="true">
						<div class="scr">
							<div class="ph-h">
								<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="22" height="22" loading="lazy" decoding="async">
								FENIX <span class="or">PRO</span><span class="burg"></span>
							</div>
							<div class="ph-b">
								<div class="db-card db-bal">
									<span>ยอดเงินรวม</span>
									<b class="num">12,480.00</b>
									<div class="db-k">
										<div>วันนี้<b class="num">+86.40</b></div>
										<div>DD<b class="w num">8.20%</b></div>
									</div>
								</div>
								<div class="db-card row"><span class="live-dot"></span>EA ทำงานอยู่ · 2 บัญชี</div>
								<div class="db-card row"><?php echo fenix_icon( 'shield', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>News Guard เปิดอยู่</div>
							</div>
							<div class="tabbar">
								<span class="on"><?php echo fenix_icon( 'home', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>หน้าหลัก</span>
								<span><?php echo fenix_icon( 'plusbox', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>บัญชี</span>
								<span><?php echo fenix_icon( 'cpu', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>เลือก EA</span>
								<span><?php echo fenix_icon( 'list', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>เพิ่มเติม</span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="feat-l">
				<?php
				foreach ( fenix_rows( fenix_mod( 'dash_items' ), 3 ) as $fx_item ) :
					?>
					<div>
						<span class="ico"><?php echo fenix_icon( $fx_item['cells'][0], 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span>
							<b><?php echo esc_html( $fx_item['cells'][1] ); ?></b>
							<small><?php echo esc_html( $fx_item['cells'][2] ); ?></small>
						</span>
					</div>
				<?php endforeach; ?>

				<?php if ( fenix_mod( 'dash_note' ) ) : ?>
					<p class="illus-note"><?php echo esc_html( fenix_mod( 'dash_note' ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 09 · แบบไหนที่ FENIX PRO ช่วยได้จริง ============ */ ?>
<?php if ( fenix_mod( 'show_fit' ) ) : ?>
<section class="sec" id="fit">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'fit_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'fit_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'fit_subtitle' ) ); ?></p>
		</div>

		<div class="fit-g up">
			<div class="pers-g">
				<?php
				$fx_avs = array( 'brief', 'heart', 'pie' );
				for ( $fx_i = 1; $fx_i <= 3; $fx_i++ ) :
					$fx_t = trim( (string) fenix_mod( 'prof' . $fx_i . '_title' ) );
					if ( '' === $fx_t ) {
						continue;
					}
					?>
					<div class="pers">
						<span class="stamp"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( fenix_mod( 'fit_stamp' ) ); ?></span>
						<span class="av"><?php echo fenix_icon( $fx_avs[ $fx_i - 1 ], 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<div>
							<h4><?php echo esc_html( $fx_t ); ?></h4>
							<p><?php echo esc_html( fenix_mod( 'prof' . $fx_i . '_desc' ) ); ?></p>
						</div>
					</div>
				<?php endfor; ?>

				<?php if ( '' !== $fx_line && fenix_mod( 'fit_ask_text' ) ) : ?>
					<div class="pers-foot">
						<span class="ico"><?php echo fenix_icon( 'chat', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span><?php echo esc_html( fenix_mod( 'fit_ask_text' ) ); ?></span>
						<a class="fx-btn fx-btn-dark" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
							<?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'fit_ask_btn' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php $fx_bad = fenix_lines( fenix_mod( 'fit_bad_items' ) ); ?>
			<?php if ( $fx_bad ) : ?>
				<div class="stop">
					<div class="stop-h">
						<span class="oct">STOP</span>
						<h3><?php echo esc_html( fenix_mod( 'fit_bad_title' ) ); ?></h3>
					</div>
					<ul>
						<?php foreach ( $fx_bad as $fx_item ) : ?>
							<li><span class="dot no"><?php echo fenix_icon( 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $fx_item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 10 · ผลการทดสอบ และวิธีตรวจสอบเราเอง ============ */ ?>
<?php if ( fenix_mod( 'show_perf' ) ) : ?>
<section class="sec alt" id="evidence">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'perf_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'perf_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'perf_subtitle' ) ); ?></p>
		</div>

		<div class="ev-g up">
			<div class="fx-card ev-main">
				<div class="tabs" role="tablist" data-tabs>
					<button type="button" role="tab" data-tab="bt" aria-selected="true">
						<?php echo fenix_icon( 'chart', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'ev_tab1' ) ); ?>
					</button>
					<button type="button" role="tab" data-tab="fw" aria-selected="false">
						<?php echo fenix_icon( 'activity', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'ev_tab2' ) ); ?>
					</button>
				</div>

				<div data-panel="bt">
					<?php $fx_chips = fenix_lines( fenix_mod( 'ev_chips' ) ); ?>
					<?php if ( $fx_chips ) : ?>
						<div class="chips">
							<?php foreach ( $fx_chips as $fx_i => $fx_chip ) : ?>
								<span class="fx-badge <?php echo 0 === $fx_i ? 'or' : 'dark'; ?>"><?php echo esc_html( $fx_chip ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php
					$fx_big   = trim( (string) fenix_mod( 'ev_big' ) );
					$fx_chars = preg_split( '//u', $fx_big, -1, PREG_SPLIT_NO_EMPTY );
					?>
					<?php if ( $fx_chars ) : ?>
						<div class="odo num" data-odo aria-label="<?php echo esc_attr( $fx_big ); ?>">
							<?php foreach ( $fx_chars as $fx_ch ) : ?>
								<?php if ( preg_match( '/^[0-9]$/', $fx_ch ) ) : ?>
									<span class="d">
										<span class="roll" data-digit="<?php echo esc_attr( $fx_ch ); ?>">
											<?php for ( $fx_d = 0; $fx_d <= 9; $fx_d++ ) : ?>
												<i><?php echo (int) $fx_d; ?></i>
											<?php endfor; ?>
										</span>
									</span>
								<?php else : ?>
									<span class="s"><?php echo esc_html( $fx_ch ); ?></span>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php
					$fx_sub  = (string) fenix_mod( 'ev_sub' );
					$fx_bold = trim( (string) fenix_mod( 'ev_sub_bold' ) );
					$fx_sub  = str_replace( '{b}', '<strong class="num">' . esc_html( $fx_bold ) . '</strong>', esc_html( $fx_sub ) );
					?>
					<p class="ev-sub"><?php echo $fx_sub; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>

					<?php $fx_kpis = fenix_rows( fenix_mod( 'ev_kpis' ), 3 ); ?>
					<?php if ( $fx_kpis ) : ?>
						<div class="kpis">
							<?php foreach ( $fx_kpis as $fx_kpi ) : ?>
								<div class="kpi">
									<span><?php echo esc_html( $fx_kpi['cells'][0] ); ?></span>
									<b class="num"><?php echo esc_html( $fx_kpi['cells'][1] ); ?></b>
									<small><?php echo esc_html( $fx_kpi['cells'][2] ); ?></small>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php $fx_risk = fenix_rows( fenix_mod( 'ev_risk_kpis' ), 3 ); ?>
					<?php if ( $fx_risk ) : ?>
						<div class="kpis two">
							<?php foreach ( $fx_risk as $fx_kpi ) : ?>
								<div class="kpi risk">
									<span><?php echo esc_html( $fx_kpi['cells'][0] ); ?></span>
									<b class="num"><?php echo esc_html( $fx_kpi['cells'][1] ); ?></b>
									<small><?php echo esc_html( $fx_kpi['cells'][2] ); ?></small>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php
					$fx_bars = fenix_rows( fenix_mod( 'ev_bars' ), 3 );
					$fx_top  = 0;
					foreach ( $fx_bars as $fx_bar ) {
						$fx_top = max( $fx_top, (float) $fx_bar['cells'][1] );
					}
					$fx_top = $fx_top > 0 ? $fx_top : 1;
					?>
					<?php if ( $fx_bars ) : ?>
						<div class="chart">
							<div class="chart-h">
								<b><?php echo esc_html( fenix_mod( 'ev_bars_title' ) ); ?> <em><?php echo esc_html( fenix_mod( 'ev_bars_unit' ) ); ?></em></b>
								<small><i></i><?php echo esc_html( fenix_mod( 'ev_bars_note' ) ); ?></small>
							</div>
							<div class="bars" data-bars style="grid-template-columns:repeat(<?php echo count( $fx_bars ); ?>,1fr)">
								<?php foreach ( $fx_bars as $fx_bar ) : ?>
									<div class="bar<?php echo 'sim' === $fx_bar['cells'][2] ? ' sim' : ''; ?>">
										<em class="num">+<?php echo esc_html( $fx_bar['cells'][1] ); ?>%</em>
										<span data-h="<?php echo esc_attr( round( (float) $fx_bar['cells'][1] / $fx_top * 80, 2 ) ); ?>%" style="height:0"></span>
									</div>
								<?php endforeach; ?>
							</div>
							<div class="mlab" style="grid-template-columns:repeat(<?php echo count( $fx_bars ); ?>,1fr)">
								<?php foreach ( $fx_bars as $fx_bar ) : ?>
									<span><?php echo esc_html( $fx_bar['cells'][0] ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( fenix_mod( 'perf_conditions' ) ) : ?>
						<div class="condbox"><b>เงื่อนไขการทดสอบ:</b> <?php echo esc_html( fenix_mod( 'perf_conditions' ) ); ?></div>
					<?php endif; ?>
				</div>

				<div data-panel="fw" hidden>
					<div class="ev-fw">
						<p><?php echo esc_html( fenix_mod( 'ev_fw_text' ) ); ?></p>
						<?php if ( fenix_mod( 'forward_link_url' ) ) : ?>
							<a class="fx-btn fx-btn-or" href="<?php echo esc_url( fenix_mod( 'forward_link_url' ) ); ?>" target="_blank" rel="noopener nofollow">
								<?php echo fenix_icon( 'activity', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'forward_link_label' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<div class="side-g">
				<div class="fx-card verify">
					<?php if ( fenix_mod( 'ev_stamp' ) ) : ?>
						<div class="vstamp" aria-hidden="true">
							<div>
								<?php echo fenix_icon( 'search', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'ev_stamp' ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<h3><?php echo esc_html( fenix_mod( 'verify_title' ) ); ?></h3>

					<div class="fx-steps">
						<?php foreach ( fenix_lines( fenix_mod( 'verify_items' ) ) as $fx_i => $fx_item ) : ?>
							<div class="fx-step"><b><?php echo (int) ( $fx_i + 1 ); ?></b><span><?php echo esc_html( $fx_item ); ?></span></div>
						<?php endforeach; ?>
					</div>

					<?php if ( fenix_mod( 'verified_link_url' ) ) : ?>
						<a class="fx-btn fx-btn-or fx-btn-full" href="<?php echo esc_url( fenix_mod( 'verified_link_url' ) ); ?>" target="_blank" rel="noopener nofollow">
							<?php echo fenix_icon( 'activity', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'verified_link_label' ) ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( fenix_mod( 'perf_disclaimer' ) ) : ?>
					<div class="note">
						<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( fenix_mod( 'perf_disclaimer' ) ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>





<?php /* ============ 12 · ใช้กับอะไรได้บ้าง ============ */ ?>
<?php if ( fenix_mod( 'show_devices' ) ) : ?>
<section class="sec alt" id="requirements">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'dev_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'dev_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'dev_sub' ) ); ?></p>
		</div>

		<div class="req-g up">
			<?php
			for ( $fx_i = 1; $fx_i <= 3; $fx_i++ ) :
				$fx_t = trim( (string) fenix_mod( 'dev' . $fx_i . '_title' ) );
				if ( '' === $fx_t ) {
					continue;
				}
				$fx_bd = trim( (string) fenix_mod( 'dev' . $fx_i . '_badge' ) );
				?>
				<div class="req<?php echo '' !== $fx_bd ? ' hl' : ''; ?>">
					<div class="dev" aria-hidden="true">
						<?php if ( '' !== $fx_bd ) : ?>
							<span class="fx-badge or"><?php echo esc_html( $fx_bd ); ?></span>
						<?php endif; ?>

						<?php if ( 1 === $fx_i ) : ?>
							<div class="laptop">
								<div class="sc">
									<div class="mini-c">
										<?php
										$fx_mc = array( 18, 26, 20, 32, 28, 38, 30, 42, 36 );
										foreach ( $fx_mc as $fx_n => $fx_hgt ) :
											?>
											<i style="height:<?php echo (int) $fx_hgt; ?>px;background:<?php echo 2 === $fx_n % 3 ? '#EF4444' : '#22C55E'; ?>"></i>
										<?php endforeach; ?>
									</div>
								</div>
								<div class="bs"></div>
							</div>
						<?php elseif ( 2 === $fx_i ) : ?>
							<div class="rack">
								<div><i></i><i></i><span></span></div>
								<div><i></i><i></i><span></span></div>
								<div><i></i><i></i><span></span></div>
							</div>
							<span class="rack-lb">ONLINE 24/7</span>
						<?php else : ?>
							<div class="mobile-d">
								<div class="s"><span>MT5</span><em>ดูพอร์ต · ปิดไม้</em></div>
								<span class="x"><?php echo fenix_icon( 'x', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							</div>
						<?php endif; ?>
					</div>

					<h4><?php echo esc_html( $fx_t ); ?></h4>
					<p><?php echo esc_html( fenix_mod( 'dev' . $fx_i . '_note' ) ); ?></p>

					<?php $fx_items = fenix_rows( fenix_mod( 'dev' . $fx_i . '_items' ), 1 ); ?>
					<?php if ( $fx_items ) : ?>
						<ul>
							<?php
							foreach ( $fx_items as $fx_item ) :
								if ( '-' === $fx_item['flag'] ) {
									$fx_cls  = 'no';
									$fx_icon = 'x';
								} elseif ( '!' === $fx_item['flag'] ) {
									$fx_cls  = 'warn';
									$fx_icon = 'warn';
								} else {
									$fx_cls  = 'ok';
									$fx_icon = 'check';
								}
								?>
								<li>
									<span class="dot <?php echo esc_attr( $fx_cls ); ?>"><?php echo fenix_icon( $fx_icon, 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<?php echo esc_html( $fx_item['cells'][0] ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>

		<div class="req-foot up">
			<div class="chips">
				<?php foreach ( array_filter( array_map( 'trim', explode( '·', (string) fenix_mod( 'dev_req' ) ) ) ) as $fx_req ) : ?>
					<span><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fx_req ); ?></span>
				<?php endforeach; ?>
			</div>
			<div class="acts">
				<?php if ( fenix_mod( 'dev_btn_label' ) ) : ?>
					<a class="fx-btn fx-btn-or" href="<?php echo esc_url( fenix_link_url( fenix_mod( 'dev_btn_url' ) ) ); ?>">
						<?php echo fenix_icon( 'download', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo esc_html( fenix_mod( 'dev_btn_label' ) ); ?>
					</a>
				<?php endif; ?>
				<?php if ( fenix_mod( 'dev_btn2_label' ) ) : ?>
					<a class="fx-btn fx-btn-dark" href="<?php echo esc_url( fenix_link_url( fenix_mod( 'dev_btn2_url' ) ) ); ?>">
						<?php echo esc_html( fenix_mod( 'dev_btn2_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 13 · ทีมที่อยู่เบื้องหลัง ============ */ ?>
<?php if ( fenix_mod( 'show_team' ) ) : ?>
<section class="sec" id="team">
	<div class="wrap">
		<div class="team-g up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'team_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'team_title' ) ); ?></h2>
				<p class="fx-lead" style="margin-top:24px"><?php echo esc_html( fenix_mod( 'team_text' ) ); ?></p>

				<?php $fx_points = fenix_lines( fenix_mod( 'team_points' ) ); ?>
				<?php if ( $fx_points ) : ?>
					<div class="ticks">
						<?php foreach ( $fx_points as $fx_point ) : ?>
							<span><span class="dot ok"><?php echo fenix_icon( 'check', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><?php echo esc_html( $fx_point ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="fx-card consult">
				<div>
					<h3><?php echo esc_html( fenix_mod( 'mid_cta_title' ) ); ?></h3>
					<p><?php echo esc_html( fenix_mod( 'mid_cta_text' ) ); ?></p>

					<?php if ( '' !== $fx_line ) : ?>
						<a class="fx-btn fx-btn-or fx-btn-full" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
							<?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'team_btn1' ) ); ?>
						</a>
					<?php endif; ?>

					<button type="button" class="fx-btn fx-btn-dark fx-btn-full" data-copy data-done="<?php echo esc_attr( fenix_mod( 'team_copy_done' ) ); ?>">
						<?php echo fenix_icon( 'copy', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span><?php echo esc_html( fenix_mod( 'team_btn2' ) ); ?></span>
					</button>
				</div>

				<div>
					<div class="chat">
						<div class="scr">
							<div class="hd">
								<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="30" height="30" loading="lazy" decoding="async">
								<div><?php echo esc_html( fenix_mod( 'team_chat_head' ) ); ?><small><?php echo esc_html( fenix_mod( 'team_chat_sub' ) ); ?></small></div>
							</div>
							<div class="bd">
								<div class="bub me" data-copy-src>
									<b><?php echo esc_html( fenix_mod( 'team_sample_title' ) ); ?></b>
									<?php
									$fx_sample = fenix_lines( fenix_mod( 'team_sample_lines' ) );
									$fx_last   = count( $fx_sample ) - 1;
									foreach ( $fx_sample as $fx_i => $fx_sline ) :
										echo esc_html( $fx_sline );
										if ( $fx_i < $fx_last ) {
											echo '<br>';
										}
									endforeach;
									?>
								</div>
								<div class="tm-row">
									<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="24" height="24" loading="lazy" decoding="async">
									<div class="bub tm typing" aria-hidden="true"><i></i><i></i><i></i></div>
								</div>
							</div>
						</div>
					</div>
					<p class="copy-cap" data-copy-cap data-was="<?php echo esc_attr( fenix_mod( 'team_copy_cap' ) ); ?>"><?php echo esc_html( fenix_mod( 'team_copy_cap' ) ); ?></p>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 14 · แพ็กเกจการใช้งาน ============ */ ?>
<?php if ( fenix_mod( 'show_pricing_home' ) ) : ?>
<section class="sec alt" id="pricing">
	<div class="wrap">
		<div class="shead center up">
			<span class="eyebrow"><?php echo esc_html( fenix_mod( 'pricing_kicker' ) ); ?></span>
			<h2 class="h2"><?php echo esc_html( fenix_mod( 'pricing_home_title' ) ); ?></h2>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'pricing_home_sub' ) ); ?></p>
		</div>

		<div class="price-g up">
			<?php
			$fx_contact = 'contact' === fenix_mod( 'pricing_mode' );
			for ( $fx_i = 1; $fx_i <= 3; $fx_i++ ) :
				$fx_name = trim( (string) fenix_mod( 'pkg' . $fx_i . '_name' ) );
				if ( '' === $fx_name ) {
					continue;
				}
				$fx_best = (bool) fenix_mod( 'pkg' . $fx_i . '_featured' );
				?>
				<div class="tk<?php echo $fx_best ? ' hl' : ''; ?>">
					<div class="tkt">
						<div class="nm">
							<h4><?php echo esc_html( $fx_name ); ?></h4>
							<?php if ( $fx_best ) : ?>
								<span class="fx-badge or"><?php echo esc_html( fenix_mod( 'pkg_featured_label' ) ); ?></span>
							<?php endif; ?>
						</div>
						<div class="pp">
							<?php if ( $fx_contact ) : ?>
								<b>สอบถาม</b>
							<?php else : ?>
								<b class="num"><?php echo esc_html( fenix_mod( 'pkg' . $fx_i . '_price' ) ); ?></b>
								<span><?php echo esc_html( fenix_mod( 'pkg' . $fx_i . '_period' ) ); ?></span>
							<?php endif; ?>
						</div>
					</div>
					<div class="perf" aria-hidden="true"></div>
					<div class="bt2">
						<p><?php echo esc_html( fenix_mod( 'pkg' . $fx_i . '_tag' ) ); ?></p>
						<?php if ( '' !== $fx_line ) : ?>
							<a class="fx-btn <?php echo $fx_best ? 'fx-btn-or' : 'fx-btn-dark'; ?> fx-btn-full" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
								<?php echo fenix_icon( 'line', 'i fill' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'pricing_btn_text' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<div class="price-more up">
			<a class="fx-link" href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">
				<?php echo esc_html( fenix_mod( 'pricing_more' ) ); ?>
				<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>

		<?php if ( fenix_mod( 'pricing_note' ) ) : ?>
			<p class="price-note up"><?php echo esc_html( fenix_mod( 'pricing_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 15 · ดูข้อมูลเชิงลึกต่อ ============ */ ?>
<section class="sec" id="explore">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'explore_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'home_cards_title' ) ); ?></h2>
			</div>
			<p class="fx-lead"><?php echo esc_html( fenix_mod( 'home_cards_sub' ) ); ?></p>
		</div>

		<div class="idx up">
			<?php
			$fx_n = 0;
			for ( $fx_i = 1; $fx_i <= 5; $fx_i++ ) :
				$fx_t = trim( (string) fenix_mod( 'card' . $fx_i . '_title' ) );
				$fx_u = trim( (string) fenix_mod( 'card' . $fx_i . '_url' ) );
				if ( '' === $fx_t || '' === $fx_u ) {
					continue;
				}
				$fx_n++;
				$fx_is_risk = false !== strpos( $fx_u, 'risk' );
				?>
				<a class="<?php echo $fx_is_risk ? 'risk' : ''; ?>" href="<?php echo esc_url( fenix_link_url( $fx_u ) ); ?>">
					<span class="no num"><?php echo esc_html( sprintf( '%02d', $fx_n ) ); ?></span>
					<div>
						<h4><?php echo $fx_is_risk ? fenix_icon( 'warn', 'i' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $fx_t ); ?></h4>
						<p><?php echo esc_html( fenix_mod( 'card' . $fx_i . '_desc' ) ); ?></p>
					</div>
					<span class="go"><?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>


<?php /* ============ 16 · คำถามที่พบบ่อย ============ */ ?>
<?php
$fx_faq = array();
for ( $fx_i = 1; $fx_i <= 10; $fx_i++ ) {
	$fx_q = trim( (string) fenix_mod( 'faq' . $fx_i . '_q' ) );
	$fx_a = trim( (string) fenix_mod( 'faq' . $fx_i . '_a' ) );
	if ( '' !== $fx_q && '' !== $fx_a ) {
		$fx_faq[] = array( $fx_q, $fx_a );
	}
}
?>
<?php if ( fenix_mod( 'show_faq' ) && $fx_faq ) : ?>
<section class="sec alt" id="faq">
	<div class="wrap">
		<div class="faq-g">
			<div class="faq-side up">
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'faq_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'faq_title' ) ); ?></h2>
				<p class="fx-lead" style="margin-top:16px"><?php echo esc_html( fenix_mod( 'faq_subtitle' ) ); ?></p>

				<label class="search">
					<?php echo fenix_icon( 'search', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="search" data-faq-search placeholder="<?php echo esc_attr( fenix_mod( 'faq_search_ph' ) ); ?>" aria-label="<?php echo esc_attr( fenix_mod( 'faq_search_ph' ) ); ?>">
				</label>

				<?php if ( '' !== $fx_line ) : ?>
					<a class="fx-card" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
						<span class="ico"><?php echo fenix_icon( 'chat', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<p><b><?php echo esc_html( fenix_mod( 'faq_ask_title' ) ); ?></b><?php echo esc_html( fenix_mod( 'faq_ask_text' ) ); ?></p>
					</a>
				<?php endif; ?>
			</div>

			<div class="up" data-faq>
				<?php foreach ( $fx_faq as $fx_i => $fx_item ) : ?>
					<div class="qa<?php echo 0 === $fx_i ? ' open' : ''; ?>">
						<button type="button" class="q" aria-expanded="<?php echo 0 === $fx_i ? 'true' : 'false'; ?>">
							<span class="qt"><?php echo esc_html( $fx_item[0] ); ?></span>
							<span class="pm">
								<?php echo fenix_icon( 'plus', 'i plus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo fenix_icon( 'minus', 'i minus' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</span>
						</button>
						<div class="a">
							<div class="qb"><span><?php echo esc_html( $fx_item[0] ); ?></span></div>
							<div class="ab">
								<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="36" height="36" loading="lazy" decoding="async">
								<div><?php echo esc_html( $fx_item[1] ); ?></div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>

				<p class="faq-empty" data-faq-empty hidden><?php echo esc_html( fenix_mod( 'faq_empty' ) ); ?></p>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 17 · บทความและความรู้ ============ */ ?>
<?php
$fx_posts = null;
if ( fenix_mod( 'show_blog' ) ) {
	$fx_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}
?>
<?php if ( $fx_posts && $fx_posts->have_posts() ) : ?>
<section class="sec" id="articles">
	<div class="wrap">
		<div class="shead up">
			<div>
				<span class="eyebrow"><?php echo esc_html( fenix_mod( 'blog_kicker' ) ); ?></span>
				<h2 class="h2"><?php echo esc_html( fenix_mod( 'blog_title' ) ); ?></h2>
				<p class="fx-lead" style="margin-top:12px"><?php echo esc_html( fenix_mod( 'blog_subtitle' ) ); ?></p>
			</div>
			<?php
			$fx_blog_page = (int) get_option( 'page_for_posts' );
			$fx_blog_url  = $fx_blog_page ? get_permalink( $fx_blog_page ) : home_url( '/articles/' );
			?>
			<a class="fx-btn fx-btn-dark" href="<?php echo esc_url( $fx_blog_url ); ?>">
				<?php echo esc_html( fenix_mod( 'blog_all_label' ) ); ?>
				<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</div>

		<div class="art-g up">
			<?php
			while ( $fx_posts->have_posts() ) :
				$fx_posts->the_post();
				$fx_cats = get_the_category();
				?>
				<article class="art">
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>">
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( null, 'medium_large' ) ); ?>" alt="<?php echo esc_attr( fenix_featured_image_alt() ); ?>" width="768" height="403" loading="lazy" decoding="async">
						</a>
					<?php endif; ?>
					<div class="b">
						<div class="meta">
							<?php if ( $fx_cats ) : ?>
								<span class="fx-badge dark"><?php echo esc_html( $fx_cats[0]->name ); ?></span>
							<?php endif; ?>
							<?php echo esc_html( get_the_date( 'j F Y' ) ); ?>
						</div>
						<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26, '…' ) ); ?></p>
						<a class="fx-link" href="<?php the_permalink(); ?>">
							<?php echo esc_html( fenix_mod( 'blog_read_more' ) ); ?>
							<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</a>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>


<?php /* ============ 18 · CTA ปิดท้าย + คำเตือนความเสี่ยง ============ */ ?>
<?php if ( fenix_mod( 'show_cta' ) ) : ?>
<section class="final-sec" id="cta">
	<div class="wrap">
		<div class="final up">
			<?php if ( fenix_mod( 'cta_watermark' ) ) : ?>
				<div class="wm" aria-hidden="true"><?php echo esc_html( fenix_mod( 'cta_watermark' ) ); ?></div>
			<?php endif; ?>

			<div class="in">
				<img src="<?php echo esc_url( $fx_logo ); ?>" alt="" width="132" height="132" loading="lazy" decoding="async">
				<div>
					<h2><?php echo esc_html( fenix_mod( 'cta_title' ) ); ?></h2>
					<p><?php echo esc_html( fenix_mod( 'cta_subtitle' ) ); ?></p>
					<div class="acts">
						<?php if ( '' !== $fx_line ) : ?>
							<a class="fx-btn fx-btn-black" href="<?php echo esc_url( $fx_line ); ?>" target="_blank" rel="noopener">
								<?php echo fenix_icon( 'line', 'i fill lg' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo esc_html( fenix_mod( 'cta_btn_text' ) ); ?>
							</a>
						<?php endif; ?>
						<a class="fx-btn fx-btn-ghost" href="<?php echo esc_url( $fx_go ); ?>">
							<?php echo fenix_icon( 'download', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo esc_html( fenix_mod( 'cta_btn2_text' ) ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php /* คำเตือนความเสี่ยง · ต้องมีในหน้าแรกเสมอ (ขอบเขตงานข้อ 3) */ ?>
<?php if ( fenix_mod( 'risk_text' ) ) : ?>
<section class="risk-sec" id="risk">
	<div class="wrap">
		<div class="riskbox up">
			<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div>
				<h4><?php echo esc_html( fenix_mod( 'risk_title' ) ); ?></h4>
				<p><?php echo esc_html( fenix_mod( 'risk_text' ) ); ?></p>
				<a class="fx-link" href="<?php echo esc_url( home_url( '/risk-disclosure/' ) ); ?>">
					<?php echo esc_html( fenix_mod( 'risk_link_label' ) ); ?>
					<?php echo fenix_icon( 'arrow', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

</main>

<?php
get_footer();
