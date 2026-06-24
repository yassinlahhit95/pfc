<?php
require_once __DIR__ . '/include/FeatureGuard.php';
$is_prematricula_enabled = FeatureGuard::check('feature_prematricula');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AulaPro — El control total de tu centro de FP</title>
<meta name="description" content="AulaPro unifica admisiones, calificaciones, pagos y comunicación de tu centro de Formación Profesional en una sola plataforma elegante y rápida.">
<link rel="shortcut icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@400;600;700;800&family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="public/css/landing/styles.css">
</head>
<body>

<!-- reusable logo symbol -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <linearGradient id="apg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="var(--primary)"/>
      <stop offset="1" stop-color="var(--primary-strong)"/>
    </linearGradient>
    <symbol id="ap-logo" viewBox="0 0 40 40">
      <rect width="40" height="40" rx="11" fill="url(#apg)"/>
      <path d="M20 8.5 L30 31 H24.7 L23 26.6 H17 L15.3 31 H10 Z M18.5 22.2 H21.5 L20 17.6 Z" fill="#fff"/>
    </symbol>
  </defs>
</svg>

<!-- ============ NAV ============ -->
<header class="nav" id="nav">
  <div class="container nav-inner">
    <a class="brand" href="#inicio"><svg class="logo"><use href="#ap-logo"/></svg>Aula<b>Pro</b></a>
    <nav class="nav-links">
      <a href="#inicio">Inicio</a>
      <a href="#funciones">Funciones</a>
      <a href="#roles">Plataforma</a>
      <a href="#ventajas">Ventajas</a>
      <a href="#precios">Precios</a>
    </nav>
    <div class="nav-cta">
      <a class="access" href="vistas/login.php">Acceso</a>
      <?php if ($is_prematricula_enabled): ?>
        <a class="btn btn-primary" href="vistas/admisiones/pre-matricula.php">Pre-matrícula</a>
      <?php endif; ?>
      <button class="nav-toggle" id="navToggle" aria-label="Menú"><span></span></button>
    </div>
  </div>
</header>
<div class="mobile-menu" id="mobileMenu">
  <a href="#inicio">Inicio</a>
  <a href="#funciones">Funciones</a>
  <a href="#roles">Plataforma</a>
  <a href="#ventajas">Ventajas</a>
  <a href="#precios">Precios</a>
  <a href="vistas/login.php">Acceso</a>
  <?php if ($is_prematricula_enabled): ?>
    <a class="btn btn-primary btn-block btn-lg" href="vistas/admisiones/pre-matricula.php">Pre-matrícula</a>
  <?php endif; ?>
</div>

<!-- ============ HERO ============ -->
<section class="hero" id="inicio">
  <div class="hero-bg">
    <div class="glow g1"></div><div class="glow g2"></div><div class="grid"></div>
  </div>
  <div class="container hero-top">
    <div class="hero-copy">
      <span class="eyebrow"><span class="dot"></span>Más matrículas · Menos papeleo</span>
      <h1>El control total de tu centro, <span class="grad">en una sola plataforma.</span></h1>
      <p class="hero-sub">Admisión, notas, pagos y comunicación de tu centro de FP en un único panel. Más matrículas y menos gestión.</p>
      <div class="hero-cta">
        <?php if ($is_prematricula_enabled): ?>
          <a class="btn btn-primary btn-lg" href="vistas/admisiones/pre-matricula.php">Hacer Pre-matrícula
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a class="btn btn-ghost btn-lg" href="vistas/admisiones/consultar.php">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Consultar estado
          </a>
        <?php endif; ?>
      </div>
      <div class="hero-social">
        <div class="av-stack">
          <span class="av" style="background:linear-gradient(135deg,#5b8def,#22b8cf)">MR</span>
          <span class="av" style="background:linear-gradient(135deg,#0F9D72,#16b886)">JL</span>
          <span class="av" style="background:linear-gradient(135deg,#f0883e,#e0792b)">NS</span>
          <span class="av" style="background:linear-gradient(135deg,#7c5cff,#a855f7)">LF</span>
          <span class="av more">+120</span>
        </div>
        <div class="hs-text">
          <div class="stars">★★★★★<b>4,9</b></div>
          <small>centros de FP en España ya confían en AulaPro</small>
        </div>
      </div>
    </div>

    <div class="hero-visual" id="heroVisual">
      <div class="hv-blob"></div>
      <div class="hv-ring"></div>
      <div class="hv-photo">
        <img src="public/imagenes/fondo.webp" alt="AulaPro Dashboard" style="width:100%;height:100%;object-fit:cover;border-radius:26px">
      </div>
      <div class="hv-badge"><svg class="logo"><use href="#ap-logo"/></svg>100% en la nube</div>
      <div class="hv-card c-notif">
        <div class="ic" style="background:linear-gradient(135deg,var(--primary),var(--primary-strong))"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></div>
        <div><b>Notas publicadas</b><small>2.º DAW · hace 1 min</small></div>
      </div>
      <div class="hv-card c-stat">
        <div class="mini-stack"><span style="background:linear-gradient(135deg,#5b8def,#22b8cf)">L</span><span style="background:linear-gradient(135deg,#0F9D72,#16b886)">D</span><span style="background:linear-gradient(135deg,#f0883e,#e0792b)">S</span></div>
        <div><b>+312 alumnos</b><small>activos este curso</small></div>
      </div>
      <div class="hv-card c-chat">
        <div class="ic" style="background:linear-gradient(135deg,#0F9D72,#16b886)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        <div><b>Laura · Secretaría</b><small>«Matrícula confirmada ✅»</small></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ LOGOS ============ -->
