<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idEstudiante = (int)($_GET['id'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$_av_nombre   = $estudiante['nombreEstudiante'];
$_av_partes   = explode(' ', trim($_av_nombre));
$_av_iniciales = mb_strtoupper(mb_substr($_av_partes[0], 0, 1));
if (count($_av_partes) > 1) $_av_iniciales .= mb_strtoupper(mb_substr($_av_partes[1], 0, 1));
$_av_paleta   = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6'];
$_av_color    = $_av_paleta[ord($_av_iniciales[0]) % count($_av_paleta)];

$titulo_pagina = 'AULAPRO | DETALLE ESTUDIANTE';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1>Ficha de Estudiante</h1>
        <p class="subtitulo-encabezado">Datos completos del alumno</p>
    </div>
    <div class="acciones-pagina">
        <a href="modificarEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-primario">
            <i class="fas fa-pen"></i> Editar
        </a>
        <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
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
                <i class="fas fa-graduation-cap"></i>
                <?= Security::escapeHtml($estudiante['nombreCiclo'] ?? '—') ?>
            </div>
        </div>
    </div>

    <div class="formulario" style="margin-top:24px;">
        <div class="campo">
            <label>Email</label>
            <div class="input-lectura"><?= Security::escapeHtml($estudiante['emailEstudiante']) ?></div>
        </div>
        <div class="campo">
            <label>DNI</label>
            <div class="input-lectura"><?= Security::escapeHtml($estudiante['dniEstudiante'] ?? '—') ?></div>
        </div>
        <div class="campo">
            <label>Teléfono</label>
            <div class="input-lectura"><?= Security::escapeHtml($estudiante['telefonoEstudiante'] ?? '—') ?></div>
        </div>
        <div class="campo">
            <label>Fecha de nacimiento</label>
            <div class="input-lectura">
                <?= $estudiante['fechaNacimientoEstudiante'] ? date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) : '—' ?>
            </div>
        </div>
        <div class="campo ancho-total">
            <label>Dirección</label>
            <div class="input-lectura"><?= Security::escapeHtml($estudiante['direccionEstudiante'] ?? '—') ?></div>
        </div>
    </div>
</div>

<!-- Cambiar contraseña (secretaria) -->
<div class="panel" style="margin-top:16px;">
    <div class="panel-titulo-seccion" style="cursor:pointer;" onclick="document.getElementById('form-cambiar-pass-sec').classList.toggle('oculto')">
        <i class="fas fa-key"></i> Cambiar contraseña del estudiante
    </div>
    <div id="form-cambiar-pass-sec" class="oculto" style="margin-top:16px;">
        <div class="formulario" style="max-width:480px;">
            <div class="campo ancho-total">
                <label for="nueva-pass-sec">Nueva contraseña <small style="color:var(--dim)">(mín. 8 caracteres)</small></label>
                <input type="password" id="nueva-pass-sec" minlength="8" autocomplete="new-password" placeholder="Nueva contraseña">
            </div>
            <div class="campo ancho-total">
                <button type="button" class="boton-primario" onclick="cambiarPassSec()">
                    <i class="fas fa-save"></i> Guardar contraseña
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
function cambiarPassSec() {
    var pass = document.getElementById('nueva-pass-sec').value;
    if (pass.length < 8) { if (window.Toast) Toast.show('Mínimo 8 caracteres.', 'error'); return; }
    fetch('/controladores/secretaria/estudiantes/cambiarPassword.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= Security::generateCSRFToken() ?>&id=<?= $idEstudiante ?>&nuevaPassword='+encodeURIComponent(pass)
    }).then(r => r.json()).then(d => {
        if (window.Toast) Toast.show(d.msg, d.ok ? 'success' : 'error');
        if (d.ok) document.getElementById('nueva-pass-sec').value = '';
    });
}
</script>
