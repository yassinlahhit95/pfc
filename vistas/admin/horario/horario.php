<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/aulas.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$ciclos  = listarTodosLosCiclos();
$niveles = listarNiveles();

$idNivelFiltro = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 0;

$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($ciclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $ciclos;

$idCicloHorario = isset($_GET['ciclo']) ? (int)$_GET['ciclo'] : (int)($ciclosFiltrados[0]['idCiclo'] ?? 0);

if ($idNivelFiltro && $idCicloHorario) {
    $cicloIds = array_column($ciclosFiltrados, 'idCiclo');
    if (!in_array($idCicloHorario, $cicloIds)) {
        $idCicloHorario = (int)($ciclosFiltrados[0]['idCiclo'] ?? 0);
    }
}

$horarioCeldas    = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$asignaciones     = $idCicloHorario ? listarAsignacionesPorCiclo($idCicloHorario) : [];
$aulasDisponibles = listarAulasActivas();
$puedeEditar      = true;
$franjasActuales  = $idCicloHorario ? obtenerFranjasHorario($idCicloHorario) : [];

// All 15-min slots 08:00 → 21:00
$todosSlots = [];
for ($h = 8; $h <= 21; $h++) {
    for ($m = 0; $m < 60; $m += 15) {
        if ($h === 21 && $m > 0) break;
        $todosSlots[] = sprintf('%02d:%02d', $h, $m);
    }
}
$endSlots   = array_values(array_filter($todosSlots, fn($s) => $s >= '08:15'));
$usedStarts = array_column($franjasActuales, 'inicio');
$freeStarts = array_values(array_filter($todosSlots, fn($s) => $s < '21:00' && !in_array($s, $usedStarts)));

$aulasParaJs = array_map(fn($au) => ['id' => (int)$au['idAula'], 'codigo' => $au['codigoAula']], $aulasDisponibles);

$titulo_pagina = "AULAPRO | CUADRO HORARIO";
$seccion = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CUADRO HORARIO</h1>
    <form method="GET" class="horario-selector-form">
        <label for="nivel">Nivel:</label>
        <select name="nivel" id="nivel" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php foreach ($niveles as $n) { ?>
                <option value="<?= Security::escapeHtml($n['idNivel']) ?>" <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($n['nombreNivel']) ?>
                </option>
            <?php } ?>
        </select>
        <label for="ciclo">Ciclo:</label>
        <select name="ciclo" id="ciclo" onchange="this.form.submit()">
            <?php foreach ($ciclosFiltrados as $c) { ?>
                <option value="<?= Security::escapeHtml($c['idCiclo']) ?>" <?= ($c['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)
                </option>
            <?php } ?>
        </select>
    </form>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
<?php } ?>

<?php if (empty($ciclosFiltrados)) { ?>
    <div class="panel"><p class="vacio">No hay ciclos para el nivel seleccionado.</p></div>
<?php } else { ?>
<div class="horario-workspace"
     id="horarioApp"
     data-ciclo="<?= Security::escapeHtml($idCicloHorario) ?>"
     data-csrf="<?= Security::generateCSRFToken() ?>">

    <aside class="horario-panel-lateral">
        <div class="horario-panel-cabecera">
            <h3>Asignaturas</h3>
            <p>Arrastra una tarjeta al horario o haz clic para seleccionarla.</p>
        </div>
        <div class="horario-buscador">
            <i class="fas fa-magnifying-glass"></i>
            <input type="search" id="horarioBuscar" class="horario-buscar-input" placeholder="Buscar módulo o profesor...">
        </div>
        <div class="horario-lista-tarjetas" id="horarioTarjetas">
            <?php if (empty($asignaciones)) { ?>
                <p class="horario-panel-vacio">Este ciclo no tiene módulos con profesor asignado.</p>
            <?php } else { ?>
                <?php foreach ($asignaciones as $a) {
                    $color = horarioColorModulo($a['idModulo']);
                ?>
                    <div class="horario-tarjeta"
                         draggable="true"
                         data-modulo="<?= Security::escapeHtml($a['idModulo']) ?>"
                         data-profesor="<?= Security::escapeHtml($a['idProfesor']) ?>"
                         data-modulo-nombre="<?= Security::escapeHtml($a['nombreModulo']) ?>"
                         data-profesor-nombre="<?= Security::escapeHtml($a['nombreProfesor']) ?>"
                         data-color="<?= Security::escapeHtml($color) ?>">
                        <div class="horario-tarjeta-info">
                            <span class="horario-avatar" style="color:<?= Security::escapeHtml($color) ?>; border-color:<?= Security::escapeHtml($color) ?>;">
                                <?= Security::escapeHtml(horarioIniciales($a['nombreModulo'])) ?>
                            </span>
                            <span class="horario-tarjeta-modulo"><?= Security::escapeHtml($a['nombreModulo']) ?></span>
                        </div>
                        <span class="horario-tarjeta-prof"><?= Security::escapeHtml($a['nombreProfesor']) ?></span>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </aside>

    <div class="horario-contenido">
        <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>

        <?php if ($puedeEditar && $idCicloHorario) { ?>
        <div class="hf-panel" id="horarioFranjaPanel">
            <div class="hf-header">
                <i class="fas fa-sliders"></i>
                <span>Añadir franja horaria</span>
                <small class="hf-hint">08:00 – 21:00 · franjas de 15 min</small>
            </div>
            <?php if (empty($freeStarts)) { ?>
                <p class="hf-lleno"><i class="fas fa-check-circle"></i> Todos los slots de 08:00 a 21:00 están configurados.</p>
            <?php } else { ?>
            <div class="hf-body">
                <div class="hf-form">
                    <div class="hf-campo">
                        <label for="franjaInicio">Inicio</label>
                        <select id="franjaInicio" class="hf-sel">
                            <option value="">— hora —</option>
                            <?php foreach ($freeStarts as $s): ?>
                                <option value="<?= $s ?>"><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hf-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="hf-campo">
                        <label for="franjaFin">Fin</label>
                        <select id="franjaFin" class="hf-sel" disabled>
                            <option value="">— hora —</option>
                        </select>
                    </div>
                    <label class="hf-check-label" for="franjaReceso">
                        <input type="checkbox" id="franjaReceso">
                        <i class="fas fa-mug-hot"></i> Descanso
                    </label>
                    <button type="button" id="btnAddFranja" class="hf-btn" disabled>
                        <i class="fas fa-plus"></i> Añadir
                    </button>
                </div>
                <p class="hf-tip"><i class="fas fa-circle-info"></i> Solo aparecen horas aún no asignadas. Las franjas marcadas como <strong>Descanso</strong> no aceptan módulos.</p>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>

<style>
/* ── Horario: eliminar franja (botón × en cada fila) ── */
.horario-hora {
    position: relative;
    white-space: nowrap;
}
.horario-hora-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: space-between;
}
.horario-hora-texto {
    flex: 1;
    font-size: .8rem;
    font-weight: 600;
    white-space: nowrap;
}
.horario-quitar-franja {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    padding: 0;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    background: #fff;
    color: #94a3b8;
    font-size: .65rem;
    cursor: pointer;
    transition: all .15s;
    line-height: 1;
}
.horario-quitar-franja:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #ef4444;
    transform: scale(1.1);
}

/* ── Panel añadir franja ── */
.hf-panel {
    margin-top: 20px;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
}
.hf-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    background: linear-gradient(135deg,#f8fafc,#f1f5f9);
    border-bottom: 1px solid #e2e8f0;
    font-size: .8rem;
    font-weight: 700;
    color: #475569;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.hf-hint { margin-left: auto; font-size: .72rem; font-weight: 400; color: #94a3b8; text-transform: none; letter-spacing: 0; }
.hf-body { padding: 16px 18px 12px; }
.hf-form {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 14px;
}
.hf-campo {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.hf-campo label {
    font-size: .72rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.hf-sel {
    padding: 8px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: .875rem;
    background: #f8fafc;
    color: #1e293b;
    min-width: 96px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    appearance: auto;
}
.hf-sel:focus  { outline: none; border-color: #667eea; background: #fff; }
.hf-sel:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
.hf-arrow { color: #94a3b8; padding-bottom: 6px; }
.hf-check-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: .82rem;
    color: #475569;
    cursor: pointer;
    padding-bottom: 6px;
    user-select: none;
}
.hf-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border: none;
    border-radius: 9px;
    background: linear-gradient(135deg,#1e3a6e,#2d5be3);
    color: #fff;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
}
.hf-btn:hover:not(:disabled)  { opacity: .9; transform: translateY(-1px); }
.hf-btn:disabled { opacity: .45; cursor: not-allowed; transform: none; }
.hf-tip { margin: 12px 0 0; font-size: .75rem; color: #94a3b8; }
.hf-tip strong { color: #64748b; }
.hf-lleno { padding: 16px 18px; color: #64748b; font-size: .85rem; margin: 0; }
.hf-lleno i { color: #10b981; margin-right: 6px; }
</style>

<script>
window.HORARIO_AULAS     = <?= json_encode($aulasParaJs, JSON_UNESCAPED_UNICODE) ?>;
window.HORARIO_END_SLOTS = <?= json_encode($endSlots) ?>;
</script>
<script src="../../../public/js/horario.js?v=<?= @filemtime(__DIR__."/../../../public/js/horario.js") ?>"></script>
<?php include '../comunes/footer.php'; ?>
