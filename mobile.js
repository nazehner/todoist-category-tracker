!function() {
  "use strict";
  var t = window.TCT || {}, e = t && "function" == typeof t.isPositiveNoIntervalGoalType ? t.isPositiveNoIntervalGoalType : function(t) {
    return "positive_no_int" === (t || "").toString().trim();
  }, n = t && "function" == typeof t.debounce ? t.debounce : function(t, e) {
    var n = null;
    return function() {
      var a = this, r = arguments;
      n && clearTimeout(n), n = setTimeout((function() {
        t.apply(a, r);
      }), e);
    };
  }, a = t && "function" == typeof t.onReady ? t.onReady : function(t) {
    "loading" === document.readyState ? document.addEventListener("DOMContentLoaded", t) : t();
  }, r = t && "function" == typeof t.setHidden ? t.setHidden : function(t, e) {
    t && (e ? t.setAttribute("hidden", "hidden") : t.removeAttribute("hidden"));
  };
  a((function() {
    var t = document.querySelector(".tct-mobile");
    if (t) {
      t.querySelector(".tct-mobile-header");
      var a = t.querySelector("[data-tct-mobile-results]"), i = t.querySelector("[data-tct-mobile-chips]"), o = t.querySelector("[data-tct-mobile-header-viewport]"), c = (t.querySelector("[data-tct-mobile-header-track]"), 
      t.querySelector('[data-tct-mobile-header-pane="default"]')), l = t.querySelector('[data-tct-mobile-header-pane="domain"]'), s = c ? c.querySelector("[data-tct-mobile-search]") : null, d = c ? c.querySelector("[data-tct-mobile-clear]") : null, u = l ? l.querySelector("[data-tct-mobile-search]") : null, m = l ? l.querySelector("[data-tct-mobile-clear]") : null, f = c ? c.querySelector("[data-tct-mobile-go]") : null, v = l ? l.querySelector("[data-tct-mobile-go]") : null, p = [ s, u ].filter((function(t) {
        return !!t;
      })), h = [ d, m ].filter((function(t) {
        return !!t;
      })), g = [ f, v ].filter((function(t) {
        return !!t;
      }));
      if (!p || 0 === p.length) {
        var b = t.querySelector("[data-tct-mobile-search]");
        b && (p = [ b ]);
      }
      if (!h || 0 === h.length) {
        var y = t.querySelector("[data-tct-mobile-clear]");
        y && (h = [ y ]);
      }
      if (!g || 0 === g.length) {
        var S = t.querySelector("[data-tct-mobile-go]");
        S && (g = [ S ]);
      }
      if (a && p && 0 !== p.length && h && 0 !== h.length) {
        var w = "default", q = ht();
        gt();
        if (window.tctMobile && tctMobile.ajaxUrl) {
          var E = [];
          i && (E = Array.prototype.slice.call(i.querySelectorAll("[data-tct-mobile-chip]")));
          var L, M = {
            critical: i ? i.querySelector('[data-tct-chip-count="critical"]') : null,
            risk: i ? i.querySelector('[data-tct-chip-count="risk"]') : null,
            vit_low: i ? i.querySelector('[data-tct-chip-count="vit_low"]') : null,
            vit_mid: i ? i.querySelector('[data-tct-chip-count="vit_mid"]') : null
          }, _ = "", C = "", A = a.innerHTML, T = !1, I = null, x = 0, N = 0;
          (L = t) && (L.addEventListener("touchstart", At, {
            passive: !0
          }), L.addEventListener("touchend", It, {
            passive: !0
          }), L.addEventListener("touchcancel", Tt, {
            passive: !0
          }));
          var D = !!(window.tctMobile && tctMobile.features && tctMobile.features.domainBrowseSwipe), F = t.querySelector("[data-tct-mobile-domain-grid]"), H = t.querySelector("[data-tct-mobile-domain-selected-row]"), k = t.querySelector("[data-tct-mobile-role-chips]"), P = window.tctMobile && Array.isArray(tctMobile.domains) ? tctMobile.domains.slice() : [], j = window.tctMobile && Array.isArray(tctMobile.roles) ? tctMobile.roles.slice() : [], R = {}, B = {}, O = {}, X = {}, U = {}, Y = 0, W = {}, G = "", J = "", z = 0;
          D && F && H && k && (X = {}, U = {}, (P = Array.isArray(P) ? P.slice() : []).forEach((function(t) {
            var e, n, a = t && isFinite(parseInt(t.id, 10)) ? parseInt(t.id, 10) : 0;
            a && (X[a] = {
              id: a,
              name: t && t.name ? String(t.name) : "",
              color: (e = t && t.color ? t.color : "", n = (e || "").trim(), n && (/^#[0-9a-fA-F]{3}$/.test(n) || /^#[0-9a-fA-F]{6}$/.test(n)) ? n : ""),
              sort_order: t && isFinite(parseInt(t.sort_order, 10)) ? parseInt(t.sort_order, 10) : 0
            });
          })), (j = Array.isArray(j) ? j.slice() : []).forEach((function(t) {
            var e = t && isFinite(parseInt(t.id, 10)) ? parseInt(t.id, 10) : 0;
            if (e) {
              var n = t && isFinite(parseInt(t.domain_id, 10)) ? parseInt(t.domain_id, 10) : 0;
              n && (U[n] || (U[n] = []), U[n].push({
                id: e,
                domain_id: n,
                name: t && t.name ? String(t.name) : "",
                sort_order: t && isFinite(parseInt(t.sort_order, 10)) ? parseInt(t.sort_order, 10) : 0
              }));
            }
          })), (P = Object.keys(X).map((function(t) {
            return X[parseInt(t, 10)];
          }))).sort(Ht), Object.keys(U).forEach((function(t) {
            U[t].sort(Ht);
          })), F && (F.innerHTML = "", O = {}, P.forEach((function(t) {
            if (t && t.id) {
              var e = parseInt(t.id, 10) || 0;
              if (e) {
                var n = document.createElement("button");
                n.type = "button", n.className = "tct-mobile-domain-chip", n.setAttribute("data-tct-mobile-domain-chip", String(e)), 
                n.setAttribute("aria-pressed", "false"), t.color && n.style.setProperty("--tct-domain-color", t.color);
                var a = document.createElement("span");
                a.className = "tct-mobile-domain-chip-text", a.textContent = t.name || "";
                var r = document.createElement("span");
                r.className = "tct-mobile-domain-chip-count", r.setAttribute("data-tct-domain-count", String(e));
                var i = parseInt(R[String(e)], 10);
                isFinite(i) || (i = 0), r.textContent = "(" + i + ")", n.appendChild(a), n.appendChild(r), 
                F.appendChild(n), O[String(e)] = r;
              }
            }
          }))), kt(), F.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-domain-chip]") : null;
            if (e && F.contains(e)) {
              var n = (e.getAttribute("data-tct-mobile-domain-chip") || "").trim();
              n && Rt(n);
            }
          })), H.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-domain-clear]") : null;
            e && H.contains(e) && (t.preventDefault(), function() {
              if (D) {
                if (Y = 0, W = {}, z++, H && (H.innerHTML = ""), k && (k.innerHTML = ""), Bt(), 
                pt() && Mt(!0), Lt()) return J && (G = "", J = ""), void yt();
                var t = !1;
                G && !J && (a.innerHTML = G, Ft(), Xt(), t = !0), G = "", J = "", t || zt({
                  from: "domain-clear",
                  silent: !0
                }), yt();
              }
            }());
          })), k.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-role-chip]") : null;
            if (e && k.contains(e)) {
              var n = (e.getAttribute("data-tct-mobile-role-chip") || "").trim();
              n && (t.preventDefault(), function(t, e) {
                if (D && Y) {
                  var n = parseInt(t, 10) || 0;
                  if (n) {
                    var a = !!W[n];
                    a ? delete W[n] : W[n] = !0, e && (e.setAttribute("aria-pressed", a ? "false" : "true"), 
                    a ? e.classList.remove("tct-mobile-role-chip-active") : e.classList.add("tct-mobile-role-chip-active")), 
                    jt();
                  }
                }
              }(n, e));
            }
          })), Bt()), requestAnimationFrame((function() {
            Mt(!1);
          }));
          var $ = t.querySelector(".tct-mobile-reward-image img");
          if ($) {
            var Z = function() {
              Mt(!1);
            };
            $.complete ? requestAnimationFrame(Z) : $.addEventListener("load", Z);
          }
          setTimeout((function() {
            Mt(!1);
          }), 400), window.addEventListener("resize", n((function() {
            Mt(!1);
          }), 200));
          var K = "", Q = null, V = n((function(t) {
            Kt(t);
          }), 180);
          p && p.length && p.forEach((function(t) {
            t && (t.addEventListener("input", te), t.addEventListener("keyup", te), t.addEventListener("change", te), 
            t.addEventListener("compositionend", te), t.addEventListener("keydown", (function(t) {
              "Escape" === t.key && Vt();
            })));
          })), h && h.length && h.forEach((function(t) {
            t && t.addEventListener("click", Vt);
          })), g && g.length && g.forEach((function(t) {
            t && t.addEventListener("click", (function(t) {
              t && "function" == typeof t.preventDefault && t.preventDefault(), zt({
                forceSearch: !0
              }), wt();
            }));
          })), i && E && E.length > 0 && i.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-chip]") : null;
            if (e && i.contains(e)) {
              var n = (e.getAttribute("data-tct-mobile-chip") || "").trim();
              if (n) {
                if (C = "", _ === n) return bt(""), Jt({}), Zt({
                  silent: !0
                }), void yt();
                bt(n), wt(), Qt(n);
              }
            }
          })), a.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-row-complete]") : null;
            if (!e || !a.contains(e)) {
              var n, r = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-toggle]") : null;
              if (r && a.contains(r)) ee(n = r.closest(".tct-mobile-result")); else if ((n = t.target && t.target.closest ? t.target.closest(".tct-mobile-result") : null) && a.contains(n) && n.classList.contains("tct-mobile-result-expanded")) {
                if (t.target && t.target.closest ? t.target.closest("button, a, input, select, textarea, [data-tct-complete-goal], [data-tct-start-timer], [data-tct-timer-complete], [data-tct-goal-edit], [data-tct-goal-history], [data-tct-ledger-undo]") : null) return;
                ee(n);
              }
            }
          })), a.addEventListener("keydown", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-row-complete]") : null;
            if (!e || !a.contains(e)) {
              var n = t.key || "";
              if ("Enter" === n || " " === n || "Spacebar" === n) {
                var r = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-toggle]") : null;
                if (r && a.contains(r)) t.preventDefault(), ee(r.closest(".tct-mobile-result"));
              }
            }
          })), a.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-row-complete]") : null;
            if (e && a.contains(e)) {
              if (e._tctLongPressJustFired) return e._tctLongPressJustFired = !1, t.preventDefault(), 
              void t.stopPropagation();
              t.preventDefault(), t.stopPropagation();
              if ("1" === e.getAttribute("data-tct-due-enabled") && "1" !== e.getAttribute("data-tct-due-today") && "1" !== e.getAttribute("data-tct-availability-paused")) {
                if (e.classList.contains("tct-mobile-row-fail-mode")) {
                  e.classList.remove("tct-mobile-row-fail-mode");
                  var n0 = e.getAttribute("data-tct-orig-html"), o0 = e.getAttribute("data-tct-orig-aria");
                  null !== n0 && (e.innerHTML = n0), null !== o0 && e.setAttribute("aria-label", o0);
                }
                var i0 = e.getAttribute("data-tct-next-due-weekday") || e.getAttribute("data-tct-next-due-label") || "";
                ie("Not due today" + (i0 ? " -- next due " + i0 : ""), "error");
                return;
              }
              e.classList.contains("tct-mobile-row-fail-mode") ? function(t) {
                var e = parseInt(t.getAttribute("data-goal-id") || "0", 10);
                if (!isFinite(e) || e <= 0) return void ie("Missing goal id.", "error");
                var n = window.tctDashboard && window.tctDashboard.failGoalNonce ? window.tctDashboard.failGoalNonce : "";
                if (!n) return void ie("Missing fail nonce.", "error");
                var a = t.closest(".tct-mobile-row-btn-group"), r = a ? Array.prototype.slice.call(a.querySelectorAll("button")) : [ t ];
                r.forEach((function(t) {
                  try {
                    t.disabled = !0;
                  } catch (t) {}
                })), de({
                  action: "tct_fail_goal",
                  nonce: n,
                  goal_id: e
                }).then((function(t) {
                  if (t && t.success) {
                    ie(t.data && t.data.message ? String(t.data.message) : "Goal failed.", "error");
                    try {
                      he();
                    } catch (e) {
                      console.error("TCT mobile after fail refresh error:", e), yt();
                    }
                  } else {
                    var e;
                    ie(t && t.data && t.data.message ? String(t.data.message) : "Could not fail goal.", "error");
                  }
                })).catch((function(t) {
                  t && "auth" === t.message || ie("Network error failing goal.", "error");
                })).finally((function() {
                  r.forEach((function(t) {
                    try {
                      t.disabled = !1;
                    } catch (t) {}
                  }));
                }));
              }(e) : ge(e);
            }
          })), function() {
            var t = null, e = 0, n = 0, r = null;
            function i() {
              t && (clearTimeout(t), t = null), r = null;
            }
            a.addEventListener("touchstart", (function(i) {
              var o = i.target && i.target.closest ? i.target.closest('[data-tct-mobile-row-complete][data-tct-fail-enabled="1"]') : null;
              if (o && a.contains(o)) {
                if ("1" === o.getAttribute("data-tct-due-enabled") && "1" !== o.getAttribute("data-tct-due-today") && "1" !== o.getAttribute("data-tct-availability-paused")) return;
                var c = i.touches && i.touches[0];
                c && (e = c.clientX, n = c.clientY, r = o, t = setTimeout((function() {
                  if (r === o && (function(t) {
                    if (t) {
                      if (t.classList.contains("tct-mobile-row-fail-mode")) {
                        t.classList.remove("tct-mobile-row-fail-mode");
                        var e = t.getAttribute("data-tct-orig-html"), n = t.getAttribute("data-tct-orig-aria");
                        null !== e && (t.innerHTML = e), null !== n && t.setAttribute("aria-label", n);
                      } else {
                        t.setAttribute("data-tct-orig-html", t.innerHTML), t.setAttribute("data-tct-orig-aria", t.getAttribute("aria-label") || ""), 
                        t.classList.add("tct-mobile-row-fail-mode");
                        var a = t.getAttribute("data-tct-fail-text") || "Fail";
                        t.innerHTML = '<span class="tct-mobile-row-complete-text">' + a + "</span>", t.setAttribute("aria-label", "Fail");
                      }
                      t._tctLongPressJustFired = !0;
                    }
                  }(o), navigator.vibrate)) try {
                    navigator.vibrate(40);
                  } catch (t) {}
                  t = null, r = null;
                }), 500));
              }
            }), {
              passive: !0
            }), a.addEventListener("touchmove", (function(t) {
              if (r) {
                var a = t.touches && t.touches[0];
                if (a) {
                  var o = Math.abs(a.clientX - e), c = Math.abs(a.clientY - n);
                  (o > 10 || c > 10) && i();
                } else i();
              }
            }), {
              passive: !0
            }), a.addEventListener("touchend", (function() {
              i();
            }), {
              passive: !0
            }), a.addEventListener("touchcancel", (function() {
              i();
            }), {
              passive: !0
            }), a.addEventListener("contextmenu", (function(t) {
              var e = t.target && t.target.closest ? t.target.closest('[data-tct-mobile-row-complete][data-tct-fail-enabled="1"]') : null;
              e && a.contains(e) && t.preventDefault();
            }));
          }(), a.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-complete-goal], [data-tct-timer-complete]") : null;
            if (e && a.contains(e)) {
              var n = e.closest ? e.closest('.tct-mobile-result[data-tct-composite-child="1"]') : null;
              n || window.setTimeout((function() {
                if (Yt()) return yt(), void (D && kt());
                he();
              }), 650);
            }
          })), document.addEventListener("click", (function(e) {
            var n = e.target && e.target.closest ? e.target.closest('[data-tct-undo-completion]') : null;
            n && (t && "function" == typeof t.contains && !t.contains(n) || window.setTimeout((function() {
              if (Yt()) return yt(), void (D && kt());
              he();
            }), 800));
          }), !0);
          var tt = n((function() {
            Xt();
          }), 150);
          window.addEventListener("resize", tt);
          var et = 12e4, nt = null, at = null, resetExpandedCompositeTimeout = 9e5;
          function getIdleResetTimeout() {
            if (!a) return et;
            return a.querySelector('.tct-mobile-result.tct-mobile-result-expanded[data-tct-composite-parent="1"], .tct-mobile-result.tct-mobile-result-expanded.tct-mobile-result-composite-parent') ? resetExpandedCompositeTimeout : et;
          }
          document.addEventListener("visibilitychange", (function() {
            "hidden" !== document.visibilityState ? (at && Date.now() - at >= getIdleResetTimeout() && !Yt() && Gt() && re(!1), 
            at = null, ae()) : at = Date.now();
          }));
          var rt = n((function() {
            ae();
          }), 250);
          window.addEventListener("scroll", rt), document.addEventListener("click", ae, !0), 
          document.addEventListener("touchstart", ae, !0), document.addEventListener("keydown", ae, !0);
          var it = null, ot = null, ct = !1, lt = null, st = null, dt = null, ut = null, mt = null, ft = document.querySelector(".tct-mobile");
          ft && ft.addEventListener("click", (function(t) {
            var e = t.target;
            if (e && e.closest) {
              var n = be();
              if (n && e === n) return t.preventDefault(), t.stopImmediatePropagation(), void Se();
              if (e.closest("[data-tct-history-close]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void Se();
              var a = Ee();
              if (a && e === a) return t.preventDefault(), t.stopImmediatePropagation(), void Me();
              var r = e.closest("[data-tct-modal-close], [data-tct-modal-cancel]");
              if (r && r.closest("[data-tct-goal-modal]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void Me();
              var i = e.closest("[data-tct-history-tab]");
              if (i && i.closest("[data-tct-history-modal]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void we(i.getAttribute("data-tct-history-tab") || "completions");
              var o = e.closest("[data-tct-undo-completion]");
              if (o && o.closest("[data-tct-history-modal]")) {
                t.preventDefault(), t.stopImmediatePropagation();
                var c = parseInt(o.getAttribute("data-completion-id") || "0", 10);
                return !isFinite(c) || c <= 0 ? void ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error") : void Pe(c, o);
              }
              var l = e.closest("[data-tct-start-timer]");
              if (l) return t.preventDefault(), t.stopImmediatePropagation(), void pe(l);
              if (e.closest("[data-tct-timer-pause]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void function() {
                if (lt && !lt.paused) {
                  var t = lt.endAtMs - Date.now();
                  lt.remainingSeconds = Math.max(0, Math.ceil(t / 1e3)), lt.paused = !0, lt.intervalId && (window.clearInterval(lt.intervalId), 
                  lt.intervalId = null), lt.pauseBtn && (lt.pauseBtn.hidden = !0), lt.resumeBtn && (lt.resumeBtn.hidden = !1), 
                  ae();
                }
              }();
              if (e.closest("[data-tct-timer-resume]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void (lt && lt.paused && (lt.paused = !1, lt.endAtMs = Date.now() + 1e3 * (lt.remainingSeconds || 0), 
              lt.pauseBtn && (lt.pauseBtn.hidden = !1), lt.resumeBtn && (lt.resumeBtn.hidden = !0), 
              lt.intervalId && window.clearInterval(lt.intervalId), lt.intervalId = window.setInterval(ve, 250), 
              ae()));
              if (e.closest("[data-tct-timer-cancel]")) return t.preventDefault(), t.stopImmediatePropagation(), 
              void fe({
                silent: !1
              });
              var s = e.closest("[data-tct-complete-goal], [data-tct-timer-complete]");
              if (s) {
                (window.tctDashboard && window.tctDashboard.quickCompleteNonce ? window.tctDashboard.quickCompleteNonce : "") && (t.preventDefault(), 
                t.stopImmediatePropagation(), ge(s));
              } else {
                var d = e.closest("[data-tct-open-goal-history]");
                if (d) {
                  t.preventDefault(), t.stopImmediatePropagation();
                  var u = parseInt(d.getAttribute("data-goal-id") || "0", 10);
                  return !isFinite(u) || u <= 0 ? void ie("Missing goal id.", "error") : void qe(u, function(t) {
                    var e = t.closest(".tct-domain-goal");
                    if (!e) return "";
                    var n = e.querySelector(".tct-domain-goal-title");
                    return n ? String(n.textContent || "").trim() : "";
                  }(d));
                }
                var m = e.closest("[data-tct-open-goal-modal]");
                if (m && "edit" === m.getAttribute("data-tct-open-goal-modal")) {
                  t.preventDefault(), t.stopImmediatePropagation();
                  var f = m.getAttribute("data-tct-goal") || "";
                  if (!f) return void ie("Missing goal payload.", "error");
                  try {
                    Te(JSON.parse(f));
                  } catch (t) {
                    ie("Could not parse goal payload.", "error");
                  }
                } else ;
              }
            }
          }), !0);
          var vt = null;
          document.addEventListener("visibilitychange", (function() {
            "visible" === document.visibilityState && Ie();
          })), vt && clearInterval(vt), vt = setInterval(Ie, 3e5), t.addEventListener("click", (function(t) {
            if (t.target && t.target.closest ? t.target.closest("[data-tct-mobile-ledger-trigger]") : null) {
              t.preventDefault();
              var e = xe({
                title: "Points Ledger",
                cls: "tct-mobile-overlay-ledger"
              });
              loadMobileLedgerBody(e.body);
            }
          })), document.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest('[data-tct-ledger-undo="1"]') : null;
            if (e) {
              t.preventDefault(), t.stopImmediatePropagation();
              if (e.hasAttribute("disabled")) return;
              var n = parseInt(e.getAttribute("data-completion-id") || "0", 10);
              return !isFinite(n) || n <= 0 ? void ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error") : void undoMobileLedgerCompletion(n, e);
            }
          }), !0), t.addEventListener("click", (function(t) {
            var e = t.target && t.target.closest ? t.target.closest("[data-tct-mobile-reward-zoom]") : null;
            if (e) {
              t.preventDefault();
              var n = e.querySelector("img");
              if (n) {
                var a = n.getAttribute("data-full-src") || n.src, r = xe({
                  cls: "tct-mobile-overlay-image"
                }), i = document.createElement("img");
                i.className = "tct-mobile-overlay-zoom-img", i.src = a, i.alt = n.alt || "Reward", 
                r.body.appendChild(i);
              }
            }
          })), Et(), Ft(), Xt(), yt(), ne();
        } else console.warn("TCT mobile: missing tctMobile/ajaxUrl");
      }
    }
    function pt() {
      return "domain" === w;
    }
    function ht() {
      return pt() && u ? u : !pt() && s ? s : p[0] || null;
    }
    function gt() {
      return pt() && m ? m : !pt() && d ? d : h[0] || null;
    }
    function bt(t) {
      var e = (t || "").trim();
      _ = e, E && 0 !== E.length && E.forEach((function(t) {
        var n = (t.getAttribute("data-tct-mobile-chip") || "").trim(), a = !!e && n === e;
        t.setAttribute("aria-pressed", a ? "true" : "false");
      }));
    }
    function yt() {
      if (i && E && 0 !== E.length) {
        var t = new FormData;
        t.append("action", "tct_mobile_chip_counts"), t.append("nonce", tctMobile.searchNonce || ""), 
        fetch(tctMobile.ajaxUrl, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: t
        }).then(ce).then((function(t) {
          return t.json();
        })).then((function(t) {
          t && t.success && t.data && t.data.counts && function(t) {
            if (t) {
              var e = parseInt(t.critical, 10), n = parseInt(t.risk, 10), a = parseInt(t.vit_low, 10), r = parseInt(t.vit_mid, 10);
              isFinite(e) || (e = 0), isFinite(n) || (n = 0), isFinite(a) || (a = 0), isFinite(r) || (r = 0), 
              M.critical && (M.critical.textContent = "(" + e + ")"), M.risk && (M.risk.textContent = "(" + n + ")"), 
              M.vit_low && (M.vit_low.textContent = "(" + a + ")"), M.vit_mid && (M.vit_mid.textContent = "(" + r + ")");
            }
          }(t.data.counts);
        })).catch((function() {}));
      }
    }
    function St() {
      q = ht(), gt();
    }
    function wt() {
      p && p.forEach((function(t) {
        if (t) try {
          t.blur();
        } catch (t) {}
      }));
    }
    function qt(t) {
      var e = null == t ? "" : String(t);
      p && p.forEach((function(t) {
        t && (t.value = e);
      })), St(), Et();
    }
    function Et() {
      St();
      var t = (q && q.value ? String(q.value) : "").trim().length > 0;
      h && h.forEach((function(e) {
        e && (e.style.display = t ? "flex" : "none");
      }));
    }
    function Lt() {
      St();
      var t = "";
      return q && "string" == typeof q.value ? t = q.value : p && p.length && p[0] && "string" == typeof p[0].value && (t = p[0].value), 
      String(t || "").trim();
    }
    function Mt(e) {
      if (o) {
        !1 === e && t.classList.add("tct-mobile-no-anim");
        var n = !1;
        !pt() && F && F.hasAttribute("hidden") && (n = !0, F.removeAttribute("hidden"));
        var a = 0, r = 0;
        if (c) {
          var i = c.getBoundingClientRect();
          a = i && i.height ? i.height : c.offsetHeight || 0;
        }
        if (l) {
          var s = l.getBoundingClientRect();
          r = s && s.height ? s.height : l.offsetHeight || 0;
        }
        n && F && F.setAttribute("hidden", "hidden");
        var d = Math.max(a, r);
        d > 0 && (o.style.height = Math.ceil(d) + "px"), !1 === e && requestAnimationFrame((function() {
          t.classList.remove("tct-mobile-no-anim");
        }));
      }
    }
    function _t(e, n) {
      var a = "domain" === e ? "domain" : "default", r = void 0 === n || !!n, i = "", o = ht();
      if (o && "string" == typeof o.value && (i = o.value), a === w) return St(), p && p.length > 1 && p.forEach((function(t) {
        t && "string" == typeof t.value && t.value !== i && (t.value = i);
      })), Et(), void Mt(r);
      r || t.classList.add("tct-mobile-no-anim"), w = a, pt() ? t.classList.add("tct-mobile-view-domain") : t.classList.remove("tct-mobile-view-domain"), 
      St(), p && p.length > 1 && p.forEach((function(t) {
        t && "string" == typeof t.value && t.value !== i && (t.value = i);
      })), Et(), D && Bt(), requestAnimationFrame((function() {
        Mt(r), zt({
          from: "view-mode",
          silent: !0
        }), r || requestAnimationFrame((function() {
          t.classList.remove("tct-mobile-no-anim");
        }));
      }));
    }
    function Ct() {
      return !t || t.classList.contains("tct-mobile-view-favorites") || (!!t.querySelector(".tct-mobile-session-expired") || (!!t.querySelector("[data-tct-history-overlay]:not([hidden])") || !!t.querySelector("[data-tct-goal-overlay]:not([hidden])")));
    }
    function At(t) {
      if (D && !Ct()) {
        if (!t || !t.touches || 1 !== t.touches.length) return T = !1, void (I = null);
        var e = t.touches[0];
        T = !0, I = "number" == typeof e.identifier ? e.identifier : null, x = "number" == typeof e.clientX ? e.clientX : 0, 
        N = "number" == typeof e.clientY ? e.clientY : 0;
      }
    }
    function Tt() {
      T = !1, I = null;
    }
    function It(t) {
      if (D) {
        if (Ct()) Tt(); else if (T) if (T = !1, !t || !t.changedTouches || t.changedTouches.length < 1) I = null; else {
          var e = null;
          if ("number" == typeof I && (e = function(t, e) {
            if (!t || "number" != typeof e) return null;
            for (var n = 0; n < t.length; n++) if (t[n] && t[n].identifier === e) return t[n];
            return null;
          }(t.changedTouches, I)), e || (e = t.changedTouches[0]), I = null, e) {
            var n = "number" == typeof e.clientX ? e.clientX : 0, a = "number" == typeof e.clientY ? e.clientY : 0, r = n - x, i = a - N, o = Math.abs(r);
            if (!(2 * o < Math.abs(i))) {
              var c, l = .15 * (c = window.innerWidth || (document.documentElement ? document.documentElement.clientWidth : 0) || 0, 
              (!isFinite(c) || c <= 0) && (c = 320), c);
              (!isFinite(l) || l <= 0) && (l = 50), o < l || (r < 0 && !pt() ? (wt(), _t("domain", !0)) : r > 0 && pt() && (wt(), 
              _t("default", !0)));
            }
          }
        }
      } else Tt();
    }
    function xt() {
      a && a.classList.remove("tct-mobile-results-populated");
    }
    function Nt(t) {
      var e = t || "Loading...";
      a.innerHTML = '<div class="tct-mobile-loading">' + e + "</div>", xt();
    }
    function Dt(t) {
      var e = t || "No matching goals found.";
      a.innerHTML = '<div class="tct-mobile-no-results">' + e + "</div>", xt();
    }
    function Ft() {
      try {
        if ("function" == typeof window.tctDashboardEnhance) {
          var t = a.closest(".tct-dashboard");
          window.tctDashboardEnhance(t || void 0);
        }
      } catch (t) {}
    }
    function Ht(t, e) {
      var n = t && isFinite(parseInt(t.sort_order, 10)) ? parseInt(t.sort_order, 10) : 0, a = e && isFinite(parseInt(e.sort_order, 10)) ? parseInt(e.sort_order, 10) : 0;
      if (n !== a) return n - a;
      var r = (t && t.name ? String(t.name) : "").toLowerCase(), i = (e && e.name ? String(e.name) : "").toLowerCase();
      return r < i ? -1 : r > i ? 1 : 0;
    }
    function kt() {
      if (D && F && P && 0 !== P.length) {
        var t = new FormData;
        t.append("action", "tct_mobile_domain_counts"), t.append("nonce", tctMobile.searchNonce || ""), 
        fetch(tctMobile.ajaxUrl, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: t
        }).then(ce).then((function(t) {
          return t.json();
        })).then((function(t) {
          t && t.success && t.data && t.data.counts && "object" == typeof t.data.counts && (R = t.data.counts || {}, 
          B = t.data.role_counts || {}, function() {
            if (O && (Object.keys(O).forEach((function(t) {
              var e = O[t];
              if (e) {
                var n = parseInt(R[t], 10);
                isFinite(n) || (n = 0), e.textContent = "(" + n + ")";
              }
            })), Y && H)) {
              var t = H.querySelector(".tct-mobile-domain-chip-count");
              if (t) {
                var e = parseInt(R[String(Y)], 10);
                isFinite(e) || (e = 0), t.textContent = "(" + e + ")";
              }
            }
          }(), Y && Pt());
        })).catch((function() {}));
      }
    }
    function Pt() {
      if (k && (k.innerHTML = "", Y)) {
        var t = X[Y], e = t && t.color ? t.color : "", n = U[Y] || U[String(Y)] || [];
        n && 0 !== n.length && n.forEach((function(t) {
          if (t && t.id) {
            var n = parseInt(t.id, 10) || 0;
            if (n) {
              var a = document.createElement("button");
              a.type = "button", a.className = "tct-mobile-role-chip" + (W[n] ? " tct-mobile-role-chip-active" : ""), 
              a.setAttribute("data-tct-mobile-role-chip", String(n)), a.setAttribute("aria-pressed", W[n] ? "true" : "false"), 
              e && a.style.setProperty("--tct-domain-color", e);
              var r = t.name || "", i = parseInt(B[String(n)], 10);
              isFinite(i) && i > 0 && (r += " (" + i + ")"), a.textContent = r, k.appendChild(a);
            }
          }
        }));
      }
    }
    function jt(t) {
      var e = !!(t || {}).silent;
      if (D && (Y && !Lt())) {
        $t(), e || Nt("Loading...");
        var n, r = new FormData;
        r.append("action", "tct_mobile_domain_filter"), r.append("nonce", tctMobile.searchNonce || ""), 
        r.append("domain_id", String(Y)), (n = [], Object.keys(W || {}).forEach((function(t) {
          var e = parseInt(t, 10) || 0;
          e > 0 && W[e] && n.push(e);
        })), n.sort((function(t, e) {
          return t - e;
        })), n).forEach((function(t) {
          r.append("role_ids[]", String(t));
        }));
        var i = ++z, o = {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: r
        };
        Q && (o.signal = Q.signal), fetch(tctMobile.ajaxUrl, o).then(ce).then((function(t) {
          return t.json();
        })).then((function(t) {
          if (i === z && Y) {
            if (!t || !t.success || !t.data) return Dt("No goals found."), void yt();
            var e = "";
            if ("string" == typeof t.data.html && (e = t.data.html), !e) return Dt("No goals found."), 
            void yt();
            a.innerHTML = e, Ft(), Xt(), yt();
          }
        })).catch((function(t) {
          t && "AbortError" === t.name || (console.error("TCT mobile domain filter error:", t), 
          Dt("No goals found."));
        }));
      }
    }
    function Rt(t) {
      if (D) {
        var e = parseInt(t, 10) || 0;
        e && X[e] && (G = a.innerHTML, J = Lt(), Y = e, W = {}, wt(), function() {
          if (H && (H.innerHTML = "", Y && X[Y])) {
            var t = X[Y], e = document.createElement("button");
            e.type = "button", e.className = "tct-mobile-domain-chip", e.setAttribute("data-tct-mobile-domain-selected", String(Y)), 
            e.setAttribute("aria-pressed", "true"), t.color && e.style.setProperty("--tct-domain-color", t.color);
            var n = document.createElement("span");
            n.className = "tct-mobile-domain-chip-text", n.textContent = t.name || "";
            var a = document.createElement("span");
            a.className = "tct-mobile-domain-chip-count";
            var r = parseInt(R[String(Y)], 10);
            isFinite(r) || (r = 0), a.textContent = "(" + r + ")", e.appendChild(n), e.appendChild(a);
            var i = document.createElement("button");
            i.type = "button", i.className = "tct-mobile-domain-clear-chip", i.setAttribute("data-tct-mobile-domain-clear", "1"), 
            i.textContent = "Clear", H.appendChild(e), H.appendChild(i);
          }
        }(), Pt(), Bt(), pt() && Mt(!0), jt());
      }
    }
    function Bt() {
      if (D && F && H && k) return pt() ? void (Y ? (r(F, !0), r(H, !1), r(k, !1)) : (r(F, !1), 
      r(H, !0), r(k, !0))) : (r(F, !0), r(H, !0), void r(k, !0));
    }
    function Ot() {
      var e = t.querySelector(".tct-mobile-header");
      if (!e) return 0;
      var n = 0;
      try {
        var a = window.getComputedStyle(e).top || "0";
        n = parseFloat(a) || 0;
      } catch (t) {
        n = 0;
      }
      var r = e.getBoundingClientRect();
      return n + (r && r.height ? r.height : e.offsetHeight || 0);
    }
    function Xt() {
      if (a) if (!!a.querySelector(".tct-mobile-result")) {
        a.classList.add("tct-mobile-results-populated");
        var t = a.querySelector(".tct-mobile-bottom-spacer");
        t || ((t = document.createElement("div")).className = "tct-mobile-bottom-spacer", 
        t.setAttribute("aria-hidden", "true"), a.appendChild(t));
        var e = window.innerHeight || document.documentElement.clientHeight || 0, n = Ot(), r = 0;
        e > 0 && (r = e - n - 12);
        var i = 200;
        e > 0 && (i = Math.max(120, Math.round(.6 * e)));
        var o = Math.max(r, i);
        (!isFinite(o) || o < 0) && (o = i), t.style.height = Math.round(o) + "px";
      } else {
        xt();
        var c = a.querySelector(".tct-mobile-bottom-spacer");
        c && c.parentNode && c.parentNode.removeChild(c);
      }
    }
    function Ut(t) {
      var e = Ot(), n = a.getBoundingClientRect(), r = (window.pageYOffset || document.documentElement.scrollTop || 0) + n.top - (e + 8);
      if (r < 0 && (r = 0), t) try {
        window.scrollTo({
          top: r,
          behavior: "smooth"
        });
      } catch (t) {
        window.scrollTo(0, r);
      } else window.scrollTo(0, r);
    }
    function Yt() {
      return !!t.querySelector("[data-tct-timer-overlay]:not([hidden])");
    }
    function Wt() {
      return !Lt() && (!(D && pt() && Y) && !(!pt() && _));
    }
    function Gt() {
      if (Lt()) return !0;
      if (D) {
        if (pt()) return !0;
        if (Y) return !0;
        if (W) for (var t in W) if (Object.prototype.hasOwnProperty.call(W, t) && W[t]) return !0;
      }
      return !!_ || !(!a || !a.querySelector(".tct-mobile-result.tct-mobile-result-expanded"));
    }
    function Jt(t) {
      var e = t || {};
      a.innerHTML = A, Ft(), Xt(), e.scrollTop && Ut(!!e.smooth);
    }
    function zt(t) {
      var e = t || {}, n = Lt();
      return n ? (e.forceSearch && (K = ""), void Kt(n)) : Y || !G || J ? void (D && pt() && Y ? jt({
        silent: !!e.silent
      }) : pt() || !_ ? (Jt({}), Zt({
        silent: !0
      })) : Qt(_)) : (a.innerHTML = G, Ft(), Xt(), G = "", J = "", void yt());
    }
    function $t() {
      Q && "function" == typeof Q.abort && Q.abort(), Q = "undefined" != typeof AbortController ? new AbortController : null;
    }
    function Zt(t) {
      var e = !!(t || {}).silent;
      if (Wt()) {
        $t(), e || Nt("Loading daily goals...");
        var n = new FormData;
        n.append("action", "tct_mobile_daily_default"), n.append("nonce", tctMobile.searchNonce || "");
        var r = {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: n
        };
        Q && (r.signal = Q.signal), fetch(tctMobile.ajaxUrl, r).then(ce).then((function(t) {
          return t.json();
        })).then((function(t) {
          if (Wt()) {
            if (!t || !t.success || !t.data) return Dt("All daily goals complete."), void yt();
            var e = "";
            "string" == typeof t.data.html && (e = t.data.html), e || (e = '<div class="tct-mobile-no-results">All daily goals complete.</div>'), 
            A = e, a.innerHTML = e, Ft(), Xt(), yt();
          }
        })).catch((function(t) {
          t && "AbortError" === t.name || console.error("TCT mobile daily default error:", t);
        }));
      }
    }
    function Kt(t) {
      var e = (t || "").trim();
      if (!e) return K = "", void zt({
        from: "search-empty",
        silent: !0
      });
      if (e !== K) {
        K = e, $t(), Nt("Searching...");
        var n = new FormData;
        n.append("action", "tct_mobile_search"), n.append("nonce", tctMobile.searchNonce || ""), 
        n.append("query", e);
        var r = {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: n
        };
        Q && (r.signal = Q.signal), fetch(tctMobile.ajaxUrl, r).then(ce).then((function(t) {
          return t.json();
        })).then((function(t) {
          if (!t || !t.success || !t.data) return Dt(), void yt();
          var e = "";
          if ("string" == typeof t.data.html && (e = t.data.html), !e) return Dt(), void yt();
          a.innerHTML = e, Ft(), Xt(), yt();
        })).catch((function(t) {
          t && "AbortError" === t.name || (console.error("TCT mobile search error:", t), Dt());
        }));
      }
    }
    function Qt(t) {
      var e = (t || "").trim();
      if (!e) return bt(""), Jt({}), void Zt({
        silent: !0
      });
      qt(""), K = "", $t(), Nt("Loading...");
      var n = new FormData;
      n.append("action", "tct_mobile_chip_filter"), n.append("nonce", tctMobile.searchNonce || ""), 
      n.append("filter", e);
      var r = {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        },
        body: n
      };
      Q && (r.signal = Q.signal), fetch(tctMobile.ajaxUrl, r).then(ce).then((function(t) {
        return t.json();
      })).then((function(t) {
        if (!t || !t.success || !t.data) return Dt("No goals found."), void yt();
        var e = "";
        if ("string" == typeof t.data.html && (e = t.data.html), !e) return Dt("No goals found."), 
        void yt();
        a.innerHTML = e, Ft(), Xt(), yt();
      })).catch((function(t) {
        t && "AbortError" === t.name || (console.error("TCT mobile chip filter error:", t), 
        Dt("No goals found."));
      }));
    }
    function Vt() {
      qt(""), K = "", C && !_ && bt(C), C = "", Et(), zt({
        from: "clear-btn",
        silent: !0
      }), St(), q && q.focus();
    }
    function te(t) {
      !function(t) {
        if (p && !(p.length < 2)) {
          var e = t || ht(), n = e && "string" == typeof e.value ? e.value : "";
          p.forEach((function(t) {
            t && t !== e && (t.value = n);
          })), Et();
        }
      }(t && t.target ? t.target : null), function() {
        var t = Lt();
        if (t && _ && !C && (C = _, bt("")), Et(), !t) return C && !_ && bt(C), C = "", 
        K = "", void zt({
          from: "search-clear",
          silent: !0
        });
        V(t);
      }();
    }
    function ee(t) {
      if (t) if (t.classList.contains("tct-mobile-result-expanded")) {
        t.classList.remove("tct-mobile-result-expanded");
        var e = t._tctSavedScrollY;
        "number" == typeof e && isFinite(e) && window.requestAnimationFrame((function() {
          try {
            window.scrollTo({
              top: e,
              behavior: "smooth"
            });
          } catch (t) {
            window.scrollTo(0, e);
          }
        })), t._tctSavedScrollY = void 0, ae();
      } else t._tctSavedScrollY = window.pageYOffset || document.documentElement.scrollTop || 0, 
      t.classList.add("tct-mobile-result-expanded"), Xt(), function(t) {
        if (t) {
          var e = t.querySelector(".tct-mobile-result-body") || t;
          window.requestAnimationFrame((function() {
            var t = Ot(), n = e.getBoundingClientRect(), a = (window.pageYOffset || document.documentElement.scrollTop || 0) + n.top - (t + 4);
            a < 0 && (a = 0);
            try {
              window.scrollTo({
                top: a,
                behavior: "smooth"
              });
            } catch (t) {
              window.scrollTo(0, a);
            }
          }));
        }
      }(t), ae();
    }
    function ne() {
      nt && clearTimeout(nt), nt = window.setTimeout((function() {
        nt = null, Yt() ? ne() : Gt() ? re(!0) : ne();
      }), getIdleResetTimeout());
    }
    function ae() {
      ne();
    }
    function re(t) {
      if (!Yt()) {
        var e = pt();
        qt(""), K = "", C = "", bt(""), Et(), Y = 0, W = {}, G = "", J = "", z++, H && (H.innerHTML = ""), 
        k && (k.innerHTML = ""), D && Bt(), wt(), $t(), function() {
          if (a) {
            var t = a.querySelectorAll(".tct-mobile-result.tct-mobile-result-expanded");
            t && 0 !== t.length && Array.prototype.forEach.call(t, (function(t) {
              t.classList.remove("tct-mobile-result-expanded");
            }));
          }
        }(), Jt({}), e ? (_t("default", !0), requestAnimationFrame((function() {
          Ut(!!t);
        }))) : (Ut(!!t), Zt({
          silent: !0
        })), yt();
      }
    }
    function ie(t, e) {
      if (t) {
        var n = it || ((it = document.createElement("div")).className = "tct-mobile-toast", 
        it.setAttribute("role", "status"), it.setAttribute("aria-live", "polite"), document.body.appendChild(it), 
        it);
        n.textContent = t, n.classList.remove("tct-mobile-toast-error"), n.classList.remove("tct-mobile-toast-success"), 
        n.classList.remove("tct-mobile-toast-show"), "error" === e ? n.classList.add("tct-mobile-toast-error") : "success" === e && n.classList.add("tct-mobile-toast-success"), 
        n.offsetWidth, n.classList.add("tct-mobile-toast-show"), ot && window.clearTimeout(ot), 
        ot = window.setTimeout((function() {
          n.classList.remove("tct-mobile-toast-show");
        }), 2400);
      }
    }
    function oe() {
      if (!ct) {
        ct = !0;
        var t = document.createElement("div");
        t.className = "tct-mobile-session-expired", t.innerHTML = '<div class="tct-mobile-session-expired-box"><div class="tct-mobile-session-expired-icon">&#128274;</div><div class="tct-mobile-session-expired-title">Session Expired</div><div class="tct-mobile-session-expired-msg">Your login session has timed out.</div><button type="button" class="tct-mobile-session-expired-btn" onclick="location.reload()">Reload</button></div>', 
        document.body.appendChild(t);
      }
    }
    function ce(t) {
      return 401 === t.status || 403 === t.status || 400 === t.status ? (oe(), Promise.reject(new Error("auth"))) : t;
    }
    function le(t) {
      var e = [];
      return t ? (Object.keys(t).forEach((function(n) {
        void 0 !== t[n] && null !== t[n] && e.push(encodeURIComponent(n) + "=" + encodeURIComponent(String(t[n])));
      })), e.join("&")) : "";
    }
    function se() {
      return window.tctDashboard && window.tctDashboard.ajaxUrl ? window.tctDashboard.ajaxUrl : window.tctMobile && window.tctMobile.ajaxUrl ? window.tctMobile.ajaxUrl : null;
    }
    function de(t) {
      var e = se();
      return e ? fetch(e, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: le(t)
      }).then(ce).then((function(t) {
        return t.json();
      })) : Promise.reject(new Error("Missing AJAX URL"));
    }
    function loadMobileLedgerBody(t) {
      return t ? (t.innerHTML = '<div style="text-align:center;padding:24px;color:#8c8f94;">Loading...</div>', 
      de({
        action: "tct_mobile_ledger",
        nonce: window.tctMobile && window.tctMobile.searchNonce ? tctMobile.searchNonce : ""
      }).then((function(e) {
        return e && e.success && e.data && e.data.html ? t.innerHTML = e.data.html : t.innerHTML = '<p style="text-align:center;color:#8c8f94;">Unable to load ledger.</p>', 
        e;
      })).catch((function(e) {
        return e && "auth" === e.message || (t.innerHTML = '<p style="text-align:center;color:#8c8f94;">Unable to load ledger.</p>'), 
        null;
      }))) : Promise.resolve(null);
    }
    function undoMobileLedgerCompletion(t, e) {
      var n = window.tctDashboard && window.tctDashboard.undoCompletionNonce ? window.tctDashboard.undoCompletionNonce : "";
      if (!n) return void ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error");
      var a = window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionConfirm ? window.tctDashboard.i18n.undoCompletionConfirm : "Undo this completion?";
      if (!window.confirm(a)) return;
      e && (e.disabled = !0), de({
        action: "tct_undo_completion",
        nonce: n,
        completion_id: t
      }).then((function(t) {
        if (t && t.success) {
          var n = t.data && t.data.message ? String(t.data.message) : "Completion undone.", a = e && e.closest ? e.closest(".tct-mobile-overlay") : null, r = a ? a.querySelector(".tct-mobile-overlay-body") : null, i = loadMobileLedgerBody(r);
          ie(n, "success");
          try {
            he();
          } catch (t) {
            console.error("TCT mobile ledger undo refresh error:", t), yt();
          }
          return i;
        }
        var o = t && t.data && t.data.message ? String(t.data.message) : window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.";
        ie(o, "error");
      })).catch((function(t) {
        t && "auth" === t.message || ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error");
      })).finally((function() {
        e && (e.disabled = !1);
      }));
    }
    function ue(t) {
      var e = parseInt(t, 10);
      return isFinite(e) || (e = 0), (e < 10 ? "0" : "") + String(e);
    }
    function me(t) {
      var e = Math.max(0, Math.floor(t || 0)), n = Math.floor(e / 3600), a = Math.floor(e % 3600 / 60), r = e % 60;
      return n > 0 ? String(n) + ":" + ue(a) + ":" + ue(r) : ue(a) + ":" + ue(r);
    }
    function fe(t) {
      lt && (lt.intervalId && window.clearInterval(lt.intervalId), lt.finishedEl && (lt.finishedEl.hidden = !0), 
      lt.overlayEl && (lt.overlayEl.hidden = !0), lt.displayEl && "number" == typeof lt.durationSeconds && (lt.displayEl.textContent = me(lt.durationSeconds)), 
      lt.pauseBtn && (lt.pauseBtn.hidden = !1), lt.resumeBtn && (lt.resumeBtn.hidden = !0), 
      lt = null, t && t.silent || ae());
    }
    function ve() {
      if (lt && !lt.paused) {
        var t = lt.endAtMs - Date.now(), e = Math.max(0, Math.ceil(t / 1e3));
        if (e !== lt.remainingSeconds && (lt.remainingSeconds = e, lt.displayEl && (lt.displayEl.textContent = me(e))), 
        t <= 0) {
          lt.intervalId && (window.clearInterval(lt.intervalId), lt.intervalId = null), lt.remainingSeconds = 0, 
          lt.displayEl && (lt.displayEl.textContent = me(0)), lt.finishedEl && (lt.finishedEl.hidden = !1), 
          lt.pauseBtn && (lt.pauseBtn.hidden = !0), lt.resumeBtn && (lt.resumeBtn.hidden = !0), 
          lt.paused = !0;
          try {
            navigator && navigator.vibrate && navigator.vibrate([ 120, 80, 120 ]);
          } catch (t) {}
        }
      }
    }
    function pe(t) {
      var e = parseInt(t.getAttribute("data-goal-id") || "0", 10);
      if (!isFinite(e) || e <= 0) ie("Missing goal id.", "error"); else {
        var n = t.closest(".tct-domain-goal");
        if (n) {
          var a = function(t) {
            var e = t && t.textContent ? String(t.textContent) : "", n = (e = e.replace(/\s+/g, " ").trim()).match(/(\d+:\d{2}(?::\d{2})?)\s*$/);
            if (!n) return 0;
            var a = n[1].split(":"), r = 0;
            return 3 === a.length ? r = 3600 * (parseInt(a[0], 10) || 0) + 60 * (parseInt(a[1], 10) || 0) + (parseInt(a[2], 10) || 0) : 2 === a.length && (r = 60 * (parseInt(a[0], 10) || 0) + (parseInt(a[1], 10) || 0)), 
            !isFinite(r) || r <= 0 ? 0 : r;
          }(t);
          if (a) {
            lt && lt.goalId !== e && fe({
              silent: !0
            });
            var r = n.querySelector("[data-tct-timer-overlay]"), i = n.querySelector("[data-tct-timer-display]"), o = n.querySelector("[data-tct-timer-pause]"), c = n.querySelector("[data-tct-timer-resume]");
            r && i && o && c ? ((lt = {
              goalId: e,
              tileEl: n,
              overlayEl: r,
              displayEl: i,
              pauseBtn: o,
              resumeBtn: c,
              durationSeconds: a,
              remainingSeconds: a,
              paused: !1,
              intervalId: null,
              endAtMs: Date.now() + 1e3 * a
            }).finishedEl = n.querySelector("[data-tct-timer-finished]"), lt.finishedEl && (lt.finishedEl.hidden = !0), 
            r.hidden = !1, i.textContent = me(a), o.hidden = !1, c.hidden = !0, lt.intervalId && window.clearInterval(lt.intervalId), 
            lt.intervalId = window.setInterval(ve, 250), ae()) : ie("Timer UI not found.", "error");
          } else ie("Missing timer duration.", "error");
        } else ie("Could not find goal tile.", "error");
      }
    }
    function refreshMobileRewardWidget() {
      var e;
      yt(), (e = new FormData).append("action", "tct_mobile_reward_refresh"), e.append("nonce", tctMobile.searchNonce || ""), 
      fetch(tctMobile.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        },
        body: e
      }).then(ce).then((function(t) {
        return t.json();
      })).then((function(e) {
        if (e && e.success && e.data && e.data.html) {
          var n = t.querySelector(".tct-mobile-reward");
          if (n) {
            var a = document.createElement("div");
            a.innerHTML = e.data.html;
            var r = a.querySelector(".tct-mobile-reward");
            if (r) {
              n.parentNode.replaceChild(r, n), requestAnimationFrame((function() {
                Mt(!1);
              }));
              var i = r.querySelector(".tct-mobile-reward-image img");
              i && !i.complete && i.addEventListener("load", (function() {
                Mt(!1);
              }));
            }
          }
        }
      })).catch((function() {})), D && kt();
    }
    function he() {
      refreshMobileRewardWidget();
      Y && (G = "", J = "");
      var n = Lt();
      if (n) return K = "", void Kt(n);
      D && pt() && Y ? jt({
        silent: !0
      }) : pt() || !_ ? Zt({
        silent: !0
      }) : Qt(_);
    }
    var compositeChildRefreshTimer = 0;
    var compositeChildParentTimer = 0;
    var compositeChildCollapseGoalId = 0;
    function findCompositeParentRowByGoalId(t) {
      var e = parseInt(t || "0", 10);
      return !isFinite(e) || e <= 0 || !a || !a.querySelector ? null : a.querySelector('.tct-mobile-result[data-tct-composite-parent="1"][data-goal-id="' + String(e) + '"]');
    }
    function getCompositeChildContainer(t) {
      return t && t.querySelector ? t.querySelector("[data-tct-mobile-composite-children], .tct-mobile-composite-children") : null;
    }
    function countVisibleCompositeChildRows(t) {
      var e = getCompositeChildContainer(t);
      return e ? e.querySelectorAll('.tct-mobile-result[data-tct-composite-child="1"]:not(.tct-mobile-result-pending-remove)').length : 0;
    }
    function removeCompositeParentEmptyState(t) {
      var e = t && t.querySelector ? t.querySelector(".tct-mobile-composite-empty") : null;
      e && e.parentNode && e.parentNode.removeChild(e);
    }
    function ensureCompositeParentEmptyState(t) {
      var e = getCompositeChildContainer(t);
      if (!t || !e) return;
      if (countVisibleCompositeChildRows(t) > 0) return void removeCompositeParentEmptyState(t);
      if (!t.querySelector(".tct-mobile-composite-empty")) {
        var n = document.createElement("div");
        n.className = "tct-mobile-composite-empty", n.textContent = "No child goals are ready right now.", 
        e.insertAdjacentElement("afterend", n);
      }
    }
    function bumpCompositeParentSummaryLine(t) {
      var e = t && t.querySelector ? t.querySelector(".tct-mobile-composite-summary-line") : null;
      if (!e) return;
      var n = String(e.textContent || "").match(/^(\d+)\s+of\s+(\d+)\s+child\s+goals\s+complete(.*)$/i);
      if (!n) return;
      var a = parseInt(n[1] || "0", 10), r = parseInt(n[2] || "0", 10);
      isFinite(a) && isFinite(r) && r > 0 && (a = Math.min(r, a + 1), e.textContent = String(a) + " of " + String(r) + " child goals complete" + String(n[3] || ""));
    }
    function removeMobileResultRow(t, e) {
      var n;
      if (!t || !t.parentNode || t.classList.contains("tct-mobile-result-pending-remove")) return;
      if (e) return t.classList.add("tct-mobile-result-pending-remove"), void (t.parentNode && (t.parentNode.removeChild(t), 
      Xt()));
      n = Math.max(0, t.offsetHeight || 0), t.style.height = String(n) + "px", t.style.maxHeight = String(n) + "px", 
      t.style.overflow = "hidden", t.offsetHeight, t.classList.add("tct-mobile-result-pending-remove"), 
      window.setTimeout((function() {
        t && t.parentNode && t.classList.contains("tct-mobile-result-pending-remove") && (t.parentNode.removeChild(t), 
        Xt());
      }), 180);
    }
    function syncCompositeParentAfterChildRemoval(t, e) {
      var n = findCompositeParentRowByGoalId(t);
      n && (bumpCompositeParentSummaryLine(n), e ? removeCompositeParentEmptyState(n) : ensureCompositeParentEmptyState(n));
    }
    function maybeCollapseCompositeParentAndScroll(t) {
      var e = findCompositeParentRowByGoalId(t);
      e && 0 === countVisibleCompositeChildRows(e) && (removeMobileResultRow(e, !0), Wt() && (A = a.innerHTML), 
      window.requestAnimationFrame((function() {
        Xt(), Ut(!0);
      })));
    }
    function queueCompositeChildRefresh(t, e) {
      var n = parseInt(t || "0", 10);
      isFinite(n) || (n = 0), e && n > 0 && (compositeChildCollapseGoalId = n, compositeChildParentTimer && window.clearTimeout(compositeChildParentTimer), 
      compositeChildParentTimer = window.setTimeout((function() {
        var t = compositeChildCollapseGoalId;
        compositeChildParentTimer = 0, compositeChildCollapseGoalId = 0, t > 0 && maybeCollapseCompositeParentAndScroll(t);
      }), 120)), compositeChildRefreshTimer && window.clearTimeout(compositeChildRefreshTimer), 
      compositeChildRefreshTimer = window.setTimeout((function() {
        compositeChildRefreshTimer = 0;
        try {
          refreshMobileRewardWidget();
        } catch (t) {
          console.error("TCT mobile composite child refresh error:", t), yt(), D && kt();
        }
      }), 260);
    }
    function finalizeCompositeChildCompletion(t, e, n) {
      var r = parseInt(e || "0", 10);
      return !!t && (removeMobileResultRow(t, !0), isFinite(r) && r > 0 && syncCompositeParentAfterChildRemoval(r, !!n), 
      Y && (G = "", J = ""), Wt() && (A = a.innerHTML), isFinite(r) && r > 0 ? queueCompositeChildRefresh(r, !!n) : queueCompositeChildRefresh(0, !1), 
      !0);
    }
    function ge(t) {
      var e = parseInt(t.getAttribute("data-goal-id") || "0", 10);
      var u = String(t.getAttribute("data-goal-link-url") || "").trim();
      if (!isFinite(e) || e <= 0) {
        ie("Missing goal id.", "error");
      } else {
        var n = window.tctDashboard && window.tctDashboard.quickCompleteNonce ? window.tctDashboard.quickCompleteNonce : "";
        if (n) {
          var a = t.closest(".tct-domain-goal");
          var r = {};
          var i = a && a.getAttribute ? String(a.getAttribute("data-goal-type") || "").trim() : "";
          var compositeChildRow = t.closest ? t.closest('.tct-mobile-result[data-tct-composite-child="1"]') : null;
          var compositeParentGoalId = compositeChildRow ? parseInt(compositeChildRow.getAttribute("data-tct-composite-parent-id") || "0", 10) : 0;
          if ("anki_cards" === i) {
            var o = window.prompt("How many Anki cards did you study today?", "");
            if (null === o) {
              return;
            }
            var c = parseInt(String(o).trim(), 10);
            if (!isFinite(c) || c <= 0) {
              ie("Please enter a whole number of Anki cards.", "error");
              return;
            }
            r.anki_cards = c;
          }
          var l = [];
          (l = a ? Array.prototype.slice.call(a.querySelectorAll("[data-tct-complete-goal], [data-tct-timer-complete], [data-tct-start-timer]")) : [ t ]).forEach((function(t) {
            try {
              t.disabled = !0;
            } catch (t) {}
          }));
          de(Object.assign({
            action: "tct_quick_complete",
            nonce: n,
            goal_id: e
          }, r)).then((function(t) {
            if (t && t.success) {
              ie(t.data && t.data.message ? String(t.data.message) : "Completed.", "success");
              if (u) {
                window.location.href = u;
                return;
              }
              if (lt && lt.goalId === e) {
                fe({
                  silent: !0
                });
              } else if (a) {
                var n = a.querySelector("[data-tct-timer-overlay]");
                n && (n.hidden = !0);
              }
              try {
                var serverCompositeParentGoalId = t && t.data ? parseInt(t.data.compositeParentGoalId || "0", 10) : 0;
                var refreshCompositeParentGoalId = isFinite(serverCompositeParentGoalId) && serverCompositeParentGoalId > 0 ? serverCompositeParentGoalId : compositeParentGoalId;
                var shouldCollapseCompositeParent = !!(t && t.data && parseInt(t.data.compositeParentAllChildrenComplete || "0", 10) > 0);
                compositeChildRow ? finalizeCompositeChildCompletion(compositeChildRow, refreshCompositeParentGoalId, shouldCollapseCompositeParent) : he();
              } catch (r) {
                console.error("TCT mobile after completion refresh error:", r);
                yt();
              }
            } else {
              ie(t && t.data && t.data.message ? String(t.data.message) : "Could not complete.", "error");
            }
          })).catch((function(t) {
            t && "auth" === t.message || ie("Network error completing goal.", "error");
          })).finally((function() {
            l.forEach((function(t) {
              try {
                t.disabled = !1;
              } catch (t) {}
            }));
          }));
        } else {
          ie("Missing completion nonce.", "error");
        }
      }
    }
    function be() {
      return st || (st = document.querySelector("[data-tct-history-overlay]"));
    }
    function ye() {
      return dt || (dt = document.querySelector("[data-tct-history-modal]"));
    }
    function Se() {
      var t = be(), e = ye();
      t && (t.hidden = !0), e && (e.hidden = !0), ae();
    }
    function we(t) {
      var e = ye();
      if (e) {
        var n = e.querySelectorAll("[data-tct-history-panel]");
        Array.prototype.forEach.call(n, (function(t) {
          t.hidden = !0;
        }));
        var a = e.querySelector('[data-tct-history-panel="' + t + '"]');
        a && (a.hidden = !1);
        var r = e.querySelectorAll("[data-tct-history-tab]");
        Array.prototype.forEach.call(r, (function(e) {
          var n = String(e.getAttribute("data-tct-history-tab")) === String(t);
          e.classList.toggle("button-primary", n);
        }));
      }
    }
    function Pe(t, e) {
      var n = window.tctDashboard && window.tctDashboard.undoCompletionNonce ? window.tctDashboard.undoCompletionNonce : "";
      if (!n) return void ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error");
      var a = window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionConfirm ? window.tctDashboard.i18n.undoCompletionConfirm : "Undo this completion?";
      if (!window.confirm(a)) return;
      var r = ye(), i = 0, o = "";
      r && (i = parseInt(r.getAttribute("data-tct-history-goal-id") || "0", 10), o = r.getAttribute("data-tct-history-goal-title") || "");
      var c = e && e.getAttribute ? parseInt(e.getAttribute("data-goal-id") || "0", 10) : 0;
      isFinite(c) && c > 0 && (i = c), e && (e.disabled = !0), de({
        action: "tct_undo_completion",
        nonce: n,
        completion_id: t
      }).then((function(t) {
        if (t && t.success) {
          var e = t.data && t.data.message ? String(t.data.message) : "Completion undone.";
          ie(e), isFinite(i) && i > 0 && qe(i, o);
        } else {
          var n = t && t.data && t.data.message ? String(t.data.message) : window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.";
          ie(n, "error");
        }
      })).catch((function() {
        ie(window.tctDashboard && window.tctDashboard.i18n && window.tctDashboard.i18n.undoCompletionError ? window.tctDashboard.i18n.undoCompletionError : "Could not undo completion.", "error");
      })).finally((function() {
        e && (e.disabled = !1);
      }));
    }
    function qe(t, e) {
      var n = be(), a = ye();
      if (n && a) {
        a.setAttribute("data-tct-history-goal-id", String(t)), a.setAttribute("data-tct-history-goal-title", e ? String(e) : "");
        var r = a.querySelector("[data-tct-history-title]");
        r && (r.textContent = e ? "History: " + e : "History");
        var i = a.querySelector("[data-tct-history-loading]"), o = a.querySelector("[data-tct-history-error]");
        i && (i.hidden = !1), o && (o.hidden = !0, o.textContent = ""), n.hidden = !1, a.hidden = !1, 
        we("completions");
        var c = window.tctDashboard && window.tctDashboard.goalHistoryNonce ? window.tctDashboard.goalHistoryNonce : "";
        if (!c) return i && (i.hidden = !0), void ie("Missing history nonce.", "error");
        de({
          action: "tct_goal_history",
          nonce: c,
          goal_id: t
        }).then((function(t) {
          if (i && (i.hidden = !0), t && t.success && t.data) !function(t, e) {
            var n = ye();
            if (n) {
              var a = n.querySelector("[data-tct-history-summary]"), r = n.querySelector("[data-tct-history-completions]"), i = n.querySelector("[data-tct-history-goals-met]");
              if (n.querySelector("[data-tct-domain-heatmap]"), a) {
                var o = [];
                t && t.periodLabel && o.push(String(t.periodLabel)), t && void 0 !== t.totalCompletions && o.push("Completions: " + String(t.totalCompletions)), 
                t && void 0 !== t.totalPoints && o.push("Points: " + String(t.totalPoints)), t && t.timezone && o.push("TZ: " + String(t.timezone)), 
                a.textContent = o.join(" - ");
              }
              if (r) if (r.innerHTML = "", t && Array.isArray(t.completions) && t.completions.length) {
                var c = document.createElement("div");
                c.className = "tct-table-wrap";
                var l = document.createElement("table");
                l.className = "tct-table";
                for (var s = document.createElement("thead"), d = document.createElement("tr"), u = [ "Completed", "Source", "Points", "" ], m = 0; m < u.length; m++) {
                  var f = document.createElement("th");
                  f.textContent = u[m], 2 === m && (f.className = "tct-ledger-points-col"), d.appendChild(f);
                }
                s.appendChild(d), l.appendChild(s);
                for (var v = document.createElement("tbody"), p = 0; p < t.completions.length; p++) {
                  var h = t.completions[p] || {}, g = document.createElement("tr"), b = document.createElement("td");
                  b.textContent = h && h.completedAt ? String(h.completedAt) : "--", g.appendChild(b);
                  var y = document.createElement("td");
                  y.textContent = h && h.sourceLabel ? String(h.sourceLabel) : h && h.source ? String(h.source) : "--", 
                  g.appendChild(y);
                  var S = document.createElement("td");
                  S.className = "tct-ledger-points-col";
                  var w = h && void 0 !== h.points ? parseInt(h.points, 10) : 0;
                  isNaN(w) || 0 === w ? S.textContent = "0" : (S.textContent = (w > 0 ? "+" : "") + String(w), 
                  S.classList.add(w > 0 ? "tct-points-positive" : "tct-points-negative")), g.appendChild(S);
                  var C = document.createElement("td");
                  C.className = "tct-history-undo-col";
                  var A = document.createElement("button");
                  A.type = "button", A.className = "tct-history-undo-btn", A.setAttribute("data-tct-undo-completion", "1"), 
                  A.setAttribute("data-completion-id", h && h.id ? String(h.id) : ""), A.setAttribute("data-goal-id", String(e)), 
                  A.setAttribute("aria-label", "Undo"), A.title = "Undo", A.innerHTML = '<span class="dashicons dashicons-undo" aria-hidden="true"></span>', 
                  C.appendChild(A), g.appendChild(C), v.appendChild(g);
                }
                l.appendChild(v), c.appendChild(l), r.appendChild(c);
              } else {
                var E = document.createElement("div");
                E.className = "tct-muted", E.textContent = "No completions in this period.", r.appendChild(E);
              }
              if (i) {
                i.innerHTML = "";
                var L = t && Array.isArray(t.goalsMet) ? t.goalsMet : [];
                if (L.length) {
                  var M = document.createElement("table");
                  M.className = "widefat striped", M.style.width = "100%";
                  var _ = document.createElement("thead");
                  _.innerHTML = "<tr><th>Period</th><th>Count</th><th>Target</th><th>Status</th></tr>", 
                  M.appendChild(_);
                  var T = document.createElement("tbody");
                  L.forEach((function(t) {
                    var e = document.createElement("tr");
                    e.innerHTML = "<td>" + String(t.label || "") + "</td><td>" + String(t.count || 0) + "</td><td>" + String(t.target || 0) + "</td><td>" + String(t.met ? "Met" : "Missed") + "</td>", 
                    T.appendChild(e);
                  })), M.appendChild(T), i.appendChild(M);
                } else {
                  var I = document.createElement("div");
                  I.className = "tct-muted", I.textContent = "No goal-period history available.", 
                  i.appendChild(I);
                }
              }
            }
          }(t.data, t); else {
            var e = t && t.data && t.data.message ? String(t.data.message) : "Could not load history.";
            o && (o.hidden = !1, o.textContent = e), ie(e, "error");
          }
        })).catch((function(t) {
          if (!t || "auth" !== t.message) {
            i && (i.hidden = !0);
            var e = "Network error loading history.";
            o && (o.hidden = !1, o.textContent = e), ie(e, "error");
          }
        })), ae();
      } else ie("History modal not found.", "error");
    }
    function Ee() {
      return ut || (ut = document.querySelector("[data-tct-goal-overlay]"));
    }
    function Le() {
      return mt || (mt = document.querySelector("[data-tct-goal-modal]"));
    }
    function Me() {
      var t = Ee(), e = Le();
      t && (t.hidden = !0), e && (e.hidden = !0), ae();
    }
    function _e(t, e) {
      if (t) {
        var n = null == e ? "" : String(e);
        t.value = n, t.value !== n && (t.value = "");
      }
    }
    function Ce(t) {
      if (t) try {
        t.dispatchEvent(new Event("change", {
          bubbles: !0
        }));
      } catch (t) {}
    }
    function Ae(t) {
      if (t) {
        var n = t.querySelector("[data-tct-goal-type-select]"), a = t.querySelector("[data-tct-goal-type-hint]"), r = t.querySelector("[data-tct-interval-row-container]"), i = t.querySelector("[data-tct-intervals-json]"), o = t.querySelector("[data-tct-tracking-row]"), c = n && n.value || "positive", l = e(c), s = "anki_cards" === c;
        a && ("positive" === c ? a.textContent = "Positive goal with interval: earn points for each completion, bonus if you hit your target." : l ? a.textContent = "Positive goal without interval: earn points for each completion. No interval targets, and no bonus/penalty loop." : s ? a.textContent = "Anki cards goal: log the total number of cards you studied today. You get normal completion points immediately, then a later daily adjustment that scales with how far under or over target you were." : "never" === c ? a.textContent = "Never: each tap incurs an escalating penalty. Bonus for keeping the goal (0 taps)." : "harm_reduction" === c && (a.textContent = "Harm Reduction: taps within your allowed limit cost 0. Exceeding the limit incurs escalating penalties.")), 
        o && ((s || "never" === c || "harm_reduction" === c) && (o.hidden = !0), s && t.querySelector("[data-tct-tracking-select]") && (t.querySelector("[data-tct-tracking-select]").value = "manual"), 
        !s && "never" !== c && "harm_reduction" !== c && (o.hidden = !1)), r && (r.hidden = !!l), 
        i && (i.value = l ? "[]" : JSON.stringify(function(t, e) {
          if (!t) return [];
          var n = t.querySelector("[data-tct-intervals]");
          if (!n) return [];
          var a = n.querySelectorAll("[data-tct-interval-row]"), r = [];
          return Array.prototype.forEach.call(a, (function(t) {
            var n = t.querySelector("[data-tct-interval-target]"), a = t.querySelector("[data-tct-interval-span]"), i = t.querySelector("[data-tct-interval-unit]"), o = t.querySelector("[data-tct-interval-target-label]"), c = n ? parseInt(n.value || "0", 10) : 0, l = a ? parseInt(a.value || "1", 10) : 1, s = i ? String(i.value || "day") : "day";
            e && (o && (o.textContent = "Cards"), a && (a.value = "1", a.hidden = !0, a.disabled = !0), 
            i && (i.value = "day", i.hidden = !0, i.disabled = !0), (!isFinite(c) || c < 0) && (c = 0), 
            l = 1, s = "day"), !e && (o && (o.textContent = "Completions"), a && (a.hidden = !1, 
            a.disabled = !1), i && (i.hidden = !1, i.disabled = !1)), (!isFinite(c) || c < 0) && (c = 0), 
            (!isFinite(l) || l < 1) && (l = 1), r.push({
              target: c,
              period_unit: s,
              period_span: l,
              period_mode: "calendar"
            });
          })), r;
        }(t, s)), Ce(i));
      }
    }
    function Te(t) {
      var e = Ee(), n = Le();
      if (e && n) {
        e.hidden = !1, n.hidden = !1;
        var a = n.querySelector("[data-tct-modal-title]");
        a && (a.textContent = "Edit Goal");
        var r = n.querySelector("[data-tct-goal-form-mode]");
        r && (r.value = "edit");
        var i = n.querySelector("[data-tct-goal-id]");
        i && (i.value = t && t.goal_id ? String(t.goal_id) : "");
        var o = n.querySelector("[data-tct-goal-name]");
        o && (o.value = t && t.goal_name ? String(t.goal_name) : ""), !function() {
          var e = t && t.link_url ? String(t.link_url).trim() : "", a = "", r = "", i = e;
          if (0 === e.toLowerCase().indexOf("tel:")) a = e.replace(/^tel:/i, "").replace(/\D+/g, ""), 
          i = ""; else if (0 === e.toLowerCase().indexOf("sms:")) r = e.replace(/^sms:/i, "").replace(/\D+/g, ""), 
          i = "";
          n.querySelector("[data-tct-goal-link-url]") && (n.querySelector("[data-tct-goal-link-url]").value = i), 
          n.querySelector("[data-tct-goal-phone-number]") && (n.querySelector("[data-tct-goal-phone-number]").value = a), 
          n.querySelector("[data-tct-goal-sms-number]") && (n.querySelector("[data-tct-goal-sms-number]").value = r);
        }(), n.querySelector("[data-tct-goal-notes]") && (n.querySelector("[data-tct-goal-notes]").value = t && t.goal_notes ? String(t.goal_notes) : ""), 
        _e(n.querySelector("[data-tct-goal-type-select]"), t && t.goal_type ? t.goal_type : "positive"), 
        _e(n.querySelector("[data-tct-tracking-select]"), t && t.tracking_mode ? t.tracking_mode : "todoist"), 
        _e(n.querySelector("[data-tct-label-select]"), t && t.label_name ? t.label_name : ""), 
        _e(n.querySelector("[data-tct-role-select]"), t && void 0 !== t.role_id ? String(t.role_id) : ""), 
        _e(n.querySelector("[data-tct-importance-select]"), t && t.importance ? t.importance : "medium"), 
        _e(n.querySelector("[data-tct-effort-select]"), t && t.effort ? t.effort : "medium");
        var c = n.querySelector("[data-tct-threshold-input]");
        c && (c.value = t && void 0 !== t.threshold && null !== t.threshold ? String(t.threshold) : "");
        var l = n.querySelector("[data-tct-plant-name]");
        l && (l.value = t && t.plant_name ? String(t.plant_name) : "", Ce(l));
        var s = n.querySelector("[data-tct-intervals-json]"), d = t && Array.isArray(t.intervals) ? t.intervals : [];
        s && (s.value = JSON.stringify(d), Ce(s)), function(t) {
          var e = Le();
          if (e) {
            var n = e.querySelector("[data-tct-intervals]"), a = e.querySelector("template[data-tct-interval-template]"), r = e.querySelector("[data-tct-intervals-json]");
            if (n && a && r && !n.querySelector("[data-tct-interval-row]")) {
              n.innerHTML = "";
              var i = Array.isArray(t) ? t : [];
              i.length || (i = [ {
                target: 1,
                period_unit: "day",
                period_span: 1,
                period_mode: "calendar"
              } ]), i.forEach((function(t) {
                var e = document.importNode(a.content, !0), r = e.querySelector("[data-tct-interval-row]");
                if (r) {
                  var i = r.querySelector("[data-tct-interval-target]"), o = r.querySelector("[data-tct-interval-span]"), c = r.querySelector("[data-tct-interval-unit]");
                  i && (i.value = t && void 0 !== t.target ? String(t.target) : "1"), o && (o.value = t && void 0 !== t.period_span ? String(t.period_span) : "1"), 
                  c && _e(c, t && t.period_unit ? String(t.period_unit) : "day"), n.appendChild(e);
                }
              })), n.addEventListener("input", (function(t) {
                var e = t.target;
                e && e.matches && e.matches("[data-tct-interval-target], [data-tct-interval-span], [data-tct-interval-unit]") && o();
              })), o();
            }
          }
          function o() {
            var t = n.querySelectorAll("[data-tct-interval-row]"), e = [];
            Array.prototype.forEach.call(t, (function(t) {
              var n = t.querySelector("[data-tct-interval-target]"), a = t.querySelector("[data-tct-interval-span]"), r = t.querySelector("[data-tct-interval-unit]"), i = n ? parseInt(n.value || "0", 10) : 0, o = a ? parseInt(a.value || "1", 10) : 1, c = r ? String(r.value || "day") : "day";
              (!isFinite(i) || i < 0) && (i = 0), (!isFinite(o) || o < 1) && (o = 1), e.push({
                target: i,
                period_unit: c,
                period_span: o,
                period_mode: "calendar"
              });
            })), r.value = JSON.stringify(e), Ce(r);
          }
        }(d), function(t) {
          if (t) {
            var e = t.querySelector("[data-tct-goal-type-select]");
            e && (e._tctGoalTypeBound || (e._tctGoalTypeBound = !0, e.addEventListener("change", (function() {
              Ae(t);
            }))));
          }
        }(n), Ae(n);
        var u = n.querySelector("[data-tct-timer-enabled]"), m = n.querySelector("[data-tct-timer-hours]"), f = n.querySelector("[data-tct-timer-minutes]"), v = n.querySelector("[data-tct-timer-seconds]"), p = t && void 0 !== t.timer_duration_seconds ? parseInt(t.timer_duration_seconds, 10) : 0;
        if ((!isFinite(p) || p < 0) && (p = 0), u && (u.checked = p > 0, Ce(u)), m && f && v) {
          var h = Math.floor(p / 3600), g = Math.floor(p % 3600 / 60), b = p % 60;
          m.value = String(h), f.value = String(g), v.value = String(b);
        }
        _e(n.querySelector("[data-tct-alarm-sound]"), t && t.alarm_sound ? t.alarm_sound : ""), 
        _e(n.querySelector("[data-tct-alarm-duration]"), t && t.alarm_duration ? t.alarm_duration : "");
        var y = n.querySelector("[data-tct-alarm-vibration]");
        y && (y.checked = !(!t || !t.alarm_vibration));
        var S = n.querySelector("[data-tct-visible-after-time]");
        S && (S.value = t && t.visible_after_time ? String(t.visible_after_time) : "");
        var fav = document.querySelector("[data-tct-favorite-enabled]");
        fav && (fav.checked = 1 === parseInt(t && t.is_favorite, 10) || 1 === t.is_favorite || "1" === t.is_favorite || !0 === t.is_favorite);
        try {
          o && o.focus();
        } catch (t) {}
        ae();
      } else ie("Edit modal not found.", "error");
    }
    function Ie() {
      var t = se();
      if (t) {
        var e = new FormData;
        e.append("action", "tct_mobile_heartbeat"), fetch(t, {
          method: "POST",
          credentials: "same-origin",
          headers: {
            "X-Requested-With": "XMLHttpRequest"
          },
          body: e
        }).then((function(t) {
          return 401 === t.status || 403 === t.status ? (oe(), null) : t.json();
        })).then((function(t) {
          if (t && t.success && t.data && t.data.nonces) {
            var e = t.data.nonces;
            window.tctMobile && e.searchNonce && (tctMobile.searchNonce = e.searchNonce), window.tctDashboard && (e.quickCompleteNonce && (tctDashboard.quickCompleteNonce = e.quickCompleteNonce), 
            e.goalHistoryNonce && (tctDashboard.goalHistoryNonce = e.goalHistoryNonce), e.undoCompletionNonce && (tctDashboard.undoCompletionNonce = e.undoCompletionNonce));
          }
        })).catch((function() {}));
      }
    }
    function xe(t) {
      var e = t || {}, n = document.createElement("div");
      n.className = "tct-mobile-overlay";
      var a = document.createElement("div");
      a.className = "tct-mobile-overlay-inner", e.cls && a.classList.add(e.cls);
      var r = document.createElement("button");
      if (r.type = "button", r.className = "tct-mobile-overlay-close", r.innerHTML = "&times;", 
      r.setAttribute("aria-label", "Close"), a.appendChild(r), e.title) {
        var i = document.createElement("div");
        i.className = "tct-mobile-overlay-title", i.textContent = e.title, a.appendChild(i);
      }
      var o = document.createElement("div");
      function c() {
        try {
          document.body.removeChild(n);
        } catch (t) {}
      }
      return o.className = "tct-mobile-overlay-body", a.appendChild(o), n.appendChild(a), 
      r.addEventListener("click", c), n.addEventListener("click", (function(t) {
        t.target === n && c();
      })), document.body.appendChild(n), {
        overlay: n,
        body: o,
        close: c
      };
    }
  }));
}();

