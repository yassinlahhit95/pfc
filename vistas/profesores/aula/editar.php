<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/Security.php";

$idProfesor = $_SESSION['idProfesor'];
$idSesion = $_GET['id'] ?? null;

if (!$idSesion) {
    header("Location: sesiones.php");
    exit;
}

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    header("Location: sesiones.php");
    exit;
}

$modulos = listarModulosDeProfesor($idProfesor);
$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | EDITAR SESIÓN VIVA';
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR SESIÓN VIVA</h1>
    <p class="texto-suave">Modifica los detalles de tu sesión</p>
</div>

<form method="POST" action="../../../controladores/aula/actualizar_sesion.php" class="formulario-principal">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($sesion['idSesion']) ?>">

    <div class="grupo-formulario">
        <label for="idModulo">MÓDULO *</label>
        <select id="idModulo" name="idModulo" required>
            <?php foreach ($modulos as $modulo) { ?>
                <option value="<?= $modulo['idModulo'] ?>" <?= ($modulo['idModulo'] == $sesion['idModulo']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($modulo['nombreModulo']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="grupo-formulario">
        <label for="titulo">TÍTULO DE LA SESIÓN *</label>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($sesion['titulo']) ?>" maxlength="200">
    </div>

    <div class="grupo-formulario">
        <label for="descripcion">DESCRIPCIÓN</label>
        <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($sesion['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="grupo-formulario">
        <label for="fechaSesion">FECHA *</label>
        <input type="date" id="fechaSesion" name="fechaSesion" required value="<?= htmlspecialchars($sesion['fechaSesion']) ?>" min="<?= date('Y-m-d') ?>">
    </div>

    <div class="grupo-formulario">
        <label for="horaSesion">HORA *</label>
        <input type="time" id="horaSesion" name="horaSesion" required value="<?= htmlspecialchars($sesion['horaSesion']) ?>">
    </div>

    <div class="grupo-formulario">
        <label for="plataforma">PLATAFORMA *</label>
        <select id="plataforma" name="plataforma" required>
            <option value="google_meet" <?= ($sesion['plataforma'] == 'google_meet') ? 'selected' : '' ?>>Google Meet</option>
            <option value="zoom" <?= ($sesion['plataforma'] == 'zoom') ? 'selected' : '' ?>>Zoom</option>
            <option value="teams" <?= ($sesion['plataforma'] == 'teams') ? 'selected' : '' ?>>Microsoft Teams</option>
            <option value="jitsi" <?= ($sesion['plataforma'] == 'jitsi') ? 'selected' : '' ?>>Jitsi</option>
            <option value="otra" <?= ($sesion['plataforma'] == 'otra') ? 'selected' : '' ?>>Otra</option>
        </select>
    </div>

    <div class="grupo-formulario">
        <label for="enlaceReunion">ENLACE DE REUNIÓN (HTTPS) *</label>
        <input type="url" id="enlaceReunion" name="enlaceReunion" required value="<?= htmlspecialchars($sesion['enlaceReunion']) ?>" placeholder="https://meet.google.com/abc-defg-hij">
        <span class="texto-pequeno texto-suave">El enlace debe comenzar con https://</span>
    </div>

    <div class="grupo-botones">
        <a href="sesiones.php" class="boton-secundario">CANCELAR</a>
        <button type="submit" class="boton-primario">
            <i class="fas fa-save"></i> GUARDAR CAMBIOS
        </button>
    </div>
</form>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
