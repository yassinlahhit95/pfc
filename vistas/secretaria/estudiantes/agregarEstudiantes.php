<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_estudiante'] ?? [];
unset($_SESSION['datos_estudiante']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
$ciclos = listarTodosLosCiclos();

$titulo_pagina = 'AULAPRO | NUEVO ESTUDIANTE';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/estudiantes/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'nombre') ?>">
            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= Security::escapeHtml($datos['nombre'] ?? '') ?>">
            <?= fieldError($errores, 'nombre') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'email') ?>">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= Security::escapeHtml($datos['email'] ?? '') ?>">
            <?= fieldError($errores, 'email') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'password') ?>">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" autocomplete="new-password">
            <?= fieldError($errores, 'password') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
            <label for="idCiclo">Ciclo formativo</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">— Seleccionar —</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>" <?= ((int)($datos['idCiclo'] ?? 0) === (int)$c['idCiclo']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idCiclo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'dni') ?>">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" value="<?= Security::escapeHtml($datos['dni'] ?? '') ?>">
            <?= fieldError($errores, 'dni') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'telefono') ?>">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= Security::escapeHtml($datos['telefono'] ?? '') ?>">
            <?= fieldError($errores, 'telefono') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaNacimiento') ?>">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" value="<?= Security::escapeHtml($datos['fechaNacimiento'] ?? '') ?>">
            <?= fieldError($errores, 'fechaNacimiento') ?>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'direccion') ?>">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= Security::escapeHtml($datos['direccion'] ?? '') ?>">
            <?= fieldError($errores, 'direccion') ?>
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="verEstudiantes.php" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