<section class="logos">
  <div class="container"><p>Centros de formación que ya confían en AulaPro</p></div>
  <div class="marquee">
    <div class="marquee-track" id="logoTrack">
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01"/></svg>Centro Avanza</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M3 12h18"/></svg>FP Mediterráneo</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 12 10-9 10 9M5 10v10h14V10"/></svg>Aula Norte</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 19 7 19 17 12 22 5 17 5 7"/></svg>Campus Select</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5l8 6 8-6v14"/></svg>FormaTech</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 4v16M16 4v16"/></svg>IES Digital</span>
      <span class="logo-mark"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 6v6c0 5 8 8 8 8s8-3 8-8V6z"/></svg>Forma Élite</span>
    </div>
  </div>
</section>

<!-- ============ FEATURES ============ -->
<section class="sec" id="funciones">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Funcionalidades</span>
      <h2>Todo lo que tu centro necesita, sin instalar nada.</h2>
      <p>Lo esencial para tu centro de FP, listo desde el primer día.</p>
    </div>


    <div class="feat-big">
      <article class="fcard">
        <div class="glow-corner"></div>
        <div class="ficon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13l2 2 4-4"/></svg></div>
        <h3>Admisiones inteligentes</h3>
        <p>Pre-matrícula online: convierte a un aspirante en alumno con un clic, con su cuenta y correo listos.</p>
        <span class="tag">Pre-matrícula · Conversión en 1 clic</span>
      </article>
      <article class="fcard">
        <div class="glow-corner"></div>
        <div class="ficon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M3 4h18v10a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3z"/><path d="M9 9l2 2 4-4"/></svg></div>
        <h3>Retos y proyectos</h3>
        <p>Publica retos con documentos adjuntos y recibe las entregas de tus alumnos, todo ordenado.</p>
        <span class="tag">Varios archivos · Descarga en bloque</span>
      </article>
      <article class="fcard">
        <div class="glow-corner"></div>
        <div class="ficon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
        <h3>Chat en tiempo real</h3>
        <p>Mensajería instantánea entre dirección, profesores y alumnos. La comunicación del centro, centralizada.</p>
        <span class="tag">Instantáneo · Avisos sonoros</span>
      </article>
      <article class="fcard">
        <div class="glow-corner"></div>
        <div class="ficon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></div>
        <h3>Notificaciones en tiempo real</h3>
        <p>Avisos al instante para que alumnos, profesores y familias estén siempre al día.</p>
        <span class="tag">Tiempo real · Sincronizado</span>
      </article>
    </div>

    <!-- dashboard mockup -->
    <div class="mock-wrap">
      <div class="shadow-floor"></div>

      <div class="float notif">
        <div class="row">
          <div class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></div>
          <div><b>Notas publicadas</b><small>Módulo de Programación · 2.º DAW</small></div>
        </div>
      </div>
      <div class="float chat">
        <div class="row">
          <div class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></div>
          <div><b>Laura · Secretaría</b><small>«Matrícula confirmada ✅»</small></div>
        </div>
      </div>

      <div class="window">
        <div class="win-bar">
          <div class="win-dots"><i></i><i></i><i></i></div>
          <div class="win-url">🔒 app.aulapro.es / <b>panel/director</b></div>
        </div>
        <div class="app">
          <aside class="app-side">
            <div class="s-brand"><svg class="logo"><use href="#ap-logo"/></svg>AulaPro</div>
            <span class="s-label">Centro</span>
            <a class="active"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>Panel</a>
            <a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13A4 4 0 0 1 16 11"/></svg>Alumnos</a>
            <a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>Ciclos y módulos</a>
            <a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 15h6"/></svg>Admisiones</a>
            <span class="s-label">Centro</span>
            <a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>Pagos</a>
            <a><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>Mensajes</a>
            <div class="s-foot"><div class="av"></div><div><b>Marta R.</b><small>Directora</small></div></div>
          </aside>
          <main class="app-main">
            <div class="app-top">
              <div><h3>Buenos días, Marta 👋</h3><div class="sub">Resumen del curso 2025/2026</div></div>
              <span class="pill">● En directo</span>
            </div>
            <div class="kpis">
              <div class="kpi"><div class="t"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></i>Alumnos activos</div><div class="v">312</div><div class="d">▲ 8% este mes</div></div>
              <div class="kpi"><div class="t"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></i>Pre-matrículas</div><div class="v">47</div><div class="d">▲ 12 sin revisar</div></div>
              <div class="kpi"><div class="t"><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></i>Cobros del mes</div><div class="v">96%</div><div class="d warn">3 pagos pendientes</div></div>
            </div>
            <div class="panel-grid">
              <div class="card">
                <div class="ch"><b>Matrículas por mes</b><span>Curso actual</span></div>
                <div class="chart">
                  <div class="bar alt" style="height:38%" data-m="Sep"></div>
                  <div class="bar alt" style="height:52%" data-m="Oct"></div>
                  <div class="bar alt" style="height:46%" data-m="Nov"></div>
                  <div class="bar alt" style="height:64%" data-m="Dic"></div>
                  <div class="bar alt" style="height:58%" data-m="Ene"></div>
                  <div class="bar" style="height:88%" data-m="Feb"></div>
                </div>
              </div>
              <div class="card">
                <div class="ch"><b>Actividad reciente</b></div>
                <div class="feed">
                  <div class="it"><div class="ic g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><p><b>Nuevo alumno</b> convertido desde admisiones<br><small>hace 4 min</small></p></div>
                  <div class="it"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></div><p>Boletín PDF generado · <b>1.º SMR</b><br><small>hace 22 min</small></p></div>
                  <div class="it"><div class="ic y"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></div><p>Pago recibido · <b>149 €</b> mensualidad<br><small>hace 1 h</small></p></div>
                </div>
              </div>
            </div>
          </main>
        </div>
      </div>
    </div>

    <div class="spotlight">
      <article class="spot">
        <div class="spot-copy">
          <span class="spot-tag">Comunicación automática</span>
          <h3>Las notas llegan al correo, solas.</h3>
          <p>Cuando un profesor publica una nota, el alumno y su familia reciben un correo al instante con la calificación y el boletín.</p>
          <ul class="spot-list">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Envío automático al publicar</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Boletín en PDF adjunto</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Con la imagen de tu centro</li>
          </ul>
        </div>
        <div class="spot-visual">
          <div class="email">
            <div class="email-head">
              <svg class="logo"><use href="#ap-logo"/></svg>
              <div><b>AulaPro · Centro Avanza</b><small>notificaciones@aulapro.es</small></div>
            </div>
            <div class="email-body">
              <div class="email-subject">✉ Nueva calificación publicada · Programación</div>
              <p>Hola familia, Diego ha obtenido una nueva nota en el módulo de <b>Programación (2.º DAW)</b>.</p>
              <div class="email-grade"><span>Calificación</span><b>9,0</b></div>
              <div class="email-attach"><span class="fic">PDF</span><div><b>Boletin_Diego.pdf</b><small>Boletín oficial del centro</small></div></div>
              <span class="email-btn">Ver en AulaPro</span>
            </div>
          </div>
        </div>
      </article>

      <article class="spot reverse">
        <div class="spot-copy">
          <span class="spot-tag">Confianza de las familias</span>
          <h3>Control parental para padres y tutores.</h3>
          <p>Las familias siguen las notas, la asistencia y los avisos de su hijo o hija desde su propio espacio.</p>
          <ul class="spot-list">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Acceso privado por alumno</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Avisos de notas y faltas</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>Contacto directo con el tutor</li>
          </ul>
        </div>
        <div class="spot-visual">
          <div class="parent">
            <div class="parent-top">
              <div class="p-av">D</div>
              <div class="p-id"><b>Diego Martín</b><small>1.º DAW · Tu hijo</small></div>
              <span class="p-pill">Asistencia 98%</span>
            </div>
            <div class="p-rows">
              <div class="p-row"><span>Programación</span><b class="ok">9,0</b></div>
              <div class="p-row"><span>Bases de datos</span><b class="ok">8,5</b></div>
              <div class="p-row"><span>Entornos de desarrollo</span><b class="ok">7,8</b></div>
            </div>
            <div class="p-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>Nueva nota publicada · hace 2 min</div>
          </div>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ============ ROLE PREVIEW ============ -->
