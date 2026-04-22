<?php
session_start();
$titulo_pagina = "Detalles Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";

$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$profesor = obtenerProfesorPorId($id);

if (!$profesor) {
    echo "<div class='mensaje-error'>Profesor no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <h1>Ficha del Profesor</h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">
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
            <p class="texto-negrita"><?php echo $profesor['nombreProfesor']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Email</label>
            <p class="texto-negrita"><?php echo $profesor['emailProfesor']; ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Teléfono</label>
            <p class="texto-negrita"><?php 
                if ($profesor['telefonoProfesor']) {
                    echo $profesor['telefonoProfesor'];
                } else {
                    echo '-';
                }
            ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">DNI</label>
            <p class="texto-negrita"><?php 
                if ($profesor['dniProfesor']) {
                    echo $profesor['dniProfesor'];
                } else {
                    echo '-';
                }
            ?></p>
        </div>
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Especialidad</label>
            <p class="texto-negrita"><?php 
                if ($profesor['especialidad']) {
                    echo $profesor['especialidad'];
                } else {
                    echo 'No definida';
                }
            ?></p>
        </div>
        <div class="campo-formulario campo-ancho-total">
            <label class="texto-atenuado texto-pequeno">Dirección</label>
            <p class="texto-negrita"><?php 
                if ($profesor['direccionProfesor']) {
                    echo $profesor['direccionProfesor'];
                } else {
                    echo '-';
                }
            ?></p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
