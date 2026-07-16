<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_horario');

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/aulas.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$ciclos  = listarTodosLosCiclos();
$niveles = listarNiveles();

$idNivelFiltro = isset($_GET['nivel']) ? (int)$_GET['nivel'] : 0;

$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($ciclos, fn($ciclo) => (int)$ciclo['idNivel'] === $idNivelFiltro))
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

$aulasParaJs = array_map(fn($aula) => ['id' => (int)$aula['idAula'], 'codigo' => $aula['codigoAula']], $aulasDisponibles);

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
            <?php foreach ($niveles as $nivel) { ?>
                <option value="<?= Security::escapeHtml($nivel['idNivel']) ?>" <?= ((int)$nivel['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                </option>
            <?php } ?>
        </select>
        <label for="ciclo">Ciclo:</label>
        <select name="ciclo" id="ciclo" onchange="this.form.submit()">
            <?php foreach ($ciclosFiltrados as $ciclo) { ?>
                <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($ciclo['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($ciclo['nombreCiclo']) ?> (<?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?>)
                </option>
            <?php } ?>
        </select>
    </form>
    <?php if ($idCicloHorario) { ?>
    <form method="POST" action="../../../controladores/admin/informes/generarHorario.php" target="_blank" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idCiclo"    value="<?= (int)$idCicloHorario ?>">
        <button type="submit" class="btn-exportar-horario">
            <i class="fas fa-print"></i> Imprimir PDF
        </button>
    </form>
    <?php } ?>
</div>


<link rel="stylesheet" href="../../../public/css/features/horario-admin.css?v=<?= @filemtime(__DIR__."/../../../public/css/features/horario-admin.css") ?>">

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
            <input type="search" id="horarioBuscar" class="horario-buscar-input" placeholder="Buscar módulo o profesor..." autocomplete="off">
        </div>
        <div class="horario-lista-tarjetas" id="horarioTarjetas">
            <?php if (empty($asignaciones)) { ?>
                <p class="horario-panel-vacio">Este ciclo no tiene módulos con profesor asignado.</p>
            <?php } else { ?>
                <?php foreach ($asignaciones as $asignacion) {
                    $color = horarioColorModulo($asignacion['idModulo']);
                ?>
                    <div class="horario-tarjeta"
                         draggable="true"
                         data-modulo="<?= Security::escapeHtml($asignacion['idModulo']) ?>"
                         data-profesor="<?= Security::escapeHtml($asignacion['idProfesor']) ?>"
                         data-modulo-nombre="<?= Security::escapeHtml($asignacion['nombreModulo']) ?>"
                         data-profesor-nombre="<?= Security::escapeHtml($asignacion['nombreProfesor']) ?>"
                         data-color="<?= Security::escapeHtml($color) ?>">
                        <div class="horario-tarjeta-info">
                            <span class="horario-avatar" style="color:<?= Security::escapeHtml($color) ?>; border-color:<?= Security::escapeHtml($color) ?>;">
                                <?= Security::escapeHtml(horarioIniciales($asignacion['nombreModulo'])) ?>
                            </span>
                            <span class="horario-tarjeta-modulo"><?= Security::escapeHtml($asignacion['nombreModulo']) ?></span>
                        </div>
                        <span class="horario-tarjeta-prof"><?= Security::escapeHtml($asignacion['nombreProfesor']) ?></span>
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

<div id="horarioOverlay" class="horario-loading-overlay">
    <i class="fas fa-spinner fa-spin"></i>
    <span>Guardando…</span>
</div>

<script>
window.HORARIO_AULAS     = <?= json_encode($aulasParaJs, JSON_UNESCAPED_UNICODE) ?>;
window.HORARIO_END_SLOTS = <?= json_encode($endSlots) ?>;
</script>
<script src="../../../public/js/features/horario.js?v=<?= @filemtime(__DIR__."/../../../public/js/features/horario.js") ?>"></script>
<?php include '../comunes/footer.php'; ?>
