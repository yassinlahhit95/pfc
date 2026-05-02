<?php
session_start();

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
    <a href="/pfc/vistas/estudiantes/dashboard.php" class="boton-secundario">
        <i class="fas fa-home"></i> VOLVER AL INICIO
    </a>
</div>

<div class="tarjeta-blanca">


    <div class="fila-detalle">
        <div class="etiqueta-detalle">Nombre completo</div>
        <div class="valor-detalle"><?php echo $estudiante['nombreEstudiante']; ?></div>
    </div> 

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciclo </div>
        <div class="valor-detalle"><?php echo $estudiante['nombreCiclo']; ?></div>
    </div>


    <div class="fila-detalle">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?php echo $estudiante['emailEstudiante']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">TelÃ©fono</div>
        <div class="valor-detalle"><?php echo $estudiante['telefonoEstudiante']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?php echo $estudiante['dniEstudiante']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Ciudad</div>
        <div class="valor-detalle"><?php echo $estudiante['ciudadEstudiante']; ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">DirecciÃ³n</div>
        <div class="valor-detalle"><?php echo $estudiante['direccionEstudiante']; ?></div>
    </div>

         <div>
            <a href="/pfc/vistas/estudiantes/perfil/editar.php" class="boton-primario">
                <i class="fas fa-edit"></i> Editar mi Perfil
            </a>
        </div>
</div>

<?php include '../comunes/footer.php'; ?>