<section class="sec sec-soft" id="roles">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Una vista para cada rol</span>
      <h2>La información correcta, para cada persona.</h2>
      <p>Cada usuario ve solo lo que necesita.</p>
    </div>

    <div class="roles-tabs" id="rolesTabs">
      <button class="active" data-role="director">Director</button>
      <button data-role="profesor">Profesor</button>
      <button data-role="alumno">Alumno</button>
    </div>

    <div class="role-stage">
      <!-- Director -->
      <div class="role-panel active" data-role="director">
        <div class="role-split">
          <div class="role-copy">
            <h3>Visión completa del centro</h3>
            <p>Estadísticas, admisiones, cobros y comunicación en un único panel.</p>
            <ul>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Estadísticas de matrícula, ocupación y cobros en directo</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Panel modular con feature toggles para activar módulos</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Informes y boletines exportables a PDF con un clic</li>
            </ul>
          </div>
          <div class="window">
            <div class="win-bar"><div class="win-dots"><i></i><i></i><i></i></div><div class="win-url"><b>panel/director</b> · estadísticas</div></div>
            <div class="app-main" style="background:#fff">
              <div class="app-top"><div><h3>Cuadro de mando</h3><div class="sub">Indicadores clave del curso</div></div><span class="pill">2025/2026</span></div>
              <div class="kpis">
                <div class="kpi"><div class="t">Ingresos mes</div><div class="v">18.4k €</div><div class="d">▲ 9%</div></div>
                <div class="kpi"><div class="t">Ocupación</div><div class="v">91%</div><div class="d">▲ 4%</div></div>
                <div class="kpi"><div class="t">Aspirantes</div><div class="v">47</div><div class="d warn">12 nuevos</div></div>
              </div>
              <div class="card">
                <div class="ch"><b>Matrículas por ciclo</b><span>Top 4</span></div>
                <div class="chart">
                  <div class="bar" style="height:82%" data-m="DAW"></div>
                  <div class="bar alt" style="height:64%" data-m="SMR"></div>
                  <div class="bar alt" style="height:55%" data-m="ASIR"></div>
                  <div class="bar alt" style="height:40%" data-m="DAM"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Profesor -->
      <div class="role-panel" data-role="profesor">
        <div class="role-split">
          <div class="role-copy">
            <h3>Menos papeleo, más docencia</h3>
            <p>Gestiona tus módulos, califica y publica retos para tus alumnos.</p>
            <ul>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Calificaciones y evaluaciones por módulo</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Retos con documentos adjuntos y entregas de alumnos</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Aviso automático al alumnado al publicar notas</li>
            </ul>
          </div>
          <div class="window">
            <div class="win-bar"><div class="win-dots"><i></i><i></i><i></i></div><div class="win-url"><b>panel/profesor</b> · calificaciones</div></div>
            <div class="app-main" style="background:#fff">
              <div class="app-top"><div><h3>Programación · 2.º DAW</h3><div class="sub">28 alumnos · evaluación 2</div></div><span class="pill">Borrador</span></div>
              <div class="card" style="margin-bottom:13px">
                <div class="ch"><b>Entregas del reto «Proyecto web»</b><span>22/28</span></div>
                <div class="feed">
                  <div class="it"><div class="ic g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><p><b>Lucía G.</b> — entregado · 9,2<br><small>2 archivos · ZIP</small></p></div>
                  <div class="it"><div class="ic g"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><p><b>Diego M.</b> — entregado · 8,5<br><small>3 archivos</small></p></div>
                  <div class="it"><div class="ic y"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v4l3 2"/></svg></div><p><b>Sara P.</b> — pendiente de corregir<br><small>hace 1 h</small></p></div>
                </div>
              </div>
              <button class="btn btn-primary btn-block">Publicar notas y notificar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Alumno -->
      <div class="role-panel" data-role="alumno">
        <div class="role-split">
          <div class="role-copy">
            <h3>Todo tu curso, en tu bolsillo</h3>
            <p>Consulta notas, recibe avisos y entrega retos desde el móvil.</p>
            <ul>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Notas y boletines disponibles al publicarse</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Mensajería directa con profesores y secretaría</li>
              <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Consulta del estado de su pre-matrícula por DNI</li>
            </ul>
          </div>
          <div class="window">
            <div class="win-bar"><div class="win-dots"><i></i><i></i><i></i></div><div class="win-url"><b>panel/alumno</b> · mis notas</div></div>
            <div class="app-main" style="background:#fff">
              <div class="app-top"><div><h3>Hola, Diego 👋</h3><div class="sub">1.º DAW · Evaluación 2</div></div><span class="pill">Media 8,4</span></div>
              <div class="kpis">
                <div class="kpi"><div class="t">Programación</div><div class="v">9,0</div><div class="d">Aprobado</div></div>
                <div class="kpi"><div class="t">Bases de datos</div><div class="v">8,5</div><div class="d">Aprobado</div></div>
                <div class="kpi"><div class="t">Entornos</div><div class="v">7,8</div><div class="d">Aprobado</div></div>
              </div>
              <div class="card">
                <div class="ch"><b>Próximas entregas</b></div>
                <div class="feed">
                  <div class="it"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><p>Reto <b>«Proyecto web»</b> · viernes<br><small>Programación</small></p></div>
                  <div class="it"><div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><p>Examen <b>Bases de datos</b> · lunes<br><small>Aula 204</small></p></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ STEPS ============ -->
