<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$articulos   = listarArticulos();
$estudiantes = listarEstudiantes();
$preselId    = (int)($_GET['id'] ?? 0);

$titulo_pagina = "AULAPRO | NUEVO PRÉSTAMO";
$seccion = 'prestamos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO PRÉSTAMO</h1>
    <a href="gestionarPrestamos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/inventario/prestar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'idArticulo') ?>">
            <label for="idArticulo">Artículo <span style="color:#ef4444">*</span></label>
            <select name="idArticulo" id="idArticulo">
                <option value="">— Selecciona artículo —</option>
                <?php foreach ($articulos as $art): ?>
                <option value="<?= (int)$art['idArticulo'] ?>"
                    <?= ($art['idArticulo'] == $preselId) ? 'selected' : '' ?>
                    <?= (($art['estado'] ?? '') === 'prestado') ? 'disabled' : '' ?>>
                    <?= Security::escapeHtml($art['nombreArticulo']) ?>
                    <?= ($art['estado'] === 'prestado') ? ' (prestado)' : '' ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idArticulo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'idEstudiante') ?>">
            <label for="idEstudiante">Estudiante <span style="color:#ef4444">*</span></label>
            <select name="idEstudiante" id="idEstudiante">
                <option value="">— Selecciona estudiante —</option>
                <?php foreach ($estudiantes as $est): ?>
                <option value="<?= (int)$est['idEstudiante'] ?>">
                    <?= Security::escapeHtml($est['nombreEstudiante']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaPrestamo') ?>">
            <label for="fechaPrestamo">Fecha de préstamo <span style="color:#ef4444">*</span></label>
            <input type="date" name="fechaPrestamo" id="fechaPrestamo"
                   value="<?= date('Y-m-d') ?>">
            <?= fieldError($errores, 'fechaPrestamo') ?>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> REGISTRAR PRÉSTAMO</button>
            <a href="gestionarPrestamos.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
