<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

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

$tituloDelPagina = "AULAPRO | GESTION DE TFGS";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTION DE TFGS ENTREGADOS</h1>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
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
                <?php if (!empty($tfgs)) { ?>
                    <?php foreach ($tfgs as $tfg) {
                        $notaTFG = $calificacionesTFG[$tfg['idEstudiante']];
                    ?>
                        <tr>
                            <td><?= Security::escapeHtml($tfg['nombreEstudiante'] ) ?></td>
                            <td><?= Security::escapeHtml($tfg['nombreCiclo'] ) ?></td>
                            <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG']))) ?></td>
                            <td>
                                <?php if (!empty($notaTFG)) { ?>
                                    <?php if ($notaTFG['nota'] >= 5) { ?>
                                        <span class="texto-verde texto-negrita"><?= Security::escapeHtml($notaTFG['nota'] ) ?></span>
                                    <?php } else { ?>
                                        <span class="texto-rojo texto-negrita"><?= Security::escapeHtml($notaTFG['nota'] ) ?></span>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="texto-suave">Sin calificar</span>
                                <?php } ?>
                            </td>
                            <td>
                                <div class="botones-accion">
                                    <a href="../../../public/uploads/pfc/<?= Security::escapeHtml($tfg['archivoTFG'] ) ?>" target="_blank" class="btn-accion btn-ver"><i class="fas fa-download"></i></a>
                                    <button type="button" class="btn-accion btn-editar" onclick="toggleFormCalificar('form-<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>')">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <a href="borrarPfc.php?id=<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>" class="btn-accion btn-eliminar"><i class="fas fa-trash"></i></a>
                                </div>

                                <div id="form-<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>" style="display: none; margin-top: 10px;">
                                    <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($tfg['idEstudiante'] ) ?>">
                                        <div class="campo">
                                            <label>Nota (0-10):</label>
                                            <input type="text" name="nota" value="<?= Security::escapeHtml(!empty($notaTFG) ? $notaTFG['nota'] : '') ?>" placeholder="Ej: 7.5" class="input-pequeno">
                                        </div>
                                        <div class="campo">
                                            <label>Observaciones:</label>
                                            <textarea name="observaciones" rows="2" placeholder="Comentarios opcionales..."><?= Security::escapeHtml(!empty($notaTFG) ? $notaTFG['observaciones'] : '') ?></textarea>
                                        </div>
                                        <div class="campo">
                                            <label>
                                                <input type="checkbox" name="notificarEstudiante" value="1">
                                                Notificar al estudiante por email y push
                                            </label>
                                        </div>
                                        <input type="submit" name="calificarTFG" class="boton-primario" value="Guardar Nota">
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="vacio">No hay TFGs subidos todavia.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFormCalificar(idFormulario) {
    $('#' + idFormulario).toggle();
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



