<?php
session_start();
$titulo_pagina = "Sobre el Proyecto - Yassin Lahhit";
$seccionActual = 'creditos';
include_once __DIR__ . "/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Sobre el Proyecto</h1>
        <p class="subtitulo">Identificación oficial del desarrollador y autoría del sistema</p>
    </div>
</div>

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

<!-- ABOUT ME SECTION -->
<div class="seccion-about-me mt-40">
    <div class="contenedor-about">
        <div class="about-imagen-izq">
            <div class="silhueta-contenedor">
                <img src="../../../public/imagenes/aulapro.png" alt="Imagen del aula del proyecto" class="silhueta-imagen">
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
            
            <div class="about-acciones mt-25">
                <a href="../../../public/uploads/pfc/memoria_tfg.pdf" download class="boton-primario">
                    <i class="fas fa-download"></i> Descargar Memoria TFG
                </a>
                <a href="https://github.com/yourusername/pfc-project" target="_blank" class="boton-secundario">
                    <i class="fab fa-github"></i> Ver en GitHub
                </a>
            </div>
            
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
            
            <div class="separador-huella mt-30">
                <p class="texto-negrita mb-5">© <?= date('Y') ?> Yassin Lahhit</p>
                <p class="texto-atenuado copyright-texto">Todos los derechos reservados. Desarrollado con PHP, MySQL y Brevo API.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>

