<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$datosSesion = $_SESSION['datos_estudiante'] ?? null;
unset($_SESSION['datos_estudiante']);
if ($datosSesion) {
    $estudiante = $datosSesion + $estudiante;
}

$listaCiclos = listarTodosLosCiclos();
$todosLosCursos = listarTodosLosCursosAcademicos();
$listaNiveles = listarNiveles();

$titulo_pagina = "AULAPRO | MODIFICAR ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR ESTUDIANTE: <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    </div>
    <a href="verEstudiantes.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante) ?>">

        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreEstudiante') ?>">
                    <label for="nombreEstudiante">Nombre Completo</label>
                    <input type="text" id="nombreEstudiante" name="nombreEstudiante" value="<?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>">
                    <?= fieldError($errores, 'nombreEstudiante') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'emailEstudiante') ?>">
                    <label for="emailEstudiante">Email</label>
                    <input type="text" id="emailEstudiante" name="emailEstudiante" value="<?= Security::escapeHtml($estudiante['emailEstudiante']) ?>">
                    <?= fieldError($errores, 'emailEstudiante') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'curso') ?>">
                    <label for="curso">Nivel</label>
                    <select name="curso" id="curso" onchange="filtrarCiclos()">
                        <option value="">-- Selecciona un nivel --</option>
                        <?php foreach ($listaNiveles as $nivel): ?>
                            <option value="<?= Security::escapeHtml($nivel['nombreNivel']) ?>" <?php if ($estudiante['curso'] == $nivel['nombreNivel']) { echo 'selected'; } ?>>
                                <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= fieldError($errores, 'curso') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
                    <label for="idCiclo">Ciclo Formativo</label>
                    <select name="idCiclo" id="idCiclo">
                        <option value="">-- Selecciona un ciclo --</option>
                    </select>
                    <?= fieldError($errores, 'idCiclo') ?>
                </div>

                <div class="campo">
                    <label for="anioEstudio">Año de estudio</label>
                    <select name="anioEstudio" id="anioEstudio">
                        <option value="">-- Sin especificar --</option>
                    </select>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'dniEstudiante') ?>">
                    <label for="dniEstudiante">DNI</label>
                    <input type="text" id="dniEstudiante" name="dniEstudiante" value="<?= Security::escapeHtml($estudiante['dniEstudiante']) ?>">
                    <?= fieldError($errores, 'dniEstudiante') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'telefonoEstudiante') ?>">
                    <label for="telefonoEstudiante">Teléfono</label>
                    <input type="text" id="telefonoEstudiante" name="telefonoEstudiante" value="<?= Security::escapeHtml($estudiante['telefonoEstudiante']) ?>">
                    <?= fieldError($errores, 'telefonoEstudiante') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'fechaNacimientoEstudiante') ?>">
                    <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                    <input type="date" id="fechaNacimientoEstudiante" name="fechaNacimientoEstudiante" value="<?= Security::escapeHtml($estudiante['fechaNacimientoEstudiante']) ?>">
                    <input type="hidden" name="fechaAltaEstudiante" value="<?= Security::escapeHtml($estudiante['fechaAltaEstudiante'] ?? '') ?>">
                    <?= fieldError($errores, 'fechaNacimientoEstudiante') ?>
                </div>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'direccionEstudiante') ?>">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" id="direccionEstudiante" name="direccionEstudiante" value="<?= Security::escapeHtml($estudiante['direccionEstudiante']) ?>">
                <?= fieldError($errores, 'direccionEstudiante') ?>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'ciudadEstudiante') ?>">
                    <label for="ciudadEstudiante">Ciudad</label>
                    <input type="text" id="ciudadEstudiante" name="ciudadEstudiante" value="<?= Security::escapeHtml($estudiante['ciudadEstudiante']) ?>">
                    <?= fieldError($errores, 'ciudadEstudiante') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'codigoPostalEstudiante') ?>">
                    <label for="codigoPostalEstudiante">Código Postal</label>
                    <input type="text" id="codigoPostalEstudiante" name="codigoPostalEstudiante" value="<?= Security::escapeHtml($estudiante['codigoPostalEstudiante']) ?>">
                    <?= fieldError($errores, 'codigoPostalEstudiante') ?>
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="observacionesEstudiante">Observaciones</label>
                <textarea id="observacionesEstudiante" name="observacionesEstudiante"><?= Security::escapeHtml($estudiante['observacionesEstudiante']) ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEstudiante" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<div class="panel" style="margin-top:20px;">
    <h3 style="margin:0 0 16px;"><i class="fas fa-key" style="color:var(--accent);"></i> Cambiar contraseña</h3>
    <div class="formulario" style="grid-template-columns:1fr 1fr;gap:15px;align-items:end;">
        <div class="campo">
            <label for="nueva-pass-est">Nueva contraseña <small style="color:var(--dim);">(mín. 8 caracteres)</small></label>
            <input type="password" id="nueva-pass-est" minlength="8" placeholder="Nueva contraseña" autocomplete="new-password">
        </div>
        <div class="campo">
            <label for="nueva-pass-est-confirm">Confirmar contraseña</label>
            <input type="password" id="nueva-pass-est-confirm" minlength="8" placeholder="Repetir contraseña" autocomplete="new-password">
        </div>
    </div>
    <div class="acciones" style="margin-top:16px;">
        <button type="button" class="boton-primario" onclick="cambiarPassEst()">
            <i class="fas fa-lock"></i> Guardar contraseña
        </button>
    </div>
