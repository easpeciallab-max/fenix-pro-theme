/* ----------------------------------------------------------
 * FENIX PRO EA · หน้าบทความ ชุด 4
 * กล่องสารบัญกางไว้ในโค้ด (บนคอมจึงกางอยู่ตลอดแม้ไม่มี JS)
 * ตอนเปิดหน้าบนมือถือค่อยพับเก็บ แล้วปล่อยให้ผู้อ่านกดเอง
 * ถ้าจอกว้างข้ามเส้น 760px ทีหลัง ต้องกางกลับให้ด้วย
 * เพราะบนคอมกล่องนี้กดพับไม่ได้
 * ไม่ได้บันทึกอะไร และไม่มีระบบติดตามการอ่าน
 * -------------------------------------------------------- */
(function () {
	'use strict';

	var toc = document.querySelector('.fxa [data-post-toc]');
	if (!toc) { return; }

	var phone = window.matchMedia('(max-width: 760px)');
	var tapped = false;

	function sync() {
		toc.open = !phone.matches;
	}

	/* ผู้อ่านกดเองแล้ว อย่าไปสั่งปิดทับตอนหน้าโหลดเสร็จ */
	var summary = toc.querySelector('summary');
	if (summary) {
		summary.addEventListener('click', function () { tapped = true; });
	}

	sync();

	/* เผื่อสคริปต์ทำงานก่อนเบราว์เซอร์รู้ความกว้างจริง */
	window.addEventListener('load', function () {
		if (!tapped) { sync(); }
	});

	if (phone.addEventListener) {
		phone.addEventListener('change', sync);
	} else if (phone.addListener) {
		phone.addListener(sync);
	}

	/* กันเหนียว เผื่อเบราว์เซอร์ไม่แจ้งตอนข้ามเส้น · สั่งได้อย่างเดียวคือกางบนจอกว้าง */
	window.addEventListener('resize', function () {
		if (!phone.matches && !toc.open) { toc.open = true; }
	});
})();
