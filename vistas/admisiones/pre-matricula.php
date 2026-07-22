<?php
require_once __DIR__ . "/../../modelos/ciclos.php";
require_once __DIR__ . "/../../modelos/configuracion.php";
require_once __DIR__ . "/../../include/FeatureGuard.php";

if (!FeatureGuard::check('feature_prematricula')) {
    header('Location: /?admisiones=desactivado');
    exit;
}

$configCentro   = obtenerConfiguracionCentro();
$filtrarNiveles = (int)($configCentro['prematricula_filtrar_niveles'] ?? 0);
$ciclos         = listarTodosLosCiclos();

$legal_titulo = 'Pre-Matrícula';
$legal_pagina = 'prematricula';
$extra_css    = [
    [
        'url'       => 'https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.min.css',
        'integrity' => 'sha384-dCW5imOdApH6OwpFau8cZNKjqVbJYnCA5q+8YsMYP3XwXKsV6Jfz1u6MZLnXaBsS',
    ],
    '/public/css/features/admisiones.css',
];

require __DIR__ . '/../legal/_header.php';
?>

<div class="legal-hero">
    <h1><i class="fas fa-user-plus" style="margin-right:10px;opacity:.9;"></i>Proceso de Pre-Matrícula</h1>
    <span class="badge">Solicitud online &middot; Respuesta en 48&nbsp;h</span>
</div>

