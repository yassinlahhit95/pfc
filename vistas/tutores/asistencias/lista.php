<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$idEstudiante = (int)($_GET['id'] ?? 0);

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$estudiante = null;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $estudiante = $hijo;
        break;
    }
}

if (!$estudiante) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$asistencias = listarAsistenciasFiltradas(null, null, $idEstudiante, null, null, null);

// Última justificación por falta, para no ofrecer el botón dos veces.
$justificaciones = [];
foreach ($asistencias as $a) {
    if (in_array($a['estado'], ['ausente', 'retraso'], true)) {
        $j = obtenerJustificacionPorAsistencia((int)$a['idAsistencia']);
        if ($j) $justificaciones[(int)$a['idAsistencia']] = $j;
    }
}

$titulo_pagina = 'AulaPro Familias — Faltas de ' . $estudiante['nombreEstudiante'];
$seccion       = 'hijo';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
  <div>
    <h1>Faltas de Asistencia &mdash; <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    <p class="subtitulo-encabezado">Consulte el historial y justifique las ausencias o retrasos</p>
  </div>
  <a href="../estudiantes/expediente.php?id=<?= (int)$estudiante['idEstudiante'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($exito): ?>
<div style="background:var(--verde-suave); color:var(--verde-ink); padding:12px; border-radius:6px; margin-bottom:16px;">
    <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
</div>
<?php endif; ?>
<?php if ($errores): ?>
<div style="background:var(--rojo-suave); color:var(--rojo-ink); padding:12px; border-radius:6px; margin-bottom:16px;">
    <i class="fas fa-triangle-exclamation"></i> <?= Security::escapeHtml(is_array($errores) ? implode(', ', $errores) : $errores) ?>
</div>
<?php endif; ?>

<?php if (empty($asistencias)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-calendar-check"></i></div>
        <div class="panel-vacio-titulo">Sin registros de asistencia</div>
        <div class="panel-vacio-desc">Todavía no hay faltas ni asistencias registradas para este estudiante.</div>
    </div>
</div>
<?php else: ?>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAsistenciasTutor">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Módulo</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Justificante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asistencias as $a):
                    $idAsist = (int)$a['idAsistencia'];
                    $just    = $justificaciones[$idAsist] ?? null;
                ?>
                <tr>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($a['fecha']))) ?></td>
                    <td><?= Security::escapeHtml($a['nombreModulo']) ?></td>
                    <td>
                        <?php if ($a['estado'] === 'presente'): ?>
                            <span class="texto-estado verde">Presente</span>
                        <?php elseif ($a['estado'] === 'justificado'): ?>
                            <span class="texto-estado azul">Justificado</span>
                        <?php elseif ($a['estado'] === 'retraso'): ?>
                            <span class="texto-estado naranja">Retraso</span>
                        <?php else: ?>
                            <span class="texto-estado rojo">Ausente</span>
                        <?php endif; ?>
                    </td>
                    <td><?= Security::escapeHtml($a['observacion'] ?? '') ?></td>
                    <td>
                        <?php if (!in_array($a['estado'], ['ausente', 'retraso'], true)): ?>
                            <span class="texto-suave">—</span>
                        <?php elseif ($just && $just['estado'] === 'pendiente'): ?>
                            <span class="texto-estado naranja">En revisión</span>
                        <?php elseif ($just && $just['estado'] === 'aprobada'): ?>
                            <span class="texto-estado verde">Aprobado</span>
                        <?php elseif ($just && $just['estado'] === 'rechazada'): ?>
                            <span class="texto-estado rojo" title="<?= Security::escapeHtml($just['motivoRechazo'] ?? '') ?>">Rechazado</span>
                        <?php else: ?>
                            <button type="button" class="boton-secundario btn-xs" style="font-size:.78rem;padding:5px 10px;"
                                    onclick="abrirJustificar(<?= $idAsist ?>)">
                                <i class="fas fa-file-upload"></i> Justificar
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Modal de justificación -->
<div id="modal-justificar" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:480px;width:90%;">
        <form method="POST" action="../../../controladores/tutores/asistencias/justificar.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$estudiante['idEstudiante'] ?>">
            <input type="hidden" name="idAsistencia" id="justificar-idAsistencia" value="">
            <h3 class="modal-titulo">Justificar falta</h3>
            <div class="campo ancho-total" style="margin-top:12px;">
                <label for="justificar-motivo">Motivo</label>
                <textarea id="justificar-motivo" name="motivo" rows="3" required placeholder="Ej: Cita médica, adjunto justificante."></textarea>
            </div>
            <div class="campo ancho-total">
                <label for="justificar-archivo">Documento (opcional)</label>
                <input type="file" id="justificar-archivo" name="archivo" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="peligro-acciones" style="margin-top:16px;">
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-justificar').classList.remove('modal-abierto')">Cancelar</button>
                <button type="submit" class="boton-primario"><i class="fas fa-paper-plane"></i> Enviar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirJustificar(idAsistencia) {
    document.getElementById('justificar-idAsistencia').value = idAsistencia;
    document.getElementById('modal-justificar').classList.add('modal-abierto');
}
document.addEventListener('DOMContentLoaded', function () {
    if (window.iniciarPaginacion) iniciarPaginacion('tablaAsistenciasTutor', 15);
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
