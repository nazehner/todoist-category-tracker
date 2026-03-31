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
      row: form.querySelector('[data-tct-availability-row]'),
      enabled: form.querySelector('[data-tct-availability-enabled]'),
      fields: form.querySelector('[data-tct-availability-fields]'),
      summary: form.querySelector('[data-tct-availability-summary]'),
      warning: form.querySelector('[data-tct-availability-warning]'),
      anchorDate: form.querySelector('[data-tct-availability-anchor-date]'),
      anchorPhase: form.querySelector('[data-tct-availability-anchor-phase]'),
      anchorDay: form.querySelector('[data-tct-availability-anchor-day]'),
      activeDuration: form.querySelector('[data-tct-availability-active-duration]'),
      pauseDuration: form.querySelector('[data-tct-availability-pause-duration]'),
      dueEnabled: form.querySelector('[data-tct-due-schedule-enabled]'),
      dueStart: form.querySelector('[data-tct-due-schedule-start]'),
      dueType: form.querySelector('[data-tct-due-schedule-type]'),
      dueEvery: form.querySelector('[data-tct-due-schedule-every]'),
      dueDom: form.querySelector('[data-tct-due-schedule-dom]')
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

  function ymdToday() {
    var modal = getModal();
    if (modal) {
      var row = modal.querySelector('[data-tct-availability-row]');
      if (row) {
        var attr = row.getAttribute('data-tct-availability-today');
        if (isValidYmd(attr)) {
          return attr;
        }
      }
    }
    var d = new Date();
    var yyyy = d.getFullYear();
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var dd = String(d.getDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
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

  function formatYmd(dateObj) {
    if (!(dateObj instanceof Date) || isNaN(dateObj.getTime())) {
      return '';
    }
    var yyyy = dateObj.getUTCFullYear();
    var mm = String(dateObj.getUTCMonth() + 1).padStart(2, '0');
    var dd = String(dateObj.getUTCDate()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd;
  }

  function compareYmd(a, b) {
    if (!isValidYmd(a) && !isValidYmd(b)) {
      return 0;
    }
    if (!isValidYmd(a)) {
      return -1;
    }
    if (!isValidYmd(b)) {
      return 1;
    }
    if (a === b) {
      return 0;
    }
    return a < b ? -1 : 1;
  }

  function addDaysYmd(ymd, delta) {
    var p = parseYmd(ymd);
    if (!p) {
      return '';
    }
    var dt = new Date(Date.UTC(p.y, p.m - 1, p.d));
    dt.setUTCDate(dt.getUTCDate() + (parseInt(delta, 10) || 0));
    return formatYmd(dt);
  }

  function daysBetweenYmd(a, b) {
    var pa = parseYmd(a);
    var pb = parseYmd(b);
    if (!pa || !pb) {
      return 0;
    }
    var da = Date.UTC(pa.y, pa.m - 1, pa.d);
    var db = Date.UTC(pb.y, pb.m - 1, pb.d);
    return Math.round((db - da) / 86400000);
  }

  function positiveMod(value, mod) {
    var m = parseInt(mod, 10) || 0;
    if (m <= 0) {
      return 0;
    }
    var out = (parseInt(value, 10) || 0) % m;
    return out < 0 ? out + m : out;
  }

  function gcd(a, b) {
    var x = Math.abs(parseInt(a, 10) || 0);
    var y = Math.abs(parseInt(b, 10) || 0);
    while (y) {
      var t = y;
      y = x % y;
      x = t;
    }
    return x || 0;
  }

  function daysInMonth(year, monthOneBased) {
    return new Date(Date.UTC(year, monthOneBased, 0)).getUTCDate();
  }

  function monthDueDay(year, monthOneBased, dayOfMonth) {
    var dom = parseInt(dayOfMonth, 10) || 1;
    if (dom < 1) {
      dom = 1;
    }
    if (dom > 31) {
      dom = 31;
    }
    return Math.min(dom, daysInMonth(year, monthOneBased));
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

  function setFieldsetDisabled(container, disabled) {
    if (!container) {
      return;
    }
    var inputs = container.querySelectorAll('input, select, textarea, button');
    inputs.forEach(function (el) {
      el.disabled = !!disabled;
    });
  }

  function normalizeGoalType(type) {
    var value = (type || 'positive').toString().trim().toLowerCase();
    return value || 'positive';
  }

  function normalizeUnit(unit) {
    var value = (unit || '').toString().trim().toLowerCase();
    if (!value) {
      return '';
    }
    if (value === 'hourly') return 'hour';
    if (value === 'daily') return 'day';
    if (value === 'weekly') return 'week';
    if (value === 'monthly') return 'month';
    if (value === 'quarterly') return 'quarter';
    if (value === 'yearly' || value === 'annual' || value === 'annually') return 'year';
    if (value === 'semi-annual' || value === 'halfyear' || value === 'half-year' || value === 'halfyears' || value === 'half-years') return 'semiannual';
    if (value.endsWith('s') && value.length > 1) {
      value = value.slice(0, -1);
    }
    return value;
  }

  function normalizeAvailability(raw) {
    var out = {
      enabled: false,
      anchor_date_local: '',
      anchor_phase: '',
      anchor_day: 0,
      active_duration: 0,
      pause_duration: 0,
      cycle_length: 0
    };

    if (typeof raw === 'string') {
      raw = raw.trim();
      if (!raw) {
        return out;
      }
      var decoded = safeJsonParse(raw);
      if (!decoded || typeof decoded !== 'object') {
        return out;
      }
      raw = decoded;
    }

    if (!raw || typeof raw !== 'object') {
      return out;
    }

    var enabledVal = null;
    if (Object.prototype.hasOwnProperty.call(raw, 'enabled')) {
      enabledVal = raw.enabled;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'is_enabled')) {
      enabledVal = raw.is_enabled;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'on')) {
      enabledVal = raw.on;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'active')) {
      enabledVal = raw.active;
    }

    if (!boolish(enabledVal)) {
      return out;
    }

    var anchorDate = '';
    ['anchor_date_local', 'anchor_date', 'anchor_local_date', 'anchor_date_ymd', 'date'].some(function (key) {
      if (raw[key] != null) {
        anchorDate = String(raw[key]).trim();
        return true;
      }
      return false;
    });
    if (anchorDate.length >= 10) {
      anchorDate = anchorDate.slice(0, 10);
    }
    if (!isValidYmd(anchorDate)) {
      return out;
    }

    var anchorPhase = '';
    if (raw.anchor_phase != null) {
      anchorPhase = String(raw.anchor_phase).trim().toLowerCase();
    } else if (raw.phase != null) {
      anchorPhase = String(raw.phase).trim().toLowerCase();
    }
    if (anchorPhase === 'paused') {
      anchorPhase = 'pause';
    }
    if (anchorPhase !== 'active' && anchorPhase !== 'pause') {
      return out;
    }

    var anchorDay = 0;
    ['anchor_day', 'anchor_day_within_phase', 'anchor_day_in_phase', 'day', 'day_in_phase'].some(function (key) {
      if (raw[key] != null) {
        anchorDay = parseInt(raw[key], 10) || 0;
        return true;
      }
      return false;
    });

    var activeDuration = 0;
    ['active_duration', 'active_days', 'active_length', 'active_span'].some(function (key) {
      if (raw[key] != null) {
        activeDuration = parseInt(raw[key], 10) || 0;
        return true;
      }
      return false;
    });

    var pauseDuration = 0;
    ['pause_duration', 'pause_days', 'pause_length', 'pause_span'].some(function (key) {
      if (raw[key] != null) {
        pauseDuration = parseInt(raw[key], 10) || 0;
        return true;
      }
      return false;
    });

    if (anchorDay <= 0 || activeDuration <= 0 || pauseDuration <= 0) {
      return out;
    }

    var phaseLength = anchorPhase === 'active' ? activeDuration : pauseDuration;
    if (anchorDay > phaseLength) {
      return out;
    }

    out.enabled = true;
    out.anchor_date_local = anchorDate;
    out.anchor_phase = anchorPhase;
    out.anchor_day = anchorDay;
    out.active_duration = activeDuration;
    out.pause_duration = pauseDuration;
    out.cycle_length = activeDuration + pauseDuration;
    return out;
  }

  function normalizeAvailabilityFromPayload(payload) {
    if (!payload || typeof payload !== 'object') {
      return normalizeAvailability(null);
    }
    if (payload.availability_cycle_json) {
      return normalizeAvailability(payload.availability_cycle_json);
    }
    if (payload.availability_cycle) {
      return normalizeAvailability(payload.availability_cycle);
    }
    if (payload.availability) {
      return normalizeAvailability(payload.availability);
    }
    return normalizeAvailability(null);
  }

  function availabilityStateOnLocalDate(cfg, localYmd) {
    var norm = normalizeAvailability(cfg);
    if (!norm.enabled || !isValidYmd(localYmd)) {
      return { enabled: false };
    }

    var cycleLength = norm.cycle_length;
    if (!cycleLength || cycleLength < 1) {
      return { enabled: false };
    }

    var anchorPhaseStartLocal = addDaysYmd(norm.anchor_date_local, 1 - norm.anchor_day);
    if (!isValidYmd(anchorPhaseStartLocal)) {
      return { enabled: false };
    }

    var cycleActiveStartLocal = norm.anchor_phase === 'active'
      ? anchorPhaseStartLocal
      : addDaysYmd(anchorPhaseStartLocal, -1 * norm.active_duration);

    if (!isValidYmd(cycleActiveStartLocal)) {
      return { enabled: false };
    }

    var diffDays = daysBetweenYmd(cycleActiveStartLocal, localYmd);
    var cycleDayIndex = positiveMod(diffDays, cycleLength);
    var phase = 'active';
    var phaseLength = norm.active_duration;
    var phaseOffset = cycleDayIndex;

    if (cycleDayIndex >= norm.active_duration) {
      phase = 'pause';
      phaseLength = norm.pause_duration;
      phaseOffset = cycleDayIndex - norm.active_duration;
    }

    var phaseDay = phaseOffset + 1;
    var currentPhaseStartLocal = addDaysYmd(localYmd, -1 * phaseOffset);
    var currentPhaseEndLocalExclusive = addDaysYmd(currentPhaseStartLocal, phaseLength);
    var daysRemainingInPhase = daysBetweenYmd(localYmd, currentPhaseEndLocalExclusive);
    if (daysRemainingInPhase < 0) {
      daysRemainingInPhase = 0;
    }

    var nextActiveStartLocal = phase === 'active' ? localYmd : currentPhaseEndLocalExclusive;
    var daysUntilResume = phase === 'pause' ? daysBetweenYmd(localYmd, nextActiveStartLocal) : 0;
    if (daysUntilResume < 0) {
      daysUntilResume = 0;
    }

    return {
      enabled: true,
      reference_local_date: localYmd,
      anchor_date_local: norm.anchor_date_local,
      anchor_phase: norm.anchor_phase,
      anchor_day: norm.anchor_day,
      active_duration: norm.active_duration,
      pause_duration: norm.pause_duration,
      cycle_length: cycleLength,
      phase: phase,
      is_active: phase === 'active',
      is_paused: phase === 'pause',
      phase_day: phaseDay,
      phase_length: phaseLength,
      cycle_day_index: cycleDayIndex,
      day_in_cycle: cycleDayIndex + 1,
      current_phase_start_local: currentPhaseStartLocal,
      current_phase_end_local_exclusive: currentPhaseEndLocalExclusive,
      days_remaining_in_phase: daysRemainingInPhase,
      days_until_resume: daysUntilResume,
      next_active_start_local: nextActiveStartLocal
    };
  }

  function pauseRangeContainsModClass(cfg, mod, klass) {
    var modulus = parseInt(mod, 10) || 0;
    if (modulus <= 0) {
      return false;
    }

    var active = cfg && cfg.active_duration != null ? parseInt(cfg.active_duration, 10) || 0 : 0;
    var pause = cfg && cfg.pause_duration != null ? parseInt(cfg.pause_duration, 10) || 0 : 0;
    if (pause <= 0) {
      return false;
    }
    if (pause >= modulus) {
      return true;
    }

    var modClass = positiveMod(klass, modulus);
    var activeMod = positiveMod(active, modulus);
    var delta = positiveMod(modClass - activeMod, modulus);
    var firstMatch = active + delta;
    var pauseEnd = active + pause - 1;
    return firstMatch <= pauseEnd;
  }

  function normalizeDueSchedule(raw, todayYmd) {
    var out = {
      enabled: false,
      type: '',
      start_date: '',
      every: 1,
      day_of_month: 1,
      effective_from: ''
    };

    if (typeof raw === 'string') {
      raw = raw.trim();
      if (!raw) {
        return out;
      }
      var decoded = safeJsonParse(raw);
      if (!decoded || typeof decoded !== 'object') {
        return out;
      }
      raw = decoded;
    }

    if (!raw || typeof raw !== 'object') {
      return out;
    }

    var enabledVal = null;
    if (Object.prototype.hasOwnProperty.call(raw, 'enabled')) {
      enabledVal = raw.enabled;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'is_enabled')) {
      enabledVal = raw.is_enabled;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'on')) {
      enabledVal = raw.on;
    } else if (Object.prototype.hasOwnProperty.call(raw, 'active')) {
      enabledVal = raw.active;
    }
    out.enabled = boolish(enabledVal);

    var type = '';
    if (raw.type != null) {
      type = String(raw.type).trim().toLowerCase();
    } else if (raw.schedule_type != null) {
      type = String(raw.schedule_type).trim().toLowerCase();
    }
    if (type === 'week' || type === 'weekly' || type === 'w') {
      type = 'weekly';
    } else if (type === 'month' || type === 'monthly' || type === 'm') {
      type = 'monthly';
    } else {
      type = '';
    }
    out.type = type;

    var start = '';
    ['start_date', 'start', 'dtstart', 'start_ymd'].some(function (key) {
      if (raw[key] != null) {
        start = String(raw[key]).trim();
        return true;
      }
      return false;
    });
    if (start.length >= 10) {
      start = start.slice(0, 10);
    }
    if (isValidYmd(start)) {
      out.start_date = start;
    }

    var effectiveFrom = '';
    ['effective_from', 'effective_from_ymd', 'effective_from_local', 'effective_from_date'].some(function (key) {
      if (raw[key] != null) {
        effectiveFrom = String(raw[key]).trim();
        return true;
      }
      return false;
    });
    if (effectiveFrom.length >= 10) {
      effectiveFrom = effectiveFrom.slice(0, 10);
    }
    if (isValidYmd(effectiveFrom)) {
      out.effective_from = effectiveFrom;
    }

    if (out.type === 'weekly') {
      var every = 1;
      ['every', 'every_weeks', 'weekly_every', 'weekly_interval', 'interval_weeks', 'n'].some(function (key) {
        if (raw[key] != null) {
          every = parseInt(raw[key], 10) || 1;
          return true;
        }
        return false;
      });
      if (every < 1) {
        every = 1;
      }
      if (every > 52) {
        every = 52;
      }
      out.every = every;
    } else if (out.type === 'monthly') {
      var dom = 0;
      ['day_of_month', 'dom', 'day', 'monthly_day'].some(function (key) {
        if (raw[key] != null) {
          dom = parseInt(raw[key], 10) || 0;
          return true;
        }
        return false;
      });
      if (dom < 1 || dom > 31) {
        if (isValidYmd(out.start_date)) {
          dom = parseInt(out.start_date.slice(8, 10), 10) || 1;
        } else {
          dom = 1;
        }
      }
      if (dom < 1) {
        dom = 1;
      }
      if (dom > 31) {
        dom = 31;
      }
      out.day_of_month = dom;
    }

    if (out.enabled) {
      if (!out.type || !out.start_date) {
        out.enabled = false;
      } else {
        var today = isValidYmd(todayYmd) ? todayYmd : ymdToday();
        if (!isValidYmd(out.effective_from)) {
          out.effective_from = compareYmd(out.start_date, today) > 0 ? out.start_date : today;
        }
        if (compareYmd(out.effective_from, out.start_date) < 0) {
          out.effective_from = out.start_date;
        }
      }
    }

    return out;
  }

  function normalizeDueScheduleFromPayload(payload, todayYmd) {
    if (!payload || typeof payload !== 'object') {
      return normalizeDueSchedule(null, todayYmd);
    }
    if (payload.due_schedule) {
      return normalizeDueSchedule(payload.due_schedule, todayYmd);
    }
    if (payload.due_schedule_json) {
      return normalizeDueSchedule(payload.due_schedule_json, todayYmd);
    }
    return normalizeDueSchedule(null, todayYmd);
  }

  function duePatternsEqual(a, b) {
    if (!a || !b) {
      return false;
    }
    return !!a.enabled === !!b.enabled &&
      String(a.type || '') === String(b.type || '') &&
      String(a.start_date || '') === String(b.start_date || '') &&
      (parseInt(a.every, 10) || 0) === (parseInt(b.every, 10) || 0) &&
      (parseInt(a.day_of_month, 10) || 0) === (parseInt(b.day_of_month, 10) || 0);
  }

  function dueScheduleNextDueLocalDate(cfg, fromLocalYmd) {
    var norm = normalizeDueSchedule(cfg, ymdToday());
    if (!norm.enabled) {
      return null;
    }

    var fromYmd = isValidYmd(fromLocalYmd) ? fromLocalYmd : ymdToday();
    if (compareYmd(fromYmd, norm.effective_from) < 0) {
      fromYmd = norm.effective_from;
    }
    if (compareYmd(fromYmd, norm.start_date) < 0) {
      fromYmd = norm.start_date;
    }

    if (norm.type === 'weekly') {
      var every = parseInt(norm.every, 10) || 1;
      if (every < 1) {
        every = 1;
      }
      var periodDays = 7 * every;
      var diffDays = daysBetweenYmd(norm.start_date, fromYmd);
      if (diffDays < 0) {
        return norm.start_date;
      }
      var remainder = diffDays % periodDays;
      return remainder === 0 ? fromYmd : addDaysYmd(fromYmd, periodDays - remainder);
    }

    if (norm.type === 'monthly') {
      var parsed = parseYmd(fromYmd);
      if (!parsed) {
        return null;
      }
      var dueDay = monthDueDay(parsed.y, parsed.m, norm.day_of_month);
      var candidate = formatYmd(new Date(Date.UTC(parsed.y, parsed.m - 1, dueDay)));
      if (compareYmd(candidate, fromYmd) < 0) {
        var nextMonth = parsed.m + 1;
        var nextYear = parsed.y;
        if (nextMonth > 12) {
          nextMonth = 1;
          nextYear += 1;
        }
        var dueDay2 = monthDueDay(nextYear, nextMonth, norm.day_of_month);
        candidate = formatYmd(new Date(Date.UTC(nextYear, nextMonth - 1, dueDay2)));
      }
      return candidate;
    }

    return null;
  }

  function availabilityConflictDetails(cfg, dueCfg, fromLocalYmd) {
    var availability = normalizeAvailability(cfg);
    var due = normalizeDueSchedule(dueCfg, fromLocalYmd);

    var out = {
      availability: availability,
      due_schedule: due,
      checked: false,
      exact: false,
      has_conflict: false,
      checked_from_local: fromLocalYmd,
      first_due_local: '',
      first_conflict_local: '',
      schedule_type: due.type || '',
      reason: ''
    };

    if (!availability.enabled) {
      out.reason = 'availability_disabled';
      return out;
    }
    if (!due.enabled) {
      out.reason = 'due_schedule_disabled';
      return out;
    }

    var cycleLength = availability.cycle_length;
    if (!cycleLength || cycleLength < 1) {
      out.reason = 'availability_invalid';
      return out;
    }

    var firstDue = dueScheduleNextDueLocalDate(due, fromLocalYmd);
    if (!isValidYmd(firstDue)) {
      out.reason = 'no_future_due';
      return out;
    }

    out.checked = true;
    out.first_due_local = firstDue;

    if (due.type === 'weekly') {
      var periodDays = 7 * (parseInt(due.every, 10) || 1);
      if (periodDays < 1) {
        periodDays = 7;
      }
      var g = gcd(cycleLength, periodDays);
      var firstState = availabilityStateOnLocalDate(availability, firstDue);
      out.exact = true;

      if (firstState && firstState.is_paused) {
        out.has_conflict = true;
        out.first_conflict_local = firstDue;
        return out;
      }

      var residue = firstState && firstState.cycle_day_index != null ? parseInt(firstState.cycle_day_index, 10) : -1;
      var klass = positiveMod(residue, g);
      out.has_conflict = pauseRangeContainsModClass(availability, g, klass);

      if (out.has_conflict) {
        var orbitSize = Math.floor(cycleLength / Math.max(1, g));
        var scanLimit = Math.min(4096, Math.max(1, orbitSize));
        var cursor = firstDue;
        for (var i = 0; i < scanLimit; i++) {
          var state = availabilityStateOnLocalDate(availability, cursor);
          if (state && state.is_paused) {
            out.first_conflict_local = cursor;
            break;
          }
          cursor = addDaysYmd(cursor, periodDays);
        }
        if (!out.first_conflict_local) {
          out.reason = 'conflict_exists_future_weekly_orbit';
        }
      } else {
        out.reason = 'no_conflict';
      }

      return out;
    }

    if (due.type === 'monthly') {
      out.exact = true;
      var cursorMonthly = firstDue;
      for (var j = 0; j < 4800; j++) {
        var monthlyState = availabilityStateOnLocalDate(availability, cursorMonthly);
        if (monthlyState && monthlyState.is_paused) {
          out.has_conflict = true;
          out.first_conflict_local = cursorMonthly;
          return out;
        }
        var nextFrom = addDaysYmd(cursorMonthly, 1);
        var nextDue = dueScheduleNextDueLocalDate(due, nextFrom);
        if (!isValidYmd(nextDue) || nextDue === cursorMonthly) {
          break;
        }
        cursorMonthly = nextDue;
      }
      out.reason = 'no_conflict';
      return out;
    }

    out.reason = 'unsupported_schedule_type';
    return out;
  }

  function getIntervalRow(form) {
    if (!form) {
      return null;
    }
    return form.querySelector('[data-tct-interval-row]');
  }

  function isAvailabilityEligible(els) {
    if (!els || !els.form || !els.goalType) {
      return false;
    }

    var goalType = normalizeGoalType(els.goalType.value);
    if (goalType === 'never' || goalType === 'harm_reduction' || goalType === 'positive_no_int') {
      return false;
    }

    var row = getIntervalRow(els.form);
    if (!row) {
      return false;
    }

    var targetEl = row.querySelector('[data-tct-interval-target]');
    var unitEl = row.querySelector('[data-tct-interval-unit]');
    var spanEl = row.querySelector('[data-tct-interval-span]');
    if (!targetEl || !unitEl) {
      return false;
    }

    var target = parseInt(targetEl.value, 10) || 0;
    var span = spanEl ? (parseInt(spanEl.value, 10) || 1) : 1;
    var unit = normalizeUnit(unitEl.value || '');

    return target > 0 && !!unit && span > 0;
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
    if (els.anchorPhase && !els.anchorPhase.value) {
      els.anchorPhase.value = 'active';
    }
    if (els.anchorDay && !els.anchorDay.value) {
      els.anchorDay.value = '1';
    }
    if (els.activeDuration && !els.activeDuration.value) {
      els.activeDuration.value = '7';
    }
    if (els.pauseDuration && !els.pauseDuration.value) {
      els.pauseDuration.value = '7';
    }
  }

  function applyAvailabilityConfig(els, cfg) {
    if (!els) {
      return;
    }
    var norm = normalizeAvailability(cfg);
    if (els.enabled) {
      els.enabled.value = norm.enabled ? '1' : '0';
    }
    if (els.anchorDate) {
      els.anchorDate.value = norm.enabled ? norm.anchor_date_local : ymdToday();
    }
    if (els.anchorPhase) {
      els.anchorPhase.value = norm.enabled ? norm.anchor_phase : 'active';
    }
    if (els.anchorDay) {
      els.anchorDay.value = String(norm.enabled ? norm.anchor_day : 1);
    }
    if (els.activeDuration) {
      els.activeDuration.value = String(norm.enabled ? norm.active_duration : 7);
    }
    if (els.pauseDuration) {
      els.pauseDuration.value = String(norm.enabled ? norm.pause_duration : 7);
    }
  }

  function rememberOriginalConfigs(els, payload) {
    if (!els || !els.modal) {
      return;
    }
    var today = ymdToday();
    els.modal.__tctAvailabilityOriginalPayload = payload || null;
    els.modal.__tctAvailabilityOriginalConfig = normalizeAvailabilityFromPayload(payload);
    els.modal.__tctAvailabilityOriginalDueConfig = normalizeDueScheduleFromPayload(payload, today);
  }

  function currentDueConfig(els) {
    if (!els || !els.dueEnabled || String(els.dueEnabled.value || '0') !== '1') {
      return normalizeDueSchedule(null, ymdToday());
    }

    var start = els.dueStart ? String(els.dueStart.value || '').trim() : '';
    var type = els.dueType ? String(els.dueType.value || 'weekly').trim() : 'weekly';
    var every = els.dueEvery ? (parseInt(els.dueEvery.value, 10) || 1) : 1;
    var dom = els.dueDom ? (parseInt(els.dueDom.value, 10) || 1) : 1;
    var today = ymdToday();

    var cfg = normalizeDueSchedule({
      enabled: 1,
      type: type,
      start_date: start,
      every: every,
      day_of_month: dom
    }, today);

    if (!cfg.enabled) {
      return cfg;
    }

    var original = els.modal ? els.modal.__tctAvailabilityOriginalDueConfig : null;
    if (original && original.enabled && duePatternsEqual(original, cfg) && isValidYmd(original.effective_from)) {
      cfg.effective_from = compareYmd(original.effective_from, cfg.start_date) < 0 ? cfg.start_date : original.effective_from;
    } else {
      cfg.effective_from = compareYmd(cfg.start_date, today) > 0 ? cfg.start_date : today;
    }

    return cfg;
  }

  function buildAvailabilityConfigFromForm(els) {
    if (!els) {
      return normalizeAvailability(null);
    }
    return normalizeAvailability({
      enabled: els.enabled ? els.enabled.value : 0,
      anchor_date_local: els.anchorDate ? els.anchorDate.value : '',
      anchor_phase: els.anchorPhase ? els.anchorPhase.value : '',
      anchor_day: els.anchorDay ? els.anchorDay.value : 0,
      active_duration: els.activeDuration ? els.activeDuration.value : 0,
      pause_duration: els.pauseDuration ? els.pauseDuration.value : 0
    });
  }

  function validateCurrent(els, showMessages) {
    if (!els || !els.row) {
      return { ok: true, message: '' };
    }

    var enabled = !!(els.enabled && els.enabled.value === '1');
    var eligible = isAvailabilityEligible(els);
    var originalEnabled = !!(els.modal && els.modal.__tctAvailabilityOriginalConfig && els.modal.__tctAvailabilityOriginalConfig.enabled);

    if (!enabled) {
      if (showMessages) {
        setWarning(els, '');
      }
      return { ok: true, message: '' };
    }

    if (!eligible) {
      var ineligibleMessage = 'Availability cycles are only available for positive interval goals. Change the goal back to a positive interval goal or switch the Active / Pause cycle to Off before saving.';
      if (showMessages) {
        setWarning(els, ineligibleMessage);
      }
      return { ok: false, message: ineligibleMessage, originalEnabled: originalEnabled };
    }

    var cfg = buildAvailabilityConfigFromForm(els);
    if (!cfg.enabled) {
      var invalidMessage = 'Invalid availability cycle. Please provide a valid anchor date, phase, day, active duration, and pause duration.';
      if (showMessages) {
        setWarning(els, invalidMessage);
      }
      return { ok: false, message: invalidMessage };
    }

    var dueCfg = currentDueConfig(els);
    if (dueCfg.enabled) {
      var details = availabilityConflictDetails(cfg, dueCfg, ymdToday());
      if (details.checked && details.has_conflict) {
        var conflictMessage = 'Availability cycle conflicts with the due schedule because a future due date would land during a paused block.';
        if (details.first_conflict_local) {
          conflictMessage = 'Availability cycle conflicts with the due schedule on ' + details.first_conflict_local + ' because that due date would land during a paused block.';
        }
        if (showMessages) {
          setWarning(els, conflictMessage);
        }
        return { ok: false, message: conflictMessage };
      }
    }

    if (showMessages) {
      setWarning(els, '');
    }
    return { ok: true, message: '' };
  }

  function updateAvailabilityUI() {
    var els = getEls();
    if (!els || !els.row || !els.enabled || !els.fields) {
      return;
    }

    resetDefaults(els);

    var enabled = els.enabled.value === '1';
    var eligible = isAvailabilityEligible(els);
    var originalEnabled = !!(els.modal && els.modal.__tctAvailabilityOriginalConfig && els.modal.__tctAvailabilityOriginalConfig.enabled);
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
      setSummary(els, 'Anchor date is interpreted in local site time. Manual completions logged during pause will count when the next active block starts.');
    } else {
      hide(els.fields);
      setFieldsetDisabled(els.fields, true);
      if (eligible) {
        setSummary(els, 'Available for positive interval goals. Turn it on to freeze vitality, urgency, and penalties during paused blocks.');
      } else {
        setSummary(els, 'This goal type cannot use an Active / Pause cycle. Change the goal back to a positive interval goal or switch the cycle to Off before saving.');
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
      rememberOriginalConfigs(els, payload);
      if (mode === 'edit' && payload) {
        applyAvailabilityConfig(els, normalizeAvailabilityFromPayload(payload));
      } else {
        applyAvailabilityConfig(els, normalizeAvailability(null));
      }
      updateAvailabilityUI();
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

    rememberOriginalConfigs(els, null);
    resetDefaults(els);
    updateAvailabilityUI();

    document.addEventListener('click', onOpenClick, true);
    document.addEventListener(
      'change',
      function (event) {
        if (!event || !event.target || !event.target.closest) {
          return;
        }
        if (event.target.closest('[data-tct-goal-modal]')) {
          updateAvailabilityUI();
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
          updateAvailabilityUI();
        }
      },
      true
    );
    els.form.addEventListener('submit', onFormSubmit, true);

    if (els.intervalContainer && window.MutationObserver) {
      var observer = new MutationObserver(function () {
        updateAvailabilityUI();
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
