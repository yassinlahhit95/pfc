<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id);

$tituloDelPagina = "Mi Perfil - Portal Estudiantes";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MI PERFIL</h1>
    <a href="../dashboard.php" class="boton-secundario">
        <i class="fas fa-home"></i> VOLVER AL INICIO
    </a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">


    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre completo</div>
        <div class="valor-detalle"><?= $estudiante['nombreEstudiante'] ?></div>
    </div> 

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciclo </div>
        <div class="valor-detalle"><?= $estudiante['nombreCiclo'] ?></div>
    </div>


    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad</div>
        <div class="valor-detalle"><?= $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Dirección</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] ?></div>
    </div>

         <div>
            <a href="editar.php" class="boton-primario">
                <i class="fas fa-edit"></i> Editar mi Perfil
            </a>
        </div>
</div>

<?php include '../comunes/footer.php'; ?>



