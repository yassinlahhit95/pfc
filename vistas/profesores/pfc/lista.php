<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$idProfesor = $_SESSION['idProfesor'];
$tfgs = listarTFGsPorProfesor($idProfesor);

$calificacionesTFG = [];
foreach ($tfgs as $tfg) {
    $calificacionesTFG[$tfg['idEstudiante']] = obtenerCalificacionTFG($tfg['idEstudiante']);
}

$tituloDelPagina = "AULAPRO | GESTIÓN DE TFGS";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>GESTIÓN DE TFGS ENTREGADOS</h1>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Fecha de Subida</th>
                    <th>Nota TFG</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tfgs) && !is_numeric($tfgs)) { ?>
                    <?php foreach ($tfgs as $tfg) {
                        $nombreLimpio = str_replace(' ', '_', $tfg['nombreEstudiante']);
                        $nombreDescarga = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . ".pdf";
                        $notaTFG = $calificacionesTFG[$tfg['idEstudiante']];
                    ?>
                        <tr>
                            <td><strong><?= $tfg['nombreEstudiante'] ?></strong></td>
                            <td><?= $tfg['nombreCiclo'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])) ?></td>
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
                                <div class="botones-accion">
                                    <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="btn-accion btn-ver" download="<?= $nombreDescarga ?>" title="Descargar"><i class="fas fa-download"></i></a>
                                    <button type="button" class="btn-accion btn-editar" title="Calificar TFG" onclick="toggleFormCalificar('form-<?= $tfg['idEstudiante'] ?>')">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <form action="../../../controladores/profesores/pfc/borrar.php" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este archivo?');" style="display: inline;">
                                        <input type="hidden" name="idEstudiante" value="<?= $tfg['idEstudiante'] ?>">
                                        <button type="submit" class="btn-accion btn-eliminar" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div id="form-<?= $tfg['idEstudiante'] ?>" style="display: none; margin-top: 10px;">
                                    <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="form-estandar">
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
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay TFGs subidos todavía.</td>
                    </tr>
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>





