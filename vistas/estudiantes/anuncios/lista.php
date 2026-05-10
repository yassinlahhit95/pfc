<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$listaAnuncios = listarAnunciosPorRol('estudiantes');

$tituloDelPagina = "AULAPRO | ANUNCIOS";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>TABLÓN DE ANUNCIOS</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (!empty($listaAnuncios)) { ?>
    <?php foreach ($listaAnuncios as $anuncio) { ?>
        <div class="anuncio-item">
            <div class="titulo-tarjeta">
                <h3 class="anuncio-titulo"><?= strtoupper($anuncio['titulo']) ?></h3>
                <small class="texto-atenuado"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></small>
            </div>
            <div class="margen-arriba">
                <p class="line-height-16"><?= nl2br($anuncio['mensaje']) ?></p>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="tarjeta-blanca">
        <p class="texto-atenuado text-center p-20">No hay avisos publicados en este momento.</p>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
