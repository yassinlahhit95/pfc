<?php
session_start();

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$ciclos = listarTodosLosCiclos();

$idCicloHorario = isset($_GET['ciclo']) ? (int)$_GET['ciclo'] : (int)($ciclos[0]['idCiclo'] ?? 0);

$horarioCeldas    = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$asignaciones     = $idCicloHorario ? listarAsignacionesPorCiclo($idCicloHorario) : [];
$aulasDisponibles = listarAulasActivas();
$puedeEditar      = true;

// Aulas en JSON para que el JS reconstruya el desplegable al asignar una tarjeta.
$aulasParaJs = [];
foreach ($aulasDisponibles as $au) {
    $aulasParaJs[] = ['id' => (int)$au['idAula'], 'codigo' => $au['codigoAula']];
}

$titulo_pagina = "AULAPRO | CUADRO HORARIO";
$seccion = 'horario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CUADRO HORARIO</h1>
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
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
<?php } ?>

<?php if (empty($ciclos)) { ?>
    <div class="panel"><p class="vacio">No hay ciclos formativos. Crea un ciclo antes de gestionar el horario.</p></div>
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
    </div>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
<script>window.HORARIO_AULAS = <?= json_encode($aulasParaJs, JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="../../../public/js/horario.js?v=<?= @filemtime(__DIR__."/../../../public/js/horario.js") ?>"></script>
