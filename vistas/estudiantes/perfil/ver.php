<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id);

$tituloDelPagina = "AULAPRO | MI PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI PERFIL</h1>
        <p class="subtitulo">Información de tu cuenta de estudiante</p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> EDITAR MI PERFIL
        </a>
    </div>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= strtoupper($estudiante['nombreEstudiante']) ?></div>
    </div> 

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciclo Formativo</div>
        <div class="valor-detalle"><span class="bolita activo-verde"><?= $estudiante['nombreCiclo'] ?></span></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">DNI / Identificación</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciudad</div>
        <div class="valor-detalle"><?= $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Dirección</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

