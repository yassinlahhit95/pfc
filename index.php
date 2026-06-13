<?php
require_once __DIR__ . '/modelos/configuracion.php';
$cfg_main = obtenerConfiguracionCentro();
?>
<!DOCTYPE html>
<html lang="es" class="landing-page">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="AulaPro - La plataforma integral para la gestión de centros de Formación Profesional. Organiza notas, pagos y comunicación de forma eficiente.">
  <title>AulaPro - Control de Centros Educativos</title>
  <link rel="shortcut icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
  <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
  
  <!-- Preconnect to Font Domains -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Optimized Google Fonts Loading -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"></noscript>

  <!-- Inlined Styles to eliminate render-blocking (Total ~4KB) -->
  <style>
    html.landing-page{scroll-behavior:smooth;}body{margin:0;padding:0;font-family:'Gilroy',sans-serif;}.landing-body{background-color:#fafafa;color:#111827;line-height:1.6;}.contenedor{max-width:1100px;padding:0 24px;margin:0 auto;}.seccion{padding:80px 0;}.rejilla{display:flex;flex-wrap:wrap;gap:24px;}.tarjeta{background:#ffffff;padding:28px;border-radius:14px;border:1px solid #e5e7eb;transition:transform 0.3s ease,box-shadow 0.3s ease;}.tarjeta:hover{transform:translateY(-4px);box-shadow:0 8px 20px #e8e8ec;}.titulo-seccion{font-size:2.2rem;font-weight:700;margin-bottom:12px;text-align:center;}.descripcion-seccion{font-size:1.05rem;color:#4b5563;max-width:650px;margin:0 auto 40px;text-align:center;}.boton{font-size:1rem;font-weight:600;border-radius:10px;padding:13px 26px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;border:none;transition:all 0.3s ease;}.boton-principal-landing{background:#252260;color:#ffffff;}.boton-principal-landing:hover{background:#1e1b4b;transform:scale(1.02);}.boton-secundario-landing{background:transparent;color:#4f46e5;border:1.5px solid #e5e7eb;}.boton-secundario-landing:hover{background:#eef2ff;border-color:#4f46e5;}.hero-landing .boton-secundario-landing{color:#d9d9d9;border-color:#666666;}.hero-landing .boton-secundario-landing:hover{border-color:#aaaaaa;background:#333060;}.cabecera-landing{position:fixed;top:0;left:0;right:0;padding:14px 0;z-index:100;background:transparent;transition:0.3s ease-in-out;}.cabecera-landing.nav-scrolled{background:#ffffff;border-bottom:1px solid #e5e7eb;padding:8px 0;}.cabecera-contenedor-landing{display:flex;justify-content:space-between;align-items:center;}.logo-landing{height:55px;width:180px;display:flex;align-items:center;}.logo-imagen-landing{width:100%;height:100%;object-fit:contain;transition:0.3s ease-in-out;}.logo-landing-png{display:block;}.logo-landing-jpeg{display:none;}.nav-scrolled .logo-landing-png{display:none;}.nav-scrolled .logo-landing-jpeg{display:block;}.menu-landing{display:flex;gap:32px;align-items:center;}.menu-enlace-landing{text-decoration:none;color:inherit;font-weight:600;font-size:0.95rem;transition:color 0.3s ease;}.menu-enlace-landing:hover{color:#4f46e5;}.hero-landing{background:linear-gradient(rgba(37,34,96,0.92),rgba(37,34,96,0.92)),url('public/imagenes/fondo.webp');background-size:cover;background-position:center;color:#ffffff;padding:160px 0 100px;text-align:center;}.hero-badge{display:inline-block;padding:6px 14px;background:rgba(255,255,255,0.1);border-radius:30px;font-size:0.85rem;font-weight:600;margin-bottom:20px;letter-spacing:0.5px;}.titulo-hero-landing{font-size:3.5rem;font-weight:800;line-height:1.15;margin-bottom:24px;}.titulo-hero-landing span{color:#818cf8;}.subtitulo-hero-landing{font-size:1.2rem;color:#d1d5db;max-width:700px;margin:0 auto 32px;}.hero-botones-landing{display:flex;justify-content:center;gap:16px;margin-bottom:24px;}.hero-motivacion{font-size:0.9rem;color:#9ca3af;font-style:italic;}.caracteristicas-rejilla-landing{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}.tarjeta-titulo-landing{font-size:1.25rem;font-weight:700;margin-bottom:12px;}.tarjeta-texto-landing{font-size:0.95rem;color:#4b5563;}.visual-landing{background:#f3f4f6;text-align:center;}.video-demo-landing{width:100%;max-width:900px;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,0.15);border:8px solid #ffffff;}.beneficios-rejilla-landing{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}.beneficio-numero-landing{font-size:2.5rem;font-weight:900;color:#e5e7eb;margin-bottom:12px;}.planes-landing{background:#ffffff;}.billing-toggle-wrapper{display:flex;justify-content:center;align-items:center;gap:12px;margin-bottom:40px;}.billing-btn{background:none;border:none;padding:8px 16px;font-weight:600;cursor:pointer;border-radius:8px;transition:0.3s;}.billing-btn.activo{background:#252260;color:#ffffff;}.ahorro-badge{font-size:0.75rem;background:#10b981;color:white;padding:2px 6px;border-radius:4px;margin-left:4px;}.planes-rejilla-landing{display:grid;grid-template-columns:repeat(2,1fr);gap:32px;max-width:900px;margin:0 auto 48px;}.tarjeta-plan-landing{position:relative;display:flex;flex-direction:column;}.tarjeta-plan-landing.destacado{border:2px solid #4f46e5;transform:scale(1.05);z-index:1;box-shadow:0 12px 30px rgba(79,70,229,0.15);}.plan-badge-popular{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:#4f46e5;color:white;padding:4px 12px;border-radius:20px;font-size:0.8rem;font-weight:700;text-transform:uppercase;}.plan-nombre-landing{font-size:1.5rem;font-weight:700;margin-bottom:8px;}.plan-precio-bloque{margin:24px 0;}.plan-precio{display:flex;align-items:baseline;justify-content:center;gap:4px;}.precio-simbolo{font-size:1.5rem;font-weight:600;}.precio-cantidad{font-size:3.5rem;font-weight:800;}.precio-periodo{color:#6b7280;}.plan-alumnos{font-size:0.9rem;color:#4b5563;margin-top:4px;}.plan-lista-landing{list-style:none;padding:0;margin:0 0 32px;text-align:left;}.plan-elemento-landing{padding:8px 0;display:flex;align-items:center;gap:10px;font-size:0.95rem;}.plan-elemento-landing::before{content:'✓';color:#10b981;font-weight:bold;}.ancho-total{width:100%;}.contacto-form-wrapper{max-width:700px;margin:0 auto;background:#f9fafb;padding:40px;border-radius:20px;}.form-fila-landing{display:flex;gap:20px;margin-bottom:20px;}.form-campo-landing{flex:1;display:flex;flex-direction:column;gap:8px;text-align:left;}.form-campo-landing label{font-weight:600;font-size:0.9rem;}.form-campo-landing input,.form-campo-landing select,.form-campo-landing textarea{padding:12px;border:1px solid #e5e7eb;border-radius:8px;font-size:0.95rem;}.pie-pagina-landing{padding:40px 0;background:#111827;color:#9ca3af;text-align:center;}.menu-boton-landing{display:none;width:40px;height:40px;background:none;border:none;cursor:pointer;position:relative;}
    @media (max-width:768px){.titulo-hero-landing{font-size:2.4rem;}.caracteristicas-rejilla-landing,.beneficios-rejilla-landing,.planes-rejilla-landing{grid-template-columns:1fr;}.planes-rejilla-landing{max-width:400px;}.tarjeta-plan-landing.destacado{transform:none;}.form-fila-landing{flex-direction:column;}.menu-landing{display:none;}}
  </style>
</head>
<body class="landing-body">

  <header class="cabecera-landing" id="cabecera">
    <div class="contenedor cabecera-contenedor-landing">
      <a href="#" class="logo-landing">
        <img src="public/imagenes/aulapro.png" alt="Logo" class="logo-imagen-landing logo-landing-png">
        <img src="public/imagenes/logo2.png" alt="Logo" class="logo-imagen-landing logo-landing-jpeg">
      </a>
      <nav class="menu-landing" id="menu">
        <a href="#inicio" class="menu-enlace-landing">Inicio</a>
        <a href="#caracteristicas" class="menu-enlace-landing">Funciones</a>
        <a href="#beneficios" class="menu-enlace-landing">Ventajas</a>
        <a href="#planes" class="menu-enlace-landing">Precios</a>
        <a href="vistas/login.php" class="boton boton-principal-landing btn-acceso-nav">Acceso</a>
      </nav>
      <button class="menu-boton-landing" id="menu-boton" aria-label="Abrir menú">
        <span class="menu-icono-landing"></span>
      </button>
    </div>
  </header>

  <main>
    <section id="inicio" class="hero-landing animar">
      <div class="contenedor">
        <div class="hero-badge">Proyecto de Gestión para Centros de FP</div>
        <h1 class="titulo-hero-landing">Control total de tu centro<br><span>de forma sencilla y rápida.</span></h1>
        <p class="subtitulo-hero-landing">AulaPro ayuda a organizar las notas, los pagos de alumnos y la comunicación entre profesores y directores sin complicaciones.</p>
        
        <div class="hero-botones-landing">
          <a href="#planes" class="boton boton-principal-landing">Ver Planes</a>
          
          <?php if ($cfg_main['feature_prematricula']): ?>
            <a href="vistas/admisiones/pre-matricula.php" class="boton boton-secundario-landing" style="border-color: #4f46e5; color: #4f46e5;">Pre-matrícula</a>
            <a href="vistas/admisiones/consultar.php" class="boton boton-secundario-landing">Consultar Estado</a>
          <?php else: ?>
            <a href="#visual" class="boton boton-secundario-landing">Demostración</a>
          <?php endif; ?>

        </div>
        <p class="hero-motivacion">Optimiza el tiempo en las tareas administrativas para centrarte en la formación.</p>
      </div>
    </section>

    <section id="caracteristicas" class="seccion caracteristicas-landing animar">
      <div class="contenedor">
        <h2 class="titulo-seccion">Funcionalidades principales</h2>
        <p class="descripcion-seccion">Herramientas diseñadas para cubrir las necesidades básicas de cualquier centro formativo.</p>
        <div class="rejilla caracteristicas-rejilla-landing">
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Gestión de Alumnos</h3>
            <p class="tarjeta-texto-landing">Fichas individuales con historial de notas y estado de sus matrículas.</p>
          </div>
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Módulos y Docentes</h3>
            <p class="tarjeta-texto-landing">Organización de las cargas lectivas y asignación de profesores por módulo.</p>
          </div>
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Ciclos de FP</h3>
            <p class="tarjeta-texto-landing">Configuración personalizada de ciclos de Grado Medio y Superior.</p>
          </div>
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Notificaciones</h3>
            <p class="tarjeta-texto-landing">Envío de avisos automáticos a los estudiantes cuando se publican notas.</p>
          </div>
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Control Económico</h3>
            <p class="tarjeta-texto-landing">Registro de mensualidades y seguimiento de pagos pendientes.</p>
          </div>
          <div class="tarjeta">
            <h3 class="tarjeta-titulo-landing">Acceso Multiplataforma</h3>
            <p class="tarjeta-texto-landing">Interfaz adaptada para su uso en tablets y dispositivos móviles.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="visual" class="seccion visual-landing animar">
      <div class="contenedor">
        <h2 class="titulo-seccion">Interfaz intuitiva</h2>
        <p class="descripcion-seccion">Vistas personalizadas según el rol de usuario: Director, Profesor o Alumno.</p>
        <video autoplay loop muted controls playsinline preload="auto" class="video-demo-landing">
          <source src="public/videos/intro.mp4" type="video/mp4">
        </video>
      </div>
    </section>

    <section id="beneficios" class="seccion beneficios-landing animar">
      <div class="contenedor">
        <h2 class="titulo-seccion">¿Por qué elegir esta plataforma?</h2>
        <p class="descripcion-seccion">Soluciones directas para los problemas de gestión más comunes.</p>
        <div class="rejilla beneficios-rejilla-landing">
          <div class="tarjeta">
            <div class="beneficio-numero-landing">01</div>
            <h3 class="tarjeta-titulo-landing">Centralización</h3>
            <p class="tarjeta-texto-landing">Todos los datos en una única base de datos segura y accesible.</p>
          </div>
          <div class="tarjeta">
            <div class="beneficio-numero-landing">02</div>
            <h3 class="tarjeta-titulo-landing">Eficiencia</h3>
            <p class="tarjeta-texto-landing">Reducción de tareas manuales repetitivas mediante automatización.</p>
          </div>
          <div class="tarjeta">
            <div class="beneficio-numero-landing">03</div>
            <h3 class="tarjeta-titulo-landing">Roles Claros</h3>
            <p class="tarjeta-texto-landing">Permisos segmentados para mantener la privacidad de la información.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="planes" class="seccion planes-landing animar">
      <div class="contenedor">
        <h2 class="titulo-seccion">Selecciona un Plan</h2>
        <p class="descripcion-seccion">Diferentes niveles de gestión según el tamaño de tu centro formativo.</p>

        <div class="billing-toggle-wrapper">
          <button class="billing-btn activo" id="btn-mensual">Mensual</button>
          <button class="billing-btn" id="btn-anual">Anual <span class="ahorro-badge">−20%</span></button>
        </div>

        <div class="planes-rejilla-landing">
          <div class="tarjeta tarjeta-plan-landing">
            <h3 class="plan-nombre-landing">Plan Académico</h3>
            <p class="plan-descripcion">Para centros que quieren digitalizar la gestión de notas y comunicación.</p>
            <div class="plan-precio-bloque">
              <div class="plan-precio">
                <span class="precio-simbolo">€</span>
                <span class="precio-cantidad" data-mensual="49" data-anual="39">49</span>
                <span class="precio-periodo">/mes</span>
              </div>
              <p class="plan-alumnos">Hasta 150 alumnos matriculados</p>
            </div>
            <ul class="plan-lista-landing">
              <li class="plan-elemento-landing">Ciclos, Módulos y Docentes</li>
              <li class="plan-elemento-landing">Calificaciones y evaluaciones</li>
              <li class="plan-elemento-landing">Retos y seguimiento académico</li>
              <li class="plan-elemento-landing">Mensajería interna y anuncios</li>
              <li class="plan-elemento-landing">Soporte por email</li>
            </ul>
            <a href="#planes" class="boton boton-secundario-landing ancho-total" data-plan="Plan Académico">Solicitar info</a>
          </div>

          <div class="tarjeta tarjeta-plan-landing destacado">
            <span class="plan-badge-popular">Más Popular</span>
            <h3 class="plan-nombre-landing">Plan Completo</h3>
            <p class="plan-descripcion">Gestión integral del centro: académico, económico y administrativo.</p>
            <div class="plan-precio-bloque">
              <div class="plan-precio">
                <span class="precio-simbolo">€</span>
                <span class="precio-cantidad" data-mensual="89" data-anual="69">89</span>
                <span class="precio-periodo">/mes</span>
              </div>
              <p class="plan-alumnos">Alumnos sin límite</p>
            </div>
            <ul class="plan-lista-landing">
              <li class="plan-elemento-landing">Todo el Plan Académico incluido</li>
              <li class="plan-elemento-landing">Módulo de pagos y matrícula</li>
              <li class="plan-elemento-landing">Gestión de TFG</li>
              <li class="plan-elemento-landing">Panel de director con estadísticas</li>
              <li class="plan-elemento-landing">Soporte prioritario</li>
            </ul>
            <a href="#planes" class="boton boton-principal-landing ancho-total" data-plan="Plan Completo">Solicitar info</a>
          </div>
        </div>

        <p class="planes-contacto-texto">¿Tienes dudas sobre qué plan se adapta mejor? Escríbenos y te ayudamos.</p>

        <div class="contacto-form-wrapper">
          <form id="form-contacto" class="contacto-form-landing">
            <input type="text" name="website" class="campo-honeypot" tabindex="-1" aria-hidden="true" title="ignore">
            <div class="form-fila-landing">
              <div class="form-campo-landing">
                <label for="cf-nombre">Nombre y Apellidos</label>
                <input type="text" id="cf-nombre" name="nombre" placeholder="Tu nombre">
              </div>
              <div class="form-campo-landing">
                <label for="cf-email">Correo de contacto</label>
                <input type="email" id="cf-email" name="email" placeholder="email@ejemplo.com">
              </div>
            </div>
            <div class="form-fila-landing">
              <div class="form-campo-landing">
                <label for="cf-centro">Nombre del Centro</label>
                <input type="text" id="cf-centro" name="centro" placeholder="IES / Instituto">
              </div>
              <div class="form-campo-landing">
                <label for="cf-plan">Plan de interés</label>
                <select id="cf-plan" name="plan">
                  <option value="">-- Seleccionar --</option>
                  <option value="Plan Académico">Plan Académico</option>
                  <option value="Plan Completo">Plan Completo</option>
                </select>
              </div>
            </div>
            <div class="form-campo-landing">
              <label for="cf-mensaje">Dudas o comentarios</label>
              <textarea id="cf-mensaje" name="mensaje" rows="4"></textarea>
            </div>
            <div class="form-acciones-landing">
              <button type="submit" class="boton boton-principal-landing" id="btn-enviar">Enviar datos</button>
            </div>
            <div id="form-feedback"></div>
          </form>
        </div>
      </div>
    </section>
  </main>

  <footer class="pie-pagina-landing">
    <div class="contenedor">
      <p>&copy; 2025/2026 AulaPro — Proyecto de Fin de Grado</p>
    </div>
  </footer>

  <script src="public/js/landing.js?v=1.1" defer></script>
</body>
</html>
