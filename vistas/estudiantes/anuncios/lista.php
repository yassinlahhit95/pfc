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
            <div class="anuncio-contenido">
                <div class="titulo-tarjeta">
                    <h3 class="anuncio-titulo"><?= mb_strtoupper($anuncio['titulo'], 'UTF-8') ?></h3>
                    <small class="texto-atenuado"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></small>
                </div>
                <div class="margen-arriba">
                    <p style="line-height: 1.6;"><?= nl2br($anuncio['mensaje']) ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="tarjeta-blanca">
        <p class="texto-atenuado" style="text-align: center; padding: 20px;">No hay avisos publicados en este momento.</p>
    </div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
