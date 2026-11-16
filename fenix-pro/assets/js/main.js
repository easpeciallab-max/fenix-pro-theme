/**
 * FENIX PRO EA · theme scripts
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
			var link = e.target.closest('a');
			if (link && !link.parentNode.classList.contains('menu-item-has-children')) {
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
			/* บนมือถือหัวข้อนี้เป็นชื่อหมวด เมนูย่อยกางอยู่แล้ว กดแล้วให้ไปหน้านั้นเลย */
			if (window.matchMedia('(max-width: 960px)').matches) {
				return;
			}

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
			var sameSite = itemUrl.host === window.location.host;
			var isMatch = sameSite && (itemPath === '/' ? currentPath === '/' : (currentPath === itemPath || currentPath.indexOf(itemPath + '/') === 0));

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

		var navBar = document.querySelector('.mobile-app-nav');
		var itemList = Array.prototype.slice.call(mobileItems);

		function moveMarker(item) {
			if (!navBar || !item) {
				return;
			}
			var index = itemList.indexOf(item);
			if (index < 0) {
				return;
			}
			navBar.style.setProperty('--nav-i', String(index));
			navBar.classList.add('has-active');
		}

		if (activeItem) {
			activeItem.classList.add('is-active');
			moveMarker(activeItem);
		}

		/* กดปุ่มไหน ขีดเลื่อนไปทันที ไม่ต้องรอหน้าโหลดเสร็จ */
		itemList.forEach(function (item) {
			var sameHost = new URL(item.href, window.location.origin).host === window.location.host;
			if (!sameHost || item.classList.contains('is-action')) {
				return;
			}

			item.addEventListener('pointerdown', function () {
				moveMarker(item);
			});
		});
	}

	/* Analytics · dedicated LINE click event for GA4/Site Kit */
	document.addEventListener('click', function (e) {
		var link = e.target.closest('a[href]');
		if (!link) {
			return;
		}

		var href = link.getAttribute('href') || '';
		var absoluteUrl;
		try {
			absoluteUrl = new URL(href, window.location.href);
		} catch (err) {
			return;
		}

		var host = absoluteUrl.hostname.replace(/^www\./, '').toLowerCase();
		var isLineLink = host === 'line.me' || host === 'lin.ee' || host === 'liff.line.me' || href.indexOf('line://') === 0;
		if (!isLineLink) {
			return;
		}

		var label = (link.textContent || link.getAttribute('aria-label') || 'LINE').replace(/\s+/g, ' ').trim();
		var payload = {
			link_url: absoluteUrl.href,
			link_text: label,
			page_path: window.location.pathname,
			page_title: document.title
		};

		if (typeof window.gtag === 'function') {
			window.gtag('event', 'line_click', payload);
		} else if (Array.isArray(window.dataLayer)) {
			window.dataLayer.push(Object.assign({ event: 'line_click' }, payload));
		}
	});

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

	/* Related posts rail · arrow scroll + show controls only when overflowing */
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

	/* Share · copy link to clipboard */
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

	/* Load more posts (AJAX) */
	var loadMoreBtn = document.querySelector('.load-more-btn');
	if (loadMoreBtn && window.fenixLoadMore) {
		var loadMoreLabel = loadMoreBtn.textContent.trim();
		loadMoreBtn.addEventListener('click', function () {
			var page = parseInt(loadMoreBtn.getAttribute('data-page'), 10) || 1;
			var max = parseInt(loadMoreBtn.getAttribute('data-max'), 10) || 1;
			var next = page + 1;
			if (loadMoreBtn.classList.contains('is-loading') || next > max) {
				return;
			}
			loadMoreBtn.classList.add('is-loading');
			loadMoreBtn.textContent = 'กำลังโหลด…';

			var data = new FormData();
			data.append('action', 'fenix_load_more');
			data.append('nonce', window.fenixLoadMore.nonce);
			data.append('page', next);
			data.append('query', loadMoreBtn.getAttribute('data-query') || '');

			fetch(window.fenixLoadMore.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			})
				.then(function (res) { return res.text(); })
				.then(function (html) {
					var grid = document.querySelector('.posts-grid');
					if (grid && html.trim()) {
						grid.insertAdjacentHTML('beforeend', html);
					}
					loadMoreBtn.setAttribute('data-page', String(next));
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
					if (next >= max) {
						var wrap = loadMoreBtn.closest('.load-more');
						if (wrap) {
							wrap.parentNode.removeChild(wrap);
						}
					}
				})
				.catch(function () {
					loadMoreBtn.classList.remove('is-loading');
					loadMoreBtn.textContent = loadMoreLabel;
				});
		});
	}

	/* Cookie consent + gated tracking (โหลด GA/Pixel เฉพาะหลังยอมรับ; ผู้ที่เคยยอมรับโหลดต่อแม้ปิดแบนเนอร์) */
	var trackingCfg = window.fenixTracking || {};
	var cookieBar = document.querySelector('.cookie-consent');

	if (trackingCfg.ga || trackingCfg.pixel || cookieBar) {
		var getConsent = function () {
			var m = document.cookie.match(/(?:^|;\s*)fenix_consent=([^;]+)/);
			return m ? m[1] : '';
		};
		var setConsent = function (value) {
			var d = new Date();
			d.setTime(d.getTime() + 365 * 24 * 60 * 60 * 1000);
			document.cookie = 'fenix_consent=' + value + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		};
		var loadTrackers = function () {
			if (trackingCfg.ga) {
				var g = document.createElement('script');
				g.async = true;
				g.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(trackingCfg.ga);
				document.head.appendChild(g);
				window.dataLayer = window.dataLayer || [];
				window.gtag = function () { window.dataLayer.push(arguments); };
				window.gtag('js', new Date());
				window.gtag('config', trackingCfg.ga);
			}
			if (trackingCfg.pixel) {
				!function (f, b, e, v, n, t, s) {
					if (f.fbq) return;
					n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
					if (!f._fbq) f._fbq = n;
					n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = [];
					t = b.createElement(e); t.async = !0; t.src = v;
					s = b.getElementsByTagName(e)[0];
					if (s && s.parentNode) { s.parentNode.insertBefore(t, s); } else { (b.head || b.documentElement).appendChild(t); }
				}(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
				window.fbq('init', trackingCfg.pixel);
				window.fbq('track', 'PageView');
			}
		};

		if (getConsent() === 'accepted') {
			loadTrackers();
		}

		if (cookieBar) {
			var sessionDismissed = function () {
				try { return sessionStorage.getItem('fenixConsentDismissed') === '1'; } catch (e) { return false; }
			};
			var setSessionDismissed = function () {
				try { sessionStorage.setItem('fenixConsentDismissed', '1'); } catch (e) {}
			};

			var consent = getConsent();
			if (consent !== 'accepted' && consent !== 'declined' && !sessionDismissed()) {
				cookieBar.classList.add('is-visible');
			}

			var acceptBtn = cookieBar.querySelector('.cookie-accept');
			var declineBtn = cookieBar.querySelector('.cookie-decline');
			var closeBtn = cookieBar.querySelector('.cookie-consent-close');
			if (acceptBtn) {
				acceptBtn.addEventListener('click', function () {
					setConsent('accepted');
					cookieBar.classList.remove('is-visible');
					loadTrackers();
				});
			}
			if (declineBtn) {
				declineBtn.addEventListener('click', function () {
					setConsent('declined');
					cookieBar.classList.remove('is-visible');
				});
			}
			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					setSessionDismissed();
					cookieBar.classList.remove('is-visible');
				});
			}
		}
	}
})();