</div>

<script>
var listaDeCiclos = <?= json_encode($listaCiclos) ?>;
var todosCursos = <?= json_encode($todosLosCursos) ?>;
var anioEstudioActual = <?= json_encode($estudiante['anioEstudio'] ?? '') ?>;

function filtrarCiclos() {
    var nivelNombre = $('#curso').val();
    var $select = $('#idCiclo').empty().append('<option value="">-- Selecciona un ciclo --</option>');

    $.each(listaDeCiclos, function(i, ciclo) {
        if (ciclo.nombreNivel === nivelNombre) {
            $select.append($('<option>').val(ciclo.idCiclo).text(ciclo.nombreCiclo));
        }
    });
    poblarAnios();
}

function poblarAnios() {
    var idCiclo = $('#idCiclo').val();
    var $select = $('#anioEstudio').empty();
    $select.append($('<option>').val('').text('-- Sin especificar --'));
    $.each(todosCursos, function(i, curso) {
        if (String(curso.idCiclo) === String(idCiclo)) {
            $select.append($('<option>').val(curso.nombre).text(curso.nombre + ' año'));
        }
    });
    if (anioEstudioActual) $select.val(anioEstudioActual);
}

$('#idCiclo').on('change', poblarAnios);

$(function() {
    filtrarCiclos();
    $('#idCiclo').val('<?= Security::escapeHtml($estudiante['idCiclo']) ?>');
    poblarAnios();
});

function cambiarPassEst() {
    var pass    = document.getElementById('nueva-pass-est').value.trim();
    var confirm = document.getElementById('nueva-pass-est-confirm').value.trim();
    if (pass.length < 8) {
        if (window.Toast) Toast.show('La contraseña debe tener al menos 8 caracteres.', 'error');
        return;
    }
    if (pass !== confirm) {
        if (window.Toast) Toast.show('Las contraseñas no coinciden.', 'error');
        return;
    }
    var token = document.querySelector('[name=csrf_token]').value;
    fetch('../../../controladores/admin/usuarios/cambiarPassword.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'tipo=estudiante&id=<?= $idEstudiante ?>&nuevaPassword=' + encodeURIComponent(pass) + '&csrf_token=' + encodeURIComponent(token)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) {
            document.getElementById('nueva-pass-est').value = '';
            document.getElementById('nueva-pass-est-confirm').value = '';
            setTimeout(function() { location.reload(); }, 1800);
        }
    })
    .catch(function() { if (window.Toast) Toast.show('Error de conexión.', 'error'); });
}
</script>

<?php include '../comunes/footer.php'; ?>
