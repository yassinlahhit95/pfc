<?php
require_once __DIR__ . "/../../include/FeatureGuard.php";
if (!FeatureGuard::check('feature_prematricula')) {
    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'>
            <h2>Módulo de Admisiones Deshabilitado</h2>
            <p>La consulta de estado no está disponible en este momento.</p>
            <a href='/'>Volver al inicio</a>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Consultar Estado de Admisión | AulaPro</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; color: #1e293b; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .consult-card { background: white; padding: 2.5rem; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); width: 100%; max-width: 500px; }
        .status-badge { padding: 0.5rem 1rem; border-radius: 50rem; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; }
        .status-PENDIENTE { background: #fef3c7; color: #92400e; }
        .status-EN_REVISION { background: #e0f2fe; color: #075985; }
        .status-ADMITIDO { background: #dcfce7; color: #166534; }
        .status-RECHAZADO { background: #fee2e2; color: #991b1b; }
        .status-SUBSANACION { background: #f1f5f9; color: #475569; }
    </style>
</head>
<body>

<div class="container p-3">
    <div class="consult-card mx-auto">
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                <i class="fas fa-search-location fa-2x text-primary"></i>
            </div>
            <h2 class="fw-bold">Estado de tu Solicitud</h2>
            <p class="text-muted">Introduce tu DNI para conocer el estado de tu pre-matrícula</p>
        </div>

        <form id="formConsultar">
            <div class="mb-4">
                <label class="form-label fw-bold small text-uppercase">DNI / NIE</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                    <input type="text" id="dni" class="form-control bg-light border-start-0" placeholder="12345678X" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3" id="btnConsultar">
                <i class="fas fa-search me-2"></i> Consultar Estado
            </button>
        </form>

        <div id="resultado" class="mt-5 d-none">
            <hr class="opacity-10 mb-4">
            <div class="text-center mb-4">
                <div id="statusBadge" class="status-badge mb-3"></div>
                <h4 class="fw-bold mb-1" id="resNombre"></h4>
                <p class="small text-muted" id="resCiclo"></p>
            </div>

            <div class="p-3 bg-light rounded-3 mb-4">
                <label class="small text-muted d-block mb-1">Fecha de solicitud:</label>
                <div class="fw-bold" id="resFecha"></div>
            </div>

            <div id="divObservaciones" class="p-3 border border-warning bg-warning bg-opacity-10 rounded-3 d-none">
                <label class="small text-warning-emphasis d-block mb-1 fw-bold"><i class="fas fa-exclamation-circle me-1"></i> Notas del Centro:</label>
                <div class="small" id="resObservaciones"></div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none small text-muted"><i class="fas fa-arrow-left me-1"></i> Volver al inicio</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#formConsultar').submit(function(e) {
        e.preventDefault();
        const dni = $('#dni').val();
        const $btn = $('#btnConsultar');
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Buscando...');
        $('#resultado').addClass('d-none');

        $.post('../../controladores/admisiones/acciones.php?action=consultar_estado', { dni: dni }, function(res) {
            $btn.prop('disabled', false).html('<i class="fas fa-search me-2"></i> Consultar Estado');
            
            if (res.status === 'success') {
                const d = res.data;
                $('#resNombre').text(d.nombre);
                $('#resCiclo').text(d.ciclo);
                $('#resFecha').text(d.fecha);
                
                // Badge de estado
                let icon = 'fa-clock';
                if (d.estado === 'ADMITIDO') icon = 'fa-check-circle';
                if (d.estado === 'RECHAZADO') icon = 'fa-times-circle';
                if (d.estado === 'SUBSANACION') icon = 'fa-exclamation-triangle';
                
                $('#statusBadge')
                    .attr('class', 'status-badge status-' + d.estado)
                    .html(`<i class="fas ${icon}"></i> ${d.estado}`);

                // Observaciones
                if (d.observaciones) {
                    $('#divObservaciones').removeClass('d-none');
                    $('#resObservaciones').text(d.observaciones);
                } else {
                    $('#divObservaciones').addClass('d-none');
                }

                $('#resultado').removeClass('d-none');
            } else {
                Swal.fire('Atención', res.message || 'Error al consultar', 'warning');
            }
        });
    });
});
</script>
</body>
</html>
