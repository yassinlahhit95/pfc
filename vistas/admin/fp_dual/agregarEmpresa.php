<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

FeatureGuard::requirePage('feature_fp_dual');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_empresa'] ?? [];
unset($_SESSION['datos_empresa']);

$titulo_pagina = "AULAPRO | AGREGAR EMPRESA DUAL";
$seccion = 'fp_dual';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>AGREGAR EMPRESA COLABORADORA</h1>
    <a href="verEmpresas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/fp_dual/insertar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombre') ?>">
                    <label for="nombre">Nombre de la Empresa</label>
                    <input type="text" name="nombre" id="nombre" value="<?= Security::escapeHtml($datos['nombre'] ?? '') ?>" required>
                    <?= fieldError($errores, 'nombre') ?>
                </div>
                <div class="campo">
                    <label for="cif">CIF / NIF</label>
                    <input type="text" name="cif" id="cif" value="<?= Security::escapeHtml($datos['cif'] ?? '') ?>">
                </div>
            </div>
            <div class="form-fila">
                <div class="campo">
                    <label for="direccion">Dirección completa</label>
                    <input type="text" name="direccion" id="direccion" value="<?= Security::escapeHtml($datos['direccion'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="contacto">Persona de Contacto</label>
                    <input type="text" name="contacto" id="contacto" value="<?= Security::escapeHtml($datos['contacto'] ?? '') ?>">
                </div>
            </div>
            <div class="form-fila">
                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" value="<?= Security::escapeHtml($datos['telefono'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= Security::escapeHtml($datos['email'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEmpresa" class="boton-primario" value="REGISTRAR EMPRESA">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
