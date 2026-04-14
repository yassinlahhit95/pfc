<?php
session_start();
$titulo_pagina = "Detalles Curso - Super Admin";
$seccion = 'cursos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/cursos.php";

$id = $_GET['id'] ?? 0;
$conexionObj = new Conexion();
$db = $conexionObj->conectar();

$cursoObj = new curso($db);
$curso = $cursoObj->obtenerCursoPorIdModelo($id);

if (!$curso) {
    echo "<div class='mensaje-error'>Curso no encontrado.</div>";
    include '../comunes/footer.php';
    exit;
}
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalles del Curso</h1>
        <p class="subtitulo-encabezado">Información completa del curso</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/cursos/verCursos.php" class="boton-primario"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="tarjeta-panel mb-20">
    <div class="encabezado-tarjeta">
        <h3><i class="fas fa-info-circle"></i> Información del Curso</h3>
    </div>
    <div class="cuadricula-formulario">
        <div class="grupo-formulario">
            <label>ID</label>
            <p class="m-0 py-12 text-dark">#<?php echo $curso['idCurso']; ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Nombre del Curso</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($curso['nombreCurso']); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Nivel</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($curso['nombreNivel']); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Tutor Asignado</label>
            <p class="m-0 py-12 text-dark">
                <?php echo htmlspecialchars($curso['nombreProfesor'] ?? 'Sin asignar'); ?>
            </p>
        </div>
        <div class="grupo-formulario">
            <label>Aula</label>
            <p class="m-0 py-12 text-dark"><?php echo htmlspecialchars($curso['nombreAula'] ?? 'Sin asignar'); ?></p>
        </div>
        <div class="grupo-formulario">
            <label>Estado</label>
            <p class="m-0 py-12">
                <span class="insignia-estado <?php echo $curso['nombreEstado'] == 'activo' ? 'estado-activo' : 'estado-inactivo'; ?>">
                    <?php echo ucfirst($curso['nombreEstado']); ?>
                </span>
            </p>
        </div>
        <div class="grupo-formulario ancho-completo">
            <label>Descripción</label>
            <p class="m-0 py-12 text-dark">
                <?php echo htmlspecialchars($curso['descripcionCurso'] ?? 'Sin descripción'); ?>
            </p>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
