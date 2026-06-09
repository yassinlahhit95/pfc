<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/Security.php";

$idProfesor = $_SESSION['idProfesor'];
$modulos = listarModulosDeProfesor($idProfesor);
$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | CREAR SESIÓN VIVA';
$seccionActual = 'aula_crear';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVA SESIÓN VIVA</h1>
    <p class="texto-suave">Programa una clase en vivo con tus estudiantes</p>
</div>

<?php if (empty($modulos)) { ?>
    <div class="alerta-error">
        <i class="fas fa-exclamation-triangle"></i>
        <p>No tienes módulos asignados. Contacta con administración.</p>
    </div>
<?php } else { ?>
    <form method="POST" action="../../../controladores/aula/crear_sesion.php" class="formulario-principal">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">

        <div class="grupo-formulario">
            <label for="idModulo">MÓDULO *</label>
            <select id="idModulo" name="idModulo" required>
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($modulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo'] ) ?>"><?= Security::escapeHtml($modulo['nombreModulo']) ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="grupo-formulario">
            <label for="titulo">TÍTULO DE LA SESIÓN *</label>
            <input type="text" id="titulo" name="titulo" required placeholder="Ej: Introducción a JavaScript" maxlength="200">
        </div>

        <div class="grupo-formulario">
            <label for="descripcion">DESCRIPCIÓN</label>
            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Describe el contenido de la sesión..."></textarea>
        </div>

        <div class="grupo-formulario">
            <label for="fechaSesion">FECHA *</label>
            <input type="date" id="fechaSesion" name="fechaSesion" required min="<?= Security::escapeHtml(date('Y-m-d')) ?>">
        </div>

        <div class="grupo-formulario">
            <label for="horaSesion">HORA *</label>
            <input type="time" id="horaSesion" name="horaSesion" required>
        </div>

        <div class="grupo-formulario">
            <label for="plataforma">PLATAFORMA *</label>
            <select id="plataforma" name="plataforma" required>
                <option value="">-- Selecciona una plataforma --</option>
                <option value="google_meet">Google Meet</option>
                <option value="zoom">Zoom</option>
                <option value="teams">Microsoft Teams</option>
                <option value="jitsi">Jitsi</option>
                <option value="otra">Otra</option>
            </select>
        </div>

        <div class="grupo-formulario">
            <label for="enlaceReunion">ENLACE DE REUNIÓN (HTTPS) *</label>
            <input type="url" id="enlaceReunion" name="enlaceReunion" required placeholder="https://meet.google.com/abc-defg-hij">
            <span class="texto-pequeno texto-suave">El enlace debe comenzar con https://</span>
        </div>

        <div class="grupo-botones">
            <a href="sesiones.php" class="boton-secundario">CANCELAR</a>
            <button type="submit" class="boton-primario">
                <i class="fas fa-save"></i> CREAR SESIÓN
            </button>
        </div>
    </form>

    <div class="info-sistema">
        <h3>Información Importante</h3>
        <ul>
            <li>Las sesiones deben ser en el futuro (mínimo hoy)</li>
            <li>El enlace debe ser una URL válida HTTPS</li>
            <li>Los estudiantes recibirán notificación de la nueva sesión</li>
            <li>Puedes editar la sesión antes de que comience</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


