<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TCT_Mobile_Composite_Scroll {
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_patch' ), 999 );
    }

    public function maybe_enqueue_patch() {
        if ( is_admin() ) {
            return;
        }

        if ( ! function_exists( 'wp_script_is' ) ) {
            return;
        }

        if ( ! wp_script_is( 'tct-mobile-js', 'enqueued' ) ) {
            return;
        }

        $script = <<<'JS'
(function(){
    "use strict";

    if (typeof window === "undefined" || typeof document === "undefined") {
        return;
    }

    var root = document.querySelector(".tct-mobile");
    if (!root) {
        return;
    }

    var results = root.querySelector("[data-tct-mobile-results]");
    if (!results) {
        return;
    }

    window.TCT = window.TCT || {};
    if (window.TCT.__tctMobileCompositeScrollPatchBound) {
        return;
    }
    window.TCT.__tctMobileCompositeScrollPatchBound = true;

    var pendingParentGoalId = 0;
    var pendingClearTimer = 0;
    var scrollQueued = false;

    function parseGoalId(value) {
        var goalId = parseInt(value || "0", 10);
        if (!isFinite(goalId) || goalId <= 0) {
            return 0;
        }
        return goalId;
    }

    function isCompositeParentRow(row) {
        return !!(row && row.getAttribute && row.getAttribute("data-tct-composite-parent") === "1");
    }

    function clearPendingParentGoal() {
        pendingParentGoalId = 0;
        if (pendingClearTimer) {
            window.clearTimeout(pendingClearTimer);
            pendingClearTimer = 0;
        }
    }

    function setPendingParentGoal(goalId) {
        pendingParentGoalId = parseGoalId(goalId);
        if (pendingClearTimer) {
            window.clearTimeout(pendingClearTimer);
            pendingClearTimer = 0;
        }
        if (pendingParentGoalId > 0) {
            pendingClearTimer = window.setTimeout(clearPendingParentGoal, 4000);
        }
    }

    function findParentRowByGoalId(goalId) {
        goalId = parseGoalId(goalId);
        if (goalId <= 0) {
            return null;
        }
        return results.querySelector('.tct-mobile-result[data-tct-composite-parent="1"][data-goal-id="' + String(goalId) + '"]');
    }

    function getCompositeChildrenContainer(parentRow) {
        if (!parentRow || !parentRow.querySelector) {
            return null;
        }
        return parentRow.querySelector("[data-tct-mobile-composite-children], .tct-mobile-composite-children");
    }

    function countVisibleChildRows(parentRow) {
        var container = getCompositeChildrenContainer(parentRow);
        if (!container) {
            return 0;
        }
        return container.querySelectorAll(".tct-mobile-result:not(.tct-mobile-result-pending-remove)").length;
    }

    function scrollParentRow(parentRow) {
        var header = root.querySelector(".tct-mobile-header");
        var top = 0;
        var offset = 8;

        if (!parentRow) {
            return;
        }

        try {
            top = parentRow.getBoundingClientRect().top + window.pageYOffset;
            if (header && header.getBoundingClientRect) {
                top -= header.getBoundingClientRect().height;
            }
            top = Math.max(0, Math.round(top) - offset);
            if (typeof window.scrollTo === "function") {
                window.scrollTo({
                    top: top,
                    behavior: "smooth"
                });
                return;
            }
        } catch (err) {
        }

        if (parentRow.scrollIntoView) {
            try {
                parentRow.scrollIntoView({
                    block: "start",
                    behavior: "smooth"
                });
                return;
            } catch (err2) {
            }
            parentRow.scrollIntoView(true);
        }
    }

    function maybeScrollToPendingParent() {
        var parentRow;

        if (pendingParentGoalId <= 0) {
            return;
        }

        parentRow = findParentRowByGoalId(pendingParentGoalId);
        if (!isCompositeParentRow(parentRow)) {
            clearPendingParentGoal();
            return;
        }

        if (countVisibleChildRows(parentRow) > 0) {
            return;
        }

        scrollParentRow(parentRow);
        clearPendingParentGoal();
    }

    function queueMaybeScrollToPendingParent() {
        if (scrollQueued) {
            return;
        }
        scrollQueued = true;
        window.requestAnimationFrame(function () {
            scrollQueued = false;
            window.setTimeout(maybeScrollToPendingParent, 0);
        });
    }

    function findCompositeParentRowFromChildTarget(target) {
        var childRow;
        var container;

        if (!target || !target.closest) {
            return null;
        }

        childRow = target.closest(".tct-mobile-result");
        if (!childRow || !results.contains(childRow) || isCompositeParentRow(childRow)) {
            return null;
        }

        container = childRow.closest("[data-tct-mobile-composite-children], .tct-mobile-composite-children");
        if (!container) {
            return null;
        }

        return container.closest('.tct-mobile-result[data-tct-composite-parent="1"]');
    }

    results.addEventListener("click", function (event) {
        var target = event.target;
        var parentButton = target && target.closest ? target.closest('[data-tct-composite-parent-complete="1"]') : null;
        var parentRow;
        var childAction;

        if (parentButton && results.contains(parentButton)) {
            parentRow = parentButton.closest(".tct-mobile-result");
            if (isCompositeParentRow(parentRow)) {
                setPendingParentGoal(parentRow.getAttribute("data-goal-id"));
            }
            return;
        }

        childAction = target && target.closest ? target.closest("[data-tct-complete-goal], [data-tct-start-timer], [data-tct-timer-complete], .tct-sleep-primary-btn, [data-tct-mobile-row-complete]") : null;
        if (!childAction || !results.contains(childAction)) {
            return;
        }

        parentRow = findCompositeParentRowFromChildTarget(childAction);
        if (!isCompositeParentRow(parentRow)) {
            return;
        }

        setPendingParentGoal(parentRow.getAttribute("data-goal-id"));
    }, true);

    results.addEventListener("keydown", function (event) {
        var key = event.key || "";
        var target = event.target;
        var parentButton;
        var parentRow;
        var childAction;

        if (key !== "Enter" && key !== " " && key !== "Spacebar") {
            return;
        }

        parentButton = target && target.closest ? target.closest('[data-tct-composite-parent-complete="1"]') : null;
        if (parentButton && results.contains(parentButton)) {
            parentRow = parentButton.closest(".tct-mobile-result");
            if (isCompositeParentRow(parentRow)) {
                setPendingParentGoal(parentRow.getAttribute("data-goal-id"));
            }
            return;
        }

        childAction = target && target.closest ? target.closest("[data-tct-complete-goal], [data-tct-start-timer], [data-tct-timer-complete], .tct-sleep-primary-btn, [data-tct-mobile-row-complete]") : null;
        if (!childAction || !results.contains(childAction)) {
            return;
        }

        parentRow = findCompositeParentRowFromChildTarget(childAction);
        if (!isCompositeParentRow(parentRow)) {
            return;
        }

        setPendingParentGoal(parentRow.getAttribute("data-goal-id"));
    }, true);

    (new MutationObserver(function (records) {
        var shouldCheck = false;

        if (pendingParentGoalId <= 0) {
            return;
        }

        records.forEach(function (record) {
            if (record && record.type === "childList") {
                shouldCheck = true;
            }
        });

        if (shouldCheck) {
            queueMaybeScrollToPendingParent();
        }
    })).observe(results, {
        childList: true,
        subtree: true
    });
})();
JS;

        wp_add_inline_script( 'tct-mobile-js', $script, 'after' );
    }
}
