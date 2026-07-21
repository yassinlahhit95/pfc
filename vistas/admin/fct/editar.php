<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fct');
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/fct.php";
require_once __DIR__ . "/../../../modelos/fp_dual.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idFCT = (int)($_GET['id'] ?? 0);
$fct = $idFCT ? obtenerFCTPorId($idFCT) : null;

if (!$fct) {
    $_SESSION['errores'] = 'FCT no encontrada.';
    header('Location: lista.php'); exit;
}

$datos = $_SESSION['datos_fct'] ?? $fct;
unset($_SESSION['datos_fct']);

$empresas = listarEmpresas();
$profesores = listarProfesores();

$titulo_pagina = "AULAPRO | EDITAR FCT";
$seccion = 'fct';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR FCT — <?= Security::escapeHtml($fct['nombreEstudiante']) ?></h1>
    <a href="lista.php?idCiclo=<?= (int)$fct['idCiclo'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores): ?>
<div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-triangle"></i>
    <?= is_array($errores) ? Security::escapeHtml(implode(' ', $errores)) : Security::escapeHtml($errores) ?>
</div>
<?php endif; ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/fct/actualizar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idFCT" value="<?= (int)$fct['idFCT'] ?>">

        <div class="form-fila">
            <div class="campo">
                <label for="idProfesorTutor">Profesor/a tutor</label>
                <select id="idProfesorTutor" name="idProfesorTutor">
                    <option value="">-- Sin asignar --</option>
                    <?php foreach ($profesores as $prof): ?>
                    <option value="<?= (int)$prof['idProfesor'] ?>" <?= ((int)($datos['idProfesorTutor'] ?? 0) === (int)$prof['idProfesor']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($prof['nombreProfesor']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="empresaExistente">Empresa colaboradora (si ya está en el directorio)</label>
                <select id="empresaExistente" onchange="fctRellenarEmpresa()">
                    <option value="">-- Escribir empresa nueva abajo --</option>
                    <?php foreach ($empresas as $emp): ?>
                    <option value="<?= (int)$emp['idEmpresa'] ?>" data-nombre="<?= Security::escapeHtml($emp['nombre']) ?>" <?= ((int)($datos['idEmpresa'] ?? 0) === (int)$emp['idEmpresa']) ? 'selected' : '' ?>><?= Security::escapeHtml($emp['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" id="idEmpresa" name="idEmpresa" value="<?= Security::escapeHtml($datos['idEmpresa'] ?? '') ?>">
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="empresa">Nombre de la empresa *</label>
                <input type="text" id="empresa" name="empresa" maxlength="200" required value="<?= Security::escapeHtml($datos['empresa'] ?? '') ?>">
                <?= fieldError($errores, 'empresa') ?>
            </div>

            <div class="campo">
                <label for="ciudadEmpresa">Ciudad</label>
                <input type="text" id="ciudadEmpresa" name="ciudadEmpresa" maxlength="100" value="<?= Security::escapeHtml($datos['ciudadEmpresa'] ?? '') ?>">
            </div>
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="tutorEmpresa">Tutor/a en la empresa</label>
                <input type="text" id="tutorEmpresa" name="tutorEmpresa" maxlength="150" value="<?= Security::escapeHtml($datos['tutorEmpresa'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="emailTutorEmpresa">Email del tutor/a</label>
                <input type="email" id="emailTutorEmpresa" name="emailTutorEmpresa" maxlength="150" value="<?= Security::escapeHtml($datos['emailTutorEmpresa'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="telefonoEmpresa">Teléfono del tutor/a</label>
                <input type="text" id="telefonoEmpresa" name="telefonoEmpresa" maxlength="20" value="<?= Security::escapeHtml($datos['telefonoEmpresa'] ?? '') ?>">
            </div>
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="fechaInicio">Fecha de inicio</label>
                <input type="date" id="fechaInicio" name="fechaInicio" value="<?= Security::escapeHtml($datos['fechaInicio'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="fechaFin">Fecha de fin</label>
                <input type="date" id="fechaFin" name="fechaFin" value="<?= Security::escapeHtml($datos['fechaFin'] ?? '') ?>">
            </div>
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="horasTotales">Horas totales requeridas</label>
                <input type="number" id="horasTotales" name="horasTotales" min="0" value="<?= Security::escapeHtml($datos['horasTotales'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="horasRealizadas">Horas realizadas</label>
                <input type="number" id="horasRealizadas" name="horasRealizadas" min="0" value="<?= Security::escapeHtml($datos['horasRealizadas'] ?? '') ?>">
            </div>
        </div>

        <div class="campo ancho-total"><hr style="border:none;border-top:1px solid var(--border);margin:4px 0;"></div>

        <div class="form-fila">
            <div class="campo">
                <label for="nota">Nota (0-10, opcional)</label>
                <input type="text" id="nota" name="nota" placeholder="Ej: 8.5" value="<?= Security::escapeHtml($datos['nota'] ?? '') ?>">
                <?= fieldError($errores, 'nota') ?>
            </div>

            <div class="campo">
                <label for="apto">Apto / No apto</label>
                <select id="apto" name="apto">
                    <option value="">-- Sin determinar --</option>
                    <option value="1" <?= (isset($datos['apto']) && $datos['apto'] !== null && (int)$datos['apto'] === 1) ? 'selected' : '' ?>>Apto</option>
                    <option value="0" <?= (isset($datos['apto']) && $datos['apto'] !== null && (int)$datos['apto'] === 0) ? 'selected' : '' ?>>No apto</option>
                </select>
            </div>
        </div>

        <div class="campo ancho-total">
            <label for="observaciones">Observaciones</label>
            <textarea id="observaciones" name="observaciones" rows="3"><?= Security::escapeHtml($datos['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
            <input type="submit" class="boton-primario" value="GUARDAR CAMBIOS">
            <a href="lista.php?idCiclo=<?= (int)$fct['idCiclo'] ?>" class="boton-secundario">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
function fctRellenarEmpresa() {
    var $sel = document.getElementById('empresaExistente');
    var opt = $sel.options[$sel.selectedIndex];
    document.getElementById('idEmpresa').value = $sel.value || '';
    if ($sel.value) {
        document.getElementById('empresa').value = opt.getAttribute('data-nombre') || '';
    }
}
</script>
