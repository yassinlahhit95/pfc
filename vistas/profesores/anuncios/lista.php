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

<div class="encabezado-pagina">
    <h1>TablÃ³n de Anuncios</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <?php if ($anuncios) { ?>
        <div class="lista-anuncios-completa">
            <?php foreach ($anuncios as $anuncio) { ?>
                <div class="anuncio-item-completo">
                    <div class="mb-10">
                        <label class="texto-negrita">TÃ­tulo del Anuncio:</label> 
                        <span class="color-primario texto-negrita"><?= strtoupper($anuncio['titulo'] ?? '') ?></span>
                    </div>

                    <div class="cuerpo-anuncio mb-15">
                        <label class="texto-negrita">Contenido:</label>
                        <div class="mt-5"><?= nl2br($anuncio['mensaje'] ?? '') ?></div>
                    </div>

                    <div class="texto-atenuado texto-pequeno">
                        <i class="fas fa-calendar-alt"></i> Disponible hasta: <?= date('d/m/Y', strtotime($anuncio['fechaExpiracion'] ?? 'now')) ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="texto-atenuado text-center p-20">No hay anuncios publicados en este momento.</p>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>




