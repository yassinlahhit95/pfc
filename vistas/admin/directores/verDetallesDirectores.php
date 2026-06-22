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

/* ── Avatar helpers ── */
$_av_nombre    = $director['nombreDirector'];
$_av_partes    = explode(' ', trim($_av_nombre));
$_av_iniciales = mb_strtoupper(mb_substr($_av_partes[0], 0, 1));
if (count($_av_partes) > 1) $_av_iniciales .= mb_strtoupper(mb_substr($_av_partes[1], 0, 1));
$_av_paleta = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6','#06b6d4','#ef4444'];
$_av_color  = $_av_paleta[ord($_av_iniciales[0]) % count($_av_paleta)];

$titulo_pagina = "AULAPRO | DETALLES DIRECTOR";
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
        <div class="perfil-avatar" style="--av-color:<?= $_av_color ?>">
            <?= Security::escapeHtml($_av_iniciales) ?>
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre"><?= Security::escapeHtml(mb_strtoupper($_av_nombre, 'UTF-8')) ?></div>
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

    </div><!-- /detalle-grid -->
</div>

<?php include '../comunes/footer.php'; ?>
