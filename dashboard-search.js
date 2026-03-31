(function () {
	'use strict';

	if (typeof window === 'undefined') {
		return;
	}

	var TCT = window.TCT || {};
	var state = (window.tctDashboardQuickSearchState = window.tctDashboardQuickSearchState || {
		query: '',
		lastRequestId: 0,
		outsideListenerAdded: false,
		completionListenerAdded: false,
		toastTimer: null
	});

	function getAjaxUrl() {
		if (window.tctDashboard && window.tctDashboard.ajaxUrl) {
			return window.tctDashboard.ajaxUrl;
		}
		// Fallback for some WP setups
		if (typeof window.ajaxurl !== 'undefined') {
			return window.ajaxurl;
		}
		return null;
	}

	function getSearchNonce() {
		return window.tctDashboard && window.tctDashboard.mobileSearchNonce
			? window.tctDashboard.mobileSearchNonce
			: null;
	}

	function ensureToastEl() {
		var existing = document.querySelector('.tct-toast.tct-dashboard-search-toast');
		if (existing) {
			return existing;
		}
		var el = document.createElement('div');
		el.className = 'tct-toast tct-dashboard-search-toast';
		el.setAttribute('role', 'status');
		el.style.display = 'none';
		document.body.appendChild(el);
		return el;
	}

	function showToast(message, isError) {
		try {
			var el = ensureToastEl();
			el.textContent = String(message || '');
			el.classList.toggle('tct-toast-error', !!isError);
			el.style.display = 'block';
			if (state.toastTimer) {
				clearTimeout(state.toastTimer);
			}
			state.toastTimer = setTimeout(function () {
				el.style.display = 'none';
			}, 2500);
		} catch (e) {
			// As a last resort.
			try {
				console.warn(e);
			} catch (err) {}
		}
	}

	function debounce(fn, waitMs) {
		if (TCT && typeof TCT.debounce === 'function') {
			return TCT.debounce(fn, waitMs);
		}
		var t = null;
		return function () {
			var ctx = this;
			var args = arguments;
			clearTimeout(t);
			t = setTimeout(function () {
				fn.apply(ctx, args);
			}, waitMs);
		};
	}

	function setHidden(el, hidden) {
		if (!el) {
			return;
		}
		if (hidden) {
			el.setAttribute('hidden', 'hidden');
		} else {
			el.removeAttribute('hidden');
		}
	}

	function getExpansionScope(rowEl, fallbackScope) {
		if (!rowEl) {
			return fallbackScope || null;
		}
		try {
			var nestedScope = rowEl.closest('[data-tct-mobile-composite-children]');
			if (nestedScope) {
				return nestedScope;
			}
		} catch (e) {}
		return fallbackScope || null;
	}

	function toggleRowExpanded(rowEl, dropdownEl) {
		if (!rowEl || !dropdownEl) {
			return;
		}

		var scopeEl = getExpansionScope(rowEl, dropdownEl);
		var isExpanded = rowEl.classList.contains('tct-mobile-result-expanded');

		try {
			scopeEl
				.querySelectorAll('.tct-mobile-result.tct-mobile-result-expanded')
				.forEach(function (el) {
					if (el === rowEl) {
						return;
					}
					if (getExpansionScope(el, dropdownEl) === scopeEl) {
						el.classList.remove('tct-mobile-result-expanded');
					}
				});
		} catch (e) {}

		if (isExpanded) {
			rowEl.classList.remove('tct-mobile-result-expanded');
		} else {
			rowEl.classList.add('tct-mobile-result-expanded');
		}
	}

	function clickPrimaryActionInsideRow(rowEl) {
		if (!rowEl) {
			return false;
		}

		// Prefer the normal "Complete" action.
		var btn = rowEl.querySelector('.tct-mobile-result-body [data-tct-complete-goal]');
		if (btn) {
			btn.click();
			return true;
		}

		// Timer goals: start timer.
		btn = rowEl.querySelector('.tct-mobile-result-body [data-tct-start-timer]');
		if (btn) {
			btn.click();
			return true;
		}

		// Sleep tracking: use the primary button if present.
		btn = rowEl.querySelector('.tct-mobile-result-body .tct-sleep-primary-btn');
		if (btn) {
			btn.click();
			return true;
		}

		return false;
	}

	function getPointsTextFromQuickBtn(btnEl) {
		if (!btnEl) {
			return '';
		}
		try {
			var txtEl = btnEl.querySelector('.tct-mobile-row-complete-text');
			var txt = txtEl ? String(txtEl.textContent || '') : String(btnEl.textContent || '');
			return String(txt || '').trim();
		} catch (e) {
			return '';
		}
	}

	function markRowPending(rowEl, quickBtnEl) {
		if (!rowEl) {
			return;
		}
		try {
			rowEl.setAttribute('data-tct-dash-search-pending', '1');
			var pts = getPointsTextFromQuickBtn(quickBtnEl);
			if (pts) {
				rowEl.setAttribute('data-tct-dash-search-points', pts);
			} else {
				rowEl.removeAttribute('data-tct-dash-search-points');
			}

			// Safety: clear pending if we never get a completion event.
			if (rowEl.__tctDashSearchPendingTimer) {
				clearTimeout(rowEl.__tctDashSearchPendingTimer);
			}
			rowEl.__tctDashSearchPendingTimer = setTimeout(function () {
				try {
					rowEl.removeAttribute('data-tct-dash-search-pending');
					rowEl.removeAttribute('data-tct-dash-search-points');
				} catch (e2) {}
			}, 8000);
		} catch (e) {}
	}

	function clearRowPending(rowEl) {
		if (!rowEl) {
			return;
		}
		try {
			rowEl.removeAttribute('data-tct-dash-search-pending');
			rowEl.removeAttribute('data-tct-dash-search-points');
			if (rowEl.__tctDashSearchPendingTimer) {
				clearTimeout(rowEl.__tctDashSearchPendingTimer);
				rowEl.__tctDashSearchPendingTimer = null;
			}
		} catch (e) {}
	}

	function updateRowLastDone(rowEl, lastCompletedText) {
		if (!rowEl) {
			return;
		}
		var el = rowEl.querySelector('.tct-mobile-result-last-done');
		if (!el) {
			return;
		}

		var txt = String(lastCompletedText || '').trim();
		if (!txt) {
			return;
		}

		// Pull the prefix from the embedded goal tile so negative goals remain correct (Enjoyed / Fell short / etc).
		var prefix = 'Completed';
		try {
			var prefixEl = rowEl.querySelector('.tct-mobile-result-body [data-tct-goal-last-prefix]');
			if (prefixEl) {
				var p = String(prefixEl.textContent || '').trim();
				if (p) {
					prefix = p;
				}
			}
		} catch (e) {}

		try {
			el.classList.remove('tct-mobile-result-last-never');
			el.classList.remove('tct-mobile-result-last-recent');
		} catch (e2) {}

		if (txt === 'never') {
			el.textContent = 'Never completed';
			try {
				el.classList.add('tct-mobile-result-last-never');
			} catch (e3) {}
			return;
		}

		if (txt === 'just now') {
			el.textContent = prefix + ' just now';
			try {
				el.classList.add('tct-mobile-result-last-recent');
			} catch (e4) {}
			return;
		}

		el.textContent = prefix + ' ' + txt;
	}

	function showPointsFloat(quickBtnEl, pointsText) {
		pointsText = String(pointsText || '').trim();
		if (!quickBtnEl || !pointsText) {
			return;
		}

		var rect;
		try {
			rect = quickBtnEl.getBoundingClientRect();
		} catch (e) {
			rect = null;
		}
		if (!rect || rect.width === 0 || rect.height === 0) {
			return;
		}

		var floatEl = document.createElement('div');
		floatEl.className = 'tct-dashboard-search-points-float';
		floatEl.textContent = pointsText;

		// Simple positive/negative styling based on sign.
		if (pointsText.charAt(0) === '-') {
			floatEl.classList.add('tct-dashboard-search-points-float-negative');
		} else {
			floatEl.classList.add('tct-dashboard-search-points-float-positive');
		}

		floatEl.style.left = rect.left + rect.width / 2 + 'px';
		floatEl.style.top = rect.top + rect.height / 2 + 'px';

		document.body.appendChild(floatEl);

		// Cleanup after animation.
		var done = function () {
			try {
				if (floatEl && floatEl.parentNode) {
					floatEl.parentNode.removeChild(floatEl);
				}
			} catch (e2) {}
		};
		floatEl.addEventListener('animationend', done);
		setTimeout(done, 1200);
	}

	function handleRowQuickComplete(btnEl) {
		if (!btnEl) {
			return;
		}

		var dueEnabled = btnEl.getAttribute('data-tct-due-enabled');
		var dueToday = btnEl.getAttribute('data-tct-due-today');
		if (dueEnabled === '1' && dueToday !== '1' && btnEl.getAttribute('data-tct-availability-paused') !== '1') {
			showToast('Not due today', true);
			return;
		}

		var rowEl = btnEl.closest('.tct-mobile-result');
		if (!rowEl) {
			return;
		}

		// Mark pending so we can show a visible confirmation when the completion event returns.
		markRowPending(rowEl, btnEl);

		var ok = clickPrimaryActionInsideRow(rowEl);
		if (!ok) {
			clearRowPending(rowEl);
			showToast('Unable to quick complete this goal.', true);
		}
	}

	function performSearch(query, dropdownEl) {
		var ajaxUrl = getAjaxUrl();
		var nonce = getSearchNonce();
		if (!ajaxUrl || !nonce) {
			showToast('Search is not available (missing nonce).', true);
			setHidden(dropdownEl, true);
			return;
		}

		var requestId = ++state.lastRequestId;
		var fd = new FormData();
		fd.append('action', 'tct_mobile_search');
		fd.append('nonce', nonce);
		fd.append('query', query);

		fetch(ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: fd
		})
			.then(function (r) {
				return r.json();
			})
			.then(function (json) {
				if (requestId !== state.lastRequestId) {
					return;
				}

				var norm =
					window.TCT && typeof window.TCT.normalizeResponse === 'function'
						? window.TCT.normalizeResponse(json)
						: {
							ok: !!(json && json.success),
							data: json && json.data
						};

				if (!norm.ok) {
					var msg =
						window.TCT && typeof window.TCT.getErrorMessage === 'function'
							? window.TCT.getErrorMessage(json, 'Search failed.')
							: 'Search failed.';
					throw new Error(msg);
				}

				var html = norm.data && typeof norm.data.html === 'string' ? norm.data.html : '';

				if (html && html.trim()) {
					dropdownEl.innerHTML = '<div class="tct-dashboard-search-results">' + html + '</div>';
					dropdownEl.scrollTop = 0;
					setHidden(dropdownEl, false);

					// Bind the same goal tile behaviors as the dashboard (complete/undo/etc).
					var resultsRoot = dropdownEl.querySelector('.tct-dashboard-search-results');
					if (resultsRoot && typeof window.tctDashboardEnhance === 'function') {
						window.tctDashboardEnhance(resultsRoot);
					}
				} else {
					dropdownEl.innerHTML = '';
					setHidden(dropdownEl, true);
				}
			})
			.catch(function (err) {
				if (requestId !== state.lastRequestId) {
					return;
				}
				dropdownEl.innerHTML = '';
				setHidden(dropdownEl, true);
				showToast(err && err.message ? err.message : 'Search failed.', true);
			});
	}

	function ensureDomainTabsHeader(domainTabsEl) {
		if (!domainTabsEl) {
			return null;
		}
		var existing = domainTabsEl.querySelector('.tct-domain-tabs-header');
		if (existing) {
			return existing;
		}

		var tabNav = domainTabsEl.querySelector('.tct-tab-nav');
		if (!tabNav || !tabNav.parentNode) {
			return null;
		}

		var header = document.createElement('div');
		header.className = 'tct-domain-tabs-header';

		// Insert header before the nav, then move nav into it.
		tabNav.parentNode.insertBefore(header, tabNav);
		header.appendChild(tabNav);

		return header;
	}

	function ensureSearchUI(root) {
		root = root || document;

		var domainTabs = root.querySelector('.tct-domain-tabs');
		if (!domainTabs) {
			return;
		}

		var header = ensureDomainTabsHeader(domainTabs);
		if (!header) {
			return;
		}

		var wrap = header.querySelector('.tct-dashboard-search');
		if (!wrap) {
			wrap = document.createElement('div');
			wrap.className = 'tct-dashboard-search';
			wrap.innerHTML =
				'<input type="text" class="tct-dashboard-search-input" placeholder="Search goals..." autocomplete="off" aria-label="Search goals" />' +
				'<button type="button" class="tct-dashboard-search-clear-btn" data-tct-dashboard-search-clear="1" aria-label="Clear search" title="Clear search">\u00d7</button>' +
				'<div class="tct-dashboard-search-dropdown" hidden="hidden"></div>';
			header.appendChild(wrap);
		}

		var input = wrap.querySelector('.tct-dashboard-search-input');
		var dropdown = wrap.querySelector('.tct-dashboard-search-dropdown');
		if (!input || !dropdown) {
			return;
		}

		// Ensure a clear (X) button exists (in case the markup was injected by an older script).
		var clearBtn = wrap.querySelector('.tct-dashboard-search-clear-btn');
		if (!clearBtn) {
			clearBtn = document.createElement('button');
			clearBtn.type = 'button';
			clearBtn.className = 'tct-dashboard-search-clear-btn';
			clearBtn.setAttribute('data-tct-dashboard-search-clear', '1');
			clearBtn.setAttribute('aria-label', 'Clear search');
			clearBtn.setAttribute('title', 'Clear search');
			clearBtn.textContent = '\u00d7';
			try {
				wrap.insertBefore(clearBtn, dropdown);
			} catch (e) {
				wrap.appendChild(clearBtn);
			}
		}

		function clearSearch(focusInput) {
			try {
				// Invalidate any in-flight search response.
				state.lastRequestId = (state.lastRequestId || 0) + 1;
			} catch (e) {}
			state.query = '';
			try {
				input.value = '';
			} catch (e2) {}
			dropdown.innerHTML = '';
			setHidden(dropdown, true);
			if (focusInput) {
				try {
					input.focus();
				} catch (e3) {}
			}
		}

		// Bind clear button click (once).
		if (clearBtn && clearBtn.dataset && clearBtn.dataset.tctClearBound !== '1') {
			clearBtn.dataset.tctClearBound = '1';
			clearBtn.addEventListener('click', function (e) {
				if (e) {
					e.preventDefault();
					e.stopPropagation();
				}
				clearSearch(true);
			});
		}

		// Keep current state in sync on re-renders.
		if (state.query && input.value !== state.query) {
			input.value = state.query;
		}

		// Avoid double-binding.
		if (wrap.dataset && wrap.dataset.tctBound === '1') {
			// Re-run search if needed (e.g. after snapshot swap).
			if (state.query) {
				performSearch(state.query, dropdown);
			}
			return;
		}
		if (wrap.dataset) {
			wrap.dataset.tctBound = '1';
		}

		var runSearchDebounced = debounce(function () {
			var q = (input.value || '').trim();
			state.query = q;
			if (!q) {
				// Invalidate any in-flight search response so cleared input doesn't repopulate results.
				try {
					state.lastRequestId = (state.lastRequestId || 0) + 1;
				} catch (e) {}
				dropdown.innerHTML = '';
				setHidden(dropdown, true);
				return;
			}
			performSearch(q, dropdown);
		}, 180);

		input.addEventListener('input', function () {
			runSearchDebounced();
		});

		input.addEventListener('focus', function () {
			var q = (input.value || '').trim();
			if (q && dropdown.innerHTML && dropdown.innerHTML.trim()) {
				setHidden(dropdown, false);
			}
		});

		input.addEventListener('keydown', function (e) {
			if (!e) {
				return;
			}
			if (e.key === 'Escape') {
				clearSearch(true);
			}
		});

		// Results interactions (event delegation)
		dropdown.addEventListener('click', function (e) {
			if (!e || !e.target) {
				return;
			}

			var completeBtn = e.target.closest('[data-tct-mobile-row-complete]');
			if (completeBtn) {
				e.preventDefault();
				e.stopPropagation();
				handleRowQuickComplete(completeBtn);
				return;
			}

			var toggleEl = e.target.closest('[data-tct-mobile-toggle]');
			if (toggleEl) {
				e.preventDefault();
				var row = toggleEl.closest('.tct-mobile-result');
				if (row) {
					toggleRowExpanded(row, dropdown);
				}
			}
		});

		dropdown.addEventListener('keydown', function (e) {
			if (!e || !e.target) {
				return;
			}
			if (e.key !== 'Enter' && e.key !== ' ') {
				return;
			}
			var toggleEl = e.target.closest('[data-tct-mobile-toggle]');
			if (toggleEl) {
				e.preventDefault();
				var row = toggleEl.closest('.tct-mobile-result');
				if (row) {
					toggleRowExpanded(row, dropdown);
				}
			}
		});

		// Close dropdown when clicking elsewhere (dropdown UX)
		if (!state.outsideListenerAdded) {
			state.outsideListenerAdded = true;
			document.addEventListener('click', function (evt) {
				try {
					var target = evt && evt.target ? evt.target : null;
					if (!target) {
						return;
					}

					var inside = target.closest('.tct-dashboard-search');
					if (inside) {
						return;
					}

					document
						.querySelectorAll('.tct-dashboard-search-dropdown')
						.forEach(function (dd) {
							setHidden(dd, true);
						});
				} catch (err) {}
			});
		}

		// Listen for completions and update the small-row meta + show a visible points confirmation.
		if (!state.completionListenerAdded) {
			state.completionListenerAdded = true;

			document.addEventListener('tct_goal_completed', function (evt) {
				try {
					var detail = evt && evt.detail ? evt.detail : null;
					if (!detail || typeof detail.goalId === 'undefined') {
						return;
					}
					var goalId = parseInt(detail.goalId, 10);
					if (!goalId) {
						return;
					}
					var lastCompletedText =
						typeof detail.lastCompletedText !== 'undefined' ? detail.lastCompletedText : '';

					// Update any active dropdown rows that include this goal.
					document
						.querySelectorAll('.tct-dashboard-search-dropdown .tct-mobile-result[data-goal-id="' + goalId + '"]')
						.forEach(function (rowEl) {
							updateRowLastDone(rowEl, lastCompletedText);

							// Only show the floating points confirmation if this completion was initiated from the quick button.
							var pending = rowEl.getAttribute('data-tct-dash-search-pending') === '1';
							if (pending) {
								var quickBtn = rowEl.querySelector('[data-tct-mobile-row-complete]');
								var pts = rowEl.getAttribute('data-tct-dash-search-points') || getPointsTextFromQuickBtn(quickBtn);
								showPointsFloat(quickBtn, pts);
								clearRowPending(rowEl);
							}
						});
				} catch (e) {}
			});
		}

		// Initial render (if we already had a query)
		if (state.query) {
			performSearch(state.query, dropdown);
		}
	}

	function init(root) {
		try {
			ensureSearchUI(root || document);
		} catch (e) {
			// Never crash the dashboard.
			try {
				console.warn(e);
			} catch (err) {}
		}
	}

	// Init on ready
	if (TCT && typeof TCT.onReady === 'function') {
		TCT.onReady(function () {
			init(document);
		});
	} else {
		document.addEventListener('DOMContentLoaded', function () {
			init(document);
		});
	}

	// Hook into the dashboard re-enhancement flow (UI snapshot swaps)
	function wrapEnhance() {
		if (typeof window.tctDashboardEnhance !== 'function') {
			return;
		}
		if (window.tctDashboardEnhance.__tctSearchWrapped) {
			return;
		}
		var original = window.tctDashboardEnhance;
		var wrapped = function (node) {
			var res;
			try {
				res = original(node);
			} catch (e) {
				res = undefined;
			}
			init(node || document);
			return res;
		};
		wrapped.__tctSearchWrapped = true;
		window.tctDashboardEnhance = wrapped;
	}

	wrapEnhance();
})();