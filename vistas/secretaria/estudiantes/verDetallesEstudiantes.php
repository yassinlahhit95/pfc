<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idEstudiante = (int)($_GET['id'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

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
$nivelLabel = ($estudiante['curso'] ?? '') === 'Grado Superior' ? 'Grado Superior' : 'Grado Medio';
$nivelClase = ($estudiante['curso'] ?? '') === 'Grado Superior' ? 'verde' : 'azul';

$titulo_pagina = 'Detalle Estudiante';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1>Ficha de Estudiante</h1>
        <p class="subtitulo-encabezado">Datos completos del alumno</p>
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
                    <?= Security::escapeHtml($estudiante['nombreCiclo'] ?? '—') ?>
                </span>
                <span class="perfil-sep"></span>
                <span style="word-break:break-all;">
                    <i class="fas fa-envelope"></i>
                    <?= Security::escapeHtml($estudiante['emailEstudiante']) ?>
                </span>
            </div>
        </div>
        <div class="perfil-acciones">
            <a href="modificarEstudiantes.php?id=<?= $idEstudiante ?>" class="boton-primario">
                <i class="fas fa-edit"></i> Editar
            </a>
            <button type="button" class="boton-peligro" onclick="confirmarEliminar(<?= $idEstudiante ?>, '<?= Security::escapeHtml(addslashes($nombreCompleto)) ?>')">
                <i class="fas fa-trash-alt"></i> Eliminar
            </button>
            <a href="../pagos/historialEstudiante.php?idEstudiante=<?= $idEstudiante ?>" class="boton-secundario">
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

        <!-- Dirección y Contacto -->
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

        <!-- Información Académica -->
        <div class="detalle-seccion">
            <div class="detalle-seccion-titulo">
                <i class="fas fa-university"></i> Información Académica
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Ciclo Formativo</span>
                <span class="detalle-valor" style="word-break:break-word;">
                    <?= Security::escapeHtml($estudiante['nombreCiclo'] ?? '—') ?>
                </span>
            </div>

            <div class="detalle-fila">
                <span class="detalle-label">Nivel / Curso</span>
                <span class="detalle-valor"><?= Security::escapeHtml($estudiante['curso'] ?? '—') ?></span>
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
                            Subido el <?= date('d/m/Y', strtotime($estudiante['fechaSubidaTFG'])) ?>
                        </span>
                    <?php else: ?>
                        <span class="indicador-estado inactivo-rojo"><i class="fas fa-times"></i> No subido</span>
                    <?php endif; ?>
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

<!-- Cambiar contraseña -->
<div class="panel" style="margin-top:16px;">
    <div class="panel-titulo-seccion" style="cursor:pointer;" onclick="document.getElementById('form-cambiar-pass-sec').classList.toggle('oculto')">
        <i class="fas fa-key"></i> Cambiar contraseña del estudiante
        <small style="margin-left:8px;color:var(--dim);font-weight:400;">Establecer nueva contraseña de acceso</small>
    </div>
    <div id="form-cambiar-pass-sec" class="oculto" style="margin-top:16px;">
        <div class="formulario" style="max-width:480px;">
            <div class="campo ancho-total">
                <label for="nueva-pass-sec">Nueva contraseña <small style="color:var(--dim)">(mín. 8 caracteres)</small></label>
                <input type="password" id="nueva-pass-sec" minlength="8" autocomplete="new-password" placeholder="Nueva contraseña">
            </div>
            <div class="campo ancho-total">
                <label for="nueva-pass-sec-confirm">Confirmar contraseña</label>
                <input type="password" id="nueva-pass-sec-confirm" minlength="8" autocomplete="new-password" placeholder="Repetir contraseña">
            </div>
            <div class="campo ancho-total">
                <button type="button" class="boton-primario" onclick="cambiarPassSec()">
                    <i class="fas fa-save"></i> Guardar contraseña
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmación eliminar -->
<div id="modal-eliminar" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;">
    <div style="background:var(--surface,#fff);border-radius:14px;padding:32px;max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="font-size:2rem;color:var(--rojo);margin-bottom:12px;text-align:center;"><i class="fas fa-trash-alt"></i></div>
        <h3 style="margin:0 0 8px;text-align:center;">¿Mover a la papelera?</h3>
        <p style="margin:0 0 24px;text-align:center;color:var(--dim);">El estudiante <strong id="modal-nombre"></strong> se moverá a la papelera. Podrá ser restaurado después.</p>
        <form id="form-eliminar" method="POST" action="../../../controladores/secretaria/estudiantes/eliminar.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" id="modal-id" value="">
            <div style="display:flex;gap:10px;justify-content:center;">
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-eliminar').style.display='none'">Cancelar</button>
                <button type="submit" class="boton-peligro"><i class="fas fa-trash-alt"></i> Mover a papelera</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
function confirmarEliminar(id, nombre) {
    document.getElementById('modal-id').value = id;
    document.getElementById('modal-nombre').textContent = nombre;
    document.getElementById('modal-eliminar').style.display = 'flex';
}

function cambiarPassSec() {
    var pass = document.getElementById('nueva-pass-sec').value;
    var confirmPass = document.getElementById('nueva-pass-sec-confirm').value;
    if (pass.length < 8) { if (window.Toast) Toast.show('La contraseña debe tener al menos 8 caracteres.', 'error'); return; }
    if (pass !== confirmPass) { if (window.Toast) Toast.show('Las contraseñas no coinciden.', 'error'); return; }
    fetch('../../../controladores/secretaria/estudiantes/cambiarPassword.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= Security::generateCSRFToken() ?>&id=<?= $idEstudiante ?>&nuevaPassword='+encodeURIComponent(pass)
    }).then(r => r.json()).then(data => {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) {
            document.getElementById('nueva-pass-sec').value = '';
            document.getElementById('nueva-pass-sec-confirm').value = '';
        }
    }).catch(function() {
        if (window.Toast) Toast.show('Error de conexión.', 'error');
    });
}
</script>
