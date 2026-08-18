<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_anuncios');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarAnunciosPorRol('profesores');

$titulo_pagina = "Anuncios";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Anuncios</h1>
        <p class="subtitulo-encabezado">Comunicados publicados por el centro</p>
    </div>
</div>

<?php if (!empty($anuncios)) { ?>
    <?php foreach ($anuncios as $anuncio) { ?>
        <div class="anuncio-item">
            <div class="anuncio-contenido">
                <div class="titulo-tarjeta">
                    <h3 class="anuncio-titulo"><?= Security::escapeHtml(strtoupper($anuncio['titulo'] ?? '')) ?></h3>
                    <span class="texto-suave"><i class="fas fa-calendar-alt"></i> <?= Security::escapeHtml(date('d/m/Y', strtotime($anuncio['fechaAnuncio']))) ?></span>
                </div>
                <div class="margen-arriba">
                    <p style="line-height: 1.6;"><?= nl2br(Security::escapeHtml($anuncio['mensaje'] ?? '')) ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="panel">
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-bullhorn"></i></div>
            <div class="panel-vacio-titulo">Sin anuncios</div>
            <div class="panel-vacio-desc">No hay anuncios publicados en este momento.</div>
        </div>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
