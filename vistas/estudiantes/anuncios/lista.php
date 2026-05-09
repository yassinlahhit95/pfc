<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/anuncios.php";

$listaAnuncios = listarAnunciosPorRol('estudiantes');

$tituloDelPagina = "AULAPRO | ANUNCIOS";
$seccionActual = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>TablÃ³n de Anuncios</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <?php if ($listaAnuncios) { ?>
        <div class="lista-avisos">
            <?php foreach ($listaAnuncios as $anuncio) { ?>
                <div class="aviso-item mb-25 pb-20 borde-abajo-gris">
                    <div class="disposicion-flexible alinear-centro espacio-entre-elementos mb-10">
                        <h2 class="texto-azul m-0"><?= strtoupper($anuncio['titulo']) ?></h2>
                        <span class="texto-atenuado texto-pequeno">
                            <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?>
                        </span>
                    </div>
                    
                    <div class="cuerpo-aviso mb-15">
                        <p class="line-height-16 texto-oscuro"><?= nl2br($anuncio['mensaje']) ?></p>
                    </div>

                    <div class="pie-aviso disposicion-flexible alinear-centro">
                        <span class="etiqueta-pequena color-secundario-suave">
                            <i class="fas fa-clock"></i> Disponible hasta: <?= date('d/m/Y', strtotime($anuncio['fechaExpiracion'])) ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="text-center p-40">
            <i class="fas fa-bullhorn fa-3x texto-atenuado mb-15"></i>
            <p class="texto-atenuado">No hay avisos publicados para estudiantes en este momento.</p>
        </div>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>

