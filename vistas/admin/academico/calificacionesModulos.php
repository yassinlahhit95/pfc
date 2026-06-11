<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito  = $_SESSION['exito']  ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaCiclos  = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$idNivelFiltro   = (int)($_GET['idNivel']  ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $listaCiclos;

$idCicloElegido  = $_GET['idCiclo']  ?? '';
$idModuloElegido = $_GET['idModulo'] ?? '';

if ($idNivelFiltro && $idCicloElegido && !in_array((int)$idCicloElegido, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idCicloElegido = $idModuloElegido = '';
}

$listaModulos     = !empty($idCicloElegido)  ? listarModulosPorCiclo($idCicloElegido)          : [];
$listaEstudiantes = !empty($idModuloElegido) ? listarCalificacionesPorModulo($idModuloElegido) : [];

$titulo_pagina = "AULAPRO | NOTAS DE MÓDULOS";
$seccion = 'notas_modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
.nota-input {
    width: 58px;
    text-align: center;
    font-weight: bold;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 6px;
    font-size: 0.9rem;
    transition: border-color .15s, background .15s;
}
.nota-input:focus { border-color: #1e3a6e; outline: none; box-shadow: 0 0 0 2px #1e3a6e22; }
.nota-input.is-co { background:#f0fdf4; border-color:#4ade80; color:#166534; pointer-events:none; }
.nota-input.is-error { border-color:#ef4444; background:#fff1f1; box-shadow: 0 0 0 2px #ef444422; }
.nota-error {
    display: none;
    font-size: 0.6rem;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    white-space: nowrap;
}
.nota-error.visible { display: block; }

.co-merged { background: #f0fdf4; text-align: center; vertical-align: middle; }
.co-merged-label {
    display: inline-block;
    color: #166534;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: .3px;
}

.badge-letra {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 4px;
    margin-left: 3px;
    vertical-align: middle;
    letter-spacing: .4px;
}
.badge-SB { background:#d1fae5; color:#065f46; }
.badge-NT { background:#d1fae5; color:#065f46; }
.badge-BI { background:#dbeafe; color:#1e40af; }
.badge-SF { background:#fef3c7; color:#92400e; }
.badge-IN { background:#fee2e2; color:#991b1b; }

/* Modern CO checkbox */
.co-toggle { display: inline-flex; align-items: center; cursor: pointer; user-select: none; }
.co-toggle input[type="checkbox"] { display: none; }
.co-box {
    width: 20px; height: 20px;
    border: 2px solid #94a3b8;
    border-radius: 5px;
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
    font-size: 11px; font-weight: bold; color: transparent;
}
.co-toggle input:checked ~ .co-box { background:#16a34a; border-color:#16a34a; color:#fff; }

.glosario-bar {
    font-size: 0.75rem;
    color: #64748b;
    padding: 8px 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 12px;
}
.glosario-bar span { font-weight: 700; color: #1e3a6e; margin-right: 2px; }
.glosario-bar .sep { margin: 0 10px; color: #cbd5e1; }
</style>

<div class="cabecera">
    <h1>CALIFICACIONES POR MÓDULO</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesModulos.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Nivel:</label>
            <select name="idNivel" onchange="this.form.submit()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $n) { ?>
                    <option value="<?= (int)$n['idNivel'] ?>" <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($n['nombreNivel']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>Seleccione un Ciclo:</label>
            <select name="idCiclo" id="selectCicloMod" onchange="this.form.submit()">
                <option value="">-Seleccionar Ciclo-</option>
                <?php foreach ($ciclosFiltrados as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['nombreNivel']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>Seleccione un Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($idCicloElegido) ? 'disabled' : '' ?>>
                <option value="">-Seleccionar Módulo-</option>
                <?php foreach ($listaModulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>" <?= ($idModuloElegido == $modulo['idModulo']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito)   { ?><div class="mensaje-exito"><?= Security::escapeHtml($exito)   ?></div><?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php } ?>

<?php if (!empty($idModuloElegido)) { ?>
    <div class="glosario-bar">
        <span>SB</span> Sobresaliente 9–10
        <span class="sep">|</span>
        <span>NT</span> Notable 7–8
        <span class="sep">|</span>
        <span>BI</span> Bien 6
        <span class="sep">|</span>
        <span>SF</span> Suficiente 5
        <span class="sep">|</span>
        <span>IN</span> Insuficiente 1–4
        <span class="sep">|</span>
        <span>NP</span> No Presentado
        <span class="sep">|</span>
        <span>EX</span> Exento
        <span class="sep">|</span>
        <span>CO</span> Convalidado
    </div>

    <div class="panel margen-arriba">
        <form action="../../../controladores/admin/academico/calificarModulos.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModuloElegido) ?>">
            <input type="hidden" name="idCiclo"  value="<?= Security::escapeHtml($idCicloElegido) ?>">
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>1ª EV</th>
                            <th>1ª FINAL</th>
                            <th>2ª EV</th>
                            <th>2ª FINAL</th>
                            <th title="Convalidado">CO</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $glLabels = [
                            'SB' => ['SB — Sobresaliente', 'badge-SB'],
                            'NT' => ['NT — Notable',       'badge-NT'],
                            'BI' => ['BI — Bien',          'badge-BI'],
                            'SF' => ['SF — Suficiente',    'badge-SF'],
                            'IN' => ['IN — Insuficiente',  'badge-IN'],
                            'NP' => ['NP — No Presentado', 'badge-IN'],
                            'EX' => ['EX — Exento',        'badge-BI'],
                            'CO' => ['CO — Convalidado',   'badge-NT'],
                        ];
                        // Returns [label, cssClass] for the definitive grade (last non-empty cell)
                        $calcGlosario = function($notas) use ($glLabels) {
                            $especiales = ['NP','EX','CO'];
                            $pairs = [
                                [$notas['nota_2final'] ?? null, $notas['estado_2final'] ?? null],
                                [$notas['nota_2ev']    ?? null, $notas['estado_2ev']    ?? null],
                                [$notas['nota_1final'] ?? null, $notas['estado_1final'] ?? null],
                                [$notas['nota_1ev']    ?? null, $notas['estado_1ev']    ?? null],
                            ];
                            foreach ($pairs as [$nota, $est]) {
                                if ($est && in_array($est, $especiales, true)) return $glLabels[$est];
                                if ($nota !== null && $nota !== '') {
                                    $d = (float)$nota;
                                    if ($d >= 9) return $glLabels['SB'];
                                    if ($d >= 7) return $glLabels['NT'];
                                    if ($d >= 6) return $glLabels['BI'];
                                    if ($d >= 5) return $glLabels['SF'];
                                    return $glLabels['IN'];
                                }
                            }
                            return null;
                        };
                        // Display value for a cell: estado code takes priority over numeric nota
                        $cellVal = function($nota, $estado) {
                            return ($estado && in_array($estado, ['NP','EX','CO'], true)) ? $estado : ($nota ?? '');
                        };
                        ?>
                        <?php if (empty($listaEstudiantes)) { ?>
                            <tr>
                                <td colspan="7" class="vacio">No hay estudiantes matriculados en este ciclo</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($listaEstudiantes as $alumno) {
                                $idEst = $alumno['idEstudiante'];
                                $notas = obtenerNotasModulo($idEst, $idModuloElegido) ?? [];
                                $gl    = $calcGlosario($notas);
                                $cells = [
                                    ['notas_1ev[]',    $cellVal($notas['nota_1ev']    ?? null, $notas['estado_1ev']    ?? null)],
                                    ['notas_1final[]', $cellVal($notas['nota_1final'] ?? null, $notas['estado_1final'] ?? null)],
                                    ['notas_2ev[]',    $cellVal($notas['nota_2ev']    ?? null, $notas['estado_2ev']    ?? null)],
                                    ['notas_2final[]', $cellVal($notas['nota_2final'] ?? null, $notas['estado_2final'] ?? null)],
                                ];
                                $allCO = array_reduce($cells, fn($c, $cell) => $c && strtoupper(trim((string)$cell[1])) === 'CO', true);
                            ?>
                                <tr>
                                    <td>
                                        <?= mb_strtoupper(Security::escapeHtml($alumno['nombreEstudiante']), 'UTF-8') ?>
                                        <input type="hidden" name="estudiantes[]" value="<?= Security::escapeHtml($idEst) ?>">
                                        <input type="hidden" name="observaciones[]" value="<?= Security::escapeHtml($notas['observaciones'] ?? '') ?>">
                                    </td>
                                    <?php foreach ($cells as [$name, $val]) {
                                        $isCO = strtoupper(trim((string)$val)) === 'CO';
                                    ?>
                                    <td class="nota-td"<?= $allCO ? ' style="display:none"' : '' ?>>
                                        <input type="text" name="<?= $name ?>"
                                               value="<?= Security::escapeHtml($val) ?>"
                                               class="nota-input<?= $isCO ? ' is-co' : '' ?>"
                                               maxlength="4"
                                               placeholder="0–10/NP/EX"
                                               <?= $isCO ? 'readonly' : '' ?>
                                               oninput="actualizarBadge(this)"
                                               onblur="validarNota(this)"
                                               onfocus="limpiarError(this)">
                                        <span class="badge-letra"></span>
                                        <span class="nota-error">Valor inválido</span>
                                    </td>
                                    <?php } ?>
                                    <td class="co-merged" colspan="4"<?= $allCO ? '' : ' style="display:none"' ?>>
                                        <span class="co-merged-label">&#10003; Convalidado</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <label class="co-toggle">
                                            <input type="checkbox" class="co-row-check"
                                                   <?= $allCO ? 'checked' : '' ?>
                                                   onchange="toggleCORow(this)">
                                            <span class="co-box">✓</span>
                                        </label>
                                    </td>
                                    <td>
                                        <span class="badge-letra celda-glosario<?= $gl ? ' '.$gl[1] : '' ?>"
                                              style="<?= $gl ? 'display:inline-block;font-size:.8rem;padding:3px 8px;' : '' ?>">
                                            <?= $gl ? $gl[0] : '—' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($listaEstudiantes)) { ?>
                <div class="acciones">
                    <input type="submit" name="guardarNotas" class="boton-primario" value="GUARDAR TODAS LAS NOTAS">
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<script>
var GL_LABELS = {
    'SB': ['SB — Sobresaliente', 'badge-SB'],
    'NT': ['NT — Notable',       'badge-NT'],
    'BI': ['BI — Bien',          'badge-BI'],
    'SF': ['SF — Suficiente',    'badge-SF'],
    'IN': ['IN — Insuficiente',  'badge-IN'],
    'NP': ['NP — No Presentado', 'badge-IN'],
    'EX': ['EX — Exento',        'badge-BI'],
    'CO': ['CO — Convalidado',   'badge-NT']
};
var ESPECIALES = ['NP', 'EX', 'CO'];

function notaALetra(v) {
    v = v.trim().toUpperCase();
    if (ESPECIALES.indexOf(v) !== -1) return v;
    var n = parseFloat(v);
    if (isNaN(n) || v === '') return '';
    if (n >= 9) return 'SB';
    if (n >= 7) return 'NT';
    if (n >= 6) return 'BI';
    if (n >= 5) return 'SF';
    if (n >= 0) return 'IN';
    return '';
}

function actualizarGlosario(fila) {
    var cel = fila.querySelector('.celda-glosario');
    if (!cel) return;
    // definitive = last non-empty: [3]=2final > [2]=2ev > [1]=1final > [0]=1ev
    var inputs = fila.querySelectorAll('.nota-input');
    var key = '';
    for (var j = inputs.length - 1; j >= 0; j--) {
        var v = inputs[j].value.trim();
        if (v !== '') { key = notaALetra(v); break; }
    }
    if (key && GL_LABELS[key]) {
        cel.textContent   = GL_LABELS[key][0];
        cel.className     = 'badge-letra celda-glosario ' + GL_LABELS[key][1];
        cel.style.cssText = 'display:inline-block;font-size:.8rem;padding:3px 8px;';
    } else {
        cel.textContent   = '—';
        cel.className     = 'celda-glosario';
        cel.style.cssText = '';
    }
}

function esValorValido(v) {
    v = v.trim().toUpperCase();
    if (v === '') return true;
    if (ESPECIALES.indexOf(v) !== -1) return true;
    var n = parseFloat(v);
    return !isNaN(n) && n >= 0 && n <= 10 && String(v).match(/^\d+(\.\d+)?$/);
}

function validarNota(input) {
    var errSpan = input.parentElement.querySelector('.nota-error');
    if (!esValorValido(input.value)) {
        input.classList.add('is-error');
        if (errSpan) errSpan.classList.add('visible');
    } else {
        input.classList.remove('is-error');
        if (errSpan) errSpan.classList.remove('visible');
    }
}

function limpiarError(input) {
    input.classList.remove('is-error');
    var errSpan = input.parentElement.querySelector('.nota-error');
    if (errSpan) errSpan.classList.remove('visible');
}

function actualizarBadge(input) {
    var v     = input.value.trim().toUpperCase();
    var badge = input.nextElementSibling;
    var letra = notaALetra(v);
    badge.textContent = letra;
    badge.className   = 'badge-letra' + (letra ? ' badge-' + letra : '');
    actualizarGlosario(input.closest('tr'));
}

function toggleCORow(checkbox) {
    var row      = checkbox.closest('tr');
    var notaTds  = row.querySelectorAll('.nota-td');
    var merged   = row.querySelector('.co-merged');
    var inputs   = row.querySelectorAll('.nota-input');
    if (checkbox.checked) {
        inputs.forEach(function(inp) {
            inp.dataset.prev = inp.value;
            inp.value        = 'CO';
            inp.readOnly     = true;
            inp.classList.remove('is-error');
            var err = inp.parentElement.querySelector('.nota-error');
            if (err) err.classList.remove('visible');
        });
        notaTds.forEach(function(td) { td.style.display = 'none'; });
        merged.style.display = '';
    } else {
        inputs.forEach(function(inp) {
            inp.value    = inp.dataset.prev || '';
            inp.readOnly = false;
            inp.classList.remove('is-co');
            actualizarBadge(inp);
        });
        notaTds.forEach(function(td) { td.style.display = ''; });
        merged.style.display = 'none';
    }
    actualizarGlosario(row);
}

document.querySelectorAll('.nota-input').forEach(function(inp) {
    actualizarBadge(inp);
});

var form = document.querySelector('form[action*="calificarModulos"]');
if (form) {
    form.addEventListener('submit', function(e) {
        var invalid = false;
        form.querySelectorAll('.nota-input').forEach(function(inp) {
            if (!esValorValido(inp.value)) {
                validarNota(inp);
                invalid = true;
            }
        });
        if (invalid) {
            e.preventDefault();
            form.querySelector('.nota-input.is-error').focus();
        }
    });
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
