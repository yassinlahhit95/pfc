<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/AssetMin.php";

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
    for ($hora = 8; $hora <= 21; $hora++) {
        for ($minuto = 0; $minuto < 60; $minuto += 15) {
            if ($hora === 21 && $minuto > 0) break;
            $todosSlots[] = sprintf('%02d:%02d', $hora, $minuto);
        }
    }
    $endSlots   = array_values(array_filter($todosSlots, fn($slot) => $slot >= '08:15'));
    $usedStarts = array_column($franjasActuales, 'inicio');
    $freeStarts = array_values(array_filter($todosSlots, fn($slot) => $slot < '21:00' && !in_array($slot, $usedStarts)));
    $aulasParaJs = array_map(fn($aula) => ['id' => (int)$aula['idAula'], 'codigo' => $aula['codigoAula']], $aulasDisponibles);
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
            <?php foreach ($ciclos as $ciclo) { ?>
                <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($ciclo['idCiclo'] == $idCicloHorario) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($ciclo['nombreCiclo']) ?> (<?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?>)
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

<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/horario-admin.css') ?>">

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
            <input type="search" id="horarioBuscar" class="horario-buscar-input" placeholder="Buscar módulo o profesor..."
                   autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other">
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
                            <?php foreach ($freeStarts as $slot): ?>
                                <option value="<?= $slot ?>"><?= $slot ?></option>
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
                                <?php foreach ($dias as $dia): ?>
                                    <th style="text-align:center;"><?= Security::escapeHtml($dia) ?></th>
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
                                    foreach ($franjas as $franja) {
                                        if ($franja['recreo']) continue;
                                        $clave = $dia . '|' . $franja['inicio'];
                                        if (isset($ocupacionEscuela[$clave][$idAula])) {
                                            $ocu = $ocupacionEscuela[$clave][$idAula];
                                            $ocupadas[] = Security::escapeHtml($franja['inicio']) . ' ' . Security::escapeHtml($ocu['abreviaturaCiclo']);
                                        }
                                    }
                                ?>
                                <td style="text-align:center;vertical-align:top;">
                                    <?php if (empty($ocupadas)): ?>
                                        <span class="texto-estado verde" style="font-size:.7rem;">Libre</span>
                                    <?php else: ?>
                                        <?php foreach ($ocupadas as $ocupada): ?>
                                            <span class="texto-estado rojo" style="font-size:.68rem;display:block;"><?= $ocupada ?></span>
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
<script src="<?= AssetMin::url(__DIR__, '../../../public/js/features/horario.js') ?>"></script>

<?php } else { ?>
<div class="horario-contenido horario-solo-lectura">
    <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
