<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

// Filters
$idProfesorFilter = isset($_GET['idProfesor']) ? (int)$_GET['idProfesor'] : 0;
$estadoFilter     = isset($_GET['estado']) ? $_GET['estado'] : '';

$justificaciones = listarTodasLasJustificaciones($idProfesorFilter ?: null, $estadoFilter ?: null);
$profesores      = listarTodosLosProfesores();
$estudiantes     = listarTodosLosEstudiantes();

$titulo_pagina = "AULAPRO | GESTIÓN DE AUSENCIAS";
$seccion       = "justificaciones";
require_once __DIR__ . "/../comunes/nav.php";

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
    return '<a href="' . Security::escapeHtml($archivoUrl) . '" target="_blank" rel="noopener" class="boton-secundario boton-pequeno"><i class="fas fa-paperclip"></i> Ver adjunto</a>';
}
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-calendar-times"></i> GESTIÓN DE AUSENCIAS Y JUSTIFICACIONES</h1>
        <p class="subtitulo-encabezado">Control global de ausencias justificadas y historial del centro</p>
    </div>
    <div class="acciones-pagina">
        <button type="button" class="boton-primario" onclick="abrirModalCrear()">
            <i class="fas fa-plus"></i> Nueva Justificación
        </button>
    </div>
</div>

