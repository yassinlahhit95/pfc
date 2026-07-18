<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/admisiones.php";
$admisiones = listarPreMatriculas();

$pendientes = array_filter($admisiones, fn($adm) => in_array($adm['estado'], ['PENDIENTE','EN_REVISION']));
$admitidos  = array_filter($admisiones, fn($adm) => $adm['estado'] === 'ADMITIDO');

$titulo_pagina = 'AULAPRO | ADMISIONES';
$seccion = 'admisiones';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-graduation-cap"></i> Solicitudes de Admisión</h1>
        <p class="subtitulo-encabezado">Monitoriza y valida los nuevos ingresos al centro</p>
    </div>
    <button onclick="window.location.reload()" class="boton-secundario">
        <i class="fas fa-sync-alt"></i> Actualizar
    </button>
</div>

<!-- Stat cards -->
<div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <?php
    $stats = [
        ['label'=>'Total recibidas', 'value'=>count($admisiones), 'icon'=>'fa-file-signature', 'color'=>'var(--accent)'],
        ['label'=>'Pendientes',      'value'=>count($pendientes), 'icon'=>'fa-clock',           'color'=>'#f59e0b'],
        ['label'=>'Admitidos',       'value'=>count($admitidos),  'icon'=>'fa-user-check',      'color'=>'#10b981'],
    ];
    foreach ($stats as $stat): ?>
    <div class="panel" style="flex:1;min-width:160px;display:flex;align-items:center;gap:16px;padding:20px;">
        <div style="width:44px;height:44px;border-radius:12px;background:color-mix(in srgb,<?= $stat['color'] ?> 12%,transparent);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:<?= $stat['color'] ?>;flex-shrink:0;">
            <i class="fas <?= $stat['icon'] ?>"></i>
        </div>
        <div>
            <div class="texto-suave" style="font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;"><?= $stat['label'] ?></div>
            <div style="font-size:1.7rem;font-weight:700;line-height:1.1;color:var(--text);"><?= $stat['value'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAdmisiones">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Solicitante</th>
                    <th>Ciclo / Curso</th>
                    <th>Estado</th>
                    <th style="width:60px;text-align:right;"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($admisiones)): ?>
                <tr>
                    <td colspan="5">
                        <div class="panel-vacio">
                            <div class="panel-vacio-icono"><i class="fas fa-folder-open"></i></div>
                            <div class="panel-vacio-titulo">Sin solicitudes</div>
                            <div class="panel-vacio-desc">No se han encontrado solicitudes de admisión.</div>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($admisiones as $adm):
                    $estadoClase = match($adm['estado']) {
                        'PENDIENTE'   => 'naranja',
                        'EN_REVISION' => 'azul',
                        'ADMITIDO'    => 'verde',
                        'RECHAZADO'   => 'rojo',
                        default       => 'gris',
                    };
                    $estadoIcono = match($adm['estado']) {
                        'PENDIENTE'   => 'fa-clock',
                        'EN_REVISION' => 'fa-search',
                        'ADMITIDO'    => 'fa-check-circle',
                        'RECHAZADO'   => 'fa-times-circle',
                        default       => 'fa-question-circle',
                    };
                ?>
                <tr>
                    <td>
                        <div class="texto-negrita"><?= date('d/m/Y', strtotime($adm['fechaSolicitud'])) ?></div>
                        <div class="texto-suave" style="font-size:.78rem;"><?= date('H:i', strtotime($adm['fechaSolicitud'])) ?></div>
                    </td>
                    <td>
                        <div class="texto-negrita"><?= Security::escapeHtml($adm['nombre'] . ' ' . $adm['apellidos']) ?></div>
                        <div class="texto-suave" style="font-size:.78rem;"><?= Security::escapeHtml($adm['dni']) ?></div>
                    </td>
                    <td>
                        <div><?= Security::escapeHtml($adm['nombreCiclo']) ?></div>
                        <div class="texto-suave" style="font-size:.78rem;"><?= Security::escapeHtml($adm['curso']) ?> curso</div>
                    </td>
                    <td>
                        <span class="texto-estado <?= $estadoClase ?>">
                            <i class="fas <?= $estadoIcono ?>"></i>
                            <?= Security::escapeHtml($adm['estado']) ?>
                        </span>
                    </td>
                    <td style="text-align:right;">
                        <button class="boton-secundario" style="padding:6px 10px;"
                                onclick="verDetalle(<?= (int)$adm['idPreMatricula'] ?>)" title="Ver detalle">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal detalle -->
