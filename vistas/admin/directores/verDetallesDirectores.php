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
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Nombre Completo</label>
            <p class="texto-negrita"><?php echo $director['nombreDirector']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Email</label>
            <p class="texto-negrita"><?php echo $director['emailDirector']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">DNI</label>
            <p class="texto-negrita"><?php 
                if (isset($director['dniDirector'])) {
                    echo $director['dniDirector'];
                } else {
                    echo '-';
                }
            ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Fecha Alta</label>
            <p class="texto-negrita"><?php echo date('d/m/Y', strtotime($director['fechaAltaDirector'])); ?></p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
