<?php
require_once __DIR__ . "/../../../include/Security.php";

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/admisiones.php";
$admisiones = listarPreMatriculas();

$titulo_pagina = 'AulaPro — Gestión de Admisiones';
$seccion       = 'admisiones';
include __DIR__ . '/../comunes/nav.php';
?>

<!-- Bootstrap CSS para Modales y Layout de la tabla -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- SweetAlert2 para notificaciones -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<div class="hero">
    <div class="hero-text">
        <div class="eyebrow">Admisiones</div>
        <h1>Gestión de <span>Solicitudes</span></h1>
        <p class="sub">Monitoriza y valida los nuevos ingresos al centro desde un panel centralizado.</p>
    </div>
</div>

<div class="grid">
    <div class="tile card-tint" style="--tint: #4f46e5;">
        <div class="tile-ico"><i class="fas fa-file-signature"></i></div>
        <div class="tile-body">
            <div class="tile-label">Total Recibidas</div>
            <div class="tile-desc"><?php echo count($admisiones); ?> solicitudes</div>
        </div>
    </div>
    
    <?php
    $pendientes = array_filter($admisiones, fn($a) => $a['estado'] === 'PENDIENTE' || $a['estado'] === 'EN_REVISION');
    $admitidos = array_filter($admisiones, fn($a) => $a['estado'] === 'ADMITIDO');
    ?>
    <div class="tile card-tint" style="--tint: #f59e0b;">
        <div class="tile-ico"><i class="fas fa-clock"></i></div>
        <div class="tile-body">
            <div class="tile-label">Pendientes</div>
            <div class="tile-desc"><?php echo count($pendientes); ?> por revisar</div>
        </div>
    </div>

    <div class="tile card-tint" style="--tint: #10b981;">
        <div class="tile-ico"><i class="fas fa-user-check"></i></div>
        <div class="tile-body">
            <div class="tile-label">Admitidos</div>
            <div class="tile-desc"><?php echo count($admitidos); ?> alumnos</div>
        </div>
    </div>
</div>

