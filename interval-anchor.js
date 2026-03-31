(function () {
  'use strict';

  function safeJsonParse(raw) {
    if (!raw || typeof raw !== 'string') {
      return null;
    }
    try {
      return JSON.parse(raw);
    } catch (e) {
      return null;
    }
  }

  function getModal() {
    return document.querySelector('[data-tct-goal-modal]');
  }

  function getForm(modal) {
    if (!modal) {
      return null;
    }
    return modal.querySelector('[data-tct-goal-form]');
  }

  function getEls() {
    var modal = getModal();
    if (!modal) {
      return null;
    }

    var form = getForm(modal);
    if (!form) {
      return null;
    }

    return {
      modal: modal,
      form: form,
      goalType: form.querySelector('[data-tct-goal-type-select]'),
      intervalContainer: form.querySelector('[data-tct-interval-row-container]'),
      row: form.querySelector('[data-tct-interval-anchor-row]'),
      enabled: form.querySelector('[data-tct-interval-anchor-enabled]'),
      fields: form.querySelector('[data-tct-interval-anchor-fields]'),
      summary: form.querySelector('[data-tct-interval-anchor-summary]'),
      warning: form.querySelector('[data-tct-interval-anchor-warning]'),
      anchorDate: form.querySelector('[data-tct-interval-anchor-date]'),
      anchorDay: form.querySelector('[data-tct-interval-anchor-day]')
    };
  }

  function boolish(value) {
    if (value === true || value === 1) {
      return true;
    }
    if (value === false || value === 0 || value == null) {
      return false;
    }
    var str = String(value).trim().toLowerCase();
    return str === '1' || str === 'true' || str === 'yes' || str === 'on';
  }

  function isValidYmd(ymd) {
    if (typeof ymd !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(ymd)) {
      return false;
    }
    var parts = ymd.split('-');
    var y = parseInt(parts[0], 10);
    var m = parseInt(parts[1], 10);
    var d = parseInt(parts[2], 10);
    if (!isFinite(y) || !isFinite(m) || !isFinite(d) || m < 1 || m > 12 || d < 1 || d > 31) {
      return false;
    }
    var dt = new Date(Date.UTC(y, m - 1, d));
    return dt.getUTCFullYear() === y && dt.getUTCMonth() === (m - 1) && dt.getUTCDate() === d;
  }

  function parseYmd(ymd) {
    if (!isValidYmd(ymd)) {
      return null;
    }
    var parts = ymd.split('-');
    return {
      y: parseInt(parts[0], 10),
      m: parseInt(parts[1], 10),
      d: parseInt(parts[2], 10)
    };
  }

  function ymdToDate(ymd) {
    var p = parseYmd(ymd);
    if (!p) {
      return null;
    }
    return new Date(Date.UTC(p.y, p.m - 1, p.d));
  }

  function formatYmd(dateObj) {
    if (!(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
      return '';
    }
    var yyyy = dateObj.getUTCFullYear();
    var mm = String(dateObj.getUTCMonth() + 1).padStart(2, '0');
    var dd = String(dateObj.getUTCDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
  }

  function addDaysYmd(ymd, delta) {
    var dt = ymdToDate(ymd);
    if (!dt) {
      return '';
    }
    dt.setUTCDate(dt.getUTCDate() + (parseInt(delta, 10) || 0));
    return formatYmd(dt);
  }

  function daysBetweenYmd(a, b) {
    var da = ymdToDate(a);
    var db = ymdToDate(b);
    if (!da || !db) {
      return 0;
    }
    return Math.round((db.getTime() - da.getTime()) / 86400000);
  }

  function ymdToday() {
    var modal = getModal();
    if (modal) {
      var row = modal.querySelector('[data-tct-interval-anchor-row]');
      if (row) {
        var attr = row.getAttribute('data-tct-interval-anchor-today');
        if (isValidYmd(attr)) {
          return attr;
        }
      }
    }
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }

  function show(el) {
    if (el) {
      el.removeAttribute('hidden');
    }
  }

  function hide(el) {
    if (el) {
      el.setAttribute('hidden', 'hidden');
    }
  }

  function setFieldsetDisabled(root, disabled) {
    if (!root || !root.querySelectorAll) {
      return;
    }
    var fields = root.querySelectorAll('input, select, textarea, button');
    Array.prototype.forEach.call(fields, function (field) {
      field.disabled = !!disabled;
    });
  }

  function normalizeGoalType(value) {
    var out = String(value || '').trim().toLowerCase();
    return out || 'positive';
  }

  function normalizeUnit(value) {
    var out = String(value || '').trim().toLowerCase();
    var map = {
      hours: 'hour',
      daily: 'day',
      days: 'day',
      weekly: 'week',
      weeks: 'week',
      monthly: 'month',
      months: 'month',
      quarterly: 'quarter',
      quarters: 'quarter',
      'semi-annual': 'semiannual',
      semiannual: 'semiannual',
      semiannually: 'semiannual',
      halfyear: 'semiannual',
      'half-year': 'semiannual',
      annual: 'year',
      annually: 'year',
      yearly: 'year',
      years: 'year'
    };
    if (map[out]) {
      out = map[out];
    }
    if (['hour', 'day', 'week', 'month', 'quarter', 'semiannual', 'year'].indexOf(out) === -1) {
      return 'week';
    }
    return out;
  }

  function getIntervalRow(form) {
    if (!form) {
      return null;
    }
    return form.querySelector('[data-tct-interval-row]');
  }

  function getIntervalConfig(els) {
    if (!els || !els.form) {
      return null;
    }
    var row = getIntervalRow(els.form);
    if (!row) {
      return null;
    }
    var targetEl = row.querySelector('[data-tct-interval-target]');
    var unitEl = row.querySelector('[data-tct-interval-unit]');
    var spanEl = row.querySelector('[data-tct-interval-span]');
    if (!targetEl || !unitEl) {
      return null;
    }
    var target = parseInt(targetEl.value, 10) || 0;
    var unit = normalizeUnit(unitEl.value || '');
    var span = spanEl ? (parseInt(spanEl.value, 10) || 1) : 1;
    if (span < 1) {
      span = 1;
    }
    return {
      target: target,
      unit: unit,
      span: span
    };
  }

  function isIntervalAnchorEligible(els) {
    if (!els || !els.goalType) {
      return false;
    }
    var goalType = normalizeGoalType(els.goalType.value);
    if (goalType === 'never' || goalType === 'harm_reduction' || goalType === 'positive_no_int') {
      return false;
    }
    var interval = getIntervalConfig(els);
    return !!interval && interval.target > 0 && !!interval.unit && interval.span > 0 && interval.unit !== 'hour';
  }

  function daysInMonth(year, month) {
    return new Date(Date.UTC(year, month, 0)).getUTCDate();
  }

  function addMonthsClamped(dateObj, months) {
    if (!(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
      return null;
    }
    var total = (dateObj.getUTCFullYear() * 12) + dateObj.getUTCMonth() + (parseInt(months, 10) || 0);
    var newYear = Math.floor(total / 12);
    var newMonthZero = total - (newYear * 12);
    if (newMonthZero < 0) {
      newYear -= 1;
      newMonthZero += 12;
    }
    var newMonth = newMonthZero + 1;
    var day = dateObj.getUTCDate();
    var maxDay = daysInMonth(newYear, newMonth);
    if (day > maxDay) {
      day = maxDay;
    }
    return new Date(Date.UTC(newYear, newMonth - 1, day));
  }

  function shiftIntervalStart(dateObj, unit, span, steps) {
    if (!(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
      return null;
    }
    var safeUnit = normalizeUnit(unit || 'week');
    var safeSpan = parseInt(span, 10) || 1;
    if (safeSpan < 1) {
      safeSpan = 1;
    }
    var safeSteps = parseInt(steps, 10) || 0;
    if (safeSteps === 0) {
      return new Date(dateObj.getTime());
    }
    if (safeUnit === 'day') {
      var nextDay = new Date(dateObj.getTime());
      nextDay.setUTCDate(nextDay.getUTCDate() + (safeSteps * safeSpan));
      return nextDay;
    }
    if (safeUnit === 'week') {
      var nextWeek = new Date(dateObj.getTime());
      nextWeek.setUTCDate(nextWeek.getUTCDate() + (safeSteps * safeSpan * 7));
      return nextWeek;
    }
    if (safeUnit === 'quarter') {
      return addMonthsClamped(dateObj, safeSteps * safeSpan * 3);
    }
    if (safeUnit === 'semiannual') {
      return addMonthsClamped(dateObj, safeSteps * safeSpan * 6);
    }
    if (safeUnit === 'month') {
      return addMonthsClamped(dateObj, safeSteps * safeSpan);
    }
    if (safeUnit === 'year') {
      return addMonthsClamped(dateObj, safeSteps * safeSpan * 12);
    }
    var fallback = new Date(dateObj.getTime());
    fallback.setUTCDate(fallback.getUTCDate() + (safeSteps * safeSpan));
    return fallback;
  }

  function normalizeIntervalAnchor(raw, intervalCfg) {
    var out = {
      enabled: false,
      anchor_date_local: '',
      anchor_day: 0,
      anchor_start_local: '',
      period_unit: intervalCfg && intervalCfg.unit ? intervalCfg.unit : '',
      period_span: intervalCfg && intervalCfg.span ? intervalCfg.span : 0,
      interval_length_days: 0
    };

    if (typeof raw === 'string') {
      var decoded = safeJsonParse(raw);
      raw = decoded || {};
    }
    if (!raw || typeof raw !== 'object') {
      return out;
    }
    if (!boolish(raw.enabled != null ? raw.enabled : raw.is_enabled)) {
      return out;
    }
    if (!intervalCfg || !intervalCfg.target || !intervalCfg.unit || intervalCfg.unit === 'hour') {
      return out;
    }

    var anchorDate = String(raw.anchor_date_local || raw.anchor_date || '').trim();
    if (anchorDate.length > 10) {
      anchorDate = anchorDate.slice(0, 10);
    }
    var anchorDay = parseInt(raw.anchor_day != null ? raw.anchor_day : (raw.day != null ? raw.day : raw.day_within_interval), 10) || 0;
    if (!isValidYmd(anchorDate) || anchorDay < 1) {
      return out;
    }

    var anchorStart = addDaysYmd(anchorDate, -1 * (anchorDay - 1));
    if (!isValidYmd(anchorStart)) {
      return out;
    }

    var anchorStartDate = ymdToDate(anchorStart);
    var anchorDateObj = ymdToDate(anchorDate);
    var anchorEndDate = shiftIntervalStart(anchorStartDate, intervalCfg.unit, intervalCfg.span, 1);
    if (!anchorStartDate || !anchorDateObj || !anchorEndDate || anchorEndDate.getTime() <= anchorStartDate.getTime()) {
      return out;
    }
    if (anchorDateObj.getTime() < anchorStartDate.getTime() || anchorDateObj.getTime() >= anchorEndDate.getTime()) {
      return out;
    }

    out.enabled = true;
    out.anchor_date_local = anchorDate;
    out.anchor_day = anchorDay;
    out.anchor_start_local = anchorStart;
    out.interval_length_days = Math.max(1, Math.round((anchorEndDate.getTime() - anchorStartDate.getTime()) / 86400000));
    return out;
  }

  function normalizeIntervalAnchorFromPayload(payload, intervalCfg) {
    if (!payload || typeof payload !== 'object') {
      return normalizeIntervalAnchor(null, intervalCfg);
    }
    var raw = payload.interval_anchor_json;
    return normalizeIntervalAnchor(raw, intervalCfg);
  }

  function setSummary(els, message) {
    if (els && els.summary) {
      els.summary.textContent = message || '';
    }
  }

  function setWarning(els, message) {
    if (!els || !els.warning) {
      return;
    }
    if (!message) {
      els.warning.textContent = '';
      hide(els.warning);
      return;
    }
    els.warning.textContent = String(message);
    show(els.warning);
  }

  function scrollWarningIntoView(els) {
    if (els && els.warning && !els.warning.hasAttribute('hidden') && els.warning.scrollIntoView) {
      try {
        els.warning.scrollIntoView({ block: 'nearest', inline: 'nearest' });
      } catch (e) {
        // ignore
      }
    }
  }

  function resetDefaults(els) {
    if (!els) {
      return;
    }
    var today = ymdToday();
    if (els.anchorDate && !els.anchorDate.value) {
      els.anchorDate.value = today;
    }
    if (els.anchorDay && !els.anchorDay.value) {
      els.anchorDay.value = '1';
    }
  }

  function applyIntervalAnchorConfig(els, cfg) {
    if (!els) {
      return;
    }
    var intervalCfg = getIntervalConfig(els);
    var norm = normalizeIntervalAnchor(cfg, intervalCfg);
    if (els.enabled) {
      els.enabled.value = norm.enabled ? '1' : '0';
    }
    if (els.anchorDate) {
      els.anchorDate.value = norm.enabled ? norm.anchor_date_local : ymdToday();
    }
    if (els.anchorDay) {
      els.anchorDay.value = String(norm.enabled ? norm.anchor_day : 1);
    }
  }

  function rememberOriginalConfig(els, payload) {
    if (!els || !els.modal) {
      return;
    }
    els.modal.__tctIntervalAnchorOriginalPayload = payload || null;
    els.modal.__tctIntervalAnchorOriginalConfig = normalizeIntervalAnchorFromPayload(payload, getIntervalConfig(els));
  }

  function buildIntervalAnchorFromForm(els) {
    if (!els) {
      return normalizeIntervalAnchor(null, getIntervalConfig(els));
    }
    return normalizeIntervalAnchor({
      enabled: els.enabled ? els.enabled.value : 0,
      anchor_date_local: els.anchorDate ? els.anchorDate.value : '',
      anchor_day: els.anchorDay ? els.anchorDay.value : 0
    }, getIntervalConfig(els));
  }

  function intervalLabel(intervalCfg) {
    if (!intervalCfg) {
      return 'interval';
    }
    var span = parseInt(intervalCfg.span, 10) || 1;
    var unit = normalizeUnit(intervalCfg.unit || 'week');
    var singular = unit;
    var plural = unit + 's';
    if (unit === 'semiannual') {
      singular = 'half-year';
      plural = 'half-years';
    } else if (unit === 'day') {
      plural = 'days';
    } else if (unit === 'week') {
      plural = 'weeks';
    } else if (unit === 'month') {
      plural = 'months';
    } else if (unit === 'quarter') {
      plural = 'quarters';
    } else if (unit === 'year') {
      plural = 'years';
    }
    return span === 1 ? singular : (span + ' ' + plural);
  }

  function validateCurrent(els, showMessages) {
    if (!els || !els.row) {
      return { ok: true, message: '' };
    }

    var enabled = !!(els.enabled && els.enabled.value === '1');
    var eligible = isIntervalAnchorEligible(els);
    var originalEnabled = !!(els.modal && els.modal.__tctIntervalAnchorOriginalConfig && els.modal.__tctIntervalAnchorOriginalConfig.enabled);
    var intervalCfg = getIntervalConfig(els);

    if (!enabled) {
      if (showMessages) {
        setWarning(els, '');
      }
      return { ok: true, message: '' };
    }

    if (!eligible) {
      var ineligibleMessage = 'Interval alignment is only available for positive interval goals with day-or-larger intervals. Change the goal back to an eligible interval goal or switch Interval alignment to Off before saving.';
      if (showMessages) {
        setWarning(els, ineligibleMessage);
      }
      return { ok: false, message: ineligibleMessage, originalEnabled: originalEnabled };
    }

    var cfg = buildIntervalAnchorFromForm(els);
    if (!cfg.enabled) {
      var invalidMessage = 'Invalid interval alignment. Please provide a valid anchor date and the current day within the interval.';
      if (showMessages) {
        setWarning(els, invalidMessage);
      }
      return { ok: false, message: invalidMessage };
    }

    if (showMessages) {
      setWarning(els, '');
    }

    return {
      ok: true,
      message: '',
      config: cfg,
      intervalCfg: intervalCfg
    };
  }

  function updateIntervalAnchorUI() {
    var els = getEls();
    if (!els || !els.row || !els.enabled || !els.fields) {
      return;
    }

    resetDefaults(els);

    var enabled = els.enabled.value === '1';
    var eligible = isIntervalAnchorEligible(els);
    var originalEnabled = !!(els.modal && els.modal.__tctIntervalAnchorOriginalConfig && els.modal.__tctIntervalAnchorOriginalConfig.enabled);
    var intervalCfg = getIntervalConfig(els);
    var shouldShowRow = eligible || enabled || originalEnabled;

    if (!shouldShowRow) {
      hide(els.row);
      hide(els.fields);
      setFieldsetDisabled(els.fields, true);
      setWarning(els, '');
      return;
    }

    show(els.row);

    if (eligible && enabled) {
      show(els.fields);
      setFieldsetDisabled(els.fields, false);
      var result = validateCurrent(els, false);
      if (result.ok && result.config) {
        setSummary(
          els,
          'Anchor date means this goal is day ' + result.config.anchor_day + ' of its current ' + intervalLabel(intervalCfg) + ' interval. The obligation window then continues from that independent anchor.'
        );
      } else {
        setSummary(els, 'Set the local date and the current day within the interval to align the obligation clock independently from the Active / Pause cycle.');
      }
    } else {
      hide(els.fields);
      setFieldsetDisabled(els.fields, true);
      if (eligible) {
        setSummary(els, 'Use this when the interval clock should start independently from the Active / Pause cycle.');
      } else {
        setSummary(els, 'This goal type cannot use interval alignment. Change the goal back to an eligible interval goal or switch Interval alignment to Off before saving.');
      }
    }

    if (!enabled) {
      setWarning(els, '');
      return;
    }

    validateCurrent(els, true);
  }

  function parseGoalPayload(button) {
    if (!button) {
      return null;
    }
    var raw = button.getAttribute('data-tct-goal');
    return raw ? safeJsonParse(raw) : null;
  }

  function onOpenClick(event) {
    if (!event || !event.target || !event.target.closest) {
      return;
    }
    var button = event.target.closest('[data-tct-open-goal-modal]');
    if (!button || button.hasAttribute('disabled')) {
      return;
    }

    var mode = button.getAttribute('data-tct-open-goal-modal') || 'add';
    var payload = mode === 'edit' ? parseGoalPayload(button) : null;

    setTimeout(function () {
      var els = getEls();
      if (!els) {
        return;
      }
      rememberOriginalConfig(els, payload);
      if (mode === 'edit' && payload) {
        applyIntervalAnchorConfig(els, payload.interval_anchor_json || null);
      } else {
        applyIntervalAnchorConfig(els, null);
      }
      updateIntervalAnchorUI();
    }, 0);
  }

  function onFormSubmit(event) {
    var els = getEls();
    if (!els || !event || event.target !== els.form) {
      return;
    }
    var result = validateCurrent(els, true);
    if (!result.ok) {
      event.preventDefault();
      scrollWarningIntoView(els);
    }
  }

  function init() {
    var els = getEls();
    if (!els || !els.row || !els.enabled || !els.fields) {
      return;
    }

    rememberOriginalConfig(els, null);
    resetDefaults(els);
    updateIntervalAnchorUI();

    document.addEventListener('click', onOpenClick, true);
    document.addEventListener(
      'change',
      function (event) {
        if (!event || !event.target || !event.target.closest) {
          return;
        }
        if (event.target.closest('[data-tct-goal-modal]')) {
          updateIntervalAnchorUI();
        }
      },
      true
    );
    document.addEventListener(
      'input',
      function (event) {
        if (!event || !event.target || !event.target.closest) {
          return;
        }
        if (event.target.closest('[data-tct-goal-modal]')) {
          updateIntervalAnchorUI();
        }
      },
      true
    );
    els.form.addEventListener('submit', onFormSubmit, true);

    if (els.intervalContainer && window.MutationObserver) {
      var observer = new MutationObserver(function () {
        updateIntervalAnchorUI();
      });
      observer.observe(els.intervalContainer, { childList: true, subtree: true });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
