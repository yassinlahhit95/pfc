/* ===========================================================================
   AulaPro Dashboard Shell — dashboard-shell.js
   Self-contained IIFE. Manages theme, sidebar collapse, mobile nav, GSAP anims.
   =========================================================================== */
(function () {
  "use strict";

  /* ── Icons ────────────────────────────────────────────────────────────── */
  var MOON_SVG = '<svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>';
  var SUN_SVG  = '<svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4V2M12 22v-2M5 5 3.6 3.6M20.4 20.4 19 19M4 12H2M22 12h-2M5 19l-1.4 1.4M20.4 3.6 19 5M12 7.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9z"/></svg>';

  /* ── State ────────────────────────────────────────────────────────────── */
  var STORE_KEY = "aulapro_tweaks_v1";
  var state = Object.assign({}, window.TWEAK_DEFAULTS || { accent: "#4F46E5", dark: false, animation: 7, density: "regular" });
  try { Object.assign(state, JSON.parse(localStorage.getItem(STORE_KEY) || "{}")); } catch (e) {}

  /* ── applyTheme — runs immediately (before DOM ready) to avoid flash ──── */
  function applyTheme() {
    var htmlEl = document.documentElement;
    htmlEl.setAttribute("data-theme", state.dark ? "dark" : "light");
    htmlEl.setAttribute("data-density", state.density || "regular");
    if (state.accent) htmlEl.style.setProperty("--accent", state.accent);
    if (typeof state.animation === "number") htmlEl.style.setProperty("--anim", String(state.animation / 10));
  }
  applyTheme();

  /* ── Helpers ──────────────────────────────────────────────────────────── */
  function getEl(sel, root) { return (root || document).querySelector(sel); }
  var gsapLib = window.gsap;
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /**
   * Resolves a URL relative to the app root, based on the 'vistas' segment in the path.
   * This makes paths robust even if the page depth changes.
   */
  function resolveAppPath(relPath) {
    if (!relPath) return "";
    // If it's already absolute (starts with / or http), return it
    if (relPath.startsWith('/') || relPath.startsWith('http')) return relPath;

    var parts = location.pathname.split('/');
    var vi = parts.indexOf('vistas');
    var base = vi > -1 ? parts.slice(0, vi).join('/') : '';
    
    // Clean up the relative part (remove leading ../)
    var cleanRel = relPath.replace(/^(\.\.\/)+/, '');
    return base + '/' + cleanRel;
  }
  window.AulaProResolveAppPath = resolveAppPath;

  function syncThemeBtn() {
    var knob = getEl("#theme .theme-knob");
    if (knob) knob.innerHTML = state.dark ? SUN_SVG : MOON_SVG;
  }

  function spinTheme() {
    var knob = getEl("#theme .theme-knob");
    if (!knob) return;
    if (gsapLib && !reduced) {
      gsapLib.fromTo(knob, { rotate: -90, opacity: 0 }, { rotate: 0, opacity: 1, duration: 0.45, ease: "back.out(2)" });
    }
  }

  function saveState() {
    try { localStorage.setItem(STORE_KEY, JSON.stringify(state)); } catch (e) {}
  }

  function failsafe(sel) {
    setTimeout(function () {
      document.querySelectorAll(sel).forEach(function (e) {
        if (gsapLib && parseFloat(getComputedStyle(e).opacity) < 0.99) {
          gsapLib.set(e, { clearProps: "transform,opacity" });
        }
      });
    }, 1500);
  }

  function animateIntro() {
    var factor = (typeof state.animation === "number" ? state.animation : 7) / 10;
    if (!gsapLib || reduced || factor === 0) return;
    gsapLib.from(".sidebar", { x: -40, opacity: 0, duration: 0.6, ease: "power3.out", clearProps: "transform,opacity" });
    gsapLib.from(".topbar > *", { y: -16, opacity: 0, duration: 0.5, stagger: 0.05, ease: "power2.out", delay: 0.1, clearProps: "transform,opacity" });
    if (document.querySelector(".hero-text")) {
      gsapLib.from(".hero-text > *", { y: 18, opacity: 0, duration: 0.55, stagger: 0.07, ease: "power3.out", delay: 0.15, clearProps: "transform,opacity" });
    }
    if (document.querySelector(".stat")) {
      gsapLib.from(".stat", { y: 18, opacity: 0, scale: 0.95, duration: 0.5, stagger: 0.08, ease: "back.out(1.5)", delay: 0.25, clearProps: "transform,opacity" });
    }
    failsafe(".sidebar, .topbar > *" + (document.querySelector(".hero-text") ? ", .hero-text > *" : "") + (document.querySelector(".stat") ? ", .stat" : ""));
  }

  function animateTiles() {
    var tiles = document.querySelectorAll(".tile");
    if (!tiles.length) return;
    var factor = (typeof state.animation === "number" ? state.animation : 7) / 10;
    if (!gsapLib || reduced || factor === 0) {
      tiles.forEach(function (t) { t.style.opacity = 1; t.style.transform = ""; });
      return;
    }
    gsapLib.killTweensOf(tiles);
    gsapLib.fromTo(tiles,
      { opacity: 0, y: 24 + 30 * factor, scale: 0.96 },
      { opacity: 1, y: 0, scale: 1, duration: 0.5 + 0.35 * factor, ease: "power3.out",
        stagger: { each: 0.03 + 0.05 * factor, from: "start" },
        clearProps: "transform,opacity" });
    failsafe(".tile");
  }

  /* ── Search ──────────────────────────────────────────────────────────── */
  function initSearch() {
    var input = getEl("#sys-search");
    var list = getEl("#search-results");
    var wrapper = getEl("#search-wrapper");
    var trigger = getEl(".mobile-search-trigger");
    var backdrop = getEl("#search-backdrop");
    var closeBtn = getEl("#search-close");

    if (!input || !list) return;

    // Do NOT touch autocomplete here — the HTML already sets the hardened
    // value (autocomplete="one-time-code", per CLAUDE.md's search-input
    // convention). This used to reset it to plain "off" on every page load,
    // silently undoing that fix and letting Chrome's own password-manager
    // UI (key icon / saved-password suggestions) show up over the search
    // results again — plain "off" is the exact value already proven not to
    // work reliably on this class of input.

    var rawUrl = input.dataset.url;
    if (!rawUrl) return;
    var url = resolveAppPath(rawUrl);

    var timer;
    var TYPE_LABELS = { reto: "Reto", anuncio: "Aviso", mensaje: "Mensaje", estudiante: "Alumno", profesor: "Profesor", modulo: "Módulo", "modulo-asignar": "Módulo", ciclo: "Ciclo", evento: "Evento", director: "Director", secretaria: "Secretaría", archivo: "Archivo", pago: "Pago", entrega: "Entrega", tarea: "Tarea", tutor: "Tutor/Familia", tfg: "TFG", calificacion: "Calificación" };

    function escHtml(s) {
      return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;")
                      .replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }

    function clearResults() {
      list.innerHTML = "";
      list.setAttribute("hidden", "");
    }

    function renderResults(data) {
      list.innerHTML = "";
      if (!data || !data.length) {
        list.innerHTML = '<li class="search-no-results">Sin resultados</li>';
        list.removeAttribute("hidden");
        return;
      }
      data.forEach(function (r) {
        var li = document.createElement("li");
        li.innerHTML = '<a href="' + escHtml(resolveAppPath(r.url)) + '" class="search-result-item">'
          + '<span class="search-result-tag">' + escHtml(TYPE_LABELS[r.type] || r.type) + '</span>'
          + '<span class="search-result-label">' + escHtml(r.label) + '</span>'
          + "</a>";
        list.appendChild(li);
      });
      list.removeAttribute("hidden");
    }

    function openMobileSearch() {
      document.body.classList.add("search-open");
      document.body.style.overflow = "hidden"; // Prevent background scrolling
      setTimeout(function() { input.focus(); }, 50);
    }

    function closeMobileSearch() {
      document.body.classList.remove("search-open");
      document.body.style.overflow = "";
      clearResults();
      input.value = "";
    }

    if (trigger) trigger.addEventListener("click", openMobileSearch);
    if (closeBtn) closeBtn.addEventListener("click", closeMobileSearch);
    if (backdrop) backdrop.addEventListener("click", closeMobileSearch);

    // El icono de la lupa (y el resto de la barra fuera del propio <input>)
    // dependía solo del comportamiento nativo de <label> para enfocar el
    // input al hacer click — con un <button> (search-close) y un <kbd>
    // también dentro de la misma etiqueta, ese enfoque implícito no es fiable
    // en todos los casos. Se fuerza el foco explícitamente para que el icono
    // sea un punto de click real y no una zona muerta junto al input.
    var searchBar = getEl(".search-modal-bar");
    if (searchBar) {
      searchBar.addEventListener("click", function (e) {
        if (e.target === input || e.target.closest("#search-close")) return;
        input.focus();
      });
    }

    input.addEventListener("focus", function () {
      var q = input.value.trim();
      if (q.length >= 2 && list.innerHTML !== "") {
        list.removeAttribute("hidden");
      }
    });

    input.addEventListener("input", function () {
      clearTimeout(timer);
      var q = input.value.trim();
      if (q.length < 2) { clearResults(); return; }
      timer = setTimeout(function () {
        fetch(url + "?q=" + encodeURIComponent(q))
          .then(function (r) { return r.json(); })
          .then(renderResults)
          .catch(clearResults);
      }, 280);
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        if (document.body.classList.contains("search-open")) {
          closeMobileSearch();
        } else {
          clearResults();
          input.blur();
        }
      }
    });

    // Close desktop search dropdown on click outside
    document.addEventListener("click", function (e) {
      if (!document.body.classList.contains("search-open") && wrapper && !e.target.closest("#search-wrapper")) {
        clearResults();
      }
    });
  }

  /* ── Breadcrumb "back" button — used when a crumb has no known parent URL ── */
  function initBreadcrumb() {
    document.querySelectorAll("[data-breadcrumb-back]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = resolveAppPath(btn.dataset.breadcrumbBack);
        }
      });
    });
  }

  /* ── Notifications ─────────────────────────────────────────────────────── */
  function initNotifications() {
    var btn   = getEl("#notif-btn");
    var panel = getEl("#notif-panel");
    var dot   = getEl("#notif-dot");
    if (!btn || !panel) return;

    var SEEN_KEY = "ap_seen_pagos";
    var seenIds;
    try { seenIds = JSON.parse(localStorage.getItem(SEEN_KEY) || "[]"); } catch (e) { seenIds = []; }

    var pagoItems = document.querySelectorAll(".notif-item--pago");

    /* Hide "Nuevo" badge on already-seen pagos */
    pagoItems.forEach(function (item) {
      var pid = parseInt(item.dataset.pid, 10);
      if (seenIds.indexOf(pid) !== -1) {
        var badge = item.querySelector(".notif-pago-new");
        if (badge) badge.hidden = true;
      }
    });

    /* Show dot if any pago is unseen */
    var hasNewPago = [].some.call(pagoItems, function (item) {
      return seenIds.indexOf(parseInt(item.dataset.pid, 10)) === -1;
    });
    if (hasNewPago && dot) dot.removeAttribute("hidden");

    /* Toggle panel */
    btn.addEventListener("click", function (e) {
      e.stopPropagation();
      var opening = panel.hasAttribute("hidden");
      panel.toggleAttribute("hidden");
      if (opening) {
        /* Mark all visible pagos as seen */
        pagoItems.forEach(function (item) {
          var pid = parseInt(item.dataset.pid, 10);
          if (seenIds.indexOf(pid) === -1) seenIds.push(pid);
          var badge = item.querySelector(".notif-pago-new");
          if (badge) badge.hidden = true;
        });
        seenIds = seenIds.slice(-50);
        try { localStorage.setItem(SEEN_KEY, JSON.stringify(seenIds)); } catch (e) {}

        /* Notificaciones genéricas (modelos/notificaciones.php) + notificaciones
           de aula (modelos/aula.php: aula_notificaciones — tarea nueva, sesión
           nueva, archivo subido, entrega enviada/corregida): a diferencia de
           los mensajes (que se marcan leídos al navegar al hilo), el panel ya
           muestra su contenido completo, así que verlas aquí cuenta como
           "vistas" — se marcan leídas de verdad en servidor (no solo con un
           localStorage como los pagos de arriba) para que no reaparezcan en
           la próxima carga de página. */
        var parseIdList = function (raw) {
          return raw ? raw.split(",").map(function (s) { return parseInt(s, 10); }).filter(Boolean) : [];
        };
        var notifIds = parseIdList(panel.dataset.notifIds || "");
        var aulaNotifIds = parseIdList(panel.dataset.aulaNotifIds || "");
        if (notifIds.length || aulaNotifIds.length) {
          panel.querySelectorAll(".notif-item .notif-badge-new").forEach(function (b) { b.hidden = true; });
          panel.dataset.notifIds = "";
          panel.dataset.aulaNotifIds = "";
          var csrfEl = getEl('[name="modal_csrf"]');
          var csrf = csrfEl ? csrfEl.value : "";
          var body = new URLSearchParams();
          notifIds.forEach(function (id) { body.append("ids[]", id); });
          aulaNotifIds.forEach(function (id) { body.append("aula_ids[]", id); });
          body.append("csrf_token", csrf);
          fetch(resolveAppPath("controladores/comunes/notificaciones_marcar_leidas.php"), {
            method: "POST", credentials: "same-origin", body: body
          }).catch(function () {});
          if (dot) {
            var restante = Math.max(0, parseInt(dot.dataset.msgs || "0", 10) - notifIds.length - aulaNotifIds.length);
            dot.dataset.msgs = restante;
          }
        }

        /* Hide dot only if there are no current unread messages */
        if (dot && parseInt(dot.dataset.msgs || "0", 10) <= 0) dot.setAttribute("hidden", "");
      }
    });

    /* Close on outside click */
    document.addEventListener("click", function () {
      panel.setAttribute("hidden", "");
    });
    panel.addEventListener("click", function (e) { e.stopPropagation(); });
  }

  /* ── Collapsible nav categories (+/-) ─────────────────────────────────── */
  function initNavSections() {
    var nav = getEl("#sidebar-nav");
    if (!nav) return;
    var KEY = "aulapro_nav_sections";
    var saved = {};
    try { saved = JSON.parse(localStorage.getItem(KEY) || "{}"); } catch (e) {}

    var titles = nav.querySelectorAll(".nav-section-title");
    Array.prototype.forEach.call(titles, function (title) {
      var name = (title.textContent || "").trim();

      // Collect the nav items that belong to this section (until the next title).
      var items = [];
      var el = title.nextElementSibling;
      while (el && !el.classList.contains("nav-section-title")) {
        if (el.classList.contains("nav-item")) items.push(el);
        el = el.nextElementSibling;
      }
      if (!items.length) return;

      title.classList.add("nav-section-toggle");
      title.setAttribute("role", "button");
      title.setAttribute("tabindex", "0");

      function apply(collapsed) {
        title.classList.toggle("collapsed", collapsed);
        items.forEach(function (it) { it.classList.toggle("nav-collapsed", collapsed); });
      }

      // Never start collapsed if the active page lives inside this section.
      var hasActive = items.some(function (it) { return it.classList.contains("active"); });
      var collapsed = !hasActive && saved[name] === true;
      apply(collapsed);

      function toggle() {
        collapsed = !collapsed;
        apply(collapsed);
        saved[name] = collapsed;
        try { localStorage.setItem(KEY, JSON.stringify(saved)); } catch (e) {}
      }
      title.addEventListener("click", toggle);
      title.addEventListener("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") { e.preventDefault(); toggle(); }
      });
    });
  }

  /* ── Collapsible nav sub-groups (e.g. "Configuración" > Página Web, Blog…) ── */
  function initNavGroups() {
    var nav = getEl("#sidebar-nav");
    if (!nav) return;
    var KEY = "aulapro_nav_groups";
    var saved = {};
    try { saved = JSON.parse(localStorage.getItem(KEY) || "{}"); } catch (e) {}

    var toggles = nav.querySelectorAll(".nav-group-toggle");
    Array.prototype.forEach.call(toggles, function (toggle) {
      var group = toggle.closest(".nav-group");
      var submenu = group ? group.querySelector(".nav-submenu") : null;
      if (!submenu) return;
      var name = (toggle.textContent || "").trim();
      var items = Array.prototype.slice.call(submenu.querySelectorAll(".nav-item"));
      if (!items.length) return;

      function apply(collapsed) {
        toggle.classList.toggle("collapsed", collapsed);
        toggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
        items.forEach(function (it) { it.classList.toggle("nav-collapsed", collapsed); });
      }

      // Never start collapsed if the active page lives inside this group.
      var hasActive = items.some(function (it) { return it.classList.contains("active"); });
      var collapsed = !hasActive && saved[name] === true;
      apply(collapsed);

      toggle.addEventListener("click", function () {
        collapsed = !collapsed;
        apply(collapsed);
        saved[name] = collapsed;
        try { localStorage.setItem(KEY, JSON.stringify(saved)); } catch (e) {}
      });
    });
  }

  /* ── Sidebar Scroll Persistence ───────────────────────────────────────── */
  function initSidebarScroll() {
    var nav = getEl("#sidebar-nav");
    if (!nav) return;

    var SCROLL_KEY = "aulapro_sidebar_scroll";
    
    // Restore scroll position
    var savedScroll = localStorage.getItem(SCROLL_KEY);
    if (savedScroll !== null) {
      nav.scrollTop = parseInt(savedScroll, 10);
    }

    // Scroll active item into view if not visible
    var activeItem = nav.querySelector(".nav-item.active");
    if (activeItem) {
      var rect = activeItem.getBoundingClientRect();
      var navRect = nav.getBoundingClientRect();
      if (rect.top < navRect.top || rect.bottom > navRect.bottom) {
        activeItem.scrollIntoView({ block: "center", behavior: "smooth" });
      }
    }

    // Save scroll position on scroll (debounced)
    var scrollTimer;
    nav.addEventListener("scroll", function() {
      clearTimeout(scrollTimer);
      scrollTimer = setTimeout(function() {
        localStorage.setItem(SCROLL_KEY, nav.scrollTop);
      }, 150);
    });
  }

  /* ── Boot on DOMContentLoaded ─────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", function () {
    /* Sync theme icon */
    syncThemeBtn();

    /* Sidebar Scroll Persistence */
    initSidebarScroll();

    /* Collapse button */
    var collapseBtn = getEl("#collapse");
    var sidebar = getEl(".sidebar");
    if (collapseBtn && sidebar) {
      if (state.sidebarCollapsed) sidebar.classList.add("collapsed");
      collapseBtn.addEventListener("click", function () {
        var nowCollapsed = sidebar.classList.toggle("collapsed");
        state.sidebarCollapsed = nowCollapsed;
        saveState();
      });
    }

    /* Mobile menu button */
    var app = getEl("#app");
    var menuBtn = getEl("#menu");
    var mobileCloseBtn = getEl("#mobile-close");
    
    function closeMobileNav() {
      if (app) app.classList.remove("nav-open");
    }

    if (menuBtn && app) {
      menuBtn.addEventListener("click", function () {
        app.classList.toggle("nav-open");
      });
    }

    if (mobileCloseBtn) {
      mobileCloseBtn.addEventListener("click", closeMobileNav);
    }

    /* Scrim click closes nav */
    var scrim = getEl(".scrim");
    if (scrim) {
      scrim.addEventListener("click", closeMobileNav);
    }

    /* Escape key / Android back closes nav */
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && app && app.classList.contains("nav-open")) {
        closeMobileNav();
      }
    });

    /* Theme toggle */
    var themeBtn = getEl("#theme");
    if (themeBtn) {
      themeBtn.addEventListener("click", function () {
        state.dark = !state.dark;
        saveState();
        applyTheme();
        syncThemeBtn();
        spinTheme();
      });
    }

    /* ⌘K / Ctrl+K focuses search */
    document.addEventListener("keydown", function (e) {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        var search = getEl("#sys-search");
        if (search) search.focus();
      }
    });

    /* Collapsible nav categories (+/-) */
    initNavSections();
    initNavGroups();

    /* Search */
    initSearch();

    /* Breadcrumb back-button fallback */
    initBreadcrumb();

    /* Notifications */
    initNotifications();

    /* Poll unread count — keeps dot in sync without a page reload.
       Uses recursive setTimeout with exponential backoff so network errors
       (ERR_NETWORK_CHANGED, etc.) don't flood the console. */
    (function pollUnread() {
      var dot = getEl("#notif-dot");
      if (!dot) return;
      var POLL_URL = resolveAppPath('controladores/comunes/contar_no_leidos.php');
      var INTERVAL  = 30000;   // normal interval ms
      var MAX_DELAY = 300000;  // cap backoff at 5 min
      var errors    = 0;
      var timer     = null;
      var paused    = false;

      function schedule(delay) {
        clearTimeout(timer);
        timer = setTimeout(poll, delay);
      }

      function poll() {
        if (!navigator.onLine) { schedule(INTERVAL); return; }
        fetch(POLL_URL, { credentials: 'same-origin' })
          .then(function(r) {
            if (r.status === 401 || r.status === 403) {
              clearTimeout(timer); // session expired — stop polling
              return null;
            }
            if (!r.ok) throw new Error(r.status);
            return r.json();
          })
          .then(function(data) {
            if (!data) return;
            errors = 0;
            var unreadCount   = parseInt(data.count || 0, 10);
            var previousCount = parseInt(dot.dataset.msgs || '0', 10);
            if (unreadCount > previousCount) {
              dot.dataset.msgs = unreadCount;
              dot.removeAttribute('hidden');
            }

            var chatUnreadCount = parseInt(data.chat_count || 0, 10);
            var cwBadge = document.getElementById('cw-fab-badge');
            if (cwBadge) {
              cwBadge.textContent = chatUnreadCount;
              if (chatUnreadCount > 0) {
                cwBadge.removeAttribute('hidden');
              } else {
                cwBadge.setAttribute('hidden', '');
              }
            }

            schedule(INTERVAL);
          })
          .catch(function() {
            errors++;
            // Exponential backoff: 30s, 60s, 120s … capped at MAX_DELAY
            var delay = Math.min(INTERVAL * Math.pow(2, errors - 1), MAX_DELAY);
            schedule(delay);
          });
      }

      // Pause while offline, resume immediately when back online
      window.addEventListener('offline', function() { paused = true; clearTimeout(timer); });
      window.addEventListener('online',  function() {
        paused = false;
        errors = 0;
        poll();
      });

      schedule(INTERVAL);
    })();

    /* Tile click elastic feedback */
    document.addEventListener("click", function (e) {
      var tile = e.target.closest(".tile");
      if (tile && gsapLib && !reduced) {
        gsapLib.fromTo(tile, { scale: 0.97 }, { scale: 1, duration: 0.4, ease: "elastic.out(1,0.5)", clearProps: "transform" });
      }
    });

    /* Entrance animations */
    animateIntro();
    animateTiles();
  });

  // Expose helper to global scope if needed
  window.resolveAppPath = resolveAppPath;

  /* ── Auto-logout after inactivity ──────────────────────────────────────── */
  (function initAutoLogout() {
    var TOTAL_MS   = 45 * 60 * 1000; // 45 min total
    var WARN_MS    = 5  * 60 * 1000; // warn with 5 min remaining
    var warnTimer  = null;
    var outTimer   = null;
    var tickTimer  = null;
    var warnEl     = null;
    var warnDeadline = 0;

    function clearWarn() {
      clearInterval(tickTimer);
      if (warnEl) { warnEl.remove(); warnEl = null; }
    }

    function reset() {
      clearTimeout(warnTimer);
      clearTimeout(outTimer);
      clearWarn();
      warnTimer = setTimeout(showWarn, TOTAL_MS - WARN_MS);
      outTimer  = setTimeout(doLogout, TOTAL_MS);
    }

    function showWarn() {
      warnDeadline = Date.now() + WARN_MS;
      warnEl = document.createElement('div');
      warnEl.id = 'autologout-bar';
      warnEl.setAttribute('style',
        'position:fixed;bottom:max(80px,calc(76px + env(safe-area-inset-bottom,0px)));' +
        'left:50%;transform:translateX(-50%);' +
        'background:var(--surface,#fff);border:1.5px solid #f59e0b;' +
        'border-radius:16px;padding:14px 20px;z-index:99998;' +
        'box-shadow:0 8px 32px rgba(0,0,0,.18);' +
        'min-width:280px;max-width:min(90vw,400px);' +
        'text-align:center;font-family:var(--font-ui,sans-serif);'
      );
      warnEl.innerHTML =
        '<div style="font-size:.85rem;font-weight:700;color:#92400e;margin-bottom:6px;">' +
          '<i class="fas fa-clock"></i> Sesión a punto de expirar</div>' +
        '<div style="font-size:.8rem;color:var(--dim,#555);">' +
          'La sesión se cerrará en <strong id="al-countdown">5:00</strong>.' +
        '</div>' +
        '<button id="al-keep" style="margin-top:10px;background:var(--accent,#4F46E5);color:#fff;' +
          'border:none;border-radius:10px;padding:7px 20px;font-weight:700;cursor:pointer;font-size:.83rem;">' +
          'Continuar sesión</button>';
      document.body.appendChild(warnEl);

      document.getElementById('al-keep').addEventListener('click', reset);

      tickTimer = setInterval(function() {
        var left = Math.max(0, warnDeadline - Date.now());
        var countdownEl = document.getElementById('al-countdown');
        if (countdownEl) {
          var minutesLeft = Math.floor(left / 60000);
          var secondsLeft = Math.floor((left % 60000) / 1000);
          countdownEl.textContent = minutesLeft + ':' + (secondsLeft < 10 ? '0' + secondsLeft : secondsLeft);
        }
        if (left === 0) clearInterval(tickTimer);
      }, 1000);
    }

    function doLogout() {
      clearWarn();
      window.location.href = resolveAppPath('controladores/logout.php');
    }

    var actEvents = ['mousemove', 'keydown', 'mousedown', 'touchstart', 'scroll', 'click'];
    actEvents.forEach(function(ev) {
      document.addEventListener(ev, reset, { passive: true, capture: true });
    });

    reset();
  })();

})();
