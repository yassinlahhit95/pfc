<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$tituloDelPagina = 'AULAPRO | AULA DIGITAL';
$seccion = 'aula_index';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave">Monitorea sesiones vivas, tareas y entregas de todo el sistema</p>
</div>

<div class="contenedor-cards-aula">
    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-video"></i>
        </div>
        <div class="card-contenido">
            <h3>SESIONES VIVAS</h3>
            <p>Monitorea todas las sesiones vivas del sistema. Visualiza estadísticas de asistencia y desempeño por profesor.</p>
            <div class="card-stats">
                <span><i class="fas fa-chart-line"></i> Estadísticas globales</span>
            </div>
        </div>
        <a href="sesiones.php" class="card-boton">
            <span>Ver Sesiones</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="card-contenido">
            <h3>TAREAS</h3>
            <p>Monitorea todas las tareas asignadas en el sistema. Analiza el desempeño agregado y distribución de trabajo.</p>
            <div class="card-stats">
                <span><i class="fas fa-bar-chart"></i> Reportes por módulo</span>
            </div>
        </div>
        <a href="tareas.php" class="card-boton">
            <span>Ver Tareas</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-file-pdf"></i>
        </div>
        <div class="card-contenido">
            <h3>ENTREGAS</h3>
            <p>Visualiza el registro completo de entregas de todos los estudiantes. Accede a calificaciones y retroalimentación.</p>
            <div class="card-stats">
                <span><i class="fas fa-list-check"></i> Datos históricos</span>
            </div>
        </div>
        <a href="entregas.php" class="card-boton">
            <span>Ver Entregas</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<div class="info-aula-digital">
    <div class="info-item">
        <i class="fas fa-binoculars"></i>
        <div>
            <h4>Supervisión Total</h4>
            <p>Acceso centralizado a todas las actividades de AULA DIGITAL en el sistema. Monitorea el desempeño global de profesores y estudiantes.</p>
        </div>
    </div>
    <div class="info-item">
        <i class="fas fa-chart-area"></i>
        <div>
            <h4>Análisis y Reportes</h4>
            <p>Visualiza estadísticas detalladas de sesiones vivas, entregas completadas y distribución de calificaciones por ciclo y módulo.</p>
        </div>
    </div>
    <div class="info-item">
        <i class="fas fa-cogs"></i>
        <div>
            <h4>Gestión Integral</h4>
            <p>Mantén la visibilidad completa del ecosistema educativo digital. Identifica tendencias y áreas de mejora en el proceso educativo.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
