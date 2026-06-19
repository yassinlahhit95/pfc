<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);
$idRetoElegido  = (int)($_GET['idReto']  ?? 0);

$listaCiclos  = listarCiclosDeProfesor($idProfesor);
$listaRetos   = $idCicloElegido ? listarRetosPorCicloDeProfesor($idCicloElegido, $idProfesor) : [];

// Authorization: verify the selected reto belongs to this professor's cycle
$idRetoValido = false;
if ($idRetoElegido && $idCicloElegido) {
    foreach ($listaRetos as $r) {
        if ((int)$r['idReto'] === $idRetoElegido) { $idRetoValido = true; break; }
    }
    if (!$idRetoValido) $idRetoElegido = 0;
}

$listaEstudiantes = [];
if ($idCicloElegido && $idRetoElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idCicloElegido);
}

$tituloDelPagina = "AULAPRO | NOTAS RETOS";
$seccionActual   = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
.nota-reto-input {
    width: 68px;
    text-align: center;
    font-weight: bold;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 8px;
    font-size: 0.95rem;
    transition: border-color .15s, background .15s;
}
.nota-reto-input:focus { border-color: #1e3a6e; outline: none; box-shadow: 0 0 0 2px #1e3a6e22; }
.nota-reto-input.is-ok    { background: #f0fdf4; border-color: #4ade80; }
.nota-reto-input.is-error { border-color: #ef4444; background: #fff1f1; }
.nota-badge {
    display: inline-block;
    min-width: 36px;
    text-align: center;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 4px;
    vertical-align: middle;
}
.nota-badge-ap { background: #d1fae5; color: #065f46; }
.nota-badge-su { background: #fee2e2; color: #991b1b; }
.nota-badge-pend { background: #f1f5f9; color: #64748b; }
</style>

<div class="cabecera">
    <h1>EVALUACIÓN DE RETOS</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesRetos.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Mis Ciclos:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar Ciclo --</option>
                <?php foreach ($listaCiclos as $c) { ?>
                    <option value="<?= (int)$c['idCiclo'] ?>" <?= ($idCicloElegido === (int)$c['idCiclo']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($c['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label>Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?= empty($listaRetos) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar Reto --</option>
                <?php foreach ($listaRetos as $r) { ?>
                    <option value="<?= (int)$r['idReto'] ?>" <?= ($idRetoElegido === (int)$r['idReto']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($r['nombreReto']) ?>
                        <?php if (!empty($r['fechaFin'])) { ?>
                            (hasta <?= Security::escapeHtml(date('d/m/Y', strtotime($r['fechaFin']))) ?>)
                        <?php } ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>


<?php if ($idRetoElegido && $idCicloElegido) { ?>
<div class="panel margen-arriba">
    <form action="../../../controladores/profesores/calificaciones/calificarRetos.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto"   value="<?= $idRetoElegido ?>">
        <input type="hidden" name="idCiclo"  value="<?= $idCicloElegido ?>">
        <input type="hidden" name="idModulo" value="0">
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Ciclo</th>
                        <th style="text-align:center;">Nota (0–10)</th>
                        <th style="text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($listaEstudiantes)) { ?>
                        <tr><td colspan="4" class="vacio">No hay estudiantes en este ciclo.</td></tr>
                    <?php } else { ?>
                        <?php foreach ($listaEstudiantes as $est) {
                            $nota = obtenerCalificacionReto($est['idEstudiante'], $idRetoElegido);
                            $notaNum = is_numeric($nota) ? (float)$nota : null;
                        ?>
                        <tr>
                            <td class="texto-negrita"><?= Security::escapeHtml(strtoupper($est['nombreEstudiante'])) ?>
                                <input type="hidden" name="estudiantes[]" value="<?= (int)$est['idEstudiante'] ?>">
                            </td>
                            <td><?= Security::escapeHtml($est['nombreCiclo']) ?></td>
                            <td style="text-align:center;">
                                <input type="text"
                                       name="notas[]"
                                       value="<?= Security::escapeHtml($nota) ?>"
                                       class="nota-reto-input<?= $notaNum !== null ? ($notaNum >= 5 ? ' is-ok' : ' is-error') : '' ?>"
                                       placeholder="0–10"
                                       maxlength="5"
                                       oninput="actualizarRetoInput(this)"
                                       onblur="validarRetoInput(this)">
                            </td>
                            <td style="text-align:center;">
                                <?php if ($notaNum !== null) { ?>
                                    <span class="nota-badge <?= $notaNum >= 5 ? 'nota-badge-ap' : 'nota-badge-su' ?>">
                                        <?= $notaNum >= 5 ? 'APTO' : 'NO APTO' ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="nota-badge nota-badge-pend">PEND</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($listaEstudiantes)) { ?>
        <div class="acciones">
            <input type="submit" name="guardarNotasReto" class="boton-primario" value="GUARDAR TODAS LAS NOTAS">
        </div>
        <?php } ?>
    </form>
</div>
<?php } elseif (!$idCicloElegido) { ?>
    <div class="panel margen-arriba"><p class="vacio">Selecciona un ciclo para ver los retos disponibles.</p></div>
<?php } elseif (!$idRetoElegido) { ?>
    <div class="panel margen-arriba"><p class="vacio">Selecciona un reto para calificar a los estudiantes.</p></div>
<?php } ?>

<script>
function actualizarRetoInput(inp) {
    var v = inp.value.trim().replace(',', '.');
    var n = parseFloat(v);
    inp.classList.remove('is-ok', 'is-error');
    if (v === '') return;
    if (!isNaN(n) && n >= 0 && n <= 10) {
        inp.classList.add(n >= 5 ? 'is-ok' : 'is-error');
        // update badge in same row
        var badge = inp.closest('tr').querySelector('.nota-badge');
        if (badge) {
            badge.textContent = n >= 5 ? 'APTO' : 'NO APTO';
            badge.className = 'nota-badge ' + (n >= 5 ? 'nota-badge-ap' : 'nota-badge-su');
        }
    } else {
        inp.classList.add('is-error');
    }
}

function validarRetoInput(inp) {
    var v = inp.value.trim().replace(',', '.');
    if (v === '') { inp.classList.remove('is-ok', 'is-error'); return; }
    var n = parseFloat(v);
    if (isNaN(n) || n < 0 || n > 10) {
        inp.classList.add('is-error');
        inp.classList.remove('is-ok');
    }
}

var form = document.querySelector('form[action*="calificarRetos"]');
if (form) {
    form.addEventListener('submit', function(e) {
        var invalid = false;
        form.querySelectorAll('.nota-reto-input').forEach(function(inp) {
            var v = inp.value.trim().replace(',', '.');
            if (v === '') return;
            var n = parseFloat(v);
            if (isNaN(n) || n < 0 || n > 10) {
                inp.classList.add('is-error');
                invalid = true;
            }
        });
        if (invalid) {
            e.preventDefault();
            form.querySelector('.nota-reto-input.is-error').focus();
        }
    });
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
