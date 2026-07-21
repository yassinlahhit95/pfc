<?php
require_once __DIR__ . "/../../../include/TutorGuard.php";
require_once __DIR__ . "/../../../modelos/tutores.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$tutor = obtenerTutorPorId($_SESSION['idTutor']);

$titulo_pagina = "AulaPro Familias — Mi Perfil";
$seccion       = 'perfil';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>MI PERFIL</h1>
</div>

<?php if (!$tutor): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-user-slash"></i></div>
        <div class="panel-vacio-titulo">Perfil no encontrado</div>
    </div>
</div>
<?php else: ?>
<div class="panel margen-abajo">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml($tutor['nombreTutor']) ?></div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= Security::escapeHtml($tutor['emailTutor']) ?></div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Teléfono</div>
        <div class="valor-detalle"><?= Security::escapeHtml($tutor['telefonoTutor'] ?? '') ?></div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">DNI</div>
        <div class="valor-detalle"><?= Security::escapeHtml($tutor['dniTutor'] ?? '') ?></div>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-shield-alt"></i> SEGURIDAD DE LA CUENTA</h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Verificación en dos pasos (2FA)</div>
        <div class="valor-detalle">
            <?php if (!empty($tutor['mfa_enabled'])): ?>
                <span style="color:var(--verde);font-weight:600;"><i class="fas fa-check-circle"></i> Activada</span>
            <?php else: ?>
                <a href="../../auth/mfa_configurar.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;">
                    <i class="fas fa-lock"></i> Activar 2FA
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Contraseña</div>
        <div class="valor-detalle"><a href="../../cambiar_password.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;"><i class="fas fa-key"></i> Cambiar contraseña</a></div>
    </div>
</div>

<?php require __DIR__ . '/../../comunes/rgpd/_mis_datos.php'; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
