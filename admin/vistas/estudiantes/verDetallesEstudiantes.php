<?php
session_start();
require_once "../../modelos/conexion.php";
require_once "../../modelos/estudiantes.php";

$idEstudiante = $_GET['id'] ?? 0;

$conexionObj = new Conexion();
$conexion = $conexionObj->conectar();

$estudianteObj = new estudiante($conexion);
$estudiante = $estudianteObj->obtenerEstudiantePorIdModelo($idEstudiante);

if (!$estudiante) {
    $_SESSION['error'] = "Estudiante no encontrado";
    header("Location: verEstudiantes.php");
    exit;
}

$titulo_pagina = "Detalles Estudiante - Super Admin";
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Estudiante</h1>
        <p class="subtitulo-encabezado">Información completa del estudiante</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/estudiantes/verEstudiantes.php" class="boton-primario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-panel">
    <div class="encabezado-tarjeta">
        <h3><i class="fas fa-user"></i> Información Personal</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>ID</label>
            <p class="m-0 py-12 text-dark"><?php echo $idEstudiante; ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Nombre</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['nombreEstudiante']); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Email</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['emailEstudiante']); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Teléfono</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['telefonoEstudiante'] ?? '-'); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>DNI</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['dniEstudiante']); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Fecha Nacimiento</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['fechaNacimientoEstudiante']); ?></p>
        </div>
    </div>
</div>

<div class="tarjeta-panel mt-20">
    <div class="encabezado-tarjeta">
        <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>Curso</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['nombreCurso'] ?? 'Sin asignar'); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Estado</label>
            <p class="m-0 py-12">
                <span class="insignia-estado estado-<?php echo strtolower($estudiante['nombreEstado'] ?? 'inactivo'); ?>">
                    <?php echo ucfirst($estudiante['nombreEstado'] ?? 'Inactivo'); ?>
                </span>
            </p>
        </div>
        <div class="grupo-formulario ancho-completo">
            <label>Observaciones</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($estudiante['observacionesEstudiante'] ?? 'Sin observaciones'); ?></p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
