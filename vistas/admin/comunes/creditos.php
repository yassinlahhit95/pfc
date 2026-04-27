<?php
session_start();
$titulo_pagina = "Huella Digital - Yassin Lahhit";
$seccion = 'creditos';
include_once "nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Fingerprint & Copyright</h1>
        <p class="subtitulo">Identificación oficial del desarrollador y autoría del sistema</p>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible alinear-centro separacion-grande">
        <div class="text-center" style="flex: 0 0 150px;">
            <div class="avatar-huella">
                YL
            </div>
        </div>
        <div class="flexible-rellenar">
            <h2 class="subtitulo-huella">Yassin Lahhit</h2>
            <p class="texto-atenuado">Full Stack Developer | Autor del TFG</p>
            <div class="mt-15 pb-15" style="border-top: 1px solid #eee;">
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
        <p class="texto-negrita mb-5">© <?php echo date('Y'); ?> Yassin Lahhit</p>
        <p class="texto-atenuado" style="font-size: 12px;">Todos los derechos reservados. Desarrollado con PHP, MySQL y Brevo API.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
