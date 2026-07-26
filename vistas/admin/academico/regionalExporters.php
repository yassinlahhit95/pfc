<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

$exito  = $_SESSION['exito']  ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$ciclos = listarTodosLosCiclos();
$configActiva = obtenerConfigAcademicaActiva();
$idConfig = $configActiva['idConfig'] ?? null;

$titulo_pagina = "AULAPRO | GESTIÓN REGIONAL Y EXPORTADORES";
$seccion = 'regional_exporters';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
    .regional-tabs {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid var(--border, #e2e8f0);
        margin-bottom: 24px;
    }
    .tab-btn {
        padding: 12px 24px;
        border: none;
        background: none;
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-2, #64748b);
        cursor: pointer;
        transition: all .2s;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }
    .tab-btn:hover {
        color: var(--accent, #1e3a8a);
    }
    .tab-btn.activo {
        color: var(--accent, #1e3a8a);
        border-bottom-color: var(--accent, #1e3a8a);
    }
    .tab-content {
        display: none;
    }
    .tab-content.activo {
        display: block;
    }
    .instrucciones-caja {
        background: rgba(30, 58, 138, 0.04);
        border-left: 4px solid var(--accent, #1e3a8a);
        padding: 16px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
        line-height: 1.5;
    }
</style>

<div class="cabecera">
    <div>
        <h1>GESTIÓN REGIONAL & EXPORTACIONES</h1>
        <p class="subtitulo-encabezado">Conectividad e integración oficial con los sistemas educativos autonómicos de España.</p>
    </div>
</div>

<?php if ($exito): ?>
    <div class="alerta alerta-exito margen-abajo">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
    </div>
<?php endif; ?>

<?php if ($errores): ?>
    <div class="alerta alerta-error margen-abajo">
        <i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml($errores) ?>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="regional-tabs">
        <button class="tab-btn activo" onclick="switchTab(this, 'tab-importer')"><i class="fas fa-file-import"></i> Importador de Currículum Oficial</button>
        <button class="tab-btn" onclick="switchTab(this, 'tab-exporter')"><i class="fas fa-file-export"></i> Exportador Hezigune (País Vasco)</button>
    </div>

    <!-- TAB 1: CURRICULUM IMPORTER -->
    <div id="tab-importer" class="tab-content activo">
        <h2>Importar Plan de Estudios (BOPV / País Vasco)</h2>
        <div class="instrucciones-caja">
            <strong><i class="fas fa-info-circle"></i> Integración Oficial:</strong>
            Esta utilidad permite pre-cargar los <strong>Resultados de Aprendizaje (RA)</strong> y <strong>Criterios de Evaluación (CE)</strong> oficiales tal como están redactados en el Boletín Oficial del País Vasco (BOPV).
            Al aplicarlo, se vincularán automáticamente a los módulos correspondientes en tu base de datos, ahorrando horas de trabajo a los docentes.
        </div>

        <form action="../../../controladores/admin/academico/importarCurriculum.php" method="POST" class="formulario" style="max-width: 650px;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idConfig" value="<?= (int)$idConfig ?>">

            <div class="campo">
                <label for="idCicloImp">Ciclo Formativo de Destino</label>
                <select name="idCiclo" id="idCicloImp" required>
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php foreach ($ciclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>"><?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="archivoCurriculum">Plan de Estudios Oficial (BOPV)</label>
                <select name="archivoCurriculum" id="archivoCurriculum" required>
                    <option value="">-- Selecciona un plan oficial --</option>
                    <option value="daw_euskadi.json">País Vasco | Desarrollo de Aplicaciones Web (DAW)</option>
                    <option value="smr_euskadi.json">País Vasco | Sistemas Microinformáticos y Redes (SMR)</option>
                </select>
            </div>

            <div class="acciones" style="margin-top: 24px;">
                <button type="submit" class="boton-primario"><i class="fas fa-upload"></i> IMPORTAR PLAN DE ESTUDIOS</button>
            </div>
        </form>
    </div>

    <!-- TAB 2: HEZIGUNE EXPORTER -->
    <div id="tab-exporter" class="tab-content">
        <h2>Generar XML de Calificaciones para Hezigune</h2>
        <div class="instrucciones-caja">
            <strong><i class="fas fa-info-circle"></i> Eusko Jaurlaritza / Gobierno Vasco:</strong>
            Genera un archivo XML en el formato exacto requerido por el Departamento de Educación del Gobierno Vasco para la importación en su portal oficial de gestión académica (**Hezigune**).
            Las calificaciones numéricas finales se redondean automáticamente a enteros (1-10) y se gestionan los estados convalidados/exentos de acuerdo a la normativa autonómica de FP.
        </div>

        <form action="../../../controladores/admin/academico/exportarRegional.php" method="GET" class="formulario" style="max-width: 650px;">
            <div class="campo">
                <label for="idCicloExp">Ciclo Formativo</label>
                <select name="idCiclo" id="idCicloExp" required>
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php foreach ($ciclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>"><?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="anioEstudioExp">Año / Curso</label>
                <select name="anioEstudio" id="anioEstudioExp">
                    <option value="">-- Todos los años --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>

            <div class="campo">
                <label for="sistemaExp">Formato del Sistema Regional</label>
                <select name="sistema" id="sistemaExp" required>
                    <option value="euskadi_hezigune" selected>Euskadi (Hezigune XML Export)</option>
                </select>
            </div>

            <div class="acciones" style="margin-top: 24px;">
                <button type="submit" class="boton-primario"><i class="fas fa-file-download"></i> GENERAR Y DESCARGAR XML</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(btn, tabId) {
        // Toggle tab buttons
        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('activo');
        });
        btn.classList.add('activo');

        // Toggle tab contents
        document.querySelectorAll('.tab-content').forEach(function(c) {
            c.classList.remove('activo');
        });
        document.getElementById(tabId).classList.add('activo');
    }
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
