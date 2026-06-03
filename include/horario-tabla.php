<?php
/**
 * Partial reutilizable del cuadro horario semanal.
 *
 * Variables esperadas antes del include:
 *   $horarioCeldas  array  -> salida de listarHorarioPorCiclo()  (key "Dia|HH:MM")
 *   $puedeEditar    bool   -> true solo para el director (CRUD), false en modo lectura
 *   $idCicloHorario int    -> ciclo mostrado (se usa en los data-* para el AJAX)
 *
 * Requiere que el modelo modelos/horarios.php este incluido (franjas y dias).
 */
if (!isset($horarioCeldas)) $horarioCeldas = [];
if (!isset($puedeEditar))   $puedeEditar = false;
if (!isset($idCicloHorario)) $idCicloHorario = 0;
if (!isset($aulasDisponibles)) $aulasDisponibles = []; // solo se usa en modo edicion

$franjasHorario = obtenerFranjasHorario();
$diasHorario    = obtenerDiasHorario();
// horarioColorModulo() y horarioIniciales() viven en modelos/horarios.php
?>
<div class="horario-tabla-envoltura<?= $puedeEditar ? ' horario-editable' : '' ?>"
     data-ciclo="<?= Security::escapeHtml($idCicloHorario) ?>">
    <table class="horario-tabla">
        <thead>
            <tr>
                <th class="horario-col-hora">Hora</th>
                <?php foreach ($diasHorario as $dia) { ?>
                    <th><?= Security::escapeHtml($dia) ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($franjasHorario as $franja) { ?>
                <?php if (!empty($franja['recreo'])) { ?>
                    <tr class="horario-fila-recreo">
                        <td class="horario-hora"><?= Security::escapeHtml($franja['inicio'] . ' - ' . $franja['fin']) ?></td>
                        <td colspan="<?= count($diasHorario) ?>" class="horario-celda-recreo">
                            <i class="fas fa-mug-hot"></i> RECREO
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="horario-hora"><?= Security::escapeHtml($franja['inicio'] . ' - ' . $franja['fin']) ?></td>
                        <?php foreach ($diasHorario as $dia) {
                            $clave  = $dia . '|' . $franja['inicio'];
                            $celda  = $horarioCeldas[$clave] ?? null;
                        ?>
                            <td class="horario-celda"
                                data-dia="<?= Security::escapeHtml($dia) ?>"
                                data-inicio="<?= Security::escapeHtml($franja['inicio']) ?>"
                                data-fin="<?= Security::escapeHtml($franja['fin']) ?>">
                                <?php if ($celda && ($celda['idModulo'] || $celda['idProfesor'])) {
                                    $color = horarioColorModulo($celda['idModulo']);
                                ?>
                                    <div class="horario-asignado" style="background-color: <?= Security::escapeHtml($color) ?>;"
                                         data-modulo="<?= Security::escapeHtml($celda['idModulo']) ?>"
                                         data-profesor="<?= Security::escapeHtml($celda['idProfesor']) ?>">
                                        <?php if ($puedeEditar) { ?>
                                            <button type="button" class="horario-limpiar" aria-label="Quitar">
                                                <i class="fas fa-xmark"></i>
                                            </button>
                                        <?php } ?>
                                        <div class="horario-asignado-modulo"><?= Security::escapeHtml($celda['nombreModulo'] ?? 'Sin módulo') ?></div>
                                        <div class="horario-asignado-prof"><?= Security::escapeHtml($celda['nombreProfesor'] ?? 'Sin profesor') ?></div>
                                        <?php if ($puedeEditar) { ?>
                                            <select class="horario-aula-select" aria-label="Aula">
                                                <option value="">— Aula —</option>
                                                <?php foreach ($aulasDisponibles as $au) { ?>
                                                    <option value="<?= Security::escapeHtml($au['idAula']) ?>" <?= ($au['idAula'] == ($celda['idAula'] ?? 0)) ? 'selected' : '' ?>>
                                                        Aula <?= Security::escapeHtml($au['codigoAula']) ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php } elseif (!empty($celda['codigoAula'])) { ?>
                                            <div class="horario-asignado-aula"><i class="fas fa-location-dot"></i> Aula <?= Security::escapeHtml($celda['codigoAula']) ?></div>
                                        <?php } ?>
                                    </div>
                                <?php } elseif ($puedeEditar) { ?>
                                    <div class="horario-vacia"><i class="fas fa-plus"></i> Asignar</div>
                                <?php } else { ?>
                                    <div class="horario-vacia-lectura">—</div>
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>