<div class="dash-panel mt-4 overflow-hidden shadow-sm" style="border-radius: 1.5rem; background: var(--surface);">
    <div class="dash-panel-head d-flex justify-content-between align-items-center p-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-list-ul text-primary"></i>
            <h3 class="mb-0 fs-5 fw-bold text-dark">Listado de Solicitudes</h3>
        </div>
        <div class="actions">
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> <span>Actualizar</span>
            </button>
        </div>
    </div>
    <div class="dash-panel-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="px-4 py-3 border-0">Fecha</th>
                        <th class="border-0">Solicitante</th>
                        <th class="border-0">Ciclo / Curso</th>
                        <th class="border-0">Estado</th>
                        <th class="text-end px-4 border-0">Acciones</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($admisiones)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                                    <p class="text-muted">No se han encontrado solicitudes de admisión.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($admisiones as $adm): ?>
                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                            <td class="px-4">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark"><?php echo date('d M', strtotime($adm['fechaSolicitud'])); ?></span>
                                    <span class="small text-muted opacity-75"><?php echo date('H:i', strtotime($adm['fechaSolicitud'])); ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle" style="background: var(--bg-2); color: var(--accent); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; border: 1px solid var(--border);">
                                        <?php echo strtoupper(substr($adm['nombre'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($adm['nombre'] . ' ' . $adm['apellidos']); ?></div>
                                        <div class="small text-muted"><?php echo $adm['dni']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="badge rounded-pill bg-light text-dark fw-medium border px-3 py-2"><?php echo htmlspecialchars($adm['nombreCiclo']); ?></div>
                                <div class="small text-muted mt-1 px-1"><?php echo $adm['curso']; ?> curso</div>
                            </td>
                            <td>
                                <?php
                                $statusMeta = match($adm['estado']) {
                                    'PENDIENTE' => ['class' => 'bg-warning text-dark', 'icon' => 'fa-clock'],
                                    'EN_REVISION' => ['class' => 'bg-info text-white', 'icon' => 'fa-search'],
                                    'ADMITIDO' => ['class' => 'bg-success text-white', 'icon' => 'fa-check-circle'],
                                    'RECHAZADO' => ['class' => 'bg-danger text-white', 'icon' => 'fa-times-circle'],
                                    'SUBSANACION' => ['class' => 'bg-secondary text-white', 'icon' => 'fa-exclamation-triangle'],
                                    default => ['class' => 'bg-light text-dark', 'icon' => 'fa-question-circle']
                                };
                                ?>
                                <span class="badge rounded-pill <?php echo $statusMeta['class']; ?> px-3 py-2 fw-semibold" style="font-size: 0.7rem;">
                                    <i class="fas <?php echo $statusMeta['icon']; ?> me-1"></i>
                                    <?php echo $adm['estado']; ?>
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <button class="btn btn-action-icon" onclick="verDetalle(<?php echo $adm['idPreMatricula']; ?>)" title="Revisar Solicitud">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Detalle Mejorado -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1.5rem;">
            <div class="modal-header bg-primary text-white p-4 border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-2" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-id-card fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="detalleNombre">Revisión de Solicitud</h5>
                        <p class="small mb-0 opacity-75">Expediente de admisión #<span id="detId"></span></p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #f8fafc;">
                <div class="row g-4">
                    <!-- Columna Izquierda: Información -->
                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-body p-4">
                                <h6 class="text-uppercase x-small fw-bold text-primary mb-4" style="letter-spacing: 0.05em;">Información de contacto</h6>
                                <div class="mb-4">
                                    <label class="small text-muted d-block mb-1">Nombre Completo</label>
                                    <div class="fw-bold text-dark" id="detFullname"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="small text-muted d-block mb-1">DNI / Identificación</label>
                                    <div class="fw-bold text-dark" id="detDni"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="small text-muted d-block mb-1">Correo Electrónico</label>
                                    <div class="fw-bold text-dark text-truncate" id="detEmail"></div>
                                </div>
                                <div class="mb-4">
                                    <label class="small text-muted d-block mb-1">Teléfono</label>
                                    <div class="fw-bold text-dark" id="detTel"></div>
                                </div>
                                <div>
                                    <label class="small text-muted d-block mb-1">Curso e Interés</label>
                                    <div class="badge bg-primary-soft text-primary mt-1" id="detCiclo"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna Derecha: Documentos y Acción -->
                    <div class="col-md-7">
                        <div class="d-flex flex-column gap-4">
                            <!-- Documentación -->
                            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                                <div class="card-body p-4">
                                    <h6 class="text-uppercase x-small fw-bold text-primary mb-3" style="letter-spacing: 0.05em;">Documentación adjunta</h6>
                                    <div id="listaArchivos" class="d-grid gap-2">
                                        <!-- Archivos via JS -->
                                    </div>
                                </div>
                            </div>

                            <!-- Resolución -->
                            <div class="card border-0 shadow-sm border-start border-4 border-primary" style="border-radius: 1rem;">
                                <div class="card-body p-4">
                                    <h6 class="text-uppercase x-small fw-bold text-primary mb-3" style="letter-spacing: 0.05em;">Resolución del expediente</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="small text-muted mb-2 fw-medium">Cambiar estado a:</label>
                                            <select id="nuevoEstado" class="form-select border-0 bg-light rounded-3 py-2 px-3">
                                                <option value="EN_REVISION">En Revisión</option>
                                                <option value="ADMITIDO">Admitir Solicitante</option>
                                                <option value="RECHAZADO">Rechazar Solicitud</option>
                                                <option value="SUBSANACION">Solicitar Subsanación</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="small text-muted mb-2 fw-medium">Notas internas o motivo:</label>
                                            <textarea id="obsAdmin" class="form-control border-0 bg-light rounded-3 py-2 px-3" rows="3" placeholder="Escribe aquí las observaciones..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white p-4 border-0 shadow-top">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-muted fw-medium" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary px-5 py-2 rounded-3 fw-bold" id="btnGuardarEstado">
                    <i class="fas fa-save me-2"></i> Actualizar Expediente
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados para el CRM de admisiones */
    .bg-primary-soft { background-color: rgba(79, 70, 229, 0.1); color: #4f46e5; border-radius: 8px; padding: 6px 12px; }
    .x-small { font-size: 0.65rem; }
    
    .btn-action-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--dim);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0;
    }
    .btn-action-icon:hover {
        background: var(--accent);
        color: #fff;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .doc-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1.15rem;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 14px;
        text-decoration: none;
        color: #1e293b;
        transition: all 0.2s;
    }
    .doc-link:hover { background: #f8fafc; border-color: var(--accent); color: var(--accent); transform: translateY(-2px); box-shadow: var(--shadow-sm); }

    /* SOLUCIÓN RADICAL DE INTERACTIVIDAD */
    .modal-backdrop { z-index: 10000 !important; }
    .modal { z-index: 10001 !important; }
    .modal-content { pointer-events: all !important; position: relative; z-index: 10002; }
    
    /* Evitar conflictos con el dashboard shell */
    body.modal-open .scrim { display: none !important; }
    body.modal-open .app { filter: blur(2px); pointer-events: none; }
    body.modal-open .modal { pointer-events: all; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentId = null;
let bModal = null;
const csrf_token = '<?php echo Security::generateCSRFToken(); ?>';

$(document).ready(function() {
    // Inicializar el modal una sola vez
    const modalEl = document.getElementById('modalDetalle');
    if (modalEl) {
        // Mover el modal al final del body para evitar problemas de stacking context
        document.body.appendChild(modalEl);
        bModal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true });
    }
});

function verDetalle(id) {
    currentId = id;
    $('#detId').text(id);
    $('#listaArchivos').html('<div class="text-center py-4 text-muted"><i class="fas fa-circle-notch fa-spin me-2"></i> Cargando...</div>');
    
    $.get('../../../controladores/admin/admisiones_acciones.php?action=get_details&id=' + id, function(res) {
        if (res.status === 'success') {
            const d = res.data;
            $('#detFullname').text(d.nombre + ' ' + d.apellidos);
            $('#detDni').text(d.dni);
            $('#detEmail').text(d.email);
            $('#detTel').text(d.telefono || 'No indicado');
            $('#detCiclo').text(d.nombreCiclo + ' (' + d.curso + ')');
            $('#nuevoEstado').val(d.estado);
            $('#obsAdmin').val(d.observaciones || '');

            let htmlArchivos = '';
            if (!res.archivos || res.archivos.length === 0) {
                htmlArchivos = `<div class="text-center py-4 text-muted border border-dashed rounded-3 bg-light"><div class="small fw-medium">Sin documentos</div></div>`;
            } else {
                res.archivos.forEach(f => {
                    let icon = f.tipoDocumento.includes('DNI') ? 'fa-id-card' : (f.tipoDocumento.includes('EXPEDIENTE') ? 'fa-graduation-cap' : 'fa-file-alt');
                    htmlArchivos += `
                        <a href="${f.rutaArchivo}" target="_blank" class="doc-link">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary-soft p-2 rounded-3" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;"><i class="fas ${icon}"></i></div>
                                <div><div class="small fw-bold">${f.tipoDocumento}</div><div class="x-small text-muted opacity-75">Ver archivo</div></div>
                            </div>
                            <i class="fas fa-external-link-alt opacity-25"></i>
                        </a>`;
                });
            }
            $('#listaArchivos').html(htmlArchivos);

            if (bModal) bModal.show();
        }
    });
}

$('#btnGuardarEstado').on('click', function() {
    if (!currentId) return;
    const $btn = $(this);
    const originalHtml = $btn.html();
    
    $btn.html('<i class="fas fa-sync fa-spin me-2"></i> Guardando...').prop('disabled', true);

    const data = {
        id: currentId,
        estado: $('#nuevoEstado').val(),
        observaciones: $('#obsAdmin').val(),
        csrf_token: csrf_token
    };

    $.post('../../../controladores/admin/admisiones_acciones.php?action=update_status', data, function(res) {
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: '¡Actualizado!', timer: 2000, showConfirmButton: false });
            if (bModal) bModal.hide();
            setTimeout(() => window.location.reload(), 1600);
        } else {
            $btn.html(originalHtml).prop('disabled', false);
            Swal.fire('Error', res.message, 'error');
        }
    });
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


