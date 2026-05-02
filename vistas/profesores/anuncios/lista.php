<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarAnunciosPorRol('profesores');

$tituloDelPagina = "Anuncios - Portal Profesores";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>TablÃ³n de Anuncios</h1>
</div>

<div class="tarjeta-blanca">
    <?php if ($anuncios) { ?>
        <div class="lista-anuncios-completa">
            <?php foreach ($anuncios as $anuncio) { ?>
                <div class="anuncio-item-completo">
                    <div class="mb-10">
                        <label class="texto-negrita">TÃ­tulo del Anuncio:</label> 
                        <span class="color-primario texto-negrita"><?php echo strtoupper($anuncio['titulo']); ?></span>
                    </div>

                    <div class="cuerpo-anuncio mb-15">
                        <label class="texto-negrita">Contenido:</label>
                        <div class="mt-5"><?php echo nl2br($anuncio['mensaje']); ?></div>
                    </div>

                    <div class="texto-atenuado texto-pequeno">
                        <i class="fas fa-calendar-alt"></i> Disponible hasta: <?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="texto-atenuado text-center p-20">No hay anuncios publicados en este momento.</p>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>

