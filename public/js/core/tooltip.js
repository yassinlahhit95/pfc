/* ===========================================================================
   AulaPro Contextual Tooltips — tooltip.js
   Small (?) triggers: <button class="ap-tip-trigger" data-tooltip="texto" aria-label="Ayuda"><i class="fas fa-circle-question"></i></button>
   Desktop: CSS :hover/:focus-visible show the bubble (see estilo.css).
   Touch/click: this script toggles it explicitly, since :hover doesn't fire
   reliably on touch devices, and closes on outside tap/Escape.
   =========================================================================== */
(function () {
  "use strict";

  var openTrigger = null;

  function closeOpen() {
    if (!openTrigger) return;
    openTrigger.classList.remove("ap-tip-open");
    openTrigger = null;
  }

  document.addEventListener("click", function (e) {
    var trigger = e.target.closest(".ap-tip-trigger");
    if (trigger) {
      e.preventDefault();
      var wasOpen = trigger === openTrigger;
      closeOpen();
      if (!wasOpen) {
        trigger.classList.add("ap-tip-open");
        openTrigger = trigger;
      }
      return;
    }
    if (openTrigger && !e.target.closest(".ap-tip-bubble")) closeOpen();
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeOpen();
  });
})();
