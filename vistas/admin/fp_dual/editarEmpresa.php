<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";
require_once __DIR__ . "/../../../modelos/fp_dual.php";

FeatureGuard::requirePage('feature_fp_dual');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idEmpresa = (int)($_GET['idEmpresa'] ?? 0);
$empresa = obtenerEmpresaPorId($idEmpresa);

if (!$empresa) {
    header("Location: verEmpresas.php");
    exit;
}

$datos = $_SESSION['datos_empresa'] ?? [];
unset($_SESSION['datos_empresa']);
if (!empty($datos)) {
    $empresa = $datos + $empresa;
}

$titulo_pagina = "AULAPRO | EDITAR EMPRESA";
$seccion = 'fp_dual';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR EMPRESA: <?= Security::escapeHtml($empresa['nombre']) ?></h1>
    <a href="verEmpresas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/fp_dual/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idEmpresa" value="<?= $idEmpresa ?>">
        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombre') ?>">
                <label for="nombre">Nombre de la Empresa</label>
                <input type="text" name="nombre" id="nombre" value="<?= Security::escapeHtml($empresa['nombre'] ?? '') ?>" required>
                <?= fieldError($errores, 'nombre') ?>
            </div>
            <div class="campo">
                <label for="cif">CIF / NIF</label>
                <input type="text" name="cif" id="cif" value="<?= Security::escapeHtml($empresa['cif'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="direccion">Dirección completa</label>
                <input type="text" name="direccion" id="direccion" value="<?= Security::escapeHtml($empresa['direccion'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="contacto">Persona de Contacto</label>
                <input type="text" name="contacto" id="contacto" value="<?= Security::escapeHtml($empresa['persona_contacto'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" value="<?= Security::escapeHtml($empresa['telefono'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= Security::escapeHtml($empresa['email'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="activo">Estado</label>
                <select name="activo" id="activo">
                    <option value="1" <?= ($empresa['activo'] == 1) ? 'selected' : '' ?>>Activa</option>
                    <option value="0" <?= ($empresa['activo'] == 0) ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEmpresa" class="boton-primario" value="GUARDAR CAMBIOS">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
