<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = (int)$_SESSION['idProfesor'];
$pendientes = listarJustificacionesPendientesPorProfesor($idProfesor);
$resueltas  = listarJustificacionesResueltasPorProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | JUSTIFICACIONES DE FALTAS";
$seccionActual   = 'justificaciones';
include_once __DIR__ . "/../comunes/nav.php";

function _adjuntoJustificacion(array $j): string {
    if (empty($j['archivo'])) {
        return '<span class="texto-suave">—</span>';
    }
    $archivoNombre = basename($j['archivo']);
    $archivoUrl = R2Client::documentoUrl(
        __DIR__ . '/../../../public/uploads/justificantes/' . $archivoNombre,
        '../../../public/uploads/justificantes/' . $archivoNombre,
        'justificantes/' . $archivoNombre
    );
    return '<a href="' . Security::escapeHtml($archivoUrl) . '" target="_blank" rel="noopener"><i class="fas fa-paperclip"></i> Ver</a>';
}
?>

<div class="cabecera">
    <div>
        <h1>JUSTIFICACIONES DE FALTAS</h1>
        <p class="subtitulo-encabezado">Faltas justificadas por tus alumnos</p>
    </div>
</div>

<div class="tabs-justif">
    <button type="button" class="tab-justif-btn activo" data-tab="pendientes" onclick="cambiarTabJustif('pendientes', this)">
        <i class="fas fa-clock"></i> Pendientes
        <?php if (!empty($pendientes)): ?><span class="chip-contador"><?= (int)count($pendientes) ?></span><?php endif; ?>
    </button>
    <button type="button" class="tab-justif-btn" data-tab="historial" onclick="cambiarTabJustif('historial', this)">
        <i class="fas fa-history"></i> Historial
    </button>
</div>

<div id="tabcontent-pendientes">
<?php if (empty($pendientes)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-inbox"></i></div>
        <div class="panel-vacio-titulo">Sin justificaciones pendientes</div>
        <div class="panel-vacio-desc">Las justificaciones que envíen tus alumnos aparecerán aquí.</div>
    </div>
</div>
<?php else: ?>
<div class="panel margen-abajo">
    <input type="text" id="buscarPendientes" class="buscador"
           autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
           data-lpignore="true" data-1p-ignore="true" data-form-type="other"
           placeholder="Buscar por estudiante, módulo o motivo…"
           oninput="filtrarTabla('buscarPendientes','tablaJustificaciones')">
</div>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaJustificaciones">
            <thead>
                <tr>
                    <th>Fecha falta</th>
                    <th>Módulo</th>
                    <th>Estudiante</th>
                    <th>Motivo</th>
                    <th>Adjunto</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $j): ?>
                <tr>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($j['fecha']))) ?></td>
                    <td><?= Security::escapeHtml($j['nombreModulo']) ?></td>
                    <td><?= Security::escapeHtml($j['nombreEstudiante']) ?></td>
                    <td style="max-width:280px;white-space:normal;"><?= Security::escapeHtml($j['motivo']) ?></td>
                    <td><?= _adjuntoJustificacion($j) /* construido con datos ya escapados/validados arriba */ ?></td>
                    <td style="text-align:right;white-space:nowrap;">
                        <form method="POST" action="../../../controladores/profesores/asistencias/resolver_justificacion.php" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="idJustificacion" value="<?= (int)$j['idJustificacion'] ?>">
                            <input type="hidden" name="decision" value="aprobar">
                            <button type="submit" class="boton-secundario btn-xs" style="font-size:.78rem;padding:5px 10px;">
                                <i class="fas fa-check"></i> Aprobar
                            </button>
                        </form>
                        <button type="button" class="boton-peligro btn-xs" style="font-size:.78rem;padding:5px 10px;"
                                onclick="abrirRechazo(<?= (int)$j['idJustificacion'] ?>)">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>

