/* ===== AulaPro landing — interactions (jQuery) ===== */
(function ($) {
  "use strict";

  $(function() {
    /* ---- Nav: scroll shadow ---- */
    var $nav = $("#nav");
    function onScroll() { $nav.toggleClass("scrolled", $(window).scrollTop() > 8); }
    $(window).on("scroll", onScroll); onScroll();

    /* ---- Mobile menu ---- */
    var $mm = $("#mobileMenu");
    $("#navToggle").on("click", function () { $mm.toggleClass("open"); });
    $mm.on("click", "a", function () { $mm.removeClass("open"); });

    /* ---- Role tabs ---- */
    $(document).on("click", "#rolesTabs button", function () {
      var role = $(this).data("role");
      $("#rolesTabs button").removeClass("active");
      $(this).addClass("active");
      $(".role-panel").removeClass("active");
      $('.role-panel[data-role="' + role + '"]').addClass("active");
    });

    /* ---- Pricing toggle ---- */
    var annual = false;
    $(document).on("click", "#priceSwitch", function () {
      annual = !annual;
      $(this).toggleClass("annual", annual);
      $("#lblM").toggleClass("on", !annual);
      $("#lblA").toggleClass("on", annual);
      $(".plan .amt").each(function () {
        var v = annual ? $(this).data("a") : $(this).data("m");
        $(this).text(v + " €");
      });
    });

    /* ---- FAQ accordion ---- */
    $(document).on("click", ".qa-q", function () {
      var $qa = $(this).closest(".qa");
      var open = $qa.hasClass("open");
      
      // Cerrar otros
      $(".qa").removeClass("open").find(".qa-a").css("max-height", "0px");
      
      if (!open) {
        $qa.addClass("open");
        var $a = $qa.find(".qa-a");
        $a.css("max-height", $a[0].scrollHeight + "px");
      }
    });

    /* ---- Demo form ---- */
    $("#demoForm").on("submit", function (e) {
      e.preventDefault();
      var $form = $(this);
      var $btn = $form.find('button[type="submit"]');
      var originalText = $btn.text();
      
      var name = ($form.find('[name="nombre"]').val() || "").trim();
      var email = ($form.find('[name="email"]').val() || "").trim();
      var centro = ($form.find('[name="centro"]').val() || "").trim();

      if (!name || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email) || !centro) {
        $btn.text("Completa los campos ⚠").css("background", "#E08A00");
        setTimeout(function () { $btn.text(originalText).css("background", ""); }, 2200);
        return;
      }

      $btn.prop('disabled', true).text("Enviando...");

      $.ajax({
          url: $form.attr('action') || 'controladores/contacto_landing.php',
          type: 'POST',
          data: $form.serialize(),
          success: function(res) {
              if (res.ok) {
                  $btn.text("¡Enviado! Te contactamos pronto ✓").css("background", "#10b981");
                  $form[0].reset();
                  setTimeout(function () { 
                      $btn.prop('disabled', false).text(originalText).css("background", ""); 
                  }, 4000);
              } else {
                  $btn.text(res.msg || "Error ⚠").css("background", "#E03E3E");
                  setTimeout(function () { 
                      $btn.prop('disabled', false).text(originalText).css("background", ""); 
                  }, 3000);
              }
          },
          error: function() {
              $btn.text("Error de red ⚠").css("background", "#E03E3E");
              setTimeout(function () { 
                  $btn.prop('disabled', false).text(originalText).css("background", ""); 
              }, 3000);
          }
      });
    });

    /* ---- Smooth-scroll offset close menu on anchor ---- */
    $('a[href^="#"]').on("click", function () {
      $mm.removeClass("open");
    });
  });

  /* ============ TWEAKS ============ */
  var PALETTES = {
    "#2563EB": { strong: "#1D4ED8", tint: "#EAF1FF", glow: "rgba(37,99,235,.16)" },
    "#0EA5E9": { strong: "#0284C7", tint: "#E6F6FE", glow: "rgba(14,165,233,.18)" },
    "#4F46E5": { strong: "#4338CA", tint: "#EEF0FE", glow: "rgba(79,70,229,.18)" },
    "#0F766E": { strong: "#0B5C56", tint: "#E5F4F2", glow: "rgba(15,118,110,.18)" },
    "#E0792B": { strong: "#C25E12", tint: "#FCF0E4", glow: "rgba(224,121,43,.20)" }
  };

  var T = window.AULAPRO_TWEAKS || {};

  function apply() {
    var p = PALETTES[T.primaryColor] || PALETTES["#2563EB"];
    var root = document.documentElement.style;
    root.setProperty("--primary", T.primaryColor);
    root.setProperty("--primary-strong", p.strong);
    root.setProperty("--primary-tint", p.tint);
    root.setProperty("--primary-glow", p.glow);
    root.setProperty("--font-head", '"' + T.headingFont + '", system-ui, sans-serif');
    root.setProperty("--radius", T.radius + "px");
    root.setProperty("--radius-lg", (parseInt(T.radius, 10) + 8) + "px");
    document.body.classList.toggle("dark-hero", !!T.darkHero);
    $("#apg stop").eq(0).attr("stop-color", T.primaryColor);
    $("#apg stop").eq(1).attr("stop-color", p.strong);
  }

  function syncUI() {
    $("#swatches .swatch").removeClass("sel").filter('[data-c="' + T.primaryColor + '"]').addClass("sel");
    $("#fonts .fontbtn").removeClass("sel").filter('[data-f="' + T.headingFont + '"]').addClass("sel");
    $("#radius").val(T.radius);
    $("#darkHero").toggleClass("on", !!T.darkHero);
  }

  function persist(edits) {
    $.extend(T, edits);
    apply();
    try { window.parent.postMessage({ type: "__edit_mode_set_keys", edits: edits }, "*"); } catch (e) {}
  }

  $("#swatches").on("click", ".swatch", function () { persist({ primaryColor: $(this).data("c") }); syncUI(); });
  $("#fonts").on("click", ".fontbtn", function () { persist({ headingFont: $(this).data("f") }); syncUI(); });
  $("#radius").on("input", function () { persist({ radius: parseInt(this.value, 10) }); });
  $("#darkHero").on("click", function () { persist({ darkHero: !T.darkHero }); syncUI(); });

  window.addEventListener("message", function (e) {
    var d = e.data || {};
    if (d.type === "__activate_edit_mode") { $("#tweaks").addClass("show"); }
    else if (d.type === "__deactivate_edit_mode") { $("#tweaks").removeClass("show"); }
  });
  $("#tweaksClose").on("click", function () {
    $("#tweaks").removeClass("show");
    try { window.parent.postMessage({ type: "__edit_mode_dismissed" }, "*"); } catch (e) {}
  });
  
  apply(); syncUI();

})(jQuery);

