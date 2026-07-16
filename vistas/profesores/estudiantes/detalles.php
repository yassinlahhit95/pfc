<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);

$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    $_SESSION['errores'] = "ESTUDIANTE NO ENCONTRADO.";
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | DETALLES DEL ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>FICHA DE ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-graduate"></i> INFORMACION PERSONAL</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= Security::escapeHtml(mb_strtoupper($estudiante['nombreEstudiante'], 'UTF-8')) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['emailEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['dniEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Telefono</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['telefonoEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo Formativo</div>
        <div class="valor-detalle">
            <span class="indicador-estado activo-verde"><?= Security::escapeHtml($estudiante['nombreCiclo'] ) ?></span>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= Security::escapeHtml(date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante']))) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciudad / Direccion</div>
        <div class="valor-detalle"><?= Security::escapeHtml($estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?= !empty($estudiante['observacionesEstudiante']) ? nl2br(Security::escapeHtml($estudiante['observacionesEstudiante'])) : '<span class="texto-suave">Sin observaciones registradas.</span>' ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-file-pdf"></i> SITUACION DEL TFG</h3>
    </div>
    <div class="caja alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="indicador-estado activo-verde">ENTREGADO</span>
                <p class="texto-pequeno texto-suave" style="margin-top: 5px;">Subido el: <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG']))) ?></p>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">PENDIENTE / NO SUBIDO</span>
            <?php } ?>
        </div>

        <?php
        $notaTFG = obtenerCalificacionTFG($idEstudiante);
        if ($notaTFG) {
        ?>
            <div style="text-align: right;">
                <p class="nombre-detalle" style="margin-bottom: 5px;">CALIFICACIÓN TFG</p>
                <span class="texto-negrita <?= Security::escapeHtml($notaTFG['nota'] >= 5 ? 'texto-verde' : 'texto-rojo') ?>" style="font-size: 1.5em;">
                    <?= Security::escapeHtml($notaTFG['nota'] ) ?> / 10
                </span>
            </div>
        <?php } ?>
    </div>

    <?php if ($notaTFG && !empty($notaTFG['observaciones'])) { ?>
        <div class="margen-arriba tarjeta-gris-suave" style="padding: 15px;">
            <p class="texto-negrita" style="font-size: 13px; color: #718096; margin-bottom: 5px;">FEEDBACK DEL TFG:</p>
            <p class="texto-pequeno"><?= nl2br(Security::escapeHtml($notaTFG['observaciones'])) ?></p>
        </div>
    <?php } ?>
</div>

<?php if (!empty($_SESSION['esTutor']) && !empty($_SESSION['idCicloTutor']) && $estudiante['idCiclo'] == $_SESSION['idCicloTutor']): ?>
<!-- Cambiar contraseña (tutor de ciclo) -->
<div class="panel" style="margin-top:16px;">
    <div class="panel-titulo-seccion" style="cursor:pointer;" onclick="document.getElementById('form-cambiar-pass-tutor').classList.toggle('oculto')">
        <i class="fas fa-key"></i> Cambiar contraseña del estudiante
        <small style="margin-left:8px;color:var(--dim);font-weight:400;">(Tutor de Ciclo)</small>
    </div>
    <div id="form-cambiar-pass-tutor" class="oculto" style="margin-top:16px;">
        <div class="formulario" style="max-width:480px;">
            <div class="campo ancho-total">
                <label for="nueva-pass-tutor">Nueva contraseña <small style="color:var(--dim)">(mín. 8 caracteres)</small></label>
                <input type="password" id="nueva-pass-tutor" minlength="8" autocomplete="new-password" placeholder="Nueva contraseña">
            </div>
            <div class="campo ancho-total">
                <button type="button" class="boton-primario" onclick="cambiarPassTutor()">
                    <i class="fas fa-save"></i> Guardar contraseña
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function cambiarPassTutor() {
    var pass = document.getElementById('nueva-pass-tutor').value;
    if (pass.length < 8) { if (window.Toast) Toast.show('Mínimo 8 caracteres.', 'error'); return; }
    fetch('../../../controladores/profesores/tutor/cambiarPassword.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'csrf_token=<?= Security::generateCSRFToken() ?>&id=<?= $idEstudiante ?>&nuevaPassword='+encodeURIComponent(pass)
    }).then(r => r.json()).then(d => {
        if (window.Toast) Toast.show(d.msg, d.ok ? 'success' : 'error');
        if (d.ok) document.getElementById('nueva-pass-tutor').value = '';
    });
}
</script>
<?php endif; ?>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
