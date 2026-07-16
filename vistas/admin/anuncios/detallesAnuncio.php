<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_anuncios');

unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/anuncios.php";

$idAnuncio = (int)($_GET['idAnuncio'] ?? 0);
$anuncio = obtenerAnuncioPorId($idAnuncio);

if (!$anuncio) {
    $_SESSION['errores'] = "El anuncio solicitado no existe.";
    header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

$titulo_pagina = "AULAPRO | DETALLES DEL ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>DETALLES DEL ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="cabecera-detalles" style="margin-bottom: 20px;">
        <h2 class="texto-azul"><?= Security::escapeHtml($anuncio['tituloAnuncio']) ?></h2>
    </div>
    
    <div class="fila-datos">
        <div class="nombre-detalle">Publicado</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Dirigido a</div>
        <div class="valor-detalle"><span class="indicador-estado activo-verde"><?= Security::escapeHtml(ucfirst($anuncio['dirigidoA'])) ?></span></div>
    </div>

    <div class="margen-arriba">
        <div class="fila-datos">
            <div class="nombre-detalle">Contenido</div>
            <div class="valor-detalle">
                <?php if (!empty($anuncio['contenidoAnuncio'])) { ?>
                    <?= Security::escapeHtml($anuncio['contenidoAnuncio']) ?>
                <?php } else { ?>
                    <span class="texto-suave">Sin contenido.</span>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="margen-arriba-grande botones-accion">
        <a href="modificarAnuncios.php?idAnuncio=<?= $idAnuncio ?>" class="boton-primario">
            <i class="fas fa-edit"></i> Editar Anuncio
        </a>
        
        <a href="#" class="boton-peligro"
           data-modal-borrar
           data-id="<?= (int)$idAnuncio ?>"
           data-tipo="Anuncio"
           data-nombre="<?= Security::escapeHtml($anuncio['tituloAnuncio']) ?>"
           data-url="/controladores/admin/anuncios/borrar.php"
           data-campo="idAnuncio"
           data-redirect="gestionAnuncios.php">
            <i class="fas fa-trash"></i> Eliminar
        </a>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

