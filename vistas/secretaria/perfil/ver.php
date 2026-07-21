<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/secretarias.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);

$titulo_pagina = "AULAPRO | MI PERFIL";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MI PERFIL</h1>
    <a href="editar.php" class="boton-primario"><i class="fas fa-pen"></i> EDITAR PERFIL</a>
</div>

<?php if (!$secretaria): ?>
<div class="panel">
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-user-slash"></i></div>
        <div class="panel-vacio-titulo">Perfil no encontrado</div>
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
                <span class="texto-estado verde">Activo</span>
                <?php else: ?>
                <span class="texto-estado rojo">Inactivo</span>
                <?php endif; ?>
                <span class="texto-estado gris" style="margin-left:8px;">Secretaría</span>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta"><h3>Información de la cuenta</h3></div>
    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label">Nombre completo</span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['nombreSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label">Correo electrónico</span>
            <span class="dato-valor"><?= Security::escapeHtml($secretaria['emailSecretaria']) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label">Estado</span>
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
        <a href="editar.php" class="boton-primario" style="margin-right:10px;"><i class="fas fa-pen"></i> Editar perfil</a>
        <a href="../../../vistas/cambiar_password.php" class="boton-secundario"><i class="fas fa-lock"></i> Cambiar contraseña</a>
    </div>
</div>

<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta"><h3><i class="fas fa-shield-alt"></i> Seguridad de la cuenta</h3></div>
    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label">Verificación en dos pasos (2FA)</span>
            <span class="dato-valor">
                <?php if (!empty($secretaria['mfa_enabled'])): ?>
                    <span style="color:var(--verde);font-weight:600;"><i class="fas fa-check-circle"></i> Activada</span>
                <?php else: ?>
                    <a href="../../auth/mfa_configurar.php" class="boton-secundario" style="font-size:.85rem;padding:6px 14px;">
                        <i class="fas fa-lock"></i> Activar 2FA
                    </a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../comunes/rgpd/_mis_datos.php'; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
