<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$tituloDelPagina = 'AULAPRO | AULA DIGITAL';
$seccionActual = 'aula_index';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave">Accede a sesiones vivas, tareas y entregas</p>
</div>

<div class="contenedor-cards-aula">
    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-video"></i>
        </div>
        <div class="card-contenido">
            <h3>SESIONES VIVAS</h3>
            <p>Accede a las clases en vivo de tus módulos. Participa en tiempo real y registra tu asistencia.</p>
            <div class="card-stats">
                <span><i class="fas fa-play-circle"></i> Próximas y en directo</span>
            </div>
        </div>
        <a href="sesiones.php" class="card-boton">
            <span>Ir a Sesiones</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="card-contenido">
            <h3>TAREAS</h3>
            <p>Visualiza todas las tareas publicadas por tus profesores y entrega tus trabajos en el plazo establecido.</p>
            <div class="card-stats">
                <span><i class="fas fa-clipboard-check"></i> Pendientes y completadas</span>
            </div>
        </div>
        <a href="tareas.php" class="card-boton">
            <span>Ver Tareas</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="card-aula">
        <div class="card-icono">
            <i class="fas fa-file-upload"></i>
        </div>
        <div class="card-contenido">
            <h3>MIS ENTREGAS</h3>
            <p>Consulta el historial de todas tus entregas, calificaciones y la retroalimentación de tus profesores.</p>
            <div class="card-stats">
                <span><i class="fas fa-star"></i> Calificaciones y comentarios</span>
            </div>
        </div>
        <a href="mis_entregas.php" class="card-boton">
            <span>Ver Entregas</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<div class="info-aula-digital">
    <div class="info-item">
        <i class="fas fa-lightbulb"></i>
        <div>
            <h4>¿Cómo funciona?</h4>
            <p>En AULA DIGITAL encontrarás todas las herramientas que necesitas para tu formación: asiste a las sesiones vivas, realiza las tareas asignadas y revisa el progreso de tus entregas.</p>
        </div>
    </div>
    <div class="info-item">
        <i class="fas fa-bell"></i>
        <div>
            <h4>Notificaciones</h4>
            <p>Recibirás notificaciones automáticas cuando se publiquen nuevas tareas, cuando tus profesores califiquen tus entregas y cuando se aproximen las sesiones vivas.</p>
        </div>
    </div>
    <div class="info-item">
        <i class="fas fa-chart-line"></i>
        <div>
            <h4>Seguimiento</h4>
            <p>Monitorea tu progreso en tiempo real: visualiza tus calificaciones, estadísticas de asistencia y el estado de todas tus entregas.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
