<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$idEstudiante = (int)$_SESSION['idEstudiante'];
$asistencias  = listarAsistenciasFiltradas(null, null, $idEstudiante, null, null, null);

// Última justificación por falta, para no ofrecer el botón dos veces ni repetir la consulta en el bucle.
$justificaciones = [];
foreach ($asistencias as $a) {
    if (in_array($a['estado'], ['ausente', 'retraso'], true)) {
        $j = obtenerJustificacionPorAsistencia((int)$a['idAsistencia']);
        if ($j) $justificaciones[(int)$a['idAsistencia']] = $j;
    }
}

$titulo_pagina = "Mis Faltas de Asistencia";
$seccionActual   = 'asistencias';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Mis Faltas de Asistencia</h1>
        <p class="subtitulo-encabezado">Consulta tu historial y justifica las faltas o retrasos</p>
    </div>
</div>

<?php if (empty($asistencias)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-calendar-check"></i></div>
        <div class="panel-vacio-titulo">Sin registros de asistencia</div>
        <div class="panel-vacio-desc">Todavía no hay faltas ni asistencias registradas.</div>
    </div>
</div>
<?php else: ?>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAsistencias">
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
        <form method="POST" action="../../../controladores/estudiantes/asistencias/justificar.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
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
    if (window.iniciarPaginacion) iniciarPaginacion('tablaAsistencias', 15);
});
</script>

<?php include '../comunes/footer.php'; ?>
