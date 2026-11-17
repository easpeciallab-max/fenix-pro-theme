/**
 * FENIX PRO EA · หน้า /go/
 * ไฮไลต์ช่องอุปกรณ์ที่ตรงกับเครื่องของคนที่เปิดหน้า
 * ตรวจฝั่งผู้ใช้เท่านั้น เพราะหน้านี้ถูกแคชไว้ ตรวจฝั่งเซิร์ฟเวอร์จะได้ผลของคนแรกติดไปทุกคน
 */
(function () {
	'use strict';

	var root = document.querySelector('.fxgo');
	if (!root) { return; }

	var tiles = root.querySelectorAll('[data-device]');
	if (!tiles.length) { return; }

	var ua = navigator.userAgent || '';
	var touch = navigator.maxTouchPoints || 0;
	var mine = '';

	if (/iPhone|iPad|iPod/i.test(ua)) {
		mine = 'ios';
	} else if (/Macintosh/i.test(ua) && touch > 1) {
		/* iPad ตั้งแต่ iPadOS 13 รายงานตัวเองเป็น Mac */
		mine = 'ios';
	} else if (/Android/i.test(ua)) {
		mine = 'android';
	} else if (/Windows NT|Win64|Win32/i.test(ua)) {
		mine = 'win';
	}

	/* เครื่องอื่นหรือตรวจไม่ได้ ไม่ต้องไฮไลต์ */
	if ('' === mine) { return; }

	Array.prototype.forEach.call(tiles, function (tile) {
		if (tile.getAttribute('data-device') !== mine) { return; }

		tile.classList.add('me');

		var label = tile.getAttribute('data-yours');
		if (!label) { return; }

		var tag = document.createElement('span');
		tag.className = 'yours';
		tag.textContent = label;
		tile.appendChild(tag);
	});
})();
