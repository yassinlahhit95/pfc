<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = (int)($_GET['idProfesor'] ?? 0);
$profesor   = obtenerProfesorPorId($idProfesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$modulosProfesor   = listarModulosDeProfesor($idProfesor);
$ciclosTutorizados = listarCiclosTutorizadosProfesor($idProfesor);

/* ── Avatar helpers ── */
$nombreCompleto = $profesor['nombreProfesor'];
$partesNombre   = explode(' ', trim($nombreCompleto));
$iniciales      = mb_strtoupper(mb_substr($partesNombre[0], 0, 1));
if (count($partesNombre) > 1) $iniciales .= mb_strtoupper(mb_substr($partesNombre[1], 0, 1));
$paletaAvatar = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6','#06b6d4','#ef4444'];
$colorAvatar  = $paletaAvatar[ord($iniciales[0]) % count($paletaAvatar)];

$titulo_pagina = "AULAPRO | DETALLES PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Ficha del Profesor</h1>
        <p class="subtitulo-encabezado">Datos completos del docente</p>
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
                <i class="fas fa-chalkboard-teacher"></i> Profesor
                <?php if (!empty($ciclosTutorizados)): ?>
                    <span class="perfil-sep"></span>
                    <span class="texto-estado morado">
                        <i class="fas fa-user-tie"></i> Tutor
                    </span>
                <?php endif; ?>
                <span class="perfil-sep"></span>
                <i class="fas fa-envelope"></i>
                <?= Security::escapeHtml($profesor['emailProfesor']) ?>
            </div>
        </div>
        <div class="perfil-acciones">
            <a href="modificarProfesores.php?idProfesor=<?= $idProfesor ?>" class="boton-primario">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="verProfesores.php" class="boton-secundario">
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
                    <?= !empty($profesor['dniProfesor'])
                        ? Security::escapeHtml($profesor['dniProfesor'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Teléfono</span>
                <span class="detalle-valor">
                    <?= !empty($profesor['telefonoProfesor'])
                        ? Security::escapeHtml($profesor['telefonoProfesor'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Nacimiento</span>
                <span class="detalle-valor">
                    <?= !empty($profesor['fechaNacimientoProfesor']) && $profesor['fechaNacimientoProfesor'] !== '0000-00-00'
                        ? date('d/m/Y', strtotime($profesor['fechaNacimientoProfesor']))
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Alta</span>
                <span class="detalle-valor">
                    <?= !empty($profesor['fechaAltaProfesor'])
                        ? date('d/m/Y', strtotime($profesor['fechaAltaProfesor']))
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
                    <?= !empty($profesor['direccionProfesor'])
                        ? Security::escapeHtml($profesor['direccionProfesor'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Ciudad</span>
                <span class="detalle-valor">
                    <?= !empty($profesor['ciudadProfesor'])
                        ? Security::escapeHtml($profesor['ciudadProfesor'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Código Postal</span>
                <span class="detalle-valor">
                    <?= !empty($profesor['codigoPostalProfesor'])
                        ? Security::escapeHtml($profesor['codigoPostalProfesor'])
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
                <?= !empty($profesor['observacionesProfesor'])
                    ? nl2br(Security::escapeHtml($profesor['observacionesProfesor']))
                    : '<span class="texto-suave">Sin observaciones registradas.</span>' ?>
            </div>
        </div>

    </div><!-- /detalle-grid -->
</div>

<!-- Ciclos tutorizados -->
<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-tie" style="color:var(--accent);margin-right:6px;"></i> Ciclos Tutorizados</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre del Ciclo</th>
                    <th>Nivel</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ciclosTutorizados)): ?>
                    <tr><td colspan="2" class="vacio">No es tutor de ningún ciclo</td></tr>
                <?php else: ?>
                    <?php foreach ($ciclosTutorizados as $cicloItem): ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($cicloItem['nombreCiclo']) ?></b></td>
                        <td><span class="texto-estado azul"><?= Security::escapeHtml($cicloItem['nombreNivel']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Módulos impartidos -->
<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-book-open" style="color:var(--accent);margin-right:6px;"></i> Módulos Impartidos</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Ciclo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($modulosProfesor)): ?>
                    <tr><td colspan="2" class="vacio">No tiene módulos asignados</td></tr>
                <?php else: ?>
                    <?php foreach ($modulosProfesor as $moduloItem): ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($moduloItem['nombreModulo']) ?></b></td>
                        <td><span class="texto-estado azul"><?= Security::escapeHtml($moduloItem['abreviaturaCiclo']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Cambiar contraseña (director) -->
<div class="panel" style="margin-top:16px;">
    <div class="panel-titulo-seccion" style="cursor:pointer;" onclick="document.getElementById('form-cambiar-pass-prof').classList.toggle('oculto')">
        <i class="fas fa-key"></i> Cambiar contraseña
        <small style="margin-left:8px;color:var(--dim);font-weight:400;">Establecer nueva contraseña para este profesor</small>
    </div>
    <div id="form-cambiar-pass-prof" class="oculto" style="margin-top:16px;">
        <div class="formulario" style="max-width:480px;">
            <div class="campo ancho-total">
                <label for="nueva-pass-prof">Nueva contraseña <small style="color:var(--dim)">(mín. 8 caracteres)</small></label>
                <input type="password" id="nueva-pass-prof" minlength="8" autocomplete="new-password" placeholder="Nueva contraseña">
            </div>
            <div class="campo ancho-total">
                <button type="button" class="boton-primario" onclick="cambiarPassProf()">
                    <i class="fas fa-save"></i> Guardar contraseña
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
function cambiarPassProf() {
    var pass = document.getElementById('nueva-pass-prof').value;
    if (pass.length < 8) { if (window.Toast) Toast.show('Mínimo 8 caracteres.', 'error'); return; }
    fetch('../../../controladores/admin/usuarios/cambiarPassword.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= Security::generateCSRFToken() ?>&tipo=profesor&id=<?= $idProfesor ?>&nuevaPassword='+encodeURIComponent(pass)
    }).then(r => r.json()).then(data => {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) document.getElementById('nueva-pass-prof').value = '';
    });
}
</script>
