<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);

require_once __DIR__ . "/../../../include/I18n.php";
$tituloDelPagina = "AULAPRO | " . __('my_profile', 'MI PERFIL');
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1><?= __('my_profile', 'MI PERFIL') ?></h1>
        <p class="subtitulo-encabezado"><?= __('profile_info_teacher', 'Información de tu cuenta de profesor/a') ?></p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> <?= __('edit', 'EDITAR INFORMACIÓN') ?>
        </a>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> <?= __('personal_data', 'DATOS PERSONALES') ?></h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('full_name', 'Nombre Completo') ?></div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml(mb_strtoupper($profesor['nombreProfesor'], 'UTF-8')) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('email', 'Email') ?></div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['emailProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('phone', 'Teléfono') ?></div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['telefonoProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('dni', 'DNI / Identificación') ?></div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['dniProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('address', 'Dirección') ?></div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['direccionProfesor'] ) ?></div>
    </div>

    <?php if(!empty($profesor['observacionesProfesor'])) { ?>
        <div class="fila-datos">
            <div class="nombre-detalle"><?= __('observaciones', 'Observaciones') ?></div>
            <div class="valor-detalle"><?= Security::escapeHtml($profesor['observacionesProfesor'] ) ?></div>
        </div>
    <?php } ?>
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
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-shield-alt"></i> <?= __('security', 'SEGURIDAD DE LA CUENTA') ?></h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('two_factor', 'Verificación en dos pasos (2FA)') ?></div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['mfa_enabled'])): ?>
                <span style="color:var(--verde);font-weight:600;"><i class="fas fa-check-circle"></i> <?= __('active', 'Activada') ?></span>
            <?php else: ?>
                <a href="../../auth/mfa_configurar.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;">
                    <i class="fas fa-lock"></i> <?= __('enable_2fa', 'Activar 2FA') ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle"><?= __('password', 'Contraseña') ?></div>
        <div class="valor-detalle"><a href="../../cambiar_password.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;"><i class="fas fa-key"></i> <?= __('change_password', 'Cambiar contraseña') ?></a></div>
    </div>
</div>

<?php require __DIR__ . '/../../comunes/rgpd/_mis_datos.php'; ?>

<?php include '../comunes/footer.php'; ?>
