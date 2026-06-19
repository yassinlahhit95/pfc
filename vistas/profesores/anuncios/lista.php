<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarAnunciosPorRol('profesores');

$tituloDelPagina = "AULAPRO | ANUNCIOS";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>TABLA DE ANUNCIOS</h1>
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
                    <p style="line-height: 1.6;"><?= Security::escapeHtml(nl2br($anuncio['mensaje'] ?? '')) ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="panel">
        <p class="texto-suave" style="text-align: center; padding: 20px;">No hay anuncios publicados en este momento.</p>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>


