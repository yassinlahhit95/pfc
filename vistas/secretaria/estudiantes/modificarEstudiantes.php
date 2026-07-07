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

$idEstudiante = (int)($_GET['id'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$ciclos = listarTodosLosCiclos();

// Repopulate: prefer session datos (after validation failure), fall back to DB
$v = fn($field) => Security::escapeHtml($datos[$field] ?? $estudiante[$field] ?? '');

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

        <!-- Nombre -->
        <div class="campo<?= fieldClass($errores, 'nombre') ?>">
            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= $v('nombreEstudiante') ?>">
            <?= fieldError($errores, 'nombre') ?>
        </div>

        <!-- Email -->
        <div class="campo<?= fieldClass($errores, 'email') ?>">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= $v('emailEstudiante') ?>">
            <?= fieldError($errores, 'email') ?>
        </div>

        <!-- DNI -->
        <div class="campo<?= fieldClass($errores, 'dni') ?>">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" value="<?= $v('dniEstudiante') ?>">
            <?= fieldError($errores, 'dni') ?>
        </div>

        <!-- Teléfono -->
        <div class="campo<?= fieldClass($errores, 'telefono') ?>">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= $v('telefonoEstudiante') ?>">
            <?= fieldError($errores, 'telefono') ?>
        </div>

        <!-- Fecha Nacimiento -->
        <div class="campo<?= fieldClass($errores, 'fechaNacimiento') ?>">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" value="<?= $v('fechaNacimientoEstudiante') ?>">
            <?= fieldError($errores, 'fechaNacimiento') ?>
        </div>

        <!-- Nivel / Curso -->
        <div class="campo<?= fieldClass($errores, 'curso') ?>">
            <label for="curso">Nivel</label>
            <select name="curso" id="curso" onchange="filtrarCiclos()">
                <option value="Grado Medio" <?= (($datos['curso'] ?? $estudiante['curso'] ?? '') === 'Grado Medio') ? 'selected' : '' ?>>Grado Medio</option>
                <option value="Grado Superior" <?= (($datos['curso'] ?? $estudiante['curso'] ?? '') === 'Grado Superior') ? 'selected' : '' ?>>Grado Superior</option>
            </select>
            <?= fieldError($errores, 'curso') ?>
        </div>

        <!-- Año de estudio -->
        <div class="campo">
            <label for="anioEstudio">Año de estudio</label>
            <select name="anioEstudio" id="anioEstudio">
                <option value="">— Sin especificar —</option>
                <option value="1º" <?= (($datos['anioEstudio'] ?? $estudiante['anioEstudio'] ?? '') == '1º') ? 'selected' : '' ?>>1º año</option>
                <option value="2º" <?= (($datos['anioEstudio'] ?? $estudiante['anioEstudio'] ?? '') == '2º') ? 'selected' : '' ?>>2º año</option>
            </select>
        </div>

        <!-- Ciclo formativo -->
        <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
            <label for="idCiclo">Ciclo formativo</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">— Seleccionar —</option>
            </select>
            <?= fieldError($errores, 'idCiclo') ?>
        </div>

        <!-- Dirección -->
        <div class="campo ancho-total<?= fieldClass($errores, 'direccion') ?>">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= $v('direccionEstudiante') ?>">
            <?= fieldError($errores, 'direccion') ?>
        </div>

        <!-- Ciudad -->
        <div class="campo<?= fieldClass($errores, 'ciudad') ?>">
            <label for="ciudad">Ciudad</label>
            <input type="text" name="ciudad" id="ciudad" value="<?= $v('ciudadEstudiante') ?>">
            <?= fieldError($errores, 'ciudad') ?>
        </div>

        <!-- Código Postal -->
        <div class="campo<?= fieldClass($errores, 'codigoPostal') ?>">
            <label for="codigoPostal">Código Postal</label>
            <input type="text" name="codigoPostal" id="codigoPostal" value="<?= $v('codigoPostalEstudiante') ?>">
            <?= fieldError($errores, 'codigoPostal') ?>
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

function filtrarCiclos() {
    var nivelNombre = document.getElementById('curso').value;
    var nivelId = nivelNombre === 'Grado Medio' ? 1 : 2;
    var sel = document.getElementById('idCiclo');
    sel.innerHTML = '<option value="">— Seleccionar —</option>';
    listaDeCiclos.forEach(function(c) {
        if (parseInt(c.idNivel) === nivelId) {
            var opt = document.createElement('option');
            opt.value = c.idCiclo;
            opt.textContent = c.nombreCiclo;
            if (parseInt(c.idCiclo) === idCicloActual) opt.selected = true;
            sel.appendChild(opt);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() { filtrarCiclos(); });
</script>
