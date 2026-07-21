<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_estudiante'] ?? [];
unset($_SESSION['datos_estudiante']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

$idEstudiante = (int)($_GET['id'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$ciclos = listarTodosLosCiclos();
$todosLosCursos = listarTodosLosCursosAcademicos();

// Repopulate: prefer session datos (after validation failure), fall back to DB
$valorCampo = fn($field) => Security::escapeHtml($datos[$field] ?? $estudiante[$field] ?? '');

$titulo_pagina = 'AULAPRO | EDITAR ESTUDIANTE';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1>EDITAR ESTUDIANTE</h1>
        <p class="subtitulo-encabezado"><?= Security::escapeHtml(mb_strtoupper($estudiante['nombreEstudiante'], 'UTF-8')) ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="verDetallesEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/estudiantes/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">
        <input type="hidden" name="fechaAltaEstudiante" value="<?= Security::escapeHtml($estudiante['fechaAltaEstudiante'] ?? '') ?>">

        <div class="form-fila">
            <!-- Nombre -->
            <div class="campo<?= fieldClass($errores, 'nombre') ?>">
                <label for="nombre">Nombre completo</label>
                <input type="text" name="nombre" id="nombre" value="<?= $valorCampo('nombreEstudiante') ?>">
                <?= fieldError($errores, 'nombre') ?>
            </div>

            <!-- Email -->
            <div class="campo<?= fieldClass($errores, 'email') ?>">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?= $valorCampo('emailEstudiante') ?>">
                <?= fieldError($errores, 'email') ?>
            </div>
        </div>

        <div class="form-fila">
            <!-- DNI -->
            <div class="campo<?= fieldClass($errores, 'dni') ?>">
                <label for="dni">DNI</label>
                <input type="text" name="dni" id="dni" value="<?= $valorCampo('dniEstudiante') ?>">
                <?= fieldError($errores, 'dni') ?>
            </div>

            <!-- Teléfono -->
            <div class="campo<?= fieldClass($errores, 'telefono') ?>">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" value="<?= $valorCampo('telefonoEstudiante') ?>">
                <?= fieldError($errores, 'telefono') ?>
            </div>

            <!-- Fecha Nacimiento -->
            <div class="campo<?= fieldClass($errores, 'fechaNacimiento') ?>">
                <label for="fechaNacimiento">Fecha de nacimiento</label>
                <input type="date" name="fechaNacimiento" id="fechaNacimiento" value="<?= $valorCampo('fechaNacimientoEstudiante') ?>">
                <?= fieldError($errores, 'fechaNacimiento') ?>
            </div>
        </div>

        <div class="form-fila">
            <!-- Nivel / Curso -->
            <div class="campo<?= fieldClass($errores, 'curso') ?>">
                <label for="curso">Nivel</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="Grado Medio" <?= (($datos['curso'] ?? $estudiante['curso'] ?? '') === 'Grado Medio') ? 'selected' : '' ?>>Grado Medio</option>
                    <option value="Grado Superior" <?= (($datos['curso'] ?? $estudiante['curso'] ?? '') === 'Grado Superior') ? 'selected' : '' ?>>Grado Superior</option>
                </select>
                <?= fieldError($errores, 'curso') ?>
            </div>

            <!-- Ciclo formativo -->
            <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
                <label for="idCiclo">Ciclo formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">— Seleccionar —</option>
                </select>
                <?= fieldError($errores, 'idCiclo') ?>
            </div>

            <!-- Año de estudio -->
            <div class="campo">
                <label for="anioEstudio">Año de estudio</label>
                <select name="anioEstudio" id="anioEstudio">
                    <option value="">— Sin especificar —</option>
                </select>
            </div>
        </div>

        <!-- Dirección -->
        <div class="campo ancho-total<?= fieldClass($errores, 'direccion') ?>">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= $valorCampo('direccionEstudiante') ?>">
            <?= fieldError($errores, 'direccion') ?>
        </div>

        <div class="form-fila">
            <!-- Ciudad -->
            <div class="campo<?= fieldClass($errores, 'ciudad') ?>">
                <label for="ciudad">Ciudad</label>
                <input type="text" name="ciudad" id="ciudad" value="<?= $valorCampo('ciudadEstudiante') ?>">
                <?= fieldError($errores, 'ciudad') ?>
            </div>

            <!-- Código Postal -->
            <div class="campo<?= fieldClass($errores, 'codigoPostal') ?>">
                <label for="codigoPostal">Código Postal</label>
                <input type="text" name="codigoPostal" id="codigoPostal" value="<?= $valorCampo('codigoPostalEstudiante') ?>">
                <?= fieldError($errores, 'codigoPostal') ?>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="campo ancho-total">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="4"><?= Security::escapeHtml($datos['observaciones'] ?? $estudiante['observacionesEstudiante'] ?? '') ?></textarea>
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="verDetallesEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
var listaDeCiclos = <?= json_encode($ciclos) ?>;
var idCicloActual = <?= (int)($datos['idCiclo'] ?? $estudiante['idCiclo'] ?? 0) ?>;
var todosCursos = <?= json_encode($todosLosCursos) ?>;
var anioEstudioActual = <?= json_encode($datos['anioEstudio'] ?? $estudiante['anioEstudio'] ?? '') ?>;

function filtrarCiclos() {
    var nivelNombre = document.getElementById('curso').value;
    var sel = document.getElementById('idCiclo');
    sel.innerHTML = '<option value="">— Seleccionar —</option>';
    listaDeCiclos.forEach(function(c) {
        if (c.nombreNivel === nivelNombre) {
            var opt = document.createElement('option');
            opt.value = c.idCiclo;
            opt.textContent = c.nombreCiclo;
            if (parseInt(c.idCiclo) === idCicloActual) opt.selected = true;
            sel.appendChild(opt);
        }
    });
    poblarAnios();
}

function poblarAnios() {
    var idCiclo = document.getElementById('idCiclo').value;
    var sel = document.getElementById('anioEstudio');
    sel.innerHTML = '<option value="">— Sin especificar —</option>';
    todosCursos.forEach(function(c) {
        if (String(c.idCiclo) === String(idCiclo)) {
            var opt = document.createElement('option');
            opt.value = c.nombre;
            opt.textContent = c.nombre + ' año';
            if (c.nombre === anioEstudioActual) opt.selected = true;
            sel.appendChild(opt);
        }
    });
}

document.getElementById('idCiclo').addEventListener('change', poblarAnios);
document.addEventListener('DOMContentLoaded', function() { filtrarCiclos(); });
</script>
