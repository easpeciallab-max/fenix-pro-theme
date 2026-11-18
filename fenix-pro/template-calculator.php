<?php
/**
 * Template Name: FENIX · เครื่องคำนวณขนาดล็อต
 *
 * หน้าเครื่องมือ: คำนวณขนาดล็อตจากทุน ความเสี่ยงต่อไม้ และระยะ Stop Loss
 * ตัวเลขทุกค่าคำนวณในเบราว์เซอร์ ไม่มีการส่งข้อมูลออกจากเครื่องผู้ใช้
 * เนื้อหาอธิบายยังอยู่ในตัวแก้ไขของ WordPress ทั้งหมด เทมเพลตนี้เพิ่มเฉพาะตัวเครื่องมือ
 *
 * @package fenix-pro
 */

get_header();

if ( have_posts() ) {
	the_post();
}

$calc_title = get_the_title();
$calc_sub   = trim( (string) fenix_mod( 'calc_sub' ) );
$calc_line  = trim( (string) fenix_mod( 'line_url' ) );
?>

<main id="main" class="fxp fxcalc">

	<?php fenix_phero( $calc_title ? $calc_title : 'เครื่องคำนวณขนาดล็อต', $calc_sub, fenix_mod( 'calc_kicker' ) ); ?>

	<section class="sec">
		<div class="wrap">

			<div class="calc-grid">

				<form class="calc-form" id="fx-calc" autocomplete="off" novalidate>

					<div class="cf-head">
						<h2 class="h2"><?php echo esc_html( fenix_mod( 'calc_form_title' ) ); ?></h2>
						<p class="fx-lead"><?php echo esc_html( fenix_mod( 'calc_form_text' ) ); ?></p>
					</div>

					<div class="cf-row">
						<label for="cf-symbol">สินทรัพย์ที่จะเทรด</label>
						<select id="cf-symbol" name="symbol">
							<option value="100" data-price="2000" data-sl="5" data-unit="ทองคำใส่ระยะเป็นดอลลาร์ต่อออนซ์ เช่น 5 คือราคาเดินสวน 5.00" selected>ทองคำ XAUUSD (1 ล็อต = 100 ออนซ์)</option>
							<option value="100000" data-price="1.08" data-sl="0.0050" data-unit="คู่เงินใส่ระยะเป็นราคา เช่น 50 จุด (pip) ของคู่ทศนิยม 4 ตำแหน่ง คือ 0.0050">คู่เงินหลักที่จบด้วย USD (1 ล็อต = 100,000)</option>
							<option value="1" data-price="60000" data-sl="500" data-unit="บิตคอยน์ใส่ระยะเป็นดอลลาร์ เช่น 500 คือราคาเดินสวน 500.00">บิตคอยน์ BTCUSD (1 ล็อต = 1 เหรียญ)</option>
							<option value="custom" data-price="100" data-sl="1" data-unit="ใส่ระยะเป็นหน่วยราคาเดียวกับที่ใช้ในขนาดสัญญา">กำหนดขนาดสัญญาเอง</option>
						</select>
						<p class="cf-hint">ค่าเริ่มต้นเป็นค่าที่โบรกเกอร์ส่วนใหญ่ตั้งไว้ ให้เปิดหน้า Specification ของบัญชีตัวเองแล้วแก้ให้ตรงเสมอ</p>
					</div>

					<div class="cf-row">
						<label for="cf-contract">ขนาดสัญญาต่อ 1 ล็อต (Contract size)</label>
						<input type="number" id="cf-contract" name="contract" value="100" min="0.0001" step="any" inputmode="decimal">
					</div>

					<div class="cf-2">
						<div class="cf-row">
							<label for="cf-balance">เงินในบัญชี (สกุลบัญชี)</label>
							<input type="number" id="cf-balance" name="balance" value="1000" min="0" step="any" inputmode="decimal">
						</div>

						<div class="cf-row">
							<label for="cf-risk">ความเสี่ยงต่อไม้ (%)</label>
							<input type="number" id="cf-risk" name="risk" value="1" min="0" max="100" step="any" inputmode="decimal">
						</div>
					</div>

					<div class="cf-2">
						<div class="cf-row">
							<label for="cf-sl">ระยะ Stop Loss (หน่วยราคา)</label>
							<input type="number" id="cf-sl" name="sl" value="5" min="0" step="any" inputmode="decimal">
							<p class="cf-hint" id="cf-sl-hint">ทองคำใส่ระยะเป็นดอลลาร์ต่อออนซ์ เช่น 5 คือราคาเดินสวน 5.00</p>
						</div>

						<div class="cf-row">
							<label for="cf-price">ราคาปัจจุบันของสินทรัพย์</label>
							<input type="number" id="cf-price" name="price" value="2000" min="0" step="any" inputmode="decimal">
						</div>
					</div>

					<div class="cf-2">
						<div class="cf-row">
							<label for="cf-leverage">เลเวอเรจของบัญชี 1 :</label>
							<input type="number" id="cf-leverage" name="leverage" value="500" min="1" step="1" inputmode="numeric">
						</div>

						<div class="cf-row">
							<label for="cf-minlot">ล็อตขั้นต่ำที่โบรกให้เปิด</label>
							<input type="number" id="cf-minlot" name="minlot" value="0.01" min="0.0001" step="any" inputmode="decimal">
						</div>
					</div>

					<button type="reset" class="fx-btn fx-btn-out cf-reset">ล้างค่าที่กรอก</button>
				</form>

				<aside class="calc-out" id="fx-calc-out" aria-live="polite">

					<div class="co-main">
						<span class="co-label">ขนาดล็อตที่ความเสี่ยงนี้รับได้</span>
						<strong class="co-value" data-out="lots">—</strong>
						<span class="co-note" data-out="lotsnote">ปัดลงตามขั้นล็อตของโบรกแล้ว</span>
					</div>

					<ul class="co-list">
						<li>
							<span>เงินที่เสี่ยงต่อไม้</span>
							<strong data-out="risk">—</strong>
						</li>
						<li>
							<span>ขาดทุนจริงถ้าโดน Stop Loss</span>
							<strong data-out="loss">—</strong>
						</li>
						<li>
							<span>มูลค่าต่อการเคลื่อนไหว 1 หน่วยราคา</span>
							<strong data-out="perpoint">—</strong>
						</li>
						<li>
							<span>มาร์จิ้นที่ถูกล็อกไว้</span>
							<strong data-out="margin">—</strong>
						</li>
						<li>
							<span>มาร์จิ้นคิดเป็นสัดส่วนของทุน</span>
							<strong data-out="marginpct">—</strong>
						</li>
					</ul>

					<p class="co-warn" data-out="warn" hidden></p>

					<p class="co-foot">คำนวณในเครื่องของคุณเอง ไม่มีการส่งตัวเลขออกไปที่ไหน</p>
				</aside>

			</div>

			<div class="note calc-note">
				<?php echo fenix_icon( 'warn', 'i' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p>ผลลัพธ์เป็นการคำนวณจากค่าที่กรอกเท่านั้น ค่าจริงของแต่ละบัญชีต่างกันที่ขนาดสัญญา ขั้นล็อต สกุลเงินบัญชี วิธีคิดมาร์จิ้น และค่าธรรมเนียม ให้ตรวจกับสเปกบัญชีของตัวเองก่อนใช้ตัวเลขนี้ตัดสินใจ การเทรดมีความเสี่ยงที่จะสูญเงินลงทุน</p>
			</div>

		</div>
	</section>

	<?php if ( trim( (string) get_the_content() ) ) : ?>
		<section class="sec alt calc-copy">
			<div class="wrap">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php fenix_pages_cta( fenix_mod( 'calc_cta_title' ), fenix_mod( 'calc_cta_text' ), fenix_mod( 'pages_cta_btn' ), $calc_line ); ?>

</main>

<?php
get_footer();