<section class="sec" id="como">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Cómo funciona</span>
      <h2>Tu centro en marcha en 3 pasos.</h2>
      <p>Sin instalaciones ni complicaciones. Todo funciona en la nube y lo dejas listo en una tarde.</p>
    </div>
    <div class="steps">
      <div class="step"><div class="num">01</div><div class="line"></div><h3>Configura tu centro</h3><p>Crea ciclos, módulos y asigna docentes en minutos.</p></div>
      <div class="step"><div class="num">02</div><div class="line"></div><h3>Matricula a tus alumnos</h3><p>Importa tus datos o abre la pre-matrícula online.</p></div>
      <div class="step"><div class="num">03</div><h3>Gestiona todo en un panel</h3><p>Notas, pagos y comunicación, todo en un panel.</p></div>
    </div>
  </div>
</section>

<!-- ============ VENTAJAS / STATS ============ -->
<section class="sec sec-soft" id="ventajas">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Por qué AulaPro</span>
      <h2>Soluciones directas a los problemas de gestión más comunes.</h2>
    </div>
    <div class="stats">
      <div class="stat"><div class="big">−70%</div><h3>Menos tareas manuales</h3><p>Menos papeleo, más tiempo para formar.</p></div>
      <div class="stat"><div class="big">100%</div><h3>En la nube y centralizado</h3><p>Tus datos seguros y accesibles desde cualquier lugar.</p></div>
      <div class="stat"><div class="big">3</div><h3>Roles claros y seguros</h3><p>Director, profesor y alumno, cada uno con su acceso.</p></div>
    </div>
  </div>
