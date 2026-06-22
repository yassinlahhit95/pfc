<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_mensajes');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$tipo   = in_array($_GET['tipoDestinatario'] ?? '', ['profesor', 'estudiante'], true)
          ? $_GET['tipoDestinatario'] : 'profesor';
$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$listaDeCiclos = listarTodosLosCiclos();

if ($tipo === 'profesor') {
    $listaDeProfesores  = listarProfesores();
    $listaDeEstudiantes = [];
} else {
    $listaDeProfesores  = [];
    $listaDeEstudiantes = !empty($idCiclo) ? listarEstudiantesPorCiclo($idCiclo) : listarEstudiantes();
}

$datos = $_SESSION['datos_mensaje'] ?? [];
unset($_SESSION['datos_mensaje']);

$titulo_pagina = "AULAPRO | Redactar Mensaje";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">

<div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--gap);">
    <a href="lista.php" class="ibtn ibtn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($errores): ?>
<div class="inbox-banner" style="margin-bottom:var(--gap);background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:#dc2626;">
    <i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml($errores) ?>
    <button class="inbox-banner-close" style="color:#dc2626;" onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>

<div class="compose-card">
    <div class="compose-head">
        <h2><i class="fas fa-pen" style="color:var(--accent);margin-right:8px;"></i> Redactar Mensaje Oficial</h2>
        <p>Envía un mensaje a un profesor, estudiante o a todo un ciclo formativo</p>
    </div>

    <!-- Recipient type toggle -->
    <div style="padding:0 var(--pad);margin-bottom:18px;">
        <div class="compose-label" style="margin-bottom:8px;">1. Grupo de destino</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="?tipoDestinatario=profesor<?= !empty($idCiclo) ? '&idCiclo='.$idCiclo : '' ?>"
               class="ibtn <?= $tipo === 'profesor' ? 'ibtn-primary' : 'ibtn-secondary' ?>">
                <i class="fas fa-chalkboard-teacher"></i> Profesores
            </a>
            <a href="?tipoDestinatario=estudiante<?= !empty($idCiclo) ? '&idCiclo='.$idCiclo : '' ?>"
               class="ibtn <?= $tipo === 'estudiante' ? 'ibtn-primary' : 'ibtn-secondary' ?>">
                <i class="fas fa-user-graduate"></i> Estudiantes
            </a>
        </div>
    </div>

    <form action="../../../controladores/admin/mensajes/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="emisor_rol" value="admin">
        <input type="hidden" name="tipoDestinatario" value="<?= Security::escapeHtml($tipo) ?>">
        <input type="hidden" name="idCicloMasivo" value="<?= Security::escapeHtml($idCiclo) ?>">

        <div class="compose-body">

        <?php if ($tipo === 'estudiante'): ?>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--gap);margin-bottom:18px;" class="compose-cols">
                <div class="compose-field">
                    <label class="compose-label">2. Filtrar por ciclo (opcional)</label>
                    <select class="compose-select" onchange="window.location.href='?tipoDestinatario=estudiante&idCiclo='+this.value">
                        <option value="">— Todos los estudiantes —</option>
                        <?php foreach ($listaDeCiclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>" <?= $idCiclo == $c['idCiclo'] ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($c['nombreCiclo']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="compose-field">
                    <label class="compose-label">
                        3. Estudiante específico
                        <?php if (!empty($idCiclo)): ?><span style="font-weight:500;color:var(--mut);text-transform:none;letter-spacing:0;">(vacío = enviar a todo el ciclo)</span><?php endif; ?>
                    </label>
                    <select name="idEstudiante" class="compose-select">
                        <option value="">— Todos del ciclo —</option>
                        <?php foreach ($listaDeEstudiantes as $est): ?>
                        <option value="<?= (int)$est['idEstudiante'] ?>"
                            <?= (isset($datos['idEstudiante']) && $datos['idEstudiante'] == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($est['nombreEstudiante']) ?> (<?= Security::escapeHtml($est['nombreCiclo'] ?? '') ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php else: ?>
            <div class="compose-field">
                <label class="compose-label">2. Destinatario específico</label>
                <select name="idProfesor" class="compose-select">
                    <option value="">— Seleccionar profesor —</option>
                    <?php foreach ($listaDeProfesores as $prof): ?>
                    <option value="<?= (int)$prof['idProfesor'] ?>"
                        <?= (isset($datos['idProfesor']) && $datos['idProfesor'] == $prof['idProfesor']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($prof['nombreProfesor']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

            <div class="compose-field">
                <label class="compose-label" for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" class="compose-input"
                       placeholder="Escribe el asunto del mensaje..."
                       value="<?= Security::escapeHtml($datos['asunto'] ?? '') ?>">
            </div>

            <div class="compose-field">
                <label class="compose-label" for="descripcion">Mensaje</label>
                <textarea id="descripcion" name="descripcion" class="compose-textarea" maxlength="1000"
                          placeholder="Escribe el contenido del mensaje..."><?= Security::escapeHtml($datos['descripcion'] ?? '') ?></textarea>
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
<style>
@media(max-width:640px){.compose-cols{grid-template-columns:1fr !important;}}
</style>
