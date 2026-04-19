<?php
session_start();
$titulo_pagina = "Detalles Director - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../modelos/directores.php";

$id = $_GET['id'] ?? 0;
$director = obtenerDirectorPorId($id);

if (!$director) {
    echo "<div class='mensaje-error'>Director no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Director</h1>
        <p class="subtitulo-encabezado">Información completa del director</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/directores/verDirectores.php" class="boton-primario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-panel mb-20">
    <div class="encabezado-tarjeta">
        <h3><i class="fas fa-user-tie"></i> Información Personal</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>ID</label>
            <p class="m-0 py-12 text-dark">#<?php echo $director['idDirector']; ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Nombre Completo</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $director['nombreDirector']; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Email</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $director['emailDirector']; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>DNI</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $director['dniDirector'] ?? '-'; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Fecha Alta</label>
            <p class="m-0 py-12 text-dark">
                <?php echo $director['fechaAltaDirector']; ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Estado</label>
            <p class="m-0 py-12 text-dark">
                <?php echo ucfirst($director['nombreEstado']); ?>
            </p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
