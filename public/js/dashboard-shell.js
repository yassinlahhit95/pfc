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
    var r = document.documentElement;
    r.setAttribute("data-theme", state.dark ? "dark" : "light");
    r.setAttribute("data-density", state.density || "regular");
    if (state.accent) r.style.setProperty("--accent", state.accent);
    if (typeof state.animation === "number") r.style.setProperty("--anim", String(state.animation / 10));
  }
  applyTheme();

  /* ── Helpers ──────────────────────────────────────────────────────────── */
  function qs(sel, root) { return (root || document).querySelector(sel); }
  var G = window.gsap;
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

  function syncThemeBtn() {
    var knob = qs("#theme .theme-knob");
    if (knob) knob.innerHTML = state.dark ? SUN_SVG : MOON_SVG;
  }

  function spinTheme() {
    var knob = qs("#theme .theme-knob");
    if (!knob) return;
    if (G && !reduced) {
      G.fromTo(knob, { rotate: -90, opacity: 0 }, { rotate: 0, opacity: 1, duration: 0.45, ease: "back.out(2)" });
    }
  }

  function saveState() {
    try { localStorage.setItem(STORE_KEY, JSON.stringify(state)); } catch (e) {}
  }

  function failsafe(sel) {
    setTimeout(function () {
      document.querySelectorAll(sel).forEach(function (e) {
        if (G && parseFloat(getComputedStyle(e).opacity) < 0.99) {
          G.set(e, { clearProps: "transform,opacity" });
        }
      });
    }, 1500);
  }

  function animateIntro() {
    var factor = (typeof state.animation === "number" ? state.animation : 7) / 10;
    if (!G || reduced || factor === 0) return;
    G.from(".sidebar", { x: -40, opacity: 0, duration: 0.6, ease: "power3.out", clearProps: "transform,opacity" });
    G.from(".topbar > *", { y: -16, opacity: 0, duration: 0.5, stagger: 0.05, ease: "power2.out", delay: 0.1, clearProps: "transform,opacity" });
    if (document.querySelector(".hero-text")) {
      G.from(".hero-text > *", { y: 18, opacity: 0, duration: 0.55, stagger: 0.07, ease: "power3.out", delay: 0.15, clearProps: "transform,opacity" });
    }
    if (document.querySelector(".stat")) {
      G.from(".stat", { y: 18, opacity: 0, scale: 0.95, duration: 0.5, stagger: 0.08, ease: "back.out(1.5)", delay: 0.25, clearProps: "transform,opacity" });
    }
    failsafe(".sidebar, .topbar > *" + (document.querySelector(".hero-text") ? ", .hero-text > *" : "") + (document.querySelector(".stat") ? ", .stat" : ""));
  }

  function animateTiles() {
    var tiles = document.querySelectorAll(".tile");
    if (!tiles.length) return;
    var factor = (typeof state.animation === "number" ? state.animation : 7) / 10;
    if (!G || reduced || factor === 0) {
      tiles.forEach(function (t) { t.style.opacity = 1; t.style.transform = ""; });
      return;
    }
    G.killTweensOf(tiles);
    G.fromTo(tiles,
      { opacity: 0, y: 24 + 30 * factor, scale: 0.96 },
      { opacity: 1, y: 0, scale: 1, duration: 0.5 + 0.35 * factor, ease: "power3.out",
        stagger: { each: 0.03 + 0.05 * factor, from: "start" },
        clearProps: "transform,opacity" });
    failsafe(".tile");
  }

  /* ── Search ──────────────────────────────────────────────────────────── */
  function initSearch() {
    var input = qs("#search");
    var list  = qs("#search-results");
    if (!input || !list) return;

    input.setAttribute('autocomplete', 'off');

    var rawUrl = input.dataset.url;
    if (!rawUrl) return;
    var url = resolveAppPath(rawUrl);

    var timer;
    var TYPE_LABELS = { reto: "Reto", anuncio: "Aviso", mensaje: "Mensaje", estudiante: "Alumno", profesor: "Profesor", modulo: "Módulo", "modulo-asignar": "Módulo", ciclo: "Ciclo" };

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

    input.addEventListener("keydown", function (e) {
      if (e.key === "Escape") { clearResults(); input.blur(); }
    });

    document.addEventListener("click", function (e) {
      if (!e.target.closest(".search-wrap")) clearResults();
    });
  }

  /* ── Notifications ────────────────────────────────────────────────────── */
  function initNotifications() {
    var btn   = qs("#notif-btn");
    var panel = qs("#notif-panel");
    var dot   = qs("#notif-dot");
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
    var nav = qs("#sidebar-nav");
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

  /* ── Sidebar Scroll Persistence ───────────────────────────────────────── */
  function initSidebarScroll() {
    var nav = qs("#sidebar-nav");
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
    var collapseBtn = qs("#collapse");
    var sidebar = qs(".sidebar");
    if (collapseBtn && sidebar) {
      if (state.sidebarCollapsed) sidebar.classList.add("collapsed");
      collapseBtn.addEventListener("click", function () {
        var nowCollapsed = sidebar.classList.toggle("collapsed");
        state.sidebarCollapsed = nowCollapsed;
        saveState();
      });
    }

    /* Mobile menu button */
    var app = qs("#app");
    var menuBtn = qs("#menu");
    if (menuBtn && app) {
      menuBtn.addEventListener("click", function () {
        app.classList.toggle("nav-open");
      });
    }

    /* Scrim click closes nav */
    var scrim = qs(".scrim");
    if (scrim && app) {
      scrim.addEventListener("click", function () {
        app.classList.remove("nav-open");
      });
    }

    /* Theme toggle */
    var themeBtn = qs("#theme");
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
        var search = qs("#search");
        if (search) search.focus();
      }
    });

    /* Collapsible nav categories (+/-) */
    initNavSections();

    /* Search */
    initSearch();

    /* Notifications */
    initNotifications();

    /* Poll unread count — keeps dot in sync without a page reload.
       Uses recursive setTimeout with exponential backoff so network errors
       (ERR_NETWORK_CHANGED, etc.) don't flood the console. */
    (function pollUnread() {
      var dot = qs("#notif-dot");
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
          .then(function(r) { if (!r.ok) throw new Error(r.status); return r.json(); })
          .then(function(d) {
            errors = 0;
            var n    = parseInt(d.count || 0, 10);
            var prev = parseInt(dot.dataset.msgs || '0', 10);
            if (n > prev) {
              dot.dataset.msgs = n;
              dot.removeAttribute('hidden');
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
      if (tile && G && !reduced) {
        G.fromTo(tile, { scale: 0.97 }, { scale: 1, duration: 0.4, ease: "elastic.out(1,0.5)", clearProps: "transform" });
      }
    });

    /* Entrance animations */
    animateIntro();
    animateTiles();
  });

  // Expose helper to global scope if needed
  window.resolveAppPath = resolveAppPath;

})();
