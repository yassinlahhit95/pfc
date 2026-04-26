<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../../modelos/anuncios.php";

$idAnuncio = $_GET['idAnuncio'] ?? 0;
$anuncio = obtenerAnuncioPorId($idAnuncio);

if (!$anuncio) {
    $_SESSION['error'] = "El anuncio solicitado no existe.";
    header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

$titulo_pagina = "Detalles del Anuncio - Super Admin";
$seccion = 'anuncios';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Detalles del Anuncio</h1>
    <a href="gestionAnuncios.php" class="boton-secundario">← Volver a la lista</a>
</div>

<div class="tarjeta-blanca">
    <div class="cabecera-detalles mb-20">
        <h2 class="texto-azul"><?php echo $anuncio['tituloAnuncio']; ?></h2>
        <div class="metadatos-anuncio mt-10">
            <p><i class="fas fa-calendar-alt"></i> <strong>Publicado el:</strong> <?php echo date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])); ?></p>
            <p><i class="fas fa-user-friends"></i> <strong>Dirigido a:</strong> <?php echo ucfirst($anuncio['dirigidoA']); ?></p>
            <p><i class="fas fa-hourglass-end"></i> <strong>Expira el:</strong> <?php echo date('d/m/Y', strtotime($anuncio['fechaExpiracion'])); ?></p>
        </div>
    </div>
    
    <hr class="margen-abajo">

    <div class="contenido-anuncio-detalle">
        <div class="tarjeta-gris-suave p-20" style="white-space: pre-wrap; font-size: 1.1em; line-height: 1.6;">
            <?php echo $anuncio['contenidoAnuncio']; ?>
        </div>
    </div>

    <div class="margen-arriba-grande botones-accion">
        <a href="modificarAnuncios.php?idAnuncio=<?php echo $idAnuncio; ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Anuncio
        </a>
        
        <form action="/pfc/controladores/admin/anuncios/borrar.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar definitivamente este anuncio?')">
            <input type="hidden" name="idAnuncio" value="<?php echo $idAnuncio; ?>">
            <button type="submit" class="boton-secundario" style="color: #dc2626; border-color: #dc2626;">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>

<style>
.metadatos-anuncio p {
    margin: 5px 0;
    color: #718096;
    font-size: 0.9rem;
}
.metadatos-anuncio i {
    width: 20px;
}
.mb-20 { margin-bottom: 20px; }
.p-20 { padding: 20px; }
</style>

<?php include '../comunes/footer.php'; ?>

