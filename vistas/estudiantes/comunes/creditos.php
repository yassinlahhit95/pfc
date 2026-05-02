<?php
session_start();
$titulo_pagina = "Huella Digital - Yassin Lahhit";
$seccionActual = 'creditos';
include_once __DIR__ . "/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Fingerprint & Copyright</h1>
        <p class="subtitulo">Identificación oficial del desarrollador y autoría del sistema</p>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <div class="text-center avatar-contenedor">
            <div class="avatar-huella">
                YL
            </div>
        </div>
        <div class="flexible-rellenar">
            <h2 class="subtitulo-huella">Yassin Lahhit</h2>
            <p class="texto-atenuado">Full Stack Developer | Autor del TFG</p>
            <div class="mt-15 pb-15 borde-superior-suave">
                <p><strong>Propiedad Intelectual:</strong> Este software es un Trabajo de Fin de Grado original desarrollado para CPS Ibaiondo.</p>
                <p><strong>Versión del Sistema:</strong> 2.0.0 (Abril 2026)</p>
            </div>
        </div>
    </div>
    
    <div class="tarjeta-gris-suave mt-20">
        <h3 class="mt-0"><i class="fas fa-fingerprint"></i> Certificado de Autoría</h3>
        <p class="line-height-16">
            Queda certificada la autoría de <strong>Yassin Lahhit</strong> sobre el diseño de la base de datos, la arquitectura lógica, 
            el desarrollo del backend en PHP y la implementación del frontend de esta plataforma de gestión académica (PFC).
        </p>
        <p class="line-height-16">
            Cualquier reproducción o uso parcial de este sistema requiere el consentimiento explícito del autor.
        </p>
    </div>

    <div class="separador-huella mt-20">
        <p class="texto-negrita mb-5">© <?= date('Y') ?> Yassin Lahhit</p>
        <p class="texto-atenuado copyright-texto">Todos los derechos reservados. Desarrollado con PHP, MySQL y Brevo API.</p>
    </div>
</div>

<!-- ABOUT ME SECTION -->
<div class="seccion-about-me mt-40">
    <div class="contenedor-about">
        <div class="about-imagen-izq">
            <div class="silhueta-contenedor">
                <svg viewBox="0 0 300 400" xmlns="http://www.w3.org/2000/svg" class="silhueta-svg">
                    <!-- Silhouette design -->
                    <defs>
                        <linearGradient id="gradientSilhueta" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#ff6b9d;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#c6364f;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <!-- Background -->
                    <rect width="300" height="400" fill="url(#gradientSilhueta)"/>
                    <!-- Black rectangle for contrast -->
                    <rect x="150" y="0" width="150" height="400" fill="#1a1a1a"/>
                    <!-- Head -->
                    <circle cx="150" cy="80" r="35" fill="black"/>
                    <!-- Neck -->
                    <rect x="140" y="115" width="20" height="20" fill="black"/>
                    <!-- Torso -->
                    <ellipse cx="150" cy="180" rx="50" ry="70" fill="black"/>
                    <!-- Arms -->
                    <ellipse cx="100" cy="160" rx="20" ry="60" fill="black" transform="rotate(-30 100 160)"/>
                    <ellipse cx="200" cy="160" rx="20" ry="60" fill="black" transform="rotate(30 200 160)"/>
                    <!-- Left arm bent -->
                    <rect x="60" y="150" width="35" height="15" fill="black" transform="rotate(-25 77 157)"/>
                    <!-- Right arm bent -->
                    <rect x="205" y="150" width="35" height="15" fill="black" transform="rotate(25 222 157)"/>
                    <!-- Hand details -->
                    <circle cx="50" cy="170" r="8" fill="black"/>
                    <circle cx="250" cy="170" r="8" fill="black"/>
                    <!-- Legs -->
                    <rect x="125" y="250" width="18" height="130" fill="black"/>
                    <rect x="157" y="250" width="18" height="130" fill="black"/>
                    <!-- Shoes -->
                    <ellipse cx="134" cy="385" rx="12" ry="8" fill="black"/>
                    <ellipse cx="166" cy="385" rx="12" ry="8" fill="black"/>
                </svg>
            </div>
        </div>

        <div class="about-contenido">
            <h2 class="about-titulo">ABOUT ME</h2>
            <h3 class="about-subtitulo">YASSIN LAHHIT - DEVELOPER</h3>
            <p class="about-descripcion">
                Full Stack Developer especializado en la creación de soluciones web integradas. 
                Experiencia en desarrollo de sistemas académicos completos, diseño de bases de datos normalizadas, 
                implementación de APIs y desarrollo de interfaces responsivas. Comprometido con la calidad del código, 
                buenas prácticas de programación y entrega de soluciones eficientes y escalables.
            </p>
            
            <div class="about-redes-sociales">
                <a href="https://behance.net" target="_blank" class="enlace-red-social" title="Behance">
                    <i class="fas fa-behance"></i>
                </a>
                <a href="https://dribbble.com" target="_blank" class="enlace-red-social" title="Dribbble">
                    <i class="fas fa-dribbble"></i>
                </a>
                <a href="https://twitter.com" target="_blank" class="enlace-red-social" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://instagram.com" target="_blank" class="enlace-red-social" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://linkedin.com" target="_blank" class="enlace-red-social" title="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
