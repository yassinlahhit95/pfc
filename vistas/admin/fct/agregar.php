<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fct');
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_fct'] ?? [];
unset($_SESSION['datos_fct']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/fp_dual.php";

$ciclos = listarTodosLosCiclos();
$idCicloSeleccionado = (int)($datos['idCiclo'] ?? $_GET['idCiclo'] ?? ($ciclos[0]['idCiclo'] ?? 0));
$estudiantes = $idCicloSeleccionado ? listarEstudiantesPorCiclo($idCicloSeleccionado) : [];
$profesores = listarProfesores();
$empresas = listarEmpresas();

$titulo_pagina = "AULAPRO | NUEVA FCT";
$seccion = 'fct';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVA FCT</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="GET" class="formulario margen-abajo">
        <div class="campo">
            <label for="idCiclo">Ciclo formativo</label>
            <select id="idCiclo" name="idCiclo" onchange="this.form.submit()">
                <?php foreach ($ciclos as $ciclo): ?>
                <option value="<?= (int)$ciclo['idCiclo'] ?>" <?= (int)$ciclo['idCiclo'] === $idCicloSeleccionado ? 'selected' : '' ?>>
                    [<?= Security::escapeHtml($ciclo['nombreNivel']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <form method="POST" action="../../../controladores/admin/fct/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idCiclo" value="<?= $idCicloSeleccionado ?>">

        <div class="campo">
            <label for="idEstudiante">Estudiante</label>
            <select id="idEstudiante" name="idEstudiante" required>
                <option value="">-- Selecciona un estudiante --</option>
                <?php foreach ($estudiantes as $est): ?>
                <option value="<?= (int)$est['idEstudiante'] ?>" <?= (isset($datos['idEstudiante']) && (int)$datos['idEstudiante'] === (int)$est['idEstudiante']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($est['nombreEstudiante']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idEstudiante') ?>
        </div>

        <div class="campo">
            <label for="idProfesorTutor">Profesor/a tutor (opcional)</label>
            <select id="idProfesorTutor" name="idProfesorTutor">
                <option value="">-- Sin asignar --</option>
                <?php foreach ($profesores as $prof): ?>
                <option value="<?= (int)$prof['idProfesor'] ?>" <?= (isset($datos['idProfesorTutor']) && (int)$datos['idProfesorTutor'] === (int)$prof['idProfesor']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($prof['nombreProfesor']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="fase">Fase / convocatoria</label>
            <input type="number" id="fase" name="fase" min="1" value="<?= Security::escapeHtml($datos['fase'] ?? '1') ?>">
            <small class="texto-suave">Solo súbelo de 1 si esta es una repetición de la FCT.</small>
        </div>

        <div class="campo">
            <label for="empresaExistente">Empresa colaboradora (si ya está en el directorio)</label>
            <select id="empresaExistente" onchange="fctRellenarEmpresa()">
                <option value="">-- Escribir empresa nueva abajo --</option>
                <?php foreach ($empresas as $emp): ?>
                <option value="<?= (int)$emp['idEmpresa'] ?>" data-nombre="<?= Security::escapeHtml($emp['nombre']) ?>"><?= Security::escapeHtml($emp['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" id="idEmpresa" name="idEmpresa" value="<?= Security::escapeHtml($datos['idEmpresa'] ?? '') ?>">

        <div class="campo">
            <label for="empresa">Nombre de la empresa *</label>
            <input type="text" id="empresa" name="empresa" maxlength="200" required value="<?= Security::escapeHtml($datos['empresa'] ?? '') ?>">
            <?= fieldError($errores, 'empresa') ?>
        </div>

        <div class="campo">
            <label for="ciudadEmpresa">Ciudad</label>
            <input type="text" id="ciudadEmpresa" name="ciudadEmpresa" maxlength="100" value="<?= Security::escapeHtml($datos['ciudadEmpresa'] ?? '') ?>">
        </div>

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

        <div class="campo">
            <label for="fechaInicio">Fecha de inicio</label>
            <input type="date" id="fechaInicio" name="fechaInicio" value="<?= Security::escapeHtml($datos['fechaInicio'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="fechaFin">Fecha de fin</label>
            <input type="date" id="fechaFin" name="fechaFin" value="<?= Security::escapeHtml($datos['fechaFin'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="horasTotales">Horas totales requeridas</label>
            <input type="number" id="horasTotales" name="horasTotales" min="0" value="<?= Security::escapeHtml($datos['horasTotales'] ?? '') ?>">
        </div>

        <div class="acciones">
            <input type="submit" class="boton-primario" value="GUARDAR FCT">
            <a href="lista.php" class="boton-secundario">CANCELAR</a>
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
