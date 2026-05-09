<?php
session_start();
$titulo_pagina = "AULAPRO | HUELLA DIGITAL";
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
        <div class="flex-0-0-150 texto-centrado">
            <div class="avatar-huella">
                YL
            </div>
        </div>
        <div class="flexible-rellenar">
            <h2 class="color-primario mb-5">Yassin Lahhit</h2>
            <p class="texto-atenuado">Full Stack Developer | Autor del TFG</p>
            <div class="mt-15 border-top-suave pt-15">
                <p><strong>Propiedad Intelectual:</strong> Este software es un Trabajo de Fin de Grado original desarrollado para CPS Ibaiondo.</p>
                <p><strong>Versión del Sistema:</strong> 2.0.0 (Abril 2026)</p>
            </div>
        </div>
    </div>
    
    <div class="tarjeta-gris-suave mt-20">
        <h3 class="mt-0"><i class="fas fa-fingerprint"></i> Certificado de Autoría</h3>
        <p class="lh-1-4">
            Queda certificada la autoría de <strong>Yassin Lahhit</strong> sobre el diseño de la base de datos, la arquitectura lógica, 
            el desarrollo del backend en PHP y la implementación del frontend de esta plataforma de gestión académica (PFC).
        </p>
        <p class="lh-1-4">
            Cualquier reproducción o uso parcial de este sistema requiere el consentimiento explícito del autor.
        </p>
    </div>

    <div class="margen-arriba texto-centrado border-top-dashed pt-20">
        <p class="texto-negrita mb-5">Â© <?php echo date('Y'); ?> Yassin Lahhit</p>
        <p class="texto-atenuado font-12">Todos los derechos reservados. Desarrollado con PHP, MySQL y Brevo API.</p>
    </div>
</div>

<?php include 'footer.php'; ?>

