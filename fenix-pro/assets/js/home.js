/**
 * FENIX PRO EA · สคริปต์ของหน้าแรก (โหลดเฉพาะหน้าแรก)
 * ส่วนที่ต้องกดได้จริง: ติ๊กข้อปัญหา · แท็บผลทดสอบ · ตัวเลขพลิก ·
 * แท่งกราฟรายเดือน · คำถามที่พบบ่อย (ค้นหา + กางทีละข้อ) · คัดลอกข้อความตัวอย่าง
 */
(function () {
	'use strict';

	var root = document.querySelector('.fxh');
	if (!root) { return; }

	var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------- โผล่ขึ้นมาเบา ๆ ตอนเลื่อนถึง ---------- */
	var ups = root.querySelectorAll('.up');
	if (calm || !('IntersectionObserver' in window)) {
		Array.prototype.forEach.call(ups, function (el) { el.classList.add('in'); });
	} else {
		var upSpy = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				entry.target.classList.add('in');
				upSpy.unobserve(entry.target);
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
		Array.prototype.forEach.call(ups, function (el) { upSpy.observe(el); });
	}

	/* เรียกฟังก์ชันครั้งเดียวตอนเลื่อนมาถึงกล่องนั้น */
	function once(el, run) {
		if (!el) { return; }
		if (calm || !('IntersectionObserver' in window)) { run(); return; }
		var spy = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				spy.unobserve(entry.target);
				run();
			});
		}, { threshold: 0.3 });
		spy.observe(el);
	}

	/* ---------- รูป 04 · ติ๊กข้อที่ตรงกับคุณ ---------- */
	(function () {
		var box = root.querySelector('[data-chk]');
		if (!box) { return; }

		var items = box.querySelectorAll('[data-chk-item]');
		var out = root.querySelector('[data-chk-out]');
		if (!items.length || !out) { return; }

		var tpl = out.getAttribute('data-tpl') || '';
		var zero = out.getAttribute('data-zero') || '';
		var total = items.length;

		function paint() {
			var n = 0;
			Array.prototype.forEach.call(items, function (el) {
				if ('true' === el.getAttribute('aria-pressed')) { n += 1; }
			});
			if (0 === n) {
				out.textContent = zero;
				return;
			}
			out.innerHTML = tpl
				.replace('{n}', '<b>' + n + '</b>')
				.replace('{total}', '<b>' + total + '</b>');
		}

		Array.prototype.forEach.call(items, function (el) {
			el.addEventListener('click', function () {
				el.setAttribute('aria-pressed', 'true' === el.getAttribute('aria-pressed') ? 'false' : 'true');
				paint();
			});
		});

		paint();
	})();

	/* ---------- รูป 10 · แท็บ BACKTEST / FORWARD TEST ---------- */
	(function () {
		var wrap = root.querySelector('[data-tabs]');
		if (!wrap) { return; }

		var buttons = wrap.querySelectorAll('[data-tab]');
		Array.prototype.forEach.call(buttons, function (btn) {
			btn.addEventListener('click', function () {
				var name = btn.getAttribute('data-tab');
				Array.prototype.forEach.call(buttons, function (other) {
					other.setAttribute('aria-selected', other === btn ? 'true' : 'false');
				});
				Array.prototype.forEach.call(root.querySelectorAll('[data-panel]'), function (panel) {
					panel.hidden = panel.getAttribute('data-panel') !== name;
				});
			});
		});
	})();

	/* ---------- รูป 10 · ตัวเลขพลิกทีละหลัก ---------- */
	(function () {
		var odo = root.querySelector('[data-odo]');
		if (!odo) { return; }

		var rolls = odo.querySelectorAll('[data-digit]');
		if (!rolls.length) { return; }

		once(odo, function () {
			Array.prototype.forEach.call(rolls, function (roll, i) {
				var target = parseInt(roll.getAttribute('data-digit'), 10) || 0;
				var move = function () {
					roll.style.transform = 'translateY(-' + (target * 10) + '%)';
				};
				if (calm) { move(); return; }
				window.setTimeout(move, 90 * i);
			});
		});
	})();

	/* ---------- รูป 10 · แท่งกำไรรายเดือน ---------- */
	(function () {
		var bars = root.querySelector('[data-bars]');
		if (!bars) { return; }

		var fills = bars.querySelectorAll('[data-h]');
		if (!fills.length) { return; }

		once(bars, function () {
			Array.prototype.forEach.call(fills, function (fill, i) {
				var h = fill.getAttribute('data-h');
				var grow = function () { fill.style.height = h; };
				if (calm) { grow(); return; }
				window.setTimeout(grow, 60 * i);
			});
		});
	})();

	/* ---------- รูป 16 · คำถามที่พบบ่อย ---------- */
	(function () {
		var list = root.querySelector('[data-faq]');
		if (!list) { return; }

		var cards = list.querySelectorAll('.qa');
		var search = root.querySelector('[data-faq-search]');
		var empty = root.querySelector('[data-faq-empty]');

		Array.prototype.forEach.call(cards, function (card) {
			var btn = card.querySelector('.q');
			if (!btn) { return; }
			btn.addEventListener('click', function () {
				var open = card.classList.contains('open');
				Array.prototype.forEach.call(cards, function (other) {
					other.classList.remove('open');
					var b = other.querySelector('.q');
					if (b) { b.setAttribute('aria-expanded', 'false'); }
				});
				if (!open) {
					card.classList.add('open');
					btn.setAttribute('aria-expanded', 'true');
				}
			});
		});

		if (!search) { return; }

		search.addEventListener('input', function () {
			var q = search.value.trim().toLowerCase();
			var shown = 0;
			Array.prototype.forEach.call(cards, function (card) {
				var hay = (card.textContent || '').toLowerCase();
				var hit = '' === q || hay.indexOf(q) > -1;
				card.hidden = !hit;
				if (hit) { shown += 1; }
			});
			if (empty) { empty.hidden = 0 !== shown; }
		});
	})();

	/* ---------- รูป 13 · คัดลอกข้อความตัวอย่าง ---------- */
	(function () {
		var btn = root.querySelector('[data-copy]');
		if (!btn) { return; }

		var src = root.querySelector('[data-copy-src]');
		var cap = root.querySelector('[data-copy-cap]');
		if (!src) { return; }

		var label = btn.querySelector('span');
		var done = btn.getAttribute('data-done') || 'คัดลอกแล้ว';
		var was = label ? label.textContent : '';

		btn.addEventListener('click', function () {
			var text = (src.innerText || src.textContent || '').replace(/\n{2,}/g, '\n').trim();

			var ok = function () {
				if (label) { label.textContent = done; }
				if (cap) { cap.textContent = done; }
				window.setTimeout(function () {
					if (label) { label.textContent = was; }
					if (cap) { cap.textContent = cap.getAttribute('data-was') || ''; }
				}, 2200);
			};

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(ok, function () {});
				return;
			}

			var ta = document.createElement('textarea');
			ta.value = text;
			ta.setAttribute('readonly', 'readonly');
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			try { document.execCommand('copy'); ok(); } catch (e) {}
			document.body.removeChild(ta);
		});
	})();
})();