!function() {
  "use strict";
  function t(t) {
    if ("function" == typeof (window.TCT && window.TCT.onReady)) return window.TCT.onReady(t);
    if ("loading" === document.readyState) return document.addEventListener("DOMContentLoaded", t);
    t();
  }
  t((function() {
    if (!window.tctMobile || !tctMobile.ajaxUrl || tctMobile.features && tctMobile.features.favoritesSwipe === !1) return;
    var t = document.querySelector(".tct-mobile");
    if (!t) return;
    var e = t.querySelector("[data-tct-mobile-results]");
    if (!e) return;
    var a = t.querySelector("[data-tct-mobile-favorites-bar]");
    if (!a) return;
    var i = t.querySelector("[data-tct-mobile-favorites-back]");
    var o = !1;
    var n = {
      html: e.innerHTML || "",
      query: "",
      chip: "",
      scrollY: window.scrollY || 0
    };
    var r = "";
    var l = !1;
    var c = null;
    var s = 0;
    var d = 0;
    var u = 0;
    var m = !1;
    var f = 0;
    var v = 0;
    var p = !1;
    var h = !1;

    function g() {
      return !!t.querySelector(".tct-mobile-session-expired") || !!t.querySelector("[data-tct-history-overlay]:not([hidden])") || !!t.querySelector("[data-tct-goal-overlay]:not([hidden])");
    }

    function y() {
      var e = t.querySelector("[data-tct-mobile-search]");
      return e && "string" == typeof e.value ? e.value.trim() : "";
    }

    function b() {
      var e = t.querySelectorAll("[data-tct-mobile-chip]");
      if (!e || !e.length) return "";
      for (var a = 0; a < e.length; a++) {
        var i = e[a];
        if ("true" === i.getAttribute("aria-pressed")) return i.getAttribute("data-tct-mobile-chip") || "";
      }
      return "";
    }

    function S(t) {
      var e = String(t || "").toLowerCase();
      return !!e && (e.indexOf('class="tct-mobile-loading"') >= 0 || e.indexOf("loading favorites") >= 0 || e.indexOf("loading daily goals") >= 0 || e.indexOf(">loading...<") >= 0);
    }

    function w() {
      var t = !!e.querySelector(".tct-mobile-result");
      t ? e.classList.add("tct-mobile-results-populated") : e.classList.remove("tct-mobile-results-populated");
      t && !e.querySelector(".tct-mobile-bottom-spacer") && e.insertAdjacentHTML("beforeend", '<div class="tct-mobile-bottom-spacer"></div>');
      "function" == typeof window.tctDashboardEnhance && window.tctDashboardEnhance(e.closest(".tct-dashboard") || void 0);
    }

    function C(t) {
      d++;
      e.innerHTML = t || "";
      w();
      window.setTimeout((function() {
        d = Math.max(0, d - 1);
      }), 0);
    }

    function N() {
      if (o) return;
      var t = e.innerHTML || "";
      n = {
        html: S(t) ? "" : t,
        query: y(),
        chip: b(),
        scrollY: window.scrollY || 0
      };
    }

    function A() {
      r = "";
      l = !0;
      n.html = "";
    }

    function k(t) {
      if (t && "function" == typeof t.abort) try {
        t.abort();
      } catch (t) {}
    }

    function E(t) {
      var a = ++u;
      k(c);
      c = "undefined" != typeof AbortController ? new AbortController : null;
      var i = new URLSearchParams;
      Object.keys(t || {}).forEach((function(e) {
        void 0 !== t[e] && null !== t[e] && i.append(e, String(t[e]));
      }));
      var o = {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        credentials: "same-origin",
        body: i.toString()
      };
      c && (o.signal = c.signal);
      return fetch(tctMobile.ajaxUrl, o).then((function(t) {
        return t.json();
      })).then((function(t) {
        return a !== u ? "" : t && t.success && t.data && "string" == typeof t.data.html ? t.data.html : "";
      })).catch((function(t) {
        if (t && "AbortError" === t.name) return "";
        throw t;
      }));
    }

    function q() {
      return E({
        action: "tct_mobile_favorites",
        nonce: tctMobile.searchNonce || ""
      });
    }

    function D() {
      var t = y();
      var e = b();
      var a = {
        action: "tct_mobile_daily_default",
        nonce: tctMobile.searchNonce || ""
      };
      if (t) {
        a.action = "tct_mobile_search";
        a.query = t;
      } else if (e) {
        a.action = "tct_mobile_chip_filter";
        a.filter = e;
        a.nonce = tctMobile.chipNonce || tctMobile.searchNonce || "";
      }
      return {
        key: {
          query: t,
          chip: e
        },
        payload: a
      };
    }

    function F(t) {
      if (m) return;
      m = !0;
      if (t) {
        C('<div class="tct-mobile-no-results">Loading favorites...</div>');
      }
      q().then((function(t) {
        m = !1;
        if (!o) return;
        r = t || '<div class="tct-mobile-no-results">No favorites yet.</div>';
        l = !1;
        C(r);
      })).catch((function() {
        m = !1;
        if (!o) return;
        r = "";
        l = !1;
        C('<div class="tct-mobile-no-results">Could not load favorites.</div>');
      }));
    }

    function R() {
      if (o || g() || t.classList.contains("tct-mobile-view-domain")) return;
      N();
      o = !0;
      t.classList.add("tct-mobile-view-favorites");
      a.hidden = !1;
      window.scrollTo(0, 0);
      if (r && !l) {
        C(r);
        return;
      }
      F(!0);
    }

    function B() {
      var t = D();
      if (n.html && n.query === t.key.query && n.chip === t.key.chip) {
        C(n.html);
        window.scrollTo(0, n.scrollY || 0);
        return;
      }
      var a = n.scrollY || 0;
      C('<div class="tct-mobile-no-results">Loading...</div>');
      E(t.payload).then((function(e) {
        var i = e || "";
        n = {
          html: i,
          query: t.key.query,
          chip: t.key.chip,
          scrollY: a
        };
        C(i);
      })).catch((function() {
        C('<div class="tct-mobile-no-results">Could not load goals.</div>');
      })).finally((function() {
        window.scrollTo(0, a);
      }));
    }

    function O() {
      if (!o) return;
      o = !1;
      t.classList.remove("tct-mobile-view-favorites");
      a.hidden = !0;
      B();
    }

    function j() {
      if (d) return;
      if (o) {
        if (s) return;
        s = window.setTimeout((function() {
          s = 0;
          if (!o) return;
          if (r && !l) {
            C(r);
            return;
          }
          F(!1);
        }), 0);
      } else {
        N();
      }
    }

    function H(t) {
      if (!t || !t.target || !t.target.closest) return;
      var e = t.target.closest("[data-tct-mobile-row-complete], [data-tct-complete-goal], [data-tct-start-timer], [data-tct-timer-complete], [data-tct-composite-parent-complete], [data-tct-ledger-undo], [data-tct-undo-completion], .tct-sleep-primary-btn");
      if (e) {
        A();
      }
    }

    if (i) {
      i.addEventListener("click", (function(t) {
        t.preventDefault();
        O();
      }));
    }

    e.addEventListener("click", H, !0);

    document.addEventListener("submit", (function(t) {
      var e = t && t.target && t.target.closest ? t.target.closest("[data-tct-goal-form]") : null;
      if (e) {
        A();
      }
    }), !0);

    if (window.MutationObserver) {
      new MutationObserver(j).observe(e, {
        childList: !0
      });
    }

    t.addEventListener("touchstart", (function(t) {
      if (g()) return;
      h = !!(t.currentTarget && t.currentTarget.classList && t.currentTarget.classList.contains("tct-mobile-view-domain"));
      var e = t.touches && t.touches[0];
      e && (f = e.clientX, v = e.clientY, p = !0);
    }), {
      passive: !0
    });

    t.addEventListener("touchend", (function(t) {
      if (!p) return;
      p = !1;
      if (g()) return;
      var e = t.changedTouches && t.changedTouches[0];
      if (!e) return;
      var a = e.clientX - f;
      var i = e.clientY - v;
      if (2 * Math.abs(a) < Math.abs(i)) return;
      var n = Math.max(.15 * window.innerWidth, 50);
      a > n && !o && !h ? R() : a < -n && o && O();
    }), {
      passive: !0
    });
  }));
}();

