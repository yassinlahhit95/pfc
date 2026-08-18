<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/secretarias.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);

require_once __DIR__ . "/../../../include/I18n.php";
$titulo_pagina = "" . __('My_Profile', 'Mi Perfil');
$Seccion = 'Perfil';
Include_Once __Dir__ . "/../Comunes/Nav.php";
?>

<div class="cabecera">
    <h1><?= __('my_profile', 'MI PERFIL') ?></h1>
    <a href="editar.php" class="boton-primario"><i class="fas fa-pen"></i> <?= __('edit_profile', 'EDITAR PERFIL') ?></a>
</div>

<?php if (!$secretaria): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-user-slash"></i></div>
        <div class="panel-vacio-titulo"><?= __('profile_not_found', 'Perfil no encontrado') ?></div>
    </div>
</div>
<?php else:
    $iniciales = strtoupper(implode('', array_map(fn($palabra) => $palabra[0], array_filter(explode(' ', trim($secretaria['nombreSecretaria']))))));
    $iniciales = mb_substr($iniciales, 0, 2, 'UTF-8');
?>
<div class="panel margen-abajo">
    <div style="display:flex;align-items:center;gap:24px;padding:8px 0 16px;">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:#fff;flex-shrink:0;">
            <?= Security::escapeHtml($iniciales) ?>
        </div>
        <div>
            <div style="font-size:1.3rem;font-weight:700;"><?= Security::escapeHtml($secretaria['nombreSecretaria']) ?></div>
            <div style="color:var(--mut);font-size:.9rem;margin-top:4px;"><?= Security::escapeHtml($secretaria['emailSecretaria']) ?></div>
            <div style="margin-top:8px;">
                <?php if ($secretaria['activoSecretaria']): ?>
                <span class="texto-estado verde"><?= __('active', 'Activo') ?></span>
                <?php else: ?>
                <span class="texto-estado rojo"><?= __('inactive', 'Inactivo') ?></span>
                <?php endif; ?>
                <span class="texto-estado gris" style="margin-left:8px;">Secretaría</span>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta"><h3><?= __('personal_data', 'Datos Personales') ?></h3></div>
    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label"><?= __('full_name', 'Nombre completo') ?></span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['nombreSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label"><?= __('email', 'Correo electrónico') ?></span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['emailSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label"><?= __('estado', 'Estado') ?></span>
            <span class="dato-valor">
                <?= $secretaria['activoSecretaria'] ? '<span class="texto-estado verde">Activo</span>' : '<span class="texto-estado rojo">Inactivo</span>' ?>
            </span>
        </div>
        <div class="dato">
            <span class="dato-label">Rol</span>
            <span class="dato-valor"><span class="texto-estado azul">Secretaría</span></span>
        </div>
    </div>
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
        <a href="editar.php" class="boton-primario" style="margin-right:10px;"><i class="fas fa-pen"></i> <?= __('edit', 'Editar') ?></a>
        <a href="../../../vistas/cambiar_password.php" class="boton-secundario"><i class="fas fa-lock"></i> <?= __('change_password', 'Cambiar contraseña') ?></a>
    </div>
</div>

<!-- Language Selector Panel -->
<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-globe"></i> <?= __('language', 'Idioma') ?></h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('select_language', 'Selecciona el idioma del sistema') ?></div>
        <div class="valor-detalle">
            <form action="../../../controladores/cambiar_idioma.php" method="POST" id="formLanguageProfile">
                <select name="lang" onchange="document.getElementById('formLanguageProfile').submit();" class="select-idioma" style="padding: 6px 12px; border-radius: 8px; border: 1.5px solid var(--border); font-size: .9rem; background: var(--bg-card); color: var(--text); cursor:pointer; font-weight:600;">
                    <option value="es" <?= I18n::getLang() === 'es' ? 'selected' : '' ?>><?= __('spanish', 'Español') ?></option>
                    <option value="eu" <?= I18n::getLang() === 'eu' ? 'selected' : '' ?>><?= __('basque', 'Euskera') ?></option>
                    <option value="ca" <?= I18n::getLang() === 'ca' ? 'selected' : '' ?>><?= __('catalan', 'Catalán') ?></option>
                    <option value="en" <?= I18n::getLang() === 'en' ? 'selected' : '' ?>><?= __('english', 'Inglés') ?></option>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta"><h3><i class="fas fa-shield-alt"></i> <?= __('security', 'Seguridad de la cuenta') ?></h3></div>
    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label"><?= __('two_factor', 'Verificación en dos pasos (2FA)') ?></span>
            <span class="dato-valor">
                <?php if (!empty($secretaria['mfa_enabled'])): ?>
                    <span style="color:var(--verde);font-weight:600;"><i class="fas fa-check-circle"></i> <?= __('active', 'Activada') ?></span>
                <?php else: ?>
                    <a href="../../auth/mfa_configurar.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;">
                        <i class="fas fa-lock"></i> <?= __('enable_2fa', 'Activar 2FA') ?>
                    </a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../comunes/rgpd/_mis_datos.php'; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
