<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$mis_ciclos = listarCiclosDeProfesor($idProfesor);
$listaEvaluacion = listarEvaluacionTFGporProfesor($idProfesor, $idCicloElegido);

$tituloDelPagina = "AULAPRO | EVALUACION TFG";
$seccionActual = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUACION DE TFGS (ALUMNOS ASIGNADOS)</h1>
</div>

<div class="panel margen-abajo">
    <form method="GET" action="tfg.php" class="caja al-final espacio-grande">
        <div class="campo relleno">
            <label for="idCiclo">Filtrar por Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos mis Ciclos --</option>
                <?php foreach ($mis_ciclos as $c) { ?>
                    <option value="<?= $c['idCiclo'] ?>" <?= $idCicloElegido == $c['idCiclo'] ? 'selected' : '' ?>>
                        <?= $c['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
</form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= $errores ?></div><?php } ?>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Entrega</th>
                    <th>Nota TFG</th>
                    <th>Observaciones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEvaluacion)) { ?>
                    <tr>
                        <td colspan="6" class="vacio">No hay estudiantes asignados en estos ciclos.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $item) { ?>
                        <tr>
                            <td><?= $item['nombreEstudiante'] ?></td>
                            <td><?= $item['nombreCiclo'] ?></td>
                            <td>
                                <?php if (!empty($item['archivoTFG'])) { ?>
                                    <span class="indicador-estado activo-verde">ENTREGADO</span>
                                    <a href="../../../public/uploads/pfc/<?= $item['archivoTFG'] ?>" target="_blank" class="color-primario" style="margin-left: 10px;"><i class="fas fa-file-pdf"></i></a>
                                <?php } else { ?>
                                    <span class="indicador-estado inactivo-rojo">PENDIENTE</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($item['nota'] !== null) { ?>
                                    <span class="texto-negrita <?= $item['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                        <?= $item['nota'] ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="texto-suave">---</span>
                                <?php } ?>
                            </td>
                            <td class="cuerpo-mensaje-tabla"><?= $item['observaciones'] ?? '<em>Sin observaciones</em>' ?></td>
                            <td>
                                <button type="button" class="btn-accion btn-editar" onclick="abrirModalCalificar(<?= $item['idEstudiante'] ?>, '<?= addslashes($item['nombreEstudiante']) ?>', '<?= $item['nota'] ?>', '<?= addslashes($item['observaciones'] ?? '') ?>')">
                                    <i class="fas fa-star"></i> Calificar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para calificar -->
<div id="modalCalificar" class="oculto" style="position:fixed; top:0; left:0; width:100%; height:100%; z-index:2000; display:flex; align-items:center; justify-content:center;">
    <div class="modal-fondo"></div>
    <div class="panel" style="max-width:500px; width:90%; position:relative; z-index:1;">
        <h2 id="modalTitulo">Calificar TFG</h2>
        <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="formulario" style="margin-top: 20px;">
            <input type="hidden" name="idEstudiante" id="modalIdEstudiante">
            <input type="hidden" name="origen" value="calificacionesTFG">
            
            <div class="campo">
                <label>Nota Final (0-10):</label>
                <input type="number" name="nota" id="modalNota" step="0.1" min="0" max="10" required>
            </div>
            
            <div class="campo">
                <label>Observaciones / Feedback:</label>
                <textarea name="observaciones" id="modalObservaciones" rows="4"></textarea>
            </div>
            
            <div class="campo">
                <label class="campo-checkbox">
                    <input type="checkbox" name="notificarEstudiante" value="1" checked>
                    Notificar al estudiante (Email + Push)
                </label>
            </div>
            
            <div class="acciones">
                <input type="submit" name="calificarTFG" class="boton-primario" value="Guardar Calificacion">
                <button type="button" class="boton-secundario" onclick="cerrarModal()">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCalificar(id, nombre, nota, obs) {
    $('#modalIdEstudiante').val(id);
    $('#modalTitulo').text('Evaluar TFG: ' + nombre);
    $('#modalNota').val(nota);
    $('#modalObservaciones').val(obs);
    $('#modalCalificar').removeClass('oculto').css('display', 'flex');
}

function cerrarModal() {
    $('#modalCalificar').addClass('oculto').hide();
}

$(document).on('click', '#modalCalificar', function(e) {
    if ($(e.target).is('#modalCalificar')) cerrarModal();
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
