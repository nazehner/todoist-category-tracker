!function(){"use strict";function t(t,e){if(!t||1!==t.nodeType)return!1;var a=Element.prototype,r=a.matches||a.msMatchesSelector||a.webkitMatchesSelector;return!!r&&r.call(t,e)}function e(e,a){for(var r=e;r&&1===r.nodeType;){if(t(r,a))return r;r=r.parentElement}return null}function a(t){try{return window.localStorage.getItem(t)}catch(t){return null}}function r(t,e){try{window.localStorage.setItem(t,e)}catch(t){}}function n(t){var e=a("tct_week_starts_on");if("0"===e||"1"===e)return parseInt(e,10);if("number"==typeof t&&(0===t||1===t))return t;if(window.tctDashboard&&void 0!==window.tctDashboard.startOfWeek){var r=parseInt(window.tctDashboard.startOfWeek,10);if(!isNaN(r))return 0===r?0:1}return 1}var i=window.TCT||{},o=i&&"function"==typeof i.isPositiveNoIntervalGoalType?i.isPositiveNoIntervalGoalType:function(t){return"positive_no_int"===(t||"").toString().trim()},l=i&&"function"==typeof i.setHidden?i.setHidden:function(t,e){t&&(e?t.setAttribute("hidden","hidden"):t.removeAttribute("hidden"))},d=i&&"function"==typeof i.onReady?i.onReady:function(t){"loading"===document.readyState?document.addEventListener("DOMContentLoaded",t):t()};function c(t){var e=parseFloat(t);if(isNaN(e)||e<=0)return"0";var a=Math.round(e);return Math.abs(e-a)<.05?String(a):e.toFixed(1)}var s={},u={};function p(t){if(!t||"string"!=typeof t)return null;var e=t.split("-");if(3!==e.length)return null;var a=parseInt(e[0],10),r=parseInt(e[1],10),n=parseInt(e[2],10);return isNaN(a)||isNaN(r)||isNaN(n)?null:{y:a,m:r,d:n}}function m(t){return new Date(Date.UTC(t.y,t.m-1,t.d,12,0,0))}function v(t){return t=parseInt(t,10),isNaN(t)&&(t=0),(t<10?"0":"")+String(t)}function g(t){var a=t?e(t,".tct-dashboard"):null;if(a||(a=document.querySelector(".tct-dashboard[data-tct-today]")),a){var r=(a.getAttribute("data-tct-today")||"").trim();if(r)return r}var n=new Date;return n.getFullYear()+"-"+v(n.getMonth()+1)+"-"+v(n.getDate())}function h(t,e){if(t){var a=t.querySelector(".tct-domain-monthbar-strip"),r=t.querySelector('[data-tct-monthbar-weekticks="1"]'),n=t.querySelector('[data-tct-monthbar-weeklabels="1"]');if(a&&r&&n){var i=a.querySelectorAll("[data-date]");if(i&&i.length){r.innerHTML="",n.innerHTML="";for(var o=[],l=0;l<i.length;l++){var d=i[l].getAttribute("data-date")||"";d&&o.push(d)}if(o.length){var c=g(t),s=-1;if(c)for(var u=0;u<o.length;u++)if(o[u]===c){s=u;break}var v=p(o[0]);if(v){m(v).getUTCDay();for(var h=0===e?0:1,f=[0],b=0;b<o.length;b++){var y=p(o[b]);if(y){var S=m(y).getUTCDay();0!==b&&S===h&&f.push(b)}}f[f.length-1]!==o.length&&f.push(o.length);for(var w=o.length,C=0;C<f.length-1;C++){var N=f[C],A=N/w*100,k=document.createElement("span");k.className="tct-domain-monthbar-week-tick",k.style.left=A.toFixed(4)+"%",r.appendChild(k);var E=f[C+1],q=(N+E)/2/w*100,D=document.createElement("span");D.className="tct-domain-monthbar-week-label",D.style.left=q.toFixed(4)+"%",D.textContent="Wk "+String(C+1),s>=N&&s<E&&(D.className+=" tct-heatmap-current-label"),n.appendChild(D)}}}}}}}function f(t){if(t)for(var e=n(1),a=t.querySelectorAll("[data-tct-domain-monthbar]"),r=0;r<a.length;r++)h(a[r],e)}function b(t){if(t){var e=t.querySelector(".tct-domain-weekbar-strip"),a=t.querySelector('[data-tct-weekbar-dayticks="1"]'),r=t.querySelector('[data-tct-weekbar-daylabels="1"]');if(e&&a&&r){var n=e.querySelectorAll("[data-date]");if(n&&n.length){a.innerHTML="",r.innerHTML="";for(var i=n.length,o=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],l=g(t),d=0;d<i;d++){var c=document.createElement("span");c.className="tct-domain-weekbar-day-tick",c.style.left=String(d/i*100)+"%",a.appendChild(c);var s=document.createElement("span");s.className="tct-domain-weekbar-day-label",s.style.left=String((d+.5)/i*100)+"%";var u=(n[d].getAttribute("data-date")||"").trim(),v=p(u),h=v?m(v):null,f=h&&!isNaN(h.getTime())?h.getUTCDay():d%7;(isNaN(f)||f<0||f>6)&&(f=d%7),s.textContent=o[f],l&&u&&u===l&&(s.className+=" tct-heatmap-current-label"),r.appendChild(s)}}}}}function y(t){if(t)for(var e=t.querySelectorAll('[data-tct-domain-weekbar="1"]'),a=0;a<e.length;a++)b(e[a])}function S(t,e){if(t){var a=t.querySelector(".tct-stats-section-header");if(a){var r=t.querySelector(".tct-stats-domain");if(r){var n=null,i=r.querySelector(".tct-stats-domain-row")||r;if("year"===e){var o=i.querySelector("[data-tct-domain-yearbar], .tct-domain-yearbar");o&&(n=o.querySelector(".tct-domain-yearbar-monthlabels"))}else if("month"===e){var l=i.querySelector("[data-tct-domain-monthbar], .tct-domain-monthbar");l&&(n=l.querySelector('[data-tct-monthbar-weeklabels="1"], .tct-domain-monthbar-weeklabels'))}else if("week"===e){var d=i.querySelector("[data-tct-domain-weekbar], [data-tct-domain-weekbar-static], .tct-domain-weekbar");d&&(n=d.querySelector('[data-tct-weekbar-daylabels="1"], .tct-domain-weekbar-daylabels'))}if(n&&n.querySelector("span")){var c=t.querySelector('.tct-stats-label-strip[data-tct-period="'+e+'"]'),s=c?c.querySelector(".tct-stats-label-strip-content"):null;if(!c){var u=document.createElement("div");u.className="tct-stats-week-row tct-stats-label-strip",u.setAttribute("data-tct-label-strip","1"),u.setAttribute("data-tct-period",e);var p=document.createElement("div");p.className="tct-stats-label-strip-spacer",p.setAttribute("aria-hidden","true"),(s=document.createElement("div")).className="tct-stats-label-strip-content",u.appendChild(p),u.appendChild(s),a.insertAdjacentElement("afterend",u),c=u}s&&(s.innerHTML="",s.appendChild(n.cloneNode(!0)),t.setAttribute("data-tct-has-label-strip","1"))}}}}}function w(t){if(t){var e=t.querySelector('.tct-statistics[data-tct-statistics="1"]');if(e){var a=e.querySelector(".tct-stats-section-week"),r=e.querySelector(".tct-stats-section-month"),n=e.querySelector(".tct-stats-section-year");n&&S(n,"year"),r&&S(r,"month"),a&&S(a,"week")}}}function C(t){if(t)try{t._tctStatsLabelStripTimer&&window.clearTimeout(t._tctStatsLabelStripTimer),t._tctStatsLabelStripTimer=window.setTimeout(function(){t._tctStatsLabelStripTimer=null,w(t)},50)}catch(e){w(t)}}function N(t,e){if(t){var a=t.querySelector('[data-tct-stats-expand-toggle="roles"]'),r=t.querySelector('[data-tct-stats-expand-toggle="goals"]');if(a){var n="roles"===e;a.setAttribute("aria-pressed",n?"true":"false")}if(r){var i="goals"===e;r.setAttribute("aria-pressed",i?"true":"false")}e?t.setAttribute("data-tct-stats-expand-mode",e):t.removeAttribute("data-tct-stats-expand-mode")}}function A(t,e){if(t){var a=t.querySelectorAll(".tct-stats-domain-toggle-input"),r=t.querySelectorAll(".tct-stats-role-toggle-input");if("roles"!==e)if("goals"!==e){for(var n=0;n<a.length;n++)a[n].checked=!1;for(var i=0;i<r.length;i++)r[i].checked=!1}else{for(var o=0;o<a.length;o++)a[o].checked=!0;for(var l=0;l<r.length;l++)r[l].checked=!0}else{for(var d=0;d<a.length;d++)a[d].checked=!0;for(var c=0;c<r.length;c++)r[c].checked=!1}}}function k(t){t&&(t.hasAttribute("data-tct-stats-expand-init")||(t.setAttribute("data-tct-stats-expand-init","1"),t.addEventListener("click",function(a){var r=a.target;r&&r.nodeType&&1!==r.nodeType&&(r=r.parentElement);var n=e(r,"[data-tct-stats-expand-toggle]");if(n&&t.contains(n)){var i=e(n,".tct-stats-section");if(i){var o=(n.getAttribute("data-tct-stats-expand-toggle")||"").toLowerCase();if("roles"===o||"goals"===o){a.preventDefault();var l=(i.getAttribute("data-tct-stats-expand-mode")||"").toLowerCase();if(l===o)return i._tctStatsExpandSnapshot?function(t){if(t){var e=t._tctStatsExpandSnapshot;if(e){for(var a=t.querySelectorAll(".tct-stats-domain-toggle-input, .tct-stats-role-toggle-input"),r=0;r<a.length;r++){var n=a[r];n&&n.id&&void 0!==e[n.id]&&(n.checked=!!e[n.id])}t._tctStatsExpandSnapshot=null}}}(i):A(i,""),void N(i,"");l||function(t){if(!t)return null;for(var e=t.querySelectorAll(".tct-stats-domain-toggle-input, .tct-stats-role-toggle-input"),a={},r=0;r<e.length;r++){var n=e[r];n&&n.id&&(a[n.id]=n.checked?1:0)}t._tctStatsExpandSnapshot=a}(i),A(i,o),N(i,o)}}}})))}function E(t,a){if(t&&window.tctDashboard&&tctDashboard.ajaxUrl){var r=t.querySelector('[data-tct-domain-weekbar="1"]');if(r){var i=parseInt(r.getAttribute("data-domain-id")||"0",10);(isNaN(i)||i<0)&&(i=0),null==a&&(a=n(1));var o=new FormData;o.append("action","tct_domain_weekbar"),o.append("nonce",tctDashboard&&tctDashboard.domainWeekbarNonce?tctDashboard.domainWeekbarNonce:""),o.append("domain_id",String(i)),o.append("week_starts_on",String(a)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:o}).then(function(t){return t.json()}).then(function(a){if(a&&a.success&&a.data&&a.data.html){var n=document.createElement("div");n.innerHTML=String(a.data.html);var i=n.firstElementChild;if(i&&r.parentNode){r.parentNode.replaceChild(i,r),b(i);var o=e(t,".tct-dashboard");o&&C(o)}}}).catch(function(){})}}}function q(t,e){if(t)for(var a=n(1),r=t.querySelectorAll(".tct-domain-row"),i=0;i<r.length;i++){var o=r[i];if(o){if(!e){var l=o.querySelector('[data-tct-domain-weekbar="1"]');if(l){var d=parseInt(l.getAttribute("data-week-starts-on")||"",10);if(!isNaN(d)&&d===a)continue}}E(o,a)}}}function D(t){if(t&&window.tctDashboard&&tctDashboard.ajaxUrl){var a=t.querySelector('[data-tct-domain-yearbar="1"]'),r=t.querySelector('[data-tct-domain-monthbar="1"]'),i=t.querySelector('[data-tct-domain-weekbar="1"]'),o=0;if(a?o=parseInt(a.getAttribute("data-domain-id")||"0",10):r?o=parseInt(r.getAttribute("data-domain-id")||"0",10):i&&(o=parseInt(i.getAttribute("data-domain-id")||"0",10)),(isNaN(o)||o<0)&&(o=0),o>0&&function(t){var e=parseInt(t,10);if(!(isNaN(e)||e<=0)){var a=String(e);s[a]&&delete s[a],u[a]&&delete u[a]}}(o),i){var l=n(1),d=new FormData;d.append("action","tct_domain_weekbar"),d.append("nonce",tctDashboard&&tctDashboard.domainWeekbarNonce?tctDashboard.domainWeekbarNonce:""),d.append("domain_id",String(o)),d.append("week_starts_on",String(l)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:d}).then(function(t){return t.json()}).then(function(a){if(a&&a.success&&a.data&&a.data.html){var r=document.createElement("div");r.innerHTML=String(a.data.html);var n=r.firstElementChild;if(n&&i.parentNode){i.parentNode.replaceChild(n,i),b(n);var o=e(t,".tct-dashboard");o&&C(o)}}}).catch(function(){})}if(a){var c=parseInt(a.getAttribute("data-year")||"0",10);(isNaN(c)||c<1970)&&(c=0);var p=new FormData;p.append("action","tct_domain_yearbar"),p.append("nonce",tctDashboard.domainYearbarNonce||""),p.append("domain_id",String(o)),p.append("year",String(c)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:p}).then(function(t){return t.json()}).then(function(r){if(r&&r.success&&r.data&&r.data.html){var n=document.createElement("div");n.innerHTML=String(r.data.html);var i=n.firstElementChild;if(i&&a.parentNode){a.parentNode.replaceChild(i,a);var o=e(t,".tct-dashboard");o&&C(o)}}}).catch(function(){})}if(r){var m=parseInt(r.getAttribute("data-year")||"0",10),v=parseInt(r.getAttribute("data-month")||"0",10);(isNaN(m)||m<1970)&&(m=0),(isNaN(v)||v<1||v>12)&&(v=0);var g=new FormData;g.append("action","tct_domain_monthbar"),g.append("nonce",tctDashboard.domainMonthbarNonce||""),g.append("domain_id",String(o)),g.append("year",String(m)),g.append("month",String(v)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:g}).then(function(t){return t.json()}).then(function(a){if(a&&a.success&&a.data&&a.data.html){var i=document.createElement("div");i.innerHTML=String(a.data.html);var o=i.firstElementChild;if(o&&r.parentNode){r.parentNode.replaceChild(o,r),h(o,n(1));var l=e(t,".tct-dashboard");l&&C(l)}}}).catch(function(){})}}}function x(t,a){return!(!t||!a)&&e(t,".tct-tabs[data-tct-tabs]")===a}function _(t,e){if(!t)return[];for(var a=t.querySelectorAll(e),r=[],n=0;n<a.length;n++)x(a[n],t)&&r.push(a[n]);return r}function T(t,e){if(!t||!e)return null;for(var a=_(t,".tct-tab[data-tct-tab]"),r=0;r<a.length;r++)if(a[r].getAttribute("data-tct-tab")===e)return a[r];return null}function I(t,e,a,n){var i=_(t,".tct-tab[data-tct-tab]"),o=_(t,".tct-tab-panel[data-tct-panel]");if(i&&0!==i.length){for(var l=!1,d=0;d<i.length;d++)if(i[d].getAttribute("data-tct-tab")===e){l=!0;break}if(l){for(var c=null,s=0;s<i.length;s++){var u=i[s],p=u.getAttribute("data-tct-tab")===e;p&&(c=u),p?u.classList.add("tct-tab-active"):u.classList.remove("tct-tab-active"),u.setAttribute("aria-selected",p?"true":"false"),u.setAttribute("tabindex",p?"0":"-1")}for(var m=0;m<o.length;m++){var v=o[m];v.getAttribute("data-tct-panel")===e?(v.classList.add("tct-tab-panel-active"),v.removeAttribute("hidden")):(v.classList.remove("tct-tab-panel-active"),v.setAttribute("hidden","hidden"))}if(a)r(n?String(n):"tct_active_tab",e);if(c&&t.classList&&t.classList.contains("tct-domain-tabs")&&"function"==typeof c.scrollIntoView)try{c.scrollIntoView({block:"nearest",inline:"nearest"})}catch(t){}}}}function L(t){if(t){if(t.hasAttribute("data-tct-tabs-init"))return;var r=t.getAttribute("data-tct-default-tab")||"dashboard",n=t.getAttribute("data-tct-storage-key")||"tct_active_tab",i=a(n),o=r;if(!T(t,o)){var l=_(t,".tct-tab[data-tct-tab]");l&&l.length>0&&(o=l[0].getAttribute("data-tct-tab")||o)}i&&T(t,i)&&(o=i),I(t,o,!1,n),t.setAttribute("data-tct-tabs-init","1"),t.addEventListener("click",function(a){var r=e(a.target,"[data-tct-open-tab]");if(r&&x(r,t)){var i=r.getAttribute("data-tct-open-tab");if(i&&T(t,i))return a.preventDefault(),void I(t,i,!0,n)}var o=e(a.target,".tct-tab[data-tct-tab]");if(o&&x(o,t)){a.preventDefault();var l=o.getAttribute("data-tct-tab");l&&I(t,l,!0,n)}}),t.addEventListener("keydown",function(a){var r=e(a.target,".tct-tab[data-tct-tab]");if(r&&x(r,t)){if(37===a.keyCode||39===a.keyCode||36===a.keyCode||35===a.keyCode){var i=_(t,".tct-tab[data-tct-tab]");if(i&&0!==i.length){for(var o=-1,l=0;l<i.length;l++)if(i[l]===r){o=l;break}if(-1!==o){var d=o;37===a.keyCode?d=(o-1+i.length)%i.length:39===a.keyCode?d=(o+1)%i.length:36===a.keyCode?d=0:35===a.keyCode&&(d=i.length-1),a.preventDefault();var c=i[d];c&&(c.focus(),I(t,c.getAttribute("data-tct-tab"),!0,n))}}}}})}}function M(t){if(!t||"string"!=typeof t)return null;try{return JSON.parse(t)}catch(t){return null}}function P(t){if(t)for(var e=t.querySelectorAll("form[data-tct-confirm]"),a=0;a<e.length;a++)e[a].addEventListener("submit",function(t){var e=this.getAttribute("data-tct-confirm")||"Are you sure?",a=this.querySelector('input[name="action"]');if("tct_goal_delete"!==(a&&a.value?String(a.value):""))window.confirm(e)||t.preventDefault();else{var r=window.prompt(e+"\n\nThis will permanently delete the goal and all completion history from the site.\nPoints already earned (including bonuses/penalties) will remain.\n\nType DELETE to confirm:");null!==r&&"DELETE"===String(r).trim()||t.preventDefault()}})}function U(t){if(t&&!t.hasAttribute("data-tct-goal-modal-init")){t.setAttribute("data-tct-goal-modal-init","1");var a=t.querySelector("[data-tct-goal-overlay]"),r=t.querySelector("[data-tct-goal-modal]");if(a&&r){e(r,".tct-tab-panel[data-tct-panel]")&&(t.appendChild(a),t.appendChild(r));var n=r.querySelector("form[data-tct-goal-form]"),i=r.querySelector("[data-tct-goal-name]"),d=r.querySelector("[data-tct-tracking-select]"),c=r.querySelector("[data-tct-goal-id]"),s=r.querySelector("[data-tct-label-row]"),u=r.querySelector("[data-tct-label-select]"),p=r.querySelector("[data-tct-role-select]"),m=r.querySelector("[data-tct-importance-select]"),v=r.querySelector("[data-tct-effort-select]"),g=r.querySelector("[data-tct-points-preview]"),h=r.querySelector("[data-tct-importance-warning]"),f=r.querySelector("[data-tct-intervals]"),b=r.querySelector("[data-tct-intervals-json]"),y=r.querySelector("[data-tct-goal-form-mode]"),S=r.querySelector("template[data-tct-interval-template]"),w=r.querySelector("[data-tct-modal-title]"),C=r.querySelector("[data-tct-goal-modal-stats]"),N=r.querySelector("[data-tct-goal-delete]"),A=r.querySelector("[data-tct-goal-archive]"),k=r.querySelector("[data-tct-plant-name]"),E=r.querySelector("[data-tct-plant-picker]"),q=r.querySelector("[data-tct-plant-selected]"),D=r.querySelector("[data-tct-plant-selected-thumb]"),x=r.querySelector("[data-tct-plant-selected-label]"),_=r.querySelector("[data-tct-plant-popover]"),T=r.querySelector("[data-tct-plant-search]"),I=r.querySelector("[data-tct-plant-options]"),L=r.querySelector("[data-tct-plant-empty]"),P=r.querySelector("[data-tct-plant-preview-img]"),U=r.querySelector("[data-tct-plant-preview-placeholder]"),O=r.querySelector("[data-tct-plant-clear]"),j=r.querySelector("[data-tct-plant-select]"),H=r.querySelector("[data-tct-visible-after-time]"),F=r.querySelector("[data-tct-visible-after-clear]"),R=r.querySelector("[data-tct-sleep-row]"),B=r.querySelector("[data-tct-sleep-enabled]"),Y=r.querySelector("[data-tct-fail-enabled]"),W=r.querySelector("[data-tct-fail-row]"),V=r.querySelector("[data-tct-sleep-rollover-fields]"),J=r.querySelector("[data-tct-sleep-rollover-time]"),G=r.querySelector("[data-tct-goal-type-select]"),X=r.querySelector("[data-tct-goal-type-hint]"),$=(r.querySelector("[data-tct-threshold-row]"),r.querySelector("[data-tct-threshold-input]")),z=r.querySelector("[data-tct-tracking-row]"),K=r.querySelector("[data-tct-interval-row-container]"),Q=r.querySelector("[data-tct-interval-heading]"),Z=r.querySelector("[data-tct-interval-hint]"),tt=r.querySelector("[data-tct-timer-row]"),et=r.querySelector("[data-tct-timer-enabled]"),at=r.querySelector("[data-tct-timer-fields]"),rt=r.querySelector("[data-tct-timer-hours]"),nt=r.querySelector("[data-tct-timer-minutes]"),it=r.querySelector("[data-tct-timer-seconds]"),ot=r.querySelector("[data-tct-alarm-sound]"),lt=r.querySelector("[data-tct-alarm-duration]"),dt=r.querySelector("[data-tct-alarm-vibration]"),ct=r.querySelector("[data-tct-vibration-notice]");ct&&(navigator.vibrate?ct.setAttribute("hidden","hidden"):ct.removeAttribute("hidden"));var st="";if(et&&et.addEventListener("change",Ct),n&&i&&p&&m&&v&&g&&f&&b&&y&&S&&w){var ut=r.querySelector("[data-tct-add-interval]");if(ut){var pt=e(ut,"p");pt?pt.setAttribute("hidden","hidden"):ut.setAttribute("hidden","hidden")}var mt=M(C?C.getAttribute("data-tct-goal-modal-stats"):null)||{};mt.domains||(mt.domains={}),mt.roles||(mt.roles={}),mt.roleDomainMap||(mt.roleDomainMap={}),mt.thresholds||(mt.thresholds={i5Count:4,i5Pct:.3,i4Pct:.6});var vt={1:1,2:2,3:4,4:7,5:11},gt={1:1,2:1.1,3:1.25,4:1.45,5:1.7},ht=[{value:"hour",label:"hours"},{value:"day",label:"days"},{value:"week",label:"weeks"},{value:"month",label:"months"},{value:"quarter",label:"quarters"},{value:"semiannual",label:"semiannual"},{value:"year",label:"years"}],ft={open:!1,selectedName:"",previewedName:"",editingPlant:"",plants:[]};if(E){ft.plants=function(){for(var t=window.tctDashboard&&Array.isArray(window.tctDashboard.vitalityPlants)?window.tctDashboard.vitalityPlants:[],e=[],a=0;a<t.length;a++){var r=t[a];if(r)if("string"!=typeof r){if("object"==typeof r){var n=jt(r.name);if(!n)continue;e.push({name:n,previewUrl:r.previewUrl?String(r.previewUrl):""})}}else{var i=jt(r);i&&e.push({name:i,previewUrl:""})}}return e}(),q&&q.addEventListener("click",function(t){t.preventDefault(),Rt()}),T&&T.addEventListener("input",function(){Wt()}),O&&O.addEventListener("click",function(t){t.preventDefault(),T&&(T.value=""),Bt(""),Ft()}),j&&j.addEventListener("click",function(t){t.preventDefault(),t.stopPropagation(),Bt(ft.previewedName||""),T&&(T.value=""),Ft()});var bt=r.querySelector("[data-tct-plant-cancel]");bt&&bt.addEventListener("click",function(t){t.preventDefault(),t.stopPropagation(),ft.previewedName=ft.selectedName,T&&(T.value=""),Ft()}),_&&_.addEventListener("click",function(t){t.stopPropagation()}),document.addEventListener("click",function(t){ft.open&&E&&!E.contains(t.target)&&Ft()}),k&&Bt(k.value||""),F&&F.addEventListener("click",function(t){t.preventDefault(),H&&(H.value="")})}B&&B.addEventListener("change",function(){Vt()}),a.addEventListener("click",function(){Xt()}),r.addEventListener("click",function(t){if(e(t.target,"[data-tct-modal-close]"))return t.preventDefault(),void Xt();if(e(t.target,"[data-tct-modal-cancel]"))return t.preventDefault(),void Xt();if(e(t.target,"[data-tct-goal-archive]")){if(t.preventDefault(),a=c?parseInt(c.value||"0",10):0){try{Dt(a)}catch(t){}try{St(a)}catch(t){}}!function(t){var e=parseInt(t,10)||0;if(e){var a=window.tctDashboard&&tctDashboard.adminPostUrl?tctDashboard.adminPostUrl:"",r=window.tctDashboard&&tctDashboard.goalArchiveNonce?tctDashboard.goalArchiveNonce:"";if(a&&r){var i="",o=n?n.querySelector('input[name="redirect_to"]'):null;i=o&&o.value?o.value:window.location.href;var l=document.createElement("form");l.method="post",l.action=a,d("action","tct_goal_archive"),d("redirect_to",i),d("goal_id",String(e)),d("_wpnonce",r),document.body.appendChild(l),l.submit()}else window.alert("Could not archive goal (missing security nonce). Please refresh and try again.")}else window.alert("Could not archive goal: missing goal id.");function d(t,e){var a=document.createElement("input");a.type="hidden",a.name=t,a.value=e,l.appendChild(a)}}(a)}else{if(e(t.target,"[data-tct-goal-delete]")){t.preventDefault();var a,r=i&&i.value?String(i.value).trim():"",o=r?'Permanently delete "'+r+'"?\n\nThis will permanently delete the goal and all completion history from the site.\nPoints already earned (including bonuses/penalties) will remain.\n\nType DELETE to confirm:':"Permanently delete this goal?\n\nThis will permanently delete the goal and all completion history from the site.\nPoints already earned (including bonuses/penalties) will remain.\n\nType DELETE to confirm:",l=window.prompt(o);if(null===l)return;if("DELETE"!==String(l).trim())return void window.alert("Deletion cancelled. To delete, type DELETE.");if(a=c?parseInt(c.value||"0",10):0){try{Dt(a)}catch(t){}try{St(a)}catch(t){}}!function(t){var e=parseInt(t,10)||0;if(e){var a=window.tctDashboard&&tctDashboard.adminPostUrl?tctDashboard.adminPostUrl:"",r=window.tctDashboard&&tctDashboard.goalDeleteNonce?tctDashboard.goalDeleteNonce:"";if(a&&r){var i="",o=n?n.querySelector('input[name="redirect_to"]'):null;i=o&&o.value?o.value:window.location.href;var l=document.createElement("form");l.method="post",l.action=a,d("action","tct_goal_delete"),d("redirect_to",i),d("goal_id",String(e)),d("_wpnonce",r),document.body.appendChild(l),l.submit()}else window.alert("Could not delete goal (missing security nonce). Please refresh and try again.")}else window.alert("Could not delete goal: missing goal id.");function d(t,e){var a=document.createElement("input");a.type="hidden",a.name=t,a.value=e,l.appendChild(a)}}(a)}else{var d=e(t.target,"[data-tct-remove-interval]");if(d){t.preventDefault();var s=e(d,"[data-tct-interval-row]");if(s){var u=s.querySelector("[data-tct-interval-target]"),p=s.querySelector("[data-tct-interval-span]"),m=s.querySelector("[data-tct-interval-unit]"),v=s.querySelector("[data-tct-interval-mode]");u&&(u.value=""),p&&(p.value="1"),m&&qt(m,"week"),v&&(v.value="calendar",v.setAttribute("hidden","hidden"),v.setAttribute("disabled","disabled")),Tt(s)}return xt(),void It()}}}}),f.addEventListener("input",function(t){var a=e(t.target,"[data-tct-interval-target]");a&&Tt(e(a,"[data-tct-interval-row]"))}),d&&d.addEventListener("change",function(){yt()}),G&&G.addEventListener("change",function(){wt(),Lt()}),u&&u.addEventListener("change",function(){"add"===y.value&&((i.value||"").trim()||u.value&&(i.value=u.value))}),m.addEventListener("change",function(){Lt(),Ut()}),v.addEventListener("change",function(){Lt()}),p.addEventListener("change",function(){Ut()}),document.addEventListener("keydown",function(t){if(27===t.keyCode&&!r.hasAttribute("hidden")){if(ft&&ft.open)return t.preventDefault(),void Ft();Xt()}}),t.addEventListener("click",function(t){var a=e(t.target,"[data-tct-open-goal-modal]");if(a&&(t.preventDefault(),!a.hasAttribute("disabled"))){var r=a.getAttribute("data-tct-open-goal-modal")||"add",n=null;if("edit"===r)n=M(a.getAttribute("data-tct-goal"));Gt(r,n)}}),n.addEventListener("submit",function(t){xt();var e=f.querySelectorAll("[data-tct-interval-row]"),a=[],r=e&&e.length?e[0]:null;if(r){var n=r.querySelector("[data-tct-interval-target]"),i=r.querySelector("[data-tct-interval-span]"),l=r.querySelector("[data-tct-interval-unit]");r.querySelector("[data-tct-interval-mode]");if(n&&l){var d=parseInt(n.value,10),c=1;i&&(c=parseInt(i.value,10)),(!isFinite(c)||c<1)&&(c=1),!isNaN(d)&&d>0&&a.push({target:d,period_span:c,period_unit:Et(l.value),period_mode:"calendar"})}}var s=G?G.value:"positive";if("harm_reduction"===s&&$&&r){var u=r.querySelector("[data-tct-interval-target]");u&&($.value=u.value||"1")}else"never"===s&&$&&($.value="");if(et&&et.checked&&3600*(rt?parseInt(rt.value,10):0)+60*(nt?parseInt(nt.value,10):0)+(it?parseInt(it.value,10):0)>0){var p=ot?ot.value:"",m=lt?lt.value:"";if(!p)return t.preventDefault(),void window.alert("Please select an alarm sound for the countdown timer.");if(!m)return t.preventDefault(),void window.alert("Please select an alarm duration for the countdown timer.")}if(B&&B.checked){var v=J?String(J.value||"").trim():"";v||(v="18:00",J&&(J.value=v));if(!/^([01]\d|2[0-3]):([0-5]\d)$/.test(v))return t.preventDefault(),void window.alert("Please enter a valid sleep rollover time in HH:MM (00:00-23:59).")}var g="never"===s||"harm_reduction"===s,h=o(s);if(h&&(a=[]),!a.length&&!g&&!h)return t.preventDefault(),void window.alert("Please add at least one interval with a target greater than 0.");if("never"===s&&!a.length&&r){i=r.querySelector("[data-tct-interval-span]");if(l=r.querySelector("[data-tct-interval-unit]")){c=i?parseInt(i.value,10):1;(!isFinite(c)||c<1)&&(c=1),a.push({target:0,period_span:c,period_unit:Et(l.value),period_mode:"calendar"})}}b.value=JSON.stringify(a)})}}}function yt(){if(d&&s&&u){if("manual"===(d.value||"todoist"))return s.setAttribute("hidden","hidden"),u.required=!1,u.value="",void u.removeAttribute("disabled");s.removeAttribute("hidden"),u.required=!0,"edit"===y.value&&st?(u.value=st,u.setAttribute("disabled","disabled")):u.removeAttribute("disabled")}}function wt(){
if(!G){return}
var t=G.value||"positive";
var e=o(t);
var a="never"===t||"harm_reduction"===t;
var r="anki_cards"===t;
if(X){
if("positive"===t){X.textContent="Positive goal with interval: earn points for each completion, bonus if you hit your target."}
else if(e){X.textContent="Positive goal without interval: earn points for each completion. No interval targets, and no bonus/penalty loop."}
else if(r){X.textContent="Anki cards goal: log the total number of cards you studied today. You get normal completion points immediately, then a later daily adjustment that scales with how far under or over target you were."}
else if("never"===t){X.textContent="Never: each tap incurs an escalating penalty. Bonus for keeping the goal (0 taps)."}
else if("harm_reduction"===t){X.textContent="Harm Reduction: taps within your allowed limit cost 0. Exceeding the limit incurs escalating penalties."}
}
if(z){
if(a||r){z.setAttribute("hidden","hidden");d&&(d.value="manual")}
else{z.removeAttribute("hidden")}
}
if(K){
if(e){K.setAttribute("hidden","hidden")}
else{K.removeAttribute("hidden")}
}
if(Q){Q.textContent=r?"Daily card target":a?"Tracking Period":"Interval"}
if(Z){
if("positive"===t){Z.textContent="Bonus/penalty is calculated automatically per interval and applied at the end of each interval."}
else if(r){Z.textContent="Base completion points are awarded immediately. At the end of the day, a proportional adjustment is added or subtracted based on cards studied versus target."}
else if("never"===t){Z.textContent="Bonus for keeping this goal (0 taps) is applied at end of period. Each tap incurs escalating penalties."}
else if("harm_reduction"===t){Z.textContent="Taps within your allowed limit cost 0. Exceeding triggers escalating penalties. Bonus for staying within limit."}
}
for(var n=f?f.querySelectorAll("[data-tct-interval-row]"):[],i=0;i<n.length;i++){
var l=n[i],c=l.querySelector("[data-tct-interval-target-label]"),s=l.querySelector("[data-tct-interval-target]"),u=l.querySelector("[data-tct-interval-target-field]"),p=l.querySelector("[data-tct-interval-bonus-label]"),m=l.querySelector("[data-tct-interval-span]"),v=l.querySelector("[data-tct-interval-unit]");
if(c){c.textContent=r?"Cards":"harm_reduction"===t?"Allowed":"never"===t?"Target":"Completions"}
if(u){
if("never"===t){u.setAttribute("hidden","hidden"),s&&(s.value="0")}
else{u.removeAttribute("hidden")}
}
if(p){p.textContent=a?"Bonus (if kept)":r?"Daily adjustment":"Bonus/penalty"}
if("harm_reduction"===t&&s&&$){
var g=$.value||"1";
g&&""!==String(g).trim()&&"0"!==String(g)&&(s.value=g)
}
if(r){
m&&(m.value="1",m.setAttribute("hidden","hidden"),m.setAttribute("disabled","disabled"));
v&&(v.value="day",v.setAttribute("hidden","hidden"),v.setAttribute("disabled","disabled"))
}else{
m&&(m.removeAttribute("hidden"),m.removeAttribute("disabled"));
v&&(v.removeAttribute("hidden"),v.removeAttribute("disabled"))
}
}
if(tt){
if("positive"===t||e){tt.removeAttribute("hidden")}
else{tt.setAttribute("hidden","hidden"),et&&(et.checked=!1),at&&at.setAttribute("hidden","hidden")}
}
if(W){
if("positive"===t){W.removeAttribute("hidden")}
else{W.setAttribute("hidden","hidden"),Y&&(Y.checked=!1)}
}
yt()
}function Ct(){et&&at&&(et.checked?at.removeAttribute("hidden"):at.setAttribute("hidden","hidden"))}function Nt(){et&&(et.checked=!1),rt&&(rt.value="0"),nt&&(nt.value="0"),it&&(it.value="0"),ot&&(ot.value=""),lt&&(lt.value=""),dt&&(dt.checked=!1),Ct()}function At(){return function(t,e){var a=parseInt(t,10),r=parseInt(e,10);if(isNaN(a)||isNaN(r)||a<1||a>5||r<1||r>5)return 0;var n=vt[a]||0,i=gt[r]||0;return n&&i?Math.round(n*i):0}(m.value,v.value)}function kt(t){var e=String(null!=t&&""!==String(t)?t:"0");p.value=e,p.value!==e&&(p.value="0")}function Et(t){var e=(t||"").toString().trim().toLowerCase();return e?"hourly"===e?"hour":"daily"===e?"day":"weekly"===e?"week":"monthly"===e?"month":"quarterly"===e?"quarter":"yearly"===e||"annually"===e||"annual"===e?"year":"semiannual"===e||"semi-annual"===e||"halfyear"===e||"half-year"===e||"halfyears"===e||"half-years"===e?"semiannual":"hours"===e?"hour":"days"===e?"day":"weeks"===e?"week":"months"===e?"month":"quarters"===e?"quarter":"years"===e?"year":"hour"===e||"day"===e||"week"===e||"month"===e||"quarter"===e||"semiannual"===e||"year"===e?e:"week":"week"}function qt(t,e){if(t){for(;t.firstChild;)t.removeChild(t.firstChild);for(var a=0;a<ht.length;a++){var r=ht[a],n=document.createElement("option");n.value=r.value,n.textContent=r.label,t.appendChild(n)}var i=Et(e||t.value);t.value=i,t.value!==i&&(t.value="week")}}function xt(){var t=f.querySelectorAll("[data-tct-interval-row]");if(t&&0!==t.length)for(var e=t.length-1;e>=1;e--)t[e]&&t[e].parentNode&&t[e].parentNode.removeChild(t[e]);else Ot({target:"",period_unit:"week",period_mode:"calendar"})}function _t(t){!function(){for(;f.firstChild;)f.removeChild(f.firstChild)}();var e=null;t&&Array.isArray(t)&&t.length&&(e=t[0]),e&&"object"==typeof e||(e={target:"",period_span:1,period_unit:"week",period_mode:"calendar"}),void 0!==e.period_span&&null!==e.period_span&&""!==e.period_span||(e.period_span=1),Ot(e),xt(),It(),wt()}function Tt(t){if(t){var e=t.querySelector("[data-tct-interval-target]"),a=t.querySelector("[data-tct-interval-bonus-preview]");if(e&&a){var r=parseInt(e.value,10);if(isNaN(r)||r<=0)a.textContent="+0 / 0";else{var n,i,o,l=At(),d=(n=r,i=parseInt(l,10),o=parseInt(n,10),isNaN(i)||isNaN(o)||i<=0||o<=0?0:Math.round(i*o*.5)),c=function(t,e){var a=parseInt(t,10),r=parseInt(e,10);return isNaN(a)||isNaN(r)||a<=0||r<=0?0:-Math.round(a*r*1)}(l,r);a.textContent="+"+String(d)+" / "+String(c)}}}}function It(){for(var t=f.querySelectorAll("[data-tct-interval-row]"),e=0;e<t.length;e++)Tt(t[e])}function Lt(){var t=At();g.textContent=String(t),It()}function Mt(t,e){var a=parseInt(t,10),r=parseInt(e,10);return isNaN(a)||isNaN(r)||r<=0?0:Math.round(a/r*100)}function Pt(t){var e=0;return mt.roleDomainMap&&void 0!==mt.roleDomainMap[String(t)]&&(e=parseInt(mt.roleDomainMap[String(t)],10)||0),e}function Ut(){if(h){var t=parseInt(p.value,10);if(isNaN(t)||t<=0)return h.textContent="",void l(h,!0);var e=parseInt(m.value,10);isNaN(e)&&(e=0);var a=Pt(t),r=function(t){var e=String(t),a=mt.roles&&mt.roles[e]?mt.roles[e]:null;return a?{total:parseInt(a.total,10)||0,i5:parseInt(a.i5,10)||0,i4plus:parseInt(a.i4plus,10)||0,name:a.name||""}:{total:0,i5:0,i4plus:0,name:""}}(t),n=function(t){var e=String(t),a=mt.domains&&mt.domains[e]?mt.domains[e]:null;return a?{total:parseInt(a.total,10)||0,i5:parseInt(a.i5,10)||0,i4plus:parseInt(a.i4plus,10)||0,name:a.name||""}:{total:0,i5:0,i4plus:0,name:""}}(a),i=r.total,o=r.i5,d=r.i4plus,c=n.total,s=n.i5,u=n.i4plus;if(i+=1,a>0&&(c+=1),5===e&&(o+=1,a>0&&(s+=1)),e>=4&&(d+=1,a>0&&(u+=1)),e<4)return h.textContent="",void l(h,!0);var v=mt.thresholds||{},g=void 0!==v.i5Count?parseInt(v.i5Count,10):4,f=void 0!==v.i5Pct?parseFloat(v.i5Pct):.3,b=void 0!==v.i4Pct?parseFloat(v.i4Pct):.6;(isNaN(g)||g<=0)&&(g=4),(isNaN(f)||f<=0)&&(f=.3),(isNaN(b)||b<=0)&&(b=.6);var y=!1;if((o>=g||(i>0?o/i:0)>=f||(i>0?d/i:0)>=b)&&(y=!0),a>0&&(s>=g||(c>0?s/c:0)>=f||(c>0?u/c:0)>=b)&&(y=!0),!y)return h.textContent="",void l(h,!0);var S="";S+="<strong>Heads up:</strong> You already have a lot of high-importance goals in this area.",S+='<div style="margin-top:6px;">',S+="Role -"+(r.name?r.name:String(t))+'": ',S+="<strong>"+String(o)+"</strong>/"+String(i)+" at importance 5 ("+String(Mt(o,i))+"%), ",S+="<strong>"+String(d)+"</strong>/"+String(i)+" at importance >=4 ("+String(Mt(d,i))+"%).",S+="</div>",a>0&&(S+='<div style="margin-top:4px;">',S+="Domain -"+(n.name?n.name:String(a))+'": ',S+="<strong>"+String(s)+"</strong>/"+String(c)+" at importance 5 ("+String(Mt(s,c))+"%), ",S+="<strong>"+String(u)+"</strong>/"+String(c)+" at importance >=4 ("+String(Mt(u,c))+"%).",S+="</div>"),S+='<div class="tct-muted" style="margin-top:6px;">This is only a warning--use high importance for true non-negotiables.</div>',h.innerHTML=S,l(h,!1)}}function Ot(t){var a;if(S.content)a=document.importNode(S.content,!0);else{var r=document.createElement("div");for(r.innerHTML=S.innerHTML,a=document.createDocumentFragment();r.firstChild;)a.appendChild(r.firstChild)}f.appendChild(a);var n=f.querySelectorAll("[data-tct-interval-row]"),i=n[n.length-1];if(i){var o=i.querySelector("[data-tct-remove-interval]");if(o){var l=e(o,".tct-interval-remove-wrap");l?l.setAttribute("hidden","hidden"):o.setAttribute("hidden","hidden"),o.setAttribute("aria-hidden","true"),o.tabIndex=-1}var d=i.querySelector("[data-tct-interval-target]"),c=i.querySelector("[data-tct-interval-span]"),s=i.querySelector("[data-tct-interval-unit]"),u=i.querySelector("[data-tct-interval-mode]");if(qt(s,t&&t.period_unit?t.period_unit:""),d&&t&&void 0!==t.target&&(d.value=t.target),c&&t&&void 0!==t.period_span){var p=parseInt(t.period_span,10);(!isFinite(p)||p<1)&&(p=1),c.value=String(p)}!d||t&&void 0!==t.target&&null!==t.target&&""!==t.target||(d.value=""),!c||t&&void 0!==t.period_span&&null!==t.period_span&&""!==t.period_span||(c.value="1"),!s||t&&t.period_unit||qt(s,"week"),u&&(u.value="calendar",u.setAttribute("hidden","hidden"),u.setAttribute("disabled","disabled")),Tt(i)}}function jt(t){return(t||"").toString().trim()}function Ht(t){var e=jt(t);if(!e)return"";for(var a=0;a<ft.plants.length;a++){var r=ft.plants[a];if(r&&r.name===e)return r.previewUrl?String(r.previewUrl):""}return""}function Ft(){_&&q?(l(_,!0),q.setAttribute("aria-expanded","false"),ft.open=!1):ft.open=!1}function Rt(){ft.open?Ft():function(){if(_&&q){ft.previewedName=ft.selectedName,l(_,!1),q.setAttribute("aria-expanded","true"),ft.open=!0;var t=ft.previewedName?Ht(ft.previewedName):"";if(P&&(t?(P.src=t,l(P,!1)):(P.removeAttribute("src"),l(P,!0))),U&&l(U,!!t),Wt(),I){var e=I.querySelector(".tct-plant-option-previewed")||I.querySelector(".tct-plant-option-selected");if(e&&e.scrollIntoView)try{e.scrollIntoView({block:"nearest"})}catch(t){try{e.scrollIntoView(!1)}catch(t){}}}if(T)try{T.focus(),T.select()}catch(t){}}}()}function Bt(t){var e=jt(t);ft.selectedName=e,ft.previewedName=e,k&&(k.value=e);var a=e?Ht(e):"";x&&(x.textContent=e||"-- No plant selected --"),D&&(a?(D.src=a,l(D,!1)):(D.removeAttribute("src"),l(D,!0))),P&&(a?(P.src=a,l(P,!1)):(P.removeAttribute("src"),l(P,!0))),U&&l(U,!!a),Wt()}function Yt(t){var e=jt(t);ft.previewedName=e;var a=e?Ht(e):"";P&&(a?(P.src=a,l(P,!1)):(P.removeAttribute("src"),l(P,!0))),U&&l(U,!!a),Wt()}function Wt(){if(I){for(var t=T?jt(T.value).toLowerCase():"",e=function(){for(var t=window.tctDashboard&&Array.isArray(window.tctDashboard.takenPlants)?window.tctDashboard.takenPlants:[],e={},a=0;a<t.length;a++){var r=jt(t[a]);r&&(e[r]=!0)}return e}(),a=jt(ft.editingPlant);I.firstChild;)I.removeChild(I.firstChild);for(var r=ft.plants||[],n=0,i=0;i<r.length;i++){var o=r[i];if(o&&o.name){var d=o.name.toLowerCase();if(!(t&&-1===d.indexOf(t)||e[o.name]&&o.name!==a)){n++;var c=document.createElement("div");c.className="tct-plant-option",c.setAttribute("role","option"),c.setAttribute("data-tct-plant-option",o.name),o.name===ft.previewedName&&(c.className+=" tct-plant-option-previewed"),o.name===ft.selectedName&&(c.className+=" tct-plant-option-selected"),c.setAttribute("aria-selected",o.name===ft.previewedName?"true":"false");var s=document.createElement("img");s.className="tct-plant-thumb",s.alt="",o.previewUrl?s.src=o.previewUrl:l(s,!0);var u=document.createElement("span");u.className="tct-plant-option-name",u.textContent=o.name,c.appendChild(s),c.appendChild(u),c.addEventListener("click",function(t){t.stopPropagation(),Yt(this.getAttribute("data-tct-plant-option")||"")}),I.appendChild(c)}}}L&&l(L,n>0)}}function Vt(){B&&V&&(B.checked?(l(V,!1),J&&(J.removeAttribute("disabled"),J.value||(J.value="18:00"))):(l(V,!0),J&&J.setAttribute("disabled","disabled")))}function Jt(t,e){if(R){var a,r=(a=0,window.tctDashboard&&void 0!==tctDashboard.sleepEnabledGoalId&&null!==tctDashboard.sleepEnabledGoalId&&(a=parseInt(tctDashboard.sleepEnabledGoalId,10)),(isNaN(a)||a<0)&&(a=0),a),n=!1;if(!e||1!==parseInt(e.sleep_tracking_enabled,10)&&1!==e.sleep_tracking_enabled&&"1"!==e.sleep_tracking_enabled||(n=!0),r>0&&!(r>0&&t===r||n))return l(R,!0),B&&(B.checked=!1,B.setAttribute("disabled","disabled")),V&&l(V,!0),void(J&&J.setAttribute("disabled","disabled"));l(R,!1),B&&B.removeAttribute("disabled"),Vt()}}function Gt(t,e){if(y.value=t,Ft(),T&&(T.value=""),"edit"===t&&e){if(w.textContent="Edit goal",l(A,!1),l(N,!1),c&&(c.value=void 0!==e.goal_id&&""!==String(e.goal_id)?String(e.goal_id):"0"),G&&(G.value=e.goal_type&&String(e.goal_type).trim()?String(e.goal_type).trim():"positive"),$){var n=void 0!==e.threshold&&null!==e.threshold&&""!==e.threshold?String(e.threshold):"1";$.value=n}var o=e.tracking_mode&&String(e.tracking_mode).trim()?String(e.tracking_mode).trim():"manual";d&&(d.value=o),st=e.label_name&&String(e.label_name).trim()?String(e.label_name).trim():"",u&&(u.value=st),i.value=e.goal_name&&String(e.goal_name).trim()?e.goal_name:"",kt(void 0!==e.role_id&&null!==e.role_id&&""!==String(e.role_id)?String(e.role_id):"0"),m.value=void 0!==e.importance&&null!==e.importance?String(e.importance):"0",v.value=void 0!==e.effort&&null!==e.effort?String(e.effort):"0",Lt(),_t(e.intervals&&Array.isArray(e.intervals)?e.intervals:[]),Bt(void 0!==e.plant_name&&null!==e.plant_name?e.plant_name:""),ft.editingPlant=void 0!==e.plant_name&&null!==e.plant_name?jt(e.plant_name):"",function(t){if((t=parseInt(t,10)||0)>0){var e=Math.floor(t/3600),a=Math.floor(t%3600/60),r=t%60;et&&(et.checked=!0),rt&&(rt.value=String(e)),nt&&(nt.value=String(a)),it&&(it.value=String(r))}else Nt();Ct()}(void 0!==e.timer_duration_seconds?e.timer_duration_seconds:0),s=void 0!==e.alarm_sound?e.alarm_sound:"",p=void 0!==e.alarm_duration?e.alarm_duration:0,g=void 0!==e.alarm_vibration?e.alarm_vibration:0,ot&&(ot.value=s||""),lt&&(lt.value=p>0?String(p):""),dt&&(dt.checked=1===g||"1"===g),H&&(H.value=void 0!==e.visible_after_time&&e.visible_after_time?String(e.visible_after_time):""),B&&(B.checked=1===parseInt(e.sleep_tracking_enabled,10)||1===e.sleep_tracking_enabled||"1"===e.sleep_tracking_enabled),J&&(J.value=void 0!==e.sleep_rollover_time&&e.sleep_rollover_time?String(e.sleep_rollover_time):"18:00"),Y&&(Y.checked=1===parseInt(e.fail_button_enabled,10)||1===e.fail_button_enabled||"1"===e.fail_button_enabled),document.querySelector("[data-tct-favorite-enabled]")&&(document.querySelector("[data-tct-favorite-enabled]").checked=1===parseInt(e.is_favorite,10)||1===e.is_favorite||"1"===e.is_favorite),!function(){var t=e.link_url?String(e.link_url).trim():"",a="",n="",o=t;if(0===t.toLowerCase().indexOf("tel:"))a=t.replace(/^tel:/i,"").replace(/\D+/g,""),o="";else if(0===t.toLowerCase().indexOf("sms:"))n=t.replace(/^sms:/i,"").replace(/\D+/g,""),o="";r.querySelector("[data-tct-goal-link-url]")&&(r.querySelector("[data-tct-goal-link-url]").value=o),r.querySelector("[data-tct-goal-phone-number]")&&(r.querySelector("[data-tct-goal-phone-number]").value=a),r.querySelector("[data-tct-goal-sms-number]")&&(r.querySelector("[data-tct-goal-sms-number]").value=n)}(),r.querySelector("[data-tct-goal-notes]")&&(r.querySelector("[data-tct-goal-notes]").value=e.goal_notes?String(e.goal_notes):"")}else w.textContent="Add goal",l(A,!0),l(N,!0),st="",c&&(c.value="0"),G&&(G.value="positive"),$&&($.value="1"),d&&(d.value="manual"),u&&(u.value="",u.removeAttribute("disabled")),i.value="",kt("0"),m.value="0",v.value="0",Lt(),_t([]),Bt(""),ft.editingPlant="",Nt(),H&&(H.value=""),B&&(B.checked=!1),J&&(J.value="18:00"),Y&&(Y.checked=!1),document.querySelector("[data-tct-favorite-enabled]")&&(document.querySelector("[data-tct-favorite-enabled]").checked=!1),r.querySelector("[data-tct-goal-link-url]")&&(r.querySelector("[data-tct-goal-link-url]").value=""),r.querySelector("[data-tct-goal-phone-number]")&&(r.querySelector("[data-tct-goal-phone-number]").value=""),r.querySelector("[data-tct-goal-sms-number]")&&(r.querySelector("[data-tct-goal-sms-number]").value=""),r.querySelector("[data-tct-goal-notes]")&&(r.querySelector("[data-tct-goal-notes]").value="");var s,p,g,h=c?parseInt(c.value||"0",10):0;isNaN(h)&&(h=0),Jt(h,e),wt(),yt(),a.removeAttribute("hidden"),r.removeAttribute("hidden"),setTimeout(function(){i&&i.focus()},50)}function Xt(){Ft(),l(r,!0),l(a,!0)}}function O(t){if(t){var a=t.querySelector("[data-tct-domain-overlay]"),r=t.querySelector("[data-tct-domain-modal]");if(a&&r){var n=r.querySelector("form[data-tct-domain-form]"),i=r.querySelector("[data-tct-domain-id]"),o=r.querySelector("[data-tct-domain-name]"),d=r.querySelector("[data-tct-domain-color]"),c=r.querySelector("[data-tct-domain-modal-title]"),s=t.querySelectorAll("[data-tct-open-domain-modal]");if(n&&i&&o&&d&&c){a.addEventListener("click",function(){m()}),r.addEventListener("click",function(t){return e(t.target,"[data-tct-domain-modal-close]")||e(t.target,"[data-tct-domain-modal-cancel]")?(t.preventDefault(),void m()):void 0}),document.addEventListener("keydown",function(t){if(27===t.keyCode&&!r.hasAttribute("hidden")){if(plantPickerState&&plantPickerState.open)return t.preventDefault(),void closePlantPopover();m()}});for(var u=0;u<s.length;u++)(function(){var t=s[u];t.addEventListener("click",function(e){e.preventDefault();var a=t.getAttribute("data-tct-open-domain-modal")||"add",r=null;"edit"===a&&(r=M(t.getAttribute("data-tct-domain")));p(a,r,t.getAttribute("data-tct-domain-default-color")||"")})})();n.addEventListener("submit",function(t){if(!o.value||!String(o.value).trim())return t.preventDefault(),void window.alert("Please enter a domain name.")})}}}function p(t,e,n){var s=n||"#2271b1";"edit"===t&&e&&e.domain_id?(c.textContent="Edit domain",i.value=String(e.domain_id),o.value=e.domain_name?e.domain_name:"",d.value=e.color_hex?e.color_hex:s):(c.textContent="Add domain",i.value="0",o.value="",d.value=s),l(a,!1),l(r,!1);try{o.focus(),o.select()}catch(t){}}function m(){closePlantPopover(),l(r,!0),l(a,!0)}}function j(t){if(t){var a=t.querySelector("[data-tct-role-overlay]"),r=t.querySelector("[data-tct-role-modal]");if(a&&r){var n=r.querySelector("form[data-tct-role-form]"),i=r.querySelector("[data-tct-role-id]"),o=r.querySelector("[data-tct-role-domain-select]"),d=r.querySelector("[data-tct-role-name]"),c=r.querySelector("[data-tct-role-modal-title]"),s=t.querySelectorAll("[data-tct-open-role-modal]");if(n&&i&&o&&d&&c){a.addEventListener("click",function(){m()}),r.addEventListener("click",function(t){return e(t.target,"[data-tct-role-modal-close]")||e(t.target,"[data-tct-role-modal-cancel]")?(t.preventDefault(),void m()):void 0}),document.addEventListener("keydown",function(t){if(27===t.keyCode&&!r.hasAttribute("hidden")){if(plantPickerState&&plantPickerState.open)return t.preventDefault(),void closePlantPopover();m()}});for(var u=0;u<s.length;u++)(function(){var t=s[u];t.addEventListener("click",function(e){if(e.preventDefault(),!t.hasAttribute("disabled")){var a=t.getAttribute("data-tct-open-role-modal")||"add",r=null;if("edit"===a)r=M(t.getAttribute("data-tct-role"));p(a,r)}})})();n.addEventListener("submit",function(t){return o.value&&""!==String(o.value).trim()&&"0"!==String(o.value)?d.value&&String(d.value).trim()?void 0:(t.preventDefault(),void window.alert("Please enter a role name.")):(t.preventDefault(),void window.alert("Please select a domain for this role."))})}}}function p(t,e){var n=0;e&&(e.role_id||e.id)&&(n=parseInt(e.role_id||e.id,10),isNaN(n)&&(n=0)),"edit"===t&&n>0?(c.textContent="Edit role",i.value=String(n),d.value=e&&e.role_name?e.role_name:"",o.value=e&&void 0!==e.domain_id&&null!==e.domain_id&&""!==String(e.domain_id)?String(e.domain_id):""):(c.textContent="Add role",i.value="0",d.value="",o.value=""),l(a,!1),l(r,!1);try{d.focus(),d.select()}catch(t){}}function m(){closePlantPopover(),l(r,!0),l(a,!0)}}function H(t){if(t){var e=t.querySelector("table.tct-goals-table[data-tct-goals-table]");if(e){var n=e.querySelector("tbody");if(n){var i=e.querySelectorAll("thead th[data-tct-goals-sort]");if(i&&0!==i.length){for(var o=a("tct_goals_sort_key")||"domain",l=a("tct_goals_sort_dir")||"asc",d=0;d<i.length;d++)(function(){var t=i[d];t.addEventListener("click",function(e){e.preventDefault();var a=t.getAttribute("data-tct-goals-sort")||"";if(a){var r="asc";a===o&&(r="asc"===l?"desc":"asc"),v(a,r,!0)}})})();v(o,l,!1)}}}}function c(t){return null==t?"":String(t).trim()}function s(t){return c(t).toLowerCase()}function u(t){var e=s(t);return""===e||"unassigned"===e}function p(t,e){var a=s(t),r=s(e),n=u(a),i=u(r);return n&&!i?1:!n&&i||a<r?-1:a>r?1:0}function m(t,e){return t&&t.getAttribute?c(t.getAttribute(e)):""}function v(t,e,a){var d=Array.prototype.slice.call(n.querySelectorAll("tr"));d.sort(function(a,r){var n=function(t,e,a){var r=m(t,"data-goal-name"),n=m(e,"data-goal-name"),i=m(t,"data-domain-name"),o=m(e,"data-domain-name"),l=m(t,"data-role-name"),d=m(e,"data-role-name"),c=0;return"role"===a?(0===(c=p(l,d))&&(c=p(i,o)),0===c&&(c=p(r,n))):(0===(c=p(i,o))&&(c=p(l,d)),0===c&&(c=p(r,n))),c}(a,r,t);return"desc"===e&&(n=-n),n});for(var c=0;c<d.length;c++)n.appendChild(d[c]);(function(t,e){for(var a=0;a<i.length;a++){var r=i[a],n=r.getAttribute("data-tct-goals-sort")||"";r.classList.remove("tct-sort-active-asc"),r.classList.remove("tct-sort-active-desc"),r.setAttribute("aria-sort","none"),n===t&&("desc"===e?(r.classList.add("tct-sort-active-desc"),r.setAttribute("aria-sort","descending")):(r.classList.add("tct-sort-active-asc"),r.setAttribute("aria-sort","ascending")))}})(o=t,l=e),a&&(r("tct_goals_sort_key",o),r("tct_goals_sort_dir",l))}}function F(t){if(t&&window.jQuery&&window.jQuery.fn&&"function"==typeof window.jQuery.fn.sortable&&window.tctDashboard&&tctDashboard.ajaxUrl&&tctDashboard.roleOrderNonce){var e=window.jQuery,a=e(t).find("tbody[data-tct-role-sortable]");a.length&&a.each(function(){var t=e(this);t.data("tct-role-sort-init")||(t.data("tct-role-sort-init",!0),t.sortable({axis:"y",handle:".tct-drag-handle",items:"tr.tct-role-row",placeholder:"tct-sort-placeholder",helper:function(t,a){return a.children().each(function(){e(this).width(e(this).width())}),a},update:function(){var a=[];t.find("tr.tct-role-row[data-role-id]").each(function(){var t=e(this).attr("data-role-id"),r=parseInt(t,10);!isNaN(r)&&r>0&&a.push(r)});var r=parseInt(t.data("domain-id")||"0",10);isNaN(r)&&(r=0),function(t,e){var a=new FormData;return a.append("action","tct_save_role_order"),a.append("nonce",tctDashboard.roleOrderNonce),a.append("domain_id",String(t)),a.append("order",JSON.stringify(e)),window.fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:a}).then(function(t){return t.json()}).then(function(t){if(!t||!t.success){var e="Could not save role order.";throw t&&t.data&&t.data.message&&(e=String(t.data.message)),new Error(e)}return t})}(r,a).catch(function(t){window.console&&console.error&&console.error(t),window.alert("Could not save role order. Please refresh and try again.")})}}))})}}function R(t){if(t){var e=t.querySelector(".tct-goal-points-pill");if(e){var a=e.querySelector(".tct-goal-points-pill-completion"),r=e.querySelector(".tct-goal-points-pill-bonus"),n=e.querySelector(".tct-goal-points-pill-penalty");if(a&&r&&n){var i=parseInt(a.textContent||"0",10);(isNaN(i)||i<0)&&(i=0);var o=t.querySelector("[data-tct-goal-target]"),l=t.querySelector("[data-tct-goal-count]"),d=o?parseInt(o.textContent||"0",10):0,c=l?parseInt(l.textContent||"0",10):0;(isNaN(d)||d<0)&&(d=0),(isNaN(c)||c<0)&&(c=0);var s=function(t,e){if(t=parseInt(t,10)||0,e=parseInt(e,10)||0,t<=0||e<=0)return 0;var a=t*e,r=Math.round(.5*a);return(isNaN(r)||r<0)&&(r=0),r}(i,d),u=function(t,e,a){if(t=parseInt(t,10)||0,e=parseInt(e,10)||0,a=parseInt(a,10)||0,t<=0||e<=0)return 0;if(a>=e)return 0;var r=a/e;r<0&&(r=0),r>1&&(r=1);var n=1-r,i=t*e*1*Math.pow(n,1.5),o=-Math.round(i);return isNaN(o)&&(o=0),o>0&&(o=-Math.abs(o)),o}(i,d,c);r.textContent="+"+String(s),n.textContent=String(u),e.title="Completion points: +"+String(i)+"\nBonus points (if met): +"+String(s)+"\nPenalty points (if missed): "+String(u)}}}}function B(t){if(t){var e=t.querySelector("[data-tct-goal-completed-label]");if(e){var a=t.querySelector("[data-tct-goal-count]"),r=a?parseInt(a.textContent||"0",10):0;(isNaN(r)||r<0)&&(r=0);var n=t.getAttribute("data-goal-type")||"positive",i=t.getAttribute("data-threshold"),l=null!==i&&""!==i?parseInt(i,10):null;if("never"===n||"harm_reduction"===n)e.textContent=function(t,e,a){if(a=parseInt(a,10),(isNaN(a)||a<0)&&(a=0),"never"===t||"harm_reduction"===t&&(null===e||""===e||parseInt(e,10)<=0))return a<=0?"Clean this period":String(a)+" "+(1===a?"violation":"violations");var r=null!==e&&""!==e?parseInt(e,10):0;return(isNaN(r)||r<0)&&(r=0),String(a)+" of "+String(r)+" allowed"}(n,l,r);else if(o(n))e.textContent=function(t){return t=parseInt(t,10),(isNaN(t)||t<0)&&(t=0),t<=0?"None completed":String(t)+" completed"}(r);else{var d=t.querySelector("[data-tct-goal-target]"),c=d?parseInt(d.textContent||"0",10):0;(isNaN(c)||c<0)&&(c=0),e.textContent=function(t,e){return t=parseInt(t,10),e=parseInt(e,10),(isNaN(t)||t<0)&&(t=0),(isNaN(e)||e<0)&&(e=0),t<=0?"None completed":String(t)+" of "+String(e)+" completed"}(r,c)}}}}function Y(t){if(t){var e=t.querySelector(".tct-goal-countdown-line");if(e){var a=t.getAttribute("data-goal-type")||"positive";if("never"!==a&&"harm_reduction"!==a)if(o(a))l(e,!0);else{var r=!1;try{r=!(!t.closest||!t.closest('.tct-tab-panel[data-tct-panel="complete"]'))}catch(t){r=!1}if(r)l(e,!1);else{var n=t.querySelector("[data-tct-goal-count]"),i=n?parseInt(n.textContent||"0",10):0;(isNaN(i)||i<0)&&(i=0);var d=t.querySelector("[data-tct-goal-target]"),c=d?parseInt(d.textContent||"0",10):0;(isNaN(c)||c<0)&&(c=0),l(e,c>0&&i>=c)}}else l(e,!1)}}}function W(t){if(t){var e=t.getAttribute("data-goal-type")||"positive";if("never"===e||"harm_reduction"===e){var a=t.querySelector("[data-tct-complete-goal]");if(a){var r=t.getAttribute("data-threshold"),n=null!==r&&""!==r?parseInt(r,10):null,i=t.querySelector("[data-tct-goal-count]"),o=i?parseInt(i.textContent||"0",10):0;a.textContent=function(t,e,a){if(a=parseInt(a,10),(isNaN(a)||a<0)&&(a=0),"never"===t||"harm_reduction"===t&&(null===e||""===e||parseInt(e,10)<=0))return"Slip";var r=null!==e&&""!==e?parseInt(e,10):0;return(isNaN(r)||r<0)&&(r=0),a<r?"Log":"Slip"}(e,n,o)}}}}function V(t){if(t){var e=t.querySelector("[data-tct-goal-last-prefix]");if(e){var a=t.getAttribute("data-goal-type")||"positive";if("never"===a||"harm_reduction"===a){var r=t.getAttribute("data-threshold"),n=null!==r&&""!==r?parseInt(r,10):null;if("never"===a||"harm_reduction"===a&&(null===n||0===n))e.textContent="Fell short";else{var i=t.querySelector("[data-tct-goal-count]"),o=i?parseInt(i.textContent||"0",10):0;e.textContent=o>n?"Fell short":"Enjoyed"}}else e.textContent="Completed"}}}function J(t,e){if(t&&e){var a=void 0!==e.statusKey?String(e.statusKey):"",r=(void 0!==e.statusLabel&&String(e.statusLabel),void 0!==e.paceLine1?String(e.paceLine1):""),n=void 0!==e.paceLine2?String(e.paceLine2):"",i=t.getAttribute("data-goal-type")||"positive";if(("never"===i||"harm_reduction"===i)&&(a="on-track",r="",n=""),a){for(var o=["completed","on-track","risk","critical"],d=0;d<o.length;d++)t.classList.remove("tct-goal-status-"+o[d]);t.classList.add("tct-goal-status-"+a)}var c=t.querySelector(".tct-goal-meta-left")||t.querySelector(".tct-domain-goal-main")||t,s=v(".tct-goal-status-line","tct-domain-goal-sub tct-muted tct-goal-status-line",c.querySelector(".tct-domain-goal-goalline")||t.querySelector(".tct-domain-goal-goalline")),u=v(".tct-goal-pace-line1","tct-domain-goal-sub tct-muted tct-goal-pace-line1",s),p=v(".tct-goal-pace-line2","tct-domain-goal-sub tct-muted tct-goal-pace-line2",u);if(s.innerHTML="",l(s,!0),u.innerHTML="",r){var m=document.createElement("span");m.className="tct-goal-pace-text",m.textContent=r,u.appendChild(m),l(u,!1)}else l(u,!0);n?(p.textContent=n,l(p,!1)):(p.textContent="",l(p,!0))}function v(e,a,r){var n,i,o=t.querySelector(e);return o||((o=document.createElement("div")).className=a,r?(i=o,(n=r)&&n.parentNode?n.nextSibling?n.parentNode.insertBefore(i,n.nextSibling):n.parentNode.appendChild(i):(c||t).appendChild(i)):c?c.appendChild(o):t.appendChild(o),o)}}function G(t,e){if(t&&e){var a=null;if(void 0!==e.pointsBalanceLabel&&null!==e.pointsBalanceLabel?a=String(e.pointsBalanceLabel):void 0!==e.pointsBalance&&null!==e.pointsBalance&&(a=String(e.pointsBalance)),a){var r=t.querySelector(".tct-points-nav-pill strong");r&&(r.textContent=a);var n=t.querySelector(".tct-reward-widget-balance");n&&(n.textContent=a);var i=t.querySelector(".tct-reward-widget-points-btn strong");if(i&&(i.textContent=a),void 0!==e.pointsBalance&&null!==e.pointsBalance){var o=parseFloat(e.pointsBalance),l=t.querySelector(".tct-reward-widget-target");if(l){var d=(l.textContent||"").replace(/[^\d.]/g,""),c=parseFloat(d);if(!isNaN(c)&&c>0&&!isNaN(o)){var s=Math.min(100,Math.round(o/c*100)),u=t.querySelector(".tct-reward-widget-pct");u&&(u.textContent=String(s)+"%");var p=t.querySelector(".tct-reward-widget-progress-bar");p&&(p.style.width=String(s)+"%")}}}}}}function X(t,e){if(t&&e&&void 0!==e.rewardStatsHtml&&null!==e.rewardStatsHtml){var a=String(e.rewardStatsHtml||"");if(a&&a.trim&&""!==a.trim()){var r=t.querySelector(".tct-reward-widget-stats");if(r&&r.parentNode){var n=document.createElement("div");n.innerHTML=a;var i=n.querySelector(".tct-reward-widget-stats")||n.firstElementChild;i&&r.parentNode.replaceChild(i,r)}}}}function $(t){if(!t)return null;var e=t.querySelector(".tct-domain-goals");if(e)return e;var a=t.querySelector(".tct-role-empty");return a&&a.parentNode&&a.parentNode.removeChild(a),(e=document.createElement("div")).className="tct-domain-goals",t.appendChild(e),e}function z(t){if(t){var e=t.querySelector(".tct-domain-goals"),a=e?e.querySelectorAll('[data-tct-goal-tile="1"]'):[],r=a?a.length:0,n=t.querySelector(".tct-urgent-count-badge");n&&(n.textContent=String(r));var i=t.querySelector(".tct-role-empty");r<=0?(i||((i=document.createElement("p")).className="tct-muted tct-role-empty",i.textContent="No goals in this bucket for the current view.",t.appendChild(i)),e&&e.parentNode&&e.parentNode.removeChild(e)):(i&&i.parentNode&&i.parentNode.removeChild(i),e||$(t))}}function K(t,a,r){if(t&&a){var n=t.querySelector('.tct-tab-panel[data-tct-panel="urgent"]');if(n){var i=n.querySelectorAll('[data-tct-goal-tile="1"][data-goal-id="'+String(a)+'"]');if(i&&!(i.length<=0)){for(var o=i[0],l=1;l<i.length;l++)i[l]&&i[l].parentNode&&i[l].parentNode.removeChild(i[l]);var d=function(t,e){e=e||{};var a=t&&t.getAttribute&&t.getAttribute("data-goal-type")||"positive",r="never"===a||"harm_reduction"===a;if(!r&&t&&t.getAttribute){var n=t.getAttribute("data-period-unit")||"",i=parseInt(t.getAttribute("data-period-span")||"1",10);if((isNaN(i)||i<1)&&(i=1),"day"===n&&1===i){var o=0,l=0;if(void 0!==e.achieved&&null!==e.achieved)o=parseInt(e.achieved,10),isNaN(o)&&(o=0);else{var d=t.getAttribute("data-vitality-achieved");null!==d&&""!==d&&(o=parseInt(d,10),isNaN(o)&&(o=0))}if(void 0!==e.target&&null!==e.target)l=parseInt(e.target,10),isNaN(l)&&(l=0);else{var c=t.getAttribute("data-vitality-target");null!==c&&""!==c&&(l=parseInt(c,10),isNaN(l)&&(l=0))}if(l>0&&o<l){var s=t.getAttribute("data-visible-after-time")||"";if(""===s)return"due_today";var u=new Date,p=String(u.getHours());p.length<2&&(p="0"+p);var m=String(u.getMinutes());if(m.length<2&&(m="0"+m),p+":"+m>=s)return"due_today"}if(l>0&&o>=l)return""}}if(!r){var v=void 0!==e.statusKey?String(e.statusKey):"";if("critical"===(v=v.toLowerCase()))return"critical";if("risk"===v)return"risk"}var g=100;if(void 0!==e.vitality&&null!==e.vitality)g=parseInt(e.vitality,10),isNaN(g)&&(g=100);else if(t&&t.getAttribute){var h=t.getAttribute("data-vitality");if(null!==h&&""!==h){var f=parseInt(h,10);isNaN(f)||(g=f)}}return g<30?"vit_low":g<=60?"vit_mid":""}(o,r||{});if(!d){var c=e(o,".tct-urgent-column");return o.parentNode&&o.parentNode.removeChild(o),void(c&&z(c))}var s=n.querySelector('.tct-urgent-column[data-tct-urgent-bucket="'+String(d)+'"]');if(s){var u=e(o,".tct-urgent-column");if(u!==s){var p=$(s);p&&p.appendChild(o),u&&z(u),z(s)}else z(s)}}}}}var Q=null,Z=[];function tt(t){t&&(t._tctCountdownRegistered||(t._tctCountdownRegistered=!0,Z.push(t)))}function et(t){if(t){var e=t.querySelector('[data-tct-goal-countdown="1"]')||t.querySelector(".tct-goal-countdown-time");if(e){!function(t){if(t){var e=parseInt(t.getAttribute("data-tct-countdown-start-ms")||"",10),a=parseInt(t.getAttribute("data-tct-countdown-start-sec")||"",10);if(isNaN(e)||isNaN(a)){var r=t.getAttribute("data-vitality-time-remaining"),n=null!==r?parseInt(r,10):NaN;(isNaN(n)||n<0)&&(n=0),t.setAttribute("data-tct-countdown-start-ms",String(Date.now())),t.setAttribute("data-tct-countdown-start-sec",String(n))}}}(t);var a=function(t){if(!t)return null;var e=parseInt(t.getAttribute("data-tct-countdown-start-ms")||"",10),a=parseInt(t.getAttribute("data-tct-countdown-start-sec")||"",10);if(isNaN(e)||isNaN(a))return null;var r=Math.floor((Date.now()-e)/1e3);r<0&&(r=0);var n=a-r;return n<0&&(n=0),n}(t);null===a&&(a=0),e.textContent=function(t){var e=parseInt(t,10);(isNaN(e)||e<0)&&(e=0);var a=Math.floor(e/86400),r=e%86400,n=Math.floor(r/3600);r%=3600;var i=Math.floor(r/60),o=r%60;function l(t){return t=parseInt(t,10),(isNaN(t)||t<0)&&(t=0),(t<10?"0":"")+String(t)}var d=l(n)+"h "+l(i)+"m "+l(o)+"s";return a>0&&(d=String(a)+"d "+d),d}(a)}}}function at(){if(Z&&Z.length)for(var t=Z.length-1;t>=0;t--){var e=Z[t];e&&document.body&&document.body.contains(e)?et(e):Z.splice(t,1)}}function rt(t){if(t){for(var e=t.querySelectorAll('[data-tct-goal-tile="1"]'),a=0;a<e.length;a++){var r=e[a];r&&(tt(r),et(r))}null===Q&&(Q=window.setInterval(at,1e3))}}function nt(t,e,a){return isNaN(t)||t<e?e:t>a?a:t}function it(t,e){if(!t||!e)return!1;var a=!1;function r(e,r){null!=r&&(t.setAttribute(e,String(r)),a=!0)}var n=t.getAttribute("data-goal-type")||"positive",i="never"===n||"harm_reduction"===n,o=void 0!==e.vitality?parseInt(e.vitality,10):NaN;if(!isNaN(o)){r("data-vitality",o=i?Math.min(o,100):nt(o,0,100));var l=t.querySelector("[data-tct-vitality-value]");l&&(l.textContent=String(o));var d=t.querySelector("[data-tct-vitality-tooltip-value]");d&&(d.textContent=String(o))}var c=void 0!==e.target?parseInt(e.target,10):NaN;if(!isNaN(c)){r("data-vitality-target",c=Math.max(0,c));var s=t.querySelector("[data-tct-vitality-target]");s&&(s.textContent=String(c))}var u=void 0!==e.achieved?parseInt(e.achieved,10):NaN;if(!isNaN(u)){r("data-vitality-achieved",u=Math.max(0,u));var p=t.querySelector("[data-tct-vitality-achieved]");p&&(p.textContent=String(u))}var m=void 0!==e.time_remaining_seconds?parseInt(e.time_remaining_seconds,10):NaN;if(isNaN(m)||(r("data-vitality-time-remaining",m=Math.max(0,m)),function(t,e){if(t){var a=parseInt(e,10);(isNaN(a)||a<0)&&(a=0),t.setAttribute("data-tct-countdown-start-ms",String(Date.now())),t.setAttribute("data-tct-countdown-start-sec",String(a)),tt(t),et(t)}}(t,m)),void 0!==e.time_remaining_label){var v=String(e.time_remaining_label);r("data-vitality-time-remaining-label",v);var g=t.querySelector("[data-tct-vitality-time-remaining-label]");g&&(g.textContent=v)}if(void 0!==e.loop_start_utc_mysql&&r("data-vitality-loop-start-utc",String(e.loop_start_utc_mysql)),void 0!==e.loop_end_utc_mysql&&r("data-vitality-loop-end-utc",String(e.loop_end_utc_mysql)),void 0!==e.plant_name&&r("data-plant-name",String(e.plant_name||"")),void 0!==e.plant_image_url){var h=String(e.plant_image_url||""),f=void 0!==e.plant_name?String(e.plant_name||""):"",b=t.querySelector('[data-tct-vitality-plant-wrap="1"]'),y=t.querySelector('[data-tct-vitality-plant-img="1"]'),S=t.querySelector('[data-tct-vitality-plant-name="1"]');if(h){if(b)y||(y=b.querySelector('[data-tct-vitality-plant-img="1"]'));else{(b=document.createElement("div")).className="tct-vitality-plant-wrap",b.setAttribute("data-tct-vitality-plant-wrap","1"),(y=document.createElement("img")).className="tct-vitality-plant-img",y.setAttribute("data-tct-vitality-plant-img","1"),y.setAttribute("loading","lazy"),y.setAttribute("decoding","async"),y.setAttribute("alt",f),b.appendChild(y),(S=document.createElement("div")).className="tct-vitality-plant-name",S.setAttribute("data-tct-vitality-plant-name","1"),S.textContent=f,b.appendChild(S);var w=t.querySelector(".tct-domain-goal-main");w?w.appendChild(b):t.appendChild(b)}y&&(y.getAttribute("src")!==h&&(y.setAttribute("src",h),a=!0),y.getAttribute("alt")!==f&&y.setAttribute("alt",f)),S||(S=b.querySelector('[data-tct-vitality-plant-name="1"]')),!S&&f&&((S=document.createElement("div")).className="tct-vitality-plant-name",S.setAttribute("data-tct-vitality-plant-name","1"),b.appendChild(S)),S&&S.textContent!==f&&(S.textContent=f),b.hidden&&(b.hidden=!1,a=!0)}else b&&!b.hidden&&(b.hidden=!0,a=!0),y&&y.removeAttribute("src")}return a}function ot(t){if(t){var a=function(t){if(!t)return null;var e=t.getAttribute("data-vitality"),a=null!==e?parseInt(e,10):NaN;if(isNaN(a)){var r=t.querySelector("[data-vitality]");r&&(a=null!==(e=r.getAttribute("data-vitality"))?parseInt(e,10):NaN)}if(isNaN(a)){var n=t.querySelector("[data-tct-vitality-value]");n&&(a=parseInt(n.textContent||"",10))}if(isNaN(a))return null;var i=t.getAttribute("data-goal-type")||"positive";return"never"===i||"harm_reduction"===i?Math.min(a,100):nt(a,0,100)}(t);if(null!==a){var r=t.querySelector("[data-tct-vitality-value]");r&&(r.textContent=String(a));var n=t.querySelector("[data-tct-vitality-tooltip-value]");n&&(n.textContent=String(a));var i=t.querySelector(".tct-vitality-ring svg")||t.querySelector("svg[data-tct-vitality-ring]");if(i){var o=i.querySelectorAll("circle");if(o&&0!==o.length){var l=i.querySelector("circle.fg-circle");l||(l=o.length>=2?o[1]:o[0]);var d=parseFloat(l.getAttribute("r")||"");if(isNaN(d)||d<=0){var c=i.querySelector("circle.bg-circle");!c&&o.length&&(c=o[0]),c&&(d=parseFloat(c.getAttribute("r")||""))}if(!(isNaN(d)||d<=0)){var s,u=Math.max(0,Math.min(a,100)),p=(s=u/100)<0?0:s>1?1:s,m=2*Math.PI*d;l.style.strokeDasharray=String(m),l.style.strokeDashoffset=String(m*(1-p));var v="hsl("+String(120*p)+", 90%, 45%)";l.style.stroke=v;var g=e(i,".tct-vitality-wrap")||e(i,".tct-goal-vitality-row")||t;g&&g.style&&g.style.setProperty&&g.style.setProperty("--tct-vitality-color",v)}}}}}}function lt(t){if(t){for(var e=t.querySelectorAll(".tct-goal-vitality-row.tct-vitality-open, .tct-vitality-wrap.tct-vitality-open"),a=0;a<e.length;a++)e[a].classList.remove("tct-vitality-open");for(var r=t.querySelectorAll('[data-tct-vitality-trigger="1"][aria-expanded="true"]'),n=0;n<r.length;n++)r[n].setAttribute("aria-expanded","false")}}function dt(t){if(t&&!t.hasAttribute("data-tct-vitality-init")){t.setAttribute("data-tct-vitality-init","1");for(var a=t.querySelectorAll('[data-tct-vitality-trigger="1"]'),r=0;r<a.length;r++){var n=a[r];n.hasAttribute("onclick")&&n.removeAttribute("onclick"),n.hasAttribute("aria-expanded")?"true"!==n.getAttribute("aria-expanded")&&n.setAttribute("aria-expanded","false"):n.setAttribute("aria-expanded","false");var i=n.getAttribute("aria-controls")||"";if(i){var o=document.getElementById(i);o&&o.hasAttribute("hidden")&&o.removeAttribute("hidden")}else{var l=n.nextElementSibling;l&&l.classList&&l.classList.contains("tct-vitality-tooltip")&&l.hasAttribute("hidden")&&l.removeAttribute("hidden")}}for(var d=t.querySelectorAll('.tct-vitality-tooltip[hidden], [data-tct-vitality-tooltip="1"][hidden]'),c=0;c<d.length;c++)d[c].removeAttribute("hidden");for(var s=t.querySelectorAll('[data-tct-goal-tile="1"]'),u=0;u<s.length;u++)ot(s[u]);t.addEventListener("click",function(a){var r=a.target;if(r&&r.nodeType&&1!==r.nodeType&&(r=r.parentElement),!e(r,".tct-vitality-tooltip")){var n=e(r,'[data-tct-vitality-trigger="1"]');if(n&&t.contains(n)){a.preventDefault();var i=e(n,".tct-goal-vitality-row")||e(n,".tct-vitality-wrap");if(!i)return;var o=i.classList.contains("tct-vitality-open");return lt(t),void(o?n.setAttribute("aria-expanded","false"):(i.classList.add("tct-vitality-open"),n.setAttribute("aria-expanded","true")))}lt(t)}})}}function ct(t){if(t&&!t.hasAttribute("data-tct-quick-complete-init")&&(t.setAttribute("data-tct-quick-complete-init","1"),void 0!==window.tctDashboard)){var a=null;t.addEventListener("click",function(r){var n=e(r.target,'[data-tct-complete-goal="1"]');if(n){if(r.preventDefault(),n.disabled||n.classList.contains("tct-btn-loading"))return;var i=e(n,'[data-tct-goal-tile="1"]');if(i&&i.getAttribute&&"1"===i.getAttribute("data-tct-due-enabled")&&"1"!==i.getAttribute("data-tct-due-today")){var o=i.getAttribute("data-tct-next-due-weekday")||"";o||(o=i.getAttribute("data-tct-next-due-label")||"");var l="Not due today";return o&&(l+="  --  next due "+o),void d(l,!0)}if(i&&i.getAttribute&&"1"===i.getAttribute("data-tct-sleep-enabled"))return void d("Sleep tracking goals are logged by entering bedtime and wake-time.",!0);var c=n.getAttribute("data-goal-id")||"";return(c=parseInt(c,10))&&!isNaN(c)?void s(c,n):void d(tctDashboard.i18n.quickCompleteError,!0)}var p=e(r.target,'[data-tct-fail-goal="1"]');if(p){if(r.preventDefault(),p.disabled||p.classList.contains("tct-btn-loading"))return;var m=p.closest?p.closest('[data-tct-goal-tile="1"]'):null;if(m&&m.getAttribute&&"1"===m.getAttribute("data-tct-due-enabled")&&"1"!==m.getAttribute("data-tct-due-today")){var v=m.getAttribute("data-tct-next-due-weekday")||"";v||(v=m.getAttribute("data-tct-next-due-label")||"");var g="Not due today";return v&&(g+="  --  next due "+v),void d(g,!0)}var h=parseInt(p.getAttribute("data-goal-id")||"0",10);h&&!isNaN(h)&&u(h,p)}})}function d(e,r){if(e){var n=function(){var e=t.querySelector("[data-tct-toast]");return e||((e=document.createElement("div")).className="tct-toast",e.setAttribute("data-tct-toast","1"),e.setAttribute("aria-live","polite"),e.setAttribute("aria-atomic","true"),e.style.display="none",t.appendChild(e),e)}();n.textContent=String(e),n.classList.remove("tct-toast-error"),r&&n.classList.add("tct-toast-error"),n.style.display="block",n.classList.add("tct-toast-show"),a&&clearTimeout(a),a=setTimeout(function(){n.classList.remove("tct-toast-show"),n.style.display="none"},3500)}}function c(t){if(t){var a=e(t,".tct-role-column");if(a){for(var r=0,n=0,i=0,l=0,d=a.querySelectorAll('[data-tct-goal-tile="1"]'),c=0;c<d.length;c++){var s=d[c],u=s.getAttribute("data-goal-type")||"positive";if(!o(u)){var p=s.querySelector("[data-tct-goal-count]"),m=s.querySelector("[data-tct-goal-target]"),v=p?parseInt(p.textContent,10):0,g=m?parseInt(m.textContent,10):0;isNaN(v)||(r+=v),isNaN(g)||(n+=g),s.classList.contains("tct-goal-status-critical")?i+=1:s.classList.contains("tct-goal-status-risk")&&(l+=1)}}var h=a.querySelector("[data-tct-role-count]"),f=a.querySelector("[data-tct-role-target]");h&&(h.textContent=String(r)),f&&(f.textContent=String(n));var b=a.querySelector("[data-tct-role-critical-count]"),y=a.querySelector("[data-tct-role-risk-count]");b&&(b.textContent=String(i),b.hidden=i<=0),y&&(y.textContent=String(l),y.hidden=l<=0);var S=a.querySelector(".tct-role-progress .tct-progress-bar");S&&n>0&&(S.style.width=String(Math.min(100,Math.round(r/n*100)))+"%")}var w=e(t,".tct-domain-row");if(w){for(var C=0,N=0,A=w.querySelectorAll(".tct-role-column"),k=0;k<A.length;k++){var E=A[k].querySelector("[data-tct-role-count]"),q=A[k].querySelector("[data-tct-role-target]"),D=E?parseInt(E.textContent,10):0,x=q?parseInt(q.textContent,10):0;isNaN(D)||(C+=D),isNaN(x)||(N+=x)}var _=w.querySelector("[data-tct-domain-count]"),T=w.querySelector("[data-tct-domain-target]");_&&(_.textContent=String(C)),T&&(T.textContent=String(N));var I=w.querySelector(".tct-domain-row-progress .tct-progress-bar");I&&N>0&&(I.style.width=String(Math.min(100,Math.round(C/N*100)))+"%")}}}function s(a,r){if(!(!r||r.disabled||r.classList&&r.classList.contains("tct-btn-loading"))){var n=e(r,'[data-tct-goal-tile="1"]');if(n&&n.getAttribute&&"1"===n.getAttribute("data-tct-sleep-enabled"))d("Sleep tracking goals are logged by entering bedtime and wake-time.",!0);else{var i=new FormData,o=n&&n.getAttribute?String(n.getAttribute("data-goal-type")||"").trim():"";if("anki_cards"===o){var l=window.prompt("How many Anki cards did you study today?","");if(null===l)return;var c=parseInt(String(l).trim(),10);if(!isFinite(c)||c<=0)return void d("Please enter a whole number of Anki cards.",!0);i.append("anki_cards",String(c))}i.append("action","tct_quick_complete"),i.append("nonce",tctDashboard.quickCompleteNonce),i.append("goal_id",String(a)),r.disabled=!0,r.classList.add("tct-btn-loading");var s=r.textContent;r.textContent="Completing...",fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:i}).then(function(t){return t.json()}).then(function(n){if(n&&n.success){var i=n.data||{},o=t.querySelectorAll('[data-tct-goal-tile="1"][data-goal-id="'+String(a)+'"]');if(!o||o.length<=0){o=[];var l=e(r,'[data-tct-goal-tile="1"]')||e(r,".tct-domain-goal");l&&o.push(l)}for(var s=[],u=0;u<o.length;u++){var p=o[u];if(p){var m=p.querySelector("[data-tct-goal-count]");m&&void 0!==i.achieved&&(m.textContent=String(i.achieved)),B(p),Y(p),W(p),V(p);var v=p.querySelector("[data-tct-goal-last]");v&&void 0!==i.lastCompletedText&&(v.textContent=String(i.lastCompletedText)),R(p),J(p,i),it(p,i),ot(p),c(p);var g=e(p,".tct-domain-row");g&&-1===s.indexOf(g)&&s.push(g)}}for(var h=0;h<s.length;h++)D(s[h]);K(t,a,i),G(t,i),X(t,i),i.message&&d(i.message,!1)}else d(n&&n.data&&n.data.message?n.data.message:tctDashboard.i18n.quickCompleteError,!0)}).catch(function(){d(tctDashboard.i18n.quickCompleteError,!0)}).finally(function(){r.disabled=!1,r.classList.remove("tct-btn-loading"),r.textContent=s})}}}function u(a,r){if(!(!r||r.disabled||r.classList&&r.classList.contains("tct-btn-loading"))){var n=tctDashboard.failGoalNonce||"";if(n){var i=new FormData;i.append("action","tct_fail_goal"),i.append("nonce",n),i.append("goal_id",String(a)),r.disabled=!0,r.classList.add("tct-btn-loading");var o=r.textContent;r.textContent="Failing...",fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:i}).then(function(t){return t.json()}).then(function(n){if(n&&n.success){var i=n.data||{},o=t.querySelectorAll('[data-tct-goal-tile="1"][data-goal-id="'+String(a)+'"]');if(!o||o.length<=0){o=[];var l=e(r,'[data-tct-goal-tile="1"]')||e(r,".tct-domain-goal");l&&o.push(l)}for(var s=[],u=0;u<o.length;u++){var p=o[u];if(p){B(p),Y(p),V(p);var m=p.querySelector("[data-tct-goal-last]");m&&void 0!==i.lastCompletedText&&(m.textContent=String(i.lastCompletedText)),R(p),J(p,i),it(p,i),ot(p),c(p);var v=e(p,".tct-domain-row");v&&-1===s.indexOf(v)&&s.push(v)}}for(var g=0;g<s.length;g++)D(s[g]);K(t,a,i),G(t,i),X(t,i),i.message&&d(i.message,!0)}else{d(n&&n.data&&n.data.message?n.data.message:"Could not fail goal.",!0)}}).catch(function(){d("Network error failing goal.",!0)}).finally(function(){r.disabled=!1,r.classList.remove("tct-btn-loading"),r.textContent=o})}else d("Missing fail nonce.",!0)}}}function st(t){if(t&&!t.hasAttribute("data-tct-sleep-bedtime-init")&&(t.setAttribute("data-tct-sleep-bedtime-init","1"),void 0!==window.tctDashboard)){var a=t.querySelectorAll('[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"]');if(a&&!(a.length<=0)){var r=null;t.addEventListener("click",function(t){var a=e(t.target,'[data-tct-sleep-now="1"]');if(a){var r=e(a,'[data-tct-sleep-enabled="1"]');if(r)if("1"===r.getAttribute("data-tct-sleep-is-default")){var n=r.getAttribute("data-goal-id"),i=r.getAttribute("data-tct-sleep-date")||"",o=r.getAttribute("data-tct-sleep-state")||"";if(n&&i){var l,d=g((l=new Date).getHours())+":"+g(l.getMinutes());"A"===o?w(n,i,d,a):"B"===o&&C(n,i,d,a)}else m("Missing goal or sleep date. Please refresh and try again.",!0)}}});var n=null;t.addEventListener("click",function(t){var a=e(t.target,'[data-tct-sleep-manual="1"]');if(a){var r=e(a,'[data-tct-sleep-enabled="1"]');if(r){var n=r.getAttribute("data-tct-sleep-state")||"";"A"===n?A(r,"bedtime"):"B"===n&&A(r,"waketime")}}}),t.addEventListener("click",function(t){var a=e(t.target,"[data-tct-sleep-edit-field]");if(a){var r=a.getAttribute("data-tct-sleep-edit-field")||"";if("bedtime"===r||"waketime"===r){var n=e(a,'[data-tct-sleep-enabled="1"]');n&&A(n,r)}}});var i=null;t.addEventListener("click",function(a){var r=e(a.target,".tct-sleep-primary-btn");if(r){var n=e(r,'[data-tct-sleep-enabled="1"]');if(n)if("C"===(n.getAttribute("data-tct-sleep-state")||""))if(a.preventDefault(),a.stopPropagation(),i&&r.parentNode&&r.parentNode.contains(i))k();else{k();var o=document.createElement("div");o.className="tct-sleep-clear-popover";var l=document.createElement("div");l.className="tct-sleep-clear-popover-label",l.textContent="Clear this sleep entry?";var d=document.createElement("div");d.className="tct-sleep-clear-popover-actions";var c=document.createElement("button");c.type="button",c.className="tct-sleep-clear-popover-btn tct-sleep-clear-popover-cancel",c.textContent="Cancel";var s=document.createElement("button");s.type="button",s.className="tct-sleep-clear-popover-btn tct-sleep-clear-popover-clear",s.textContent="Clear",d.appendChild(c),d.appendChild(s),o.appendChild(l),o.appendChild(d);var u=r.parentNode;u.style.position="relative",u.appendChild(o),i=o,c.addEventListener("click",function(t){t.stopPropagation(),k()}),s.addEventListener("click",function(e){e.stopPropagation();var a=n.getAttribute("data-goal-id")||"",r=n.getAttribute("data-tct-sleep-date")||"";k(),a&&r&&function(e,a){if(tctDashboard.ajaxUrl&&tctDashboard.sleepClearCycleNonce){var r=new FormData;r.append("action","tct_sleep_clear_cycle"),r.append("nonce",tctDashboard.sleepClearCycleNonce),r.append("goal_id",String(e)),r.append("sleep_date",String(a)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:r}).then(function(t){return t.json()}).then(function(a){if(!a||!a.success||!a.data){var r=a&&a.data&&a.data.message?a.data.message:"Could not clear sleep entry.";throw new Error(r)}y(t,e,a.data),a.data.message&&m(a.data.message,!1)}).catch(function(t){m(t&&t.message?t.message:"Could not clear sleep entry. Please try again.",!0)})}else m("Missing configuration. Please refresh.",!0)}(a,r)}),setTimeout(function(){document.addEventListener("mousedown",function t(e){o&&!o.contains(e.target)&&e.target!==r&&(k(),document.removeEventListener("mousedown",t))})},0)}}}),t.addEventListener("change",function(a){var r=e(a.target,'[data-tct-sleep-date-input="1"]');if(r){var n=e(r,'[data-tct-sleep-enabled="1"]');if(n){var i=n.getAttribute("data-goal-id");if(i){var o=String(r.value||"").trim();if(/^\d{4}-\d{2}-\d{2}$/.test(o))n.setAttribute("data-tct-sleep-date",o),n.setAttribute("data-tct-sleep-is-default","0"),f(n,o),n.querySelectorAll('[data-tct-sleep-now="1"]').forEach(function(t){try{t.disabled=!0}catch(t){}}),r.disabled=!0,S(i,o).then(function(e){y(t,i,e)}).catch(function(t){m(t&&t.message?t.message:"Could not load sleep state.",!0)}).finally(function(){r.disabled=!1});else m("Please select a valid date.",!0)}else m("Missing goal id. Please refresh.",!0)}}});var o=null;t.addEventListener("click",function(e){for(var a=null,r=e.target;r&&r!==t;){if(r.hasAttribute&&r.hasAttribute("data-tct-sleep-calendar-btn")){a=r;break}r=r.parentNode}if(a){e.preventDefault(),e.stopPropagation();var n=null;for(r=a;r&&r!==t;){if(r.getAttribute&&"1"===r.getAttribute("data-tct-sleep-enabled")){n=r;break}r=r.parentNode}n&&(o&&a.parentNode&&a.parentNode.contains(o)?E():q(a,n))}}),t.addEventListener("tct_sleep_refresh",function(e){try{var a=e&&e.detail?e.detail:null,r=a&&a.goalId?parseInt(a.goalId,10):0,n=a&&a.sleepDate?String(a.sleepDate):"";if(!r||isNaN(r))return;S(r,n,{silent:!0}).then(function(e){y(t,r,e)}).catch(function(){})}catch(t){}});for(var l={},d={},c=0;c<a.length;c++){var s=a[c];if(s){var u=parseInt(s.getAttribute("data-goal-id")||"0",10);if(u&&!isNaN(u)){var p=String(u);if(!d[p])d[p]=!0,D(u,s.getAttribute("data-tct-sleep-rollover")||"18:00")}}}}}function m(e,a){var n=function(){var e=t.querySelector("[data-tct-toast]");return e||((e=document.createElement("div")).className="tct-toast",e.setAttribute("data-tct-toast","1"),e.setAttribute("aria-live","polite"),e.setAttribute("aria-atomic","true"),e.style.display="none",t.appendChild(e),e)}();n.textContent=e?String(e):"",n.classList.remove("tct-toast-error"),a&&n.classList.add("tct-toast-error"),n.style.display="block",window.clearTimeout(r),r=window.setTimeout(function(){n.style.display="none"},3500)}function v(t){if(!t)return"";var e=String(t).trim();if(e.length>=5&&(e=e.slice(0,5)),!/^\d{2}:\d{2}$/.test(e))return"";var a=parseInt(e.slice(0,2),10),r=parseInt(e.slice(3,5),10);return isNaN(a)||isNaN(r)||a<0||a>23||r<0||r>59?"":(a<10?"0":"")+String(a)+":"+(r<10?"0":"")+String(r)}function g(t){return t=parseInt(t,10),(isNaN(t)||t<0)&&(t=0),(t<10?"0":"")+String(t)}function h(t,e){if(!t||!/^\d{4}-\d{2}-\d{2}$/.test(String(t)))return"";e=parseInt(e,10),isNaN(e)&&(e=0);var a=parseInt(String(t).slice(0,4),10),r=parseInt(String(t).slice(5,7),10)-1,n=parseInt(String(t).slice(8,10),10);if(isNaN(a)||isNaN(r)||isNaN(n))return"";var i=new Date(Date.UTC(a,r,n));i.setUTCDate(i.getUTCDate()+e);var o=i.getUTCFullYear(),l=i.getUTCMonth()+1,d=i.getUTCDate();return String(o)+"-"+g(l)+"-"+g(d)}function f(t,e){if(t){var a=e?String(e):"";if(/^\d{4}-\d{2}-\d{2}$/.test(a)){var r=h(a,1);if(r){var n=t.querySelector('[data-tct-sleep-night-range="1"]');if(n){var i=parseInt(a.slice(5,7),10),o=parseInt(a.slice(8,10),10),l=parseInt(r.slice(5,7),10),d=parseInt(r.slice(8,10),10);n.textContent=i+"/"+o+"-"+l+"/"+d}}}}}function b(t,e){if(t&&e){var a=e.stateKey?String(e.stateKey):t.getAttribute("data-tct-sleep-state")||"A",r=e.sleepDate?String(e.sleepDate):t.getAttribute("data-tct-sleep-date")||"",n=e.bedTime?String(e.bedTime):"",i=e.wakeTime?String(e.wakeTime):"",o=e.duration?String(e.duration):"",l=!!e.isDefault;t.setAttribute("data-tct-sleep-state",a),r&&t.setAttribute("data-tct-sleep-date",r),t.setAttribute("data-tct-sleep-bed-time",n),t.setAttribute("data-tct-sleep-wake-time",i),t.setAttribute("data-tct-sleep-duration",o),t.setAttribute("data-tct-sleep-is-default",l?"1":"0");var d=t.querySelector(".tct-sleep-primary-btn");d&&(d.removeAttribute("data-tct-sleep-save-bedtime"),d.removeAttribute("data-tct-sleep-save-waketime"),"A"===a?(d.textContent="Enter Bedtime",d.title="Log bedtime using Now or Manual above.",d.disabled=!0,d.setAttribute("aria-disabled","true")):"B"===a?(d.textContent="Enter Waketime",d.title="Log wake-time using Now or Manual above.",d.disabled=!0,d.setAttribute("aria-disabled","true")):(d.textContent="Completed",d.title="Click to clear this sleep entry.",d.disabled=!1,d.removeAttribute("aria-disabled")));var c=t.querySelector('[data-tct-sleep-tile-ui="1"]');if(c){var s=c.querySelector("[data-tct-sleep-date-input]");if(s&&r)try{s.value=r}catch(S){}f(t,r);var u=c.querySelector(".tct-sleep-date-row");if(u){for(;u.nextSibling;)c.removeChild(u.nextSibling);if("A"===a)c.appendChild(b("Bedtime","bedtime"));else if("B"===a){var p=document.createElement("div");p.className="tct-sleep-summary-line";var m=document.createElement("span");m.className="tct-muted",m.textContent="Bedtime:";var v=document.createElement("strong");v.setAttribute("data-tct-sleep-bedtime-text","1"),v.setAttribute("data-tct-sleep-edit-field","bedtime"),v.textContent=y(n),p.appendChild(m),p.appendChild(document.createTextNode(" ")),p.appendChild(v),c.appendChild(p),c.appendChild(b("Waketime","waketime"))}else{var h=document.createElement("div");function w(t,e,a,r){var n=document.createElement("div");n.className="tct-sleep-summary-row",r&&(n.className+=" tct-sleep-editable",n.setAttribute("data-tct-sleep-edit-field",String(r)));var i=document.createElement("span");i.className="tct-muted",i.textContent=t;var o=document.createElement("strong");return a&&o.setAttribute(a,"1"),o.textContent=e?String(e):"",n.appendChild(i),n.appendChild(o),n}h.className="tct-sleep-summary-list",h.setAttribute("data-tct-sleep-summary-list","1"),h.appendChild(w("Bedtime",y(n),"data-tct-sleep-bedtime-text","bedtime")),h.appendChild(w("Waketime",y(i),"data-tct-sleep-waketime-text","waketime")),h.appendChild(w("Duration",o,"data-tct-sleep-duration-text","")),c.appendChild(h)}}}}function b(t,e){var a=document.createElement("div");a.className="tct-sleep-action-row",e&&a.setAttribute("data-tct-sleep-action",String(e));var r=document.createElement("span");r.className="tct-muted tct-sleep-action-label",r.textContent=String(t||""),a.appendChild(r);var n=document.createElement("button");n.type="button",n.className="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-now-btn",n.textContent="Now",n.setAttribute("data-tct-sleep-now","1"),n.title="Use the current time (only enabled for the current night).",l||(n.disabled=!0);var i=document.createElement("button");return i.type="button",i.className="tct-goal-action-btn tct-sleep-mini-btn tct-sleep-manual-btn",i.textContent="Manual",i.setAttribute("data-tct-sleep-manual","1"),i.title="Enter a time manually.",a.appendChild(n),a.appendChild(i),a}function y(t){if(!t||t.length<5)return t||"";var e=parseInt(t.slice(0,2),10),a=parseInt(t.slice(3,5),10);if(isNaN(e)||isNaN(a))return t;var r=e>=12?"PM":"AM",n=e%12;return 0===n&&(n=12),n+":"+g(a)+" "+r}}function y(t,e,a){if(t&&e)for(var r=t.querySelectorAll('[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"][data-goal-id="'+String(e)+'"]'),n=0;n<r.length;n++)b(r[n],a)}function S(e,a,r){if(r=r||{},!tctDashboard.ajaxUrl){var n="Missing AJAX URL. Please refresh and try again.";return r.silent||m(n,!0),Promise.reject(new Error(n))}if(!tctDashboard.sleepStateNonce){var i="Missing security nonce. Please refresh and try again.";return r.silent||m(i,!0),Promise.reject(new Error(i))}var o=new FormData;return o.append("action","tct_sleep_state"),o.append("nonce",tctDashboard.sleepStateNonce),o.append("goal_id",String(e)),a&&/^\d{4}-\d{2}-\d{2}$/.test(String(a))&&o.append("sleep_date",String(a)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:o}).then(function(t){return t.json()}).then(function(a){if(!a||!a.success||!a.data){var r=a&&a.data&&a.data.message?a.data.message:"Could not load sleep state.";throw new Error(r)}var n=a.data||{};return y(t,e,n),n}).catch(function(t){r.silent||m(t&&t.message?t.message:"Could not load sleep state.",!0);throw t})}function w(e,a,r,n){if(!n||n.disabled||n.classList&&n.classList.contains("tct-btn-loading"))return Promise.resolve(!1);if(!tctDashboard.ajaxUrl)return m("Missing AJAX URL. Please refresh and try again.",!0),Promise.resolve(!1);if(!tctDashboard.sleepBedtimeNonce)return m("Missing security nonce. Please refresh and try again.",!0),Promise.resolve(!1);var i=new FormData;i.append("action","tct_sleep_save_bedtime"),i.append("nonce",tctDashboard.sleepBedtimeNonce),i.append("goal_id",String(e)),i.append("sleep_date",String(a)),i.append("bed_time",String(r));var o=n.textContent,l=!1;return n.disabled=!0,n.classList.add("tct-btn-loading"),n.textContent="Saving...",fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:i}).then(function(t){return t.json()}).then(function(a){if(!a||!a.success||!a.data){var r=a&&a.data&&a.data.message?a.data.message:"Could not save bedtime.";throw new Error(r)}var n=a.data||{};return l=!0,y(t,e,n),n.message&&m(n.message,!1),!0}).catch(function(t){return m(t&&t.message?t.message:"Could not save bedtime. Please try again.",!0),!1}).finally(function(){n&&n.parentElement&&(n.classList.remove("tct-btn-loading"),l||(n.disabled=!1,n.textContent=o))})}function C(e,a,r,n){if(!n||n.disabled||n.classList&&n.classList.contains("tct-btn-loading"))return Promise.resolve(!1);if(!tctDashboard.ajaxUrl)return m("Missing AJAX URL. Please refresh and try again.",!0),Promise.resolve(!1);if(!tctDashboard.sleepWaketimeNonce)return m("Missing security nonce. Please refresh and try again.",!0),Promise.resolve(!1);var i=new FormData;i.append("action","tct_sleep_save_waketime"),i.append("nonce",tctDashboard.sleepWaketimeNonce),i.append("goal_id",String(e)),i.append("sleep_date",String(a)),i.append("wake_time",String(r));var o=n.textContent,l=!1;return n.disabled=!0,n.classList.add("tct-btn-loading"),n.textContent="Saving...",fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:i}).then(function(t){return t.json()}).then(function(a){if(!a||!a.success||!a.data){var r=a&&a.data&&a.data.message?a.data.message:"Could not save wake-time.";throw new Error(r)}var n=a.data||{};return l=!0,y(t,e,n),n.message&&m(n.message,!1),!0}).catch(function(t){return m(t&&t.message?t.message:"Could not save wake-time. Please try again.",!0),!1}).finally(function(){n&&n.parentElement&&(n.classList.remove("tct-btn-loading"),l||(n.disabled=!1,n.textContent=o))})}function N(){if(n)return n;var e=document.createElement("div");e.className="tct-modal-overlay tct-clock-overlay",e.setAttribute("data-tct-sleep-manual-overlay","1"),e.hidden=!0;var a=document.createElement("div");a.className="tct-clock-modal",a.setAttribute("data-tct-sleep-manual-modal","1"),a.hidden=!0;var r=document.createElement("div");r.className="tct-clock-header";var i=document.createElement("div");i.className="tct-clock-title",i.setAttribute("data-tct-sleep-manual-title","1"),i.textContent="Bedtime";var o=document.createElement("div");o.className="tct-clock-subtitle",o.setAttribute("data-tct-sleep-manual-night","1"),o.textContent="",r.appendChild(i),r.appendChild(o);var l=document.createElement("div");l.className="tct-clock-time-display";var d=document.createElement("button");d.type="button",d.className="tct-clock-digit tct-clock-digit-active",d.setAttribute("data-tct-clock-select","hour"),d.textContent="12";var c=document.createElement("span");c.className="tct-clock-colon",c.textContent=":";var s=document.createElement("button");s.type="button",s.className="tct-clock-digit",s.setAttribute("data-tct-clock-select","minute"),s.textContent="00";var u=document.createElement("div");u.className="tct-clock-ampm-wrap";var p=document.createElement("button");p.type="button",p.className="tct-clock-ampm tct-clock-ampm-active",p.setAttribute("data-tct-clock-ampm","AM"),p.textContent="AM";var v=document.createElement("button");v.type="button",v.className="tct-clock-ampm",v.setAttribute("data-tct-clock-ampm","PM"),v.textContent="PM",u.appendChild(p),u.appendChild(v),l.appendChild(d),l.appendChild(c),l.appendChild(s),l.appendChild(u);var h=document.createElement("div");h.className="tct-clock-face-wrap";var f=document.createElement("div");f.className="tct-clock-face";var b=document.createElement("div");b.className="tct-clock-hand",f.appendChild(b);var y=document.createElement("div");y.className="tct-clock-center-dot",f.appendChild(y),h.appendChild(f);var S=document.createElement("div");S.className="tct-clock-actions";var N=document.createElement("button");N.type="button",N.className="tct-clock-btn tct-clock-btn-cancel",N.setAttribute("data-tct-sleep-manual-cancel","1"),N.textContent="Cancel";var A=document.createElement("button");A.type="button",A.className="tct-clock-btn tct-clock-btn-save",A.setAttribute("data-tct-sleep-manual-save","1"),A.textContent="Save",S.appendChild(N),S.appendChild(A),a.appendChild(r),a.appendChild(l),a.appendChild(h),a.appendChild(S),t.appendChild(e),t.appendChild(a);var k={mode:"hour",hour12:12,minute:0,ampm:"AM"};function E(){var t;return g((t=k.hour12,"AM"===k.ampm?12===t?0:t:12===t?12:t+12))+":"+g(k.minute)}function q(){d.textContent=String(k.hour12),s.textContent=g(k.minute),"AM"===k.ampm?(p.classList.add("tct-clock-ampm-active"),v.classList.remove("tct-clock-ampm-active")):(v.classList.add("tct-clock-ampm-active"),p.classList.remove("tct-clock-ampm-active")),"hour"===k.mode?(d.classList.add("tct-clock-digit-active"),s.classList.remove("tct-clock-digit-active")):(s.classList.add("tct-clock-digit-active"),d.classList.remove("tct-clock-digit-active")),function(){for(var t=f.querySelectorAll(".tct-clock-num"),e=0;e<t.length;e++)f.removeChild(t[e]);var a,r,n=[];if("hour"===k.mode){for(var i=1;i<=12;i++)n.push(i);a=k.hour12}else{for(var o=0;o<60;o+=5)n.push(o);a=k.minute}for(var l=0;l<n.length;l++){var d=n[l],c=("hour"===k.mode?d/12*360-90:d/60*360-90)*(Math.PI/180),s=50+90/2.3*Math.cos(c),u=50+90/2.3*Math.sin(c),p=document.createElement("button");p.type="button",p.className="tct-clock-num","hour"===k.mode?(p.textContent=String(d),p.setAttribute("data-tct-clock-val",String(d))):(p.textContent=g(d),p.setAttribute("data-tct-clock-val",String(d)));var m=!1;"hour"===k.mode?m=d===a:(m=d===5*Math.round(a/5)%60,a>=58&&0===d&&(m=!0)),m&&p.classList.add("tct-clock-num-selected"),p.style.left=s.toFixed(1)+"%",p.style.top=u.toFixed(1)+"%",f.appendChild(p)}r="hour"===k.mode?k.hour12/12*360:k.minute/60*360,b.style.transform="rotate("+r+"deg)"}()}d.addEventListener("click",function(){k.mode="hour",q()}),s.addEventListener("click",function(){k.mode="minute",q()}),p.addEventListener("click",function(){k.ampm="AM",q()}),v.addEventListener("click",function(){k.ampm="PM",q()}),f.addEventListener("click",function(t){for(var e=null,a=t.target;a&&a!==f;){if(a.classList&&a.classList.contains("tct-clock-num")){e=a;break}a=a.parentNode}if(e){var r=parseInt(e.getAttribute("data-tct-clock-val"),10);isNaN(r)||("hour"===k.mode?(k.hour12=r,k.mode="minute"):k.minute=r,q())}});var D=!1;function x(t,e){var a,r,n=e.getBoundingClientRect(),i=n.left+n.width/2,o=n.top+n.height/2;t.touches&&t.touches.length>0?(a=t.touches[0].clientX,r=t.touches[0].clientY):(a=t.clientX,r=t.clientY);var l=a-i,d=r-o,c=Math.atan2(d,l)*(180/Math.PI)+90;return c<0&&(c+=360),c}function _(t){if("hour"===k.mode){var e=Math.round(t/30);e<=0&&(e=12),e>12&&(e=12),k.hour12=e}else{var a=Math.round(t/6);a>=60&&(a=0),k.minute=a}q()}function T(){e.hidden=!0,a.hidden=!0,a.removeAttribute("data-tct-sleep-manual-goal-id"),a.removeAttribute("data-tct-sleep-manual-sleep-date"),a.removeAttribute("data-tct-sleep-manual-mode"),A&&(A.classList.remove("tct-btn-loading"),A.disabled=!1,A.textContent="Save")}return f.addEventListener("mousedown",function(t){t.target!==f&&t.target!==b&&t.target!==y||(D=!0,_(x(t,f)),t.preventDefault())}),f.addEventListener("touchstart",function(t){D=!0,_(x(t,f))},{passive:!0}),document.addEventListener("mousemove",function(t){D&&_(x(t,f))}),document.addEventListener("touchmove",function(t){D&&_(x(t,f))},{passive:!0}),document.addEventListener("mouseup",function(){D&&(D=!1,"hour"===k.mode&&(k.mode="minute",q()))}),document.addEventListener("touchend",function(){D&&(D=!1,"hour"===k.mode&&(k.mode="minute",q()))}),e.addEventListener("click",T),N.addEventListener("click",T),A.addEventListener("click",function(){var t=a.getAttribute("data-tct-sleep-manual-goal-id")||"",e=a.getAttribute("data-tct-sleep-manual-sleep-date")||"",r=a.getAttribute("data-tct-sleep-manual-mode")||"",n=E();if(t&&e&&("bedtime"===r||"waketime"===r))if(n){var i="bedtime"===r?w(t,e,n,A):C(t,e,n,A);Promise.resolve(i).then(function(t){t&&T()})}else m("Please select a time.",!0);else m("Missing context. Please close and try again.",!0)}),n={overlay:e,modal:a,titleEl:i,nightEl:o,clockState:k,setFrom24:function(t,e){e=Math.max(0,Math.min(59,e)),0===(t=Math.max(0,Math.min(23,t)))?(k.hour12=12,k.ampm="AM"):t<12?(k.hour12=t,k.ampm="AM"):12===t?(k.hour12=12,k.ampm="PM"):(k.hour12=t-12,k.ampm="PM"),k.minute=e},fullRender:q}}function A(t,e){if(t&&("bedtime"===e||"waketime"===e)){var a=N(),r=t.getAttribute("data-goal-id")||"",n=t.getAttribute("data-tct-sleep-date")||"";if(r&&n){var i=h(n,1);if(i){var o=parseInt(n.slice(5,7),10),l=parseInt(n.slice(8,10),10),d=parseInt(i.slice(5,7),10),c=parseInt(i.slice(8,10),10);a.nightEl.textContent="Night of "+o+"/"+l+"-"+d+"/"+c}else a.nightEl.textContent="Night of "+n;if("bedtime"===e){a.titleEl.textContent="Bedtime";var s=v(t.getAttribute("data-tct-sleep-bed-time")||"");if(s)a.setFrom24(parseInt(s.slice(0,2),10),parseInt(s.slice(3,5),10));else{var u=new Date;a.setFrom24(u.getHours(),u.getMinutes())}}else{a.titleEl.textContent="Waketime";var p=v(t.getAttribute("data-tct-sleep-wake-time")||"");p?a.setFrom24(parseInt(p.slice(0,2),10),parseInt(p.slice(3,5),10)):a.setFrom24(7,0)}a.clockState.mode="hour",a.fullRender(),a.modal.setAttribute("data-tct-sleep-manual-goal-id",r),a.modal.setAttribute("data-tct-sleep-manual-sleep-date",n),a.modal.setAttribute("data-tct-sleep-manual-mode",e),a.overlay.hidden=!1,a.modal.hidden=!1}else m("Missing goal or sleep date. Please refresh and try again.",!0)}}function k(){if(i){try{i.parentNode.removeChild(i)}catch(t){}i=null}}function E(){if(o){try{o.parentNode.removeChild(o)}catch(t){}o=null}}function q(t,e){E();var a=e.querySelector('[data-tct-sleep-date-input="1"]'),r=a?String(a.value||""):"";if(!/^\d{4}-\d{2}-\d{2}$/.test(r)){var n=new Date;r=n.getFullYear()+"-"+g(n.getMonth()+1)+"-"+g(n.getDate())}var i=parseInt(r.slice(0,4),10),l=parseInt(r.slice(5,7),10)-1,d=document.createElement("div");d.className="tct-sleep-cal-dropdown",o=d,function t(){d.innerHTML="";var e=document.createElement("div");e.className="tct-sleep-cal-nav";var r=document.createElement("button");r.type="button",r.className="tct-sleep-cal-nav-btn",r.innerHTML="&#8249;",r.title="Previous month",r.addEventListener("click",function(e){e.stopPropagation(),--l<0&&(l=11,i--),t()});var n=document.createElement("button");n.type="button",n.className="tct-sleep-cal-nav-btn",n.innerHTML="&#8250;",n.title="Next month",n.addEventListener("click",function(e){e.stopPropagation(),++l>11&&(l=0,i++),t()});var o=document.createElement("span");o.className="tct-sleep-cal-title",o.textContent=["January","February","March","April","May","June","July","August","September","October","November","December"][l]+" "+i,e.appendChild(r),e.appendChild(o),e.appendChild(n),d.appendChild(e);var c=a?String(a.value||""):"",s=function(t,e,a){var r=document.createElement("table");r.className="tct-sleep-cal-table";for(var n=document.createElement("thead"),i=document.createElement("tr"),o=["Su","Mo","Tu","We","Th","Fr","Sa"],l=0;l<7;l++){var d=document.createElement("th");d.textContent=o[l],i.appendChild(d)}n.appendChild(i),r.appendChild(n);for(var c=a&&/^\d{4}-\d{2}-\d{2}$/.test(a)?a:"",s=c?h(c,1):"",u=new Date,p=u.getFullYear()+"-"+g(u.getMonth()+1)+"-"+g(u.getDate()),m=document.createElement("tbody"),v=new Date(t,e,1).getDay(),f=new Date(t,e+1,0).getDate(),b=document.createElement("tr"),y=0;y<v;y++)b.appendChild(document.createElement("td"));for(var S=1;S<=f;S++){var w=t+"-"+g(e+1)+"-"+g(S),C=document.createElement("td"),N=document.createElement("button");N.type="button",N.className="tct-sleep-cal-day",N.textContent=String(S),N.setAttribute("data-ymd",w),w===c&&N.classList.add("tct-sleep-cal-night-start"),w===s&&N.classList.add("tct-sleep-cal-night-end"),w===p&&N.classList.add("tct-sleep-cal-today"),C.appendChild(N),b.appendChild(C),(v+S)%7==0&&(m.appendChild(b),b=document.createElement("tr"))}var A=(v+f)%7;if(A>0){for(var k=A;k<7;k++)b.appendChild(document.createElement("td"));m.appendChild(b)}return r.appendChild(m),r}(i,l,c);d.appendChild(s),s.addEventListener("click",function(t){var e=t.target.closest?t.target.closest(".tct-sleep-cal-day"):null;if(!e)for(var r=t.target;r&&r!==s;){if(r.classList&&r.classList.contains("tct-sleep-cal-day")){e=r;break}r=r.parentNode}if(e){var n=e.getAttribute("data-ymd");if(n){if(a){var i;a.value=n;try{i=new Event("change",{bubbles:!0})}catch(t){(i=document.createEvent("Event")).initEvent("change",!0,!0)}a.dispatchEvent(i)}E()}}})}();var c=t.closest(".tct-sleep-date-row")||t.parentNode;c.style.position="relative",c.appendChild(d),setTimeout(function(){document.addEventListener("mousedown",function e(a){d&&!d.contains(a.target)&&a.target!==t&&(E(),document.removeEventListener("mousedown",e))})},0)}function D(t,e){var a=String(t);l[a]&&window.clearTimeout(l[a]);var r=function(t){var e=v(t);e||(e="18:00");var a=parseInt(e.slice(0,2),10),r=parseInt(e.slice(3,5),10);(isNaN(a)||isNaN(r))&&(a=18,r=0);var n=new Date,i=new Date(n.getTime());i.setHours(a,r,0,0),i.getTime()<=n.getTime()&&i.setDate(i.getDate()+1);var o=i.getTime()-n.getTime();return(isNaN(o)||o<0)&&(o=0),o}(e)+1500;r<1e3&&(r=1e3),l[a]=window.setTimeout(function(){S(t,"",{silent:!0}).catch(function(){}).finally(function(){D(t,e)})},r)}}function ut(t){if(t&&!t.hasAttribute("data-tct-goal-history-init")&&(t.setAttribute("data-tct-goal-history-init","1"),void 0!==window.tctDashboard)){var a=t.querySelector("[data-tct-history-overlay]"),i=t.querySelector("[data-tct-history-modal]");if(a&&i){e(i,".tct-tab-panel[data-tct-panel]")&&(t.appendChild(a),t.appendChild(i));var d=i.querySelector("[data-tct-history-title]"),c=i.querySelector("[data-tct-history-summary]"),s=i.querySelector("[data-tct-history-completions]"),u=i.querySelector("[data-tct-history-goals-met]"),v=i.querySelector("[data-tct-history-loading]"),f=i.querySelector("[data-tct-history-error]"),y=i.querySelector("[data-tct-history-heatmap]"),S=i.querySelector("[data-tct-history-heatmap-grid]"),w=i.querySelector("[data-tct-history-heatmap-year]"),C=i.querySelector("[data-tct-history-heatmap-loading]"),N=i.querySelector("[data-tct-history-heatmap-prev]"),A=i.querySelector("[data-tct-history-heatmap-next]"),k=i.querySelector("[data-tct-history-heatmap-current]"),E=i.querySelector("[data-tct-domain-heatmap-viewtabs]");if(d&&c&&s&&u&&v&&f){var q=null,x="year",_=null,T=null,I=null,L=null,M=null,P=null,U=1;void 0!==tctDashboard.startOfWeek&&(U=parseInt(tctDashboard.startOfWeek,10),(isNaN(U)||U<0||U>6)&&(U=1));var O=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];a.addEventListener("click",function(){H()}),i.addEventListener("click",function(t){var a=e(t.target,"[data-tct-history-close]"),r=e(t.target,"[data-tct-domain-heatmap-view]"),n=e(t.target,"[data-tct-history-heatmap-prev]");if(a)return t.preventDefault(),void H();if(r&&E&&E.contains(r)){t.preventDefault(),nt(r.getAttribute("data-tct-domain-heatmap-view")||"year");var i=at(x);dt(q,x,i)}else if(n){if(t.preventDefault(),n.disabled)return;var o=rt(_||at(x),x,-1);dt(q,x,o)}else{var l=e(t.target,"[data-tct-history-heatmap-current]");if(l){if(t.preventDefault(),l.disabled)return;var d=at(x);dt(q,x,d)}else{var c=e(t.target,"[data-tct-history-heatmap-next]");if(c){if(t.preventDefault(),c.disabled)return;var s=rt(_||at(x),x,1);dt(q,x,s)}else{var u=e(t.target,"[data-tct-history-tab]");if(u)return t.preventDefault(),void ct(u.getAttribute("data-tct-history-tab"));var p=e(t.target,"[data-tct-undo-completion]");if(p){t.preventDefault();var m=p.getAttribute("data-completion-id")||"";m=parseInt(m,10);var v=p.getAttribute("data-goal-id")||q||"";return v=parseInt(v,10),!m||isNaN(m)||!v||isNaN(v)?void j(tctDashboard.i18n&&tctDashboard.i18n.undoCompletionError?tctDashboard.i18n.undoCompletionError:"Could not undo completion.",!0):void ut(m,v,p)}}}}}),document.addEventListener("keydown",function(t){27===t.keyCode&&(i.hasAttribute("hidden")||H())}),t.addEventListener("click",function(t){var r=e(t.target,'[data-tct-ledger-undo="1"]');if(r){if(t.preventDefault(),r.hasAttribute("disabled"))return;var n=r.getAttribute("data-completion-id")||"",o=r.getAttribute("data-goal-id")||"";return n=parseInt(n,10),o=parseInt(o,10),!n||isNaN(n)||!o||isNaN(o)?void j(tctDashboard.i18n&&tctDashboard.i18n.undoCompletionError?tctDashboard.i18n.undoCompletionError:"Could not undo completion.",!0):void ut(n,o,r,{suppressModalRefresh:!0,reloadAfter:!0})}var d=e(t.target,"[data-tct-open-goal-history]");if(d&&(t.preventDefault(),!d.hasAttribute("disabled"))){var c=d.getAttribute("data-goal-id")||d.getAttribute("data-goal")||"";if(!(c=parseInt(c,10))||isNaN(c))return F("Invalid goal."),l(a,!1),void l(i,!1);pt(c)}})}}}function j(e,a){var r=function(){var e=t.querySelector("[data-tct-toast]");return e||((e=document.createElement("div")).className="tct-toast",e.setAttribute("data-tct-toast","1"),e.setAttribute("hidden","hidden"),t.appendChild(e),e)}();r.textContent=e?String(e):"",r.classList.remove("tct-toast-error"),a&&r.classList.add("tct-toast-error"),l(r,!1),window.clearTimeout(r._tctTimer),r._tctTimer=window.setTimeout(function(){l(r,!0)},3500)}function H(){q=null,x="year",_=null,T=null,I=null,L=null,M=null,P=null,l(i,!0),l(a,!0)}function F(t){f.textContent=t?String(t):"Could not load history.",l(f,!1)}function V(t){t?(v.textContent="Loading...",l(v,!1)):l(v,!0)}function $(t){C&&l(C,!t)}function z(t){return t=parseInt(t,10),isNaN(t)&&(t=0),(t<10?"0":"")+String(t)}function Q(t,e,a){return String(t)+"-"+z(e)+"-"+z(a)}function Z(){var e=g(t);if(!e){var a=new Date;e=Q(a.getFullYear(),a.getMonth()+1,a.getDate())}return e}function tt(t,e){var a=p(t);if(!a)return t;var r=m(a),n=new Date(r.getTime()+864e5*e);return Q(n.getUTCFullYear(),n.getUTCMonth()+1,n.getUTCDate())}function et(t,e){t||(t=Z());var a=p(t);if(a||(a=p(t=Z())),!a){var r=new Date;return Q(r.getFullYear(),r.getMonth()+1,r.getDate())}if("week"===e){var n=m(a),i=(n.getUTCDay()-U+7)%7,o=new Date(n.getTime()-864e5*i);return Q(o.getUTCFullYear(),o.getUTCMonth()+1,o.getUTCDate())}return Q(a.y,"month"===e?a.m:1,1)}function at(t){return et(Z(),t)}function rt(t,e,a){t||(t=at(e));var r=p(t=et(t,e));if(!r)return t;if("week"===e)return tt(t,7*a);if("month"===e){for(var n=r.y,i=r.m+a;i<1;)i+=12,n-=1;for(;i>12;)i-=12,n+=1;return Q(n,i,1)}return Q(r.y+a,1,1)}function nt(t){if("week"!==t&&"month"!==t&&"year"!==t&&(t="year"),x=t,y&&y.setAttribute("data-tct-view",t),E)for(var e=E.querySelectorAll("[data-tct-domain-heatmap-view]"),a=0;a<e.length;a++){var r=e[a],n=r.getAttribute("data-tct-domain-heatmap-view")===t;r.classList.toggle("tct-domain-heatmap-viewtab-active",n),r.setAttribute("aria-selected",n?"true":"false")}var i="Previous year",o="This year",l="Next year";"month"===t?(i="Previous month",o="This month",l="Next month"):"week"===t&&(i="Previous week",o="This week",l="Next week"),N&&(N.setAttribute("aria-label",i),N.title=i),k&&(k.setAttribute("aria-label",o),k.title=o),A&&(A.setAttribute("aria-label",l),A.title=l)}function lt(t){if(t=parseInt(t,10),isNaN(t)||t<=0)return"";var e=.45+.15*t;return e>1&&(e=1),e<.15&&(e=.15),e.toFixed(3)}function dt(t,e,a){if(y&&S&&tctDashboard.goalHeatmapNonce&&(t=parseInt(t,10),!(isNaN(t)||t<=0))){e||(e=x||"year"),"week"!==e&&"month"!==e&&"year"!==e&&(e="year"),a||(a=_||at(e)),a=et(a,e),$(!0);var r=new FormData;r.append("action","tct_goal_heatmap"),r.append("nonce",tctDashboard.goalHeatmapNonce),r.append("goal_id",String(t)),r.append("view",e),r.append("cursor",String(a));var i=p(a);i&&(r.append("year",String(i.y)),"month"===e&&r.append("month",String(i.m)),"week"===e&&r.append("week_start",String(a))),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:r}).then(function(t){return t.json()}).then(function(t){if(t&&t.success){var r=t.data||{},i=r.view?String(r.view):e;if("week"!==i&&"month"!==i&&"year"!==i&&(i=e),nt(i),_=r.periodStart?String(r.periodStart):a,T=r.minStart?String(r.minStart):null,I=r.maxStart?String(r.maxStart):null,L=void 0!==r.year?parseInt(r.year,10):null,isNaN(L)||null===L){var o=p(_);L=o?o.y:null}if(M=void 0!==r.minYear?parseInt(r.minYear,10):null,isNaN(M)&&(M=null),P=void 0!==r.maxYear?parseInt(r.maxYear,10):null,isNaN(P)&&(P=null),"month"===i)!function(t,e,a){if(S){S.innerHTML="",t=parseInt(t,10),e=parseInt(e,10),(isNaN(t)||t<1970)&&(t=(new Date).getFullYear()),(isNaN(e)||e<1||e>12)&&(e=(new Date).getMonth()+1);var r=new Date(Date.UTC(t,e,0)).getUTCDate(),i=document.createElement("div");i.className="tct-domain-monthbar tct-domain-monthbar-modal";var o=document.createElement("div");o.className="tct-domain-monthbar-bar";var l=document.createElement("div");l.className="tct-domain-monthbar-strip",l.style.setProperty("--tct-domain-monthbar-days",String(r));for(var d=1;d<=r;d++){var c=Q(t,e,d),s=0;a&&Object.prototype.hasOwnProperty.call(a,c)&&(s=parseInt(a[c],10),isNaN(s)&&(s=0));var u=document.createElement("span");if(u.className="tct-domain-monthbar-seg",u.setAttribute("data-date",c),s>0){u.classList.add("tct-domain-monthbar-filled");var p=lt(s);p&&u.style.setProperty("--tct-heat-alpha",p),u.title=c+" * "+String(s)+" completion"+(1===s?"":"s")}else u.title=c;l.appendChild(u)}o.appendChild(l);var m=document.createElement("div");m.className="tct-domain-monthbar-weekticks",m.setAttribute("data-tct-monthbar-weekticks","1"),o.appendChild(m);var v=document.createElement("div");v.className="tct-domain-monthbar-weeklabels",v.setAttribute("data-tct-monthbar-weeklabels","1"),o.appendChild(v),i.appendChild(o),S.appendChild(i);try{h(i,n(1))}catch(t){}if(w){var g=O[e-1]||String(e);w.textContent=g+" "+String(t)}}}(void 0!==r.year?parseInt(r.year,10):p(_)?p(_).y:(new Date).getFullYear(),void 0!==r.month?parseInt(r.month,10):p(_)?p(_).m:(new Date).getMonth()+1,r.dates||{});else"week"===i?function(t,e){if(S){S.innerHTML="",t||(t=at("week"));var a=p(t=et(t,"week"));if(a||(a=p(t=at("week"))),a){var r=document.createElement("div");r.className="tct-domain-weekbar tct-domain-weekbar-modal";var n=document.createElement("div");n.className="tct-domain-weekbar-bar";var i=document.createElement("div");i.className="tct-domain-weekbar-strip",i.style.setProperty("--tct-domain-weekbar-days","7");for(var o=0;o<7;o++){var l=tt(t,o),d=0;e&&Object.prototype.hasOwnProperty.call(e,l)&&(d=parseInt(e[l],10),isNaN(d)&&(d=0));var c=document.createElement("span");if(c.className="tct-domain-weekbar-seg",c.setAttribute("data-date",l),d>0){c.classList.add("tct-domain-weekbar-filled");var s=lt(d);s&&c.style.setProperty("--tct-heat-alpha",s),c.title=l+" * "+String(d)+" completion"+(1===d?"":"s")}else c.title=l;i.appendChild(c)}n.appendChild(i);var u=document.createElement("div");u.className="tct-domain-weekbar-dayticks",u.setAttribute("data-tct-weekbar-dayticks","1"),n.appendChild(u),r.appendChild(n);var m=document.createElement("div");m.className="tct-domain-weekbar-daylabels",m.setAttribute("data-tct-weekbar-daylabels","1"),r.appendChild(m),S.appendChild(r);try{b(r)}catch(t){}if(w){var v=tt(t,6);w.textContent=t+" - "+v}}}}(_,r.dates||{}):function(t,e){if(S){S.innerHTML="",t=parseInt(t,10),(isNaN(t)||t<1970)&&(t=(new Date).getFullYear());for(var a=new Date(Date.UTC(t,0,1)),r=(a.getUTCDay()-U+7)%7,n=new Date(a.getTime()-864e5*r),i=new Date(Date.UTC(t,11,31)),o=i.getUTCDay(),l=(U+6-o+7)%7,d=new Date(i.getTime()+864e5*l),c=Math.floor((d.getTime()-n.getTime())/864e5)+1,s=0;s<c;s++){var u=new Date(n.getTime()+864e5*s),p=u.getUTCFullYear(),m=Q(p,u.getUTCMonth()+1,u.getUTCDate()),v=document.createElement("div");if(v.className="tct-history-heatmap-cell",p===t){v.classList.add("tct-history-heatmap-cell-inyear");var g=0;e&&Object.prototype.hasOwnProperty.call(e,m)&&(g=parseInt(e[m],10),isNaN(g)&&(g=0)),g>0&&v.classList.add("tct-history-heatmap-cell-done"),v.title=m+(g>0?" * "+String(g)+" completion"+(1===g?"":"s"):"")}else v.classList.add("tct-history-heatmap-cell-outyear");S.appendChild(v)}w&&(w.textContent=String(t))}}(L||(new Date).getFullYear(),r.dates||{});!function(){var t=_;t||(t=at(x));var e=T,a=I;if(N&&(N.disabled=!(!e||!t)&&String(t)<=String(e)),A&&(A.disabled=!(!a||!t)&&String(t)>=String(a)),k){var r=at(x);k.disabled=!(!t||!r)&&String(t)===String(r)}}()}else{j(tctDashboard.i18n&&tctDashboard.i18n.goalHeatmapError?tctDashboard.i18n.goalHeatmapError:"Could not load completion map.",!0)}}).catch(function(){j(tctDashboard.i18n&&tctDashboard.i18n.goalHeatmapError?tctDashboard.i18n.goalHeatmapError:"Could not load completion map.",!0)}).finally(function(){$(!1)})}}function ct(t){t||(t="completions");for(var e=i.querySelectorAll("[data-tct-history-tab]"),a=0;a<e.length;a++){var r=e[a],n=r.getAttribute("data-tct-history-tab")===t;r.classList.toggle("tct-history-tab-active",n),r.setAttribute("aria-selected",n?"true":"false")}for(var o=i.querySelectorAll("[data-tct-history-panel]"),d=0;d<o.length;d++){var c=o[d],s=c.getAttribute("data-tct-history-panel")===t;c.classList.toggle("tct-history-panel-active",s),l(c,!s)}}function st(t){if(t){var a=e(t,".tct-role-column");if(a){for(var r=0,n=0,i=0,l=0,d=a.querySelectorAll(".tct-domain-goal"),c=0;c<d.length;c++){var s=d[c],u=s.getAttribute("data-goal-type")||"positive";if(!o(u)){var p=s.querySelector("[data-tct-goal-count]"),m=s.querySelector("[data-tct-goal-target]"),v=p?parseInt(p.textContent,10):0,g=m?parseInt(m.textContent,10):0;isNaN(v)||(r+=v),isNaN(g)||(n+=g),s.classList.contains("tct-goal-status-critical")?i+=1:s.classList.contains("tct-goal-status-risk")&&(l+=1)}}var h=a.querySelector("[data-tct-role-count]"),f=a.querySelector("[data-tct-role-target]");h&&(h.textContent=String(r)),f&&(f.textContent=String(n));var b=a.querySelector("[data-tct-role-critical-count]"),y=a.querySelector("[data-tct-role-risk-count]");b&&(b.textContent=String(i),b.hidden=i<=0),y&&(y.textContent=String(l),y.hidden=l<=0);var S=a.querySelector(".tct-role-progress .tct-progress-bar");S&&n>0&&(S.style.width=String(Math.min(100,Math.round(r/n*100)))+"%")}var w=e(t,".tct-domain-row");if(w){for(var C=0,N=0,A=w.querySelectorAll(".tct-role-column"),k=0;k<A.length;k++){var E=A[k].querySelector("[data-tct-role-count]"),q=A[k].querySelector("[data-tct-role-target]"),D=E?parseInt(E.textContent,10):0,x=q?parseInt(q.textContent,10):0;isNaN(D)||(C+=D),isNaN(x)||(N+=x)}var _=w.querySelector("[data-tct-domain-count]"),T=w.querySelector("[data-tct-domain-target]");_&&(_.textContent=String(C)),T&&(T.textContent=String(N));var I=w.querySelector(".tct-domain-row-progress .tct-progress-bar");I&&N>0&&(I.style.width=String(Math.min(100,Math.round(C/N*100)))+"%")}}}function ut(a,n,i,o){var l=!!(o=o||{}).suppressModalRefresh,d=!!o.reloadAfter,c=tctDashboard.i18n&&tctDashboard.i18n.undoCompletionConfirm?tctDashboard.i18n.undoCompletionConfirm:"Undo this completion?";if(window.confirm(c)){var s=new FormData;s.append("action","tct_undo_completion"),s.append("nonce",tctDashboard.undoCompletionNonce),s.append("completion_id",String(a)),i.disabled=!0,fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:s}).then(function(t){return t.json()}).then(function(a){if(a&&a.success){var i=a.data||{};if(function(a,r){r=r||{};var n=t.querySelectorAll('[data-tct-goal-tile="1"][data-goal-id="'+String(a)+'"]');if((!n||n.length<=0)&&(n=t.querySelectorAll('.tct-domain-goal[data-goal-id="'+String(a)+'"]')),n&&!(n.length<=0)){for(var i=void 0!==r.achieved?r.achieved:void 0,o=void 0!==r.lastCompletedText?r.lastCompletedText:void 0,l=[],d=0;d<n.length;d++){var c=n[d];if(c){var s=c.querySelector("[data-tct-goal-count]");s&&void 0!==i&&(s.textContent=String(i)),B(c),Y(c),W(c);var u=c.querySelector("[data-tct-goal-last]");u&&void 0!==o&&(u.textContent=String(o)),R(c),J(c,r),it(c,r),ot(c),st(c);var p=e(c,".tct-domain-row");p&&-1===l.indexOf(p)&&l.push(p)}}for(var m=0;m<l.length;m++)D(l[m]);K(t,a,r),G(t,r),X(t,r)}}(n,i),i&&i.sleepCleared)try{var o=void 0!==i.sleepClearedDate&&null!==i.sleepClearedDate?String(i.sleepClearedDate):"",c=new CustomEvent("tct_sleep_refresh",{detail:{goalId:n,sleepDate:o}});t.dispatchEvent(c)}catch(t){}var s=t.querySelector('[data-tct-goal-tile="1"][data-goal-id="'+String(n)+'"]');if(s){var u=e(s,".tct-domain-row");u&&D(u)}if(i.message&&j(i.message,!1),d){try{r("tct_active_tab","ledger")}catch(t){}window.setTimeout(function(){window.location.reload()},250)}else l||pt(n,{preserveHeatmapYear:!0})}else{j(a&&a.data&&a.data.message?a.data.message:tctDashboard.i18n&&tctDashboard.i18n.undoCompletionError?tctDashboard.i18n.undoCompletionError:"Could not undo completion.",!0)}}).catch(function(){j(tctDashboard.i18n&&tctDashboard.i18n.undoCompletionError?tctDashboard.i18n.undoCompletionError:"Could not undo completion.",!0)}).finally(function(){i.disabled=!1})}}function pt(r,n){q=r,(n=n||{}).preserveHeatmapYear||(x="year",_=null,T=null,I=null,L=null,M=null,P=null),c.textContent="",s.innerHTML="",u.innerHTML="",f.textContent="",l(f,!0),S&&(S.innerHTML=""),w&&(w.textContent=""),$(!1),V(!0),ct("completions"),d.textContent="History",c.textContent="",l(a,!1),l(i,!1);try{var o=t.querySelector('[data-tct-goal-tile="1"][data-goal-id="'+String(r)+'"]'),p="",m="";if(o){var v=e(o,".tct-domain-row");if(v&&window.getComputedStyle){var g=window.getComputedStyle(v);m=(g.getPropertyValue("--tct-domain-color")||"").trim(),p=(g.getPropertyValue("--tct-domain-color-rgb")||"").trim()}}p||(p="0, 163, 42"),i.style.setProperty("--tct-domain-color-rgb",p),m&&i.style.setProperty("--tct-domain-color",m)}catch(t){}nt(x||"year");var h=_;h||(h=at(x)),h=et(h,x),dt(r,x,h);var b=new FormData;b.append("action","tct_goal_history"),b.append("nonce",tctDashboard.goalHistoryNonce),b.append("goal_id",String(r)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:b}).then(function(t){return t.json()}).then(function(t){if(t&&t.success){var e=t.data||{};d.textContent=(e.goalName?String(e.goalName):"Goal")+" -- History";var a=void 0!==e.totalCompletions?parseInt(e.totalCompletions,10):null,n=void 0!==e.shownCompletions?parseInt(e.shownCompletions,10):null,i=void 0!==e.totalPoints?parseInt(e.totalPoints,10):null,o=[],z=!!e.isCompositeParent,B=void 0!==e.totalSettlements?parseInt(e.totalSettlements,10):null;null===a||isNaN(a)||(z?o.push(String(a)+" direct completions"):null!==n&&!isNaN(n)&&n<a?o.push(String(a)+" completions (showing latest "+String(n)+")"):o.push(String(a)+" completions")),z&&(null===B||isNaN(B)||o.push(String(B)+" settlement events")),null===i||isNaN(i)||o.push("Total points: "+String(i)),e.timezoneLabel&&o.push("Times shown in "+String(e.timezoneLabel)),c.textContent=o.join(" * "),function(t,e){if(s.innerHTML="",!t||!t.length){var a=document.createElement("div");return a.className="tct-muted",a.textContent="No completions found.",void s.appendChild(a)}var r=document.createElement("div");r.className="tct-table-wrap";var n=document.createElement("table");n.className="tct-table";for(var i=document.createElement("thead"),o=document.createElement("tr"),l=["Completed","Source","Points",""],d=0;d<l.length;d++){var c=document.createElement("th");c.textContent=l[d],2===d&&(c.className="tct-ledger-points-col"),o.appendChild(c)}i.appendChild(o),n.appendChild(i);for(var u=document.createElement("tbody"),p=0;p<t.length;p++){var m=t[p]||{},v=document.createElement("tr"),g=document.createElement("td");g.textContent=m.completedAt||"--",v.appendChild(g);var h=document.createElement("td");h.textContent=m.sourceLabel||m.source||"--",v.appendChild(h);var f=document.createElement("td");f.className="tct-ledger-points-col";var b=void 0!==m.points?parseInt(m.points,10):0;isNaN(b)||0===b?f.textContent="0":(f.textContent=(b>0?"+":"")+String(b),f.classList.add(b>0?"tct-points-positive":"tct-points-negative")),v.appendChild(f);var y=document.createElement("td");y.className="tct-history-undo-col";var S=document.createElement("button");S.type="button",S.className="tct-history-undo-btn",S.setAttribute("data-tct-undo-completion","1"),S.setAttribute("data-completion-id",m.id?String(m.id):""),S.setAttribute("data-goal-id",String(e)),S.setAttribute("aria-label","Undo"),S.title="Undo",S.innerHTML='<span class="dashicons dashicons-undo" aria-hidden="true"></span>',y.appendChild(S),v.appendChild(y),u.appendChild(v)}n.appendChild(u),r.appendChild(n),s.appendChild(r)}(e.completions||[],r);var l={target:void 0!==e.periodTarget?e.periodTarget:e.weeklyTarget||0,unit:void 0!==e.periodUnit?e.periodUnit:"week",span:void 0!==e.periodSpan?e.periodSpan:1,label:void 0!==e.periodLabel?e.periodLabel:""},p=void 0!==e.goalType?String(e.goalType):"positive",m=void 0!==e.threshold&&null!==e.threshold?parseInt(e.threshold,10):null;!function(t,e,a,r){u.innerHTML="";var n=e||{},i="never"===a||"harm_reduction"===a,o=parseInt(n.target,10);(isNaN(o)||o<0)&&(o=0);var l=void 0!==n.unit&&null!==n.unit?String(n.unit):"";l=l.trim();var d=parseInt(n.span,10);(isNaN(d)||d<1)&&(d=1);var c=void 0!==n.label&&null!==n.label?String(n.label):"";if(!(c=c.trim())&&(o>0||i))if(i)c="never"===a?"Never":"harm_reduction"===a&&null!=r?"Max "+String(r)+" per "+(1===d?l:String(d)+" "+l+"s"):"Avoid";else if(l)if(1===d)c=String(o)+" every "+l;else{var s=l;s.endsWith("s")||(s+="s"),c=String(o)+" every "+String(d)+" "+s}else c=String(o);function p(t){return String(t).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;")}if(i||o&&!(o<=0))if(t&&t.length){var m='<div class="tct-goal-history-hint">'+(c?"Period target: <strong>"+p(c)+"</strong>":"Period target: <strong>"+String(o)+"</strong>")+"</div>",v=i?{met:"Kept",missed:"Slipped",sofar:"So far"}:{met:"Met",missed:"Missed",sofar:"So far"},g='<table class="tct-table tct-goals-met-table">';g+="<thead><tr><th>Period</th><th>Result</th><th>Status</th></tr></thead><tbody>",t.forEach(function(t){var e=(t=t||{}).status?String(t.status):"";(e=e.toLowerCase())||(e=t.met?"met":t.inProgress?"sofar":"missed");var r=e.replace(/[^a-z]/g,"");r||(r="missed");var n,l=v[r]||r,d=void 0!==t.label&&null!==t.label?String(t.label):"--",c=void 0!==t.count&&null!==t.count?t.count:0,s=void 0!==t.target&&null!==t.target?t.target:o;n=i?"never"===a?String(c)+" occurrence"+(1===c?"":"s"):String(c)+" / "+String(s)+" max":String(c)+" / "+String(s),g+="<tr><td>"+p(d)+"</td><td>"+p(n)+'</td><td><span class="tct-history-status-pill tct-history-status-'+p(r)+'">'+p(l)+"</span></td></tr>"}),g+="</tbody></table>",u.innerHTML=m+g}else u.innerHTML='<div class="tct-muted">No period history found.</div>';else u.innerHTML='<div class="tct-muted">No period goal configured.</div>'}(e.goalsMet||[],l,p,m),function(t,e){var a=i.querySelector('[data-tct-history-tab="goals-met"]');if(a&&(a.textContent=e?"Settlements":"Goals Met"),!e)return void ct("completions");if(u.innerHTML="",!(t&&t.length)){u.innerHTML='<div class="tct-muted">No settlement events yet.</div>',s.innerHTML='<div class="tct-muted">Composite parents do not complete directly. Child outcomes settle the parent when the window closes.</div>';return void ct("completions")}var r=document.createElement("div");r.className="tct-table-wrap";var n=document.createElement("table");n.className="tct-table tct-history-settlements-table";var o=document.createElement("thead"),l=document.createElement("tr");["Settled","Event","Points"].forEach(function(t,e){var a=document.createElement("th");a.textContent=t,2===e&&(a.className="tct-ledger-points-col"),l.appendChild(a)}),o.appendChild(l),n.appendChild(o);var d=document.createElement("tbody");t.forEach(function(t){t=t||{};var e=document.createElement("tr"),a=document.createElement("td");a.textContent=t.occurredAt||"--",e.appendChild(a);var r=document.createElement("td"),n=document.createElement("div"),o=document.createElement("span");o.className="tct-history-event-pill tct-history-event-pill-"+(t.typeClass||"neutral"),o.textContent=t.typeLabel||t.type||"Settlement",n.appendChild(o),r.appendChild(n);var l=[];t.summaryLines&&t.summaryLines.length&&t.summaryLines.forEach(function(t){t&&l.push(String(t))});for(var c=0;c<l.length;c++){var s2=document.createElement("div");s2.className=0===c?"tct-history-settlement-window":"tct-history-settlement-note",s2.textContent=l[c],r.appendChild(s2)}e.appendChild(r);var u2=document.createElement("td");u2.className="tct-ledger-points-col";var p2=void 0!==t.points?parseInt(t.points,10):0;isNaN(p2)||0===p2?u2.textContent="0":(u2.textContent=(p2>0?"+":"")+String(p2),u2.classList.add(p2>0?"tct-points-positive":"tct-points-negative")),e.appendChild(u2),d.appendChild(e)}),n.appendChild(d),r.appendChild(n),u.appendChild(r),ct("goals-met"),e&&e.completions&&e.completions.length||(s.innerHTML='<div class="tct-muted">Composite parents do not complete directly. Child outcomes settle the parent when the window closes.</div>')}(e.settlements||[],!!e.isCompositeParent)}else{F(t&&t.data&&t.data.message?t.data.message:tctDashboard.i18n&&tctDashboard.i18n.goalHistoryError?tctDashboard.i18n.goalHistoryError:"Could not load history.")}}).catch(function(){F(tctDashboard.i18n&&tctDashboard.i18n.goalHistoryError?tctDashboard.i18n.goalHistoryError:"Could not load history.")}).finally(function(){V(!1)})}}function pt(t){t&&void 0!==window.tctDashboard&&(t.hasAttribute("data-tct-domainbar-role-tooltips-init")||(t.setAttribute("data-tct-domainbar-role-tooltips-init","1"),t.addEventListener("mouseover",function(a){var r=e(a.target,".tct-domain-yearbar-seg, .tct-domain-monthbar-seg, .tct-domain-weekbar-seg");if(r&&t.contains(r)&&"1"!==r.getAttribute("data-tct-role-tip-ready")){var n=r.getAttribute("data-date")||"";if(n){var i=e(r,'[data-tct-domain-yearbar="1"], [data-tct-domain-monthbar="1"], [data-tct-domain-weekbar="1"]');if(i){var o=parseInt(i.getAttribute("data-domain-id")||"0",10);isNaN(o)||o<=0||function(t){var e=parseInt(t,10);if(isNaN(e)||e<=0)return Promise.reject(new Error("Invalid domain id"));var a=String(e);if(s[a])return Promise.resolve(s[a]);if(u[a])return u[a];if(!window.tctDashboard||!tctDashboard.ajaxUrl)return Promise.reject(new Error("Missing AJAX URL"));var r=new FormData;r.append("action","tct_domain_heatmap"),r.append("nonce",tctDashboard.domainHeatmapNonce||""),r.append("domain_id",a);var n=fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:r}).then(function(t){return t.json()}).then(function(t){if(!t||!t.success||!t.data)throw new Error(t&&t.data&&t.data.message?t.data.message:"Heatmap fetch failed");var e=t.data||{};if(e._yearsIndex={},e.years&&e.years.length)for(var r=0;r<e.years.length;r++){var n=e.years[r];n&&void 0!==n.year&&(e._yearsIndex[String(n.year)]=n)}return s[a]=e,delete u[a],e}).catch(function(t){throw delete u[a],t});return u[a]=n,n}(o).then(function(t){var e=function(t,e){if(!t||!e)return"";var a=String(e),r=a.slice(0,4),n=t._yearsIndex&&t._yearsIndex[r]?t._yearsIndex[r]:null;if(!n&&t.years&&t.years.length)for(var i=0;i<t.years.length;i++){var o=t.years[i];if(o&&String(o.year)===r){n=o;break}}if(!n)return a;var l=0,d=0;n.points&&void 0!==n.points[a]&&(l=parseInt(n.points[a],10),isNaN(l)&&(l=0)),n.possible&&void 0!==n.possible[a]&&(d=parseFloat(n.possible[a]),isNaN(d)&&(d=0));var s=0;d>0?(s=Math.round(l/d*100))<0&&(s=0):l>0&&(s=100);var u=a;if((l>0||d>0)&&(u=a+" * "+String(l)+"/"+c(d)+" pts ("+String(s)+"%)"),t.roles&&n.roles)for(var p=0;p<t.roles.length;p++){var m=t.roles[p];if(m&&void 0!==m.id){var v=String(m.id),g=void 0!==m.name&&m.name?String(m.name):"Role "+v,h=n.roles[v];if(h){var f=0,b=0;if(h.points&&void 0!==h.points[a]&&(f=parseInt(h.points[a],10),isNaN(f)&&(f=0)),h.possible&&void 0!==h.possible[a]&&(b=parseFloat(h.possible[a]),isNaN(b)&&(b=0)),!(f<=0&&b<=0)){var y=0;b>0?(y=Math.round(f/b*100))<0&&(y=0):f>0&&(y=100),u+="\n"+g+": "+String(f)+"/"+c(b)+" pts ("+String(y)+"%)"}}}}return u}(t,n);e&&(r.title=e,r.setAttribute("data-tct-role-tip-ready","1"))}).catch(function(){})}}}},!0)))}function mt(t){if(t&&"1"!==t.getAttribute("data-tct-domain-heatmap-init")){t.setAttribute("data-tct-domain-heatmap-init","1");var a=t.querySelector("[data-tct-domain-heatmap-overlay]"),r=t.querySelector("[data-tct-domain-heatmap-modal]");if(a&&r){e(r,".tct-tab-panel[data-tct-panel]")&&(t.appendChild(a),t.appendChild(r));var i=r.querySelector("[data-tct-domain-heatmap-title]"),o=r.querySelector("[data-tct-domain-heatmap-close]"),l=r.querySelector("[data-tct-domain-heatmap-loading]"),d=r.querySelector("[data-tct-domain-heatmap-error]"),c=r.querySelector("[data-tct-domain-heatmap-content]");if(i&&o&&l&&d&&c){var s=r.querySelector("[data-tct-domain-heatmap-viewtabs]"),u=s?s.querySelectorAll("[data-tct-domain-heatmap-view]"):[];a.addEventListener("click",x),o.addEventListener("click",x);var v=null,g=null,S=null,w=null,C=null;s&&s.parentNode&&((v=document.createElement("div")).className="tct-domain-heatmap-toolbar",s.parentNode.insertBefore(v,s),v.appendChild(s)),(g=document.createElement("div")).className="tct-domain-heatmap-nav",(S=document.createElement("button")).type="button",S.className="tct-domain-heatmap-navbtn",S.setAttribute("data-tct-domain-heatmap-navdir","prev"),S.setAttribute("aria-label","Previous"),S.innerHTML="&lsaquo;",(w=document.createElement("button")).type="button",w.className="tct-domain-heatmap-navbtn",w.setAttribute("data-tct-domain-heatmap-navdir","next"),w.setAttribute("aria-label","Next"),w.innerHTML="&rsaquo;",g.appendChild(S),g.appendChild(w),S&&S.addEventListener("click",function(t){t.preventDefault(),L(-1)}),w&&w.addEventListener("click",function(t){t.preventDefault(),L(1)}),s&&s.addEventListener("click",function(a){var i=e(a.target,"[data-tct-domain-heatmap-view]");if(i){var o=(i.getAttribute("data-tct-domain-heatmap-view")||"").trim();if("week"===o||"month"===o||"year"===o){var l=parseInt(r.getAttribute("data-domain-id")||"0",10);if(l){var d=T(),c=p(d),s=c?c.y:0,u=c?c.m+1:0;if("year"!==o)if("month"!==o){if("week"===o){var m=n(1),v=(r.getAttribute("data-week-start")||"").trim();v||(v=I(d||T(),m));var g=t.querySelector('[data-tct-domain-weekbar="1"][data-domain-id="'+l+'"]');g&&W(g,v)}}else{var h=s||parseInt(r.getAttribute("data-month-year")||"0",10)||parseInt(r.getAttribute("data-year-focus")||"0",10),f=u||parseInt(r.getAttribute("data-month-num")||"0",10)||1,b=t.querySelector('[data-tct-domain-monthbar="1"][data-domain-id="'+l+'"]');b&&B(b,h,f)}else{var y=t.querySelector('[data-tct-domain-yearbar="1"][data-domain-id="'+l+'"]');y&&H(y,s||null)}}}}}),document.addEventListener("keydown",function(t){var e=t.key||t.keyCode;r.hidden||"Escape"!==e&&"Esc"!==e&&27!==e||(t.preventDefault(),x())});var N=["January","February","March","April","May","June","July","August","September","October","November","December"];t.addEventListener("click",function(a){var r=a.target;r&&r.nodeType&&1!==r.nodeType&&(r=r.parentElement);var n=e(r,'[data-tct-domain-weekbar="1"]');if(n&&t.contains(n))return a.preventDefault(),void W(n);var i=e(r,'[data-tct-domain-monthbar="1"]');return i&&t.contains(i)?(a.preventDefault(),void B(i)):void 0}),t.addEventListener("keydown",function(a){var r=a.key||a.keyCode;if("Enter"===r||" "===r||13===r||32===r){var n=a.target;n&&n.nodeType&&1!==n.nodeType&&(n=n.parentElement);var i=e(n,'[data-tct-domain-weekbar="1"]');if(i&&t.contains(i))return a.preventDefault(),void W(i);var o=e(n,'[data-tct-domain-monthbar="1"]');o&&t.contains(o)&&(a.preventDefault(),B(o))}}),t.addEventListener("click",function(a){var r=a.target;r&&r.nodeType&&1!==r.nodeType&&(r=r.parentElement);var n=e(r,'[data-tct-domain-yearbar="1"]');n&&t.contains(n)&&(a.preventDefault(),H(n))}),t.addEventListener("keydown",function(a){var r=a.key||a.keyCode;if("Enter"===r||" "===r||13===r||32===r){var n=a.target;n&&n.nodeType&&1!==n.nodeType&&(n=n.parentElement);var i=e(n,'[data-tct-domain-yearbar="1"]');i&&t.contains(i)&&(a.preventDefault(),H(i))}})}}}function A(t){if(t||(t="year"),r.setAttribute("data-tct-view",t),u&&u.length)for(var e=0;e<u.length;e++){var a=u[e];if(a){var n=(a.getAttribute("data-tct-domain-heatmap-view")||"").trim()===t;n?a.classList.add("tct-domain-heatmap-viewtab-active"):a.classList.remove("tct-domain-heatmap-viewtab-active"),a.setAttribute("aria-selected",n?"true":"false"),a.setAttribute("tabindex",n?"0":"-1")}}}function k(){a.hidden=!1,r.hidden=!1}function E(t){l.hidden=!t}function q(t){d.textContent=t||"",d.hidden=!t}function D(){E(!1),q(""),c.innerHTML=""}function x(){D(),a.hidden=!0,r.hidden=!0}function _(t){var e=document.createElement("div");e.className="tct-domain-heatmap-period";var a=document.createElement("div");return a.className="tct-domain-heatmap-year-label",a.textContent=t||"",e.appendChild(a),g&&e.appendChild(g),C=a,e}function T(){var t=(r.getAttribute("data-cursor-date")||"").trim();if(t)return t;var e=(r.getAttribute("data-week-start")||"").trim();if(e)return e;var a=parseInt(r.getAttribute("data-month-year")||"0",10),n=parseInt(r.getAttribute("data-month-num")||"0",10);if(!isNaN(a)&&a>1900&&!isNaN(n)&&n>=1&&n<=12)return String(a)+"-"+M(n)+"-01";var i=parseInt(r.getAttribute("data-year-focus")||"0",10);return!isNaN(i)&&i>1900?String(i)+"-01-01":""}function I(t,e){var a=p(t);if(!a)return t;var r=m(a);if(!r||isNaN(r.getTime()))return t;var n=1===e?1:0,i=(r.getUTCDay()-n+7)%7,o=new Date(r.getTime()-864e5*i);return P(o.getUTCFullYear(),o.getUTCMonth(),o.getUTCDate())}function L(e){var a=(r.getAttribute("data-tct-view")||"").trim(),i=parseInt(r.getAttribute("data-domain-id")||"0",10);if(i){var o=n(1);if("week"!==a)if("month"!==a){if("year"===a){var l=function(){for(var t=c.querySelectorAll(".tct-domain-heatmap-year-block[data-year]"),e=[],a=0;a<t.length;a++){var r=t[a];if(r){var n=parseInt(r.getAttribute("data-year")||"0",10);!isNaN(n)&&n>1900&&e.push(n)}}return e.sort(function(t,e){return t-e}),e}();if(!l.length){var d=t.querySelector('[data-tct-domain-yearbar="1"][data-domain-id="'+i+'"]');return void(d&&H(d))}var s=parseInt(r.getAttribute("data-year-focus")||"0",10);if(!s){var u=p(T());u&&(s=u.y)}var v=l.indexOf(s);v<0&&(v=l.length-1);var g=v+e;g<0&&(g=0),g>=l.length&&(g=l.length-1);var h=l[g];r.setAttribute("data-year-focus",String(h)),r.setAttribute("data-cursor-date",String(h)+"-01-01"),C&&"year"===r.getAttribute("data-tct-view")&&(C.textContent="Year: "+String(h)),j(h)}}else{var f=parseInt(r.getAttribute("data-month-year")||"0",10),b=parseInt(r.getAttribute("data-month-num")||"0",10);if(!f||!b){var y=p(T());y&&(f=y.y,b=y.m+1)}if(!f||!b)return;var S=function(t,e,a){for(var r=t,n=e+a;n<1;)n+=12,r-=1;for(;n>12;)n-=12,r+=1;return{year:r,month:n}}(f,b,e),w=t.querySelector('[data-tct-domain-monthbar="1"][data-domain-id="'+i+'"]');w&&B(w,S.year,S.month)}else{var N=(r.getAttribute("data-week-start")||"").trim();N||(N=I(T(),o));var A=function(t,e){var a=p(t);if(!a)return t;var r=m(a);if(!r||isNaN(r.getTime()))return t;var n=new Date(r.getTime()+864e5*e);return P(n.getUTCFullYear(),n.getUTCMonth(),n.getUTCDate())}(N,7*e),k=t.querySelector('[data-tct-domain-weekbar="1"][data-domain-id="'+i+'"]');k&&W(k,A)}}}function M(t){return t<10?"0"+t:String(t)}function P(t,e,a){return t+"-"+M(e+1)+"-"+M(a)}function U(t,e){var a=p(t);if(!a)return{week:0,year:0};var r=a.y,n=m(a);if(!n||isNaN(n.getTime()))return{week:0,year:r};var i=function(t,e){var a=parseInt(t,10);if(isNaN(a)||a<1900)return null;var r=new Date(Date.UTC(a,0,1)),n=((1===e?1:0)-r.getUTCDay()+7)%7;return new Date(r.getTime()+864e5*n)}(r,e);if(!i||isNaN(i.getTime()))return{week:0,year:r};var o=Math.floor((n.getTime()-i.getTime())/864e5),l=Math.floor(o/7)+1;return l<1&&(l=1),{week:l,year:r}}function O(t,e,a,r,n,i){var o=window.tctDashboard&&"number"==typeof window.tctDashboard.startOfWeek?window.tctDashboard.startOfWeek:0,l=new Date(Date.UTC(t,0,1)),d=new Date(Date.UTC(t,11,31)),c=(l.getUTCDay()-o+7)%7,s=new Date(Date.UTC(t,0,1-c)),u=(o+6-d.getUTCDay()+7)%7,p=new Date(Date.UTC(t,11,31+u+1)),m=Math.round((p.getTime()-s.getTime())/864e5),v=document.createElement("div");v.className="tct-domain-heatmap-grid";for(var g=0;g<m;g++){var h=new Date(s.getTime()+864e5*g),f=h.getUTCFullYear(),b=P(f,h.getUTCMonth(),h.getUTCDate()),y=document.createElement("div");if(y.className="tct-domain-heatmap-cell",f===t){var S=0;e&&Object.prototype.hasOwnProperty.call(e,b)&&(S=parseInt(e[b],10)||0);var w=0;a&&Object.prototype.hasOwnProperty.call(a,b)&&(w=parseInt(a[b],10)||0);var C=0;if(r&&Object.prototype.hasOwnProperty.call(r,b)&&(C=parseInt(r[b],10)||0),S>0){var N=.15+.85*Math.min(1,Math.max(0,S/100));y.className+=" tct-domain-heatmap-cell-filled",y.style.setProperty("--tct-heat-alpha",N.toFixed(3));var A=b;if(A+=C>0?" * "+w+"/"+C+" pts * "+S+"%":" * "+w+" pts",n&&n.length&&i)for(var k=0;k<n.length;k++){var E=n[k];if(E&&void 0!==E.id){var q=String(E.id);if(i[q]){var D=i[q],x=0,_=0;if(D.points&&Object.prototype.hasOwnProperty.call(D.points,b)&&(x=parseInt(D.points[b],10)||0),D.possible&&Object.prototype.hasOwnProperty.call(D.possible,b)&&(_=parseInt(D.possible[b],10)||0),!(_<=0&&x<=0)){var T=0;_>0?((T=Math.round(x/_*100))<0&&(T=0),T>100&&(T=100)):x>0&&(T=100);var I=(E.name||"").trim();I||(I="Role "+q),A+="\n"+I+": "+x+"/"+_+" ("+T+"%)"}}}}y.title=A}else y.className+=" tct-domain-heatmap-cell-empty",y.title=b;v.appendChild(y)}else y.className+=" tct-domain-heatmap-cell-outyear",y.title=b,v.appendChild(y)}return v}function j(t){var e=parseInt(String(t||"0"),10);if(!(isNaN(e)||e<1900)){var a=c.querySelector('.tct-domain-heatmap-year-block[data-year="'+String(e)+'"]');if(a)try{a.scrollIntoView({behavior:"smooth",block:"start",inline:"nearest"})}catch(t){a.scrollIntoView(!0)}}}function H(t,a){var n=parseInt(t.getAttribute("data-domain-id")||"0",10);(isNaN(n)||n<0)&&(n=0);var o=(t.getAttribute("data-domain-name")||"").trim();if(!o){var l=e(t,".tct-domain-row");if(l){var d=l.querySelector(".tct-domain-row-title");d&&d.textContent&&(o=d.textContent.trim())}}var s=0;s=null!=a&&""!==String(a).trim()?parseInt(String(a),10):parseInt(t.getAttribute("data-year")||"0",10),(isNaN(s)||s<1900)&&(s=0),s?(r.setAttribute("data-year-focus",String(s)),r.setAttribute("data-cursor-date",String(s)+"-01-01")):r.removeAttribute("data-year-focus"),i.textContent=o?o+" -- Activity heatmap":"Domain activity",r.setAttribute("data-domain-id",String(n)),r.setAttribute("data-domain-name",o),A("year");var u=e(t,".tct-domain-row");if(u&&window.getComputedStyle){var p=window.getComputedStyle(u),m=(p.getPropertyValue("--tct-domain-color")||"").trim(),v=(p.getPropertyValue("--tct-domain-color-rgb")||"").trim();m&&r.style.setProperty("--tct-domain-color",m),v&&r.style.setProperty("--tct-domain-color-rgb",v)}if(D(),E(!0),k(),!window.tctDashboard||!tctDashboard.ajaxUrl)return E(!1),void q("Missing AJAX configuration.");var g=new FormData;g.append("action","tct_domain_heatmap"),g.append("nonce",tctDashboard.domainHeatmapNonce||""),g.append("domain_id",String(n)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:g}).then(function(t){return t.json()}).then(function(t){(E(!1),t&&t.success&&t.data)?(!function(t){if(c.innerHTML="",t&&t.years&&t.years.length){var e=t&&t.roles&&t.roles.length?t.roles:[],a=parseInt(r.getAttribute("data-year-focus")||"0",10);if(isNaN(a)||a<1900){var n=t.years[t.years.length-1];n&&"number"==typeof n.year&&(a=n.year)}!isNaN(a)&&a>1900?(r.setAttribute("data-year-focus",String(a)),r.setAttribute("data-cursor-date",String(a)+"-01-01"),c.appendChild(_("Year: "+String(a)))):c.appendChild(_("Year")),t.years.forEach(function(t){if(t&&"number"==typeof t.year){var a=t.year,r=t.pcts||{},n=t.points||{},i=t.possible||{},o=t.roles||{},l=document.createElement("div");l.className="tct-domain-heatmap-year-block",l.setAttribute("data-year",String(a));var d=document.createElement("div");d.className="tct-domain-heatmap-year-label",d.textContent=String(a),l.appendChild(d);var s=document.createElement("div");s.className="tct-domain-heatmap-year-section";var u=document.createElement("div");u.className="tct-domain-heatmap-row";var p=document.createElement("div");p.className="tct-domain-heatmap-row-label",p.textContent="Domain total",u.appendChild(p);var m=document.createElement("div");if(m.className="tct-domain-heatmap-grid-wrap",m.appendChild(O(a,r,n,i,e,o)),u.appendChild(m),s.appendChild(u),e&&e.length)for(var v=0;v<e.length;v++){var g=e[v];if(g&&void 0!==g.id){var h=String(g.id);if(o[h]){var f=o[h],b=document.createElement("div");b.className="tct-domain-heatmap-row";var y=document.createElement("div");y.className="tct-domain-heatmap-row-label",y.textContent=(g.name||"").trim()?g.name:"Role "+h,b.appendChild(y);var S=document.createElement("div");S.className="tct-domain-heatmap-grid-wrap",S.appendChild(O(a,f.pcts||{},f.points||{},f.possible||{},null,null)),b.appendChild(S),s.appendChild(b)}}}l.appendChild(s),c.appendChild(l)}})}else c.innerHTML='<div class="tct-domain-heatmap-empty">No data.</div>'}(t.data),s&&j(s)):q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainHeatmapError||"Could not load domain heatmap.")}).catch(function(){E(!1),q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainHeatmapError||"Could not load domain heatmap.")})}function F(t,e,a,r,i,o,l,d){var c=document.createElement("div");c.className="tct-domain-monthbar tct-domain-monthbar-modal",c.setAttribute("data-tct-domain-monthbar","0");var s=document.createElement("div");s.className="tct-domain-monthbar-bar",c.appendChild(s);var u=document.createElement("div");u.className="tct-domain-monthbar-strip",u.style.setProperty("--tct-domain-monthbar-days",String(t.length)),s.appendChild(u),o&&"object"==typeof o||(o=null),l&&"object"==typeof l||(l=null),(d="string"==typeof d?d.trim():"")||(d="Domain");for(var p=0;p<t.length;p++){var m=t[p],v=0,g=0;e&&Object.prototype.hasOwnProperty.call(e,m)&&(v=parseInt(e[m],10)||0),a&&Object.prototype.hasOwnProperty.call(a,m)&&(g=parseFloat(a[m])||0);var f=0;g>0?((f=Math.round(v/g*100))<0&&(f=0),f>100&&(f=100)):v>0&&(f=100);var b=document.createElement("span");b.className="tct-domain-monthbar-seg",b.setAttribute("data-date",m);var y=m+" * "+v+" pts";if(g>0&&(y=m+" * "+v+"/"+g+" pts * "+f+"%"),r&&r.length&&i)for(var S=0;S<r.length;S++){var w=r[S];if(w&&void 0!==w.id){var C=String(w.id);if(i[C]){var N=i[C],A=0,k=0;if(N.points&&Object.prototype.hasOwnProperty.call(N.points,m)&&(A=parseInt(N.points[m],10)||0),N.possible&&Object.prototype.hasOwnProperty.call(N.possible,m)&&(k=parseFloat(N.possible[m])||0),!(A<=0&&k<=0)){var E=0;k>0?((E=Math.round(A/k*100))<0&&(E=0),E>100&&(E=100)):A>0&&(E=100);var q=(w.name||"").trim();q||(q="Role "+C);var D=q+": "+A+"/"+k+" ("+E+"%)";if(v>0&&A>0){var x=Math.round(A/v*100);x<0&&(x=0),x>100&&(x=100),D+=" * "+x+"% of Domain"}y+="\n"+D}}}}if(v<=0)b.className+=" tct-domain-monthbar-empty";else{var _=null;if(o&&Object.prototype.hasOwnProperty.call(o,m))_=parseFloat(o[m]),isNaN(_)&&(_=0),_<0&&(_=0),_>1&&(_=1);else _=.15+.85*Math.min(1,Math.max(0,f/100));if(l&&d){var T=0;Object.prototype.hasOwnProperty.call(l,m)&&(T=parseInt(l[m],10)||0);var I=0;T>0?((I=Math.round(v/T*100))<0&&(I=0),I>100&&(I=100)):I=v>0?100:0,y+=" * "+I+"% of "+d}b.className+=" tct-domain-monthbar-filled",b.style.setProperty("--tct-heat-alpha",_.toFixed(3))}b.title=y,u.appendChild(b)}var L=document.createElement("div");L.className="tct-domain-monthbar-weekticks",L.setAttribute("data-tct-monthbar-weekticks","1"),s.appendChild(L);var M=document.createElement("div");return M.className="tct-domain-monthbar-weeklabels",M.setAttribute("data-tct-monthbar-weeklabels","1"),s.appendChild(M),h(c,n(1)),c}function R(t){if(c.innerHTML="",t&&t.dates&&t.dates.length){for(var e=t.roles&&t.roles.length?t.roles:[],a=t.rolesData||{},n=t.dates,i=t.domain&&t.domain.points?t.domain.points:{},o=t.domain&&t.domain.possible?t.domain.possible:{},l={},d=0;d<n.length;d++){var s=n[d],u=0,p=0;i&&Object.prototype.hasOwnProperty.call(i,s)&&(u=parseInt(i[s],10)||0),o&&Object.prototype.hasOwnProperty.call(o,s)&&(p=parseFloat(o[s])||0);var m=0;if(p>0?((m=Math.round(u/p*100))<0&&(m=0),m>100&&(m=100)):u>0&&(m=100),u>0){var v=Math.min(1,Math.max(0,m/100));l[s]=.15+.85*v}else l[s]=0}for(var g=!1,h={},b={},y=0;y<n.length;y++){var S=n[y],w=0,C=0;i&&Object.prototype.hasOwnProperty.call(i,S)&&(w=parseInt(i[S],10)||0),o&&Object.prototype.hasOwnProperty.call(o,S)&&(C=parseFloat(o[S])||0);for(var A=0,k=0,E=0;E<e.length;E++){var q=e[E];if(q&&void 0!==q.id){var D=a[String(q.id)];D&&(D.points&&Object.prototype.hasOwnProperty.call(D.points,S)&&(A+=parseInt(D.points[S],10)||0),D.possible&&Object.prototype.hasOwnProperty.call(D.possible,S)&&(k+=parseFloat(D.possible[S])||0))}}var x=w-A;x<0&&(x=0);var T=C-k;T<0&&(T=0),(x>0||T>1e-4)&&(g=!0,x>0&&(h[S]=x),T>0&&(b[S]=T))}var I=e,L=a;if(g){for(var M in(I=e.slice()).push({id:0,name:"Unassigned"}),L={},a)Object.prototype.hasOwnProperty.call(a,M)&&(L[M]=a[M]);L[0]={points:h,possible:b}}var P=document.createElement("div");P.className="tct-domain-heatmap-year-block";var U,O,j=(U=t.month,O=parseInt(U,10),isNaN(O)||O<1||O>12?"":N[O-1]||""),H="";H=j?j+" "+String(t.year):String(t.year)+"-"+String(t.month).padStart(2,"0"),P.appendChild(_(H));var R=document.createElement("div");R.className="tct-domain-heatmap-year-section";var B=document.createElement("div");B.className="tct-domain-heatmap-row";var Y=document.createElement("div");Y.className="tct-domain-heatmap-row-label",Y.textContent="Domain total",B.appendChild(Y);var W=document.createElement("div");if(W.className="tct-domain-heatmap-grid-wrap",W.appendChild(F(n,i||{},o||{},I,L)),B.appendChild(W),R.appendChild(B),I&&I.length&&L)for(var V=0;V<I.length;V++){var J=I[V];if(J&&void 0!==J.id){var G=String(J.id),X=L[G];if(X){for(var $={},z=0;z<n.length;z++){var K=n[z],Q=0;i&&Object.prototype.hasOwnProperty.call(i,K)&&(Q=parseInt(i[K],10)||0);var Z=0;X.points&&Object.prototype.hasOwnProperty.call(X.points,K)&&(Z=parseInt(X.points[K],10)||0);var tt=0;Object.prototype.hasOwnProperty.call(l,K)&&(tt=parseFloat(l[K])||0);var et=0;Z>0&&Q>0&&tt>0&&((et=tt*(Z/Q))<0&&(et=0),et>1&&(et=1)),$[K]=et}var at=document.createElement("div");at.className="tct-domain-heatmap-row";var rt=document.createElement("div");rt.className="tct-domain-heatmap-row-label";var nt=(J.name||"").trim();nt||(nt="Role "+G),rt.textContent=nt,at.appendChild(rt);var it=document.createElement("div");it.className="tct-domain-heatmap-grid-wrap",it.appendChild(F(n,X.points||{},X.possible||{},null,null,$,i||{},"Domain")),at.appendChild(it),R.appendChild(at)}}}P.appendChild(R),c.appendChild(P),f(r)}else c.innerHTML='<div class="tct-domain-heatmap-empty">No data.</div>'}function B(t,a,n){var o=parseInt(t.getAttribute("data-domain-id")||"0",10);(isNaN(o)||o<0)&&(o=0);var l=0,d=0;l=null!=a&&""!==String(a).trim()?parseInt(String(a),10):parseInt(t.getAttribute("data-year")||"0",10),d=null!=n&&""!==String(n).trim()?parseInt(String(n),10):parseInt(t.getAttribute("data-month")||"0",10),(isNaN(l)||l<1900)&&(l=0),(isNaN(d)||d<1||d>12)&&(d=0);var c=(t.getAttribute("data-domain-name")||"").trim();if(!c){var s=e(t,".tct-domain-row");if(s){var u=s.querySelector(".tct-domain-row-title");u&&u.textContent&&(c=u.textContent.trim())}}i.textContent=c?c+" -- Month activity":"Domain month activity";var p=e(t,".tct-domain-row");if(p&&window.getComputedStyle){var m=window.getComputedStyle(p),v=(m.getPropertyValue("--tct-domain-color")||"").trim(),g=(m.getPropertyValue("--tct-domain-color-rgb")||"").trim();v&&r.style.setProperty("--tct-domain-color",v),g&&r.style.setProperty("--tct-domain-color-rgb",g)}if(r.setAttribute("data-domain-id",String(o)),r.setAttribute("data-domain-name",c),r.setAttribute("data-month-year",String(l)),r.setAttribute("data-month-num",String(d)),l&&d&&r.setAttribute("data-cursor-date",String(l)+"-"+M(d)+"-01"),A("month"),D(),E(!0),k(),!window.tctDashboard||!tctDashboard.ajaxUrl)return E(!1),void q("Missing AJAX configuration.");var h=new FormData;h.append("action","tct_domain_month_heatmap"),h.append("nonce",tctDashboard.domainMonthHeatmapNonce||""),h.append("domain_id",String(o)),h.append("year",String(l)),h.append("month",String(d)),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:h}).then(function(t){return t.json()}).then(function(t){(E(!1),t&&t.success&&t.data)?R(t.data):q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainHeatmapError||"Could not load month heatmap.")}).catch(function(){E(!1),q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainHeatmapError||"Could not load month heatmap.")})}function Y(t,e,a,r,n){var i=document.createElement("div");i.className="tct-domain-weekbar tct-domain-weekbar-modal",i.setAttribute("data-tct-domain-weekbar","0");var o=document.createElement("div");o.className="tct-domain-weekbar-bar",i.appendChild(o);var l=document.createElement("div");l.className="tct-domain-weekbar-strip",l.style.setProperty("--tct-domain-weekbar-days",String(t.length)),o.appendChild(l);for(var d=0;d<t.length;d++){var c=t[d],s=0,u=0;e&&Object.prototype.hasOwnProperty.call(e,c)&&(s=parseInt(e[c],10)||0),a&&Object.prototype.hasOwnProperty.call(a,c)&&(u=parseFloat(a[c])||0);var p=0;u>0?((p=Math.round(s/u*100))<0&&(p=0),p>100&&(p=100)):s>0&&(p=100);var m=document.createElement("span");m.className="tct-domain-weekbar-seg",m.setAttribute("data-date",c);var v=c+" * "+s+" pts";if(u>0&&(v=c+" * "+s+"/"+u+" pts * "+p+"%"),r&&r.length&&n)for(var g=0;g<r.length;g++){var h=r[g];if(h&&void 0!==h.id){var f=String(h.id);if(n[f]){var y=n[f],S=0,w=0;if(y.points&&Object.prototype.hasOwnProperty.call(y.points,c)&&(S=parseInt(y.points[c],10)||0),y.possible&&Object.prototype.hasOwnProperty.call(y.possible,c)&&(w=parseFloat(y.possible[c])||0),!(S<=0&&w<=0)){var C=0;w>0?((C=Math.round(S/w*100))<0&&(C=0),C>100&&(C=100)):S>0&&(C=100);var N=(h.name||"").trim();N||(N="Role "+f),v+="\n"+N+": "+S+"/"+w+" ("+C+"%)"}}}}if(s<=0)m.className+=" tct-domain-weekbar-empty";else{var A=.15+.85*Math.min(1,Math.max(0,p/100));m.className+=" tct-domain-weekbar-filled",m.style.setProperty("--tct-heat-alpha",A.toFixed(3))}m.title=v,l.appendChild(m)}var k=document.createElement("div");k.className="tct-domain-weekbar-dayticks",k.setAttribute("data-tct-weekbar-dayticks","1"),o.appendChild(k);var E=document.createElement("div");return E.className="tct-domain-weekbar-daylabels",E.setAttribute("data-tct-weekbar-daylabels","1"),i.appendChild(E),b(i),i}function W(t,a){var o=parseInt(t.getAttribute("data-domain-id")||"0",10);(isNaN(o)||o<0)&&(o=0);var l=(t.getAttribute("data-domain-name")||"").trim();if(!l){var d=e(t,".tct-domain-row");if(d){var s=d.querySelector(".tct-domain-row-title");s&&s.textContent&&(l=s.textContent.trim())}}i.textContent=l?l+" -- Week activity":"Domain week activity";var u=e(t,".tct-domain-row");if(u&&window.getComputedStyle){var p=window.getComputedStyle(u),m=(p.getPropertyValue("--tct-domain-color")||"").trim(),v=(p.getPropertyValue("--tct-domain-color-rgb")||"").trim();m&&r.style.setProperty("--tct-domain-color",m),v&&r.style.setProperty("--tct-domain-color-rgb",v)}var g="";if(null!=a&&""!==String(a).trim())g=String(a).trim();else{var h=t.querySelector(".tct-domain-weekbar-strip [data-date]");h&&(g=(h.getAttribute("data-date")||"").trim())}if(r.setAttribute("data-domain-id",String(o)),r.setAttribute("data-domain-name",l),g&&(r.setAttribute("data-week-start",g),r.setAttribute("data-cursor-date",g)),A("week"),D(),E(!0),k(),!window.tctDashboard||!tctDashboard.ajaxUrl)return E(!1),void q("Missing AJAX configuration.");var f=new FormData;f.append("action","tct_domain_week_heatmap"),f.append("nonce",tctDashboard.domainWeekHeatmapNonce||""),f.append("domain_id",String(o)),g&&f.append("week_start",g),f.append("week_starts_on",String(n(1))),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:f}).then(function(t){return t.json()}).then(function(t){(E(!1),t&&t.success&&t.data)?function(t){if(c.innerHTML="",t&&t.dates&&t.dates.length){var e=t.roles&&t.roles.length?t.roles:[],a=t.dates,i=document.createElement("div");i.className="tct-domain-heatmap-year-block";var o=a[0],l=a[a.length-1],d=U(o,n(1)),s="Week";d&&d.week&&d.year&&(s="Week "+d.week+" of "+d.year);var u=s+" ("+o+" - "+l+")";i.appendChild(_(u));var p=document.createElement("div");p.className="tct-domain-heatmap-year-section";var m=document.createElement("div");m.className="tct-domain-heatmap-row";var v=document.createElement("div");v.className="tct-domain-heatmap-row-label",v.textContent="Domain total",m.appendChild(v);var g=document.createElement("div");if(g.className="tct-domain-heatmap-grid-wrap",g.appendChild(Y(a,t.domain.points||{},t.domain.possible||{},e,t.rolesData||{})),m.appendChild(g),p.appendChild(m),e&&e.length&&t.rolesData)for(var h=0;h<e.length;h++){var f=e[h];if(f&&void 0!==f.id){var b=String(f.id),S=t.rolesData[b];if(S){var w=document.createElement("div");w.className="tct-domain-heatmap-row";var C=document.createElement("div");C.className="tct-domain-heatmap-row-label",C.textContent=(f.name||"").trim()?f.name:"Role "+b,w.appendChild(C);var N=document.createElement("div");N.className="tct-domain-heatmap-grid-wrap",N.appendChild(Y(a,S.points||{},S.possible||{},null,null)),w.appendChild(N),p.appendChild(w)}}}i.appendChild(p),c.appendChild(i),y(r)}else c.innerHTML='<div class="tct-domain-heatmap-empty">No data.</div>'}(t.data):q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainWeekHeatmapError||"Could not load week heatmap.")}).catch(function(){E(!1),q(window.tctDashboard&&window.tctDashboard.i18n&&window.tctDashboard.i18n.domainWeekHeatmapError||"Could not load week heatmap.")})}}function vt(t){if(t&&!t.hasAttribute("data-tct-settings-init")){t.setAttribute("data-tct-settings-init","1");var e=t.querySelector('[data-tct-week-start-select="1"]');if(e){var a=parseInt(e.getAttribute("data-default")||"1",10);(isNaN(a)||0!==a&&1!==a)&&(a=1);var i=n(a);e.value=String(i),e.addEventListener("change",function(){var n,i=parseInt(e.value||"1",10);(isNaN(i)||0!==i&&1!==i)&&(i=a),r("tct_week_starts_on",0===(n=i)||"0"===n?"0":"1"),f(t),q(t,!0),w(t);var o=document.querySelector('[data-tct-domain-heatmap-modal][data-tct-view="month"].tct-domain-heatmap-modal-open');o&&f(o)}),f(t),y(t),q(t,!1),w(t)}}}function gt(t){if(t&&!t.hasAttribute("data-tct-archived-goals-init")){t.setAttribute("data-tct-archived-goals-init","1");var a=t.querySelector('[data-tct-archived-goal-search="1"]');if(a&&window.tctDashboard&&window.tctDashboard.ajaxUrl&&window.tctDashboard.archivedGoalsSearchNonce){var r=window.tctDashboard.ajaxUrl,n=window.tctDashboard.archivedGoalsSearchNonce,i=(window.tctDashboard&&window.tctDashboard.i18n?window.tctDashboard.i18n:{}).archivedGoalsSearchError||"Could not search archived goals. Please try again.",o=t.querySelector('[data-tct-archived-goal-results="1"]'),d=t.querySelector('[data-tct-archived-goal-results-body="1"]'),c=t.querySelector('[data-tct-archived-goal-empty="1"]'),s=t.querySelector('[data-tct-archived-goal-selected-label="1"]'),u=t.querySelector('[data-tct-archived-goal-id="1"]'),p=t.querySelector('[data-tct-archived-goal-restore-btn="1"]'),m=null,v=0;o&&l(o,!0),s&&l(s,!0),p&&(p.disabled=!0),p&&p.form&&p.form.addEventListener("submit",function(t){return!(!u||!u.value)||(t.preventDefault(),!1)}),a.addEventListener("input",function(){m&&(clearTimeout(m),m=null);var t=a.value||"";m=setTimeout(function(){b(t)},200)}),a.addEventListener("focus",function(){(a.value||"").toString().trim()||b("")}),o&&o.addEventListener("click",function(t){var a=e(t.target,'[data-tct-archived-goal-row="1"]');if(a&&o.contains(a)){var r=a.getAttribute("data-goal-id")||"";if(r){if(h(),u&&(u.value=r),p&&(p.disabled=!1),s){var n=a.getAttribute("data-goal-name")||"";s.textContent=n?"Selected: "+n:"Selected goal",l(s,!1)}a.classList.add("tct-archived-goal-selected")}}})}}function g(t){c&&(c.textContent=t||"",l(c,!1))}function h(){if(u&&(u.value=""),p&&(p.disabled=!0),s&&(s.textContent="",l(s,!0)),o)for(var t=o.querySelectorAll(".tct-archived-goal-selected"),e=0;e<t.length;e++)t[e].classList.remove("tct-archived-goal-selected")}function f(t,e){!function(){if(d)for(;d.firstChild;)d.removeChild(d.firstChild)}();var a=(e||"").toString().trim();if(!t||!t.length)return o&&l(o,!0),void g(a?"No archived goals found.":"No archived goals.");c&&l(c,!0),o&&l(o,!1);for(var r=0;r<t.length;r++){var n=t[r]||{},i=n.id?String(n.id):"";if(i){var s=(n.goal_name||"").toString(),u=(n.label_name||"").toString(),p=(n.updated_display||"").toString();p||(p="--");var m=document.createElement("tr");m.setAttribute("data-tct-archived-goal-row","1"),m.setAttribute("data-goal-id",i),m.setAttribute("data-goal-name",s),m.style.cursor="pointer";var v=document.createElement("td"),h=document.createElement("strong");if(h.textContent=s||u||"Goal",v.appendChild(h),u&&u.trim()&&u!==s){var f=document.createElement("span");f.className="tct-muted",f.textContent=" ("+u+")",v.appendChild(f)}var b=document.createElement("td"),y=document.createElement("span");y.className="tct-muted",y.textContent=p,b.appendChild(y),m.appendChild(v),m.appendChild(b),d&&d.appendChild(m)}}}function b(t){h();var e=(t||"").toString().trim(),a=++v;o&&l(o,!0),g(e?"Searching...":"Loading...");var d=new FormData;d.append("action","tct_archived_goals_search"),d.append("nonce",n),d.append("q",e),fetch(r,{method:"POST",credentials:"same-origin",body:d}).then(function(t){return t.json()}).then(function(t){if(a===v)return t&&t.success&&t.data&&Array.isArray(t.data.results)?void f(t.data.results,e):(o&&l(o,!0),void g(i))}).catch(function(){a===v&&(o&&l(o,!0),g(i))})}}var ht="tct_timer_",ft={};function bt(t){try{var e=ht+String(t),a=localStorage.getItem(e);return a?JSON.parse(a):null}catch(t){return null}}function yt(t,e){try{var a=ht+String(t);e?localStorage.setItem(a,JSON.stringify(e)):localStorage.removeItem(a),window.dispatchEvent(new StorageEvent("storage",{key:a}))}catch(t){}}function St(t){yt(t,null)}function wt(t){if(!t)return 0;if(t.pausedAt&&"number"==typeof t.remainingWhenPaused)return Math.max(0,t.remainingWhenPaused);var e=(Date.now()-t.startedAt)/1e3;return Math.max(0,t.duration-e)}function Ct(t){t=Math.max(0,Math.ceil(t));var e=Math.floor(t/3600),a=Math.floor(t%3600/60),r=t%60;return e>0?String(e)+":"+String(a).padStart(2,"0")+":"+String(r).padStart(2,"0"):String(a)+":"+String(r).padStart(2,"0")}var Nt=null,At={};function kt(){return Nt||(Nt=new(window.AudioContext||window.webkitAudioContext)),"suspended"===Nt.state&&Nt.resume(),Nt}function Et(t,e,a,r,n,i){i=i||"sine";var o=t.createOscillator(),l=t.createGain();o.type=i,o.frequency.value=e,l.gain.setValueAtTime(0,a),l.gain.linearRampToValueAtTime(n,a+.02),l.gain.exponentialRampToValueAtTime(.001,a+r),o.connect(l),l.connect(t.destination),o.start(a),o.stop(a+r)}var qt={soft_chime:function(){try{var t=kt(),e=t.currentTime;Et(t,523.25,e,.6,.25,"sine"),Et(t,659.25,e+.3,.5,.2,"sine")}catch(t){}},meditation_bell:function(){try{var t=kt(),e=t.currentTime;Et(t,220,e,2,.3,"sine"),Et(t,440,e,1.5,.15,"sine"),Et(t,660,e,1,.08,"sine")}catch(t){}},wind_chimes:function(){try{for(var t=kt(),e=t.currentTime,a=[523,587,659,698,784],r=0;r<a.length;r++){var n=.15*r+.05*Math.random();Et(t,a[r],e+n,.4,.15+.1*Math.random(),"sine")}}catch(t){}},gentle_pulse:function(){try{for(var t=kt(),e=t.currentTime,a=0;a<3;a++)Et(t,440,e+.4*a,.25,.2,"sine")}catch(t){}},standard_alert:function(){try{var t=kt(),e=t.currentTime;Et(t,880,e,.15,.35,"sine"),Et(t,1100,e+.18,.15,.35,"sine"),Et(t,880,e+.5,.15,.35,"sine"),Et(t,1100,e+.68,.15,.35,"sine")}catch(t){}},digital_beep:function(){try{var t=kt(),e=t.currentTime;Et(t,1e3,e,.1,.4,"square"),Et(t,1e3,e+.2,.1,.4,"square"),Et(t,1200,e+.4,.15,.4,"square")}catch(t){}},rapid_pulse:function(){try{for(var t=kt(),e=t.currentTime,a=0;a<6;a++)Et(t,800,e+.12*a,.08,.4,"sine")}catch(t){}},urgent_alarm:function(){try{for(var t=kt(),e=t.currentTime,a=[600,700,800,900,1e3],r=0;r<a.length;r++)Et(t,a[r],e+.1*r,.08,.45,"sawtooth");for(var n=a.length-1;n>=0;n--)Et(t,a[n],e+.6+.1*(a.length-1-n),.08,.45,"sawtooth")}catch(t){}},alarm_clock:function(){try{for(var t=kt(),e=t.currentTime,a=0;a<8;a++){Et(t,a%2==0?880:698,e+.08*a,.06,.5,"square")}}catch(t){}},vibration_only:function(){}};function Dt(t){var e=At[t];e&&(e.intervalId&&clearInterval(e.intervalId),e.vibrationIntervalId&&clearInterval(e.vibrationIntervalId),delete At[t]),navigator.vibrate&&navigator.vibrate(0)}function xt(t){for(var e=bt(t),a=document.querySelectorAll('[data-tct-goal-tile][data-goal-id="'+String(t)+'"]'),r=0;r<a.length;r++){var n=a[r],i=n.querySelector("[data-tct-timer-overlay]"),o=n.querySelector("[data-tct-timer-display]"),l=n.querySelector("[data-tct-timer-pause]"),d=n.querySelector("[data-tct-timer-resume]"),c=n.querySelector("[data-tct-start-timer]");if(i)if(e){i.removeAttribute("hidden"),c&&c.setAttribute("hidden","hidden");var s=wt(e),u=!!e.pausedAt,p=s<=0;o&&(o.textContent=p?"Time's up!":Ct(s)),u&&!p?i.classList.add("tct-timer-paused"):i.classList.remove("tct-timer-paused"),p?i.classList.add("tct-timer-finished"):i.classList.remove("tct-timer-finished"),l&&d&&(p?(l.setAttribute("hidden","hidden"),d.setAttribute("hidden","hidden")):u?(l.setAttribute("hidden","hidden"),d.removeAttribute("hidden")):(l.removeAttribute("hidden"),d.setAttribute("hidden","hidden")))}else i.setAttribute("hidden","hidden"),i.classList.remove("tct-timer-paused","tct-timer-finished"),c&&c.removeAttribute("hidden")}}function _t(t){ft[t]&&clearInterval(ft[t]);var e=!1;function a(){var a=bt(t);if(!a)return clearInterval(ft[t]),delete ft[t],Dt(t),void xt(t);if(a.pausedAt)xt(t);else{var r=wt(a);if(xt(t),r<=0&&!e){e=!0;var n=document.querySelector('[data-tct-goal-tile][data-goal-id="'+String(t)+'"]'),i="standard_alert",o=15,l=!1;if(n){var d=n.getAttribute("data-alarm-sound"),c=n.getAttribute("data-alarm-duration"),s=n.getAttribute("data-alarm-vibration");d&&""!==d&&(i=d),c&&parseInt(c,10)>0&&(o=parseInt(c,10)),l="1"===s}!function(t,e,a,r){Dt(t);var n=qt[e];n||"vibration_only"===e||(n=qt.standard_alert);var i=Date.now()+1e3*a,o=1500;"meditation_bell"===e?o=2500:"alarm_clock"===e||"urgent_alarm"===e?o=1e3:"rapid_pulse"!==e&&"digital_beep"!==e||(o=1200);var l=null,d=null,c=r||"vibration_only"===e;"vibration_only"!==e&&n&&n(),c&&navigator.vibrate&&navigator.vibrate([200,100,200]),"vibration_only"!==e&&n&&(l=setInterval(function(){Date.now()>=i?Dt(t):n()},o)),c&&navigator.vibrate&&(d=setInterval(function(){Date.now()>=i?Dt(t):navigator.vibrate([200,100,200])},1e3)),"vibration_only"!==e||navigator.vibrate||((n=qt.standard_alert)(),l=setInterval(function(){Date.now()>=i?Dt(t):n()},o)),At[t]={intervalId:l,vibrationIntervalId:d,stopTime:i},setTimeout(function(){Dt(t)},1e3*a)}(t,i,o,l)}}}ft[t]=setInterval(a,250),a()}function Tt(t){ft[t]&&(clearInterval(ft[t]),delete ft[t]),Dt(t),St(t),xt(t)}function It(t){if(t&&!t.hasAttribute("data-tct-goal-timers-init")){t.setAttribute("data-tct-goal-timers-init","1");for(var e=t.querySelectorAll("[data-tct-goal-tile][data-timer-duration]"),a={},r=0;r<e.length;r++){var n=e[r].getAttribute("data-goal-id");if(n&&!a[n])bt(n)&&(a[n]=!0,_t(n))}t.addEventListener("click",function(e){var a=e.target.closest("[data-tct-start-timer]");if(a){e.preventDefault();var r=a.getAttribute("data-goal-id"),n=(c=a.closest("[data-tct-goal-tile]"))?parseInt(c.getAttribute("data-timer-duration"),10):0;r&&n>0&&function(t,e){yt(t,{startedAt:Date.now(),duration:e,pausedAt:null,remainingWhenPaused:null}),_t(t)}(r,n)}else{var i=e.target.closest("[data-tct-timer-pause]");if(i)return e.preventDefault(),void((r=(c=i.closest("[data-tct-goal-tile]"))?c.getAttribute("data-goal-id"):null)&&function(t){var e=bt(t);e&&!e.pausedAt&&(e.pausedAt=Date.now(),e.remainingWhenPaused=wt(e),yt(t,e),xt(t))}(r));var o=e.target.closest("[data-tct-timer-resume]");if(o)return e.preventDefault(),void((r=(c=o.closest("[data-tct-goal-tile]"))?c.getAttribute("data-goal-id"):null)&&function(t){var e=bt(t);if(e&&e.pausedAt){var a=e.remainingWhenPaused||0;e.startedAt=Date.now()-1e3*(e.duration-a),e.pausedAt=null,e.remainingWhenPaused=null,yt(t,e),xt(t)}}(r));var l=e.target.closest("[data-tct-timer-cancel]");if(l)return e.preventDefault(),void((r=(c=l.closest("[data-tct-goal-tile]"))?c.getAttribute("data-goal-id"):null)&&Tt(r));var d=e.target.closest("[data-tct-timer-complete]");if(d){e.preventDefault();r=d.getAttribute("data-goal-id");var c=d.closest("[data-tct-goal-tile]");return r&&Tt(r),void(r&&c&&function(t,e,a){if(e&&t){var r=new FormData;r.append("action","tct_quick_complete"),r.append("nonce",tctDashboard.quickCompleteNonce),r.append("goal_id",String(e)),a&&(a.disabled=!0,a.textContent="Completing..."),fetch(tctDashboard.ajaxUrl,{method:"POST",credentials:"same-origin",body:r}).then(function(t){return t.json()}).then(function(a){if(a&&a.success){for(var r=a.data||{},n=t.querySelectorAll('[data-tct-goal-tile="1"][data-goal-id="'+String(e)+'"]'),i=0;i<n.length;i++){var o=n[i];if(o){var l=o.querySelector("[data-tct-goal-count]");l&&void 0!==r.achieved&&(l.textContent=String(r.achieved)),B(o),Y(o),J(o,r),it(o,r),ot(o)}}G(t,r),X(t,r)}else{var d=a&&a.data&&a.data.message?a.data.message:"Completion failed.";console.error("[TCT Timer Complete]",d)}}).catch(function(t){console.error("[TCT Timer Complete] Error:",t)}).finally(function(){a&&(a.disabled=!1,a.textContent="Complete")})}}(t,r,d))}}}),window.addEventListener("storage",function(t){if(t.key&&0===t.key.indexOf(ht)){var e=t.key.replace(ht,"");if(e){var a=bt(e);a&&!ft[e]?_t(e):!a&&ft[e]&&(clearInterval(ft[e]),delete ft[e],Dt(e)),xt(e)}}})}}function Lt(t){if(t){if(t.hasAttribute("data-tct-vitality-init")){!function(t){if(t){for(var e=t.querySelectorAll('[data-tct-vitality-tooltip="1"][hidden], .tct-vitality-tooltip[hidden]'),a=0;a<e.length;a++)e[a].removeAttribute("hidden");for(var r=t.querySelectorAll('[data-tct-vitality-trigger="1"]'),n=0;n<r.length;n++){var i=r[n];i.hasAttribute("onclick")&&i.removeAttribute("onclick"),i.hasAttribute("aria-expanded")||i.setAttribute("aria-expanded","false");var o=i.getAttribute("aria-controls");if(o){var l=document.getElementById(o);l&&l.hasAttribute("hidden")&&l.removeAttribute("hidden")}else{var d=i.nextElementSibling;d&&d.hasAttribute&&d.hasAttribute("hidden")&&d.removeAttribute("hidden")}}}}(t);for(var e=t.querySelectorAll('[data-tct-goal-tile="1"]'),a=0;a<e.length;a++)ot(e[a])}else dt(t);rt(t)}}function Mt(t){var e=t.querySelector("[data-tct-backup-section]");if(e){var a=window.tctDashboard||{},r=a.ajaxUrl||"/wp-admin/admin-ajax.php",n=a.backupNonce||"";if(n){var i=e.querySelector("[data-tct-backup-create]"),o=(e.querySelector("[data-tct-backup-list-wrap]"),e.querySelector("[data-tct-backup-table]")),l=e.querySelector("[data-tct-backup-tbody]"),d=e.querySelector("[data-tct-backup-loading]"),c=e.querySelector("[data-tct-backup-empty]"),s=null;i&&i.addEventListener("click",function(){i.disabled=!0,i.textContent="Creating...",h({action:"tct_backup_create"},function(t){(i.disabled=!1,i.textContent="Create Backup Now",t&&t.success)?(u(t.data.message||"Backup created.",!1),t.data.backups?g(t.data.backups):f()):u(window.TCT&&"function"==typeof window.TCT.getErrorMessage?window.TCT.getErrorMessage(t,"Backup failed."):t&&t.data&&t.data.message?t.data.message:"Backup failed.",!0)})}),l&&l.addEventListener("click",function(t){var e=t.target.closest?t.target.closest(".tct-backup-restore-btn"):null;if(e){var a=e.getAttribute("data-filename")||"";if(!a)return;if(!confirm("Restore from this backup?\n\nA pre-restore snapshot will be saved automatically. All current data will be replaced."))return;return e.disabled=!0,e.textContent="Restoring...",void h({action:"tct_backup_restore",filename:a},function(t){(e.disabled=!1,e.textContent="Restore",t&&t.success)?(u(t.data.message||"Restored.",!1),t.data.backups?g(t.data.backups):f(),setTimeout(function(){location.reload()},1500)):u(window.TCT&&"function"==typeof window.TCT.getErrorMessage?window.TCT.getErrorMessage(t,"Restore failed."):t&&t.data&&t.data.message?t.data.message:"Restore failed.",!0)})}var r=t.target.closest?t.target.closest(".tct-backup-delete-btn"):null;if(r){var n=r.getAttribute("data-filename")||"";if(!n)return;if(!confirm("Delete this backup? This cannot be undone."))return;r.disabled=!0,h({action:"tct_backup_delete",filename:n},function(t){(r.disabled=!1,t&&t.success)?(u(t.data.message||"Deleted.",!1),t.data.backups?g(t.data.backups):f()):u(window.TCT&&"function"==typeof window.TCT.getErrorMessage?window.TCT.getErrorMessage(t,"Delete failed."):t&&t.data&&t.data.message?t.data.message:"Delete failed.",!0)})}}),f()}}function u(e,a){if(e){var r=function(){var e=t.querySelector("[data-tct-toast]");return e||((e=document.createElement("div")).className="tct-toast",e.setAttribute("data-tct-toast","1"),e.setAttribute("aria-live","polite"),e.setAttribute("aria-atomic","true"),e.style.display="none",t.appendChild(e),e)}();r.textContent=String(e),r.classList.remove("tct-toast-error"),a&&r.classList.add("tct-toast-error"),r.style.display="block",r.classList.add("tct-toast-show"),s&&clearTimeout(s),s=setTimeout(function(){r.classList.remove("tct-toast-show"),r.style.display="none"},3500)}}function p(t){if(!t)return"--";try{var e=new Date(t.replace(" ","T")+"Z");return e.toLocaleDateString(void 0,{month:"short",day:"numeric",year:"numeric"})+" "+e.toLocaleTimeString(void 0,{hour:"numeric",minute:"2-digit"})}catch(e){return t}}function m(t){return"daily"===t?"Auto":"manual"===t?"Manual":"pre-restore"===t?"Pre-restore":t}function v(t){return"daily"===t?"tct-backup-badge-auto":"manual"===t?"tct-backup-badge-manual":"pre-restore"===t?"tct-backup-badge-prerestore":""}function g(t){if(l){if(l.innerHTML="",!t||0===t.length)return o&&(o.hidden=!0),void(c&&(c.hidden=!1));o&&(o.hidden=!1),c&&(c.hidden=!0);for(var e=0;e<t.length;e++){var a=t[e],i=document.createElement("tr"),d=document.createElement("td");d.textContent=p(a.created_at),i.appendChild(d);var s=document.createElement("td"),u=document.createElement("span");u.className="tct-backup-badge "+v(a.type),u.textContent=m(a.type),s.appendChild(u),i.appendChild(s);var g=document.createElement("td");g.textContent=a.size_human||"--",i.appendChild(g);var h=document.createElement("td");h.className="tct-backup-actions-cell";var f=document.createElement("button");f.type="button",f.className="button tct-backup-restore-btn",f.textContent="Restore",f.setAttribute("data-filename",a.filename),h.appendChild(f);var b=document.createElement("a");b.className="button tct-backup-download-btn",b.textContent="Download",b.href=r+"?action=tct_backup_download&nonce="+encodeURIComponent(n)+"&filename="+encodeURIComponent(a.filename),b.setAttribute("download",""),h.appendChild(b);var y=document.createElement("button");y.type="button",y.className="button tct-backup-delete-btn",y.textContent="Delete",y.setAttribute("data-filename",a.filename),h.appendChild(y),i.appendChild(h),l.appendChild(i)}}}function h(t,e){var a=new FormData;for(var i in t)t.hasOwnProperty(i)&&a.append(i,t[i]);a.append("nonce",n);var o=new XMLHttpRequest;o.open("POST",r,!0),o.onload=function(){var t=null;try{t=JSON.parse(o.responseText)}catch(t){}e(t)},o.onerror=function(){e(null)},o.send(a)}function f(){d&&(d.hidden=!1),o&&(o.hidden=!0),c&&(c.hidden=!0),h({action:"tct_backup_list"},function(t){(d&&(d.hidden=!0),t&&t.success&&t.data&&t.data.backups)?g(t.data.backups):(g([]),u(window.TCT&&"function"==typeof window.TCT.getErrorMessage?window.TCT.getErrorMessage(t,"Could not load backups."):t&&t.data&&t.data.message?t.data.message:"Could not load backups.",!0))})}}function Pt(t){var e=t.querySelector("[data-tct-exp-settings-schema-section]");if(e){var a=window.tctDashboard||{};if(a.features&&a.features.experimental){var r=a.ajaxUrl||"/wp-admin/admin-ajax.php",n=a.experimentalSettingsSchemaStatusNonce||"",i=a.experimentalSettingsSchemaMigrateNonce||"",o=e.querySelector("[data-tct-exp-schema-status-btn]"),l=e.querySelector("[data-tct-exp-schema-migrate-btn]"),d=e.querySelector("[data-tct-exp-schema-inline-status]"),c=e.querySelector("[data-tct-exp-schema-output]"),s=!1,u=null;if(!n||!i)return o&&(o.disabled=!0),l&&(l.disabled=!0),b("Admin-only (missing nonces).","error"),void y(["This section requires an administrator account (manage_options)."]);if(a.experimentalSettingsSchema&&"object"==typeof a.experimentalSettingsSchema){var p=a.experimentalSettingsSchema.installedVersion,m=a.experimentalSettingsSchema.targetVersion,v=!!a.experimentalSettingsSchema.needsMigration;void 0!==p&&void 0!==m&&(u=v,f(),b(v?"Migration needed.":"Up to date.",v?"error":"ok"),y(["Installed version: "+String(p),"Target version: "+String(m),"Needs migration: "+(v?"Yes":"No"),"Checked: "+g(),'Tip: Click "Check status" to verify current server state.']))}else u=null,f();o&&o.addEventListener("click",function(){if(o){var t=o.textContent;h(!0),o.textContent="Checking...",b("Requesting status...",null),y(["Loading..."]),S("tct_experimental_settings_schema_status",n,function(e,r){var n=w(e);if(h(!1),o.textContent=t,n.ok&&n.data){var i=n.data.installedVersion,l=n.data.targetVersion,d=!!n.data.needsMigration;a.experimentalSettingsSchema={installedVersion:i,targetVersion:l,needsMigration:d},u=d,f(),b(d?"Migration needed.":"Up to date.",d?"error":"ok");var c=["Installed version: "+String(i),"Target version: "+String(l),"Needs migration: "+(d?"Yes":"No"),"Checked: "+g()],s=N(n);s&&c.push(s),y(c)}else u=null,f(),b("Status failed.","error"),y([C(e,"Could not load schema status."),"HTTP "+String(r)])})}}),l&&l.addEventListener("click",function(){if(l&&!0===u&&confirm("Run settings schema migration now?\n\nA pre-migration snapshot backup will be created if changes are needed.")){var t=l.textContent;h(!0),l.textContent="Migrating...",b("Running migration...",null),y(["Running..."]),S("tct_experimental_settings_schema_migrate",i,function(e,r){var n=w(e);if(h(!1),l.textContent=t,n.ok&&n.data){var i=n.data.message||"Migration complete.";b(i,n.data.backupError?"error":"ok");var o=[String(i),"Before: "+String(n.data.beforeVersion),"After: "+String(n.data.afterVersion),"Target: "+String(n.data.targetVersion),"Did migrate: "+(n.data.didMigrate?"Yes":"No"),"Completed: "+g()];void 0!==n.data.backupCreated&&o.push("Backup created: "+(n.data.backupCreated?"Yes":"No")),n.data.backupFilename&&(o.push("Backup file: "+String(n.data.backupFilename)),o.push("Tip: You can restore/download it in the Backup section below.")),n.data.backupError&&o.push("Backup warning: "+String(n.data.backupError));var d=N(n);d&&o.push(d),y(o);var c=n.data.afterVersion,s=n.data.targetVersion,p=!1;void 0!==c&&void 0!==s&&(p=Number(c)<Number(s)),a.experimentalSettingsSchema={installedVersion:c,targetVersion:s,needsMigration:p},u=p,f(),p&&b("Migration incomplete. Re-check status.","error")}else u=null,f(),b("Migration failed.","error"),y([C(e,"Migration failed."),"HTTP "+String(r),"Tip: If you see 'migration in progress', wait a moment and retry."])})}}),f()}}function g(){try{return(new Date).toLocaleString()}catch(t){return""}}function h(t){s=!!t,e&&e.setAttribute("aria-busy",s?"true":"false"),o&&(o.disabled=s),f()}function f(){if(l)if(s)l.disabled=!0;else{if(!0===u)return l.disabled=!1,void(l.title="");l.disabled=!0,l.title=!1===u?"Up to date":"Check status first"}}function b(t,e){d&&(d.textContent=t||"",d.style.color="","error"===e?d.style.color="#d63638":"ok"===e&&(d.style.color="#00a32a"))}function y(t){if(c)if(c.innerHTML="",t&&t.length)for(var e=0;e<t.length;e++){var a=document.createElement("div");a.textContent=String(t[e]),c.appendChild(a)}else c.textContent=""}function S(t,e,a){var n=new FormData;n.append("action",t),n.append("nonce",e);var i=new XMLHttpRequest;i.open("POST",r,!0),i.timeout=15e3,i.onload=function(){var t=null;try{t=JSON.parse(i.responseText)}catch(t){}a(t,i.status||0)},i.onerror=function(){a({ok:!1,error:{code:"network_error",message:"Network error."}},0)},i.ontimeout=function(){a({ok:!1,error:{code:"timeout",message:"Request timed out. Please try again."}},0)},i.send(n)}function w(t){return window.TCT&&"function"==typeof window.TCT.normalizeResponse?window.TCT.normalizeResponse(t):{ok:!(!t||!t.success),data:t&&t.data?t.data:null,error:t&&t.error?t.error:null,requestId:t&&t.requestId?t.requestId:null}}function C(t,e){return window.TCT&&"function"==typeof window.TCT.getErrorMessage?window.TCT.getErrorMessage(t,e):t&&t.data&&t.data.message?String(t.data.message):t&&t.error&&t.error.message?String(t.error.message):e||"Request failed."}function N(t){return t&&t.requestId?"Ref: "+String(t.requestId).slice(0,8):""}}"undefined"!=typeof window&&(window.tctDashboardEnhance=function(t){if(t&&1===t.nodeType){for(var e=t.matches&&t.matches(".tct-tabs[data-tct-tabs]")?[t]:t.querySelectorAll(".tct-tabs[data-tct-tabs]"),a=0;a<e.length;a++)L(e[a]);Lt(t)}else for(var r=document.querySelectorAll(".tct-dashboard"),n=0;n<r.length;n++){for(var i=r[n].querySelectorAll(".tct-tabs[data-tct-tabs]"),o=0;o<i.length;o++)L(i[o]);Lt(r[n])}}),d(function(){for(var t=document.querySelectorAll(".tct-tabs[data-tct-tabs]"),e=0;e<t.length;e++)L(t[e]);for(var a=document.querySelectorAll(".tct-dashboard"),r=0;r<a.length;r++)U(a[r]),ut(a[r]),pt(a[r]),mt(a[r]),ct(a[r]),st(a[r]),dt(a[r]),rt(a[r]),H(a[r]),O(a[r]),j(a[r]),F(a[r]),P(a[r]),vt(a[r]),gt(a[r]),k(a[r]),It(a[r]),Mt(a[r]),Pt(a[r])})}();
/* --- TCT Due Schedule modal hardening (chunk 9) --- */
;(function(){
  function safeJsonParse(str){
    if(!str || typeof str !== "string"){ return null; }
    try { return JSON.parse(str); } catch(e){ return null; }
  }
  function normalizeDueSchedule(raw){
    var cfg = raw && typeof raw === "object" ? raw : null;
    var enabled = cfg && cfg.enabled ? 1 : 0;
    var type = (cfg && cfg.type === "monthly") ? "monthly" : "weekly";
    var start = (cfg && typeof cfg.start_date === "string") ? cfg.start_date : "";
    var every = cfg && cfg.every != null ? parseInt(cfg.every,10) : 1;
    if(!every || every < 1){ every = 1; }
    if(every > 52){ every = 52; }
    var dom = cfg && cfg.day_of_month != null ? parseInt(cfg.day_of_month,10) : 1;
    if(!dom || dom < 1){ dom = 1; }
    if(dom > 31){ dom = 31; }
    return { enabled: enabled, type: type, start_date: start, every: every, day_of_month: dom };
  }
  function ymdToday(){
    var d = new Date();
    var yyyy = d.getFullYear();
    var mm = String(d.getMonth()+1).padStart(2,"0");
    var dd = String(d.getDate()).padStart(2,"0");
    return yyyy + "-" + mm + "-" + dd;
  }
  function dayFromYmd(ymd){
    if(typeof ymd !== "string" || !/^\d{4}-\d{2}-\d{2}$/.test(ymd)){ return null; }
    var n = parseInt(ymd.slice(8,10),10);
    return (n && n >= 1 && n <= 31) ? n : null;
  }
  function getModalForButton(btn){
    var dash = btn.closest(".tct-dashboard");
    if(dash){
      return dash.querySelector("[data-tct-goal-modal]");
    }
    return document.querySelector("[data-tct-goal-modal]");
  }
  function updateVisibility(modal){
    if(!modal){ return; }
    var enabledSel = modal.querySelector("[data-tct-due-schedule-enabled]");
    var typeSel = modal.querySelector("[data-tct-due-schedule-type]");
    var weeklyWrap = modal.querySelector("[data-tct-due-schedule-weekly]");
    var monthlyWrap = modal.querySelector("[data-tct-due-schedule-monthly]");
    if(!enabledSel || !typeSel || !weeklyWrap || !monthlyWrap){ return; }

    var enabled = enabledSel.value === "1";
    var type = typeSel.value === "monthly" ? "monthly" : "weekly";

    weeklyWrap.hidden = !enabled || type !== "weekly";
    monthlyWrap.hidden = !enabled || type !== "monthly";

    // Disable non-enabled inputs so they won't submit.
    var startInput = modal.querySelector("[data-tct-due-schedule-start]");
    var everyInput = modal.querySelector("[data-tct-due-schedule-every]");
    var domInput = modal.querySelector("[data-tct-due-schedule-dom]");
    if(startInput){ startInput.disabled = !enabled; }
    if(typeSel){ typeSel.disabled = !enabled; }
    if(everyInput){ everyInput.disabled = !enabled || type !== "weekly"; }
    if(domInput){ domInput.disabled = !enabled || type !== "monthly"; }

    // If monthly and dom is empty, default to start-date day.
    if(enabled && type === "monthly" && domInput && startInput){
      if(!domInput.value){
        var dom = dayFromYmd(startInput.value);
        if(dom){ domInput.value = String(dom); }
      }
    }
  }
  function updateWarning(modal){
    if(!modal){ return; }
    var warn = modal.querySelector("[data-tct-due-schedule-warning]");
    if(!warn){ return; }
    warn.hidden = true;
    warn.textContent = "";

    var enabledSel = modal.querySelector("[data-tct-due-schedule-enabled]");
    if(!enabledSel || enabledSel.value !== "1"){ return; }

    var rows = modal.querySelectorAll("[data-tct-interval-row]");
    var hasDaily = false;
    rows.forEach(function(r){
      var unitEl = r.querySelector("[data-tct-interval-unit]");
      var spanEl = r.querySelector("[data-tct-interval-span]");
      var targetEl = r.querySelector("[data-tct-interval-target]");
      if(!unitEl || !spanEl || !targetEl){ return; }
      var unit = unitEl.value;
      var span = parseInt(spanEl.value,10) || 0;
      var target = parseInt(targetEl.value,10) || 0;
      if(unit === "day" && span === 1 && target > 0){ hasDaily = true; }
    });
    if(hasDaily){
      warn.hidden = false;
      warn.textContent = "Heads up: this goal has a daily interval target. With a due schedule, you can only complete on due days, so a daily target will likely be impossible. Consider changing the interval to weekly/monthly (or disable the due schedule).";
    }
  }
  function applyToModal(modal, cfg){
    if(!modal){ return; }
    var enabledSel = modal.querySelector("[data-tct-due-schedule-enabled]");
    var startInput = modal.querySelector("[data-tct-due-schedule-start]");
    var typeSel = modal.querySelector("[data-tct-due-schedule-type]");
    var everyInput = modal.querySelector("[data-tct-due-schedule-every]");
    var domInput = modal.querySelector("[data-tct-due-schedule-dom]");
    if(!enabledSel || !startInput || !typeSel || !everyInput || !domInput){ return; }

    enabledSel.value = cfg.enabled ? "1" : "0";
    startInput.value = cfg.start_date || ymdToday();
    typeSel.value = cfg.type || "weekly";
    everyInput.value = String(cfg.every || 1);
    domInput.value = String(cfg.day_of_month || dayFromYmd(startInput.value) || 1);

    updateVisibility(modal);
    updateWarning(modal);
  }

  document.addEventListener("DOMContentLoaded", function(){
    // Keep weekly/monthly sub-fields and warning in sync as the user edits.
    document.querySelectorAll("[data-tct-goal-modal]").forEach(function(modal){
      var enabledSel = modal.querySelector("[data-tct-due-schedule-enabled]");
      var startInput = modal.querySelector("[data-tct-due-schedule-start]");
      var typeSel = modal.querySelector("[data-tct-due-schedule-type]");
      var everyInput = modal.querySelector("[data-tct-due-schedule-every]");
      var domInput = modal.querySelector("[data-tct-due-schedule-dom]");
      if(!enabledSel || !startInput || !typeSel || !everyInput || !domInput){ return; }

      function refresh(){ updateVisibility(modal); updateWarning(modal); }
      [enabledSel,startInput,typeSel,everyInput,domInput].forEach(function(el){
        el.addEventListener("change", refresh);
        el.addEventListener("input", refresh);
      });

      // Also refresh warning when interval rows change.
      modal.addEventListener("change", function(ev){
        if(ev && ev.target && ev.target.matches && ev.target.matches("[data-tct-interval-unit],[data-tct-interval-span],[data-tct-interval-target]")){
          refresh();
        }
      });
      modal.addEventListener("input", function(ev){
        if(ev && ev.target && ev.target.matches && ev.target.matches("[data-tct-interval-unit],[data-tct-interval-span],[data-tct-interval-target]")){
          refresh();
        }
      });
    });

    // When the goal modal opens, populate due-schedule fields from the goal payload.
    document.addEventListener("click", function(ev){
      var btn = ev.target && ev.target.closest ? ev.target.closest("[data-tct-open-goal-modal]") : null;
      if(!btn){ return; }
      var modal = getModalForButton(btn);
      if(!modal){ return; }

      var mode = btn.getAttribute("data-tct-open-goal-modal");
      var cfg = normalizeDueSchedule(null);

      if(mode === "edit"){
        var raw = btn.getAttribute("data-tct-goal");
        var payload = raw ? safeJsonParse(raw) : null;
        if(payload){
          var ds = payload.due_schedule;
          if(!ds && payload.due_schedule_json){ ds = safeJsonParse(payload.due_schedule_json); }
          cfg = normalizeDueSchedule(ds);
        }
        if(!cfg.start_date){ cfg.start_date = ymdToday(); }
      } else {
        // add mode
        cfg = normalizeDueSchedule({ enabled: 0, type: "weekly", start_date: ymdToday(), every: 1, day_of_month: dayFromYmd(ymdToday()) || 1 });
      }

      applyToModal(modal, cfg);
    });
  });
})();


/* ==========================================================
 * TCT Auto UI refresh (dashboard + ledger) when points change
 * ========================================================== */
(function () {
  'use strict';

  if (!window.tctDashboard || !tctDashboard.ajaxUrl) {
    return;
  }

  var cfg = window.tctDashboard;
  var POLL_INTERVAL_MS = 15000;

  var lastBalance = null;
  var pollTimer = null;

  var refreshTimer = null;
  var refreshInFlight = false;
  var refreshPending = false;

  // Sleep tracker rollover visibility / refresh
  var sleepRolloverTimer = null;

  function toInt(v) {
    var n = parseInt(String(v), 10);
    return isFinite(n) ? n : null;
  }

  function postForm(data) {
    var fd = new FormData();
    Object.keys(data || {}).forEach(function (k) {
      fd.append(k, data[k]);
    });

    return fetch(cfg.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: fd
    }).then(function (r) {
      // Most handlers return JSON; if not, throw.
      return r.json();
    });
  }

  function scheduleRefresh(reason) {
    if (refreshInFlight) {
      refreshPending = true;
      return;
    }
    if (refreshTimer) {
      return;
    }

    refreshTimer = setTimeout(function () {
      refreshTimer = null;
      doRefresh(reason || '');
    }, 350);
  }

  function doRefresh(reason) {
    if (refreshInFlight) {
      refreshPending = true;
      return;
    }
    refreshInFlight = true;
    refreshPending = false;

    var nonce = cfg.uiSnapshotNonce || cfg.pointsPollNonce || '';
    if (!nonce) {
      refreshInFlight = false;
      return;
    }

    postForm({
      action: 'tct_ui_snapshot',
      nonce: nonce,
      redirectHere: window.location.href
    })
      .then(function (resp) {
        if (!resp || !resp.success || !resp.data) {
          return;
        }
        var data = resp.data || {};

        var b = toInt(data.pointsBalance);
        if (null !== b) {
          lastBalance = b;
        }

        // Update nav pills (points / reward widget)
        var nav = document.querySelector('.tct-dashboard .tct-nav-pills');
        if (nav && typeof data.navPillsHtml === 'string') {
          nav.innerHTML = data.navPillsHtml;
        }

        var dashboardRoot = null;

        // Update dashboard panel (tiles, vitality, colors, ordering)
        var dashPanel = document.getElementById('tct-tab-panel-dashboard');
        if (dashPanel && typeof data.dashboardHtml === 'string') {
          dashPanel.innerHTML = data.dashboardHtml;
          dashboardRoot = (dashPanel.closest && dashPanel.closest('.tct-dashboard')) || dashboardRoot;
          try {
            applySleepTrackerRolloverVisibility();
            scheduleSleepRolloverRefresh();
          } catch (e) {
            // ignore
          }
        }

        // Update ledger panel
        var ledgerPanel = document.getElementById('tct-tab-panel-ledger');
        if (ledgerPanel && typeof data.ledgerHtml === 'string') {
          ledgerPanel.innerHTML = data.ledgerHtml;
          dashboardRoot = (ledgerPanel.closest && ledgerPanel.closest('.tct-dashboard')) || dashboardRoot;
        }

        if (dashboardRoot && typeof window.tctDashboardEnhance === 'function') {
          try {
            window.tctDashboardEnhance(dashboardRoot);
          } catch (e2) {
            // ignore
          }
        }
      })
      .catch(function () {
        // silent
      })
      .finally(function () {
        refreshInFlight = false;
        if (refreshPending) {
          refreshPending = false;
          scheduleRefresh('pending');
        }
      });
  }

  function pollBalance() {
    if (document.hidden) {
      return;
    }
    if (!cfg.pointsPollNonce) {
      return;
    }

    postForm({
      action: 'tct_points_poll',
      nonce: cfg.pointsPollNonce
    })
      .then(function (resp) {
        if (!resp || !resp.success || !resp.data) {
          return;
        }
        var b = toInt(resp.data.pointsBalance);
        if (null === b) {
          return;
        }

        if (null === lastBalance) {
          lastBalance = b;
          return;
        }

        if (b !== lastBalance) {
          lastBalance = b;
          scheduleRefresh('poll');
        }
      })
      .catch(function () {
        // silent
      });
  }

  function startPolling() {
    if (pollTimer) {
      clearInterval(pollTimer);
    }
    pollTimer = setInterval(pollBalance, POLL_INTERVAL_MS);

    // Kick once shortly after load
    setTimeout(pollBalance, 2000);
  }


  function pad2(n) {
    var x = parseInt(String(n), 10);
    if (!isFinite(x)) {
      return '00';
    }
    return x < 10 ? '0' + String(x) : String(x);
  }

  function formatYmdLocal(d) {
    try {
      return (
        String(d.getFullYear()) +
        '-' +
        pad2(d.getMonth() + 1) +
        '-' +
        pad2(d.getDate())
      );
    } catch (e) {
      return '';
    }
  }

  function addDaysLocal(d, days) {
    try {
      var x = new Date(d.getTime());
      x.setDate(x.getDate() + days);
      return x;
    } catch (e) {
      return d;
    }
  }

  function parseRolloverHHMM(val) {
    var s = String(val || '').trim();
    if (!s) {
      return null;
    }
    var m = s.match(/^(\d{1,2}):(\d{2})(?::\d{2})?$/);
    if (!m) {
      return null;
    }
    var h = parseInt(m[1], 10);
    var mi = parseInt(m[2], 10);
    if (!isFinite(h) || !isFinite(mi) || h < 0 || h > 23 || mi < 0 || mi > 59) {
      return null;
    }
    return { h: h, m: mi };
  }

  function isMobileDailyDefaultView() {
    try {
      var root = document.querySelector('.tct-mobile');
      if (!root) {
        return false;
      }

      if (root.classList) {
        if (
          root.classList.contains('tct-mobile-view-domain') ||
          root.classList.contains('tct-mobile-view-favorites')
        ) {
          return false;
        }
      }

      // Any active chip means we're not in the default daily list.
      var chips = root.querySelectorAll('[data-tct-mobile-chip]');
      for (var i = 0; i < chips.length; i++) {
        if (String(chips[i].getAttribute('aria-pressed') || '') === 'true') {
          return false;
        }
      }

      // Any search text means we're not in the default daily list.
      var inputs = root.querySelectorAll('[data-tct-mobile-search]');
      for (var j = 0; j < inputs.length; j++) {
        var v = '';
        try {
          v = String(inputs[j].value || '').trim();
        } catch (e) {
          v = '';
        }
        if (v) {
          return false;
        }
      }

      return true;
    } catch (e2) {
      return false;
    }
  }

  function refreshMobileDailyDefault(reason) {
    try {
      var root = document.querySelector('.tct-mobile');
      if (!root) {
        return;
      }
      if (!isMobileDailyDefaultView()) {
        return;
      }
      if (!window.tctMobile || !tctMobile.ajaxUrl) {
        return;
      }
      var nonce = tctMobile.searchNonce || '';
      if (!nonce) {
        return;
      }
      var results = root.querySelector('[data-tct-mobile-results]');
      if (!results) {
        return;
      }

      var params = new URLSearchParams();
      params.append('action', 'tct_mobile_daily_default');
      params.append('nonce', nonce);

      fetch(tctMobile.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: params.toString()
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (resp) {
          if (resp && resp.success && resp.data && typeof resp.data.html === 'string') {
            results.innerHTML = resp.data.html || '';
            try {
              if (typeof window.tctDashboardEnhance === 'function') {
                window.tctDashboardEnhance(
                  (results.closest && results.closest('.tct-dashboard')) || undefined
                );
              }
            } catch (e3) {
              // ignore
            }
          }
        })
        .catch(function () {
          // silent
        })
        .finally(function () {
          try {
            applySleepTrackerRolloverVisibility();
          } catch (e4) {
            // ignore
          }
        });
    } catch (e) {
      // ignore
    }
  }

  function applySleepTrackerRolloverVisibility() {
    try {
      var now = new Date();
      var nowSec = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

      // DASHBOARD (cockpit): only hide in the "Due Today" urgent column.
      var dashPanel = document.getElementById('tct-tab-panel-dashboard');
      if (dashPanel) {
        var dueTodayCol = dashPanel.querySelector(
          '.tct-urgent-column[data-tct-urgent-bucket="due_today"]'
        );
        var tiles = dashPanel.querySelectorAll(
          '[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"]'
        );

        for (var i = 0; i < tiles.length; i++) {
          var tile = tiles[i];
          if (!tile || !tile.getAttribute) {
            continue;
          }

          var inDailyColumn = !!(dueTodayCol && dueTodayCol.contains(tile));

          var rolloverStr = tile.getAttribute('data-tct-sleep-rollover') || '';
          var rollover = parseRolloverHHMM(rolloverStr);
          if (!rollover) {
            // If we previously hid it, restore.
            if (String(tile.getAttribute('data-tct-sleep-rollover-hidden') || '') === '1') {
              tile.style.display = '';
              tile.removeAttribute('data-tct-sleep-rollover-hidden');
            }
            continue;
          }
          var rolloverSec = rollover.h * 3600 + rollover.m * 60;

          var state = String(tile.getAttribute('data-tct-sleep-state') || '').trim();
          var isDefault = String(tile.getAttribute('data-tct-sleep-is-default') || '') === '1';
          var tileDate = String(tile.getAttribute('data-tct-sleep-date') || '').trim();

          var hiddenByUs =
            String(tile.getAttribute('data-tct-sleep-rollover-hidden') || '') === '1';

          // Default sleep date given the current time and rollover.
          var defaultDateObj = nowSec < rolloverSec ? addDaysLocal(now, -1) : now;
          var defaultDate = formatYmdLocal(defaultDateObj);

          // Hide completed sleep cycle after wake-time is entered, until the next rollover,
          // BUT ONLY in the daily "Due Today" column.
          var shouldHide = inDailyColumn && isDefault && state === 'C' && nowSec < rolloverSec;

          if (shouldHide) {
            if (!hiddenByUs) {
              tile.setAttribute('data-tct-sleep-rollover-hidden', '1');
              tile.style.display = 'none';
            }
            continue;
          }

          // If it was hidden by us, unhide unless we're still showing the previous cycle
          // in the daily column immediately after rollover (before snapshot refresh).
          if (hiddenByUs) {
            if (inDailyColumn) {
              if (tileDate && defaultDate && tileDate !== defaultDate) {
                continue;
              }
            }
            tile.style.display = '';
            tile.removeAttribute('data-tct-sleep-rollover-hidden');
          }
        }
      }

      // MOBILE: only hide in the default daily list (not domain browse, favorites, search, or chip filters).
      var mobileRoot = document.querySelector('.tct-mobile');
      if (mobileRoot) {
        var results = mobileRoot.querySelector('[data-tct-mobile-results]');
        if (results) {
          var inMobileDaily = isMobileDailyDefaultView();

          var mtiles = results.querySelectorAll(
            '[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"]'
          );

          for (var k = 0; k < mtiles.length; k++) {
            var mtile = mtiles[k];
            if (!mtile || !mtile.getAttribute) {
              continue;
            }

            var mrolloverStr = mtile.getAttribute('data-tct-sleep-rollover') || '';
            var mrollover = parseRolloverHHMM(mrolloverStr);
            if (!mrollover) {
              if (String(mtile.getAttribute('data-tct-sleep-rollover-hidden') || '') === '1') {
                mtile.style.display = '';
                mtile.removeAttribute('data-tct-sleep-rollover-hidden');
              }
              continue;
            }
            var mrolloverSec = mrollover.h * 3600 + mrollover.m * 60;

            var mstate = String(mtile.getAttribute('data-tct-sleep-state') || '').trim();
            var misDefault =
              String(mtile.getAttribute('data-tct-sleep-is-default') || '') === '1';
            var mtileDate = String(mtile.getAttribute('data-tct-sleep-date') || '').trim();

            var mhidden =
              String(mtile.getAttribute('data-tct-sleep-rollover-hidden') || '') === '1';

            var mdefaultDateObj = nowSec < mrolloverSec ? addDaysLocal(now, -1) : now;
            var mdefaultDate = formatYmdLocal(mdefaultDateObj);

            var mshouldHide =
              inMobileDaily && misDefault && mstate === 'C' && nowSec < mrolloverSec;

            if (mshouldHide) {
              if (!mhidden) {
                mtile.setAttribute('data-tct-sleep-rollover-hidden', '1');
                mtile.style.display = 'none';
              }
              continue;
            }

            if (mhidden) {
              // If we're not in the default daily list, always show it.
              if (inMobileDaily) {
                if (mtileDate && mdefaultDate && mtileDate !== mdefaultDate) {
                  continue;
                }
              }
              mtile.style.display = '';
              mtile.removeAttribute('data-tct-sleep-rollover-hidden');
            }
          }
        }
      }
    } catch (e) {
      // ignore
    }
  }

  function scheduleSleepRolloverRefresh() {
    try {
      // Prefer dashboard tile; fall back to mobile tile.
      var tile = null;
      var dashPanel = document.getElementById('tct-tab-panel-dashboard');
      if (dashPanel) {
        tile = dashPanel.querySelector(
          '[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"]'
        );
      }
      if (!tile) {
        var mobileRoot = document.querySelector('.tct-mobile');
        if (mobileRoot) {
          var results = mobileRoot.querySelector('[data-tct-mobile-results]');
          if (results) {
            tile = results.querySelector(
              '[data-tct-goal-tile="1"][data-tct-sleep-enabled="1"]'
            );
          }
        }
      }
      if (!tile) {
        if (sleepRolloverTimer) {
          clearTimeout(sleepRolloverTimer);
          sleepRolloverTimer = null;
        }
        return;
      }

      var rolloverStr = tile.getAttribute('data-tct-sleep-rollover') || '';
      var rollover = parseRolloverHHMM(rolloverStr);
      if (!rollover) {
        return;
      }

      var now = new Date();
      var target = new Date(
        now.getFullYear(),
        now.getMonth(),
        now.getDate(),
        rollover.h,
        rollover.m,
        0,
        0
      );
      if (now.getTime() >= target.getTime()) {
        target.setDate(target.getDate() + 1);
      }

      var ms = target.getTime() - now.getTime();
      if (!isFinite(ms) || ms < 250) {
        ms = 250;
      }

      if (sleepRolloverTimer) {
        clearTimeout(sleepRolloverTimer);
      }

      sleepRolloverTimer = setTimeout(function () {
        sleepRolloverTimer = null;

        // Dashboard snapshot refresh only if the dashboard panel exists.
        if (document.getElementById('tct-tab-panel-dashboard')) {
          scheduleRefresh('sleep_rollover');
        }

        // Mobile: refresh the default daily list at rollover.
        refreshMobileDailyDefault('sleep_rollover');

        // Re-arm after rollover (and any subsequent refresh).
        setTimeout(function () {
          scheduleSleepRolloverRefresh();
        }, 2000);
      }, ms);
    } catch (e) {
      // ignore
    }
  }

function initSleepTrackerRollover() {
    // Apply once after load.
    setTimeout(function () {
      applySleepTrackerRolloverVisibility();
      scheduleSleepRolloverRefresh();
    }, 50);
  }


  function getActionFromBody(body) {
    try {
      if (!body) {
        return '';
      }
      if (typeof FormData !== 'undefined' && body instanceof FormData) {
        return String(body.get('action') || '');
      }
      if (typeof URLSearchParams !== 'undefined' && body instanceof URLSearchParams) {
        return String(body.get('action') || '');
      }
      if (typeof body === 'string') {
        try {
          var p = new URLSearchParams(body);
          return String(p.get('action') || '');
        } catch (e) {
          return '';
        }
      }
    } catch (e2) {
      return '';
    }
    return '';
  }

  function shouldIgnoreAction(action) {
    var a = String(action || '').trim();
    if (!a) return true;
    return (
      a === 'tct_points_poll' ||
      a === 'tct_ui_snapshot'
    );
  }

  function installFetchInterceptor() {
    if (!window.fetch || window._tctPointsFetchInterceptorInstalled) {
      return;
    }
    window._tctPointsFetchInterceptorInstalled = true;

    var origFetch = window.fetch;

    window.fetch = function (input, init) {
      var body = init && init.body ? init.body : null;
      var action = getActionFromBody(body);

      // Call the original fetch.
      var p = origFetch.apply(this, arguments);

      // Best-effort: sniff point changes from JSON responses.
      p.then(function (resp) {
        try {
          if (!resp || !resp.clone || shouldIgnoreAction(action)) {
            return;
          }
          var clone = resp.clone();
          clone
            .json()
            .then(function (j) {
              if (!j || !j.success) {
                return;
              }

              // Sleep tracker: after sleep state changes, re-apply rollover visibility.
              if (action && String(action).indexOf('tct_sleep_') === 0) {
                setTimeout(function () {
                  try {
                    applySleepTrackerRolloverVisibility();
                    scheduleSleepRolloverRefresh();
                    if (String(action) === 'tct_sleep_save_waketime') {
                      try {
                        scheduleRefresh('sleep_waketime');
                      } catch (e4) {
                        // ignore
                      }
                    } else if (String(action) === 'tct_sleep_clear_cycle') {
                      try {
                        scheduleRefresh('sleep_clear_cycle');
                      } catch (e5) {
                        // ignore
                      }
                    }
                  } catch (e3) {
                    // ignore
                  }
                }, 0);
              }

              if (!j.data) {
                return;
              }
              if (typeof j.data.pointsBalance === 'undefined' || null === j.data.pointsBalance) {
                return;
              }
              var b = toInt(j.data.pointsBalance);
              if (null === b) {
                return;
              }
              if (null === lastBalance) {
                lastBalance = b;
                return;
              }
              if (b !== lastBalance) {
                lastBalance = b;
                scheduleRefresh('action:' + action);
              }
            })
            .catch(function () {
              // not JSON; ignore
            });
        } catch (e) {
          // ignore
        }
      }).catch(function () {
        // ignore
      });

      return p;
    };
  }

  installFetchInterceptor();
  startPolling();
  initSleepTrackerRollover();
})();
/* --- TCT Secret add-goal: click role title to open Add goal modal with role preselected (chunk 10) --- */
;(function(){
  'use strict';

  function matches(el, selector){
    if(!el || el.nodeType !== 1){ return false; }
    var fn = el.matches || el.msMatchesSelector || el.webkitMatchesSelector;
    return fn ? fn.call(el, selector) : false;
  }

  function closest(el, selector){
    while(el && el.nodeType === 1){
      if(matches(el, selector)){ return el; }
      el = el.parentElement;
    }
    return null;
  }

  function dispatchChange(el){
    if(!el){ return; }
    try {
      el.dispatchEvent(new Event('change', { bubbles: true }));
    } catch(e) {
      try {
        var evt = document.createEvent('Event');
        evt.initEvent('change', true, true);
        el.dispatchEvent(evt);
      } catch(err) {}
    }
  }

  function openAddGoalModal(dash){
    if(!dash){ return false; }

    // Prefer the existing Add-goal trigger (usually in the Goals tab header).
    var btn = dash.querySelector('[data-tct-open-goal-modal="add"]');
    if(btn && !btn.hasAttribute('disabled')){
      btn.click();
      return true;
    }

    // Fallback: create a temporary hidden trigger inside the dashboard so the
    // built-in delegated handler can still open the modal.
    var tmp = document.createElement('button');
    tmp.type = 'button';
    tmp.style.display = 'none';
    tmp.setAttribute('data-tct-open-goal-modal', 'add');
    dash.appendChild(tmp);
    tmp.click();
    dash.removeChild(tmp);
    return true;
  }

  function preselectRole(dash, roleId){
    var modal = dash ? dash.querySelector('[data-tct-goal-modal]') : null;
    if(!modal){
      modal = document.querySelector('[data-tct-goal-modal]');
    }
    if(!modal){ return false; }

    var sel = modal.querySelector('[data-tct-role-select]');
    if(!sel){ return false; }

    var val = String(roleId);
    // If the option isn't present (e.g., role was deleted), default to Unassigned (0).
    var ok = false;
    if(sel.options && sel.options.length){
      for(var i=0;i<sel.options.length;i++){
        if(String(sel.options[i].value) === val){
          ok = true;
          break;
        }
      }
    }
    if(!ok){ val = '0'; }

    sel.value = val;
    dispatchChange(sel);
    return true;
  }

  document.addEventListener('click', function(ev){
    var target = ev.target;
    if(target && target.nodeType !== 1){ target = target.parentElement; }
    if(!target){ return; }

    var title = target.closest ? target.closest('.tct-role-title') : closest(target, '.tct-role-title');
    if(!title){ return; }

    // Only real role columns include data-role-id (exclude "Complete" buckets, etc).
    var roleCol = title.closest ? title.closest('.tct-role-column[data-role-id]') : closest(title, '.tct-role-column[data-role-id]');
    if(!roleCol){ return; }

    var dash = roleCol.closest ? roleCol.closest('.tct-dashboard') : closest(roleCol, '.tct-dashboard');
    if(!dash){ return; }

    var rid = parseInt(roleCol.getAttribute('data-role-id') || '0', 10);
    if(!isFinite(rid) || rid < 0){ rid = 0; }

    // Open the normal Add Goal modal...
    openAddGoalModal(dash);

    // ...then set the role after the built-in opener resets the form.
    window.setTimeout(function(){
      preselectRole(dash, rid);
    }, 0);
  });
})();

(function () {
  "use strict";

  if (typeof window === "undefined") {
    return;
  }

  var TCT = window.TCT = window.TCT || {};

  if (typeof TCT.getCompositeFeatureConfig !== "function") {
    TCT.getCompositeFeatureConfig = function () {
      var cfg = {
        enabled: false,
        goalType: "composite_parent",
        scaffoldOnly: true
      };

      function mergeFrom(source) {
        if (!source || typeof source !== "object") {
          return;
        }

        if (source.composite && typeof source.composite === "object") {
          if (typeof source.composite.enabled !== "undefined") {
            cfg.enabled = !!source.composite.enabled;
          }
          if (typeof source.composite.goalType === "string" && source.composite.goalType) {
            cfg.goalType = source.composite.goalType;
          }
          if (typeof source.composite.scaffoldOnly !== "undefined") {
            cfg.scaffoldOnly = !!source.composite.scaffoldOnly;
          }
        }

        if (source.features && typeof source.features === "object" && typeof source.features.compositeGoals !== "undefined") {
          cfg.enabled = !!source.features.compositeGoals;
        }
      }

      mergeFrom(window.tctDashboard);
      mergeFrom(window.tctMobile);

      return cfg;
    };
  }

  if (typeof TCT.isCompositeGoalsEnabled !== "function") {
    TCT.isCompositeGoalsEnabled = function () {
      return !!TCT.getCompositeFeatureConfig().enabled;
    };
  }

  if (typeof TCT.getCompositeGoalType !== "function") {
    TCT.getCompositeGoalType = function () {
      return String(TCT.getCompositeFeatureConfig().goalType || "composite_parent");
    };
  }
})();
(function (window, document) {
  "use strict";

  if (!window || !document) {
    return;
  }

  var TCT = window.TCT = window.TCT || {};

  function matches(el, selector) {
    if (!el || el.nodeType !== 1) {
      return false;
    }
    var fn = el.matches || el.msMatchesSelector || el.webkitMatchesSelector;
    return fn ? fn.call(el, selector) : false;
  }

  function closest(el, selector) {
    while (el && el.nodeType === 1) {
      if (matches(el, selector)) {
        return el;
      }
      el = el.parentElement;
    }
    return null;
  }

  function parseJson(value) {
    if (!value || typeof value !== "string") {
      return null;
    }
    try {
      return JSON.parse(value);
    } catch (err) {
      return null;
    }
  }

  function toInt(value) {
    var parsed = parseInt(value, 10);
    return isFinite(parsed) ? parsed : 0;
  }

  function toNumber(value) {
    var parsed = parseFloat(value);
    return isFinite(parsed) ? parsed : 0;
  }

  function setHidden(el, hidden) {
    if (!el) {
      return;
    }
    if (hidden) {
      el.setAttribute("hidden", "hidden");
    } else {
      el.removeAttribute("hidden");
    }
  }

  function text(value) {
    return value == null ? "" : String(value);
  }

  function lower(value) {
    return text(value).toLowerCase();
  }

  function trim(value) {
    return text(value).replace(/^\s+|\s+$/g, "");
  }

  function isArray(value) {
    return Object.prototype.toString.call(value) === "[object Array]";
  }

  function uniqueIds(ids) {
    var out = [];
    var seen = {};
    var i;
    for (i = 0; i < ids.length; i += 1) {
      var id = toInt(ids[i]);
      if (id <= 0 || seen[id]) {
        continue;
      }
      seen[id] = true;
      out.push(id);
    }
    return out;
  }

  function countKeys(obj) {
    var count = 0;
    var key;
    if (!obj || typeof obj !== "object") {
      return 0;
    }
    for (key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key) && obj[key]) {
        count += 1;
      }
    }
    return count;
  }

  function formatNumber(value) {
    var num = toNumber(value);
    if (!isFinite(num)) {
      num = 0;
    }
    if (Math.abs(num - Math.round(num)) < 0.001) {
      return String(Math.round(num));
    }
    return String(Math.round(num * 10) / 10);
  }

  function createEl(tag, className, textValue) {
    var el = document.createElement(tag);
    if (className) {
      el.className = className;
    }
    if (typeof textValue !== "undefined" && textValue !== null) {
      el.textContent = String(textValue);
    }
    return el;
  }

  function createChip(label, className) {
    return createEl("span", "tct-composite-chip" + (className ? " " + className : ""), label);
  }

  function getFeatureState(modal) {
    if (!modal || modal.nodeType !== 1) {
      return null;
    }

    if (modal._tctCompositeGoalState) {
      return modal._tctCompositeGoalState;
    }

    var scaffold = modal.querySelector("[data-tct-composite-goal-scaffold]");
    var statsEl = modal.querySelector("[data-tct-goal-modal-stats]");
    var stats = parseJson(statsEl ? statsEl.getAttribute("data-tct-goal-modal-stats") : "") || {};
    var compositeStats = stats && stats.composite && typeof stats.composite === "object" ? stats.composite : {};
    var enabled = false;

    if (typeof compositeStats.enabled !== "undefined") {
      enabled = !!compositeStats.enabled;
    } else if (scaffold && scaffold.getAttribute("data-tct-composite-goals-enabled") === "1") {
      enabled = true;
    } else if (typeof TCT.isCompositeGoalsEnabled === "function") {
      enabled = !!TCT.isCompositeGoalsEnabled();
    }

    if (!enabled) {
      modal._tctCompositeGoalState = { enabled: false };
      return modal._tctCompositeGoalState;
    }

    var goalType = trim(compositeStats.goalType || (scaffold ? scaffold.getAttribute("data-tct-composite-goal-type") : "") || (typeof TCT.getCompositeGoalType === "function" ? TCT.getCompositeGoalType() : "composite_parent"));
    if (!goalType) {
      goalType = "composite_parent";
    }

    var rawCatalog = isArray(compositeStats.pickerGoals) ? compositeStats.pickerGoals : [];
    var catalog = [];
    var catalogById = {};
    var i;

    for (i = 0; i < rawCatalog.length; i += 1) {
      var item = normalizeCatalogItem(rawCatalog[i]);
      if (!item || item.goalId <= 0) {
        continue;
      }
      catalog.push(item);
      catalogById[item.goalId] = item;
    }

    var state = {
      enabled: true,
      modal: modal,
      form: modal.querySelector("[data-tct-goal-form]"),
      formModeInput: modal.querySelector("[data-tct-goal-form-mode]"),
      goalIdInput: modal.querySelector("[data-tct-goal-id]"),
      typeSelect: modal.querySelector("[data-tct-goal-type-select]"),
      roleSelect: modal.querySelector("[data-tct-role-select]"),
      typeHint: modal.querySelector("[data-tct-goal-type-hint]"),
      scoringRow: modal.querySelector("[data-tct-goal-scoring-row]"),
      intervalHeading: modal.querySelector("[data-tct-interval-heading]"),
      intervalHint: modal.querySelector("[data-tct-interval-hint]"),
      compositeRow: modal.querySelector("[data-tct-composite-config-row]"),
      searchInput: modal.querySelector("[data-tct-composite-search]"),
      resultsEl: modal.querySelector("[data-tct-composite-results]"),
      resultsEmptyEl: modal.querySelector("[data-tct-composite-results-empty]"),
      selectedEl: modal.querySelector("[data-tct-composite-selected-list]"),
      selectedEmptyEl: modal.querySelector("[data-tct-composite-selected-empty]"),
      validationEl: modal.querySelector("[data-tct-composite-validation]"),
      childIdsInput: modal.querySelector("[data-tct-composite-child-ids-json]"),
      configInput: modal.querySelector("[data-tct-composite-config-json]"),
      previewCountEl: modal.querySelector("[data-tct-composite-preview-count]"),
      previewPointsEl: modal.querySelector("[data-tct-composite-preview-points]"),
      previewBmaxEl: modal.querySelector("[data-tct-composite-preview-bmax]"),
      previewPmaxEl: modal.querySelector("[data-tct-composite-preview-pmax]"),
      previewPerfectEl: modal.querySelector("[data-tct-composite-preview-perfect]"),
      progressExponent: toNumber(compositeStats.progressExponent || 1.2),
      perfectBonusRate: toNumber(compositeStats.perfectBonusRate || 0.1),
      roleDomainMap: stats && stats.roleDomainMap && typeof stats.roleDomainMap === "object" ? stats.roleDomainMap : {},
      goalType: goalType,
      catalog: catalog,
      catalogById: catalogById,
      selectedIds: [],
      currentGoalId: 0,
      currentMode: "add",
      sortableReady: false,
      uiBound: false
    };

    modal._tctCompositeGoalState = state;
    bindStateEvents(state);
    ensureSortable(state);
    syncCompositeMode(state);
    return state;
  }

  function normalizeCatalogItem(raw) {
    if (!raw || typeof raw !== "object") {
      return null;
    }

    return {
      goalId: toInt(raw.goalId),
      goalName: trim(raw.goalName),
      goalType: trim(raw.goalType || "positive"),
      roleId: toInt(raw.roleId),
      roleName: trim(raw.roleName),
      domainId: toInt(raw.domainId),
      domainName: trim(raw.domainName),
      pointsPerCompletion: toInt(raw.pointsPerCompletion),
      intervalTarget: toInt(raw.intervalTarget),
      periodUnit: trim(raw.periodUnit || "week"),
      periodSpan: toInt(raw.periodSpan) || 1,
      intervalLabel: trim(raw.intervalLabel),
      bonusPoints: toNumber(raw.bonusPoints),
      penaltyPointsMagnitude: toNumber(raw.penaltyPointsMagnitude),
      aliases: isArray(raw.aliases) ? raw.aliases : [],
      isTracked: !!raw.isTracked,
      availabilityEnabled: !!raw.availabilityEnabled,
      availabilityPaused: !!raw.availabilityPaused,
      availabilityStateLabel: trim(raw.availabilityStateLabel),
      availabilityStateMeta: trim(raw.availabilityStateMeta),
      isCandidate: !!raw.isCandidate,
      isParent: !!raw.isParent,
      currentParentGoalId: toInt(raw.currentParentGoalId),
      currentParentGoalLabel: trim(raw.currentParentGoalLabel),
      currentSortOrder: toInt(raw.currentSortOrder)
    };
  }

  function getParentRoleId(state) {
    return state && state.roleSelect ? toInt(state.roleSelect.value) : 0;
  }

  function getParentDomainId(state) {
    if (!state || !state.roleDomainMap) {
      return 0;
    }
    var roleId = getParentRoleId(state);
    if (roleId <= 0) {
      return 0;
    }
    return toInt(state.roleDomainMap[String(roleId)] || state.roleDomainMap[roleId]);
  }

  function isCompositeMode(state) {
    return !!(state && state.enabled && state.typeSelect && trim(state.typeSelect.value) === state.goalType);
  }

  function hydrateStateFromOpenModal(state) {
    if (!state || !state.enabled) {
      return;
    }

    state.currentGoalId = state.goalIdInput ? toInt(state.goalIdInput.value) : 0;
    state.currentMode = state.formModeInput ? trim(state.formModeInput.value || "add") : "add";

    var nextSelected = [];
    var hiddenSelected = parseJson(state.childIdsInput ? state.childIdsInput.value : "");
    if (isArray(hiddenSelected) && hiddenSelected.length) {
      nextSelected = uniqueIds(hiddenSelected);
    } else if (state.currentGoalId > 0) {
      nextSelected = deriveSelectedIdsFromCatalog(state, state.currentGoalId);
    }

    state.selectedIds = uniqueIds(nextSelected);
    if (state.searchInput) {
      state.searchInput.value = "";
    }
    syncCompositeMode(state);
    renderCompositeState(state);
  }

  function deriveSelectedIdsFromCatalog(state, parentGoalId) {
    var list = [];
    var i;
    for (i = 0; i < state.catalog.length; i += 1) {
      var item = state.catalog[i];
      if (!item || item.currentParentGoalId !== parentGoalId) {
        continue;
      }
      list.push(item);
    }
    list.sort(function (a, b) {
      if (a.currentSortOrder !== b.currentSortOrder) {
        return a.currentSortOrder - b.currentSortOrder;
      }
      if (a.goalName < b.goalName) {
        return -1;
      }
      if (a.goalName > b.goalName) {
        return 1;
      }
      return a.goalId - b.goalId;
    });
    var out = [];
    for (i = 0; i < list.length; i += 1) {
      out.push(list[i].goalId);
    }
    return out;
  }

  function bindStateEvents(state) {
    if (!state || state.uiBound || !state.form) {
      return;
    }
    state.uiBound = true;

    if (state.typeSelect) {
      state.typeSelect.addEventListener("change", function () {
        syncCompositeMode(state);
        renderCompositeState(state);
      });
    }

    if (state.roleSelect) {
      state.roleSelect.addEventListener("change", function () {
        renderCompositeState(state);
      });
    }

    if (state.searchInput) {
      state.searchInput.addEventListener("input", function () {
        renderResults(state);
      });
    }

    state.modal.addEventListener("click", function (event) {
      var addBtn = closest(event.target, "[data-tct-composite-add]");
      if (addBtn && state.modal.contains(addBtn)) {
        event.preventDefault();
        addCompositeChild(state, toInt(addBtn.getAttribute("data-tct-composite-add")));
        return;
      }

      var removeBtn = closest(event.target, "[data-tct-composite-remove]");
      if (removeBtn && state.modal.contains(removeBtn)) {
        event.preventDefault();
        removeCompositeChild(state, toInt(removeBtn.getAttribute("data-tct-composite-remove")));
      }
    });

    state.form.addEventListener("submit", function (event) {
      if (!isCompositeMode(state)) {
        syncHiddenInputs(state);
        return;
      }

      var messages = getValidationMessages(state, true);
      syncHiddenInputs(state);
      if (messages.length) {
        event.preventDefault();
        renderValidation(state, messages);
        window.alert(messages[0]);
      }
    });
  }

  function addCompositeChild(state, goalId) {
    var roleId = getParentRoleId(state);
    var item = state.catalogById[goalId];
    if (!item || goalId <= 0) {
      return;
    }
    if (roleId <= 0) {
      renderValidation(state, ["Pick a role before adding composite child goals."]);
      return;
    }
    if (state.selectedIds.indexOf(goalId) !== -1) {
      return;
    }
    if (!isResultEligible(state, item)) {
      renderValidation(state, ["That goal cannot be added to this composite parent."]);
      return;
    }
    state.selectedIds.push(goalId);
    renderCompositeState(state);
  }

  function removeCompositeChild(state, goalId) {
    var next = [];
    var i;
    for (i = 0; i < state.selectedIds.length; i += 1) {
      if (toInt(state.selectedIds[i]) !== goalId) {
        next.push(toInt(state.selectedIds[i]));
      }
    }
    state.selectedIds = next;
    renderCompositeState(state);
  }

  function syncCompositeMode(state) {
    if (!state || !state.enabled) {
      return;
    }

    var compositeMode = isCompositeMode(state);
    setHidden(state.compositeRow, !compositeMode);
    setHidden(state.scoringRow, compositeMode);

    if (compositeMode) {
      if (state.typeHint) {
        state.typeHint.textContent = "Composite parent goals summarize child goals, settle their own parent bonus or penalty, and cannot be completed directly.";
      }
      if (state.intervalHeading) {
        state.intervalHeading.textContent = "Parent Tracking Period";
      }
      if (state.intervalHint) {
        state.intervalHint.textContent = "Set the parent settlement interval here. Child goals keep their own intervals and remain individually completable.";
      }
    }

    syncHiddenInputs(state);
  }

  function syncHiddenInputs(state) {
    if (!state) {
      return;
    }

    if (!isCompositeMode(state)) {
      if (state.childIdsInput) {
        state.childIdsInput.value = "";
      }
      if (state.configInput) {
        state.configInput.value = "";
      }
      return;
    }

    var ids = uniqueIds(state.selectedIds);
    state.selectedIds = ids;

    if (state.childIdsInput) {
      state.childIdsInput.value = JSON.stringify(ids);
    }
    if (state.configInput) {
      state.configInput.value = JSON.stringify({
        version: 1,
        enabled: true,
        summaryOnly: true,
        hideChildrenStandalone: true
      });
    }
  }

  function isResultEligible(state, item) {
    if (!item || item.goalId <= 0) {
      return false;
    }
    if (!item.isTracked || !item.isCandidate || item.isParent) {
      return false;
    }
    if (state.currentGoalId > 0 && item.goalId === state.currentGoalId) {
      return false;
    }
    if (item.currentParentGoalId > 0 && item.currentParentGoalId !== state.currentGoalId) {
      return false;
    }
    var roleId = getParentRoleId(state);
    var domainId = getParentDomainId(state);
    if (roleId > 0 && item.roleId !== roleId) {
      return false;
    }
    if (roleId > 0 && domainId > 0 && item.domainId !== domainId) {
      return false;
    }
    return true;
  }

  function getSelectedItemIssues(state, item) {
    var issues = [];
    if (!item) {
      issues.push("Missing goal");
      return issues;
    }
    if (!item.isTracked) {
      issues.push("Archived");
    }
    if (!item.isCandidate) {
      issues.push("Not eligible");
    }
    if (item.isParent) {
      issues.push("Already a parent");
    }
    if (state.currentGoalId > 0 && item.goalId === state.currentGoalId) {
      issues.push("Cannot include itself");
    }
    if (item.currentParentGoalId > 0 && item.currentParentGoalId !== state.currentGoalId) {
      issues.push("Attached to " + (item.currentParentGoalLabel || "another parent"));
    }

    var roleId = getParentRoleId(state);
    var domainId = getParentDomainId(state);
    if (roleId > 0 && item.roleId !== roleId) {
      issues.push("Role mismatch");
    }
    if (roleId > 0 && domainId > 0 && item.domainId !== domainId) {
      issues.push("Domain mismatch");
    }
    return issues;
  }

  function buildSearchHaystack(item) {
    var parts = [item.goalName, item.roleName, item.domainName, item.intervalLabel];
    var i;
    if (isArray(item.aliases)) {
      for (i = 0; i < item.aliases.length; i += 1) {
        parts.push(text(item.aliases[i]));
      }
    }
    return lower(parts.join(" "));
  }

  function renderCompositeState(state) {
    if (!state || !state.enabled) {
      return;
    }
    renderSelected(state);
    renderResults(state);
    renderPreview(state);
    renderValidation(state, getValidationMessages(state, false));
    syncHiddenInputs(state);
    ensureSortable(state);
  }

  function renderResults(state) {
    if (!state || !state.resultsEl) {
      return;
    }

    var compositeMode = isCompositeMode(state);
    state.resultsEl.innerHTML = "";
    if (!compositeMode) {
      setHidden(state.resultsEmptyEl, true);
      return;
    }

    var query = lower(trim(state.searchInput ? state.searchInput.value : ""));
    var roleId = getParentRoleId(state);
    var resultCount = 0;
    var i;

    for (i = 0; i < state.catalog.length; i += 1) {
      var item = state.catalog[i];
      if (!item) {
        continue;
      }
      if (!isResultEligible(state, item)) {
        continue;
      }
      if (query && buildSearchHaystack(item).indexOf(query) === -1) {
        continue;
      }

      var card = createEl("div", "tct-composite-result-card");
      var main = createEl("div", "tct-composite-result-main");
      var title = createEl("div", "tct-composite-result-title", item.goalName || ("Goal #" + item.goalId));
      var metaParts = [];
      if (item.roleName) {
        metaParts.push(item.roleName);
      }
      if (item.domainName) {
        metaParts.push(item.domainName);
      }
      if (item.intervalLabel) {
        metaParts.push(item.intervalLabel);
      }
      if (item.pointsPerCompletion > 0) {
        metaParts.push("Task " + formatNumber(item.pointsPerCompletion));
      }
      main.appendChild(title);
      main.appendChild(createEl("div", "tct-composite-result-meta", metaParts.join(" | ")));

      var chips = createEl("div", "tct-composite-chip-row");
      chips.appendChild(createChip("Bmax " + formatNumber(item.bonusPoints), "tct-composite-chip-neutral"));
      chips.appendChild(createChip("Pmax " + formatNumber(item.penaltyPointsMagnitude), "tct-composite-chip-neutral"));
      if (item.availabilityPaused) {
        chips.appendChild(createChip(item.availabilityStateLabel || "Paused", "tct-composite-chip-paused"));
      }
      if (state.selectedIds.indexOf(item.goalId) !== -1) {
        chips.appendChild(createChip("Added", "tct-composite-chip-selected"));
      } else if (roleId <= 0) {
        chips.appendChild(createChip("Pick role first", "tct-composite-chip-warning"));
      }
      main.appendChild(chips);
      card.appendChild(main);

      var button = createEl("button", "button button-secondary tct-composite-result-action");
      button.type = "button";
      button.setAttribute("data-tct-composite-add", String(item.goalId));
      if (state.selectedIds.indexOf(item.goalId) !== -1) {
        button.disabled = true;
        button.textContent = "Added";
      } else if (roleId <= 0) {
        button.disabled = true;
        button.textContent = "Pick role";
      } else {
        button.textContent = "Add";
      }
      card.appendChild(button);
      state.resultsEl.appendChild(card);
      resultCount += 1;
    }

    setHidden(state.resultsEmptyEl, resultCount > 0);
    if (!resultCount && state.resultsEmptyEl) {
      if (!roleId) {
        state.resultsEmptyEl.textContent = "Pick a role to narrow the child goal list, then search and add children.";
      } else {
        state.resultsEmptyEl.textContent = "No goals match the current filter.";
      }
    }
  }

  function renderSelected(state) {
    if (!state || !state.selectedEl) {
      return;
    }

    state.selectedEl.innerHTML = "";
    if (!isCompositeMode(state)) {
      setHidden(state.selectedEmptyEl, true);
      return;
    }

    var ids = uniqueIds(state.selectedIds);
    state.selectedIds = ids;
    var hasItems = false;
    var i;

    for (i = 0; i < ids.length; i += 1) {
      var goalId = ids[i];
      var item = state.catalogById[goalId] || {
        goalId: goalId,
        goalName: "Goal #" + goalId,
        intervalLabel: "",
        roleName: "",
        domainName: "",
        pointsPerCompletion: 0,
        bonusPoints: 0,
        penaltyPointsMagnitude: 0,
        availabilityPaused: false,
        availabilityStateLabel: "",
        currentParentGoalId: 0,
        currentParentGoalLabel: "",
        isTracked: false,
        isCandidate: false,
        isParent: false
      };

      var issues = getSelectedItemIssues(state, item);
      var card = createEl("div", "tct-composite-selected-card" + (issues.length ? " is-invalid" : ""));
      card.setAttribute("data-goal-id", String(goalId));

      var handle = createEl("span", "tct-drag-handle tct-composite-drag");
      handle.setAttribute("aria-hidden", "true");
      card.appendChild(handle);

      var main = createEl("div", "tct-composite-selected-main");
      main.appendChild(createEl("div", "tct-composite-selected-title", item.goalName || ("Goal #" + goalId)));

      var meta = [];
      if (item.roleName) {
        meta.push(item.roleName);
      }
      if (item.domainName) {
        meta.push(item.domainName);
      }
      if (item.intervalLabel) {
        meta.push(item.intervalLabel);
      }
      if (item.pointsPerCompletion > 0) {
        meta.push("Task " + formatNumber(item.pointsPerCompletion));
      }
      main.appendChild(createEl("div", "tct-composite-selected-meta", meta.join(" | ")));

      var chips = createEl("div", "tct-composite-chip-row");
      chips.appendChild(createChip("Bmax " + formatNumber(item.bonusPoints), "tct-composite-chip-neutral"));
      chips.appendChild(createChip("Pmax " + formatNumber(item.penaltyPointsMagnitude), "tct-composite-chip-neutral"));
      if (item.availabilityPaused) {
        chips.appendChild(createChip(item.availabilityStateLabel || "Paused", "tct-composite-chip-paused"));
      }
      var j;
      for (j = 0; j < issues.length; j += 1) {
        chips.appendChild(createChip(issues[j], "tct-composite-chip-warning"));
      }
      main.appendChild(chips);
      card.appendChild(main);

      var removeBtn = createEl("button", "button-link-delete tct-composite-remove");
      removeBtn.type = "button";
      removeBtn.textContent = "Remove";
      removeBtn.setAttribute("data-tct-composite-remove", String(goalId));
      card.appendChild(removeBtn);

      state.selectedEl.appendChild(card);
      hasItems = true;
    }

    setHidden(state.selectedEmptyEl, hasItems);
  }

  function renderPreview(state) {
    if (!state) {
      return;
    }

    var count = 0;
    var taskPoints = 0;
    var bmax = 0;
    var pmax = 0;
    var i;

    for (i = 0; i < state.selectedIds.length; i += 1) {
      var item = state.catalogById[state.selectedIds[i]];
      if (!item) {
        continue;
      }
      count += 1;
      taskPoints += toNumber(item.pointsPerCompletion);
      bmax += toNumber(item.bonusPoints);
      pmax += toNumber(item.penaltyPointsMagnitude);
    }

    if (state.previewCountEl) {
      state.previewCountEl.textContent = formatNumber(count);
    }
    if (state.previewPointsEl) {
      state.previewPointsEl.textContent = formatNumber(taskPoints);
    }
    if (state.previewBmaxEl) {
      state.previewBmaxEl.textContent = formatNumber(bmax);
    }
    if (state.previewPmaxEl) {
      state.previewPmaxEl.textContent = formatNumber(pmax);
    }
    if (state.previewPerfectEl) {
      state.previewPerfectEl.textContent = formatNumber(bmax * state.perfectBonusRate);
    }
  }

  function getValidationMessages(state, strict) {
    if (!state || !isCompositeMode(state)) {
      return [];
    }

    var messages = [];
    var roleId = getParentRoleId(state);
    var uniqueRoles = {};
    var uniqueDomains = {};
    var invalidCount = 0;
    var i;

    if (roleId <= 0) {
      messages.push("Pick a role for this composite parent before saving.");
    }
    if (!state.selectedIds.length) {
      messages.push("Choose at least one child goal for this composite parent.");
    }

    for (i = 0; i < state.selectedIds.length; i += 1) {
      var item = state.catalogById[state.selectedIds[i]];
      if (!item) {
        invalidCount += 1;
        continue;
      }
      if (item.roleId > 0) {
        uniqueRoles[item.roleId] = true;
      }
      if (item.domainId > 0) {
        uniqueDomains[item.domainId] = true;
      }
      if (getSelectedItemIssues(state, item).length) {
        invalidCount += 1;
      }
    }

    if (!roleId && countKeys(uniqueRoles) > 1) {
      messages.push("Selected children span multiple roles. Composite parents require one shared role and domain.");
    }
    if (!roleId && countKeys(uniqueDomains) > 1) {
      messages.push("Selected children span multiple domains. Pick a role before saving.");
    }
    if (invalidCount > 0) {
      messages.push("One or more selected child goals are no longer valid for this parent. Remove them or adjust the parent role.");
    }

    if (!strict && !messages.length && state.selectedIds.length) {
      messages.push("Child task points, Bmax, Pmax, and perfect bonus are previews only. Final parent settlement still depends on eligible child outcomes at settlement time.");
    }

    return messages;
  }

  function renderValidation(state, messages) {
    if (!state || !state.validationEl) {
      return;
    }
    if (!messages || !messages.length || !isCompositeMode(state)) {
      setHidden(state.validationEl, true);
      state.validationEl.innerHTML = "";
      state.validationEl.classList.remove("tct-goal-warning-error");
      return;
    }

    var strictMessages = getValidationMessages(state, true);
    var html = [];
    var i;
    for (i = 0; i < messages.length; i += 1) {
      html.push("<div>" + escapeHtml(messages[i]) + "</div>");
    }
    state.validationEl.innerHTML = html.join("");
    if (strictMessages.length) {
      state.validationEl.classList.add("tct-goal-warning-error");
    } else {
      state.validationEl.classList.remove("tct-goal-warning-error");
    }
    setHidden(state.validationEl, false);
  }

  function escapeHtml(value) {
    return text(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/\"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  function ensureSortable(state) {
    if (!state || state.sortableReady || !state.selectedEl || !window.jQuery || !window.jQuery.fn || !window.jQuery.fn.sortable) {
      return;
    }
    state.sortableReady = true;
    window.jQuery(state.selectedEl).sortable({
      items: "> .tct-composite-selected-card",
      handle: ".tct-composite-drag",
      placeholder: "tct-sort-placeholder",
      axis: "y",
      update: function () {
        var ids = [];
        window.jQuery(state.selectedEl).children("[data-goal-id]").each(function () {
          var id = toInt(this.getAttribute("data-goal-id"));
          if (id > 0) {
            ids.push(id);
          }
        });
        state.selectedIds = uniqueIds(ids);
        renderCompositeState(state);
      }
    });
  }

  function initAllCompositeGoalModals() {
    var modals = document.querySelectorAll("[data-tct-goal-modal]");
    var i;
    for (i = 0; i < modals.length; i += 1) {
      getFeatureState(modals[i]);
    }
  }

  function refreshAllOpenCompositeGoalModals() {
    var modals = document.querySelectorAll("[data-tct-goal-modal]");
    var i;
    for (i = 0; i < modals.length; i += 1) {
      var state = getFeatureState(modals[i]);
      if (!state || !state.enabled) {
        continue;
      }
      hydrateStateFromOpenModal(state);
    }
  }

  initAllCompositeGoalModals();

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAllCompositeGoalModals);
  }

  document.addEventListener("click", function (event) {
    var btn = closest(event.target, "[data-tct-open-goal-modal]");
    if (!btn) {
      return;
    }
    window.setTimeout(function () {
      refreshAllOpenCompositeGoalModals();
    }, 0);
  });
})(window, document);

!function(){"use strict";if("undefined"==typeof window||"undefined"==typeof document)return;window.TCT=window.TCT||{};if(window.TCT.__tctCompositeParentCompleteBound)return;window.TCT.__tctCompositeParentCompleteBound=!0;function t(){return window.tctDashboard&&window.tctDashboard.ajaxUrl?window.tctDashboard.ajaxUrl:window.tctMobile&&window.tctMobile.ajaxUrl?window.tctMobile.ajaxUrl:""}function n(){return window.tctDashboard&&window.tctDashboard.quickCompleteNonce?window.tctDashboard.quickCompleteNonce:""}function o(e){var n=t();if(!n)return Promise.reject(new Error("missing_ajax_url"));var o=new FormData;return Object.keys(e||{}).forEach(function(t){void 0!==e[t]&&null!==e[t]&&o.append(t,e[t])}),fetch(n,{method:"POST",credentials:"same-origin",headers:{"X-Requested-With":"XMLHttpRequest"},body:o}).then(function(e){return e.json()})}function i(t,n){var o=document.querySelectorAll('[data-tct-composite-parent-complete][data-goal-id="'+String(t)+'"]');Array.prototype.forEach.call(o,function(t){if(t)if(n){try{t.disabled=!0}catch(e){}t.hasAttribute("data-tct-orig-html")||t.setAttribute("data-tct-orig-html",t.innerHTML);var o=t.className||"";-1!==o.indexOf("tct-mobile-row-complete-btn")?t.innerHTML='<span class="tct-mobile-row-complete-text">...</span>':t.textContent="Completing..."}else{try{t.disabled=!1}catch(e){}var i=t.getAttribute("data-tct-orig-html");null!==i&&(t.innerHTML=i,t.removeAttribute("data-tct-orig-html"))}})}function r(t){if(window.TCT&&"function"==typeof window.TCT.getErrorMessage)return window.TCT.getErrorMessage(t,"Could not complete child goals.");return t&&t.data&&t.data.message?String(t.data.message):"Could not complete child goals."}function a(t){var e=window.TCT&&"function"==typeof window.TCT.normalizeResponse?window.TCT.normalizeResponse(t):{ok:!!(t&&t.success),data:t&&t.data?t.data:null};return!!(e&&e.ok)}function c(e){var t=e&&e.target&&e.target.closest?e.target.closest("[data-tct-composite-parent-complete]"):null;if(!t)return;e.preventDefault(),e.stopPropagation();var c=parseInt(t.getAttribute("data-goal-id")||"0",10);if(!isFinite(c)||c<=0)return void window.alert("Missing parent goal.");if(t.disabled)return;var d=n();if(!d)return void window.alert("Missing completion nonce.");var l=String(t.getAttribute("data-goal-name")||"").trim(),u=l?'Complete all child goals for "'+l+'"?':"Complete all child goals for this parent?";if(u+="\n\nChildren that are blocked right now will be skipped.",!window.confirm(u))return;i(c,!0),t.blur&&t.blur(),t.setAttribute("aria-busy","true"),o({action:"tct_composite_complete_parent",nonce:d,goal_id:c}).then(function(e){if(a(e)){var t=e&&e.data&&e.data.message?String(e.data.message):"Child goals completed.";t&&window.alert(t),window.location.reload()}else window.alert(r(e))}).catch(function(){window.alert("Network error completing child goals.")}).finally(function(){i(c,!1),t.removeAttribute("aria-busy")})}document.addEventListener("click",c,!0)}();
