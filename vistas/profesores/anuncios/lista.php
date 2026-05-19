<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarAnunciosPorRol('profesores');

$tituloDelPagina = "AULAPRO | ANUNCIOS";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>TABLA DE ANUNCIOS</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<?php if (!empty($anuncios)) { ?>
    <?php foreach ($anuncios as $anuncio) { ?>
        <div class="anuncio-item">
            <div class="anuncio-contenido">
                <div class="titulo-tarjeta">
                    <h3 class="anuncio-titulo"><?= strtoupper($anuncio['titulo'] ?? '') ?></h3>
                    <span class="texto-suave"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></span>
                </div>
                <div class="margen-arriba">
                    <p style="line-height: 1.6;"><?= nl2br($anuncio['mensaje'] ?? '') ?></p>
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