</section>

<!-- ============ PRICING ============ -->
<section class="sec" id="precios">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Planes</span>
      <h2>Un plan para cada tamaño de centro.</h2>
      <p>Sin permanencia. Cambia o cancela cuando quieras. Prueba gratuita de 14 días.</p>
    </div>
    <div class="price-toggle">
      <span class="on" id="lblM">Mensual</span>
      <button class="switch" id="priceSwitch" aria-label="Cambiar facturación"><i></i></button>
      <span id="lblA">Anual</span>
      <span class="save-pill">Ahorra 20%</span>
    </div>
    <div class="plans">
      <article class="plan">
        <h3>Plan Académico</h3>
        <p class="pdesc">Para centros que quieren digitalizar la gestión de notas y comunicación.</p>
        <div class="price"><span class="amt" data-m="49" data-a="39">49 €</span><span class="per">/mes</span></div>
        <div class="cap">Hasta 150 alumnos matriculados</div>
        <ul class="plan-feats">
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Ciclos, módulos y docentes</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Calificaciones y evaluaciones</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Retos y seguimiento académico</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Mensajería interna y anuncios</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Soporte por email</li>
        </ul>
        <a class="btn btn-soft btn-block btn-lg" href="#empezar">Empezar gratis</a>
      </article>
      <article class="plan featured">
        <span class="ribbon">★ Más popular</span>
        <h3>Plan Completo</h3>
        <p class="pdesc">Gestión integral del centro: académico, económico y administrativo.</p>
        <div class="price"><span class="amt" data-m="89" data-a="71">89 €</span><span class="per">/mes</span></div>
        <div class="cap">Alumnos sin límite</div>
        <ul class="plan-feats">
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Todo el Plan Académico incluido</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Módulo de pagos y matrícula</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Admisiones y gestión de TFG</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Panel de director con estadísticas</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3"><path d="M20 6 9 17l-5-5"/></svg>Soporte prioritario</li>
        </ul>
        <a class="btn btn-primary btn-block btn-lg" href="#empezar">Empezar gratis</a>
      </article>
    </div>
    <p class="price-note">Precios por centro, IVA no incluido. ¿No sabes qué plan elegir? <a href="#empezar" style="color:var(--primary-strong);font-weight:600">Escríbenos y te ayudamos →</a></p>
  </div>
