<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/directores.php";

$idDirector = (int)($_GET['id'] ?? 0);
$director   = obtenerDirectorPorId($idDirector);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

$nombreCompleto = $director['nombreDirector'];
$partesNombre   = explode(' ', trim($nombreCompleto));
$iniciales      = mb_strtoupper(mb_substr($partesNombre[0], 0, 1));
if (count($partesNombre) > 1) $iniciales .= mb_strtoupper(mb_substr($partesNombre[1], 0, 1));
$paletaAvatar = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6','#06b6d4','#ef4444'];
$colorAvatar  = $paletaAvatar[ord($iniciales[0]) % count($paletaAvatar)];

$titulo_pagina = "Detalles Director";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Ficha del Director</h1>
        <p class="subtitulo-encabezado">Datos completos del director del centro</p>
    </div>
</div>

<div class="panel">
    <div class="perfil-cabecera">
        <div class="perfil-avatar" style="--av-color:<?= $colorAvatar ?>">
            <?= Security::escapeHtml($iniciales) ?>
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre"><?= Security::escapeHtml(mb_strtoupper($nombreCompleto, 'UTF-8')) ?></div>
            <div class="perfil-meta">
                <i class="fas fa-user-shield"></i> Director
                <span class="perfil-sep"></span>
                <i class="fas fa-envelope"></i>
                <?= Security::escapeHtml($director['emailDirector']) ?>
            </div>
        </div>
        <div class="perfil-acciones">
            <a href="modificarDirectores.php?id=<?= $idDirector ?>" class="boton-primario">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form method="POST" action="/controladores/admin/tours/reiniciar.php" style="display:inline;"
                  data-ajax-confirm="¿Reiniciar el tour de bienvenida de este director/a? Volverá a verlo en su próximo inicio de sesión.">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idUsuario" value="<?= $idDirector ?>">
                <input type="hidden" name="tipoUsuario" value="admin">
                <button type="submit" class="boton-secundario">
                    <i class="fas fa-route"></i> Reiniciar tour
                </button>
            </form>
            <a href="verDirectores.php" class="boton-secundario">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="detalle-grid">

        <!-- Datos personales -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-id-card"></i> Datos Personales
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">DNI</span>
                <span class="detalle-valor">
                    <?= !empty($director['dniDirector'])
                        ? Security::escapeHtml($director['dniDirector'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Teléfono</span>
                <span class="detalle-valor">
                    <?= !empty($director['telefonoDirector'])
                        ? Security::escapeHtml($director['telefonoDirector'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Nacimiento</span>
                <span class="detalle-valor">
                    <?= !empty($director['fechaNacimientoDirector']) && $director['fechaNacimientoDirector'] !== '0000-00-00'
                        ? date('d/m/Y', strtotime($director['fechaNacimientoDirector']))
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Alta</span>
                <span class="detalle-valor">
                    <?= !empty($director['fechaAltaDirector'])
                        ? date('d/m/Y', strtotime($director['fechaAltaDirector']))
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>
        </div>

        <!-- Dirección -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-map-marker-alt"></i> Dirección y Contacto
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Dirección</span>
                <span class="detalle-valor">
                    <?= !empty($director['direccionDirector'])
                        ? Security::escapeHtml($director['direccionDirector'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Ciudad</span>
                <span class="detalle-valor">
                    <?= !empty($director['ciudadDirector'])
                        ? Security::escapeHtml($director['ciudadDirector'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Código Postal</span>
                <span class="detalle-valor">
                    <?= !empty($director['codigoPostalDirector'])
                        ? Security::escapeHtml($director['codigoPostalDirector'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-sticky-note"></i> Observaciones
            </div>
            <div class="detalle-valor" style="padding-top:4px;">
                <?= !empty($director['observacionesDirector'])
                    ? nl2br(Security::escapeHtml($director['observacionesDirector']))
                    : '<span class="texto-suave">Sin observaciones registradas.</span>' ?>
            </div>
        </div>
        <!-- Idioma de la plataforma -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-globe"></i> Preferencia de Idioma / Language
            </div>
            <div class="detalle-fila" style="align-items:center;">
                <span class="detalle-label">Idioma del Sistema</span>
                <span class="detalle-valor">
                    <form action="../../../controladores/cambiar_idioma.php" method="POST" id="formLanguageProfileAdmin" style="margin:0;">
                        <select name="lang" onchange="document.getElementById('formLanguageProfileAdmin').submit();" class="select-idioma" style="padding: 6px 12px; border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: .9rem; background: var(--bg-card); color: var(--text); cursor:pointer; font-weight:600;">
                            <option value="es" <?= I18n::getLang() === 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="eu" <?= I18n::getLang() === 'eu' ? 'selected' : '' ?>>Euskera</option>
                            <option value="ca" <?= I18n::getLang() === 'ca' ? 'selected' : '' ?>>Catalán</option>
                            <option value="en" <?= I18n::getLang() === 'en' ? 'selected' : '' ?>>Inglés</option>
                        </select>
                    </form>
                </span>
            </div>
        </div>

    </div><!-- /detalle-grid -->
</div>

<?php include '../comunes/footer.php'; ?>
