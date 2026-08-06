/* ===========================================================================
   AulaPro Onboarding Tour — onboarding-tour.js
   Self-contained IIFE. Spotlight + tooltip walkthrough shown once per user
   per tour_key (server decides via tours_completados). No CDN dependency.

   Usage: a page sets window.AULAPRO_TOUR = { tourKey, completeUrl, steps }
   before this script loads; the tour auto-starts if that global is present.
   Each step: { selector, title, text, placement? }  (placement: 'bottom'|'top'|'right'|'left', default 'bottom')
   =========================================================================== */
(function () {
  "use strict";

  var cfg = window.AULAPRO_TOUR;
  if (!cfg || !Array.isArray(cfg.steps) || !cfg.steps.length) return;

  var current = 0;
  var overlay, card, spotlight;

  function buildDom() {
    overlay = document.createElement("div");
    overlay.className = "ap-tour-overlay";

    spotlight = document.createElement("div");
    spotlight.className = "ap-tour-spotlight";
    overlay.appendChild(spotlight);

    card = document.createElement("div");
    card.className = "ap-tour-card";
    overlay.appendChild(card);

    document.body.appendChild(overlay);
  }

  function resolveUrl(rel) {
    return window.AulaProResolveAppPath ? window.AulaProResolveAppPath(rel) : rel;
  }

  function finish(reason) {
    if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
    if (!cfg.completeUrl) return;
    fetch(resolveUrl(cfg.completeUrl), {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: "tourKey=" + encodeURIComponent(cfg.tourKey) + "&csrf_token=" + encodeURIComponent(cfg.csrfToken || "") + "&reason=" + encodeURIComponent(reason)
    }).catch(function () {});
  }

  function placeCard(target, placement) {
    var rect = target.getBoundingClientRect();
    var cardRect = card.getBoundingClientRect();
    var gap = 14;
    var top, left;

    switch (placement) {
      case "top":
        top = rect.top - cardRect.height - gap;
        left = rect.left + rect.width / 2 - cardRect.width / 2;
        break;
      case "right":
        top = rect.top + rect.height / 2 - cardRect.height / 2;
        left = rect.right + gap;
        break;
      case "left":
        top = rect.top + rect.height / 2 - cardRect.height / 2;
        left = rect.left - cardRect.width - gap;
        break;
      default: // bottom
        top = rect.bottom + gap;
        left = rect.left + rect.width / 2 - cardRect.width / 2;
    }

    // Clamp inside viewport
    top = Math.max(10, Math.min(top, window.innerHeight - cardRect.height - 10));
    left = Math.max(10, Math.min(left, window.innerWidth - cardRect.width - 10));

    card.style.top = top + "px";
    card.style.left = left + "px";
  }

  function positionSpotlight(target) {
    var rect = target.getBoundingClientRect();
    var pad = 6;
    spotlight.style.top = (rect.top - pad) + "px";
    spotlight.style.left = (rect.left - pad) + "px";
    spotlight.style.width = (rect.width + pad * 2) + "px";
    spotlight.style.height = (rect.height + pad * 2) + "px";
  }

  function renderStep() {
    var step = cfg.steps[current];
    var target = step.selector ? document.querySelector(step.selector) : null;

    if (step.selector && !target) {
      // El objetivo no está en esta página (p. ej. un ítem de nav colapsado) — pasar al siguiente
      if (current < cfg.steps.length - 1) { current++; renderStep(); } else { finish("completed"); }
      return;
    }

    var isLast = current === cfg.steps.length - 1;

    card.innerHTML =
      '<div class="ap-tour-step">' + (current + 1) + ' / ' + cfg.steps.length + '</div>' +
      '<h3 class="ap-tour-title"></h3>' +
      '<p class="ap-tour-text"></p>' +
      '<div class="ap-tour-actions">' +
      '<button type="button" class="ap-tour-skip">Saltar</button>' +
      '<div class="ap-tour-nav">' +
      (current > 0 ? '<button type="button" class="ap-tour-prev">Atrás</button>' : '') +
      '<button type="button" class="ap-tour-next">' + (isLast ? 'Finalizar' : 'Siguiente') + '</button>' +
      '</div></div>';

    card.querySelector(".ap-tour-title").textContent = step.title || "";
    card.querySelector(".ap-tour-text").textContent = step.text || "";

    if (target) {
      spotlight.style.display = "block";
      positionSpotlight(target);
      target.scrollIntoView({ block: "center", behavior: "smooth" });
      setTimeout(function () {
        positionSpotlight(target);
        placeCard(target, step.placement || "bottom");
      }, 260);
    } else {
      spotlight.style.display = "none";
      card.style.top = "50%";
      card.style.left = "50%";
      card.style.transform = "translate(-50%, -50%)";
    }

    card.querySelector(".ap-tour-skip").addEventListener("click", function () { finish("skipped"); });
    var nextBtn = card.querySelector(".ap-tour-next");
    nextBtn.addEventListener("click", function () {
      if (isLast) { finish("completed"); return; }
      current++;
      renderStep();
    });
    var prevBtn = card.querySelector(".ap-tour-prev");
    if (prevBtn) prevBtn.addEventListener("click", function () { current--; renderStep(); });
  }

  document.addEventListener("DOMContentLoaded", function () {
    buildDom();
    renderStep();
    window.addEventListener("resize", function () {
      var step = cfg.steps[current];
      var target = step && step.selector ? document.querySelector(step.selector) : null;
      if (target) { positionSpotlight(target); placeCard(target, step.placement || "bottom"); }
    });
  });
})();