<div id="admModal" style="display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;">
    <div class="modal-backdrop-adm" onclick="cerrarModal()"
         style="position:absolute;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(2px);"></div>
    <div style="position:relative;z-index:9001;background:var(--surface);border-radius:16px;width:min(720px,94vw);max-height:88vh;overflow-y:auto;box-shadow:var(--shadow-lg);display:flex;flex-direction:column;">
        <!-- Header -->
        <div style="padding:24px 28px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:color-mix(in srgb,var(--accent) 12%,transparent);display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:1.1rem;flex-shrink:0;">
                <i class="fas fa-id-card"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <h3 id="admNombre" style="margin:0;font-size:1.1rem;">Revisión de Solicitud</h3>
                <div class="texto-suave" style="font-size:.82rem;">Expediente #<span id="admId">—</span></div>
            </div>
            <button onclick="cerrarModal()" class="boton-secundario" style="padding:6px 10px;flex-shrink:0;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Body -->
        <div style="padding:24px 28px;display:grid;grid-template-columns:1fr 1fr;gap:24px;flex:1;">
            <!-- Info col -->
            <div class="detalle-seccion">
                <div class="detalle-seccion-titulo"><i class="fas fa-user"></i> Información</div>
                <div class="detalle-fila"><span class="detalle-label">Nombre</span><span class="detalle-valor texto-negrita" id="admFullname">—</span></div>
                <div class="detalle-fila"><span class="detalle-label">DNI</span><span class="detalle-valor" id="admDni">—</span></div>
                <div class="detalle-fila"><span class="detalle-label">Email</span><span class="detalle-valor" id="admEmail" style="word-break:break-all;">—</span></div>
                <div class="detalle-fila"><span class="detalle-label">Teléfono</span><span class="detalle-valor" id="admTel">—</span></div>
                <div class="detalle-fila"><span class="detalle-label">Ciclo</span><span class="detalle-valor" id="admCiclo">—</span></div>
                <div class="detalle-fila"><span class="detalle-label">Fecha</span><span class="detalle-valor" id="admFecha">—</span></div>
                <div id="admTutorWrap" style="display:none;">
                    <div class="detalle-seccion-titulo" style="margin-top:16px;"><i class="fas fa-user-friends"></i> Tutor Legal</div>
                    <div class="detalle-fila"><span class="detalle-label">Nombre</span><span class="detalle-valor" id="admTutorNombre">—</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Parentesco</span><span class="detalle-valor" id="admTutorParentesco">—</span></div>
                    <div class="detalle-fila"><span class="detalle-label">Email</span><span class="detalle-valor" id="admTutorEmail" style="word-break:break-all;">—</span></div>
                </div>
            </div>

            <!-- Resolución col -->
            <div class="detalle-seccion">
                <div class="detalle-seccion-titulo"><i class="fas fa-gavel"></i> Resolución</div>
                <div class="campo" style="margin-bottom:12px;">
                    <label>Cambiar estado</label>
                    <select id="admEstado" class="campo-input">
                        <option value="EN_REVISION">En Revisión</option>
                        <option value="ADMITIDO">Admitir</option>
                        <option value="RECHAZADO">Rechazar</option>
                        <option value="SUBSANACION">Subsanación</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Notas internas / Motivo</label>
                    <textarea id="admObs" rows="5" class="campo-input" placeholder="Observaciones para el solicitante..."></textarea>
                </div>

                <div class="detalle-seccion-titulo" style="margin-top:16px;"><i class="fas fa-paperclip"></i> Documentos adjuntos</div>
                <div id="admArchivos" class="texto-suave" style="font-size:.85rem;">—</div>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding:16px 28px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:12px;">
            <button onclick="cerrarModal()" class="boton-secundario">Cerrar</button>
            <button id="admGuardar" class="boton-primario"><i class="fas fa-save"></i> Actualizar expediente</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
