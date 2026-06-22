<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_mensaje'] ?? [];
unset($_SESSION['datos_mensaje']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idProfesor          = (int)$_SESSION['idProfesor'];
$listaDeCiclos       = listarCiclosDeProfesor($idProfesor);
$idCicloSeleccionado = (int)($_GET['idCiclo'] ?? 0);

$listaDeEstudiantes = !empty($idCicloSeleccionado)
    ? listarEstudiantesPorCiclo($idCicloSeleccionado)
    : listarEstudiantesDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | Redactar Mensaje";
$seccionActual   = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">

<div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--gap);">
    <a href="lista.php" class="ibtn ibtn-secondary"><i class="fas fa-arrow-left"></i> Volver al buzón</a>
</div>

<?php if ($errores): ?>
<div class="inbox-banner" style="margin-bottom:var(--gap);background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:#dc2626;">
    <i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml($errores) ?>
    <button class="inbox-banner-close" style="color:#dc2626;" onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>
<?php if ($exito): ?>
<div class="inbox-banner" style="margin-bottom:var(--gap);">
    <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
    <button class="inbox-banner-close" onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>

<div class="compose-card">
    <div class="compose-head">
        <h2><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i> Redactar Mensaje</h2>
        <p>Envía un mensaje a uno de tus <b>alumnos</b> o a la <b>dirección</b> del centro</p>
    </div>

    <?php if (!empty($listaDeCiclos)): ?>
    <div style="padding:0 var(--pad);margin-bottom:18px;">
        <div class="compose-label" style="margin-bottom:8px;">Filtrar alumnos por ciclo</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="agregar.php" class="ibtn <?= empty($idCicloSeleccionado) ? 'ibtn-primary' : 'ibtn-secondary' ?>">
                Todos
            </a>
            <?php foreach ($listaDeCiclos as $ciclo): ?>
            <a href="?idCiclo=<?= (int)$ciclo['idCiclo'] ?>" class="ibtn <?= $idCicloSeleccionado == $ciclo['idCiclo'] ? 'ibtn-primary' : 'ibtn-secondary' ?>">
                <?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <form action="../../../controladores/profesores/mensajes/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="compose-body">
            <div class="compose-field">
                <label class="compose-label" for="idEstudiante">Destinatario</label>
                <select name="idEstudiante" id="idEstudiante" class="compose-select">
                    <option value="">— Dirección (Administración) —</option>
                    <optgroup label="Mis Estudiantes">
                        <?php foreach ($listaDeEstudiantes as $est): ?>
                        <option value="<?= (int)$est['idEstudiante'] ?>"
                            <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($est['nombreEstudiante']) ?>
                            (<?= Security::escapeHtml($est['abreviaturaCiclo'] ?? '') ?>)
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <div class="compose-field">
                <label class="compose-label" for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" class="compose-input"
                       placeholder="Asunto del mensaje"
                       value="<?= Security::escapeHtml($datos['asunto'] ?? '') ?>">
            </div>

            <div class="compose-field">
                <label class="compose-label" for="descripcion">Mensaje</label>
                <textarea id="descripcion" name="descripcion" class="compose-textarea" maxlength="1000"
                          placeholder="Escribe tu mensaje..."><?= Security::escapeHtml($datos['descripcion'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="compose-actions">
            <button type="submit" name="enviarMensaje" class="ibtn ibtn-primary">
                <i class="fas fa-paper-plane"></i> Enviar Mensaje
            </button>
            <input type="reset" class="ibtn ibtn-secondary" value="Limpiar">
            <a href="lista.php" class="ibtn ibtn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script src="../../../public/js/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/mensajes.js') ?>"></script>
