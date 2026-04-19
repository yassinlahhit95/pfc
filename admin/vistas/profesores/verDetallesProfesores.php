<?php
session_start();
$titulo_pagina = "Detalles Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../modelos/profesores.php";

$id = $_GET['id'] ?? 0;
$profesor = obtenerProfesorPorId($id);

if (!$profesor) {
    echo "<div class='mensaje-error'>Profesor no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Profesor</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/profesores/verProfesores.php" class="boton-secundario">Volver</a>
    </div>
</div>

<div class="tarjeta-panel mb-20">
    <div class="encabezado-tarjeta">
        <h3>Información Personal</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>ID</label>
            <p class="m-0 py-12 text-dark">#<?php echo $profesor['idProfesor']; ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Nombre Completo</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $profesor['nombreProfesor']; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Email</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $profesor['emailProfesor']; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Teléfono</label>
            <p class="m-0 py-12 text-dark">
                <?php if (isset($profesor['telefonoProfesor'])) { echo $profesor['telefonoProfesor']; } else { echo '-'; } ?>
            </p>
        </div>
    </div>
</div>

<div class="tarjeta-panel mb-20">
    <div class="encabezado-tarjeta">
        <h3>Información Profesional</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>Especialidad</label>
            <p class="m-0 py-12 text-dark">
                <?php if (isset($profesor['especialidad'])) { echo $profesor['especialidad']; } else { echo 'No especificada'; } ?>
            </p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
