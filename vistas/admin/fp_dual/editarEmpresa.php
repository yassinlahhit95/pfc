<?php
require_once __DIR__ . "/../../../include/Security.php";
Security::initSession();
if (empty($_SESSION['idAdmin']) && empty($_SESSION['idSecretaria'])) {
    header("Location: /vistas/login.php");
    exit;
}
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

$isSecretaria = !empty($_SESSION['idSecretaria']);
$titulo_pagina = "Editar Empresa";
$seccion = 'fp_dual';
include_once $isSecretaria ? __DIR__ . "/../../secretaria/comunes/nav.php" : __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1><i class="fas fa-building"></i> EDITAR EMPRESA: <?= Security::escapeHtml($empresa['nombre']) ?></h1>
    <a href="verEmpresas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito): ?>
    <div class="alerta alerta-exito"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if (is_string($errores) && !empty($errores)): ?>
    <div class="alerta alerta-error"><i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml($errores) ?></div>
<?php endif; ?>

<div class="panel">
    <form action="../../../controladores/admin/fp_dual/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idEmpresa" value="<?= $idEmpresa ?>">
        <div class="formulario">
            <h3 style="margin: 0 0 16px 0; font-size: 1.05rem; color: var(--text); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-info-circle" style="color: var(--accent);"></i> Datos Generales
            </h3>
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombre') ?>">
                    <label for="nombre">Nombre de la Empresa <span style="color:var(--rojo);">*</span></label>
                    <input type="text" name="nombre" id="nombre" value="<?= Security::escapeHtml($empresa['nombre'] ?? '') ?>" required>
                    <?= fieldError($errores, 'nombre') ?>
                </div>
                <div class="campo">
                    <label for="cif">CIF / NIF</label>
                    <input type="text" name="cif" id="cif" value="<?= Security::escapeHtml($empresa['cif'] ?? '') ?>">
                </div>
            </div>
            <div class="form-fila">
                <div class="campo">
                    <label for="direccion">Dirección completa</label>
                    <input type="text" name="direccion" id="direccion" value="<?= Security::escapeHtml($empresa['direccion'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="contacto">Persona de Contacto</label>
                    <input type="text" name="contacto" id="contacto" value="<?= Security::escapeHtml($empresa['persona_contacto'] ?? '') ?>">
                </div>
            </div>
            <div class="form-fila">
                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" value="<?= Security::escapeHtml($empresa['telefono'] ?? '') ?>">
                </div>
                <div class="campo<?= fieldClass($errores, 'email') ?>">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?= Security::escapeHtml($empresa['email'] ?? '') ?>">
                    <?= fieldError($errores, 'email') ?>
                </div>
                <div class="campo">
                    <label for="activo">Estado</label>
                    <select name="activo" id="activo">
                        <option value="1" <?= ($empresa['activo'] == 1) ? 'selected' : '' ?>>Activa</option>
                        <option value="0" <?= ($empresa['activo'] == 0) ? 'selected' : '' ?>>Inactiva</option>
                    </select>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border); margin: 24px 0 16px 0;">

            <h3 style="margin: 0 0 8px 0; font-size: 1.05rem; color: var(--text); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-key" style="color: var(--accent);"></i> Acceso y Contraseña de la Empresa
            </h3>
            <p style="margin: 0 0 16px 0; font-size: 0.85rem; color: var(--dim);">
                Permite a los directores y secretaría establecer o restablecer la contraseña de acceso al portal para esta empresa. Dejar en blanco si no se desea modificar.
            </p>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nuevaPassword') ?>">
                    <label for="nuevaPassword">Nueva Contraseña</label>
                    <input type="password" name="nuevaPassword" id="nuevaPassword" minlength="8" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                    <?= fieldError($errores, 'nuevaPassword') ?>
                </div>
                <div class="campo<?= fieldClass($errores, 'repetirPassword') ?>">
                    <label for="repetirPassword">Repetir Nueva Contraseña</label>
                    <input type="password" name="repetirPassword" id="repetirPassword" minlength="8" placeholder="Repite la contraseña" autocomplete="new-password">
                    <?= fieldError($errores, 'repetirPassword') ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top: 24px;">
            <button type="submit" name="guardarEmpresa" class="boton-primario">
                <i class="fas fa-floppy-disk"></i> GUARDAR CAMBIOS
            </button>
        </div>
    </form>
</div>

<?php include $isSecretaria ? __DIR__ . "/../../secretaria/comunes/footer.php" : '../comunes/footer.php'; ?>