/* ============ MOTION LAYER (vanilla) ============ */
(function () {
  "use strict";
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var fine = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  /* ---- Infinite logo marquee ---- */
  function initMarquee() {
    var track = document.getElementById("logoTrack");
    if (!track) return;
    var inner = track.innerHTML;
    track.innerHTML = '<div class="mq-group">' + inner + '</div><div class="mq-group">' + inner + '</div>';
    function size() {
      var groups = track.querySelectorAll(".mq-group");
      if (groups.length < 2) return;
      track.style.setProperty("--mq", "-" + groups[1].offsetLeft + "px");
    }
    size();
    window.addEventListener("resize", size);
    setTimeout(size, 400);
  }

  /* ---- Scroll reveal ---- */
  function initReveal() {
    var solo = [".section-head", ".mock-wrap", ".cta-box", ".price-toggle"];
    var groups = [".feat-big", ".spotlight", ".steps", ".stats", ".plans", ".faq", ".cta-points", ".role-copy ul"];
    var targets = [];
    solo.forEach(function (s) {
      document.querySelectorAll(s).forEach(function (el) {
        el.classList.add("reveal");
        if (s === ".mock-wrap") el.classList.add("r-zoom");
        targets.push(el);
      });
    });
    groups.forEach(function (s) {
      document.querySelectorAll(s).forEach(function (parent) {
        Array.prototype.forEach.call(parent.children, function (child, i) {
          child.classList.add("reveal");
          child.style.transitionDelay = Math.min(i * 80, 480) + "ms";
          targets.push(child);
        });
      });
    });
    if (reduce || !("IntersectionObserver" in window)) {
      targets.forEach(function (el) { el.classList.add("in"); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add("in"); io.unobserve(e.target); }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
    targets.forEach(function (el) { io.observe(el); });
  }

  /* ---- Stats Count-up ---- */
  function initCounters() {
    var els = document.querySelectorAll(".stat .big");
    if (!els.length) return;
    function run(el) {
      var raw = el.textContent.trim();
      var m = raw.match(/^(\D*)(\d+)(\D*)$/);
      if (!m) return;
      var pre = m[1], end = parseInt(m[2], 10), suf = m[3], dur = 1100, t0 = null;
      function step(ts) {
        if (!t0) t0 = ts;
        var p = Math.min((ts - t0) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = pre + Math.round(end * eased) + suf;
        if (p < 1) requestAnimationFrame(step);
      }
      el.textContent = pre + "0" + suf;
      requestAnimationFrame(step);
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { run(e.target); io.unobserve(e.target); } });
    }, { threshold: 0.6 });
    els.forEach(function (el) { io.observe(el); });
  }

  document.addEventListener("DOMContentLoaded", function() {
    initMarquee();
    initReveal();
    initCounters();
  });
})();
