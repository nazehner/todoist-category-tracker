/* global jQuery, tctDashboard */
(function ($) {
  'use strict';

  function getCfg() {
    if (typeof window.tctDashboard === 'object' && window.tctDashboard) {
      return window.tctDashboard;
    }
    return {};
  }

  function statusEl(type) {
    return $('[data-tct-goal-order-status="' + type + '"]');
  }

  function setStatus(type, msg, isError) {
    var $el = statusEl(type);
    if (!$el.length) return;

    if (!msg) {
      $el.text('');
      $el.removeClass('tct-error');
      return;
    }

    $el.text(msg);
    if (isError) {
      $el.addClass('tct-error');
    } else {
      $el.removeClass('tct-error');
    }
  }

  function collectOrder(type) {
    var $table = $('[data-tct-goal-order-table="' + type + '"]');
    if (!$table.length) return [];

    var ids = [];
    $table.find('tbody tr').each(function () {
      var id = parseInt($(this).attr('data-goal-id'), 10);
      if (id && id > 0) ids.push(id);
    });

    return ids;
  }

  var timers = {};

  function ajaxErrorMessage(cfg) {
    if (cfg && cfg.i18n && cfg.i18n.goalOrderError) {
      return cfg.i18n.goalOrderError;
    }
    return 'Could not save goal order. Please refresh and try again.';
  }

  function saveOrder(type) {
    var cfg = getCfg();
    if (!cfg.ajaxUrl || !cfg.goalOrderNonce) return;

    var ids = collectOrder(type);

    setStatus(type, 'Saving…', false);

    $.post(
      cfg.ajaxUrl,
      {
        action: 'tct_save_goal_order',
        nonce: cfg.goalOrderNonce,
        type: type,
        order: JSON.stringify(ids)
      },
      function (resp) {
        if (resp && resp.success) {
          setStatus(type, 'Saved.', false);
          window.clearTimeout(timers[type + '_clear']);
          timers[type + '_clear'] = window.setTimeout(function () {
            setStatus(type, '', false);
          }, 1500);
        } else {
          setStatus(type, ajaxErrorMessage(cfg), true);
        }
      },
      'json'
    ).fail(function () {
      setStatus(type, ajaxErrorMessage(cfg), true);
    });
  }

  function debounceSave(type) {
    window.clearTimeout(timers[type]);
    timers[type] = window.setTimeout(function () {
      saveOrder(type);
    }, 250);
  }

  function initSortableForType(type) {
    var $table = $('[data-tct-goal-order-table="' + type + '"]');
    if (!$table.length) return;

    // Sortable can be finicky when initialized while the Settings tab panel is hidden.
    // If we're currently hidden, wait until the tab is opened.
    if (!$table.is(':visible')) return;

    var $tbody = $table.find('tbody');
    if (!$tbody.length) return;

    if ($tbody.data('tctGoalOrderInit')) return;
    $tbody.data('tctGoalOrderInit', true);

    try {
      $tbody.sortable({
        items: '> tr',
        handle: '.tct-drag-handle',
        placeholder: 'tct-sort-placeholder',
        axis: 'y',
        helper: function (e, tr) {
          // Keep cell widths when dragging.
          var $originals = tr.children();
          var $helper = tr.clone();
          $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width());
          });
          return $helper;
        },
        update: function () {
          debounceSave(type);
        }
      });
    } catch (e) {
      // jQuery UI not available.
    }
  }

  function initAll() {
    initSortableForType('daily');
    initSortableForType('favorites');
  }

  $(function () {
    initAll();

    // The Settings tab is keyed as "connection" in the dashboard tab system.
    // Re-init after switching to that tab because sortable can be finicky when initialized while hidden.
    $(document).on('click', '[data-tct-tab="connection"]', function () {
      window.setTimeout(initAll, 50);
    });
  });
})(jQuery);
