/**
 * WP Top Bar — fixed / sticky header compatibility.
 *
 * The bar lives in the normal document flow, but many themes pin their header
 * to the viewport with `position: fixed|sticky; top: 0`. Such a header knows
 * nothing about the bar and paints over (or under) it. This script measures how
 * much of the bar is currently visible at the top of the viewport and pushes
 * those pinned headers down by that amount (synchronously in the scroll event,
 * so they never lag a frame behind the bar):
 *
 *   newTop = max(originalTop, barVisibleBottom)
 *
 * The value is also published as the `--wptb-offset` CSS variable on <html>, so
 * themes can use `top: var(--wptb-offset, 0px)` themselves.
 */
(function () {
	'use strict';

	var bar = document.getElementById('wptb-bar');
	if (!bar) {
		return;
	}

	var root = document.documentElement;
	// { el, base, saved: {value, priority}, applied }
	var tracked = [];

	function isBarHidden() {
		return window.getComputedStyle(bar).display === 'none';
	}

	function adminBarHeight() {
		// Counted whatever its position: below 600px WordPress makes the admin bar
		// `absolute`, yet themes still offset their pinned header by its height.
		var admin = document.getElementById('wpadminbar');
		return admin ? admin.offsetHeight : 0;
	}

	function restore(item) {
		if (item.saved.value) {
			item.el.style.setProperty('top', item.saved.value, item.saved.priority);
		} else {
			item.el.style.removeProperty('top');
		}
		item.applied = null;
	}

	/**
	 * Find pinned elements sitting at the very top of the viewport.
	 */
	function scan() {
		var i;
		for (i = 0; i < tracked.length; i++) {
			restore(tracked[i]);
		}
		tracked = [];

		var adminH = adminBarHeight();
		var vw = window.innerWidth;
		var vh = window.innerHeight;
		var nodes = document.body.getElementsByTagName('*');

		for (i = 0; i < nodes.length; i++) {
			var el = nodes[i];

			if (el === bar || bar.contains(el) || el.id === 'wpadminbar') {
				continue;
			}

			var cs = window.getComputedStyle(el);
			if (cs.position !== 'fixed' && cs.position !== 'sticky') {
				continue;
			}

			var base = parseFloat(cs.top);
			if (isNaN(base) || base < 0 || base > adminH + 1) {
				continue;
			}

			var rect = el.getBoundingClientRect();
			// Skip display:none, full-height drawers / overlays and small floaters.
			if (!rect.height || rect.height > vh * 0.5 || rect.width < vw * 0.5) {
				continue;
			}

			if (el.closest('#wpadminbar')) {
				continue;
			}

			tracked.push({
				el: el,
				base: base,
				saved: {
					value: el.style.getPropertyValue('top'),
					priority: el.style.getPropertyPriority('top')
				},
				applied: null
			});
		}
	}

	function update() {
		var bottom = isBarHidden() ? 0 : Math.max(0, bar.getBoundingClientRect().bottom);
		root.style.setProperty('--wptb-offset', Math.round(bottom * 100) / 100 + 'px');

		for (var i = 0; i < tracked.length; i++) {
			var item = tracked[i];
			var top = Math.max(item.base, bottom);
			var next = top > item.base ? top : null;

			if (next === item.applied) {
				continue;
			}

			if (next === null) {
				restore(item);
			} else {
				item.el.style.setProperty('top', next + 'px', 'important');
				item.applied = next;
			}
		}
	}

	function rescan() {
		scan();
		update();
	}

	var resizeTimer;
	function onResize() {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(rescan, 100);
	}

	window.addEventListener('scroll', update, { passive: true });
	window.addEventListener('resize', onResize);
	window.addEventListener('orientationchange', onResize);
	window.addEventListener('load', rescan);
	window.addEventListener('wptb:change', rescan);

	if (typeof window.ResizeObserver === 'function') {
		new window.ResizeObserver(update).observe(bar);
	}

	rescan();
})();
