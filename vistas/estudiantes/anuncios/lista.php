<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarAnunciosPorRol('estudiantes');

$tituloDelPagina = "Anuncios - Portal Estudiantes";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Tablón de Anuncios</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <?php if ($anuncios) { ?>
        <div class="lista-anuncios-completa">
            <?php foreach ($anuncios as $anuncio) { ?>
                <div class="anuncio-item-completo">
                    <div class="mb-10">
                        <label class="texto-negrita">Título del Anuncio:</label> 
                        <span class="color-primario texto-negrita"><?= strtoupper($anuncio['titulo']) ?></span>
                    </div>
                    
                    <div class="cuerpo-anuncio mb-15">
                        <label class="texto-negrita">Contenido:</label>
                       <span class="color-primario texto-negrita"><?= nl2br($anuncio['mensaje']) ?></span>
                    </div>

                    <div class="cuerpo-anuncio mb-15">
                        <label class="texto-negrita">Disponible hasta:</label>
                       <span class="color-primario texto-negrita"><?= date('d/m/Y', strtotime($anuncio['fechaExpiracion'])) ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="texto-atenuado text-center p-20">No hay anuncios publicados en este momento.</p>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>



