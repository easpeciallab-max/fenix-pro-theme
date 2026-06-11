/**
 * FENIX PRO EA — theme scripts
 */
(function () {
	'use strict';

	/* Header scrolled state */
	var header = document.querySelector('.site-header');
	function onScroll() {
		if (header) {
			header.classList.toggle('scrolled', window.scrollY > 10);
		}
	}
	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });

	/* Mobile navigation */
	var toggle = document.querySelector('.nav-toggle');
	var nav = document.getElementById('site-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = document.body.classList.toggle('nav-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && document.body.classList.contains('nav-open')) {
				document.body.classList.remove('nav-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* Reveal on scroll */
	var items = document.querySelectorAll('.reveal');
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if ('IntersectionObserver' in window && !reduced) {
		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('in');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
		);
		items.forEach(function (el) {
			io.observe(el);
		});
	} else {
		items.forEach(function (el) {
			el.classList.add('in');
		});
	}
})();
