<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/anuncios.php";

$anuncios = listarTodosLosAnuncios();

$tituloDelPagina = "Anuncios - Portal Profesores";
$seccionActual = 'anuncios';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Tablón de Anuncios</h1>
</div>

<div class="tarjeta-blanca">
    <?php if ($anuncios) { ?>
        <div class="lista-anuncios">
            <?php foreach ($anuncios as $anuncio) { ?>
                <div class="anuncio-item-completo">
                    <h3 class="texto-azul"><?php echo $anuncio['titulo']; ?></h3>
                    <p class="texto-pequeno texto-atenuado">Publicado hasta: <?php echo $anuncio['fechaExpiracion']; ?></p>
                    <div class="mensaje-anuncio mt-10">
                        <?php echo $anuncio['mensaje']; ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="texto-atenuado">No hay anuncios publicados en este momento.</p>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>