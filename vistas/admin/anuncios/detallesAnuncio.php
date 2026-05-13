<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/anuncios.php";

$idAnuncio = $_GET['idAnuncio'] ?? 0;
$anuncio = obtenerAnuncioPorId($idAnuncio);

if (!$anuncio) {
    $_SESSION['error'] = "El anuncio solicitado no existe.";
    header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

$titulo_pagina = "AULAPRO | DETALLES DEL ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>DETALLES DEL ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <div class="cabecera-detalles" style="margin-bottom: 20px;">
        <h2 class="texto-azul"><?= $anuncio['tituloAnuncio'] ?></h2>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Publicado</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Dirigido a</div>
        <div class="valor-detalle"><span class="estado-bolita activo-verde"><?= ucfirst($anuncio['dirigidoA']) ?></span></div>
    </div>

    <div class="margen-arriba">
        <div class="fila-detalle">
            <div class="etiqueta-detalle">Contenido</div>
            <div class="valor-detalle">
                <?php if (!empty($anuncio['contenidoAnuncio'])) { ?>
                    <?= $anuncio['contenidoAnuncio'] ?>
                <?php } else { ?>
                    <span class="texto-atenuado">Sin contenido.</span>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="margen-arriba-grande botones-accion">
        <a href="modificarAnuncios.php?idAnuncio=<?= $idAnuncio ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Anuncio
        </a>
        
        <form action="../../../controladores/admin/anuncios/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar definitivamente este anuncio?')">
            <input type="hidden" name="idAnuncio" value="<?= $idAnuncio ?>">
            <button type="submit" class="boton-secundario color-error border-error">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




