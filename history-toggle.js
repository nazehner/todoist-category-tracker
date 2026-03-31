(function () {
  "use strict";

  if (typeof window === "undefined" || typeof document === "undefined") {
    return;
  }

  function hasDashboardConfig() {
    return (
      window.tctDashboard &&
      window.tctDashboard.ajaxUrl &&
      window.tctDashboard.undoCompletionNonce
    );
  }

  function getToastRoot() {
    return (
      document.querySelector(".tct-dashboard") ||
      document.querySelector("body") ||
      document.documentElement
    );
  }

  function getToastEl() {
    var root = getToastRoot();
    var el = root ? root.querySelector("[data-tct-toast]") : null;
    if (!el) {
      el = document.createElement("div");
      el.className = "tct-toast";
      el.setAttribute("data-tct-toast", "1");
      el.setAttribute("hidden", "hidden");
      if (root) {
        root.appendChild(el);
      } else {
        document.body.appendChild(el);
      }
    }
    return el;
  }

  function showToast(message, isError) {
    var el = getToastEl();
    el.textContent = message ? String(message) : "";
    el.classList.remove("tct-toast-error");
    if (isError) {
      el.classList.add("tct-toast-error");
    }
    el.removeAttribute("hidden");
    window.clearTimeout(el._tctTimer);
    el._tctTimer = window.setTimeout(function () {
      el.setAttribute("hidden", "hidden");
    }, 3500);
  }

  function isFailishLabel(label) {
    if (!label) return false;
    var t = String(label).toLowerCase();
    return t.indexOf("fail") !== -1 || t.indexOf("miss") !== -1;
  }

  function buildSwitchButton(toState, completionId, goalId) {
    var btn = document.createElement("button");
    btn.type = "button";
    btn.className = "tct-history-undo-btn tct-history-switch-btn";
    btn.setAttribute("data-tct-switch-completion", "1");
    btn.setAttribute("data-switch-to", toState);
    btn.setAttribute("data-completion-id", completionId ? String(completionId) : "");
    btn.setAttribute("data-goal-id", goalId ? String(goalId) : "");

    updateSwitchButtonPresentation(btn, toState);
    return btn;
  }

  function updateSwitchButtonPresentation(btn, toState) {
    var isToFail = toState === "fail";
    var aria = isToFail ? "Mark as fail" : "Mark as complete";
    var title = isToFail ? "Fail" : "Complete";
    var icon = isToFail ? "dashicons-dismiss" : "dashicons-yes";
    btn.setAttribute("aria-label", aria);
    btn.title = title;
    btn.innerHTML =
      '<span class="dashicons ' + icon + '" aria-hidden="true"></span>';
  }

  function updatePointsCell(pointsCell, points) {
    if (!pointsCell) return;
    var p = parseInt(points, 10);
    if (isNaN(p)) p = 0;

    pointsCell.classList.remove("tct-points-positive", "tct-points-negative");
    if (p === 0) {
      pointsCell.textContent = "0";
      return;
    }
    pointsCell.textContent = (p > 0 ? "+" : "") + String(p);
    pointsCell.classList.add(p > 0 ? "tct-points-positive" : "tct-points-negative");
  }

  function recomputeHistorySummary(modal) {
    if (!modal) return;
    var summaryEl = modal.querySelector("[data-tct-history-summary]");
    if (!summaryEl) return;

    var tbody = modal.querySelector("[data-tct-history-completions] tbody");
    if (!tbody) return;

    var rows = tbody.querySelectorAll("tr");
    var total = 0;
    for (var i = 0; i < rows.length; i++) {
      var tr = rows[i];
      var tds = tr.querySelectorAll("td");
      if (tds.length < 3) continue;
      var txt = (tds[2].textContent || "").trim();
      if (!txt) continue;
      var n = parseInt(txt.replace("+", ""), 10);
      if (!isNaN(n)) total += n;
    }

    var text = (summaryEl.textContent || "").trim();
    if (!text) {
      summaryEl.textContent = "Total points: " + String(total);
      return;
    }

    var parts = text
      .split("*")
      .map(function (p) {
        return (p || "").trim();
      })
      .filter(function (p) {
        return !!p;
      });

    var replaced = false;
    for (var j = 0; j < parts.length; j++) {
      if (parts[j].toLowerCase().indexOf("total points:") === 0) {
        parts[j] = "Total points: " + String(total);
        replaced = true;
      }
    }
    if (!replaced) {
      parts.push("Total points: " + String(total));
    }
    summaryEl.textContent = parts.join(" * ");
  }

  function enhanceHistoryRows(modal) {
    if (!modal) return;
    var undoButtons = modal.querySelectorAll(
      "[data-tct-undo-completion=\"1\"]"
    );
    if (!undoButtons || !undoButtons.length) return;

    for (var i = 0; i < undoButtons.length; i++) {
      var undoBtn = undoButtons[i];
      var td = undoBtn.parentElement;
      if (!td) continue;
      if (td.querySelector("[data-tct-switch-completion=\"1\"]")) continue;

      var tr = td.parentElement;
      if (!tr) continue;
      var tds = tr.querySelectorAll("td");
      var sourceLabel = tds.length > 1 ? (tds[1].textContent || "").trim() : "";
      var toState = isFailishLabel(sourceLabel) ? "success" : "fail";

      var completionId = undoBtn.getAttribute("data-completion-id") || "";
      var goalId = undoBtn.getAttribute("data-goal-id") || "";

      var switchBtn = buildSwitchButton(toState, completionId, goalId);
      td.insertBefore(switchBtn, undoBtn);
    }
  }

  function handleSwitchClick(btn) {
    if (!hasDashboardConfig()) {
      showToast("Dashboard config missing.", true);
      return;
    }

    var completionId = parseInt(btn.getAttribute("data-completion-id") || "", 10);
    var goalId = parseInt(btn.getAttribute("data-goal-id") || "", 10);
    var toState = btn.getAttribute("data-switch-to") || "";

    if (!completionId || isNaN(completionId) || !goalId || isNaN(goalId)) {
      showToast("Could not update entry.", true);
      return;
    }
    if (toState !== "fail" && toState !== "success") {
      showToast("Could not update entry.", true);
      return;
    }

    btn.disabled = true;

    var fd = new FormData();
    fd.append("action", "tct_switch_completion_state");
    fd.append("nonce", window.tctDashboard.undoCompletionNonce);
    fd.append("completion_id", String(completionId));
    fd.append("goal_id", String(goalId));
    fd.append("to_state", toState);

    fetch(window.tctDashboard.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
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
              : "Could not update entry.";
          showToast(msg, true);
          return;
        }

        var data = resp.data || {};
        var td = btn.parentElement;
        var tr = td ? td.parentElement : null;
        if (tr) {
          var tds = tr.querySelectorAll("td");
          if (tds.length > 1 && data.sourceLabel) {
            tds[1].textContent = String(data.sourceLabel);
          }
          if (tds.length > 2) {
            updatePointsCell(tds[2], data.points);
          }
        }

        var nextTo = toState === "fail" ? "success" : "fail";
        btn.setAttribute("data-switch-to", nextTo);
        updateSwitchButtonPresentation(btn, nextTo);

        var modal = btn.closest("[data-tct-history-modal]");
        recomputeHistorySummary(modal);

      if (window.tctHistoryStatsRefresh) {
        window.tctHistoryStatsRefresh(goalId);
      }
      })
      .catch(function () {
        showToast("Could not update entry.", true);
      })
      .finally(function () {
        btn.disabled = false;
      });
  }

  function init() {
    if (!hasDashboardConfig()) return;

    var modal = document.querySelector("[data-tct-history-modal]");
    if (modal) {
      enhanceHistoryRows(modal);

      // Watch for history table refreshes (period changes / undo actions).
      var target = modal.querySelector("[data-tct-history-completions]");
      if (target && typeof window.MutationObserver !== "undefined") {
        var obs = new MutationObserver(function () {
          enhanceHistoryRows(modal);
        });
        obs.observe(target, { childList: true, subtree: true });
      }
    }

    document.addEventListener("click", function (ev) {
      var btn = ev.target && ev.target.closest
        ? ev.target.closest('[data-tct-switch-completion="1"]')
        : null;
      if (!btn) return;
      ev.preventDefault();
      if (btn.disabled) return;
      handleSwitchClick(btn);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
