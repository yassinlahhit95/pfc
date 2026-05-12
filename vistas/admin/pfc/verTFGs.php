<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$todos_los_tfgs = listarTodosLosTFGs();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$calificacionesTFG = [];
foreach ($todos_los_tfgs as $tfg) {
    $calificacionesTFG[$tfg['idEstudiante']] = obtenerCalificacionTFG($tfg['idEstudiante']);
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "AULAPRO | GESTIÓN DE TFGS";
$seccion = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE TRABAJOS FIN DE GRADO</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="disposicion-flexible envoltura-flexible separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivelTFG" onchange="filtrarNivelTFGs()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= $nivelFiltro['idNivel'] ?>">
                        <?= $nivelFiltro['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo-formulario flexible-rellenar">
            <label>FILTRAR POR CICLO:</label>
            <select id="selectFiltroCicloTFG" onchange="filtrarTabla('selectFiltroCicloTFG', 'tablaTFGs')">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8') ?>" data-nivel="<?= $cicloFiltro['idNivel'] ?>">
                        <?= mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTFGs">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Archivo</th>
                    <th>Fecha Subida</th>
                    <th>Nota TFG</th>
                    <th>Calificar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_tfgs)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay TFGs registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_tfgs as $tfg) {
                        $nombreLimpio = str_replace(' ', '_', $tfg['nombreEstudiante']);
                        $nombreDescarga = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . ".pdf";
                        $notaTFG = $calificacionesTFG[$tfg['idEstudiante']];
                    ?>
                    <tr>
                        <td><strong><?= $tfg['nombreEstudiante'] ?></strong></td>
                        <td><?= $tfg['nombreCiclo'] ?></td>
                        <td>
                            <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="boton-secundario boton-pequeno" download="<?= $nombreDescarga ?>">
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </a>
                        </td>
                        <td><?= date('d/m/Y', strtotime($tfg['fechaSubidaTFG'])) ?></td>
                        <td>
                            <?php if (!empty($notaTFG)) { ?>
                                <?php if ($notaTFG['nota'] >= 5) { ?>
                                    <span class="texto-verde texto-negrita"><?= $notaTFG['nota'] ?></span>
                                <?php } else { ?>
                                    <span class="texto-rojo texto-negrita"><?= $notaTFG['nota'] ?></span>
                                <?php } ?>
                            <?php } else { ?>
                                <span class="texto-atenuado">Sin calificar</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn-accion btn-editar" title="Calificar TFG" onclick="toggleFormCalificar('form-<?= $tfg['idEstudiante'] ?>')">
                                <i class="fas fa-star"></i>
                            </button>
                            <div id="form-<?= $tfg['idEstudiante'] ?>" style="display: none; margin-top: 10px;">
                                <form action="../../../controladores/admin/pfc/calificar.php" method="POST" class="form-estandar">
                                    <input type="hidden" name="idEstudiante" value="<?= $tfg['idEstudiante'] ?>">
                                    <div class="campo-formulario">
                                        <label>Nota (0-10):</label>
                                        <input type="text" name="nota" value="<?= !empty($notaTFG) ? $notaTFG['nota'] : '' ?>" placeholder="Ej: 7.5" class="input-pequeno">
                                    </div>
                                    <div class="campo-formulario">
                                        <label>Observaciones:</label>
                                        <textarea name="observaciones" rows="2" placeholder="Comentarios opcionales..."><?= !empty($notaTFG) ? $notaTFG['observaciones'] : '' ?></textarea>
                                    </div>
                                    <div class="campo-formulario">
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
function filtrarNivelTFGs() {
    var idNivel = document.getElementById('selectFiltroNivelTFG').value;
    var selectCiclo = document.getElementById('selectFiltroCicloTFG');
    var opciones = selectCiclo.querySelectorAll('option');

    opciones.forEach(function(opcion) {
        if (opcion.value === '') {
            opcion.style.display = '';
            return;
        }
        if (idNivel === '' || opcion.getAttribute('data-nivel') === idNivel) {
            opcion.style.display = '';
        } else {
            opcion.style.display = 'none';
        }
    });

    selectCiclo.value = '';
    filtrarTabla('selectFiltroCicloTFG', 'tablaTFGs');
}

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




