<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
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

$titulo_pagina = "Detalles del Anuncio - Super Admin";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Anuncio</h1>
    <a href="gestionAnuncios.php" class="boton-secundario">â† Volver a la lista</a>
</div>

<div class="tarjeta-blanca">
    <div class="cabecera-detalles mb-20">
        <h2 class="texto-azul"><?php echo $anuncio['tituloAnuncio']; ?></h2>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle"><i class="fas fa-calendar-alt"></i> Publicado</div>
        <div class="valor-detalle"><?php echo date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])); ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle"><i class="fas fa-user-friends"></i> Dirigido a</div>
        <div class="valor-detalle"><span class="estado-bolita activo-verde"><?php echo ucfirst($anuncio['dirigidoA']); ?></span></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle"><i class="fas fa-hourglass-end"></i> Expira el</div>
        <div class="valor-detalle"><?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?></div>
    </div>
    
    <div class="margen-arriba">
        <div class="titulo-tarjeta">
            <h3>Contenido del Anuncio</h3>
        </div>
        <div class="tarjeta-gris-suave p-20 pre-wrap font-size-11 line-height-16">
            <?php echo $anuncio['contenidoAnuncio']; ?>
        </div>
    </div>

    <div class="margen-arriba-grande botones-accion">
        <a href="modificarAnuncios.php?idAnuncio=<?php echo $idAnuncio; ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Anuncio
        </a>
        
        <form action="/pfc/controladores/admin/anuncios/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('Â¿Eliminar definitivamente este anuncio?')">
            <input type="hidden" name="idAnuncio" value="<?php echo $idAnuncio; ?>">
            <button type="submit" class="boton-secundario color-error border-error">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
