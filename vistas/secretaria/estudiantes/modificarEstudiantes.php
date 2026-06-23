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
    <h1>EDITAR ESTUDIANTE</h1>
    <div class="acciones-pagina">
        <a href="verDetallesEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/estudiantes/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">

        <div class="campo<?= fieldClass($errores, 'nombre') ?>">
            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= $v('nombreEstudiante') ?>">
            <?= fieldError($errores, 'nombre') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'email') ?>">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= $v('emailEstudiante') ?>">
            <?= fieldError($errores, 'email') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
            <label for="idCiclo">Ciclo formativo</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">— Seleccionar —</option>
                <?php
                $idCicloActual = (int)($datos['idCiclo'] ?? $estudiante['idCiclo'] ?? 0);
                foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>" <?= ($idCicloActual === (int)$c['idCiclo']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idCiclo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'dni') ?>">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" value="<?= $v('dniEstudiante') ?>">
            <?= fieldError($errores, 'dni') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'telefono') ?>">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= $v('telefonoEstudiante') ?>">
            <?= fieldError($errores, 'telefono') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaNacimiento') ?>">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" value="<?= $v('fechaNacimientoEstudiante') ?>">
            <?= fieldError($errores, 'fechaNacimiento') ?>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'direccion') ?>">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= $v('direccionEstudiante') ?>">
            <?= fieldError($errores, 'direccion') ?>
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="verDetallesEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