<?php if ($exito): ?>
    <div class="alerta alerta-exito"><?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
    <div class="alerta alerta-error"><?= Security::escapeHtml($errores) ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="panel margen-abajo">
    <form method="GET" class="formulario" id="form-filtros-justif" style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">
        <div class="campo" style="flex:1; min-width:200px;">
            <label for="idProfesor">Docente / Profesor</label>
            <select name="idProfesor" id="idProfesor" onchange="this.form.submit()">
                <option value="">— Todos los profesores —</option>
                <?php foreach ($profesores as $p): ?>
                    <option value="<?= (int)$p['idProfesor'] ?>" <?= $idProfesorFilter === (int)$p['idProfesor'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($p['nombreProfesor']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo" style="flex:1; min-width:200px;">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" onchange="this.form.submit()">
                <option value="">— Todos los estados —</option>
                <option value="pendiente" <?= $estadoFilter === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="aprobada" <?= $estadoFilter === 'aprobada' ? 'selected' : '' ?>>Aprobada</option>
                <option value="rechazada" <?= $estadoFilter === 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
            </select>
        </div>
        <div>
            <a href="justificaciones.php" class="boton-secundario" style="height:42px; display:inline-flex; align-items:center;">Limpiar</a>
        </div>
    </form>
</div>

<!-- History Table -->
<div class="panel">
    <?php if (empty($justificaciones)): ?>
        <div class="panel-vacio">
            <div class="panel-vacio-icono"><i class="fas fa-inbox"></i></div>
            <div class="panel-vacio-titulo">Sin registros de ausencias</div>
            <div class="panel-vacio-desc">No se encontraron justificaciones registradas con los filtros seleccionados.</div>
        </div>
    <?php else: ?>
        <div class="contenedor-tabla">
            <table class="tabla-datos" id="tablaJustificaciones">
                <thead>
                    <tr>
                        <th>Fecha falta</th>
                        <th>Estudiante</th>
                        <th>Módulo</th>
                        <th>Profesor</th>
                        <th>Motivo</th>
                        <th>Adjunto</th>
                        <th>Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($justificaciones as $j): ?>
                        <tr>
                            <td><?= Security::escapeHtml(date('d/m/Y', strtotime($j['fecha']))) ?></td>
                            <td><b><?= Security::escapeHtml($j['nombreEstudiante']) ?></b></td>
                            <td><?= Security::escapeHtml($j['nombreModulo']) ?></td>
                            <td><?= Security::escapeHtml($j['nombreProfesor'] ?? '—') ?></td>
                            <td style="max-width:250px; white-space:normal; font-size:.9rem;"><?= Security::escapeHtml($j['motivo']) ?></td>
                            <td><?= _adjuntoJustificacion($j) ?></td>
                            <td>
                                <?php if ($j['estado'] === 'pendiente'): ?>
                                    <span class="texto-estado naranja">Pendiente</span>
                                <?php elseif ($j['estado'] === 'aprobada'): ?>
                                    <span class="texto-estado verde">Aprobada</span>
                                <?php else: ?>
                                    <span class="texto-estado rojo" title="Motivo: <?= Security::escapeHtml($j['motivoRechazo'] ?? '') ?>">Rechazada</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <?php if ($j['estado'] === 'pendiente'): ?>
                                    <form method="POST" action="../../../controladores/secretaria/asistencias/resolver_justificacion.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="idJustificacion" value="<?= (int)$j['idJustificacion'] ?>">
                                        <input type="hidden" name="decision" value="aprobar">
                                        <button type="submit" class="boton-secundario btn-xs" title="Aprobar"><i class="fas fa-check"></i></button>
                                    </form>
                                    <button type="button" class="boton-peligro btn-xs" title="Rechazar" onclick="abrirModalRechazar(<?= (int)$j['idJustificacion'] ?>)"><i class="fas fa-times"></i></button>
                                <?php endif; ?>
                                <a href="#" class="boton-peligro btn-xs" style="background:#fee2e2; color:#ef4444;" title="Eliminar Justificación"
                                   data-modal-borrar
                                   data-id="<?= (int)$j['idJustificacion'] ?>"
                                   data-tipo="Justificación"
                                   data-nombre="Justificación del alumno <?= Security::escapeHtml($j['nombreEstudiante']) ?>"
                                   data-url="/controladores/secretaria/asistencias/borrar_justificacion.php"
                                   data-campo="idJustificacion"
                                   data-aviso="Se eliminará esta justificación y el estado de la asistencia del estudiante volverá a ser el original.">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Crear Justificación -->
<div class="capa-bloqueo-modal" id="modalCrear" hidden>
    <div class="caja-modal" style="max-width:500px;">
        <div class="modal-cabecera">
            <h2>Nueva Justificación de Ausencia</h2>
            <button type="button" class="modal-cerrar" onclick="cerrarModalCrear()">&times;</button>
        </div>
        <form method="POST" action="../../../controladores/secretaria/asistencias/insertar_justificacion.php" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="modal-cuerpo">
                <div class="campo" style="margin-bottom:16px;">
                    <label for="idEstudianteSelect">Estudiante</label>
                    <select name="idEstudiante" id="idEstudianteSelect" required onchange="cargarFaltasEstudiante(this.value)">
                        <option value="">— Selecciona Estudiante —</option>
                        <?php foreach ($estudiantes as $e): ?>
                            <option value="<?= (int)$e['idEstudiante'] ?>"><?= Security::escapeHtml($e['nombreEstudiante']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo" style="margin-bottom:16px;">
                    <label for="idAsistenciaSelect">Falta / Ausencia asociada</label>
                    <select name="idAsistencia" id="idAsistenciaSelect" required>
                        <option value="">— Selecciona estudiante primero —</option>
                    </select>
                </div>
                <div class="campo" style="margin-bottom:16px;">
                    <label for="motivo">Motivo de Justificación</label>
                    <textarea name="motivo" id="motivo" rows="3" placeholder="Ej: Visita médica o cita oficial..." required></textarea>
                </div>
                <div class="campo">
                    <label for="archivo">Prueba / Justificante <span class="texto-suave">(opcional, PDF o imagen)</span></label>
                    <input type="file" name="archivo" id="archivo" accept=".pdf,image/*">
                </div>
            </div>
            <div class="modal-pie">
                <button type="button" class="boton-secundario" onclick="cerrarModalCrear()">Cancelar</button>
                <button type="submit" class="boton-primario">Registrar y Aprobar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Rechazar Justificación -->
<div class="capa-bloqueo-modal" id="modalRechazar" hidden>
    <div class="caja-modal" style="max-width:450px;">
        <div class="modal-cabecera">
            <h2>Rechazar Justificación</h2>
            <button type="button" class="modal-cerrar" onclick="cerrarModalRechazar()">&times;</button>
        </div>
        <form method="POST" action="../../../controladores/secretaria/asistencias/resolver_justificacion.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idJustificacion" id="rechazarIdJustificacion">
            <input type="hidden" name="decision" value="rechazar">
            <div class="modal-cuerpo">
                <div class="campo">
                    <label for="motivoRechazo">Escribe el motivo del rechazo <span style="color:var(--rojo)">*</span></label>
                    <input type="text" name="motivoRechazo" id="motivoRechazo" placeholder="Ej: El justificante no corresponde a la fecha..." required>
                </div>
            </div>
            <div class="modal-pie">
                <button type="button" class="boton-secundario" onclick="cerrarModalRechazar()">Cancelar</button>
                <button type="submit" class="boton-peligro">Rechazar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaJustificaciones', 15);

function abrirModalCrear() {
    document.getElementById('modalCrear').hidden = false;
}
function cerrarModalCrear() {
    document.getElementById('modalCrear').hidden = true;
}
function abrirModalRechazar(id) {
    document.getElementById('rechazarIdJustificacion').value = id;
    document.getElementById('modalRechazar').hidden = false;
}
function cerrarModalRechazar() {
    document.getElementById('modalRechazar').hidden = true;
}

function cargarFaltasEstudiante(idEstudiante) {
    var selectFaltas = document.getElementById('idAsistenciaSelect');
    selectFaltas.innerHTML = '<option value="">Cargando ausencias...</option>';
    if (!idEstudiante) {
        selectFaltas.innerHTML = '<option value="">— Selecciona estudiante primero —</option>';
        return;
    }
    
    fetch('../../../controladores/secretaria/asistencias/obtener_faltas_json.php?idEstudiante=' + idEstudiante)
        .then(response => response.json())
        .then(data => {
            selectFaltas.innerHTML = '';
            if (data.length === 0) {
                selectFaltas.innerHTML = '<option value="">— No tiene faltas sin justificar —</option>';
            } else {
                selectFaltas.innerHTML = '<option value="">— Selecciona Falta —</option>';
                data.forEach(function(falta) {
                    selectFaltas.innerHTML += '<option value="' + falta.idAsistencia + '">' + falta.fecha + ' : ' + falta.nombreModulo + ' (' + falta.estado + ')</option>';
                });
            }
        })
        .catch(err => {
            selectFaltas.innerHTML = '<option value="">Error al cargar faltas</option>';
        });
}
</script>
