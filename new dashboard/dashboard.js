/* ===========================================================================
   AulaPro Dashboard — dashboard.js  (vanilla, no framework)
   =========================================================================== */
(function () {
  "use strict";

  /* ── Icons (inline SVG inner markup) ─────────────────────────────────── */
  const ICONS = {
    admin: '<path d="M12 2.2 4 5.2v5.1c0 4.7 3.2 8.4 8 9.5 4.8-1.1 8-4.8 8-9.5V5.2l-8-3z"/><path d="M10.6 13.9 8.4 11.7a1 1 0 0 0-1.5 1.4l2.9 2.9c.4.4 1.1.4 1.5 0l4.4-4.6a1 1 0 1 0-1.5-1.4l-3.6 3.9z" fill="#fff"/>',
    app: '<rect x="6" y="2.2" width="12" height="19.6" rx="3"/><rect x="8" y="4.4" width="8" height="12.8" rx="1.4" fill="#fff" opacity=".92"/><circle cx="12" cy="19.4" r="1.1" fill="#fff"/><rect x="9.6" y="6.4" width="4.8" height="1.5" rx=".75" fill="currentColor" opacity=".5"/><rect x="9.6" y="9.2" width="3.2" height="1.5" rx=".75" fill="currentColor" opacity=".5"/>',
    clases: '<rect x="2.5" y="4" width="19" height="12.4" rx="2.2"/><rect x="4.6" y="6" width="14.8" height="8.4" rx="1" fill="#fff" opacity=".92"/><path d="M3 18.2h18a1 1 0 0 1 0 2H3a1 1 0 0 1 0-2z"/><path d="M8 9.2h8M8 11.6h5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" opacity=".55"/>',
    config: '<path d="M12 8.6a3.4 3.4 0 1 0 0 6.8 3.4 3.4 0 0 0 0-6.8zm0 2a1.4 1.4 0 1 1 0 2.8 1.4 1.4 0 0 1 0-2.8z"/><path d="M19.4 12c0-.5 0-1-.1-1.4l2-1.5-2-3.4-2.3 1a7.3 7.3 0 0 0-2.4-1.4L14.2 2H9.8l-.4 2.3c-.9.3-1.7.8-2.4 1.4l-2.3-1-2 3.4 2 1.5a7.6 7.6 0 0 0 0 2.8l-2 1.5 2 3.4 2.3-1c.7.6 1.5 1 2.4 1.4l.4 2.3h4.4l.4-2.3c.9-.4 1.7-.8 2.4-1.4l2.3 1 2-3.4-2-1.5c.1-.4.1-.9.1-1.4z"/>',
    crm: '<circle cx="8" cy="8.2" r="3.2"/><circle cx="16.4" cy="9.4" r="2.6"/><path d="M2.6 19.6c0-3 2.4-5.2 5.4-5.2s5.4 2.2 5.4 5.2a1 1 0 0 1-1 1H3.6a1 1 0 0 1-1-1z"/><path d="M14.6 14.6c2.5.2 4.4 2.1 4.8 4.6a1 1 0 0 1-1 1.4h-2.5c.1-2.1-.5-4.1-1.3-5.4-.1-.2 0-.5-.1-.6z"/>',
    estudiantes: '<path d="M12 3 1.8 7.6 12 12.2l8.4-3.8v4.4a1 1 0 0 0 2 0V7.6L12 3z"/><path d="M6 12.6v3.1c0 1.9 2.7 3.4 6 3.4s6-1.5 6-3.4v-3.1l-6 2.7-6-2.7z" fill="currentColor" opacity=".62"/>',
    eventos: '<rect x="3" y="4.4" width="18" height="16.4" rx="2.4"/><rect x="5" y="9" width="14" height="9.8" rx="1" fill="#fff" opacity=".95"/><rect x="7" y="2.2" width="2" height="4.4" rx="1"/><rect x="15" y="2.2" width="2" height="4.4" rx="1"/><circle cx="9" cy="12.6" r="1.3" fill="currentColor" opacity=".7"/><circle cx="13.2" cy="12.6" r="1.3" fill="currentColor" opacity=".4"/><circle cx="9" cy="16" r="1.3" fill="currentColor" opacity=".4"/>',
    personal: '<rect x="3" y="4.6" width="18" height="14.8" rx="2.6"/><rect x="5" y="2.6" width="6" height="3.4" rx="1.4" fill="currentColor"/><circle cx="9" cy="11" r="2.2" fill="#fff"/><path d="M5.4 16.6c0-1.8 1.6-3 3.6-3s3.6 1.2 3.6 3a.8.8 0 0 1-.8.8H6.2a.8.8 0 0 1-.8-.8z" fill="#fff"/><path d="M14.6 10h4M14.6 13h3" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>',
    noticias: '<rect x="2.4" y="4.2" width="15.4" height="15.6" rx="2"/><path d="M17.8 8h2.4a1.6 1.6 0 0 1 1.6 1.6v8.4a1.8 1.8 0 0 1-3.6 0V8z" fill="currentColor" opacity=".55"/><rect x="4.6" y="6.4" width="6.4" height="5" rx="1" fill="#fff" opacity=".95"/><path d="M12.6 6.6h3M12.6 9.2h3M4.6 13.6h11M4.6 16h8" stroke="#fff" stroke-width="1.4" stroke-linecap="round"/>',
    familiares: '<circle cx="7" cy="6.6" r="2.8"/><circle cx="17" cy="6.6" r="2.8"/><circle cx="12" cy="13.4" r="2.2" fill="currentColor" opacity=".7"/><path d="M2.4 16.4c0-2.6 2.1-4.4 4.6-4.4s4.6 1.8 4.6 4.4a.9.9 0 0 1-.9.9H3.3a.9.9 0 0 1-.9-.9z"/><path d="M12.4 16.4c0-2.6 2.1-4.4 4.6-4.4s4.6 1.8 4.6 4.4a.9.9 0 0 1-.9.9h-7.4a.9.9 0 0 1-.9-.9z"/><path d="M8.4 20.6c0-2 1.6-3.4 3.6-3.4s3.6 1.4 3.6 3.4a.8.8 0 0 1-.8.8H9.2a.8.8 0 0 1-.8-.8z" fill="currentColor" opacity=".7"/>',
    pagos: '<rect x="2.4" y="5" width="19.2" height="14" rx="2.6"/><rect x="2.4" y="8.2" width="19.2" height="3" fill="#fff" opacity=".85"/><rect x="5" y="14.4" width="6" height="2.2" rx="1.1" fill="#fff" opacity=".9"/><rect x="15.4" y="14.4" width="3.4" height="2.2" rx="1.1" fill="#fff" opacity=".55"/>',
    reportes: '<rect x="3" y="3.4" width="18" height="17.2" rx="2.6"/><rect x="6.4" y="12.6" width="2.8" height="5" rx="1" fill="#fff"/><rect x="10.6" y="9.2" width="2.8" height="8.4" rx="1" fill="#fff" opacity=".92"/><rect x="14.8" y="6.4" width="2.8" height="11.2" rx="1" fill="#fff" opacity=".75"/>',
    grid: '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>',
    home: '<path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/>',
    chat: '<path d="M21 11.5a8.4 8.4 0 0 1-12 7.6L3 21l1.9-6A8.4 8.4 0 1 1 21 11.5z"/>',
    calendar: '<path d="M7 2v3M17 2v3M3.5 9h17M5 5h14a1.5 1.5 0 0 1 1.5 1.5V19A1.5 1.5 0 0 1 19 20.5H5A1.5 1.5 0 0 1 3.5 19V6.5A1.5 1.5 0 0 1 5 5z"/>',
    folder: '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>',
    star: '<path d="m12 3 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9 6.8 19.6l1-5.8L3.5 9.7l5.9-.9L12 3z"/>',
    settings: '<path d="M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM19.4 12a7.4 7.4 0 0 0-.1-1.4l1.6-1.2-1.7-2.9-1.9.8a7 7 0 0 0-2.4-1.4L14.5 4h-3l-.4 1.9a7 7 0 0 0-2.4 1.4l-1.9-.8L5 9.4l1.6 1.2a7.5 7.5 0 0 0 0 2.8L5 14.6l1.7 2.9 1.9-.8c.7.6 1.5 1.1 2.4 1.4l.4 1.9h3l.4-1.9c.9-.3 1.7-.8 2.4-1.4l1.9.8 1.7-2.9-1.6-1.2c.1-.4.1-.9.1-1.4z"/>',
    search: '<path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/>',
    bell: '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/>',
    sun: '<path d="M12 4V2M12 22v-2M5 5 3.6 3.6M20.4 20.4 19 19M4 12H2M22 12h-2M5 19l-1.4 1.4M20.4 3.6 19 5M12 7.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9z"/>',
    moon: '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>',
    chevron: '<path d="m9 6 6 6-6 6"/>',
    help: '<path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM9.5 9.5a2.5 2.5 0 0 1 4.9.7c0 1.7-2.4 2-2.4 3.5M12 17h.01"/>',
    logout: '<path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 17l-5-5 5-5M5 12h11"/>',
    arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>',
  };
  const FILLED = new Set(["admin", "app", "clases", "config", "crm", "estudiantes", "eventos", "personal", "noticias", "familiares", "pagos", "reportes"]);

  function icon(name, size) {
    const filled = FILLED.has(name);
    return `<svg viewBox="0 0 24 24" width="${size}" height="${size}" fill="${filled ? "currentColor" : "none"}" stroke="${filled ? "none" : "currentColor"}" stroke-width="${filled ? 0 : 1.8}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${ICONS[name] || ""}</svg>`;
  }

  /* ── Module catalog ──────────────────────────────────────────────────── */
  const MODULES = [
    { id: "admin", label: "Admin", icon: "admin", hue: "#4F46E5", desc: "Centro de control", stat: "12 áreas" },
    { id: "app", label: "App", icon: "app", hue: "#0EA5E9", desc: "Móvil & accesos", stat: "4.8★" },
    { id: "clases", label: "Clases", icon: "clases", hue: "#14B8A6", desc: "Horarios y aulas", stat: "86 activas" },
    { id: "config", label: "Configuración", icon: "config", hue: "#64748B", desc: "Ajustes del campus", stat: null },
    { id: "crm", label: "CRM", icon: "crm", hue: "#F43F5E", desc: "Prospectos & leads", stat: "23 nuevos", badge: 23 },
    { id: "estudiantes", label: "Estudiantes", icon: "estudiantes", hue: "#F59E0B", desc: "Expedientes", stat: "1,248" },
    { id: "eventos", label: "Eventos", icon: "eventos", hue: "#8B5CF6", desc: "Calendario escolar", stat: "5 esta semana" },
    { id: "personal", label: "Personal", icon: "personal", hue: "#06B6D4", desc: "Docentes & staff", stat: "94" },
    { id: "noticias", label: "Noticias", icon: "noticias", hue: "#10B981", desc: "Comunicados", stat: "3 borradores" },
    { id: "familiares", label: "Familiares", icon: "familiares", hue: "#F97316", desc: "Tutores & padres", stat: "2,106" },
    { id: "pagos", label: "Pagos", icon: "pagos", hue: "#22C55E", desc: "Colegiaturas", stat: "$184k", badge: 7 },
    { id: "reportes", label: "Reportes", icon: "reportes", hue: "#D946EF", desc: "Métricas & KPIs", stat: "Q2 listo" },
  ];

  const NAV_TOP = [
    { icon: "home", label: "Inicio" },
    { icon: "grid", label: "Módulos", active: true },
    { icon: "chat", label: "Mensajes", badge: 4 },
    { icon: "calendar", label: "Calendario" },
    { icon: "star", label: "Favoritos" },
    { icon: "folder", label: "Archivos" },
  ];
  const NAV_BOTTOM = [
    { icon: "help", label: "Ayuda" },
    { icon: "settings", label: "Ajustes" },
  ];

  const $ = (s, r = document) => r.querySelector(s);
  const el = (html) => { const t = document.createElement("template"); t.innerHTML = html.trim(); return t.content.firstElementChild; };

  /* ── State ───────────────────────────────────────────────────────────── */
  const STORE_KEY = "aulapro_tweaks_v1";
  let state = Object.assign({}, window.TWEAK_DEFAULTS);
  try { Object.assign(state, JSON.parse(localStorage.getItem(STORE_KEY) || "{}")); } catch (e) {}

  function applyTheme() {
    const r = document.documentElement;
    r.setAttribute("data-theme", state.dark ? "dark" : "light");
    r.setAttribute("data-density", state.density);
    r.style.setProperty("--accent", state.accent);
    r.style.setProperty("--anim", String(state.animation / 10));
  }

  /* ── Render: sidebar ─────────────────────────────────────────────────── */
  function navItem(it, collapsed) {
    return `<button class="nav-item${it.active ? " active" : ""}" ${collapsed ? `title="${it.label}"` : ""}>
      <span class="nav-ico">${icon(it.icon, 21)}</span>
      <span class="nav-label">${it.label}</span>
      ${it.badge ? `<span class="nav-badge">${it.badge}</span>` : ""}
      ${it.active ? '<span class="nav-rail"></span>' : ""}
    </button>`;
  }
  function renderSidebar() {
    $("#nav-top").innerHTML = NAV_TOP.map((it) => navItem(it)).join("");
    $("#nav-bottom").innerHTML = NAV_BOTTOM.map((it) => navItem(it)).join("");
  }

  /* ── Render: module grid ─────────────────────────────────────────────── */
  function renderGrid() {
    const grid = $("#grid");
    grid.innerHTML = "";
    MODULES.forEach((m) => {
      const tint = state.iconMode === "mono" ? state.accent : m.hue;
      const node = el(`<button class="tile card-${state.cardStyle}" style="--tint:${tint}" data-id="${m.id}">
        <span class="tile-sheen"></span>
        <span class="tile-ico">${icon(m.icon, 30)}${m.badge ? `<span class="tile-badge">${m.badge > 9 ? "9+" : m.badge}</span>` : ""}</span>
        <span class="tile-body">
          <span class="tile-label">${m.label}</span>
          <span class="tile-desc">${m.desc}</span>
        </span>
        <span class="tile-foot">
          ${state.showStats && m.stat ? `<span class="tile-stat">${m.stat}</span>` : "<span></span>"}
          <span class="tile-go">${icon("arrow", 16)}</span>
        </span>
      </button>`);
      grid.appendChild(node);
    });
    animateTiles();
  }

  /* ── Animations (GSAP) ───────────────────────────────────────────────── */
  const G = window.gsap;
  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function failsafe(sel) {
    setTimeout(() => {
      document.querySelectorAll(sel).forEach((e) => {
        if (G && parseFloat(getComputedStyle(e).opacity) < 0.99) {
          // Clear ONLY GSAP's opacity/transform — never the inline --tint or
          // other custom props. clearProps applies instantly (no rAF needed).
          G.set(e, { clearProps: "transform,opacity" });
        }
      });
    }, 1500);
  }

  function animateTiles() {
    const tiles = document.querySelectorAll(".tile");
    const factor = state.animation / 10;
    if (!G || reduced || factor === 0) { tiles.forEach((t) => { t.style.opacity = 1; t.style.transform = ""; }); return; }
    G.killTweensOf(tiles);
    G.fromTo(tiles,
      { opacity: 0, y: 24 + 30 * factor, scale: 0.96 },
      { opacity: 1, y: 0, scale: 1, duration: 0.5 + 0.35 * factor, ease: "power3.out",
        stagger: { each: 0.03 + 0.05 * factor, from: "start" },
        clearProps: "transform,opacity" });
    failsafe(".tile");
  }
  function animateIntro() {
    const factor = state.animation / 10;
    if (!G || reduced || factor === 0) return;
    G.from(".sidebar", { x: -40, opacity: 0, duration: 0.6, ease: "power3.out", clearProps: "transform,opacity" });
    G.from(".topbar > *", { y: -16, opacity: 0, duration: 0.5, stagger: 0.05, ease: "power2.out", delay: 0.1, clearProps: "transform,opacity" });
    G.from(".hero-text > *", { y: 18, opacity: 0, duration: 0.55, stagger: 0.07, ease: "power3.out", delay: 0.15, clearProps: "transform,opacity" });
    G.from(".stat", { y: 18, opacity: 0, scale: 0.95, duration: 0.5, stagger: 0.08, ease: "back.out(1.5)", delay: 0.25, clearProps: "transform,opacity" });
    failsafe(".sidebar, .topbar > *, .hero-text > *, .stat");
  }

  /* ── Interactions ────────────────────────────────────────────────────── */
  function wireShell() {
    const app = $("#app");
    $("#collapse").addEventListener("click", () => $(".sidebar").classList.toggle("collapsed"));
    $("#menu").addEventListener("click", () => app.classList.toggle("nav-open"));
    $(".scrim").addEventListener("click", () => app.classList.remove("nav-open"));

    $("#theme").addEventListener("click", () => setTweaks({ dark: !state.dark }));

    // school switcher
    const sw = $("#school-switch");
    $("#school-btn").addEventListener("click", (e) => { e.stopPropagation(); sw.classList.toggle("open"); renderDropdown(); });
    document.addEventListener("click", (e) => { if (!sw.contains(e.target)) sw.classList.remove("open"); });

    // tile click feedback
    $("#grid").addEventListener("click", (e) => {
      const tile = e.target.closest(".tile");
      if (tile && G && !reduced) G.fromTo(tile, { scale: 0.97 }, { scale: 1, duration: 0.4, ease: "elastic.out(1,0.5)", clearProps: "transform" });
    });

    // ⌘K focuses search
    document.addEventListener("keydown", (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") { e.preventDefault(); $("#search").focus(); }
    });

    // keep theme icon in sync
    syncThemeBtn();
  }

  const SCHOOLS = ["Western Preparatory Academy", "Colegio del Valle", "Instituto Cumbres", "Liceo Moderno"];
  let currentSchool = SCHOOLS[0];
  function initials(s) { return s.split(" ").map((w) => w[0]).slice(0, 2).join(""); }
  function renderDropdown() {
    const dd = $("#school-dd");
    dd.innerHTML = SCHOOLS.map((s) => `<button class="dropdown-item${s === currentSchool ? " sel" : ""}" data-s="${s}"><span class="school-avatar sm">${initials(s)}</span>${s}</button>`).join("");
    dd.querySelectorAll(".dropdown-item").forEach((b) => b.addEventListener("click", () => {
      currentSchool = b.dataset.s;
      $("#school-name").textContent = currentSchool;
      $("#school-init").textContent = initials(currentSchool);
      $("#school-switch").classList.remove("open");
    }));
  }
  function syncThemeBtn() {
    $("#theme .theme-knob").innerHTML = icon(state.dark ? "sun" : "moon", 19);
  }

  /* ── Tweaks panel (vanilla, host protocol) ───────────────────────────── */
  function setTweaks(edits) {
    Object.assign(state, edits);
    try { localStorage.setItem(STORE_KEY, JSON.stringify(state)); } catch (e) {}
    window.parent.postMessage({ type: "__edit_mode_set_keys", edits }, "*");
    applyTheme();
    if ("dark" in edits) { syncThemeBtn(); spinTheme(); }
    if ("iconMode" in edits || "cardStyle" in edits || "showStats" in edits ||
        ("accent" in edits && state.iconMode === "mono")) renderGrid();
    if ("animation" in edits) animateTiles();
    syncControls();
  }
  function spinTheme() {
    const k = $("#theme .theme-knob");
    if (G && !reduced) G.fromTo(k, { rotate: -90, opacity: 0 }, { rotate: 0, opacity: 1, duration: 0.45, ease: "back.out(2)" });
  }

  let panelEl = null;
  function buildPanel() {
    injectPanelStyle();
    panelEl = el(`<div class="twk" data-omelette-chrome="" hidden>
      <div class="twk-hd"><b>Tweaks</b><button class="twk-x" aria-label="Cerrar">✕</button></div>
      <div class="twk-body">
        <div class="twk-sect">Marca</div>
        <div class="twk-row"><span class="twk-l">Acento</span><div class="twk-chips" data-k="accent"></div></div>
        <div class="twk-row"><span class="twk-l">Iconos</span><div class="twk-seg" data-k="iconMode" data-opts="multi:Color,mono:Mono"></div></div>
        <div class="twk-sect">Tarjetas</div>
        <div class="twk-row"><span class="twk-l">Estilo</span><div class="twk-seg" data-k="cardStyle" data-opts="soft:Suave,tint:Tinte,outline:Línea"></div></div>
        <div class="twk-row"><span class="twk-l">Densidad</span><div class="twk-seg" data-k="density" data-opts="compact:Comp.,regular:Normal,comfy:Amplia"></div></div>
        <div class="twk-row twk-h"><span class="twk-l">Mostrar métricas</span><button class="twk-tog" data-k="showStats"><i></i></button></div>
        <div class="twk-sect">Movimiento</div>
        <div class="twk-row"><span class="twk-l">Animación <em data-v="animation"></em></span><input class="twk-rng" type="range" min="0" max="10" step="1" data-k="animation"></div>
        <div class="twk-row twk-h"><span class="twk-l">Modo oscuro</span><button class="twk-tog" data-k="dark"><i></i></button></div>
      </div>
    </div>`);
    document.body.appendChild(panelEl);

    // accent chips
    const accents = ["#4F46E5", "#F43F5E", "#0EA5A4", "#8B5CF6", "#0F172A"];
    const chips = panelEl.querySelector('[data-k="accent"]');
    chips.innerHTML = accents.map((c) => `<button class="twk-chip" style="background:${c}" data-c="${c}" title="${c}"></button>`).join("");
    chips.querySelectorAll(".twk-chip").forEach((b) => b.addEventListener("click", () => setTweaks({ accent: b.dataset.c })));

    // segmented
    panelEl.querySelectorAll(".twk-seg").forEach((seg) => {
      const k = seg.dataset.k;
      seg.innerHTML = seg.dataset.opts.split(",").map((pair) => {
        const [v, l] = pair.split(":");
        return `<button data-v="${v}">${l}</button>`;
      }).join("") + '<span class="twk-thumb"></span>';
      seg.querySelectorAll("button").forEach((b) => b.addEventListener("click", () => setTweaks({ [k]: b.dataset.v })));
    });

    // toggles
    panelEl.querySelectorAll(".twk-tog").forEach((b) => b.addEventListener("click", () => setTweaks({ [b.dataset.k]: !state[b.dataset.k] })));
    // slider
    panelEl.querySelector(".twk-rng").addEventListener("input", (e) => setTweaks({ animation: Number(e.target.value) }));
    // close
    panelEl.querySelector(".twk-x").addEventListener("click", () => { panelEl.hidden = true; window.parent.postMessage({ type: "__edit_mode_dismissed" }, "*"); });

    syncControls();
  }

  function syncControls() {
    if (!panelEl) return;
    panelEl.querySelectorAll(".twk-chip").forEach((b) => b.classList.toggle("on", b.dataset.c.toLowerCase() === String(state.accent).toLowerCase()));
    panelEl.querySelectorAll(".twk-seg").forEach((seg) => {
      const k = seg.dataset.k;
      const btns = [...seg.querySelectorAll("button")];
      btns.forEach((b) => b.classList.toggle("on", b.dataset.v === state[k]));
      const idx = Math.max(0, btns.findIndex((b) => b.dataset.v === state[k]));
      const thumb = seg.querySelector(".twk-thumb");
      thumb.style.width = `calc((100% - 4px) / ${btns.length})`;
      thumb.style.left = `calc(2px + ${idx} * (100% - 4px) / ${btns.length})`;
    });
    panelEl.querySelectorAll(".twk-tog").forEach((b) => b.setAttribute("data-on", state[b.dataset.k] ? "1" : "0"));
    const rng = panelEl.querySelector(".twk-rng");
    rng.value = state.animation;
    panelEl.querySelector('[data-v="animation"]').textContent = state.animation;
  }

  function injectPanelStyle() {
    const css = `
    .twk{position:fixed;right:16px;bottom:16px;z-index:2147483646;width:268px;
      background:color-mix(in oklab,var(--surface) 80%,transparent);color:var(--text);
      -webkit-backdrop-filter:blur(22px) saturate(160%);backdrop-filter:blur(22px) saturate(160%);
      border:1px solid var(--border-2);border-radius:16px;box-shadow:var(--shadow-lg);
      font-family:var(--font-ui);font-size:12px;overflow:hidden}
    .twk-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 12px 12px 16px;
      border-bottom:1px solid var(--border)}
    .twk-hd b{font-family:var(--font-display);font-size:13px;font-weight:600}
    .twk-x{border:0;background:transparent;color:var(--mut);width:24px;height:24px;border-radius:7px;font-size:13px}
    .twk-x:hover{background:var(--surface-3);color:var(--text)}
    .twk-body{padding:6px 16px 16px;display:flex;flex-direction:column;gap:11px}
    .twk-sect{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--mut);padding-top:8px}
    .twk-row{display:flex;flex-direction:column;gap:7px}
    .twk-row.twk-h{flex-direction:row;align-items:center;justify-content:space-between}
    .twk-l{font-weight:600;color:var(--dim);display:flex;justify-content:space-between}
    .twk-l em{font-style:normal;color:var(--mut);font-variant-numeric:tabular-nums}
    .twk-chips{display:flex;gap:8px}
    .twk-chip{flex:1;height:30px;border:0;border-radius:9px;box-shadow:inset 0 0 0 1px rgba(0,0,0,.12);transition:transform .15s,box-shadow .15s;position:relative}
    .twk-chip:hover{transform:translateY(-1px)}
    .twk-chip.on{box-shadow:0 0 0 2px var(--surface),0 0 0 4px currentColor;color:var(--text)}
    .twk-chip.on::after{content:"✓";position:absolute;inset:0;display:grid;place-items:center;color:#fff;font-size:13px;font-weight:800;text-shadow:0 1px 2px rgba(0,0,0,.4)}
    .twk-seg{position:relative;display:flex;padding:2px;border-radius:10px;background:var(--surface-3)}
    .twk-seg button{position:relative;z-index:1;flex:1;border:0;background:transparent;color:var(--dim);
      font:inherit;font-weight:600;font-size:11.5px;padding:6px 4px;border-radius:8px;transition:color .2s}
    .twk-seg button.on{color:var(--text)}
    .twk-thumb{position:absolute;top:2px;bottom:2px;border-radius:8px;background:var(--surface);
      box-shadow:var(--shadow-sm);transition:left .2s cubic-bezier(.3,.7,.4,1),width .2s}
    .twk-tog{position:relative;width:38px;height:22px;border:0;border-radius:999px;background:var(--border-2);transition:background .2s;padding:0}
    .twk-tog[data-on="1"]{background:var(--accent)}
    .twk-tog i{position:absolute;top:2px;left:2px;width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 3px rgba(0,0,0,.3);transition:transform .2s}
    .twk-tog[data-on="1"] i{transform:translateX(16px)}
    .twk-rng{-webkit-appearance:none;appearance:none;width:100%;height:5px;border-radius:999px;background:var(--surface-3);outline:0}
    .twk-rng::-webkit-slider-thumb{-webkit-appearance:none;width:16px;height:16px;border-radius:50%;background:var(--accent);box-shadow:0 1px 4px rgba(0,0,0,.3);cursor:pointer}
    .twk-rng::-moz-range-thumb{width:16px;height:16px;border:0;border-radius:50%;background:var(--accent)}
    @media (max-width:560px){.twk{display:none}}`;
    const s = document.createElement("style");
    s.textContent = css;
    document.head.appendChild(s);
  }

  function wireProtocol() {
    window.addEventListener("message", (e) => {
      const t = e && e.data && e.data.type;
      if (t === "__activate_edit_mode" && panelEl) panelEl.hidden = false;
      else if (t === "__deactivate_edit_mode" && panelEl) panelEl.hidden = true;
    });
    window.parent.postMessage({ type: "__edit_mode_available" }, "*");
  }

  /* ── Boot ────────────────────────────────────────────────────────────── */
  applyTheme();
  renderSidebar();
  renderGrid();
  wireShell();
  buildPanel();
  wireProtocol();
  animateIntro();
})();
