<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = (int)$_SESSION['idProfesor'];
$pendientes = listarJustificacionesPendientesPorProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | JUSTIFICACIONES DE FALTAS";
$seccionActual   = 'justificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>JUSTIFICACIONES DE FALTAS</h1>
        <p class="subtitulo-encabezado">Faltas justificadas por tus alumnos pendientes de revisión</p>
    </div>
</div>

<?php if (empty($pendientes)): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-inbox"></i></div>
        <div class="panel-vacio-titulo">Sin justificaciones pendientes</div>
        <div class="panel-vacio-desc">Las justificaciones que envíen tus alumnos aparecerán aquí.</div>
    </div>
</div>
<?php else: ?>
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
                    <td>
                        <?php if (!empty($j['archivo'])):
                            $archivoNombre = basename($j['archivo']);
                            $archivoUrl = R2Client::documentoUrl(
                                __DIR__ . '/../../../public/uploads/justificantes/' . $archivoNombre,
                                '../../../public/uploads/justificantes/' . $archivoNombre,
                                'justificantes/' . $archivoNombre
                            ); ?>
                        <a href="<?= Security::escapeHtml($archivoUrl) ?>" target="_blank" rel="noopener">
                            <i class="fas fa-paperclip"></i> Ver
                        </a>
                        <?php else: ?>
                        <span class="texto-suave">—</span>
                        <?php endif; ?>
                    </td>
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

<script>
function abrirRechazo(idJustificacion) {
    document.getElementById('rechazar-idJustificacion').value = idJustificacion;
    document.getElementById('modal-rechazar').classList.add('modal-abierto');
}
document.addEventListener('DOMContentLoaded', function () {
    if (window.iniciarPaginacion) iniciarPaginacion('tablaJustificaciones', 15);
});
</script>

<?php include '../comunes/footer.php'; ?>