<div id="tabcontent-historial" style="display:none;">
<?php if (empty($resueltas)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-history"></i></div>
        <div class="panel-vacio-titulo">Sin historial todavía</div>
        <div class="panel-vacio-desc">Las justificaciones que apruebes o rechaces aparecerán aquí, con opción de corregir la decisión.</div>
    </div>
</div>
<?php else: ?>
<div class="panel margen-abajo">
    <input type="text" id="buscarHistorial" class="buscador"
           autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
           data-lpignore="true" data-1p-ignore="true" data-form-type="other"
           placeholder="Buscar por estudiante, módulo o motivo…"
           oninput="filtrarTabla('buscarHistorial','tablaHistorialJustif')">
</div>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaHistorialJustif">
            <thead>
                <tr>
                    <th>Fecha falta</th>
                    <th>Módulo</th>
                    <th>Estudiante</th>
                    <th>Motivo</th>
                    <th>Decisión</th>
                    <th>Resuelta el</th>
                    <th>Adjunto</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resueltas as $j): ?>
                <tr>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($j['fecha']))) ?></td>
                    <td><?= Security::escapeHtml($j['nombreModulo']) ?></td>
                    <td><?= Security::escapeHtml($j['nombreEstudiante']) ?></td>
                    <td style="max-width:240px;white-space:normal;">
                        <?= Security::escapeHtml($j['motivo']) ?>
                        <?php if ($j['estado'] === 'rechazada' && !empty($j['motivoRechazo'])): ?>
                        <div class="texto-suave" style="font-size:.78rem;margin-top:4px;">
                            <i class="fas fa-comment-slash"></i> <?= Security::escapeHtml($j['motivoRechazo']) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($j['estado'] === 'aprobada'): ?>
                        <span class="texto-estado verde">Aprobada</span>
                        <?php else: ?>
                        <span class="texto-estado rojo">Rechazada</span>
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($j['fechaResolucion']) ? Security::escapeHtml(date('d/m/Y H:i', strtotime($j['fechaResolucion']))) : '—' ?></td>
                    <td><?= _adjuntoJustificacion($j) /* construido con datos ya escapados/validados arriba */ ?></td>
                    <td style="text-align:right;white-space:nowrap;">
                        <?php if ($j['estado'] === 'aprobada'): ?>
                        <button type="button" class="boton-peligro btn-xs" style="font-size:.78rem;padding:5px 10px;"
                                onclick="abrirRechazo(<?= (int)$j['idJustificacion'] ?>)">
                            <i class="fas fa-rotate-left"></i> Cambiar a rechazada
                        </button>
                        <?php else: ?>
                        <form method="POST" action="../../../controladores/profesores/asistencias/resolver_justificacion.php" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="idJustificacion" value="<?= (int)$j['idJustificacion'] ?>">
                            <input type="hidden" name="decision" value="aprobar">
                            <button type="submit" class="boton-secundario btn-xs" style="font-size:.78rem;padding:5px 10px;">
                                <i class="fas fa-rotate-left"></i> Cambiar a aprobada
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
</div>

<div id="modal-rechazar" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:440px;width:90%;">
        <form method="POST" action="../../../controladores/profesores/asistencias/resolver_justificacion.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idJustificacion" id="rechazar-idJustificacion" value="">
            <input type="hidden" name="decision" value="rechazar">
            <h3 class="modal-titulo">Rechazar justificación</h3>
            <div class="campo ancho-total" style="margin-top:12px;">
                <label for="rechazar-motivo">Motivo del rechazo</label>
                <textarea id="rechazar-motivo" name="motivoRechazo" rows="3" required placeholder="Explica por qué se rechaza…"></textarea>
            </div>
            <div class="peligro-acciones" style="margin-top:16px;">
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-rechazar').classList.remove('modal-abierto')">Cancelar</button>
                <button type="submit" class="boton-peligro"><i class="fas fa-times"></i> Rechazar</button>
            </div>
        </form>
    </div>
</div>

<style>
.tabs-justif { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; }
.tab-justif-btn {
    display:inline-flex; align-items:center; gap:8px;
    padding:9px 16px; border-radius:8px; border:1px solid var(--border);
    background:var(--surface); color:var(--mut); font-weight:600; font-size:.88rem;
    cursor:pointer; transition:all .15s ease;
}
.tab-justif-btn:hover { color:var(--text); border-color:var(--border-2); }
.tab-justif-btn.activo { background:var(--accent); border-color:var(--accent); color:#fff; }
.tab-justif-btn .chip-contador {
    background:rgba(255,255,255,.25); border-radius:999px; padding:1px 8px; font-size:.75rem;
}
.tab-justif-btn.activo .chip-contador { background:rgba(255,255,255,.3); }
.tab-justif-btn:not(.activo) .chip-contador { background:var(--rojo); color:#fff; }
</style>

<script>
function abrirRechazo(idJustificacion) {
    document.getElementById('rechazar-idJustificacion').value = idJustificacion;
    document.getElementById('modal-rechazar').classList.add('modal-abierto');
}
function cambiarTabJustif(tab, btn) {
    document.getElementById('tabcontent-pendientes').style.display = tab === 'pendientes' ? '' : 'none';
    document.getElementById('tabcontent-historial').style.display = tab === 'historial' ? '' : 'none';
    document.querySelectorAll('.tab-justif-btn').forEach(function (b) { b.classList.remove('activo'); });
    btn.classList.add('activo');
}
document.addEventListener('DOMContentLoaded', function () {
    if (window.iniciarPaginacion) {
        iniciarPaginacion('tablaJustificaciones', 15);
        iniciarPaginacion('tablaHistorialJustif', 15);
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