!function() {
  "use strict";
  if ("undefined" == typeof window || "undefined" == typeof document) return;
  window.TCT = window.TCT || {};
  if (window.TCT.__tctCompositeParentCompleteBound) return;
  window.TCT.__tctCompositeParentCompleteBound = !0;
  function t() {
    return window.tctDashboard && window.tctDashboard.ajaxUrl ? window.tctDashboard.ajaxUrl : window.tctMobile && window.tctMobile.ajaxUrl ? window.tctMobile.ajaxUrl : "";
  }
  function n() {
    return window.tctDashboard && window.tctDashboard.quickCompleteNonce ? window.tctDashboard.quickCompleteNonce : "";
  }
  function o(e) {
    var n = t();
    if (!n) return Promise.reject(new Error("missing_ajax_url"));
    var o = new FormData;
    return Object.keys(e || {}).forEach((function(t) {
      void 0 !== e[t] && null !== e[t] && o.append(t, e[t]);
    })), fetch(n, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "X-Requested-With": "XMLHttpRequest"
      },
      body: o
    }).then((function(e) {
      return e.json();
    }));
  }
  function i(t, n) {
    var o = document.querySelectorAll('[data-tct-composite-parent-complete][data-goal-id="' + String(t) + '"]');
    Array.prototype.forEach.call(o, (function(t) {
      if (t) if (n) {
        try {
          t.disabled = !0;
        } catch (e) {}
        t.hasAttribute("data-tct-orig-html") || t.setAttribute("data-tct-orig-html", t.innerHTML);
        var o = t.className || "";
        -1 !== o.indexOf("tct-mobile-row-complete-btn") ? t.innerHTML = '<span class="tct-mobile-row-complete-text">...</span>' : t.textContent = "Completing...";
      } else {
        try {
          t.disabled = !1;
        } catch (e) {}
        var i = t.getAttribute("data-tct-orig-html");
        null !== i && (t.innerHTML = i, t.removeAttribute("data-tct-orig-html"));
      }
    }));
  }
  function r(t) {
    if (window.TCT && "function" == typeof window.TCT.getErrorMessage) return window.TCT.getErrorMessage(t, "Could not complete child goals.");
    return t && t.data && t.data.message ? String(t.data.message) : "Could not complete child goals.";
  }
  function a(t) {
    var e = window.TCT && "function" == typeof window.TCT.normalizeResponse ? window.TCT.normalizeResponse(t) : {
      ok: !!(t && t.success),
      data: t && t.data ? t.data : null
    };
    return !!(e && e.ok);
  }
  function c(e) {
    var t = e && e.target && e.target.closest ? e.target.closest("[data-tct-composite-parent-complete]") : null;
    if (!t) return;
    e.preventDefault(), e.stopPropagation();
    var c = parseInt(t.getAttribute("data-goal-id") || "0", 10);
    if (!isFinite(c) || c <= 0) return void window.alert("Missing parent goal.");
    if (t.disabled) return;
    var d = n();
    if (!d) return void window.alert("Missing completion nonce.");
    var l = String(t.getAttribute("data-goal-name") || "").trim(), u = l ? 'Complete all child goals for "' + l + '"?' : "Complete all child goals for this parent?";
    if (u += "\n\nChildren that are blocked right now will be skipped.", !window.confirm(u)) return;
    i(c, !0), t.blur && t.blur(), t.setAttribute("aria-busy", "true"), o({
      action: "tct_composite_complete_parent",
      nonce: d,
      goal_id: c
    }).then((function(e) {
      if (a(e)) {
        var t = e && e.data && e.data.message ? String(e.data.message) : "Child goals completed.";
        t && window.alert(t), window.location.reload();
      } else window.alert(r(e));
    })).catch((function() {
      window.alert("Network error completing child goals.");
    })).finally((function() {
      i(c, !1), t.removeAttribute("aria-busy");
    }));
  }
  document.addEventListener("click", c, !0);
}();
