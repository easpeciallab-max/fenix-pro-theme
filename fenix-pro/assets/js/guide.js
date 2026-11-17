/* ----------------------------------------------------------
 * FENIX PRO EA · หน้าคู่มือ ชุด 2
 * บนมือถือแถบ 8 ขั้นเลื่อนซ้ายขวาได้ ตอนเปิดหน้าให้เลื่อนไว้
 * ให้ขั้นที่กำลังอ่านอยู่กลางจอ ขั้น 6-8 จะได้ไม่ตกขอบ
 * ไม่ได้บันทึกอะไร และไม่มีระบบติ๊กความคืบหน้า
 * -------------------------------------------------------- */
(function () {
	'use strict';

	var wrap = document.querySelector('.fxg .railwrap');
	if (!wrap) { return; }

	var now = wrap.querySelector('.rail li.now');
	if (!now) { return; }

	function center() {
		if (wrap.scrollWidth <= wrap.clientWidth) { return; }
		wrap.scrollLeft = now.offsetLeft - (wrap.clientWidth - now.offsetWidth) / 2;
	}

	center();
	window.addEventListener('resize', center);
})();
