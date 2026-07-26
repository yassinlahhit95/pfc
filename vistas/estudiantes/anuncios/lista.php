<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_anuncios');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$listaAnuncios = listarAnunciosPorRol('estudiantes');

$tituloDelPagina = "AULAPRO | ANUNCIOS";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>TABLA DE ANUNCIOS</h1>
</div>


<?php if (!empty($listaAnuncios)) { ?>
    <?php foreach ($listaAnuncios as $anuncio) {
        $esNuevo = strtotime($anuncio['fechaAnuncio']) >= strtotime('-3 days');
    ?>
        <div class="anuncio-item">
            <div class="anuncio-cabecera">
                <span class="anuncio-icono"><i class="fas fa-bullhorn"></i></span>
                <div class="anuncio-cabecera-texto">
                    <h3 class="anuncio-titulo"><?= Security::escapeHtml($anuncio['titulo']) ?></h3>
                    <span class="anuncio-fecha"><i class="fas fa-calendar-alt"></i> <?= Security::escapeHtml(date('d/m/Y', strtotime($anuncio['fechaAnuncio']))) ?></span>
                </div>
                <?php if ($esNuevo) { ?><span class="texto-dirigido">NUEVO</span><?php } ?>
            </div>
            <p class="anuncio-mensaje"><?= nl2br(Security::escapeHtml($anuncio['mensaje'])) ?></p>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="panel">
        <p class="texto-suave" style="text-align: center; padding: 20px;">No hay avisos publicados en este momento.</p>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>


