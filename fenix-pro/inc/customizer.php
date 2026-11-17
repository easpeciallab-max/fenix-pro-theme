<?php
/**
 * FENIX PRO EA · Customizer (หน้า "ปรับแต่ง")
 * ทุกข้อความ รูปภาพ ลิงก์ และการเปิดและปิด section แก้ได้จากที่นี่
 *
 * @package fenix-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------- Sanitize helpers ---------------- */

function fenix_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

function fenix_sanitize_pricing_mode( $value ) {
	return in_array( $value, array( 'price', 'contact' ), true ) ? $value : 'contact';
}

/* ---------------- Register ---------------- */

function fenix_customize_register( $wp_customize ) {

	$d = fenix_defaults();

	$wp_customize->add_panel(
		'fenix_panel',
		array(
			'title'       => 'FENIX PRO · ตั้งค่าหน้าเว็บ',
			'description' => 'แก้ไขข้อความ รูปภาพ ลิงก์ และเปิดและปิดแต่ละส่วนของหน้าแรกได้ทั้งหมดจากเมนูนี้ กด "เผยแพร่" เพื่อบันทึก',
			'priority'    => 10,
		)
	);

	/*
	 * โครงสร้าง: section_id => [ title, description, fields ]
	 * field: id => [ label, type, (choices/description) ]
	 */
	$sections = array(

		'fenix_general' => array(
			'title'       => '1) ช่องทางติดต่อ (สำคัญ ตั้งค่าก่อน)',
			'description' => 'ลิงก์ LINE จะถูกใช้กับปุ่มทุกปุ่มบนเว็บโดยอัตโนมัติ',
			'fields'      => array(
				'line_url'        => array( 'ลิงก์ LINE OA', 'url', 'เช่น https://lin.ee/xxxxx หรือ https://line.me/R/ti/p/@xxxxx' ),
				'facebook_url'    => array( 'ลิงก์ Facebook Page (ถ้ามี)', 'url' ),
				'instagram_url'   => array( 'ลิงก์ Instagram (ถ้ามี)', 'url' ),
				'tiktok_url'      => array( 'ลิงก์ TikTok (ถ้ามี)', 'url' ),
				'youtube_url'     => array( 'ลิงก์ YouTube (ถ้ามี)', 'url' ),
				'contact_email'   => array( 'อีเมลติดต่อ (ถ้ามี)', 'text' ),
				'show_float_line' => array( 'แสดงปุ่ม LINE ลอยมุมขวาล่าง', 'checkbox' ),
				'float_line_text' => array( 'ข้อความบนปุ่ม LINE ลอย', 'text' ),
				'show_language_switcher' => array( 'แสดงตัวสลับภาษาในเมนูบน', 'checkbox' ),
				'language_fallback_items' => array( 'รายการภาษาสำรอง (code|ธง|ป้ายสั้น|ชื่อภาษา)', 'textarea' ),
			),
		),

		'fenix_hero' => array(
			'title'  => '2) Hero · ส่วนแรกของหน้า',
			'fields' => array(
				'show_hero'      => array( 'แสดงส่วนนี้', 'checkbox' ),
				'hero_title'     => array( 'ชื่อหลัก (Headline)', 'text' ),
				'hero_subtitle'  => array( 'ประโยคหลัก (Sub headline)', 'text' ),
				'hero_desc'      => array( 'คำอธิบายสั้น', 'textarea' ),
				'hero_btn1_text' => array( 'ข้อความปุ่มหลัก (ลิงก์ไป LINE)', 'text' ),
				'hero_btn2_text' => array( 'ข้อความปุ่มรอง (เลื่อนไปส่วนรู้จักระบบ)', 'text' ),
				'hero_deck_featured' => array( 'ปก EA ที่อยู่หน้าสุดของสำรับ', 'radio', array( 'plus' => 'การ์ดใบ FENIX PLUS', 'fast' => 'การ์ดใบแรกของหน้าดาวน์โหลด', 'card3' => 'การ์ดใบที่สามของหน้าดาวน์โหลด' ) ),
				'hero_deck_cta'      => array( 'ข้อความปุ่มบนการ์ด EA (เว้นว่าง = ไม่แสดง)', 'text' ),
				'hero_deck_cta_mode' => array( 'ปุ่มบนการ์ดพาไปไหน', 'radio', array( 'page' => 'หน้าดาวน์โหลด (มีคำอธิบายครบ)', 'file' => 'โหลดไฟล์ของใบนั้นทันที' ) ),
			),
		),

		'fenix_pain' => array(
			'title'  => '3) ปัญหาของนักเทรด',
			'fields' => array(
				'show_pain'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pain_title'    => array( 'หัวข้อ', 'text' ),
				'pain_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pain1_title'   => array( 'การ์ด 1 · หัวข้อ', 'text' ),
				'pain1_desc'    => array( 'การ์ด 1 · รายละเอียด', 'textarea' ),
				'pain2_title'   => array( 'การ์ด 2 · หัวข้อ', 'text' ),
				'pain2_desc'    => array( 'การ์ด 2 · รายละเอียด', 'textarea' ),
				'pain3_title'   => array( 'การ์ด 3 · หัวข้อ', 'text' ),
				'pain3_desc'    => array( 'การ์ด 3 · รายละเอียด', 'textarea' ),
				'pain4_title'   => array( 'การ์ด 4 · หัวข้อ', 'text' ),
				'pain4_desc'    => array( 'การ์ด 4 · รายละเอียด', 'textarea' ),
				'pain_img'      => array( 'รูปประกอบหัวข้อนี้', 'image', 'แนะนำภาพแนวนอน กว้าง 1200px โทนมืด' ),
				'pain_img_alt'  => array( 'คำอธิบายรูป (ALT · สำคัญกับ SEO)', 'text' ),
			),
		),

		'fenix_about' => array(
			'title'  => '4) FENIX PRO EA คืออะไร',
			'fields' => array(
				'show_about'  => array( 'แสดงส่วนนี้', 'checkbox' ),
				'about_title' => array( 'หัวข้อ', 'text' ),
				'about_text'  => array( 'เนื้อหา (เว้นบรรทัด = ขึ้นย่อหน้าใหม่)', 'textarea' ),
				'about_img'     => array( 'รูปประกอบหัวข้อนี้', 'image', 'แนะนำภาพแนวนอน กว้าง 1200px โทนมืด' ),
				'about_img_alt' => array( 'คำอธิบายรูป (ALT)', 'text' ),
				'about_flow1'   => array( 'ขั้นการทำงาน 1', 'text' ),
				'about_flow2'   => array( 'ขั้นการทำงาน 2', 'text' ),
				'about_flow3'   => array( 'ขั้นการทำงาน 3', 'text' ),
			),
		),

		'fenix_features' => array(
			'title'  => '5) จุดเด่นของระบบ (6 ข้อ)',
			'fields' => array(
				'show_features'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'features_title'    => array( 'หัวข้อ', 'text' ),
				'features_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'feat1_title'       => array( 'จุดเด่น 1 · หัวข้อ', 'text' ),
				'feat1_desc'        => array( 'จุดเด่น 1 · รายละเอียด', 'textarea' ),
				'feat2_title'       => array( 'จุดเด่น 2 · หัวข้อ', 'text' ),
				'feat2_desc'        => array( 'จุดเด่น 2 · รายละเอียด', 'textarea' ),
				'feat3_title'       => array( 'จุดเด่น 3 · หัวข้อ', 'text' ),
				'feat3_desc'        => array( 'จุดเด่น 3 · รายละเอียด', 'textarea' ),
				'feat4_title'       => array( 'จุดเด่น 4 · หัวข้อ', 'text' ),
				'feat4_desc'        => array( 'จุดเด่น 4 · รายละเอียด', 'textarea' ),
				'feat5_title'       => array( 'จุดเด่น 5 · หัวข้อ', 'text' ),
				'feat5_desc'        => array( 'จุดเด่น 5 · รายละเอียด', 'textarea' ),
				'feat6_title'       => array( 'จุดเด่น 6 · หัวข้อ', 'text' ),
				'feat6_desc'        => array( 'จุดเด่น 6 · รายละเอียด', 'textarea' ),
				'features_img'      => array( 'รูปประกอบหัวข้อนี้', 'image' ),
				'features_img_alt'  => array( 'คำอธิบายรูป (ALT)', 'text' ),
			),
		),

		'fenix_gallery' => array(
			'title'       => '6) ภาพ Dashboard / ระบบจริง',
			'description' => 'ถ้ายังไม่อัปโหลดภาพ ธีมจะแสดงกรอบตัวอย่าง (ระบุว่าเป็นภาพประกอบ) ให้อัตโนมัติ',
			'fields'      => array(
				'show_gallery'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'gallery_title'    => array( 'หัวข้อ', 'text' ),
				'gallery_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'gallery_img1'     => array( 'ภาพที่ 1', 'image' ),
				'gallery_cap1'     => array( 'คำบรรยายภาพที่ 1', 'text' ),
				'gallery_img2'     => array( 'ภาพที่ 2', 'image' ),
				'gallery_cap2'     => array( 'คำบรรยายภาพที่ 2', 'text' ),
				'gallery_img3'     => array( 'ภาพที่ 3', 'image' ),
				'gallery_cap3'     => array( 'คำบรรยายภาพที่ 3', 'text' ),
				'gallery_img4'     => array( 'ภาพที่ 4', 'image' ),
				'gallery_cap4'     => array( 'คำบรรยายภาพที่ 4', 'text' ),
				'gallery_note'     => array( 'หมายเหตุท้ายส่วน', 'text' ),
			),
		),

		'fenix_steps' => array(
			'title'  => '7) วิธีเริ่มใช้งาน (4 ขั้นตอน)',
			'fields' => array(
				'show_steps'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'steps_kicker'   => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
				'steps_title'    => array( 'หัวข้อ', 'text' ),
				'steps_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'step1_title'    => array( 'ขั้นตอน 1 · หัวข้อ', 'text' ),
				'step1_desc'     => array( 'ขั้นตอน 1 · รายละเอียด', 'textarea' ),
				'step2_title'    => array( 'ขั้นตอน 2 · หัวข้อ', 'text' ),
				'step2_desc'     => array( 'ขั้นตอน 2 · รายละเอียด', 'textarea' ),
				'step3_title'    => array( 'ขั้นตอน 3 · หัวข้อ', 'text' ),
				'step3_desc'     => array( 'ขั้นตอน 3 · รายละเอียด', 'textarea' ),
				'step4_title'    => array( 'ขั้นตอน 4 · หัวข้อ', 'text' ),
				'step4_desc'     => array( 'ขั้นตอน 4 · รายละเอียด', 'textarea' ),
				'steps_img'      => array( 'รูปประกอบหัวข้อนี้', 'image' ),
				'steps_img_alt'  => array( 'คำอธิบายรูป (ALT)', 'text' ),
			),
		),

		'fenix_perf' => array(
			'title'       => '8) ผลการทดสอบ / Performance',
			'description' => 'กรอกข้อมูลจริงจากการทดสอบเท่านั้น และอย่าลบข้อความ Disclaimer',
			'fields'      => array(
				'show_perf'          => array( 'แสดงส่วนนี้', 'checkbox' ),
				'perf_kicker'        => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
				'perf_title'         => array( 'หัวข้อ', 'text' ),
				'perf_subtitle'      => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'stat1_label'        => array( 'ข้อมูล 1 · หัวข้อ', 'text' ),
				'stat1_value'        => array( 'ข้อมูล 1 · ค่า', 'text' ),
				'stat2_label'        => array( 'ข้อมูล 2 · หัวข้อ', 'text' ),
				'stat2_value'        => array( 'ข้อมูล 2 · ค่า', 'text' ),
				'stat3_label'        => array( 'ข้อมูล 3 · หัวข้อ', 'text' ),
				'stat3_value'        => array( 'ข้อมูล 3 · ค่า', 'text' ),
				'stat4_label'        => array( 'ข้อมูล 4 · หัวข้อ', 'text' ),
				'stat4_value'        => array( 'ข้อมูล 4 · ค่า', 'text' ),
				'stat5_label'        => array( 'ข้อมูล 5 · หัวข้อ', 'text' ),
				'stat5_value'        => array( 'ข้อมูล 5 · ค่า', 'text' ),
				'stat6_label'        => array( 'ข้อมูล 6 · หัวข้อ', 'text' ),
				'stat6_value'        => array( 'ข้อมูล 6 · ค่า', 'text' ),
				'perf_image'         => array( 'ภาพกราฟผลทดสอบ (Backtest/Forward)', 'image' ),
				'perf_image_caption' => array( 'คำบรรยายภาพ', 'text' ),
				'verified_link_label' => array( 'ข้อความปุ่มผลเรียลไทม์ (ถ้ามี)', 'text' ),
				'verified_link_url'   => array( 'ลิงก์ผลที่ตรวจสอบได้ เช่น Myfxbook / FXBlue (แสดงปุ่มเมื่อกรอกเท่านั้น)', 'url' ),
				'perf_note'          => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
				'perf_disclaimer'    => array( 'ข้อความ Disclaimer (จำเป็นต้องมี)', 'textarea' ),
				'perf_conditions'    => array( 'เงื่อนไขการทดสอบ (ช่วงเวลา · ทุน · โบรกเกอร์ · สเปรด)', 'textarea', 'ทุกตัวเลขต้องมีเงื่อนไขกำกับเสมอ' ),
				'verify_title'       => array( 'หัวข้อกล่องวิธีตรวจสอบ', 'text' ),
				'verify_items'       => array( 'วิธีตรวจสอบ (บรรทัดละ 1 ข้อ · เว้นว่าง = ซ่อนกล่อง)', 'textarea' ),
			),
		),

		'fenix_fit' => array(
			'title'  => '9) เหมาะกับใคร / ไม่เหมาะกับใคร',
			'fields' => array(
				'show_fit'       => array( 'แสดงส่วนนี้', 'checkbox' ),
				'fit_title'      => array( 'หัวข้อ', 'text' ),
				'fit_subtitle'   => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'fit_good_title' => array( 'หัวข้อฝั่ง "เหมาะกับ"', 'text' ),
				'fit_good_items' => array( 'รายการฝั่ง "เหมาะกับ" (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'fit_bad_title'  => array( 'หัวข้อฝั่ง "ไม่เหมาะกับ"', 'text' ),
				'fit_bad_items'  => array( 'รายการฝั่ง "ไม่เหมาะกับ" (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'prof1_title'    => array( 'โปรไฟล์ 1 · หัวข้อ', 'text' ),
				'prof1_desc'     => array( 'โปรไฟล์ 1 · รายละเอียด', 'textarea' ),
				'prof2_title'    => array( 'โปรไฟล์ 2 · หัวข้อ', 'text' ),
				'prof2_desc'     => array( 'โปรไฟล์ 2 · รายละเอียด', 'textarea' ),
				'prof3_title'    => array( 'โปรไฟล์ 3 · หัวข้อ', 'text' ),
				'prof3_desc'     => array( 'โปรไฟล์ 3 · รายละเอียด', 'textarea' ),
			),
		),

		'fenix_pricing' => array(
			'title'       => '10) แพ็กเกจราคา',
			'description' => 'เลือกได้ว่าจะโชว์ราคา หรือให้สอบถามราคาทาง LINE',
			'fields'      => array(
				'show_pricing'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'pricing_title'    => array( 'หัวข้อ', 'text' ),
				'pricing_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'pricing_mode'     => array(
					'รูปแบบการแสดงราคา',
					'radio',
					array(
						'price'   => 'แสดงตัวเลขราคา',
						'contact' => 'ไม่แสดงราคา · ให้สอบถามทาง LINE',
					),
				),
				'pkg1_name'        => array( 'แพ็กเกจ 1 · ชื่อ', 'text' ),
				'pkg1_tag'         => array( 'แพ็กเกจ 1 · คำอธิบายสั้น', 'text' ),
				'pkg1_price'       => array( 'แพ็กเกจ 1 · ราคา', 'text' ),
				'pkg1_period'      => array( 'แพ็กเกจ 1 · หน่วย/ระยะเวลา', 'text' ),
				'pkg1_features'    => array( 'แพ็กเกจ 1 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg1_featured'    => array( 'แพ็กเกจ 1 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg2_name'        => array( 'แพ็กเกจ 2 · ชื่อ', 'text' ),
				'pkg2_tag'         => array( 'แพ็กเกจ 2 · คำอธิบายสั้น', 'text' ),
				'pkg2_price'       => array( 'แพ็กเกจ 2 · ราคา', 'text' ),
				'pkg2_period'      => array( 'แพ็กเกจ 2 · หน่วย/ระยะเวลา', 'text' ),
				'pkg2_features'    => array( 'แพ็กเกจ 2 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg2_featured'    => array( 'แพ็กเกจ 2 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pkg3_name'        => array( 'แพ็กเกจ 3 · ชื่อ', 'text' ),
				'pkg3_tag'         => array( 'แพ็กเกจ 3 · คำอธิบายสั้น', 'text' ),
				'pkg3_price'       => array( 'แพ็กเกจ 3 · ราคา', 'text' ),
				'pkg3_period'      => array( 'แพ็กเกจ 3 · หน่วย/ระยะเวลา', 'text' ),
				'pkg3_features'    => array( 'แพ็กเกจ 3 · สิ่งที่ได้ (บรรทัดละ 1 ข้อ)', 'textarea' ),
				'pkg3_featured'    => array( 'แพ็กเกจ 3 · ติดป้าย "แนะนำ"', 'checkbox' ),
				'pricing_btn_text' => array( 'ข้อความปุ่มบนการ์ดราคา', 'text' ),
				'pricing_note'     => array( 'หมายเหตุท้ายส่วนราคา', 'text' ),
				'show_pricing_home'  => array( 'แสดงตัวอย่างแพ็กเกจบนหน้าแรก', 'checkbox' ),
				'pricing_home_title' => array( 'หน้าแรก · หัวข้อแพ็กเกจ', 'text' ),
				'pricing_home_sub'   => array( 'หน้าแรก · คำอธิบายแพ็กเกจ', 'textarea' ),
			),
		),

		'fenix_reviews' => array(
			'title'       => '11) รีวิวลูกค้า',
			'description' => 'สำคัญ: ใช้รีวิวจริงจากลูกค้าเท่านั้น ห้ามแต่งรีวิว และหลีกเลี่ยงรีวิวแนวการันตีกำไร เมื่อมีรีวิวจริงแล้วค่อยติ๊ก "แสดงส่วนนี้"',
			'fields'      => array(
				'show_reviews'     => array( 'แสดงส่วนนี้ (เปิดเมื่อมีรีวิวจริง)', 'checkbox' ),
				'reviews_title'    => array( 'หัวข้อ', 'text' ),
				'reviews_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
				'rev1_text'        => array( 'รีวิว 1 · ข้อความ', 'textarea' ),
				'rev1_name'        => array( 'รีวิว 1 · ชื่อผู้รีวิว', 'text' ),
				'rev2_text'        => array( 'รีวิว 2 · ข้อความ', 'textarea' ),
				'rev2_name'        => array( 'รีวิว 2 · ชื่อผู้รีวิว', 'text' ),
				'rev3_text'        => array( 'รีวิว 3 · ข้อความ', 'textarea' ),
				'rev3_name'        => array( 'รีวิว 3 · ชื่อผู้รีวิว', 'text' ),
			),
		),

		'fenix_faq' => array(
			'title'       => '12) FAQ คำถามที่พบบ่อย',
			'description' => 'มีช่องให้ 10 ข้อ ข้อไหนเว้นว่างไว้จะไม่แสดงผล',
			'fields'      => array(
				'show_faq'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'faq_title'    => array( 'หัวข้อ', 'text' ),
				'faq_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			),
		),

		'fenix_risk' => array(
			'title'       => '13) คำเตือนความเสี่ยง',
			'description' => 'ส่วนนี้สำคัญต่อความน่าเชื่อถือของแบรนด์ ไม่แนะนำให้ปิด',
			'fields'      => array(
				'show_risk'  => array( 'แสดงส่วนนี้', 'checkbox' ),
				'risk_title' => array( 'หัวข้อ', 'text' ),
				'risk_text'  => array( 'ข้อความคำเตือน', 'textarea' ),
			),
		),

		'fenix_cta' => array(
			'title'  => '14) CTA ปิดท้าย',
			'fields' => array(
				'show_cta'     => array( 'แสดงส่วนนี้', 'checkbox' ),
				'show_mid_cta'  => array( 'แสดงแถบทัก LINE คั่นกลางหน้าแรก', 'checkbox' ),
				'mid_cta_title' => array( 'แถบกลางหน้า · หัวข้อ', 'text' ),
				'mid_cta_text'  => array( 'แถบกลางหน้า · คำอธิบาย', 'textarea' ),
				'cta_title'    => array( 'หัวข้อ', 'text' ),
				'cta_subtitle' => array( 'คำอธิบาย', 'textarea' ),
				'cta_btn_text' => array( 'ข้อความปุ่ม', 'text' ),
			),
		),

		'fenix_footer' => array(
			'title'  => '15) Footer ท้ายเว็บ',
			'fields' => array(
				'footer_kicker'       => array( 'ข้อความเล็กเหนือ CTA footer', 'text' ),
				'footer_cta_title'    => array( 'หัวข้อ CTA ใน footer', 'text' ),
				'footer_cta_text'     => array( 'คำอธิบาย CTA ใน footer', 'textarea' ),
				'footer_line_text'    => array( 'ข้อความปุ่ม LINE ใน footer', 'text' ),
				'footer_facebook_text' => array( 'ข้อความลิงก์ Facebook ใน footer', 'text' ),
				'footer_email_text'    => array( 'ข้อความลิงก์ Email ใน footer', 'text' ),
				'footer_prep_title'    => array( 'หัวข้อการ์ดเตรียมข้อมูลก่อนทัก LINE', 'text' ),
				'footer_prep_text'     => array( 'คำอธิบายการ์ดเตรียมข้อมูลก่อนทัก LINE', 'textarea' ),
				'footer_prep_items'    => array( 'รายการที่ควรเตรียม (บรรทัดละ 1 รายการ)', 'textarea' ),
				'footer_tagline'      => array( 'คำโปรยใต้โลโก้', 'textarea' ),
				'footer_risk_link'    => array( 'ข้อความลิงก์คำเตือนความเสี่ยง', 'text' ),
			),
		),

		'fenix_mobile_nav' => array(
			'title'       => '16) เมนูลัดมือถือด้านล่าง',
			'description' => 'เมนูลัดด้านล่างบนมือถือ ใช้สำหรับพาผู้เข้าชมไปยังหน้าหลักที่สำคัญและปุ่มทัก LINE',
			'fields'      => array(
				'show_mobile_nav'        => array( 'แสดงเมนูลัดด้านล่างบนมือถือ', 'checkbox' ),
				'mobile_nav_home_label'  => array( 'เมนู 1 · ชื่อ', 'text' ),
				'mobile_nav_home_url'    => array( 'เมนู 1 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_test_label'  => array( 'เมนู 2 · ชื่อ', 'text' ),
				'mobile_nav_test_url'    => array( 'เมนู 2 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_price_label'   => array( 'เมนู 3 · ชื่อ', 'text' ),
				'mobile_nav_price_url'     => array( 'เมนู 3 · ลิงก์หรือ slug', 'text' ),
				'mobile_nav_dash_label'    => array( 'เมนู 4 · ชื่อ (แดชบอร์ด)', 'text' ),
				'mobile_nav_dash_url'      => array( 'เมนู 4 · ลิงก์ (เว้นว่าง = ใช้ลิงก์แดชบอร์ดในหมวด 29)', 'text' ),
				'mobile_nav_line_label'    => array( 'เมนู 5 · ชื่อปุ่ม LINE', 'text' ),
			),
		),
	);

	$sections['fenix_seo'] = array(
		'title'       => '17) SEO / แชร์ลิงก์',
		'description' => 'รูปและคำโปรยเวลาแชร์ลิงก์ใน LINE/Facebook (Open Graph) บทความจะใช้ภาพหน้าปก (Featured image) อัตโนมัติ',
		'fields'      => array(
			'og_default_image'       => array( 'รูปสำหรับแชร์ (แนะนำ 1200×630px)', 'image' ),
			'og_default_description' => array( 'คำโปรยเวลาแชร์หน้าหลัก/หน้าที่ไม่มีภาพ', 'textarea' ),
			'search_console_verify'  => array( 'โค้ดยืนยัน Google Search Console (เฉพาะค่าใน content="...")', 'text' ),
			'bing_verify'            => array( 'โค้ดยืนยัน Bing Webmaster (msvalidate.01)', 'text' ),
		),
	);

	$sections['fenix_cookie'] = array(
		'title'       => '18) คุกกี้ / Tracking',
		'description' => 'แถบขอความยินยอมคุกกี้ และโค้ดติดตาม (Google Analytics / Facebook Pixel จะโหลดเฉพาะหลังผู้ใช้กด "ยอมรับ" เท่านั้น)',
		'fields'      => array(
			'show_cookie_consent' => array( 'แสดงแถบขอความยินยอมคุกกี้', 'checkbox' ),
			'cookie_consent_text' => array( 'ข้อความบนแถบคุกกี้', 'textarea' ),
			'ga_measurement_id'   => array( 'Google Analytics 4 ID (เช่น G-XXXXXXXXXX)', 'text' ),
			'fb_pixel_id'         => array( 'Facebook Pixel ID', 'text' ),
		),
	);

	/* เพิ่ม FAQ 10 ข้อเข้า section FAQ */
	for ( $i = 1; $i <= 10; $i++ ) {
		$sections['fenix_faq']['fields'][ 'faq' . $i . '_q' ] = array( 'คำถามข้อ ' . $i, 'text' );
		$sections['fenix_faq']['fields'][ 'faq' . $i . '_a' ] = array( 'คำตอบข้อ ' . $i, 'textarea' );
	}

	/* ===================================================
	 * หมวดเพิ่มเติมสำหรับหน้าย่อย (multipage)
	 * =================================================== */

	$sections['fenix_home'] = array(
		'title'       => '19) หน้าแรก · ไฮไลต์ & การ์ดนำทาง',
		'description' => 'แถบไฮไลต์ใต้ Hero และการ์ด 5 ใบที่ลิงก์ไปยังหน้าย่อย ลิงก์ตั้งค่าตาม slug มาตรฐานแล้ว แก้ได้หากใช้ slug อื่น',
		'fields'      => array(
			'show_highlight'   => array( 'แสดงแถบไฮไลต์ใต้ Hero', 'checkbox' ),
			'highlight1'       => array( 'ไฮไลต์ 1', 'text' ),
			'highlight2'       => array( 'ไฮไลต์ 2', 'text' ),
			'highlight3'       => array( 'ไฮไลต์ 3', 'text' ),
			'highlight4'       => array( 'ไฮไลต์ 4', 'text' ),
			'highlight_versions' => array( 'เติมชิปเวอร์ชันล่าสุดอัตโนมัติจากไฟล์ EA', 'checkbox' ),

			'show_notus'   => array( 'แสดงหัวข้อ "เราไม่ทำอะไรบ้าง"', 'checkbox' ),
			'notus_kicker' => array( 'เราไม่ทำอะไร · ป้ายเล็ก', 'text' ),
			'notus_title'  => array( 'เราไม่ทำอะไร · หัวข้อ', 'text' ),
			'notus_sub'    => array( 'เราไม่ทำอะไร · คำอธิบาย', 'textarea' ),
			'notus_items'  => array( 'เราไม่ทำอะไร · รายการ (บรรทัดละ 1 ข้อ ใช้ — คั่นหัวข้อกับคำอธิบาย)', 'textarea' ),

			'show_devices' => array( 'แสดงหัวข้อ "ใช้กับอะไรได้บ้าง"', 'checkbox' ),
			'dev_kicker'   => array( 'อุปกรณ์ · ป้ายเล็ก', 'text' ),
			'dev_title'    => array( 'อุปกรณ์ · หัวข้อ', 'text' ),
			'dev_sub'      => array( 'อุปกรณ์ · คำอธิบาย', 'textarea' ),
			'dev1_title'   => array( 'อุปกรณ์ 1 · ชื่อ', 'text' ),
			'dev1_note'    => array( 'อุปกรณ์ 1 · ทำอะไรได้', 'textarea' ),
			'dev1_badge'   => array( 'อุปกรณ์ 1 · ป้ายมุม (เช่น แนะนำ)', 'text' ),
			'dev2_title'   => array( 'อุปกรณ์ 2 · ชื่อ', 'text' ),
			'dev2_note'    => array( 'อุปกรณ์ 2 · ทำอะไรได้', 'textarea' ),
			'dev2_badge'   => array( 'อุปกรณ์ 2 · ป้ายมุม', 'text' ),
			'dev3_title'   => array( 'อุปกรณ์ 3 · ชื่อ', 'text' ),
			'dev3_note'    => array( 'อุปกรณ์ 3 · ทำอะไรได้', 'textarea' ),
			'dev3_badge'   => array( 'อุปกรณ์ 3 · ป้ายมุม', 'text' ),
			'dev_req'      => array( 'บรรทัดความต้องการระบบ', 'textarea' ),
			'dev_img'      => array( 'อุปกรณ์ · รูปประกอบ', 'image' ),
			'dev_img_alt'  => array( 'อุปกรณ์ · คำอธิบายรูป (ALT)', 'text' ),
			'dev_btn_label'  => array( 'อุปกรณ์ · ปุ่มหลัก', 'text' ),
			'dev_btn_url'    => array( 'อุปกรณ์ · ลิงก์ปุ่มหลัก', 'text' ),
			'dev_btn2_label' => array( 'อุปกรณ์ · ปุ่มรอง', 'text' ),
			'dev_btn2_url'   => array( 'อุปกรณ์ · ลิงก์ปุ่มรอง', 'text' ),

			'show_own'   => array( 'แสดงหัวข้อ "สิ่งที่ยังเป็นของคุณ"', 'checkbox' ),
			'own_kicker' => array( 'สิ่งที่เป็นของคุณ · ป้ายเล็ก', 'text' ),
			'own_title'  => array( 'สิ่งที่เป็นของคุณ · หัวข้อ', 'text' ),
			'own_sub'    => array( 'สิ่งที่เป็นของคุณ · คำอธิบาย', 'textarea' ),
			'own_items'  => array( 'สิ่งที่เป็นของคุณ · รายการ (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'show_live_status'     => array( 'แสดงแถบสถานะเคลื่อนไหวใต้ Hero', 'checkbox' ),
			'live_status_kicker'   => array( 'ป้ายแถบสถานะ', 'text' ),
			'live_status_items'    => array( 'ข้อความในแถบสถานะ (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'show_control_center'  => array( 'แสดงส่วน Control Center หน้าแรก', 'checkbox' ),
			'control_kicker'       => array( 'Control Center · ป้ายเล็ก', 'text' ),
			'control_title'        => array( 'Control Center · หัวข้อ', 'text' ),
			'control_subtitle'     => array( 'Control Center · คำอธิบาย', 'textarea' ),
			'control_panel_title'  => array( 'แผงสถานะ · หัวข้อ', 'text' ),
			'control_panel_status' => array( 'แผงสถานะ · สถานะหลัก', 'text' ),
			'control_badge'        => array( 'แผงสถานะ · ป้ายด้านขวา', 'text' ),
			'control_panel_text'   => array( 'แผงสถานะ · รายละเอียด', 'textarea' ),
			'control_metric1_label' => array( 'ข้อมูลย่อย 1 · หัวข้อ', 'text' ),
			'control_metric1_value' => array( 'ข้อมูลย่อย 1 · ค่า', 'text' ),
			'control_metric2_label' => array( 'ข้อมูลย่อย 2 · หัวข้อ', 'text' ),
			'control_metric2_value' => array( 'ข้อมูลย่อย 2 · ค่า', 'text' ),
			'control_metric3_label' => array( 'ข้อมูลย่อย 3 · หัวข้อ', 'text' ),
			'control_metric3_value' => array( 'ข้อมูลย่อย 3 · ค่า', 'text' ),
			'control_list_title'   => array( 'รายการเตรียมตัว · หัวข้อ', 'text' ),
			'control_list_items'   => array( 'รายการเตรียมตัว (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'home_cards_title' => array( 'หัวข้อกลุ่มการ์ด', 'text' ),
			'home_cards_sub'   => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'card1_title'      => array( 'การ์ด 1 · หัวข้อ', 'text' ),
			'card1_desc'       => array( 'การ์ด 1 · รายละเอียด', 'textarea' ),
			'card1_url'        => array( 'การ์ด 1 · ลิงก์', 'url' ),
			'card2_title'      => array( 'การ์ด 2 · หัวข้อ', 'text' ),
			'card2_desc'       => array( 'การ์ด 2 · รายละเอียด', 'textarea' ),
			'card2_url'        => array( 'การ์ด 2 · ลิงก์', 'url' ),
			'card3_title'      => array( 'การ์ด 3 · หัวข้อ', 'text' ),
			'card3_desc'       => array( 'การ์ด 3 · รายละเอียด', 'textarea' ),
			'card3_url'        => array( 'การ์ด 3 · ลิงก์', 'url' ),
			'card4_title'      => array( 'การ์ด 4 · หัวข้อ', 'text' ),
			'card4_desc'       => array( 'การ์ด 4 · รายละเอียด', 'textarea' ),
			'card4_url'        => array( 'การ์ด 4 · ลิงก์', 'url' ),
			'card5_title'      => array( 'การ์ด 5 · หัวข้อ', 'text' ),
			'card5_desc'       => array( 'การ์ด 5 · รายละเอียด', 'textarea' ),
			'card5_url'        => array( 'การ์ด 5 · ลิงก์', 'url' ),
		),
	);

	$sections['fenix_backtest'] = array(
		'title'       => '20) หน้า Backtest',
		'description' => 'กรอกผลการทดสอบย้อนหลังจริงเท่านั้น และอย่าลบ Disclaimer',
		'fields'      => array(
			'backtest_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'backtest_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'bt_stat1_label'       => array( 'สถิติ 1 · หัวข้อ', 'text' ),
			'bt_stat1_value'       => array( 'สถิติ 1 · ค่า', 'text' ),
			'bt_stat2_label'       => array( 'สถิติ 2 · หัวข้อ', 'text' ),
			'bt_stat2_value'       => array( 'สถิติ 2 · ค่า', 'text' ),
			'bt_stat3_label'       => array( 'สถิติ 3 · หัวข้อ', 'text' ),
			'bt_stat3_value'       => array( 'สถิติ 3 · ค่า', 'text' ),
			'bt_stat4_label'       => array( 'สถิติ 4 · หัวข้อ', 'text' ),
			'bt_stat4_value'       => array( 'สถิติ 4 · ค่า', 'text' ),
			'bt_stat5_label'       => array( 'สถิติ 5 · หัวข้อ', 'text' ),
			'bt_stat5_value'       => array( 'สถิติ 5 · ค่า', 'text' ),
			'bt_stat6_label'       => array( 'สถิติ 6 · หัวข้อ', 'text' ),
			'bt_stat6_value'       => array( 'สถิติ 6 · ค่า', 'text' ),
			'bt_stat7_label'       => array( 'สถิติ 7 · หัวข้อ', 'text' ),
			'bt_stat7_value'       => array( 'สถิติ 7 · ค่า', 'text' ),
			'bt_stat8_label'       => array( 'สถิติ 8 · หัวข้อ', 'text' ),
			'bt_stat8_value'       => array( 'สถิติ 8 · ค่า', 'text' ),
			'backtest_img'         => array( 'ภาพกราฟ/รายงานผล Backtest', 'image' ),
			'backtest_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
			'backtest_note'        => array( 'หมายเหตุเงื่อนไขการทดสอบ', 'textarea' ),
			'backtest_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
		),
	);

	$sections['fenix_forward'] = array(
		'title'       => '21) หน้า Forward Test',
		'description' => 'กรอกผลการทดสอบบนบัญชีจริง/เดโม่จริงเท่านั้น และอย่าลบ Disclaimer',
		'fields'      => array(
			'forward_sub'         => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'forward_intro'       => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'fw_stat1_label'      => array( 'สถิติ 1 · หัวข้อ', 'text' ),
			'fw_stat1_value'      => array( 'สถิติ 1 · ค่า', 'text' ),
			'fw_stat2_label'      => array( 'สถิติ 2 · หัวข้อ', 'text' ),
			'fw_stat2_value'      => array( 'สถิติ 2 · ค่า', 'text' ),
			'fw_stat3_label'      => array( 'สถิติ 3 · หัวข้อ', 'text' ),
			'fw_stat3_value'      => array( 'สถิติ 3 · ค่า', 'text' ),
			'fw_stat4_label'      => array( 'สถิติ 4 · หัวข้อ', 'text' ),
			'fw_stat4_value'      => array( 'สถิติ 4 · ค่า', 'text' ),
			'fw_stat5_label'      => array( 'สถิติ 5 · หัวข้อ', 'text' ),
			'fw_stat5_value'      => array( 'สถิติ 5 · ค่า', 'text' ),
			'fw_stat6_label'      => array( 'สถิติ 6 · หัวข้อ', 'text' ),
			'fw_stat6_value'      => array( 'สถิติ 6 · ค่า', 'text' ),
			'forward_img'         => array( 'ภาพผลการทดสอบ', 'image' ),
			'forward_img_caption' => array( 'คำบรรยายภาพ', 'text' ),
			'forward_link_label'  => array( 'ข้อความปุ่มลิงก์ผลเรียลไทม์ (ถ้ามี)', 'text' ),
			'forward_link_url'    => array( 'ลิงก์ผลเรียลไทม์ (เช่น Myfxbook)', 'url' ),
			'forward_note'        => array( 'หมายเหตุ', 'textarea' ),
			'forward_disclaimer'  => array( 'Disclaimer (จำเป็น)', 'textarea' ),
		),
	);

	$sections['fenix_install'] = array(
		'title'       => '22) หน้า How to Install',
		'description' => 'คู่มือติดตั้งทีละขั้นตอน อัปโหลดภาพประกอบแต่ละขั้นได้ (เว้นว่างได้)',
		'fields'      => array(
			'install_sub'      => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'install_intro'    => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'install_req'      => array( 'สิ่งที่ต้องเตรียม (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'inst_step1_title' => array( 'ขั้นตอน 1 · หัวข้อ', 'text' ),
			'inst_step1_desc'  => array( 'ขั้นตอน 1 · รายละเอียด', 'textarea' ),
			'inst_step1_img'   => array( 'ขั้นตอน 1 · ภาพ', 'image' ),
			'inst_step2_title' => array( 'ขั้นตอน 2 · หัวข้อ', 'text' ),
			'inst_step2_desc'  => array( 'ขั้นตอน 2 · รายละเอียด', 'textarea' ),
			'inst_step2_img'   => array( 'ขั้นตอน 2 · ภาพ', 'image' ),
			'inst_step3_title' => array( 'ขั้นตอน 3 · หัวข้อ', 'text' ),
			'inst_step3_desc'  => array( 'ขั้นตอน 3 · รายละเอียด', 'textarea' ),
			'inst_step3_img'   => array( 'ขั้นตอน 3 · ภาพ', 'image' ),
			'inst_step4_title' => array( 'ขั้นตอน 4 · หัวข้อ', 'text' ),
			'inst_step4_desc'  => array( 'ขั้นตอน 4 · รายละเอียด', 'textarea' ),
			'inst_step4_img'   => array( 'ขั้นตอน 4 · ภาพ', 'image' ),
			'inst_step5_title' => array( 'ขั้นตอน 5 · หัวข้อ', 'text' ),
			'inst_step5_desc'  => array( 'ขั้นตอน 5 · รายละเอียด', 'textarea' ),
			'inst_step5_img'   => array( 'ขั้นตอน 5 · ภาพ', 'image' ),
			'inst_step6_title' => array( 'ขั้นตอน 6 · หัวข้อ', 'text' ),
			'inst_step6_desc'  => array( 'ขั้นตอน 6 · รายละเอียด', 'textarea' ),
			'inst_step6_img'   => array( 'ขั้นตอน 6 · ภาพ', 'image' ),
			'install_note'     => array( 'ข้อความปิดท้าย', 'text' ),
		),
	);

	$sections['fenix_pricing_extra'] = array(
		'title'       => '23) หน้า Pricing (เพิ่มเติม)',
		'description' => 'หน้านี้ใช้แพ็กเกจจากหมวด "แพ็กเกจราคา" ร่วมกัน และเพิ่มตารางเปรียบเทียบได้ที่นี่',
		'fields'      => array(
			'pricing_sub'   => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'compare_title' => array( 'หัวข้อตารางเปรียบเทียบ', 'text' ),
			'compare_rows'  => array( 'ตารางเปรียบเทียบ (บรรทัดละ 1 แถว คั่นช่องด้วย | บรรทัดแรกคือหัวตาราง ใช้ ✓ และ ✗ ได้)', 'textarea' ),
		),
	);

	$sections['fenix_riskpage'] = array(
		'title'       => '24) หน้า Risk Disclosure',
		'description' => 'หน้าประกาศความเสี่ยงฉบับเต็ม มี 6 หัวข้อ เว้นว่างหัวข้อที่ไม่ใช้ได้',
		'fields'      => array(
			'riskpage_sub'     => array( 'คำโปรยใต้ชื่อหน้า', 'text' ),
			'riskpage_intro'   => array( 'ย่อหน้าเกริ่นนำ', 'textarea' ),
			'riskpage_image'   => array( 'ภาพประกอบความเสี่ยง', 'image' ),
			'riskpage_image_caption' => array( 'คำบรรยายภาพประกอบความเสี่ยง', 'text' ),
			'rp_block1_title'  => array( 'หัวข้อ 1', 'text' ),
			'rp_block1_text'   => array( 'เนื้อหา 1', 'textarea' ),
			'rp_block2_title'  => array( 'หัวข้อ 2', 'text' ),
			'rp_block2_text'   => array( 'เนื้อหา 2', 'textarea' ),
			'rp_block3_title'  => array( 'หัวข้อ 3', 'text' ),
			'rp_block3_text'   => array( 'เนื้อหา 3', 'textarea' ),
			'rp_block4_title'  => array( 'หัวข้อ 4', 'text' ),
			'rp_block4_text'   => array( 'เนื้อหา 4', 'textarea' ),
			'rp_block5_title'  => array( 'หัวข้อ 5', 'text' ),
			'rp_block5_text'   => array( 'เนื้อหา 5', 'textarea' ),
			'rp_block6_title'  => array( 'หัวข้อ 6', 'text' ),
			'rp_block6_text'   => array( 'เนื้อหา 6', 'textarea' ),
			'riskpage_updated' => array( 'วันที่ปรับปรุงล่าสุด', 'text' ),
		),
	);

	$sections['fenix_team'] = array(
		'title'       => '25) ทีมงาน / ใครอยู่เบื้องหลัง',
		'description' => 'แสดงตัวตนของทีม/ผู้พัฒนาเพื่อสร้างความน่าเชื่อถือ แนะนำประสบการณ์และเหตุผลที่สร้างระบบ เปิด "แสดงส่วนนี้" เมื่อกรอกข้อมูลจริงแล้ว ห้ามใส่ข้อมูลเท็จ',
		'fields'      => array(
			'show_team'   => array( 'แสดงส่วนนี้ (เปิดเมื่อกรอกข้อมูลจริง)', 'checkbox' ),
			'team_kicker' => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'team_title'  => array( 'หัวข้อ', 'text' ),
			'team_text'   => array( 'เนื้อหา (เว้นบรรทัด = ย่อหน้าใหม่)', 'textarea' ),
			'team_points' => array( 'จุดเด่นของทีม (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'team_img'    => array( 'รูปทีม / ผู้พัฒนา (ถ้ามี)', 'image' ),
			'company_name'    => array( 'ชื่อผู้ประกอบการ / นิติบุคคล', 'text', 'เว้นว่าง = ไม่แสดงแถบตัวตน' ),
			'company_reg'     => array( 'เลขทะเบียนพาณิชย์ / นิติบุคคล', 'text' ),
			'company_address' => array( 'ที่อยู่ที่เปิดเผยได้', 'text' ),
			'company_email'   => array( 'อีเมลติดต่อ', 'text' ),
		),
	);

	$sections['fenix_assurance'] = array(
		'title'       => '26) ความมั่นใจก่อนเริ่ม',
		'description' => 'จุดสร้างความสบายใจก่อนตัดสินใจ ใช้ข้อความที่เป็นจริง ไม่การันตีกำไร',
		'fields'      => array(
			'show_assurance'     => array( 'แสดงส่วนนี้', 'checkbox' ),
			'assurance_kicker'   => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'assurance_title'    => array( 'หัวข้อ', 'text' ),
			'assurance_subtitle' => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'assurance_items'    => array( 'รายการความมั่นใจ (บรรทัดละ 1 ข้อ)', 'textarea' ),
		),
	);

	$sections['fenix_blog'] = array(
		'title'       => '27) หน้าแรก · บทความล่าสุด',
		'description' => 'แสดงบทความล่าสุด 3 รายการบนหน้าแรก ส่วนนี้จะซ่อนอัตโนมัติเมื่อยังไม่มีบทความที่เผยแพร่',
		'fields'      => array(
			'show_blog'      => array( 'แสดงส่วนนี้', 'checkbox' ),
			'blog_kicker'    => array( 'ป้ายเล็กเหนือหัวข้อ', 'text' ),
			'blog_title'     => array( 'หัวข้อ', 'text' ),
			'blog_subtitle'  => array( 'คำอธิบายใต้หัวข้อ', 'textarea' ),
			'blog_all_label' => array( 'ข้อความลิงก์ดูบทความทั้งหมด', 'text' ),
		),
	);

	$sections['fenix_links'] = array(
		'title'       => '28) หน้า Link Hub (สำหรับยิงแอด)',
		'description' => 'หน้า "ลิงก์รวม" สไตล์ Linktree สำหรับใช้เป็นปลายทางยิงแอด: สร้างเพจใหม่ เลือกเทมเพลต "FENIX · หน้า Link Hub" แล้วตั้ง slug เช่น go ปุ่มที่เว้นว่าง (ทั้งข้อความและลิงก์) จะถูกซ่อนอัตโนมัติ',
		'fields'      => array(
			'links_logo'       => array( 'โลโก้ (ไม่ใส่ = ใช้โลโก้เว็บ)', 'image' ),
			'links_title'      => array( 'ชื่อแบรนด์', 'text' ),
			'links_tagline'    => array( 'คำโปรยใต้ชื่อ (เว้นบรรทัด = ขึ้นบรรทัดใหม่)', 'textarea' ),
			'links_badges'     => array( 'ป้ายเล็ก (บรรทัดละ 1 ป้าย)', 'textarea' ),
			'links_signup_label' => array( 'ข้อความปุ่มสมัคร', 'text' ),
			'links_signup_url'   => array( 'ลิงก์ปุ่มสมัคร (เช่น /pricing/ หรือ URL เต็ม)', 'text' ),
			'links_account_guide_label' => array( 'ข้อความปุ่มสอนเปิดบัญชี', 'text' ),
			'links_account_guide_url'   => array( 'ลิงก์ปุ่มสอนเปิดบัญชี', 'text' ),
			'links_mt5_download_label'  => array( 'ข้อความปุ่มโหลด MT5', 'text' ),
			'links_mt5_download_url'    => array( 'ลิงก์ปุ่มโหลด MT5', 'text' ),
			'links_top_line_label'      => array( 'ปุ่ม LINE สั้นใต้ชื่อแบรนด์ · ข้อความ (ว่าง = ซ่อน)', 'text' ),
			'links_steps_title'         => array( 'ขั้นตอนเริ่มใช้งาน · หัวข้อรวม', 'text', 'ใส่ {n} เพื่อแสดงจำนวนขั้นตอนอัตโนมัติ · ขั้นที่ไม่มีปุ่มจะถูกซ่อนและเลขเรียงใหม่' ),
			'links_step1_title'         => array( 'ขั้นเปิดบัญชี · หัวข้อ', 'text' ),
			'links_step1_desc'          => array( 'ขั้นเปิดบัญชี · คำอธิบาย', 'text' ),
			'links_mt5_install_title'   => array( 'ขั้นติดตั้ง MT5 · หัวข้อ', 'text', 'ปุ่มระบบที่เว้นลิงก์ว่างจะถูกซ่อน' ),
			'links_step2_desc'          => array( 'ขั้นติดตั้ง MT5 · คำอธิบาย', 'text' ),
			'links_step3_title'         => array( 'ขั้นฝากเงิน · หัวข้อ', 'text' ),
			'links_step3_desc'          => array( 'ขั้นฝากเงิน · คำอธิบาย', 'text' ),
			'links_deposit_label'       => array( 'ขั้นฝากเงิน · ข้อความปุ่มหลัก', 'text' ),
			'links_deposit_url'         => array( 'ขั้นฝากเงิน · ลิงก์ปุ่มหลัก (ว่าง = ซ่อน)', 'text' ),
			'links_deposit_guide_label' => array( 'ขั้นฝากเงิน · ข้อความปุ่มคู่มือ', 'text' ),
			'links_deposit_guide_url'   => array( 'ขั้นฝากเงิน · ลิงก์คู่มือฝากเงิน (ว่าง = ซ่อน)', 'text' ),
			'links_step4_title'         => array( 'ขั้นดาวน์โหลด EA · หัวข้อ', 'text' ),
			'links_step4_desc'          => array( 'ขั้นดาวน์โหลด EA · คำอธิบาย', 'text' ),
			'links_step5_title'         => array( 'ขั้นติดตั้ง EA · หัวข้อ', 'text' ),
			'links_step5_desc'          => array( 'ขั้นติดตั้ง EA · คำอธิบาย', 'text' ),
			'links_step6_title'         => array( 'ขั้น VPS · หัวข้อ', 'text' ),
			'links_step6_desc'          => array( 'ขั้น VPS · คำอธิบาย', 'text' ),
			'links_step6_badge'         => array( 'ขั้น VPS · ป้ายเล็ก (ว่าง = ซ่อน)', 'text' ),
			'links_info_title'          => array( 'กลุ่มข้อมูลก่อนตัดสินใจ · หัวข้อ', 'text' ),
			'links_help_title'          => array( 'กลุ่มติดต่อทีม · หัวข้อ', 'text' ),
			'links_mt5_ios_label'       => array( 'ติดตั้ง MT5 · iPhone · ข้อความ', 'text' ),
			'links_mt5_ios_url'         => array( 'ติดตั้ง MT5 · iPhone · ลิงก์ App Store', 'text' ),
			'links_mt5_android_label'   => array( 'ติดตั้ง MT5 · Android · ข้อความ', 'text' ),
			'links_mt5_android_url'     => array( 'ติดตั้ง MT5 · Android · ลิงก์ Google Play', 'text' ),
			'links_mt5_windows_label'   => array( 'ติดตั้ง MT5 · Windows · ข้อความ', 'text' ),
			'links_mt5_windows_url'     => array( 'ติดตั้ง MT5 · Windows · ลิงก์ตัวติดตั้ง', 'text' ),
			'links_mt5_macos_label'     => array( 'ติดตั้ง MT5 · macOS · ข้อความ', 'text' ),
			'links_mt5_macos_url'       => array( 'ติดตั้ง MT5 · macOS · ลิงก์ (ว่าง = ซ่อน)', 'text' ),
			'links_line_label' => array( 'ข้อความปุ่ม LINE (ปุ่มหลัก)', 'text', 'ลิงก์ใช้ค่า LINE OA จากหมวด 1 อัตโนมัติ' ),
			'links_openchat_label' => array( 'ข้อความปุ่ม LINE OpenChat', 'text' ),
			'links_openchat_url'   => array( 'ลิงก์ LINE OpenChat', 'url' ),
			'links_fast_enabled' => array( 'แสดงการ์ดสินค้าตัวที่ 2 (FENIX DUAL) แทนการ์ดดาวน์โหลดเดิมทั้งสองใบ', 'checkbox' ),
			'links_fast_show'    => array( 'FENIX DUAL · แสดงการ์ดนี้ (ปิด = ซ่อน โดยไม่ดึงการ์ดเก่ากลับมา)', 'checkbox' ),
			'links_fast_img'     => array( 'FENIX DUAL · รูปดาวน์โหลด', 'image' ),
			'links_fast_url'     => array( 'FENIX DUAL · ลิงก์ไฟล์ดาวน์โหลด', 'text' ),
			'links_fast_alt'     => array( 'FENIX DUAL · ข้อความอธิบายรูป (Alt)', 'text' ),
			'links_fast_version' => array( 'FENIX DUAL · เลขเวอร์ชันที่แสดง เช่น 6.1 (เว้นว่าง = ดึงจากชื่อไฟล์ให้อัตโนมัติ)', 'text' ),
			'links_plus_enabled' => array( 'แสดงการ์ด FENIX PLUS (ต่อจาก FENIX FAST)', 'checkbox' ),
			'links_plus_img'     => array( 'FENIX PLUS · รูปดาวน์โหลด', 'image' ),
			'links_plus_url'     => array( 'FENIX PLUS · ลิงก์ไฟล์ดาวน์โหลด', 'text' ),
			'links_plus_alt'     => array( 'FENIX PLUS · ข้อความอธิบายรูป (Alt)', 'text' ),
			'links_plus_version_label' => array( 'คำนำหน้าเลขเวอร์ชันของทุกการ์ด (เว้นว่าง = ไม่แสดงคำนำหน้า)', 'text' ),
			'links_plus_version'       => array( 'FENIX PLUS · เลขเวอร์ชันที่แสดง เช่น 4.2 (เว้นว่าง = ดึงจากชื่อไฟล์ให้อัตโนมัติ)', 'text' ),
			'links_card3_enabled' => array( 'แสดงการ์ด FENIX BTC', 'checkbox' ),
			'links_card3_img'     => array( 'FENIX BTC · รูปดาวน์โหลด', 'image' ),
			'links_card3_url'     => array( 'FENIX BTC · ลิงก์ไฟล์ดาวน์โหลด', 'text' ),
			'links_card3_alt'     => array( 'FENIX BTC · ข้อความอธิบายรูป (Alt)', 'text' ),
			'links_card3_version' => array( 'FENIX BTC · เลขเวอร์ชันที่แสดง (เว้นว่าง = ดึงจากชื่อไฟล์ให้อัตโนมัติ)', 'text' ),
			'links_download_label' => array( 'ข้อความบนปุ่มดาวน์โหลด (เว้นว่าง = ไม่แสดงปุ่ม)', 'text' ),
			'links_download_pos'   => array(
				'ตำแหน่งปุ่มดาวน์โหลดบนรูป',
				'radio',
				array(
					'bl' => 'มุมล่างซ้าย',
					'br' => 'มุมล่างขวา',
					'bc' => 'กลางด้านล่าง',
				),
			),
			'links_version_pos'    => array(
				'ตำแหน่งป้ายเลขเวอร์ชัน',
				'radio',
				array(
					'br'    => 'บนรูป · มุมล่างขวา',
					'bl'    => 'บนรูป · มุมล่างซ้าย',
					'under' => 'ใต้รูป (แบบเดิม)',
				),
			),
			'links_feature_img'     => array( 'รูปโชว์ (ใต้ปุ่ม LINE) · ไม่ใส่ = ใช้รูปดาวน์โหลดค่าเริ่มต้น', 'image', 'แนะนำภาพแนวนอน 1200×630px สำหรับ Link Hub' ),
			'links_feature_url'     => array( 'รูปโชว์ · ลิงก์เมื่อคลิก (ไม่ใส่ = รูปค่าเริ่มต้นจะไป LINE)', 'text' ),
			'links_feature_caption' => array( 'รูปโชว์ · ข้อความใต้รูป (ถ้ามี)', 'text' ),
			'links_feature2_img'     => array( 'รูปโชว์ 2 (เว้นว่าง = แสดงโครงรอใส่รูป)', 'image', 'แนะนำภาพแนวนอน 1200×630px' ),
			'links_feature2_url'     => array( 'รูปโชว์ 2 · ลิงก์เมื่อคลิก', 'text' ),
			'links_feature2_caption' => array( 'รูปโชว์ 2 · ข้อความใต้รูป (ถ้ามี)', 'text' ),
			'links_feature2_placeholder' => array( 'รูปโชว์ 2 · ข้อความในโครงรูป', 'text' ),
			'links_btn1_label' => array( 'ปุ่ม 1 · ข้อความ', 'text' ),
			'links_btn1_url'   => array( 'ปุ่ม 1 · ลิงก์ (ใส่ slug เช่น /forward-test/ หรือ URL เต็ม)', 'text' ),
			'links_btn2_label' => array( 'ปุ่ม 2 · ข้อความ', 'text' ),
			'links_btn2_url'   => array( 'ปุ่ม 2 · ลิงก์', 'text' ),
			'links_btn3_label' => array( 'ปุ่ม 3 · ข้อความ', 'text' ),
			'links_btn3_url'   => array( 'ปุ่ม 3 · ลิงก์', 'text' ),
			'links_btn4_label' => array( 'ปุ่ม 4 · ข้อความ', 'text' ),
			'links_btn4_url'   => array( 'ปุ่ม 4 · ลิงก์', 'text' ),
			'links_btn5_label' => array( 'ปุ่ม 5 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
			'links_btn5_url'   => array( 'ปุ่ม 5 · ลิงก์', 'text' ),
			'links_btn6_label' => array( 'ปุ่ม 6 · ข้อความ (เว้นว่าง = ซ่อน)', 'text' ),
			'links_btn6_url'   => array( 'ปุ่ม 6 · ลิงก์', 'text' ),
			'links_guide1_label' => array( 'คู่มือ 1 · ข้อความ', 'text' ),
			'links_guide1_url'   => array( 'คู่มือ 1 · ลิงก์', 'text' ),
			'links_guide2_label' => array( 'คู่มือ 2 · ข้อความ', 'text' ),
			'links_guide2_url'   => array( 'คู่มือ 2 · ลิงก์', 'text' ),
			'links_guide3_label' => array( 'คู่มือ 3 · ข้อความ', 'text' ),
			'links_guide3_url'   => array( 'คู่มือ 3 · ลิงก์', 'text' ),
			'links_guide4_label' => array( 'คู่มือ 4 · ข้อความ', 'text' ),
			'links_guide4_url'   => array( 'คู่มือ 4 · ลิงก์', 'text' ),
			'links_guide5_label' => array( 'คู่มือ 5 · ข้อความ', 'text' ),
			'links_guide5_url'   => array( 'คู่มือ 5 · ลิงก์', 'text' ),
			'links_note'       => array( 'ข้อความเตือนความเสี่ยง (ด้านล่างสุด)', 'textarea' ),
		),
	);

	$sections['fenix_dashboard'] = array(
		'title'       => '29) แดชบอร์ดลูกค้า (ปุ่มสมัคร / เข้าสู่ระบบ)',
		'description' => 'ปุ่มแดชบอร์ดบนแถบเมนู (แสดงทั้งเดสก์ท็อปและมือถือ) · เมนูลัดมือถือปุ่มที่ 4 ก็ชี้มาที่ลิงก์นี้',
		'fields'      => array(
			'dashboard_url'       => array( 'ลิงก์แดชบอร์ด', 'url' ),
			'show_header_auth'    => array( 'แสดงปุ่มแดชบอร์ดบนแถบเมนู', 'checkbox' ),
			'header_dash_label'   => array( 'ข้อความปุ่มแดชบอร์ด', 'text' ),
		),
	);

	$sections['fenix_home_hero2'] = array(
		'title'       => '19) หน้าแรก · Hero (ป้ายและตัวเลขเล็ก)',
		'description' => 'ชื่อและเวอร์ชัน EA ในป้ายและในเทปวิ่ง ดึงจากไฟล์ที่ตั้งไว้หน้าดาวน์โหลด (หมวด 12) จุดเดียวอัตโนมัติ',
		'fields'      => array(
			'hero_pill_tag'     => array( 'ป้ายสีส้มในแถบเวอร์ชัน', 'text' ),
			'hero_pill_prefix'  => array( 'คำนำหน้าเลขเวอร์ชัน', 'text' ),
			'hero_mini1_value'  => array( 'ตัวเลขสั้นที่ 1 · ค่า', 'text' ),
			'hero_mini1_label'  => array( 'ตัวเลขสั้นที่ 1 · คำอธิบาย', 'text' ),
			'hero_mini2_value'  => array( 'ตัวเลขสั้นที่ 2 · ค่า', 'text' ),
			'hero_mini2_label'  => array( 'ตัวเลขสั้นที่ 2 · คำอธิบาย', 'text' ),
			'hero_mini3_value'  => array( 'ตัวเลขสั้นที่ 3 · ค่า', 'text' ),
			'hero_mini3_label'  => array( 'ตัวเลขสั้นที่ 3 · คำอธิบาย', 'text' ),
			'hero_status_text'  => array( 'ป้ายลอย · สถานะ (ต่อท้ายชื่อ EA)', 'text' ),
			'hero_toast_title'  => array( 'ป้ายลอย · หัวข้อ', 'text' ),
			'hero_toast_text'   => array( 'ป้ายลอย · บรรทัดรอง', 'text' ),
			'hero_proof_top'    => array( 'ป้ายผลทดสอบ · บรรทัดบน (ต้องบอกช่วงเวลาและเวอร์ชัน)', 'text' ),
			'hero_proof_value'  => array( 'ป้ายผลทดสอบ · ตัวเลข', 'text' ),
			'hero_proof_bottom' => array( 'ป้ายผลทดสอบ · บรรทัดล่าง (ต้องบอกทุนและ Drawdown)', 'text' ),
		),
	);

	$sections['fenix_home_products'] = array(
		'title'       => '20) หน้าแรก · EA ในชุด FENIX PRO',
		'description' => 'รูปปก ชื่อ และเลขเวอร์ชันของการ์ดดึงจากหมวด 12 (หน้าดาวน์โหลด) อัตโนมัติ ที่นี่กรอกเฉพาะหัวข้อและช่องสเปก · รูปแบบช่องสเปก: ป้ายเล็ก | ค่า',
		'fields'      => array(
			'show_products'     => array( 'แสดงส่วนนี้', 'checkbox' ),
			'products_kicker'   => array( 'ป้ายบนหัวข้อ', 'text' ),
			'products_title'    => array( 'หัวข้อ', 'text' ),
			'products_sub'      => array( 'คำโปรย', 'textarea' ),
			'prod_badge_latest' => array( 'คำนำหน้าเวอร์ชันของตัวล่าสุด', 'text' ),
			'prod_badge_other'  => array( 'คำนำหน้าเวอร์ชันของตัวอื่น', 'text' ),
			'prod_plus_spec1'   => array( 'FENIX PLUS · ช่องสเปก 1', 'text' ),
			'prod_plus_spec2'   => array( 'FENIX PLUS · ช่องสเปก 2', 'text' ),
			'prod_fast_spec1'   => array( 'การ์ดใบแรกของหน้าดาวน์โหลด · ช่องสเปก 1', 'text' ),
			'prod_fast_spec2'   => array( 'การ์ดใบแรกของหน้าดาวน์โหลด · ช่องสเปก 2', 'text' ),
			'prod_card3_spec1'  => array( 'การ์ดใบที่สาม · ช่องสเปก 1', 'text' ),
			'prod_card3_spec2'  => array( 'การ์ดใบที่สาม · ช่องสเปก 2', 'text' ),
		),
	);

	$sections['fenix_home_vs'] = array(
		'title'       => '21) หน้าแรก · เทรดเอง vs ใช้ EA',
		'description' => 'ตารางเทียบ บรรทัดละ 1 แถว รูปแบบ: หัวข้อแถว | ฝั่งเทรดเอง | ฝั่งใช้ EA · แถวความเสี่ยงของตลาดอยู่ช่องแยกด้านล่างและจะแสดงต่อท้ายเสมอ',
		'fields'      => array(
			'show_vs'        => array( 'แสดงส่วนนี้', 'checkbox' ),
			'vs_kicker'      => array( 'ป้ายบนหัวข้อ', 'text' ),
			'vs_title'       => array( 'หัวข้อ', 'text' ),
			'vs_sub'         => array( 'คำโปรย', 'textarea' ),
			'vs_left_label'  => array( 'ชื่อฝั่งซ้าย', 'text' ),
			'vs_right_label' => array( 'ชื่อฝั่งขวา', 'text' ),
			'vs_rows'        => array( 'แถวเทียบ (บรรทัดละ 1 แถว)', 'textarea' ),
			'vs_risk_row'    => array( 'แถวความเสี่ยงของตลาด (ห้ามลบ)', 'textarea' ),
		),
	);

	$sections['fenix_home_problem'] = array(
		'title'       => '22) หน้าแรก · ไม้บรรทัด 24 ชั่วโมง และการติ๊ก',
		'description' => 'แถบเวลาของคุณ บรรทัดละ 1 ช่วง รูปแบบ: ความกว้าง | ข้อความ · ใส่ + ไว้หน้าบรรทัดคือช่วง "ดูจอได้" (ช่องสีขาว) · คำในวงเล็บเหลี่ยม [แบบนี้] จะถูกซ่อนบนมือถือ',
		'fields'      => array(
			'pain_kicker'    => array( 'ป้ายบนหัวข้อ', 'text' ),
			'pain_day_title' => array( 'หัวการ์ดไม้บรรทัดเวลา', 'text' ),
			'pain_day_you'   => array( 'แถบเวลาของคุณ', 'textarea' ),
			'pain_day_ea'    => array( 'แถบเวลาของ EA', 'text' ),
			'pain_day_note1' => array( 'คำอธิบายสีขาว', 'text' ),
			'pain_day_note2' => array( 'คำอธิบายสีส้ม', 'text' ),
			'pain_chk_title' => array( 'หัวข้อชวนติ๊ก', 'text' ),
			'pain_chk_zero'  => array( 'ข้อความเมื่อยังไม่ติ๊ก', 'text' ),
			'pain_chk_res'   => array( 'ข้อความสรุป (ใช้ {n} และ {total} แทนตัวเลข)', 'text' ),
			'pain_chk_btn'   => array( 'ปุ่มไปส่วนเทียบ', 'text' ),
		),
	);

	$sections['fenix_home_dash'] = array(
		'title'       => '23) หน้าแรก · Dashboard',
		'description' => 'รายการความสามารถ บรรทัดละ 1 ข้อ รูปแบบ: ชื่อไอคอน | หัวข้อ | คำอธิบาย (ไอคอนที่ใช้ได้ เช่น wallet, gauge, activity, shield, bell, chart, cpu)',
		'fields'      => array(
			'show_dash'   => array( 'แสดงส่วนนี้', 'checkbox' ),
			'dash_kicker' => array( 'ป้ายบนหัวข้อ', 'text' ),
			'dash_title'  => array( 'หัวข้อ', 'text' ),
			'dash_sub'    => array( 'คำโปรย', 'textarea' ),
			'dash_image'  => array( 'ภาพหน้าจอ Dashboard จริง (ใส่แล้วจะใช้ภาพนี้แทนภาพจำลอง)', 'image', 'ต้องปิดชื่อผู้ใช้และเลขบัญชีก่อนอัปโหลด' ),
			'dash_host'   => array( 'ที่อยู่เว็บในแถบเบราว์เซอร์จำลอง', 'text' ),
			'dash_menu'   => array( 'เมนูข้างในภาพจำลอง (บรรทัดละ 1 เมนู)', 'textarea' ),
			'dash_news'   => array( 'แถบตัวกรองข่าว (หัวข้อ | คำอธิบาย)', 'text' ),
			'dash_items'  => array( 'รายการความสามารถ', 'textarea' ),
			'dash_note'   => array( 'หมายเหตุใต้รายการ (ห้ามลบ)', 'textarea' ),
		),
	);

	$sections['fenix_home_ev'] = array(
		'title'       => '24) หน้าแรก · ตัวเลขผลทดสอบ',
		'description' => 'ตัวเลขทุกชุดต้องมีเงื่อนไขกำกับ (ทุน ช่วงเวลา Drawdown) · KPI บรรทัดละ 1 ช่อง รูปแบบ: ป้าย | ค่า | บรรทัดเล็ก · กราฟรายเดือน รูปแบบ: เดือน | ค่า % | ใส่คำว่า sim ถ้าเป็นเดือนที่ราคาเป็นแบบจำลอง',
		'fields'      => array(
			'ev_tab1'        => array( 'ชื่อแท็บที่ 1', 'text' ),
			'ev_tab2'        => array( 'ชื่อแท็บที่ 2', 'text' ),
			'ev_chips'       => array( 'ชิปเงื่อนไข (บรรทัดละ 1 ชิป)', 'textarea' ),
			'ev_big'         => array( 'ตัวเลขใหญ่', 'text' ),
			'ev_sub'         => array( 'บรรทัดอธิบาย (ใช้ {b} แทนตัวเลขตัวหนา)', 'textarea' ),
			'ev_sub_bold'    => array( 'ตัวเลขตัวหนาในบรรทัดอธิบาย', 'text' ),
			'ev_kpis'        => array( 'KPI 3 ช่อง', 'textarea' ),
			'ev_risk_kpis'   => array( 'KPI ความเสี่ยง (ห้ามซ่อน)', 'textarea' ),
			'ev_bars_title'  => array( 'หัวกราฟรายเดือน', 'text' ),
			'ev_bars_unit'   => array( 'หน่วยของกราฟ', 'text' ),
			'ev_bars_note'   => array( 'คำอธิบายพื้นเทา', 'text' ),
			'ev_bars'        => array( 'ข้อมูลกราฟรายเดือน', 'textarea' ),
			'ev_stamp'       => array( 'ข้อความในตราประทับ', 'text' ),
			'ev_fw_text'     => array( 'ข้อความในแท็บ Forward Test', 'textarea' ),
			'perf_conditions' => array( 'เงื่อนไขการทดสอบฉบับเต็ม (ห้ามลบ)', 'textarea' ),
		),
	);

	$sections['fenix_home_rv'] = array(
		'title'       => '25) หน้าแรก · เสียงจากผู้ใช้งาน (ภาพแชต)',
		'description' => 'ใช้ได้เฉพาะภาพแชตจริงที่ผู้ใช้อนุญาต และต้องปิดชื่อ รูปโปรไฟล์ และเลขบัญชีก่อนอัปโหลด · ถ้ายังไม่มีภาพจริง ปิดส่วนนี้ไว้ ห้ามใช้ภาพสมมติ',
		'fields'      => array(
			'show_rv'   => array( 'แสดงส่วนนี้ (เปิดเมื่อมีภาพจริงแล้ว)', 'checkbox' ),
			'rv_kicker' => array( 'ป้ายบนหัวข้อ', 'text' ),
			'rv_sub'    => array( 'คำโปรย', 'textarea' ),
			'rv_group'  => array( 'ชื่อกลุ่มบนหัวจอโทรศัพท์', 'text' ),
			'rv_img1'   => array( 'ภาพแชตที่ 1 (แนวตั้ง 1080 × 1920)', 'image' ),
			'rv_img2'   => array( 'ภาพแชตที่ 2', 'image' ),
			'rv_img3'   => array( 'ภาพแชตที่ 3', 'image' ),
			'rv_note'   => array( 'หมายเหตุใต้ภาพ (ห้ามลบ)', 'textarea' ),
		),
	);

	$sections['fenix_home_misc'] = array(
		'title'       => '26) หน้าแรก · ข้อความประกอบส่วนอื่น',
		'description' => 'ข้อความเล็ก ๆ ในภาพประกอบและหัวข้อย่อยของแต่ละส่วน',
		'fields'      => array(
			'about_kicker'       => array( 'EA คืออะไร · ป้ายบนหัวข้อ', 'text' ),
			'about_bold'         => array( 'EA คืออะไร · ประโยคที่ทำตัวหนา', 'text' ),
			'about_chart_pair'   => array( 'กราฟ · ชื่อคู่เงิน', 'text' ),
			'about_chart_note'   => array( 'กราฟ · หมายเหตุ (ต้องมีคำว่า ภาพประกอบ)', 'text' ),
			'about_chart_run'    => array( 'กราฟ · ป้ายสถานะ', 'text' ),
			'steps_hl'           => array( 'ขั้นตอน · ขั้นที่ต้องการเน้น (1-4)', 'text' ),
			'step1_pic'          => array( 'ขั้น 1 · ข้อความในภาพ', 'text' ),
			'step2_pic'          => array( 'ขั้น 2 · ชื่อไฟล์ในภาพ', 'text' ),
			'step2_pic2'         => array( 'ขั้น 2 · ป้ายผลลัพธ์', 'text' ),
			'step3_pic'          => array( 'ขั้น 3 · คำอธิบายใต้สวิตช์', 'text' ),
			'step4_pic'          => array( 'ขั้น 4 · ป้ายบนแถบเลื่อน', 'text' ),
			'features_kicker'    => array( 'จุดเด่น · ป้ายบนหัวข้อ', 'text' ),
			'feat1_log'          => array( 'จุดเด่น · บันทึกการทำงาน (เวลา | เหตุการณ์ | รายละเอียด)', 'textarea' ),
			'feat4_chips'        => array( 'จุดเด่น · ป้ายใต้มาตรวัด (บรรทัดละ 1 ป้าย)', 'textarea' ),
			'feat5_ring'         => array( 'จุดเด่น · ข้อความในวงแหวน (ตัวเลข | คำอธิบาย)', 'text' ),
			'feat6_lots'         => array( 'จุดเด่น · ป้ายใต้แท่ง (ซ้าย | กลาง | ขวา)', 'text' ),
			'fit_kicker'         => array( 'เหมาะกับใคร · ป้ายบนหัวข้อ', 'text' ),
			'fit_stamp'          => array( 'เหมาะกับใคร · ข้อความในตรา', 'text' ),
			'fit_ask_text'       => array( 'เหมาะกับใคร · แถบชวนทัก', 'textarea' ),
			'fit_ask_btn'        => array( 'เหมาะกับใคร · ปุ่มชวนทัก', 'text' ),
			'dev1_items'         => array( 'อุปกรณ์ 1 · รายการ (+ ทำได้ / ! ต้องระวัง / - ทำไม่ได้)', 'textarea' ),
			'dev2_items'         => array( 'อุปกรณ์ 2 · รายการ', 'textarea' ),
			'dev3_items'         => array( 'อุปกรณ์ 3 · รายการ', 'textarea' ),
			'team_btn1'          => array( 'ทีมงาน · ปุ่มทัก LINE', 'text' ),
			'team_btn2'          => array( 'ทีมงาน · ปุ่มคัดลอกข้อความ', 'text' ),
			'team_chat_head'     => array( 'ทีมงาน · ชื่อบนหัวแชต', 'text' ),
			'team_chat_sub'      => array( 'ทีมงาน · บรรทัดรองบนหัวแชต', 'text' ),
			'team_sample_title'  => array( 'ทีมงาน · หัวข้อความตัวอย่าง', 'text' ),
			'team_sample_lines'  => array( 'ทีมงาน · ข้อความตัวอย่าง (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'team_copy_cap'      => array( 'ทีมงาน · คำอธิบายใต้แชต', 'text' ),
			'team_copy_done'     => array( 'ทีมงาน · ข้อความเมื่อคัดลอกแล้ว', 'text' ),
			'pricing_kicker'     => array( 'แพ็กเกจ · ป้ายบนหัวข้อ', 'text' ),
			'pkg_featured_label' => array( 'แพ็กเกจ · ป้ายของแพ็กเกจแนะนำ', 'text' ),
			'pricing_more'       => array( 'แพ็กเกจ · ลิงก์ดูทั้งหมด', 'text' ),
			'explore_kicker'     => array( 'ดูข้อมูลต่อ · ป้ายบนหัวข้อ', 'text' ),
			'faq_kicker'         => array( 'FAQ · ป้ายบนหัวข้อ', 'text' ),
			'faq_search_ph'      => array( 'FAQ · ข้อความในช่องค้นหา', 'text' ),
			'faq_empty'          => array( 'FAQ · ข้อความเมื่อค้นไม่เจอ', 'text' ),
			'faq_ask_title'      => array( 'FAQ · หัวข้อการ์ดชวนทัก', 'text' ),
			'faq_ask_text'       => array( 'FAQ · คำอธิบายการ์ดชวนทัก', 'text' ),
			'blog_read_more'     => array( 'บทความ · ข้อความลิงก์อ่านต่อ', 'text' ),
			'cta_btn2_text'      => array( 'CTA ปิดท้าย · ปุ่มที่สอง', 'text' ),
			'cta_watermark'      => array( 'CTA ปิดท้าย · ลายน้ำ', 'text' ),
			'risk_link_label'    => array( 'คำเตือน · ข้อความลิงก์อ่านฉบับเต็ม', 'text' ),
		),
	);

	$sections['fenix_go_new'] = array(
		'title'       => '27) หน้า /go/ · ส่วนที่เพิ่มในดีไซน์ใหม่',
		'description' => 'รูปปก ชื่อ และเลขเวอร์ชันของการ์ด EA ยังดึงจากหมวด 12 เหมือนเดิม · ทางลัดกรอกเป็น ชื่อปุ่ม | ปลายทาง | ไอคอน (ปลายทางเป็น #dl #install #vps หรือลิงก์เต็มก็ได้)',
		'fields'      => array(
			'links_cover_wm'       => array( 'ลายน้ำบนแถบส้ม', 'text' ),
			'links_cover_tape'     => array( 'เทปดำมุมขวาบน', 'text' ),
			'links_badges'         => array( 'ชิปใต้ชื่อ (ตัวหนา | ข้อความ บรรทัดละ 1 ชิป)', 'textarea' ),
			'links_btn1_sub'       => array( 'แถวผลเทรดจริง · บรรทัดรอง', 'text' ),
			'links_btn2_sub'       => array( 'แถวแพ็กเกจ · บรรทัดรอง (ตรวจกับหน้า /pricing/)', 'text' ),
			'links_risk'           => array( 'กล่องคำเตือนความเสี่ยง (ห้ามลบ)', 'textarea' ),
			'links_steps_kicker'   => array( 'ป้ายบนหัวข้อขั้นตอน', 'text' ),
			'links_steps_sub'      => array( 'คำโปรยใต้หัวข้อขั้นตอน', 'textarea' ),
			'links_shortcut_label' => array( 'คำนำหน้าแถวทางลัด', 'text' ),
			'links_shortcuts'      => array( 'ปุ่มทางลัด', 'textarea' ),
			'links_dest_prefix'    => array( 'คำนำหน้าบรรทัดบอกปลายทาง', 'text', 'ชื่อเว็บปลายทางดึงจากลิงก์ให้เอง' ),
			'links_device_badge'   => array( 'ป้ายบนช่องอุปกรณ์ของผู้ใช้', 'text' ),
			'links_ea_note'        => array( 'บรรทัดเงื่อนไขใต้การ์ด EA (บรรทัดละ 1 ข้อ)', 'textarea' ),
			'links_algo_label'     => array( 'ภาพจำลอง · ชื่อปุ่ม', 'text' ),
			'links_algo_strong'    => array( 'ภาพจำลอง · ข้อความตัวหนา', 'text' ),
			'links_algo_text'      => array( 'ภาพจำลอง · ข้อความต่อท้าย', 'text' ),
			'links_vps_labels'     => array( 'ชื่อสั้นบนช่อง VPS (บรรทัดละ 1 ชื่อ เรียงตามคู่มือ 2-3-4)', 'textarea' ),
			'links_vps_why'        => array( 'ประโยชน์ของ VPS (บรรทัดละ 1 ข้อ)', 'textarea' ),
		),
	);

	$sections['fenix_pages_b1'] = array(
		'title'       => '28) หน้าภายใน · Backtest · Forward Test · แพ็กเกจ · เกี่ยวกับเรา · บทความ',
		'description' => 'เนื้อหาของหน้า BACKTEST และ เกี่ยวกับเรา ยังแก้ในตัวแก้ไขของหน้านั้นเหมือนเดิม ที่นี่เป็นข้อความประกอบรอบ ๆ',
		'fields'      => array(
			'pages_cta_btn'     => array( 'ปุ่มในกล่อง CTA ส้ม (ทุกหน้า)', 'text' ),
			'post_cta_title'    => array( 'หน้าบทความ · หัวข้อกล่อง CTA', 'text' ),
			'post_cta_text'     => array( 'หน้าบทความ · ข้อความกล่อง CTA', 'textarea' ),
			'forward_kicker'    => array( 'Forward Test · ป้ายบนหัวข้อ', 'text' ),
			'fw_cta_title'      => array( 'Forward Test · หัวข้อกล่อง CTA', 'text' ),
			'fw_cta_text'       => array( 'Forward Test · ข้อความกล่อง CTA', 'textarea' ),
			'packages_kicker'   => array( 'แพ็กเกจ · ป้ายเหนือการ์ด', 'text' ),
			'compare_kicker'    => array( 'แพ็กเกจ · ป้ายเหนือตารางเทียบ', 'text' ),
			'pr_cta_title'      => array( 'แพ็กเกจ · หัวข้อกล่อง CTA', 'text' ),
			'pr_cta_text'       => array( 'แพ็กเกจ · ข้อความกล่อง CTA', 'textarea' ),
			'bt_lot_btn'        => array( 'Backtest · ปุ่มไปหน้าเลือกคูณ LOT', 'text' ),
			'bt_line_btn'       => array( 'Backtest · ปุ่มทักทีมงาน', 'text' ),
			'about_page_kicker' => array( 'เกี่ยวกับเรา · ป้ายบนหัวข้อ', 'text' ),
			'about_page_sub'    => array( 'เกี่ยวกับเรา · คำโปรยใต้หัวข้อ', 'textarea' ),
			'about_banner'      => array( 'เกี่ยวกับเรา · รูปแบนเนอร์', 'image' ),
			'about_banner_alt'  => array( 'เกี่ยวกับเรา · คำอธิบายรูป', 'text' ),
			'blog_page_sub'     => array( 'บทความ · คำโปรยใต้หัวข้อ', 'textarea' ),
			'blog_count_label'  => array( 'บทความ · คำต่อท้ายจำนวนบทความ', 'text' ),
			'blog_more_label'   => array( 'บทความ · ปุ่มโหลดเพิ่ม', 'text' ),
		),
	);

	$priority = 10;

	foreach ( $sections as $section_id => $section ) {

		$wp_customize->add_section(
			$section_id,
			array(
				'panel'       => 'fenix_panel',
				'title'       => $section['title'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'priority'    => $priority,
			)
		);
		$priority += 10;

		foreach ( $section['fields'] as $field_id => $field ) {

			$label = $field[0];
			$type  = $field[1];
			$extra = isset( $field[2] ) ? $field[2] : '';

			switch ( $type ) {
				case 'checkbox':
					$sanitize = 'fenix_sanitize_checkbox';
					break;
				case 'url':
				case 'image':
					$sanitize = 'esc_url_raw';
					break;
				case 'textarea':
					$sanitize = 'sanitize_textarea_field';
					break;
				case 'radio':
					$radio_sanitizers = array(
						'links_download_pos' => 'fenix_sanitize_download_pos',
						'links_version_pos'  => 'fenix_sanitize_version_pos',
						'hero_visual'        => 'fenix_sanitize_hero_visual',
						'hero_deck_featured' => 'fenix_sanitize_deck_slot',
						'hero_deck_cta_mode' => 'fenix_sanitize_deck_cta_mode',
					);
					$sanitize = isset( $radio_sanitizers[ $field_id ] ) ? $radio_sanitizers[ $field_id ] : 'fenix_sanitize_pricing_mode';
					break;
				default:
					$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				$field_id,
				array(
					'default'           => isset( $d[ $field_id ] ) ? $d[ $field_id ] : '',
					'type'              => 'theme_mod',
					'sanitize_callback' => $sanitize,
				)
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$field_id,
						array(
							'label'       => $label,
							'section'     => $section_id,
							'description' => is_string( $extra ) ? $extra : '',
						)
					)
				);
			} elseif ( 'radio' === $type ) {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'   => $label,
						'section' => $section_id,
						'type'    => 'radio',
						'choices' => is_array( $extra ) ? $extra : array(),
					)
				);
			} else {
				$wp_customize->add_control(
					$field_id,
					array(
						'label'       => $label,
						'section'     => $section_id,
						'type'        => $type,
						'description' => is_string( $extra ) ? $extra : '',
					)
				);
			}
		}
	}
}
add_action( 'customize_register', 'fenix_customize_register' );
