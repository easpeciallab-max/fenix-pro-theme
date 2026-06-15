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

	/* Parent menu items with a submenu toggle the dropdown instead of navigating */
	var parentLinks = document.querySelectorAll('.nav-list .menu-item-has-children > a');
	parentLinks.forEach(function (link) {
		link.setAttribute('aria-haspopup', 'true');
		link.setAttribute('aria-expanded', 'false');
		link.addEventListener('click', function (e) {
			e.preventDefault();
			var li = link.parentNode;
			var willOpen = !li.classList.contains('is-open');

			var siblings = li.parentNode.querySelectorAll('.menu-item-has-children.is-open');
			siblings.forEach(function (other) {
				if (other !== li) {
					other.classList.remove('is-open');
					var otherLink = other.querySelector(':scope > a');
					if (otherLink) {
						otherLink.setAttribute('aria-expanded', 'false');
					}
				}
			});

			li.classList.toggle('is-open', willOpen);
			link.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
		});
	});

	document.addEventListener('click', function (e) {
		if (e.target.closest('.nav-list .menu-item-has-children')) {
			return;
		}
		document.querySelectorAll('.nav-list .menu-item-has-children.is-open').forEach(function (li) {
			li.classList.remove('is-open');
			var openLink = li.querySelector(':scope > a');
			if (openLink) {
				openLink.setAttribute('aria-expanded', 'false');
			}
		});
	});

	/* Mobile app-style bottom navigation */
	var mobileNav = document.querySelector('.mobile-app-nav');
	if (mobileNav) {
		document.body.classList.add('has-mobile-app-nav');

		var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
		var activeItem = null;
		var activeLength = 0;
		var mobileItems = mobileNav.querySelectorAll('.mobile-app-nav-item');

		mobileItems.forEach(function (item) {
			var itemUrl = new URL(item.href, window.location.origin);
			var itemPath = itemUrl.pathname.replace(/\/+$/, '') || '/';
			var isMatch = itemPath === '/' ? currentPath === '/' : currentPath.indexOf(itemPath) === 0;

			if (!item.classList.contains('is-action') && isMatch && itemPath.length >= activeLength) {
				activeItem = item;
				activeLength = itemPath.length;
			}

			item.addEventListener('pointerdown', function () {
				item.classList.add('is-pressing');
			});

			item.addEventListener('pointerup', function () {
				item.classList.remove('is-pressing');
			});

			item.addEventListener('pointerleave', function () {
				item.classList.remove('is-pressing');
			});
		});

		if (activeItem) {
			activeItem.classList.add('is-active');
		}
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

	/* Related posts rail — arrow scroll + show controls only when overflowing */
	document.querySelectorAll('.related-section').forEach(function (section) {
		var rail = section.querySelector('.related-rail');
		if (!rail) {
			return;
		}

		function updateControls() {
			var scrollable = rail.scrollWidth > rail.clientWidth + 4;
			section.classList.toggle('is-scrollable', scrollable);
		}

		section.querySelectorAll('.rail-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var dir = parseInt(btn.getAttribute('data-dir'), 10) || 1;
				var card = rail.querySelector('.post-card');
				var step = card ? card.offsetWidth + 18 : rail.clientWidth * 0.8;
				rail.scrollBy({ left: dir * step, behavior: 'smooth' });
			});
		});

		updateControls();
		window.addEventListener('resize', updateControls);
	});

	/* Share — copy link to clipboard */
	document.querySelectorAll('.share-copy').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-url') || window.location.href;
			var done = function () {
				btn.classList.add('is-copied');
				setTimeout(function () {
					btn.classList.remove('is-copied');
				}, 1600);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {});
			} else {
				var ta = document.createElement('textarea');
				ta.value = url;
				ta.setAttribute('readonly', '');
				ta.style.position = 'absolute';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				try {
					document.execCommand('copy');
					done();
				} catch (e) {}
				document.body.removeChild(ta);
			}
		});
	});

	/* Reading progress bar */
	var progress = document.querySelector('.reading-progress span');
	var progressArticle = document.querySelector('.single-article');
	if (progress && progressArticle) {
		var onProgress = function () {
			var total = progressArticle.offsetHeight - window.innerHeight;
			var scrolled = Math.min(Math.max(-progressArticle.getBoundingClientRect().top, 0), Math.max(total, 0));
			progress.style.width = (total > 0 ? (scrolled / total) * 100 : 0) + '%';
		};
		window.addEventListener('scroll', onProgress, { passive: true });
		window.addEventListener('resize', onProgress);
		onProgress();
	}
})();
