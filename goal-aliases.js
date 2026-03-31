/* global jQuery, tctDashboard */
/**
 * Goal Aliases + OpenAI Suggestions
 *
 * This file is intentionally standalone so we don't have to modify the (minified) dashboard.js.
 */
(function ($) {
  'use strict';

  function getGoalModal() {
    return document.querySelector('[data-tct-goal-modal]');
  }

  function getAliasesRow(modal) {
    return modal ? modal.querySelector('[data-tct-aliases-row]') : null;
  }

  function getAliasesList(modal) {
    return modal ? modal.querySelector('[data-tct-aliases-list]') : null;
  }

  function getGoalNameInput(modal) {
    return modal ? modal.querySelector('[data-tct-goal-name]') : null;
  }

  function clearAliases(modal) {
    var list = getAliasesList(modal);
    if (list) {
      list.innerHTML = '';
    }
  }

  function normalizeAlias(s) {
    return (s || '').trim().toLowerCase();
  }

  function dedupeAliases(arr) {
    var out = [];
    var seen = Object.create(null);
    (arr || []).forEach(function (a) {
      var v = (a || '').trim();
      if (!v) return;
      var k = normalizeAlias(v);
      if (!k) return;
      if (seen[k]) return;
      seen[k] = true;
      out.push(v);
    });
    return out;
  }

  function getAliasesFromInputs(modal) {
    var list = getAliasesList(modal);
    if (!list) return [];
    var inputs = list.querySelectorAll('input[name="aliases[]"]');
    var out = [];
    inputs.forEach(function (inp) {
      var v = (inp && inp.value ? inp.value : '').trim();
      if (v) out.push(v);
    });
    return out;
  }

  function addAliasRow(modal, value) {
    var list = getAliasesList(modal);
    if (!list) return null;

    var row = document.createElement('div');
    row.className = 'tct-alias-item';

    var input = document.createElement('input');
    input.type = 'text';
    input.name = 'aliases[]';
    input.className = 'tct-select';
    input.placeholder = 'Alias';
    input.value = (value || '').trim();

    var remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'button tct-alias-remove';
    remove.textContent = 'Remove';
    remove.addEventListener('click', function () {
      row.remove();
    });

    row.appendChild(input);
    row.appendChild(remove);
    list.appendChild(row);

    return input;
  }

  function renderAliases(modal, aliases) {
    clearAliases(modal);
    (aliases || []).forEach(function (a) {
      addAliasRow(modal, a);
    });
  }

  function ensureStatusEl(modal) {
    var row = getAliasesRow(modal);
    if (!row) return null;

    var el = row.querySelector('[data-tct-aliases-status]');
    if (el) return el;

    el = document.createElement('div');
    el.setAttribute('data-tct-aliases-status', '1');
    el.className = 'tct-muted tct-settings-help';
    el.style.marginTop = '6px';
    row.appendChild(el);
    return el;
  }

  function setStatus(modal, msg, isError) {
    var el = ensureStatusEl(modal);
    if (!el) return;
    el.textContent = msg || '';
    el.style.color = isError ? '#842029' : '';
  }

  function setSuggestLoading(modal, isLoading) {
    var btn = modal ? modal.querySelector('[data-tct-suggest-aliases]') : null;
    if (!btn) return;

    if (isLoading) {
      btn.disabled = true;
      btn.textContent = 'Suggesting...';
      btn.setAttribute('data-loading', '1');
    } else {
      btn.disabled = false;
      btn.textContent = 'Suggest aliases';
      btn.removeAttribute('data-loading');
    }
  }

  function ajaxUrl() {
    if (window.tctDashboard && window.tctDashboard.ajaxUrl) return window.tctDashboard.ajaxUrl;
    if (window.ajaxurl) return window.ajaxurl;
    return '/wp-admin/admin-ajax.php';
  }

  function suggestAliases(modal) {
    var btn = modal ? modal.querySelector('[data-tct-suggest-aliases]') : null;
    if (!btn) return;

    var nonce = btn.getAttribute('data-tct-suggest-aliases-nonce') || '';
    var nameInput = getGoalNameInput(modal);
    var title = (nameInput && nameInput.value ? nameInput.value : '').trim();

    if (!title) {
      setStatus(modal, 'Enter a goal name first.', true);
      return;
    }

    setStatus(modal, 'Contacting OpenAI...', false);
    setSuggestLoading(modal, true);

    $.post(ajaxUrl(), {
      action: 'tct_suggest_aliases',
      nonce: nonce,
      title: title
    })
      .done(function (resp) {
        setSuggestLoading(modal, false);

        if (resp && resp.success && resp.data && Array.isArray(resp.data.aliases)) {
          var existing = getAliasesFromInputs(modal);
          var merged = dedupeAliases(existing.concat(resp.data.aliases));
          renderAliases(modal, merged);

          if (resp.data.aliases.length) {
            setStatus(modal, 'Suggested ' + resp.data.aliases.length + ' aliases. Edit/remove them, then save the goal.', false);
          } else {
            setStatus(modal, 'No suggestions returned.', false);
          }
        } else {
          var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Could not get alias suggestions.';
          setStatus(modal, msg, true);
        }
      })
      .fail(function (xhr) {
        setSuggestLoading(modal, false);
        var msg = 'Request failed.';
        try {
          if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
            msg = xhr.responseJSON.data.message;
          }
        } catch (e) {}
        setStatus(modal, msg, true);
      });
  }

  // Populate aliases when opening the goal modal (edit/add).
  // We wait a tick because the main dashboard script populates the form fields.
  document.addEventListener(
    'click',
    function (e) {
      var target = e.target;
      if (!target || !target.closest) return;

      var openBtn = target.closest('[data-tct-open-goal-modal]');
      if (!openBtn) return;

      var mode = openBtn.getAttribute('data-tct-open-goal-modal');
      var payloadStr = openBtn.getAttribute('data-tct-goal');

      setTimeout(function () {
        var modal = getGoalModal();
        if (!modal) return;

        // Clear any old status.
        setStatus(modal, '', false);

        if (mode === 'add') {
          clearAliases(modal);
          return;
        }

        if (mode === 'edit' && payloadStr) {
          try {
            var goal = JSON.parse(payloadStr);
            var aliases = Array.isArray(goal.aliases) ? goal.aliases : [];
            renderAliases(modal, dedupeAliases(aliases));
          } catch (err) {
            // ignore
          }
        }
      }, 0);
    },
    true
  );

  // Add alias button
  document.addEventListener('click', function (e) {
    var target = e.target;
    if (!target || !target.closest) return;
    var btn = target.closest('[data-tct-add-alias]');
    if (!btn) return;

    var modal = getGoalModal();
    if (!modal) return;
    var input = addAliasRow(modal, '');
    if (input) input.focus();
  });

  // Suggest aliases button
  document.addEventListener('click', function (e) {
    var target = e.target;
    if (!target || !target.closest) return;
    var btn = target.closest('[data-tct-suggest-aliases]');
    if (!btn) return;

    var modal = getGoalModal();
    if (!modal) return;
    suggestAliases(modal);
  });
})(jQuery);
