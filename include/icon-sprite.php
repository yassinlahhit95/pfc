<?php
// Sprite SVG de iconos de línea (estilo Lucide) para el sidebar.
// Cada <symbol> usa viewBox 0 0 24 24; el trazo (stroke/fill) se define
// en .ico dentro de public/css/sidebar.css. Uso: <svg class="ico"><use href="#ic-NOMBRE"/></svg>
if (!defined('ICON_SPRITE_LOADED')):
    define('ICON_SPRITE_LOADED', 1);
?>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <symbol id="ic-bars" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
  <symbol id="ic-ellipsis-v" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></symbol>
  <symbol id="ic-chart-line" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></symbol>
  <symbol id="ic-user-graduate" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
  <symbol id="ic-layer-group" viewBox="0 0 24 24"><path d="M12 2 2 7l10 5 10-5z"/><path d="m2 12 10 5 10-5"/><path d="m2 17 10 5 10-5"/></symbol>
  <symbol id="ic-book" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></symbol>
  <symbol id="ic-tasks" viewBox="0 0 24 24"><path d="m3 6 1.5 1.5L7 4"/><path d="M11 6h10"/><path d="m3 13 1.5 1.5L7 11"/><path d="M11 13h10"/><path d="m3 20 1.5 1.5L7 18"/><path d="M11 20h10"/></symbol>
  <symbol id="ic-graduation-cap" viewBox="0 0 24 24"><path d="M22 10 12 5 2 10l10 5z"/><path d="M6 12v5c0 1 2 3 6 3s6-2 6-3v-5"/><path d="M22 10v6"/></symbol>
  <symbol id="ic-star" viewBox="0 0 24 24"><path d="M12 2l2.9 6.2 6.8.6-5.1 4.5 1.5 6.7L12 16.9 5.9 20.5l1.5-6.7L2.3 9.4l6.8-.6z"/></symbol>
  <symbol id="ic-check-double" viewBox="0 0 24 24"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></symbol>
  <symbol id="ic-user-tie" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></symbol>
  <symbol id="ic-chalkboard-teacher" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="12" rx="1"/><path d="M12 15v4"/><path d="m8 21 4-3 4 3"/></symbol>
  <symbol id="ic-wallet" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="14.5" r="1"/></symbol>
  <symbol id="ic-calendar-alt" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol>
  <symbol id="ic-bullhorn" viewBox="0 0 24 24"><path d="m3 11 18-5v12L3 14z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></symbol>
  <symbol id="ic-envelope" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></symbol>
  <symbol id="ic-boxes" viewBox="0 0 24 24"><path d="M21 8v8a2 2 0 0 1-1 1.73l-7 4a2 2 0 0 1-2 0l-7-4A2 2 0 0 1 3 16V8a2 2 0 0 1 1-1.73l7-4a2 2 0 0 1 2 0l7 4A2 2 0 0 1 21 8z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></symbol>
  <symbol id="ic-hand-holding" viewBox="0 0 24 24"><path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/></symbol>
  <symbol id="ic-home" viewBox="0 0 24 24"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></symbol>
  <symbol id="ic-sign-out-alt" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></symbol>
  <symbol id="ic-user-circle" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18.5a6 6 0 0 1 11 0"/></symbol>
  <symbol id="ic-cubes" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></symbol>
  <symbol id="ic-folder-open" viewBox="0 0 24 24"><path d="m6 14 1.45-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.55 6a2 2 0 0 1-1.94 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H18a2 2 0 0 1 2 2v2"/></symbol>
  <symbol id="ic-paper-plane" viewBox="0 0 24 24"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/></symbol>
  <symbol id="ic-file-pdf" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/></symbol>
  <symbol id="ic-credit-card" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></symbol>
  <symbol id="ic-chevron" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
  <symbol id="ic-grip" viewBox="0 0 24 24"><circle cx="9" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="18" r="1"/></symbol>
  <symbol id="ic-mas" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
  <symbol id="ic-menos" viewBox="0 0 24 24"><path d="M5 12h14"/></symbol>
</svg>
<?php endif; ?>
