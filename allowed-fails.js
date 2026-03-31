(function () {
  function normalizeUnit(u) {
    if (!u) return '';
    u = String(u).trim().toLowerCase();
    if (u === 'daily') return 'day';
    // Convert plural-ish values to singular.
    if (u.length > 1 && u.endsWith('s')) u = u.slice(0, -1);
    return u;
  }

  function getModal() {
    return document.querySelector('[data-tct-goal-modal]');
  }

  function getForm(modal) {
    if (!modal) return null;
    return modal.querySelector('[data-tct-goal-form]');
  }

  function getEls() {
    var modal = getModal();
    if (!modal) return null;
    var form = getForm(modal);
    if (!form) return null;

    return {
      modal: modal,
      form: form,
      goalType: form.querySelector('[data-tct-goal-type-select]'),
      afRow: form.querySelector('[data-tct-allowed-fails-row]'),
      afTarget: form.querySelector('[data-tct-allowed-fails-target]'),
      afUnit: form.querySelector('[data-tct-allowed-fails-unit]'),
      afSpan: form.querySelector('[data-tct-allowed-fails-span]')
    };
  }

  function allowedFailsEligible(els) {
    if (!els || !els.afRow || !els.afTarget || !els.afUnit || !els.afSpan || !els.goalType || !els.form) return false;

    var t = (els.goalType.value || 'positive').toString();
    if (t !== 'positive') return false;

    var intervalRow = els.form.querySelector('[data-tct-interval-row]');
    if (!intervalRow) return false;

    var targetEl = intervalRow.querySelector('[data-tct-interval-target]');
    var spanEl = intervalRow.querySelector('[data-tct-interval-span]');
    var unitEl = intervalRow.querySelector('[data-tct-interval-unit]');
    if (!targetEl || !spanEl || !unitEl) return false;

    var target = parseInt(targetEl.value, 10);
    var span = parseInt(spanEl.value, 10);
    if (isNaN(span) || span < 1) span = 1;

    var unit = normalizeUnit(unitEl.value);
    return target === 1 && span === 1 && unit === 'day';
  }

  function sanitizeAllowedFailsInputs(els) {
    if (!els) return;

    var t = parseInt(els.afTarget.value, 10);
    if (isNaN(t) || t < 0) t = 0;
    els.afTarget.value = String(t);

    var s = parseInt(els.afSpan.value, 10);
    if (isNaN(s) || s < 1) s = 1;
    els.afSpan.value = String(s);

    var u = normalizeUnit(els.afUnit.value);
    if (u !== 'week' && u !== 'month' && u !== 'year') u = 'week';
    els.afUnit.value = u;
  }

  function updateAllowedFailsUI() {
    var els = getEls();
    if (!els || !els.afRow || !els.afTarget || !els.afUnit || !els.afSpan) return;

    if (allowedFailsEligible(els)) {
      els.afRow.removeAttribute('hidden');
      els.afTarget.removeAttribute('disabled');
      els.afUnit.removeAttribute('disabled');
      els.afSpan.removeAttribute('disabled');

      if (String(els.afTarget.value || '') === '') els.afTarget.value = '0';
      if (String(els.afSpan.value || '') === '') els.afSpan.value = '1';
      if (String(els.afUnit.value || '') === '') els.afUnit.value = 'week';

      sanitizeAllowedFailsInputs(els);
    } else {
      els.afRow.setAttribute('hidden', 'hidden');

      els.afTarget.value = '0';
      els.afUnit.value = 'week';
      els.afSpan.value = '1';

      els.afTarget.setAttribute('disabled', 'disabled');
      els.afUnit.setAttribute('disabled', 'disabled');
      els.afSpan.setAttribute('disabled', 'disabled');
    }
  }

  function applyFromPayload(payload) {
    var els = getEls();
    if (!els || !els.afTarget || !els.afUnit || !els.afSpan) return;

    // Accept both snake_case (PHP) and camelCase (legacy) keys.
    var t = 0;
    var u = 'week';
    var s = 1;

    if (payload && typeof payload === 'object') {
      if (payload.allowed_fails_target != null) t = payload.allowed_fails_target;
      else if (payload.allowedFailsTarget != null) t = payload.allowedFailsTarget;

      if (payload.allowed_fails_unit) u = payload.allowed_fails_unit;
      else if (payload.allowedFailsUnit) u = payload.allowedFailsUnit;

      if (payload.allowed_fails_span != null) s = payload.allowed_fails_span;
      else if (payload.allowedFailsSpan != null) s = payload.allowedFailsSpan;
    }

    var tInt = parseInt(t, 10);
    var sInt = parseInt(s, 10);

    els.afTarget.value = String(isNaN(tInt) ? 0 : tInt);
    els.afUnit.value = normalizeUnit(u) || 'week';
    els.afSpan.value = String(isNaN(sInt) ? 1 : sInt);

    updateAllowedFailsUI();
  }

  function parseGoalDataFromEl(el) {
    if (!el) return null;
    var json = el.getAttribute('data-tct-goal');
    if (!json) return null;
    try {
      return JSON.parse(json);
    } catch (e) {
      return null;
    }
  }

  function onOpenClick(e) {
    if (!e || !e.target || !e.target.closest) return;
    var btn = e.target.closest('[data-tct-open-goal-modal]');
    if (!btn) return;

    var payload = parseGoalDataFromEl(btn);
    // Let the core dashboard JS populate the modal first.
    setTimeout(function () {
      applyFromPayload(payload);
    }, 0);
  }

  function init() {
    // Initial UI state (covers "Add Goal" default modal state).
    updateAllowedFailsUI();

    // Re-run when modal is opened and populated.
    document.addEventListener('click', onOpenClick, true);

    // Re-run when goal type / interval fields change.
    document.addEventListener(
      'change',
      function (e) {
        if (!e || !e.target || !e.target.closest) return;
        if (e.target.closest('[data-tct-goal-modal]')) updateAllowedFailsUI();
      },
      true
    );

    document.addEventListener(
      'input',
      function (e) {
        if (!e || !e.target || !e.target.closest) return;
        if (e.target.closest('[data-tct-goal-modal]')) updateAllowedFailsUI();
      },
      true
    );

    // If intervals are added/removed dynamically, keep eligibility up-to-date.
    var modal = getModal();
    if (modal && window.MutationObserver) {
      var form = getForm(modal);
      if (form) {
        var container = form.querySelector('[data-tct-interval-row-container]');
        if (container) {
          var obs = new MutationObserver(function () {
            updateAllowedFailsUI();
          });
          obs.observe(container, { childList: true, subtree: true });
        }
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();