<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idDelEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idDelEstudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

/* ── Avatar helpers ── */
$nombreCompleto = $estudiante['nombreEstudiante'];
$partesNombre   = explode(' ', trim($nombreCompleto));
$iniciales      = mb_strtoupper(mb_substr($partesNombre[0], 0, 1));
if (count($partesNombre) > 1) $iniciales .= mb_strtoupper(mb_substr($partesNombre[1], 0, 1));
$paletaAvatar = ['#4F46E5','#0ea5e9','#10b981','#f59e0b','#ec4899','#8b5cf6','#06b6d4','#ef4444'];
$colorAvatar  = $paletaAvatar[ord($iniciales[0]) % count($paletaAvatar)];

/* ── Chip: nivel ── */
$nivelLabel = $estudiante['curso'] === 'Grado Superior' ? 'Grado Superior' : 'Grado Medio';
$nivelClase = $estudiante['curso'] === 'Grado Superior' ? 'verde' : 'azul';

$titulo_pagina = "AULAPRO | DETALLE DEL ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Ficha de Estudiante</h1>
        <p class="subtitulo-encabezado">Datos completos del alumno registrado</p>
    </div>
</div>

<div class="panel">
    <div class="perfil-cabecera">
        <div class="perfil-avatar" style="--av-color:<?= $colorAvatar ?>">
            <?= Security::escapeHtml($iniciales) ?>
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre"><?= Security::escapeHtml(mb_strtoupper($nombreCompleto, 'UTF-8')) ?></div>
            <div class="perfil-meta" style="flex-wrap:wrap;gap:6px 12px;">
                <span class="texto-estado <?= $nivelClase ?>"><?= Security::escapeHtml($nivelLabel) ?></span>
                <span class="perfil-sep"></span>
                <span style="white-space:normal;word-break:break-word;">
                    <i class="fas fa-graduation-cap"></i>
                    <?= Security::escapeHtml($estudiante['nombreCiclo']) ?>
                </span>
                <span class="perfil-sep"></span>
                <span style="word-break:break-all;">
                    <i class="fas fa-envelope"></i>
                    <?= Security::escapeHtml($estudiante['emailEstudiante']) ?>
                </span>
            </div>
        </div>
        <div class="perfil-acciones">
            <a href="modificarEstudiantes.php?idEstudiante=<?= $idDelEstudiante ?>" class="boton-primario">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="/controladores/admin/rgpd/exportar.php?idEstudiante=<?= $idDelEstudiante ?>" class="boton-secundario" title="RGPD Art. 20 – Exportar datos personales">
                <i class="fas fa-file-export"></i> Exportar datos
            </a>
            <form method="POST" action="/controladores/admin/tours/reiniciar.php" style="display:inline;"
                  data-ajax-confirm="¿Reiniciar el tour de bienvenida de este estudiante? Volverá a verlo en su próximo inicio de sesión.">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idUsuario" value="<?= $idDelEstudiante ?>">
                <input type="hidden" name="tipoUsuario" value="estudiante">
                <button type="submit" class="boton-secundario">
                    <i class="fas fa-route"></i> Reiniciar tour
                </button>
            </form>
            <a href="../pagos/historialEstudiante.php?idEstudiante=<?= $idDelEstudiante ?>" class="boton-secundario">
                <i class="fas fa-file-invoice-dollar"></i> Historial de Pagos
            </a>
            <a href="verEstudiantes.php" class="boton-secundario">
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
                    <?= !empty($estudiante['dniEstudiante'])
                        ? Security::escapeHtml($estudiante['dniEstudiante'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Teléfono</span>
                <span class="detalle-valor">
                    <?= !empty($estudiante['telefonoEstudiante'])
                        ? Security::escapeHtml($estudiante['telefonoEstudiante'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Nacimiento</span>
                <span class="detalle-valor">
                    <?= !empty($estudiante['fechaNacimientoEstudiante']) && $estudiante['fechaNacimientoEstudiante'] !== '0000-00-00'
                        ? date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante']))
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Fecha de Alta</span>
                <span class="detalle-valor">
                    <?= !empty($estudiante['fechaAltaEstudiante'])
                        ? date('d/m/Y', strtotime($estudiante['fechaAltaEstudiante']))
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
                    <?= !empty($estudiante['direccionEstudiante'])
                        ? Security::escapeHtml($estudiante['direccionEstudiante'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Ciudad</span>
                <span class="detalle-valor">
                    <?= !empty($estudiante['ciudadEstudiante'])
                        ? Security::escapeHtml($estudiante['ciudadEstudiante'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Código Postal</span>
                <span class="detalle-valor">
                    <?= !empty($estudiante['codigoPostalEstudiante'])
                        ? Security::escapeHtml($estudiante['codigoPostalEstudiante'])
                        : '<span class="texto-suave">No especificado</span>' ?>
                </span>
            </div>
        </div>

        <!-- Académico + TFG -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-university"></i> Información Académica
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Ciclo Formativo</span>
                <span class="detalle-valor" style="word-break:break-word;">
                    <?= Security::escapeHtml($estudiante['nombreCiclo']) ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Nivel / Curso</span>
                <span class="detalle-valor"><?= Security::escapeHtml($estudiante['curso']) ?></span>
            </div>

            <?php if (!empty($estudiante['anioEstudio'])): ?>
            <div class="detalle-fila">
                <span class="detalle-label">Año de Estudio</span>
                <span class="detalle-valor">
                    <span class="texto-estado azul"><?= Security::escapeHtml($estudiante['anioEstudio']) ?> año</span>
                </span>
            </div>
            <?php endif; ?>

            <div class="detalle-fila">
                <span class="detalle-label">TFG</span>
                <span class="detalle-valor">
                    <?php if (!empty($estudiante['archivoTFG'])): ?>
                        <span class="indicador-estado activo-verde"><i class="fas fa-check"></i> Entregado</span>
                        <span class="texto-suave" style="display:block;margin-top:4px;font-size:12px;">
                            Subido el <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?>
                        </span>
                        <a href="/controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($estudiante['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="boton-secundario" style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;font-size:.82rem;padding:5px 12px;">
                            <i class="fas fa-eye"></i> Ver PDF
                        </a>
                    <?php else: ?>
                        <span class="indicador-estado inactivo-rojo"><i class="fas fa-times"></i> No subido</span>
                    <?php endif; ?>
                    <form action="../../../controladores/admin/estudiantes/subirTFG.php" method="POST" enctype="multipart/form-data" style="margin-top:10px;">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="idEstudiante" value="<?= $idDelEstudiante ?>">
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <input type="file" name="archivoTFG" accept=".pdf,application/pdf" required style="font-size:.82rem;">
                            <button type="submit" name="subirTFG" class="boton-primario" style="font-size:.82rem;padding:6px 14px;">
                                <i class="fas fa-upload"></i> <?= !empty($estudiante['archivoTFG']) ? 'Reemplazar TFG' : 'Subir TFG' ?>
                            </button>
                        </div>
                        <span class="texto-suave" style="font-size:.75rem;display:block;margin-top:4px;">Solo PDF · máx. 10 MB</span>
                    </form>
                </span>
            </div>
        </div>

        <!-- Observaciones -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-sticky-note"></i> Observaciones
            </div>
            <div class="detalle-valor" style="padding-top:4px;">
                <?= !empty($estudiante['observacionesEstudiante'])
                    ? nl2br(Security::escapeHtml($estudiante['observacionesEstudiante']))
                    : '<span class="texto-suave">Sin observaciones registradas.</span>' ?>
            </div>
        </div>

    </div><!-- /detalle-grid -->
</div>

<!-- Cambiar contraseña (director) -->
<div class="panel margen-abajo" id="panel-cambiar-pass" style="margin-top:16px;">
    <div class="panel-titulo-seccion" style="cursor:pointer;" onclick="document.getElementById('form-cambiar-pass').classList.toggle('oculto')">
        <i class="fas fa-key"></i> Cambiar contraseña
        <small style="margin-left:8px;color:var(--dim);font-weight:400;">Establecer nueva contraseña para este estudiante</small>
    </div>
    <div id="form-cambiar-pass" class="oculto" style="margin-top:16px;">
        <div class="formulario" style="max-width:480px;">
            <div class="campo ancho-total">
                <label for="nueva-pass-est">Nueva contraseña <small style="color:var(--dim)">(mín. 8 caracteres)</small></label>
                <input type="password" id="nueva-pass-est" minlength="8" autocomplete="new-password" placeholder="Nueva contraseña">
            </div>
            <div class="campo ancho-total">
                <button type="button" class="boton-primario" onclick="cambiarPassUsuario('estudiante', <?= $idDelEstudiante ?>)">
                    <i class="fas fa-save"></i> Guardar contraseña
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
function cambiarPassUsuario(tipo, id) {
    var pass = document.getElementById('nueva-pass-est').value;
    if (pass.length < 8) { if (window.Toast) Toast.show('La contraseña debe tener al menos 8 caracteres.', 'error'); return; }
    fetch('../../../controladores/admin/usuarios/cambiarPassword.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= Security::generateCSRFToken() ?>&tipo='+tipo+'&id='+id+'&nuevaPassword='+encodeURIComponent(pass)
    }).then(r => r.json()).then(data => {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) document.getElementById('nueva-pass-est').value = '';
    }).catch(function() {
        if (window.Toast) Toast.show('Error de conexión.', 'error');
    });
}
</script>

