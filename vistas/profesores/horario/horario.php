<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

if ($esTutor && $idCicloTutor) {
    // Tutor: edit their assigned cycle; selector locked to that cycle
    $cicloTutor     = obtenerCicloPorId($idCicloTutor);
    $ciclos         = $cicloTutor ? [$cicloTutor] : [];
    $idCicloHorario = $idCicloTutor;
    $puedeEditar    = true;
    $aulasDisponibles  = listarAulasActivas();
    $asignaciones      = $idCicloHorario ? listarAsignacionesPorCiclo($idCicloHorario) : [];
    $ocupacionEscuela  = listarOcupacionAulasEscuela();
    $franjasActuales   = $idCicloHorario ? obtenerFranjasHorario($idCicloHorario) : [];
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
} else {
    // Normal professor: read-only view of their own cycles
    $ciclos = listarCiclosDeProfesor($idProfesor);
    $idsPermitidos  = array_column($ciclos, 'idCiclo');
    $idCicloPedido  = isset($_GET['ciclo']) ? (int)$_GET['ciclo'] : 0;
    $idCicloHorario = in_array($idCicloPedido, $idsPermitidos) ? $idCicloPedido : (int)($ciclos[0]['idCiclo'] ?? 0);
    $puedeEditar    = false;
    $aulasDisponibles = [];
    $asignaciones   = [];
    $ocupacionEscuela = [];
    $franjasActuales  = [];
    $freeStarts       = [];
    $endSlots         = [];
    $aulasParaJs      = [];
}

$horarioCeldas = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];

$tituloDelPagina = "AULAPRO | CUADRO HORARIO";
$seccionActual   = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CUADRO HORARIO<?= $esTutor ? ' <span style="font-size:.65em;opacity:.7;">(Tutor)</span>' : '' ?></h1>
    <?php if (!$esTutor): ?>
    <form method="GET" class="horario-selector-form">
        <label for="ciclo">Ciclo:</label>
        <select name="ciclo" id="ciclo" onchange="this.form.submit()">
            <?php foreach ($ciclos as $c) { ?>
                <option value="<?= Security::escapeHtml($c['idCiclo']) ?>" <?= ($c['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($c['nombreCiclo']) ?> (<?= Security::escapeHtml($c['abreviaturaCiclo']) ?>)
                </option>
            <?php } ?>
        </select>
    </form>
    <?php else: ?>
    <span class="texto-suave"><?= Security::escapeHtml($ciclos[0]['nombreCiclo'] ?? '') ?></span>
    <?php endif; ?>
</div>

<?php if (empty($ciclos)) { ?>
    <div class="panel"><p class="vacio">No tienes ciclos asignados.</p></div>
<?php } elseif ($esTutor) { ?>

<link rel="stylesheet" href="../../../public/css/horario-admin.css?v=<?= @filemtime(__DIR__."/../../../public/css/horario-admin.css") ?>">

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

        <?php if ($idCicloHorario) { ?>
        <div class="hf-panel" id="horarioFranjaPanel">
            <div class="hf-header">
                <i class="fas fa-sliders"></i>
                <span>Añadir franja horaria</span>
                <small class="hf-hint">08:00 – 21:00 · franjas de 15 min</small>
            </div>
            <?php if (empty($freeStarts)) { ?>
                <p class="hf-lleno"><i class="fas fa-check-circle"></i> Todos los slots están configurados.</p>
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
            </div>
            <?php } ?>
        </div>

        <!-- School-wide aula availability panel -->
        <div class="panel margen-arriba">
            <details>
                <summary style="cursor:pointer;font-weight:600;padding:12px 0;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-map-marker-alt" style="color:var(--accent)"></i>
                    Disponibilidad de aulas — todo el centro
                </summary>
                <div style="overflow-x:auto;margin-top:12px;">
                    <?php
                    $dias = obtenerDiasHorario();
                    $todasLasAulas = listarAulasActivas();
                    $franjas = obtenerFranjasHorario($idCicloHorario);
                    ?>
                    <table class="tabla-datos" style="font-size:.78rem;">
                        <thead>
                            <tr>
                                <th>Aula</th>
                                <?php foreach ($dias as $d): ?>
                                    <th style="text-align:center;"><?= Security::escapeHtml($d) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($todasLasAulas as $aula):
                            $idAula = (int)$aula['idAula'];
                        ?>
                            <tr>
                                <td><strong><?= Security::escapeHtml($aula['codigoAula']) ?></strong><br><span class="texto-suave" style="font-size:.72rem;"><?= Security::escapeHtml($aula['nombreAula']) ?></span></td>
                                <?php foreach ($dias as $dia):
                                    $ocupadas = [];
                                    foreach ($franjas as $f) {
                                        if ($f['recreo']) continue;
                                        $clave = $dia . '|' . $f['inicio'];
                                        if (isset($ocupacionEscuela[$clave][$idAula])) {
                                            $ocu = $ocupacionEscuela[$clave][$idAula];
                                            $ocupadas[] = Security::escapeHtml($f['inicio']) . ' ' . Security::escapeHtml($ocu['abreviaturaCiclo']);
                                        }
                                    }
                                ?>
                                <td style="text-align:center;vertical-align:top;">
                                    <?php if (empty($ocupadas)): ?>
                                        <span class="texto-estado verde" style="font-size:.7rem;">Libre</span>
                                    <?php else: ?>
                                        <?php foreach ($ocupadas as $o): ?>
                                            <span class="texto-estado rojo" style="font-size:.68rem;display:block;"><?= $o ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
        <?php } ?>
    </div>
</div>

<div id="horarioOverlay" class="horario-loading-overlay">
    <i class="fas fa-spinner fa-spin"></i>
    <span>Guardando…</span>
</div>

<script>
window.HORARIO_AULAS     = <?= json_encode($aulasParaJs, JSON_UNESCAPED_UNICODE) ?>;
window.HORARIO_END_SLOTS = <?= json_encode($endSlots) ?>;
window.HORARIO_CTRL_BASE = '../../../controladores/profesores/horario/';
</script>
<script src="../../../public/js/horario.js?v=<?= @filemtime(__DIR__."/../../../public/js/horario.js") ?>"></script>

<?php } else { ?>
<div class="horario-contenido horario-solo-lectura">
    <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