/* ----------------------------------------------------------
 * สำรับการ์ดปก EA ในหน้าแรก
 * ใบบนสุดสไลด์ออกไปอยู่ท้ายสำรับทุก 5 วินาที · ปัดเองได้ · กดจุดเพื่อข้ามได้
 * -------------------------------------------------------- */
(function () {
	var deck = document.querySelector('[data-deck]');
	if (!deck) { return; }

	var stack = deck.querySelector('.hero-deck-stack');
	var cards = Array.prototype.slice.call(deck.querySelectorAll('.deck-card'));
	if (!stack || cards.length < 2) { return; }

	var nameEl = deck.querySelector('.deck-name');
	var verEl = deck.querySelector('.deck-version');
	var cta = deck.querySelector('.deck-cta');
	var dots = Array.prototype.slice.call(deck.querySelectorAll('.deck-dot'));
	var order = cards.slice();
	var still = window.matchMedia('(prefers-reduced-motion: reduce)');
	var timer = null;
	var busy = false;
	var held = false;
	var seen = true;

	function paint() {
		order.forEach(function (card, i) {
			card.className = 'deck-card is-' + Math.min(i + 1, 3);
		});

		var top = order[0];

		if (nameEl) { nameEl.textContent = top.getAttribute('data-name') || ''; }

		if (verEl) {
			var version = top.getAttribute('data-version') || '';
			verEl.textContent = version ? 'เวอร์ชัน ' + version : '';
			verEl.hidden = !version;
		}

		if (cta && top.getAttribute('data-url')) { cta.setAttribute('href', top.getAttribute('data-url')); }

		var active = cards.indexOf(top);
		dots.forEach(function (dot, i) {
			dot.classList.toggle('is-on', i === active);
		});
	}

	function advance() {
		if (busy) { return; }
		busy = true;

		var top = order[0];
		top.classList.add('is-out');

		window.setTimeout(function () {
			/* ย้ายใบที่ปลิวออกไปไว้ท้ายสำรับแบบไม่ให้เห็นมันวิ่งกลับ */
			top.style.transition = 'none';
			order.push(order.shift());
			paint();
			void top.offsetWidth;
			top.style.transition = '';
			busy = false;
		}, still.matches ? 0 : 420);
	}

	function back() {
		if (busy) { return; }
		busy = true;

		/* เอาใบท้ายสำรับไปตั้งไว้นอกจอซ้ายก่อน แล้วค่อยปล่อยให้มันไหลกลับเข้ามา */
		var last = order[order.length - 1];
		last.style.transition = 'none';
		last.className = 'deck-card is-out';
		void last.offsetWidth;
		last.style.transition = '';

		order.unshift(order.pop());
		paint();

		window.setTimeout(function () { busy = false; }, still.matches ? 0 : 420);
	}

	function goTo(index) {
		var guard = 0;
		while (cards.indexOf(order[0]) !== index && guard < cards.length) {
			order.push(order.shift());
			guard += 1;
		}
		paint();
	}

	function stop() {
		if (timer) { window.clearInterval(timer); timer = null; }
	}

	function start() {
		stop();
		if (still.matches || held || !seen || document.hidden) { return; }
		timer = window.setInterval(advance, 5000);
	}

	stack.addEventListener('mouseenter', function () { held = true; stop(); });
	stack.addEventListener('mouseleave', function () { held = false; start(); });
	deck.addEventListener('focusin', function () { held = true; stop(); });
	deck.addEventListener('focusout', function () { held = false; start(); });
	document.addEventListener('visibilitychange', start);

	dots.forEach(function (dot, i) {
		dot.addEventListener('click', function () {
			goTo(i);
			start();
		});
	});

	/* ----- ลากเปลี่ยนการ์ดเองได้ ไม่ต้องรอเวลา ----- */
	var startX = 0;
	var moved = 0;
	var dragging = false;

	function grip(on) {
		dragging = on;
		stack.classList.toggle('is-dragging', on);
	}

	/* เบราว์เซอร์จะชิงเอาการลากไปทำ drag-and-drop ของมันเอง ต้องห้ามไว้ก่อน */
	stack.addEventListener('dragstart', function (e) { e.preventDefault(); });

	stack.addEventListener('pointerdown', function (e) {
		if (busy || e.button > 0) { return; }
		startX = e.clientX;
		moved = 0;
		grip(true);
		stop();
		if (stack.setPointerCapture) {
			try { stack.setPointerCapture(e.pointerId); } catch (err) { /* เบราว์เซอร์บางตัวไม่ยอม ก็ลากได้ตามปกติ */ }
		}
	});

	function onMove(e) {
		if (!dragging) { return; }
		moved = e.clientX - startX;

		/* ใบบนสุดวิ่งตามมือไปเลย จะได้รู้ว่ามันลากได้ */
		var top = order[0];
		top.style.transition = 'none';
		top.style.transform = 'translateX(' + moved + 'px) rotate(' + (moved / 26) + 'deg)';
	}

	function release(e) {
		if (!dragging) { return; }
		grip(false);

		var top = order[0];
		top.style.transition = '';
		top.style.transform = '';

		if (moved < -50) {
			advance();
		} else if (moved > 50) {
			back();
		}

		start();
	}

	/* ฟังที่หน้าต่างด้วย เผื่อเมาส์ถูกลากออกนอกกรอบการ์ดก่อนปล่อย */
	stack.addEventListener('pointermove', onMove);
	window.addEventListener('pointermove', onMove);
	stack.addEventListener('pointerup', release);
	window.addEventListener('pointerup', release);
	stack.addEventListener('pointercancel', release);
	window.addEventListener('pointercancel', release);
	stack.addEventListener('lostpointercapture', release);

	/* ล้อเมาส์แนวนอนหรือแทร็กแพด ก็เปลี่ยนใบได้ (ไม่แตะการเลื่อนหน้าแนวตั้ง) */
	var wheelAt = 0;
	stack.addEventListener('wheel', function (e) {
		if (Math.abs(e.deltaX) < 12 || Math.abs(e.deltaX) <= Math.abs(e.deltaY)) { return; }
		var now = Date.now();
		if (now - wheelAt < 500) { return; }
		wheelAt = now;
		e.preventDefault();
		if (e.deltaX > 0) { advance(); } else { back(); }
		start();
	}, { passive: false });

	/* ปุ่มลูกศรซ้ายขวา ตอนโฟกัสอยู่ในสำรับ */
	deck.addEventListener('keydown', function (e) {
		if ('ArrowRight' === e.key) { e.preventDefault(); advance(); start(); }
		if ('ArrowLeft' === e.key) { e.preventDefault(); back(); start(); }
	});

	stack.addEventListener('click', function (e) {
		var card = e.target.closest('.deck-card');
		if (!card) { return; }
		if (Math.abs(moved) > 10 || card !== order[0]) {
			e.preventDefault();
			if (card !== order[0] && Math.abs(moved) <= 10) { goTo(cards.indexOf(card)); }
			start();
		}
	});

	if ('IntersectionObserver' in window) {
		new IntersectionObserver(function (entries) {
			seen = entries[0].isIntersecting;
			start();
		}, { threshold: 0.2 }).observe(stack);
	}

	paint();
	start();
})();

