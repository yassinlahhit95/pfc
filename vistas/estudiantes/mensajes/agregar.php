<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_mensaje'] ?? [];
unset($_SESSION['datos_mensaje']);

require_once __DIR__ . "/../../../modelos/profesores.php";

$idEstudiante    = (int)$_SESSION['idEstudiante'];
$listaProfesores = listarProfesoresConModulosParaEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | Nuevo Mensaje";
$seccionActual   = 'reclamaciones';
include_once "../comunes/nav.php";
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
        <h2><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i> Nuevo Mensaje</h2>
        <p>Envía una consulta a tu <b>profesor</b> o a la <b>dirección</b> del centro</p>
    </div>

    <form action="../../../controladores/estudiantes/mensajes/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="compose-body">
            <div class="compose-field">
                <label class="compose-label" for="idProfesor">Destinatario</label>
                <select id="idProfesor" name="idProfesor" class="compose-select">
                    <option value="">— Dirección (Administración) —</option>
                    <?php foreach ($listaProfesores as $prof): ?>
                    <option value="<?= (int)$prof['idProfesor'] ?>"
                        <?= (isset($datos['idProfesor']) && $datos['idProfesor'] == $prof['idProfesor']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($prof['nombreProfesor'] . ' (' . $prof['nombreModulo'] . ')') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <span style="font-size:12px;color:var(--mut);margin-top:4px;display:block;">
                    Si no seleccionas ninguno, el mensaje se enviará a la dirección del centro.
                </span>
            </div>

            <div class="compose-field">
                <label class="compose-label" for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" class="compose-input"
                       placeholder="Ej: Consulta sobre nota, Duda en módulo..."
                       value="<?= Security::escapeHtml($datos['asunto'] ?? '') ?>">
            </div>

            <div class="compose-field">
                <label class="compose-label" for="descripcion">Mensaje</label>
                <textarea id="descripcion" name="descripcion" class="compose-textarea" maxlength="1000"
                          placeholder="Escribe tu mensaje aquí..."><?= Security::escapeHtml($datos['descripcion'] ?? '') ?></textarea>
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