<main class="admisiones-main">
<div class="asistente-container">

    <!-- ── Progress steps ── -->
    <div class="asistente-steps">
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

    <!-- ── Form content ── -->
    <div class="asistente-content">

        <!-- Step 1: Datos Personales -->
        <div class="step-content active" data-step="1">

            <p class="form-section-title">Información del Solicitante</p>
            <div class="form-grid">
                <div>
                    <label class="form-label" for="dni">DNI / NIE *</label>
                    <input type="text" id="dni" class="form-control" placeholder="12345678X" required>
                </div>
                <div>
                    <label class="form-label" for="nombre">Nombre *</label>
                    <input type="text" id="nombre" class="form-control" required>
                </div>
                <div class="full">
                    <label class="form-label" for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" class="form-control" required>
                </div>
                <div>
                    <label class="form-label" for="email">Email *</label>
                    <input type="email" id="email" class="form-control" placeholder="ejemplo@correo.com" required>
                </div>
                <div>
                    <label class="form-label" for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" class="form-control">
                </div>
                <?php if ($filtrarNiveles === 1): ?>
                <div class="full" style="margin-top: 10px;">
                    <label class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                        <span>Filtrar por Nivel de Grado</span>
                        <span class="text-muted" style="font-size:11px; font-weight:normal;">Selecciona para reducir la lista</span>
                    </label>
                    <div class="nivel-filter-tabs" style="display:flex; gap: 8px;">
                        <button type="button" class="btn active-tab" data-filter="all" style="flex:1; padding: 7px 10px; font-size: 12px; font-weight: 600; border-radius: 8px; border: 1.5px solid var(--accent); background: var(--accent); color: white; cursor: pointer; transition: all 0.2s;">Todos</button>
                        <button type="button" class="btn" data-filter="medio" style="flex:1; padding: 7px 10px; font-size: 12px; font-weight: 600; border-radius: 8px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text-muted); cursor: pointer; transition: all 0.2s;">Grado Medio</button>
                        <button type="button" class="btn" data-filter="superior" style="flex:1; padding: 7px 10px; font-size: 12px; font-weight: 600; border-radius: 8px; border: 1.5px solid var(--border); background: var(--surface); color: var(--text-muted); cursor: pointer; transition: all 0.2s;">Grado Superior</button>
                    </div>
                </div>
                <?php endif; ?>
                <div>
                    <label class="form-label" for="idCiclo">Ciclo Formativo *</label>
                    <select id="idCiclo" class="form-select" required>
                        <option value="">Selecciona un ciclo…</option>
                        <?php foreach ($ciclos as $ciclo): ?>
                        <option value="<?= (int)$ciclo['idCiclo'] ?>" data-nivel="<?= htmlspecialchars(strtolower($ciclo['nombreNivel'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($ciclo['nombreCiclo'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="curso">Curso *</label>
                    <select id="curso" class="form-select">
                        <option value="1º">1º Curso</option>
                        <option value="2º">2º Curso</option>
                    </select>
                </div>
            </div>

            <hr class="form-section-sep">

            <p class="form-section-title">Datos del Tutor Legal</p>
            <p class="form-section-desc">Información del padre, madre o representante legal responsable.</p>
            <div class="form-grid">
                <div>
                    <label class="form-label" for="nombreTutor">Nombre del Tutor *</label>
                    <input type="text" id="nombreTutor" class="form-control" required>
                </div>
                <div>
                    <label class="form-label" for="dniTutor">DNI del Tutor *</label>
                    <input type="text" id="dniTutor" class="form-control" required>
                </div>
                <div>
                    <label class="form-label" for="emailTutor">Email del Tutor *</label>
                    <input type="email" id="emailTutor" class="form-control" required>
                </div>
                <div>
                    <label class="form-label" for="telefonoTutor">Teléfono del Tutor *</label>
                    <input type="tel" id="telefonoTutor" class="form-control" required>
                </div>
                <div class="full">
                    <label class="form-label" for="parentescoTutor">Parentesco *</label>
                    <select id="parentescoTutor" class="form-select" required>
                        <option value="Padre">Padre</option>
                        <option value="Madre">Madre</option>
                        <option value="Tutor Legal">Tutor Legal</option>
                    </select>
                </div>
                <div class="full">
                    <div class="politica-box">
                        <div class="politica-check">
                            <input type="checkbox" id="aceptoRGPD" required>
                            <label for="aceptoRGPD">He leído y acepto la <a href="/vistas/legal/politica-de-privacidad.php" target="_blank">Política de Privacidad</a> y el tratamiento de mis datos personales.</label>
                        </div>
                        <p class="politica-note"><strong>Información básica:</strong> AulaPro tratará sus datos para gestionar su pre-matrícula y, en su caso, la formalización de su expediente académico. No se cederán datos a terceros salvo obligación legal. Tiene derecho a acceder, rectificar y suprimir sus datos escribiendo al centro.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Documentación -->
        <div class="step-content" data-step="2">
            <p class="form-section-title">Sube tu Documentación</p>
            <p class="form-section-desc">Por favor, sube una imagen o PDF de los siguientes documentos:</p>

            <div class="doc-item">
                <div class="doc-item-info">
                    <h5>DNI Frontal</h5>
                    <p>Imagen clara de la parte delantera</p>
                </div>
                <div class="doc-item-actions">
                    <div class="file-status" data-tipo="DNI_FRONTAL"></div>
                    <label class="upload-btn">
                        <i class="fas fa-upload" style="margin-right:6px;"></i>Seleccionar
                        <input type="file" class="file-input" data-tipo="DNI_FRONTAL" accept="image/*,.pdf">
                    </label>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-item-info">
                    <h5>DNI Reverso</h5>
                    <p>Imagen clara de la parte trasera</p>
                </div>
                <div class="doc-item-actions">
                    <div class="file-status" data-tipo="DNI_REVERSO"></div>
                    <label class="upload-btn">
                        <i class="fas fa-upload" style="margin-right:6px;"></i>Seleccionar
                        <input type="file" class="file-input" data-tipo="DNI_REVERSO" accept="image/*,.pdf">
                    </label>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-item-info">
                    <h5>Expediente Académico</h5>
                    <p>Documento que acredite tu nota media o acceso</p>
                </div>
                <div class="doc-item-actions">
                    <div class="file-status" data-tipo="EXPEDIENTE"></div>
                    <label class="upload-btn">
                        <i class="fas fa-upload" style="margin-right:6px;"></i>Seleccionar
                        <input type="file" class="file-input" data-tipo="EXPEDIENTE" accept="image/*,.pdf">
                    </label>
                </div>
            </div>
        </div>

        <!-- Step 3: Resumen -->
        <div class="step-content" data-step="3">
            <p class="form-section-title">Resumen de tu Solicitud</p>
            <div class="admisiones-info">
                <i class="fas fa-info-circle"></i>
                <span>Revisa que todos tus datos sean correctos antes de finalizar.</span>
            </div>
            <div class="resumen-card">
                <div class="resumen-row">
                    <span class="resumen-label">Nombre Completo</span>
                    <span class="resumen-val" id="summary-nombre">—</span>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Ciclo Seleccionado</span>
                    <span class="resumen-val" id="summary-ciclo">—</span>
                </div>
                <div class="resumen-row">
                    <span class="resumen-label">Email de Contacto</span>
                    <span class="resumen-val" id="summary-email">—</span>
                </div>
            </div>
        </div>

    </div><!-- /asistente-content -->

    <!-- ── Navigation ── -->
    <div class="asistente-buttons">
        <button class="btn-asistente btn-secondary-asistente btn-prev" style="display:none;">
            <i class="fas fa-arrow-left" style="margin-right:6px;"></i>Anterior
        </button>
        <button class="btn-asistente btn-primary-asistente btn-next">
            Siguiente<i class="fas fa-arrow-right" style="margin-left:6px;"></i>
        </button>
    </div>

</div><!-- /asistente-container -->
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script src="/public/js/features/admisiones.js"></script>

<?php require __DIR__ . '/../legal/_footer.php'; ?>