/* ----------------------------------------------------------
 * สารบัญในหน้าคู่มือ · สร้างจากหัวข้อในเนื้อหาเอง
 * คู่มือที่มีหัวข้อตั้งแต่ 4 อันขึ้นไปถึงจะแสดง หน้าสั้น ๆ ไม่ต้องมี
 * -------------------------------------------------------- */
(function () {
	var box = document.querySelector('[data-guide-toc]');
	var body = document.querySelector('.guide-content');
	if (!box || !body) { return; }

	var list = box.querySelector('ol');
	var heads = Array.prototype.slice.call(body.querySelectorAll('h2')).filter(function (h) {
		return h.textContent.trim() !== '';
	});

	if (heads.length < 4) { return; }

	heads.forEach(function (h, i) {
		if (!h.id) { h.id = 'guide-step-' + (i + 1); }

		var li = document.createElement('li');
		var a = document.createElement('a');
		a.href = '#' + h.id;
		a.textContent = h.textContent.trim();
		li.appendChild(a);
		list.appendChild(li);
	});

	box.hidden = false;

	/* ไฮไลต์หัวข้อที่กำลังอ่านอยู่ */
	if (!('IntersectionObserver' in window)) { return; }

	var items = Array.prototype.slice.call(list.children);
	var spy = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) { return; }
			var at = heads.indexOf(entry.target);
			items.forEach(function (li, i) {
				li.classList.toggle('is-reading', i === at);
			});
		});
	}, { rootMargin: '-80px 0px -70% 0px' });

	heads.forEach(function (h) { spy.observe(h); });
})();