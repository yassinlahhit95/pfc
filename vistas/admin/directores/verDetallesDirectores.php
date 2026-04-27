<?php
session_start();
$titulo_pagina = "Detalles Director - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../../modelos/directores.php";

$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$director = obtenerDirectorPorId($id);

if (!$director) {
    echo "<div class='mensaje-error'>Director no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <h1>Ficha del Director</h1>
    <a href="/pfc/vistas/admin/directores/verDirectores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Información General</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?php echo $director['nombreDirector']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?php echo $director['emailDirector']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle">
            <?php 
                if (isset($director['dniDirector']) && !empty($director['dniDirector'])) {
                    echo $director['dniDirector'];
                } else {
                    echo '<span class="texto-atenuado">No especificado</span>';
                }
            ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha Alta</div>
        <div class="valor-detalle"><?php echo date('d/m/Y', strtotime($director['fechaAltaDirector'])); ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

