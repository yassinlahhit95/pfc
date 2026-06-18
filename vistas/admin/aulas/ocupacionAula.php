<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/aulas.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

$aulas = listarAulas();
$idAula = isset($_GET['aula']) ? (int)$_GET['aula'] : (int)($aulas[0]['idAula'] ?? 0);
$aulaActual = $idAula ? obtenerAulaPorId($idAula) : null;

$ocupacion = $idAula ? listarOcupacionAula($idAula) : [];
$franjas = obtenerFranjasHorario();
$dias    = obtenerDiasHorario();

$titulo_pagina = "AULAPRO | OCUPACIÓN POR AULA";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>OCUPACIÓN POR AULA</h1>
    <form method="GET" class="horario-selector-form">
        <label for="aula">Aula:</label>
        <select name="aula" id="aula" onchange="this.form.submit()">
            <?php foreach ($aulas as $a) { ?>
                <option value="<?= Security::escapeHtml($a['idAula']) ?>" <?= ($a['idAula'] == $idAula) ? 'selected' : '' ?>>
                    Aula <?= Security::escapeHtml($a['codigoAula']) ?><?= $a['nombreAula'] ? ' — ' . Security::escapeHtml($a['nombreAula']) : '' ?>
                </option>
            <?php } ?>
        </select>
        <a href="gestionAulas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </form>
</div>

<?php if (empty($aulas)) { ?>
    <div class="panel"><p class="vacio">No hay aulas registradas. Crea un aula primero.</p></div>
<?php } else { ?>
<div class="horario-contenido horario-solo-lectura">
    <div class="horario-tabla-envoltura">
        <table class="horario-tabla">
            <thead>
                <tr>
                    <th class="horario-col-hora">Hora</th>
                    <?php foreach ($dias as $dia) { ?>
                        <th><?= Security::escapeHtml($dia) ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($franjas as $franja) { ?>
                    <?php if (!empty($franja['recreo'])) { ?>
                        <tr class="horario-fila-recreo">
                            <td class="horario-hora"><?= Security::escapeHtml($franja['inicio'] . ' - ' . $franja['fin']) ?></td>
                            <td colspan="<?= count($dias) ?>" class="horario-celda-recreo"><i class="fas fa-mug-hot"></i> RECREO</td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td class="horario-hora"><?= Security::escapeHtml($franja['inicio'] . ' - ' . $franja['fin']) ?></td>
                            <?php foreach ($dias as $dia) {
                                $celda = $ocupacion[$dia . '|' . $franja['inicio']] ?? null;
                            ?>
                                <td class="horario-celda">
                                    <?php if ($celda) {
                                        $color = horarioColorModulo($celda['idModulo']);
                                    ?>
                                        <div class="horario-asignado" style="background-color: <?= Security::escapeHtml($color) ?>;">
                                            <div class="horario-asignado-modulo"><?= Security::escapeHtml($celda['nombreModulo'] ?? 'Sin módulo') ?></div>
                                            <div class="horario-asignado-prof"><?= Security::escapeHtml($celda['nombreProfesor'] ?? '') ?></div>
                                            <div class="horario-asignado-aula"><i class="fas fa-layer-group"></i> <?= Security::escapeHtml($celda['abreviaturaCiclo']) ?></div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="horario-vacia-lectura">Libre</div>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

<?php include '../comunes/footer.php'; ?>
