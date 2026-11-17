/* ----------------------------------------------------------
 * FENIX PRO EA · หน้ากฎหมาย ชุด 3
 * กล่องสารบัญกางไว้ในโค้ด (บนคอมจึงกางอยู่ตลอดแม้ไม่มี JS)
 * ตอนเปิดหน้าบนมือถือค่อยพับเก็บ แล้วปล่อยให้ผู้อ่านกดเอง
 * ไม่ได้บันทึกอะไร และไม่มีระบบติดตามการอ่าน
 * -------------------------------------------------------- */
(function () {
	'use strict';

	var toc = document.querySelector('.fxl [data-legal-toc]');
	if (!toc) { return; }

	function collapseOnPhone() {
		if (window.matchMedia('(max-width: 760px)').matches) {
			toc.open = false;
		}
	}

	collapseOnPhone();
	/* เผื่อสคริปต์ทำงานก่อนเบราว์เซอร์รู้ความกว้างจริง */
	window.addEventListener('load', collapseOnPhone);
})();
