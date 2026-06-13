<?php
require_once __DIR__ . "/../../modelos/ciclos.php";
require_once __DIR__ . "/../../modelos/configuracion.php";

$cfg_adm = obtenerConfiguracionCentro();
if (!$cfg_adm['feature_prematricula']) {
    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'>
            <h2>Módulo de Admisiones Deshabilitado</h2>
            <p>El portal de pre-matrícula no está disponible en este momento. Por favor, contacte con el centro.</p>
            <a href='/'>Volver al inicio</a>
         </div>");
}

$ciclos = listarTodosLosCiclos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Admisiones | AulaPro</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/public/css/admisiones.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .header-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 4rem 0 8rem;
            color: white;
            text-align: center;
        }
        .wizard-wrapper {
            margin-top: -6rem;
            padding-bottom: 4rem;
        }
    </style>
</head>
<body>

    <div class="header-bg">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">Proceso de Pre-Matriculación</h1>
            <p class="lead opacity-75">Completa tu solicitud en unos sencillos pasos</p>
        </div>
    </div>

    <div class="container wizard-wrapper">
        <div class="wizard-container">
            <!-- Steps Progress -->
            <div class="wizard-steps">
                <div class="step-item active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Datos Personales</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Documentación</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Confirmación</div>
                </div>
            </div>

            <!-- Wizard Content -->
            <div class="wizard-content">
                <!-- Step 1: Datos Personales -->
                <div class="step-content active" data-step="1">
                    <h3 class="mb-4">Información del Solicitante</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">DNI / NIE *</label>
                            <input type="text" id="dni" class="form-control" placeholder="12345678X" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" id="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" id="apellidos" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" id="telefono" class="form-control">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Ciclo Formativo *</label>
                            <select id="idCiclo" class="form-select form-control" required>
                                <option value="">Selecciona un ciclo...</option>
                                <?php foreach ($ciclos as $ciclo): ?>
                                    <option value="<?php echo $ciclo['idCiclo']; ?>">
                                        <?php echo htmlspecialchars($ciclo['nombreCiclo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Curso *</label>
                            <select id="curso" class="form-select form-control">
                                <option value="1º">1º Curso</option>
                                <option value="2º">2º Curso</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Documentación -->
                <div class="step-content" data-step="2">
                    <h3 class="mb-4">Sube tu Documentación</h3>
                    <p class="text-muted mb-4">Por favor, sube una imagen o PDF de los siguientes documentos:</p>
                    
                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">DNI Frontal</h5>
                                <p class="small text-muted mb-0">Imagen clara de la parte delantera</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="file-status" data-tipo="DNI_FRONTAL"></div>
                                <label class="btn btn-sm btn-outline-primary mb-0">
                                    Seleccionar <input type="file" class="file-input d-none" data-tipo="DNI_FRONTAL" accept="image/*,.pdf">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">DNI Reverso</h5>
                                <p class="small text-muted mb-0">Imagen clara de la parte trasera</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="file-status" data-tipo="DNI_REVERSO"></div>
                                <label class="btn btn-sm btn-outline-primary mb-0">
                                    Seleccionar <input type="file" class="file-input d-none" data-tipo="DNI_REVERSO" accept="image/*,.pdf">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="mb-1">Expediente Académico</h5>
                                <p class="small text-muted mb-0">Documento que acredite tu nota media o acceso</p>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="file-status" data-tipo="EXPEDIENTE"></div>
                                <label class="btn btn-sm btn-outline-primary mb-0">
                                    Seleccionar <input type="file" class="file-input d-none" data-tipo="EXPEDIENTE" accept="image/*,.pdf">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Resumen -->
                <div class="step-content" data-step="3">
                    <h3 class="mb-4">Resumen de tu Solicitud</h3>
                    <div class="alert alert-info">
                        <p class="mb-0"><i class="fas fa-info-circle me-2"></i> Revisa que todos tus datos sean correctos antes de finalizar.</p>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Nombre Completo:</div>
                                <div class="col-sm-8" id="summary-nombre">-</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Ciclo Seleccionado:</div>
                                <div class="col-sm-8" id="summary-ciclo">-</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold">Email de contacto:</div>
                                <div class="col-sm-8" id="summary-email">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="wizard-buttons">
                <button class="btn-wizard btn-secondary-wizard btn-prev" style="display:none;">Anterior</button>
                <button class="btn-wizard btn-primary-wizard btn-next ms-auto">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/public/js/admisiones.js"></script>
</body>
</html>