let currentId = null;
const csrf_token = '<?= Security::generateCSRFToken() ?>';

iniciarPaginacion('tablaAdmisiones', 15);

function verDetalle(id) {
    currentId = id;
    $('#admId').text(id);
    $('#admArchivos').html('<i class="fas fa-circle-notch fa-spin"></i> Cargando...');
    $('#admTutorWrap').hide();
    document.getElementById('admModal').style.display = 'flex';

    $.get('../../../controladores/secretaria/admisiones/acciones.php?action=get_details&id=' + id, function(res) {
        if (res.status !== 'success') {
            if (window.Toast) Toast.show('Error al cargar los datos.', 'error');
            return;
        }
        const data = res.data;
        $('#admNombre').text(data.nombre + ' ' + data.apellidos);
        $('#admFullname').text(data.nombre + ' ' + data.apellidos);
        $('#admDni').text(data.dni || '—');
        $('#admEmail').text(data.email || '—');
        $('#admTel').text(data.telefono || 'No indicado');
        $('#admCiclo').text(data.nombreCiclo + ' (' + data.curso + ')');
        $('#admFecha').text(data.fechaSolicitud ? data.fechaSolicitud.substr(0,10).split('-').reverse().join('/') : '—');
        $('#admEstado').val(data.estado);
        $('#admObs').val(data.observaciones || '');

        // Tutor legal
        if (data.nombreTutor) {
            $('#admTutorNombre').text(data.nombreTutor || '—');
            $('#admTutorParentesco').text(data.parentescoTutor || '—');
            $('#admTutorEmail').text(data.emailTutor || '—');
            $('#admTutorWrap').show();
        }

        // Archivos
        let html = '';
        if (!res.archivos || !res.archivos.length) {
            html = '<span class="texto-suave">Sin documentos adjuntos.</span>';
        } else {
            res.archivos.forEach(function(archivo) {
                html += '<div style="margin-bottom:8px;">'
                      + '<a href="' + archivo.rutaArchivo + '" target="_blank" style="color:var(--accent);text-decoration:none;">'
                      + '<i class="fas fa-file-alt" style="margin-right:6px;"></i>'
                      + archivo.tipoDocumento
                      + ' <i class="fas fa-external-link-alt" style="font-size:.7rem;opacity:.6;"></i>'
                      + '</a></div>';
            });
        }
        $('#admArchivos').html(html);
    });
}

function cerrarModal() {
    document.getElementById('admModal').style.display = 'none';
    currentId = null;
}

$('#admGuardar').on('click', function() {
    if (!currentId) return;
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spin fa-circle-notch"></i> Guardando...');

    $.post('../../../controladores/secretaria/admisiones/acciones.php?action=update_status', {
        id: currentId,
        estado: $('#admEstado').val(),
        observaciones: $('#admObs').val(),
        csrf_token: csrf_token
    }, function(res) {
        if (res.status === 'success') {
            if (window.Toast) Toast.show('Expediente actualizado correctamente.', 'success');
            cerrarModal();
            setTimeout(() => window.location.reload(), 1200);
        } else {
            if (window.Toast) Toast.show(res.message || 'Error al guardar', 'error');
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar expediente');
        }
    }).fail(function(jqXHR) {
        // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
        if (!(jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500)) {
            if (window.Toast) Toast.show('Error de conexión.', 'error');
        }
        $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Actualizar expediente');
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') cerrarModal();
});
</script>
