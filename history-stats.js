(function () {
  'use strict';

  function qs(selector, root) {
    return (root || document).querySelector(selector);
  }

  function getHistoryModal(fromEl) {
    // The history modal is rendered with a data attribute.
    // Prefer the dashboard-local modal if we have a click source element.
    if (fromEl && fromEl.closest) {
      var dash = fromEl.closest('.tct-dashboard');
      if (dash) {
        var local = qs('[data-tct-history-modal]', dash);
        if (local) return local;
      }
    }
    return document.querySelector('[data-tct-history-modal]');
  }

  function setText(el, text) {
    if (!el) return;
    el.textContent = text;
  }

  function show(el) {
    if (!el) return;
    el.hidden = false;
  }

  function hide(el) {
    if (!el) return;
    el.hidden = true;
  }

  var lastRequestId = 0;

  function refresh(goalId, fromEl) {
    goalId = parseInt(goalId, 10);
    if (!goalId || goalId < 1) return;

    var modal = getHistoryModal(fromEl);
    if (!modal) return;

    var container =
      qs('[data-tct-history-heatmap-stats]', modal) ||
      qs('[data-tct-history-heatmap-stats]');

    if (!container) return;

    // Provide immediate feedback while loading
    var rateEl = qs('[data-tct-history-success-rate]', container);
    var longestEl = qs('[data-tct-history-longest-streak]', container);
    var currentEl = qs('[data-tct-history-current-streak]', container);

    setText(rateEl, '…');
    setText(longestEl, '…');
    setText(currentEl, '…');
    show(container);

    if (!window.tctDashboard || !tctDashboard.ajaxUrl || !tctDashboard.goalHistoryNonce) {
      // Can't call the API; hide instead of showing placeholders forever.
      hide(container);
      return;
    }

    var reqId = ++lastRequestId;

    var body = new URLSearchParams();
    body.append('action', 'tct_goal_success_stats');
    body.append('nonce', tctDashboard.goalHistoryNonce);
    body.append('goal_id', String(goalId));

    fetch(tctDashboard.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      credentials: 'same-origin',
      body: body.toString(),
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (json) {
        if (reqId !== lastRequestId) return;

        if (!json || !json.success || !json.data) {
          hide(container);
          return;
        }

        if (json.data.available === false) {
          hide(container);
          return;
        }

        var pctLabel =
          typeof json.data.successPctLabel === 'string'
            ? json.data.successPctLabel
            : typeof json.data.successPct === 'number'
              ? String(Math.round(json.data.successPct)) + '%'
              : '--';

        setText(rateEl, pctLabel);
        setText(longestEl, String(json.data.longestStreak || 0));
        setText(currentEl, String(json.data.currentStreak || 0));

        show(container);
      })
      .catch(function () {
        if (reqId !== lastRequestId) return;
        hide(container);
      });
  }

  // Expose for other scripts (e.g. history-toggle.js)
  window.tctHistoryStatsRefresh = refresh;

  // Refresh when the history modal is opened.
  document.addEventListener(
    'click',
    function (e) {
      var openBtn = e.target && e.target.closest
        ? e.target.closest('[data-tct-open-goal-history="1"]')
        : null;

      if (openBtn) {
        var gid = openBtn.getAttribute('data-goal-id');
        // Let the core handler build the modal, then fetch stats.
        setTimeout(function () {
          refresh(gid, openBtn);
        }, 0);
        return;
      }

      // Refresh after an undo (core handler updates the lists asynchronously).
      var undoBtn = null;
      if (e.target && e.target.closest) {
        // In the history modal, the button is data-tct-undo-completion.
        // In the main Ledger table, it's data-tct-ledger-undo.
        undoBtn =
          e.target.closest('[data-tct-undo-completion="1"]') ||
          e.target.closest('[data-tct-ledger-undo="1"]');
      }

      if (undoBtn) {
        var gid2 = undoBtn.getAttribute('data-goal-id');
        setTimeout(function () {
          refresh(gid2, undoBtn);
        }, 350);
      }
    },
    true
  );
})();