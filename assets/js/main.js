/**
 * PENTA SERVE theme scripts. Vanilla JS, no dependencies.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Mobile menu.
	var toggle = document.querySelector('.ps-nav-toggle');
	var nav = document.getElementById('ps-nav');
	if (toggle && nav) {
		var setOpen = function (open) {
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			nav.classList.toggle('is-open', open);
			document.body.classList.toggle('ps-nav-open', open);
		};
		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { setOpen(false); }
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) { setOpen(false); }
		});
		window.addEventListener('resize', function () {
			if (window.innerWidth > 1100) { setOpen(false); }
		});
	}

	// Helper: run a handler at most once per animation frame while scrolling.
	var onFrame = function (fn) {
		var queued = false;
		return function () {
			if (queued) { return; }
			queued = true;
			window.requestAnimationFrame(function () { queued = false; fn(); });
		};
	};

	// Video: the cover image stays visible and one tap plays.
	// Desktop: the player is created on click (autoplay works there).
	// Touch devices block autoplay inside an iframe created by the tap, so the real
	// player is prepared just before the video scrolls into view, hidden under the
	// cover; the tap then sends "play" to it from the user's own gesture.
	var isTouch = window.matchMedia && window.matchMedia('(hover: none)').matches;
	var playerAttrs = function (frame, btn, src) {
		frame.src = src;
		frame.title = btn.getAttribute('data-title') || '';
		frame.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen');
		frame.setAttribute('allowfullscreen', '');
		frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
		return frame;
	};
	Array.prototype.forEach.call(document.querySelectorAll('.ps-video__poster'), function (btn) {
		var autoSrc = btn.getAttribute('data-src') || '';

		if (!isTouch) {
			btn.addEventListener('click', function () {
				if (!btn.parentNode) { return; }
				btn.parentNode.replaceChild(playerAttrs(document.createElement('iframe'), btn, autoSrc), btn);
			});
			return;
		}

		var frame = null;
		var ready = false;
		var prepare = function () {
			if (frame || !btn.parentNode) { return; }
			var sep = autoSrc.indexOf('?') > -1 ? '&' : '?';
			var quietSrc = autoSrc.replace(/&autoplay=1/, '') + sep + 'enablejsapi=1&origin=' + encodeURIComponent(window.location.origin);
			frame = playerAttrs(document.createElement('iframe'), btn, quietSrc);
			frame.addEventListener('load', function () { ready = true; });
			btn.parentNode.insertBefore(frame, btn); // the cover stays on top
		};

		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				if (entries[0].isIntersecting) { io.disconnect(); prepare(); }
			}, { rootMargin: '400px 0px' });
			io.observe(btn);
		} else {
			prepare();
		}

		btn.addEventListener('click', function () {
			if (!btn.parentNode) { return; }
			if (!frame) {
				// Tapped before the player was prepared: fall back to the plain behaviour.
				btn.parentNode.replaceChild(playerAttrs(document.createElement('iframe'), btn, autoSrc), btn);
				return;
			}
			if (ready) {
				try {
					frame.contentWindow.postMessage(JSON.stringify({ event: 'command', func: 'playVideo', args: [] }), '*');
				} catch (e) { /* ignore */ }
			} else {
				frame.src = autoSrc;
			}
			btn.parentNode.removeChild(btn);
		});
	});

	// Header shadow on scroll.
	var header = document.getElementById('ps-header');
	if (header) {
		var onScrollHeader = function () {
			header.classList.toggle('is-stuck', window.scrollY > 8);
		};
		onScrollHeader();
		window.addEventListener('scroll', onFrame(onScrollHeader), { passive: true });
	}

	// Back to top.
	var topBtn = document.getElementById('ps-top');
	if (topBtn) {
		var toggleTop = function () {
			topBtn.classList.toggle('is-visible', window.scrollY > 480);
		};
		toggleTop();
		window.addEventListener('scroll', onFrame(toggleTop), { passive: true });
		topBtn.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
		});
	}

	// Sticky sub-navigation & scrollspy on Services & Investment Opportunities.
	var subnav = document.getElementById('ps-subnav');
	if (subnav) {
		var links = Array.prototype.slice.call(subnav.querySelectorAll('.ps-subnav__link'));
		var sections = links
			.map(function (link) {
				var href = link.getAttribute('href');
				if (!href || href.charAt(0) !== '#') { return null; }
				var target = document.getElementById(href.slice(1));
				return target ? { link: link, target: target } : null;
			})
			.filter(Boolean);

		var getOffset = function () {
			var adminBar = document.getElementById('wpadminbar');
			var h = header ? header.offsetHeight : 78;
			var s = subnav.offsetHeight || 50;
			var a = adminBar ? adminBar.offsetHeight : 0;
			return h + s + a + 16;
		};

		var setActive = function (activeLink) {
			links.forEach(function (l) {
				var isMatch = l === activeLink;
				l.classList.toggle('is-active', isMatch);
				if (isMatch) {
					l.setAttribute('aria-current', 'true');
				} else {
					l.removeAttribute('aria-current');
				}
			});
		};

		links.forEach(function (link) {
			link.addEventListener('click', function (e) {
				var href = link.getAttribute('href');
				if (!href || href.charAt(0) !== '#') { return; }
				var target = document.getElementById(href.slice(1));
				if (!target) { return; }
				e.preventDefault();
				var top = target.getBoundingClientRect().top + window.scrollY - getOffset() + 8;
				window.scrollTo({ top: Math.max(0, top), behavior: reduceMotion ? 'auto' : 'smooth' });
				if (history.replaceState) {
					history.replaceState(null, '', href);
				}
				setActive(link);
			});
		});

		var updateSpy = function () {
			if (!sections.length) { return; }
			var threshold = window.scrollY + getOffset() + 24;
			var current = sections[0].link;
			for (var i = 0; i < sections.length; i++) {
				if (sections[i].target.offsetTop <= threshold) {
					current = sections[i].link;
				}
			}
			setActive(current);
		};

		updateSpy();
		window.addEventListener('scroll', onFrame(updateSpy), { passive: true });
		window.addEventListener('resize', onFrame(updateSpy), { passive: true });
	}

	// Forms: scroll to status alert and prevent double submit.
	['ps-contact-form', 'ps-investment-form'].forEach(function (id) {
		var form = document.getElementById(id);
		if (!form) { return; }
		var alertBox = form.querySelector('.ps-alert');
		if (alertBox) {
			alertBox.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' });
		}
		form.addEventListener('submit', function () {
			var b = form.querySelector('button[type="submit"]');
			if (b) { b.disabled = true; b.style.opacity = '.6'; }
		});
	});
})();