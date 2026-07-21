<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/profesores.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);

$tituloDelPagina = "AULAPRO | MI PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI PERFIL</h1>
        <p class="subtitulo-encabezado">Informacion de tu cuenta de profesor</p>
    </div>
    <div class="acciones-pagina">
        <a href="editar.php" class="boton-primario">
            <i class="fas fa-edit"></i> EDITAR INFORMACION
        </a>
    </div>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-circle"></i> DATOS PERSONALES</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml(mb_strtoupper($profesor['nombreProfesor'], 'UTF-8')) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email Corporativo</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['emailProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Telefono</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['telefonoProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI / Identificacion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['dniProfesor'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Direccion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($profesor['direccionProfesor'] ) ?></div>
    </div>

    <?php if(!empty($profesor['observacionesProfesor'])) { ?>
        <div class="fila-datos">
            <div class="nombre-detalle">Observaciones</div>
            <div class="valor-detalle"><?= Security::escapeHtml($profesor['observacionesProfesor'] ) ?></div>
        </div>
    <?php } ?>
</div>

<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-shield-alt"></i> SEGURIDAD DE LA CUENTA</h3>
    </div>
    <div class="fila-datos">
        <div class="nombre-detalle">Verificación en dos pasos (2FA)</div>
        <div class="valor-detalle">
            <?php if (!empty($profesor['mfa_enabled'])): ?>
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

<?php include '../comunes/footer.php'; ?>
