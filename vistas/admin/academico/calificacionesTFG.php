<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$listaCiclos = listarTodosLosCiclos();
$listaEvaluacion = listarEvaluacionTFG($idCicloElegido);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | GESTIÓN TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN Y EVALUACIÓN DE TFGs</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesTFG.php" class="d-flex alinear-centro sep-g envoltura-flexible">
        <div class="campo relleno">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" id="selectCicloTFG" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= $ciclo['nombreNivel'] ?>] <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <button type="button" class="boton-secundario" onclick="window.location.href = 'calificacionesTFG.php';">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($error) { ?><div class="mensaje-error"><?= $error ?></div><?php } ?>

<div class="panel margen-arriba">
    <div class="tcont">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Estado</th>
                    <th>Fecha Subida</th>
                    <th>Archivo PDF</th>
                    <th>Nota TFG</th>
                    <th>Evaluar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEvaluacion)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay estudiantes registrados.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $item) {
                        $nombreLimpio = str_replace(' ', '_', $item['nombreEstudiante']);
                        $nombreDescarga = "TFG_" . $nombreLimpio . ".pdf";
                    ?>
                    <tr>
                        <td><?= $item['nombreEstudiante'] ?></td>
                        <td><?= $item['abreviaturaCiclo'] ?></td>
                        <td>
                            <?php if (!empty($item['archivoTFG'])) { ?>
                                <span class="bolita activo-verde">ENTREGADO</span>
                            <?php } else { ?>
                                <span class="bolita inactivo-rojo">PENDIENTE</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($item['fechaSubidaTFG'])) { ?>
                                <?= date('d/m/Y', strtotime($item['fechaSubidaTFG'])) ?>
                            <?php } else { ?>
                                <span class="atenuado">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($item['archivoTFG'])) { ?>
                                <a href="../../../public/uploads/pfc/<?= $item['archivoTFG'] ?>" target="_blank" class="btn-accion btn-ver">
                                    <i class="fas fa-file-pdf"></i> Descargar
                                </a>
                            <?php } else { ?>
                                <span class="atenuado">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($item['nota'] !== null) { ?>
                                <span class="texto-negrita <?= $item['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= $item['nota'] ?>
                                </span>
                            <?php } else { ?>
                                <span class="atenuado">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn-accion btn-editar" onclick="toggleFormCalificar('form-<?= $item['idEstudiante'] ?>')">
                                <i class="fas fa-edit"></i> Evaluar
                            </button>
                            <div id="form-<?= $item['idEstudiante'] ?>" style="display: none; margin-top: 10px;">
                                <form action="../../../controladores/admin/pfc/calificar.php" method="POST" class="formulario">
                                    <input type="hidden" name="idEstudiante" value="<?= $item['idEstudiante'] ?>">
                                    <div class="campo">
                                        <label>Nota (0-10):</label>
                                        <input type="text" name="nota" value="<?= $item['nota'] ?? '' ?>" placeholder="Ej: 7.5">
                                    </div>
                                    <div class="campo">
                                        <label>Observaciones:</label>
                                        <textarea name="observaciones" rows="2"><?= $item['observaciones'] ?? '' ?></textarea>
                                    </div>
                                    <div class="campo">
                                        <label>
                                            <input type="checkbox" name="notificarEstudiante" value="1">
                                            Notificar al estudiante por email y push
                                        </label>
                                    </div>
                                    <button type="submit" name="calificarTFG" class="boton-primario">
                                        <i class="fas fa-save"></i> Guardar Nota
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFormCalificar(idFormulario) {
    var formulario = document.getElementById(idFormulario);
    if (formulario.style.display === 'none') {
        formulario.style.display = 'block';
    } else {
        formulario.style.display = 'none';
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