</section>


<!-- ============ FAQ ============ -->
<section class="sec" id="faq">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow"><span class="dot"></span>Preguntas frecuentes</span>
      <h2>Todo lo que quieres saber.</h2>
    </div>
    <div class="faq">
      <div class="qa"><button class="qa-q">¿Necesito instalar algo o saber de informática?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>No, nada. AulaPro funciona en la nube: solo necesitas tu ordenador, tablet o móvil con internet. Nosotros nos encargamos de todo el mantenimiento, las copias de seguridad y la seguridad por ti.</p></div></div>
      <div class="qa"><button class="qa-q">¿Funciona en móvil y tablet?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>Sí. La interfaz está totalmente adaptada a ordenador, tablet y móvil, con vistas optimizadas para cada rol (director, profesor y alumno).</p></div></div>
      <div class="qa"><button class="qa-q">¿Puedo migrar los datos de mi centro?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>Por supuesto. Te ayudamos a importar alumnos, ciclos y módulos durante la puesta en marcha para que empieces sin trabajo extra.</p></div></div>
      <div class="qa"><button class="qa-q">¿Es seguro y cumple el RGPD?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>Sí. Tus datos se guardan de forma segura y cada usuario solo ve la información que le corresponde según su rol. La privacidad de alumnos y familias es lo primero.</p></div></div>
      <div class="qa"><button class="qa-q">¿Hay permanencia o coste de alta?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>Ninguna. Pagas mes a mes (o anual con descuento) y puedes cambiar de plan o cancelar cuando quieras. La prueba de 14 días no requiere tarjeta.</p></div></div>
      <div class="qa"><button class="qa-q">¿El soporte es en español?<span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg></span></button><div class="qa-a"><p>Siempre. Nuestro equipo de soporte es íntegramente en español, por email en todos los planes y prioritario en el Plan Completo.</p></div></div>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="cta-final" id="empezar">
  <div class="container">
    <div class="cta-box">
      <div class="glow a"></div><div class="glow b"></div>
      <div class="cta-grid">
        <div>
          <h2>¿List@ para modernizar tu centro?</h2>
          <p>Empieza gratis durante 14 días o pídenos una demo. Te respondemos en menos de 24 horas.</p>
          <ul class="cta-points">
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>Sin tarjeta ni permanencia</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>Puesta en marcha guiada</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>Soporte en español</li>
          </ul>
        </div>
        <form class="form" id="demoForm" action="controladores/contacto_landing.php" method="POST" novalidate>
          <div class="two">
            <div class="fr"><label>Nombre y apellidos</label><input type="text" name="nombre" placeholder="Tu nombre" required></div>
            <div class="fr"><label>Correo de contacto</label><input type="email" name="email" placeholder="nombre@centro.es" required></div>
          </div>
          <div class="fr"><label>Nombre del centro</label><input type="text" name="centro" placeholder="Tu centro de FP" required></div>
          <div class="fr"><label>Plan de interés</label>
            <select name="plan">
              <option value="">-- Seleccionar --</option>
              <option value="Plan Académico">Plan Académico</option>
              <option value="Plan Completo">Plan Completo</option>
              <option value="Duda General">No lo sé todavía</option>
            </select>
          </div>
          <div class="fr"><label>Dudas o comentarios</label><textarea name="mensaje" placeholder="Cuéntanos sobre tu centro..."></textarea></div>
          <button type="submit" class="btn btn-primary btn-block btn-lg">Empezar gratis</button>
          <p class="form-note">Al enviar aceptas que te contactemos. No compartimos tus datos.</p>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <div class="container">
    <div class="foot-grid">
      <div class="foot-brand foot-col">
        <a class="brand" href="#inicio"><svg class="logo"><use href="#ap-logo"/></svg>Aula<b style="color:var(--primary)">Pro</b></a>
        <p>La plataforma integral para centros de Formación Profesional en España. Admisión, notas, pagos y comunicación en un solo lugar.</p>
      </div>
      <div class="foot-col">
        <h4>Plataforma</h4>
        <a href="#funciones">Funciones</a>
        <a href="#roles">Roles</a>
        <a href="#ventajas">Ventajas</a>
        <a href="#precios">Precios</a>
        <a href="#faq">Preguntas frecuentes</a>
      </div>
      <div class="foot-col">
        <h4>Legal</h4>
        <a href="vistas/legal/aviso-legal.php">Aviso Legal</a>
        <a href="vistas/legal/politica-de-privacidad.php">Privacidad</a>
        <a href="vistas/legal/politica-de-cookies.php">Cookies</a>
        <a href="vistas/legal/politica-de-gestion.php">Política de Gestión</a>
      </div>
      <div class="foot-col">
        <h4>Acceso</h4>
        <a href="vistas/login.php">Iniciar sesión</a>
        <?php if ($is_prematricula_enabled): ?>
        <a href="vistas/admisiones/pre-matricula.php">Pre-matrícula</a>
        <a href="vistas/admisiones/consultar.php">Consultar estado</a>
        <?php endif; ?>
        <a href="#empezar">Contacto</a>
      </div>
    </div>
    <div class="foot-bot">
      <span>© <?= date('Y') ?> AulaPro · Hecho en España</span>
      <div class="socials">
        <a href="#" aria-label="X"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.3 8.3L23 22h-6.8l-5.3-6.9L4.8 22H1.7l7.8-8.9L1 2h7l4.8 6.3zm-2.4 18h1.9L7.6 4H5.6z"/></svg></a>
        <a href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5M3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05C21.4 8.65 22 11 22 14.1V21h-4v-6.1c0-1.45-.03-3.3-2-3.3s-2.3 1.57-2.3 3.2V21h-4z"/></svg></a>
        <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a>
      </div>
    </div>
  </div>
</footer>

<!-- Script de carga dinámica de imágenes (basado en slots) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="public/js/landing/app.js"></script>

</body>
</html>
