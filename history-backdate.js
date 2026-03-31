(function () {
  'use strict';

  function getConfig() {
    return typeof window !== 'undefined' ? window.tctDashboard : null;
  }

  function hasConfig() {
    var cfg = getConfig();
    return !!(
      cfg &&
      cfg.ajaxUrl &&
      cfg.goalHeatmapNonce &&
      cfg.undoCompletionNonce
    );
  }

  function getToastEl() {
    var t = document.querySelector('[data-tct-toast]');
    if (t) return t;

    t = document.createElement('div');
    t.setAttribute('data-tct-toast', '1');
    t.className = 'tct-toast';
    document.body.appendChild(t);
    return t;
  }

  function showToast(message, isError) {
    var t = getToastEl();
    t.textContent = String(message || '');
    t.classList.toggle('tct-toast-error', !!isError);
    t.style.display = 'block';

    if (showToast._timer) {
      clearTimeout(showToast._timer);
    }
    showToast._timer = setTimeout(function () {
      t.style.display = 'none';
    }, 2600);
  }

  function pad(n) {
    return n < 10 ? '0' + String(n) : String(n);
  }

  function formatYMD(date) {
    return (
      String(date.getFullYear()) +
      '-' +
      pad(date.getMonth() + 1) +
      '-' +
      pad(date.getDate())
    );
  }

  function parseYMD(str) {
    if (!str || typeof str !== 'string') return null;
    var m = str.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!m) return null;
    var y = parseInt(m[1], 10);
    var mo = parseInt(m[2], 10);
    var d = parseInt(m[3], 10);
    if (!y || !mo || !d) return null;
    if (mo < 1 || mo > 12) return null;
    if (d < 1 || d > 31) return null;
    return { y: y, m: mo - 1, d: d };
  }

  var MONTHS = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
  ];

  var DOW = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];

  var state = {
    goalId: 0,
    yearCache: {},
    yearCachePromises: {},
    calendarYear: null,
    calendarMonth: null,
  };

  function ensureBackdateUI(modal) {
    if (!modal) return null;

    var existing = modal.querySelector('[data-tct-history-backdate]');
    if (existing) return existing;

    var heatmap = modal.querySelector('[data-tct-history-heatmap]');
    if (!heatmap) return null;

    var wrap = document.createElement('div');
    wrap.className = 'tct-history-backdate';
    wrap.setAttribute('data-tct-history-backdate', '1');

    wrap.innerHTML =
      '<div class="tct-history-backdate-label">Enter completion for another date</div>' +
      '<div class="tct-history-backdate-controls">' +
      '  <div class="tct-history-backdate-picker" data-tct-history-backdate-picker="1">' +
      '    <input type="text" class="tct-history-backdate-date" data-tct-history-backdate-date="1" placeholder="Select a date" readonly />' +
      '    <div class="tct-sleep-cal-dropdown tct-history-backdate-calendar" data-tct-history-backdate-calendar="1" hidden></div>' +
      '  </div>' +
      '  <button type="button" class="tct-history-undo-btn tct-history-backdate-submit" data-tct-history-backdate-submit="1" aria-label="Add completion" title="Add completion" disabled>' +
      '    <span class="dashicons dashicons-yes" aria-hidden="true"></span>' +
      '  </button>' +
      '</div>';

    heatmap.insertAdjacentElement('afterend', wrap);
    return wrap;
  }

  function closeCalendar(modal) {
    if (!modal) return;
    var cal = modal.querySelector('[data-tct-history-backdate-calendar]');
    if (cal) {
      cal.setAttribute('hidden', 'hidden');
    }
  }

  function resetBackdateUI(modal) {
    if (!modal) return;
    var input = modal.querySelector('[data-tct-history-backdate-date]');
    var btn = modal.querySelector('[data-tct-history-backdate-submit]');
    if (input) input.value = '';
    if (btn) btn.disabled = true;
    closeCalendar(modal);

    state.calendarYear = null;
    state.calendarMonth = null;
  }

  function fetchYearDates(year) {
    var cfg = getConfig();
    if (!cfg || !cfg.ajaxUrl) {
      return Promise.reject(new Error('Missing config.'));
    }

    var goalId = state.goalId;
    if (!goalId) {
      return Promise.reject(new Error('Missing goal id.'));
    }

    if (state.yearCache[year]) {
      return Promise.resolve(state.yearCache[year]);
    }

    if (state.yearCachePromises[year]) {
      return state.yearCachePromises[year];
    }

    var fd = new FormData();
    fd.append('action', 'tct_goal_heatmap');
    fd.append('nonce', cfg.goalHeatmapNonce);
    fd.append('goal_id', String(goalId));
    fd.append('view', 'year');
    fd.append('year', String(year));
    fd.append('cursor', String(year) + '-01-01');

    var p = fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (resp) {
        if (!resp || !resp.success) {
          var msg =
            resp && resp.data && resp.data.message
              ? resp.data.message
              : 'Could not load completion dates.';
          throw new Error(msg);
        }

        var datesObj = resp && resp.data && resp.data.dates ? resp.data.dates : {};
        var set = {};
        Object.keys(datesObj).forEach(function (d) {
          if (d && datesObj[d] > 0) {
            set[d] = true;
          }
        });
        state.yearCache[year] = set;
        delete state.yearCachePromises[year];
        return set;
      })
      .catch(function (err) {
        delete state.yearCachePromises[year];
        throw err;
      });

    state.yearCachePromises[year] = p;
    return p;
  }

  function renderCalendar(calEl, year, month, selectedYmd) {
    if (!calEl) return;

    var set = state.yearCache[year] || {};

    // Use noon to avoid DST edge cases.
    var first = new Date(year, month, 1, 12, 0, 0);
    var firstDow = first.getDay();
    if (isNaN(firstDow) || firstDow < 0 || firstDow > 6) firstDow = 0;

    var daysInMonth = new Date(year, month + 1, 0, 12, 0, 0).getDate();
    if (!daysInMonth || isNaN(daysInMonth)) daysInMonth = 30;

    var today = new Date();
    var todayYmd = formatYMD(today);

    var title =
      (MONTHS[month] ? MONTHS[month] : 'Month') + ' ' + String(year);

    var html = '';
    html += '<div class="tct-sleep-cal-nav">';
    html +=
      '<button type="button" class="tct-sleep-cal-nav-btn" data-tct-history-cal-nav="-1" aria-label="Previous month">&#x2039;</button>';
    html += '<div class="tct-sleep-cal-title">' + title + '</div>';
    html +=
      '<button type="button" class="tct-sleep-cal-nav-btn" data-tct-history-cal-nav="1" aria-label="Next month">&#x203A;</button>';
    html += '</div>';

    html += '<table class="tct-sleep-cal-table">';
    html += '<thead><tr>';
    for (var i = 0; i < DOW.length; i++) {
      html += '<th scope="col">' + DOW[i] + '</th>';
    }
    html += '</tr></thead>';
    html += '<tbody>';

    // 6 rows x 7 days (42 cells)
    var cell = 0;
    for (var row = 0; row < 6; row++) {
      html += '<tr>';
      for (var col = 0; col < 7; col++) {
        var dayNum = cell - firstDow + 1;
        if (dayNum < 1 || dayNum > daysInMonth) {
          html += '<td></td>';
        } else {
          var dObj = new Date(year, month, dayNum, 12, 0, 0);
          var ymd = formatYMD(dObj);
          var isDisabled = !!(set && set[ymd]);
          var isSelected = !!(selectedYmd && selectedYmd === ymd);
          var isToday = todayYmd === ymd;

          var cls = 'tct-sleep-cal-day';
          if (isSelected) cls += ' tct-sleep-cal-night-start';
          if (isToday) cls += ' tct-sleep-cal-today';

          html += '<td>';
          html +=
            '<button type="button" class="' +
            cls +
            '" data-tct-history-cal-day="1" data-date="' +
            ymd +
            '"' +
            (isDisabled ? ' disabled aria-disabled="true" title="Already completed"' : '') +
            '>' +
            String(dayNum) +
            '</button>';
          html += '</td>';
        }
        cell++;
      }
      html += '</tr>';
    }

    html += '</tbody></table>';

    calEl.innerHTML = html;
  }

  function openCalendar(modal) {
    if (!modal) return;

    var input = modal.querySelector('[data-tct-history-backdate-date]');
    var cal = modal.querySelector('[data-tct-history-backdate-calendar]');
    var picker = modal.querySelector('[data-tct-history-backdate-picker]');
    if (!input || !cal || !picker) return;

    // Determine which month to show.
    var parsed = parseYMD(String(input.value || '').trim());
    var base = parsed
      ? new Date(parsed.y, parsed.m, parsed.d, 12, 0, 0)
      : new Date();

    var year = base.getFullYear();
    var month = base.getMonth();

    state.calendarYear = year;
    state.calendarMonth = month;

    cal.innerHTML = '<div style="padding:8px;font-size:12px;color:#6b7280;">Loading...</div>';
    cal.removeAttribute('hidden');

    fetchYearDates(year)
      .then(function () {
        renderCalendar(cal, year, month, String(input.value || '').trim());
      })
      .catch(function () {
        // Render without disabled info; server will still validate.
        renderCalendar(cal, year, month, String(input.value || '').trim());
      });
  }

  function toggleCalendar(modal) {
    if (!modal) return;
    var cal = modal.querySelector('[data-tct-history-backdate-calendar]');
    if (!cal) return;

    if (cal.hasAttribute('hidden')) {
      openCalendar(modal);
    } else {
      closeCalendar(modal);
    }
  }

  function shiftMonth(year, month, delta) {
    var y = year;
    var m = month + delta;
    while (m < 0) {
      m += 12;
      y -= 1;
    }
    while (m > 11) {
      m -= 12;
      y += 1;
    }
    return { y: y, m: m };
  }

  function handleOpenHistoryClick(ev) {
    var trigger =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-open-goal-history="1"]')
        : null;
    if (!trigger) return;

    var gid = parseInt(trigger.getAttribute('data-goal-id') || '0', 10);
    if (!gid) return;

    var modal = document.querySelector('[data-tct-history-modal]');
    if (!modal) return;

    modal.setAttribute('data-tct-history-goal-id', String(gid));
    state.goalId = gid;
    state.yearCache = {};
    state.yearCachePromises = {};

    ensureBackdateUI(modal);
    resetBackdateUI(modal);

    // Prefetch current year so disabled days are ready when the picker opens.
    fetchYearDates(new Date().getFullYear()).catch(function () {});
  }

  function handleDateFieldClick(ev) {
    var input =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-history-backdate-date]')
        : null;
    if (!input) return;

    var modal = input.closest('[data-tct-history-modal]');
    if (!modal) return;

    ensureBackdateUI(modal);
    toggleCalendar(modal);
  }

  function handleCalendarClick(ev) {
    var cal =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-history-backdate-calendar]')
        : null;
    if (!cal) return;

    var modal = cal.closest('[data-tct-history-modal]');
    if (!modal) return;

    var input = modal.querySelector('[data-tct-history-backdate-date]');
    var submitBtn = modal.querySelector('[data-tct-history-backdate-submit]');
    if (!input || !submitBtn) return;

    var nav =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-history-cal-nav]')
        : null;
    if (nav) {
      ev.preventDefault();
      var dir = parseInt(nav.getAttribute('data-tct-history-cal-nav') || '0', 10);
      if (!dir || isNaN(dir)) return;

      var curYear = state.calendarYear;
      var curMonth = state.calendarMonth;
      if (typeof curYear !== 'number' || typeof curMonth !== 'number') {
        var base = parseYMD(String(input.value || '').trim());
        var baseDate = base
          ? new Date(base.y, base.m, base.d, 12, 0, 0)
          : new Date();
        curYear = baseDate.getFullYear();
        curMonth = baseDate.getMonth();
      }

      var next = shiftMonth(curYear, curMonth, dir);
      state.calendarYear = next.y;
      state.calendarMonth = next.m;

      cal.innerHTML = '<div style="padding:8px;font-size:12px;color:#6b7280;">Loading...</div>';

      fetchYearDates(next.y)
        .then(function () {
          renderCalendar(cal, next.y, next.m, String(input.value || '').trim());
        })
        .catch(function () {
          renderCalendar(cal, next.y, next.m, String(input.value || '').trim());
        });
      return;
    }

    var dayBtn =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-history-cal-day]')
        : null;
    if (dayBtn) {
      ev.preventDefault();
      if (dayBtn.disabled) return;

      var dateValue = String(dayBtn.getAttribute('data-date') || '').trim();
      if (!/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) return;

      input.value = dateValue;
      submitBtn.disabled = false;
      closeCalendar(modal);
      return;
    }
  }

  function handleOutsideClick(ev) {
    var modal = document.querySelector('[data-tct-history-modal]');
    if (!modal) return;

    var cal = modal.querySelector('[data-tct-history-backdate-calendar]');
    var picker = modal.querySelector('[data-tct-history-backdate-picker]');
    if (!cal || !picker) return;
    if (cal.hasAttribute('hidden')) return;

    if (ev.target && picker.contains(ev.target)) {
      return;
    }

    closeCalendar(modal);
  }

  function handleSubmit(ev) {
    var btn =
      ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-history-backdate-submit]')
        : null;
    if (!btn) return;

    var cfg = getConfig();
    if (!cfg || !cfg.ajaxUrl) return;

    var modal = document.querySelector('[data-tct-history-modal]');
    if (!modal) return;

    var goalId =
      state.goalId ||
      parseInt(modal.getAttribute('data-tct-history-goal-id') || '0', 10);
    if (!goalId) {
      showToast('Could not determine goal.', true);
      return;
    }
    state.goalId = goalId;

    var input = modal.querySelector('[data-tct-history-backdate-date]');
    if (!input) {
      showToast('Date field not found.', true);
      return;
    }

    var dateValue = String(input.value || '').trim();
    if (!dateValue) {
      showToast('Pick a date first.', true);
      return;
    }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
      showToast('Invalid date.', true);
      return;
    }

    var year = parseInt(dateValue.slice(0, 4), 10);
    var set = state.yearCache[year];
    if (set && set[dateValue]) {
      showToast('That date already has a completion.', true);
      return;
    }

    btn.disabled = true;
    btn.classList.add('tct-loading');

    var fd = new FormData();
    fd.append('action', 'tct_backdate_completion');
    fd.append('nonce', cfg.undoCompletionNonce);
    fd.append('goal_id', String(goalId));
    fd.append('date', dateValue);

    fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd,
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (resp) {
        if (!resp || !resp.success) {
          var msg =
            resp && resp.data && resp.data.message
              ? resp.data.message
              : 'Could not add completion.';
          throw new Error(msg);
        }

        if (!state.yearCache[year]) state.yearCache[year] = {};
        state.yearCache[year][dateValue] = true;

        closeCalendar(modal);
        input.value = '';
        showToast('Completion added for ' + dateValue + '.', false);

        // Refresh the history modal by re-triggering its existing opener.
        var refreshTrigger = document.querySelector(
          '[data-tct-open-goal-history="1"][data-goal-id="' + goalId + '"]'
        );
        if (refreshTrigger) {
          refreshTrigger.click();
        }
      })
      .catch(function (err) {
        showToast(
          err && err.message ? err.message : 'Could not add completion.',
          true
        );
      })
      .finally(function () {
        btn.classList.remove('tct-loading');
        btn.disabled = true;
      });
  }

  if (!hasConfig()) return;

  // Capture open-history clicks early to record goal id and prime the backdate UI.
  document.addEventListener('click', handleOpenHistoryClick, true);
  document.addEventListener('click', handleDateFieldClick, false);
  document.addEventListener('click', handleCalendarClick, false);
  document.addEventListener('click', handleSubmit, false);

  // Close calendar when clicking elsewhere.
  document.addEventListener('click', handleOutsideClick, true);

  document.addEventListener('DOMContentLoaded', function () {
    var modal = document.querySelector('[data-tct-history-modal]');
    if (!modal) return;
    ensureBackdateUI(modal);
  });
})();
