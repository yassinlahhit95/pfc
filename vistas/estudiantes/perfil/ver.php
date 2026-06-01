<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($idEstudiante);

$tituloDelPagina = "AULAPRO | MI PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI PERFIL</h1>
        <p class="subtitulo">Informacion de tu cuenta de estudiante</p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> EDITAR MI PERFIL
        </a>
    </div>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml(strtoupper($estudiante['nombreEstudiante'])) ?></div>
    </div> 

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo Formativo</div>
        <div class="valor-detalle"><span class="indicador-estado activo-verde"><?= Security::escapeHtml($estudiante['nombreCiclo'] ) ?></span></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['emailEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Telefono</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['telefonoEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI / Identificacion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['dniEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciudad</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['ciudadEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Direccion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['direccionEstudiante'] ) ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



