<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$mis_ciclos = obtenerCiclosDeProfesor($idProfesor);
$listaEvaluacion = listarEvaluacionTFGporProfesor($idProfesor, $idCicloElegido);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$tituloDelPagina = "AULAPRO | EVALUACIÓN TFG";
$seccionActual = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>EVALUACIÓN DE TFGS (ALUMNOS ASIGNADOS)</h1>
</div>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="tfg.php" class="disposicion-flexible alinear-fin separacion-grande">
        <div class="campo-formulario flexible-rellenar">
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
        <div class="mb-15">
            <button type="button" class="boton-secundario" onclick="window.location.href = 'tfg.php';">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($error) { ?><div class="mensaje-error"><?= $error ?></div><?php } ?>

<div class="tarjeta-blanca">
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
                        <td colspan="6" class="sin-datos">No hay estudiantes asignados en estos ciclos.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $item) { ?>
                        <tr>
                            <td><strong><?= $item['nombreEstudiante'] ?></strong></td>
                            <td><?= $item['nombreCiclo'] ?></td>
                            <td>
                                <?php if (!empty($item['archivoTFG'])) { ?>
                                    <span class="estado-bolita activo-verde">ENTREGADO</span>
                                    <a href="../../../public/uploads/pfc/<?= $item['archivoTFG'] ?>" target="_blank" class="color-primario ml-10"><i class="fas fa-file-pdf"></i></a>
                                <?php } else { ?>
                                    <span class="estado-bolita inactivo-rojo">PENDIENTE</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($item['nota'] !== null) { ?>
                                    <span class="texto-negrita <?= $item['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                        <?= $item['nota'] ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="texto-atenuado">---</span>
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
<div id="modalCalificar" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; display:flex; align-items:center; justify-content:center;">
    <div class="tarjeta-blanca" style="max-width:500px; width:90%; position:relative;">
        <h2 id="modalTitulo">Calificar TFG</h2>
        <form action="../../../controladores/profesores/pfc/calificar.php" method="POST" class="form-estandar mt-20">
            <input type="hidden" name="idEstudiante" id="modalIdEstudiante">
            <input type="hidden" name="origen" value="calificacionesTFG">
            
            <div class="campo-formulario">
                <label>Nota Final (0-10):</label>
                <input type="number" name="nota" id="modalNota" step="0.1" min="0" max="10" required>
            </div>
            
            <div class="campo-formulario">
                <label>Observaciones / Feedback:</label>
                <textarea name="observaciones" id="modalObservaciones" rows="4"></textarea>
            </div>
            
            <div class="campo-formulario">
                <label class="campo-checkbox">
                    <input type="checkbox" name="notificarEstudiante" value="1" checked>
                    Notificar al estudiante (Email + Push)
                </label>
            </div>
            
            <div class="form-acciones">
                <button type="submit" name="calificarTFG" class="boton-primario">
                    <i class="fas fa-save"></i> Guardar Calificación
                </button>
                <button type="button" class="boton-secundario" onclick="cerrarModal()">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCalificar(id, nombre, nota, obs) {
    document.getElementById('modalIdEstudiante').value = id;
    document.getElementById('modalTitulo').innerText = 'Evaluar TFG: ' + nombre;
    document.getElementById('modalNota').value = nota;
    document.getElementById('modalObservaciones').value = obs;
    document.getElementById('modalCalificar').classList.remove('d-none');
    document.getElementById('modalCalificar').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalCalificar').classList.add('d-none');
    document.getElementById('modalCalificar').style.display = 'none';
}

window.onclick = function(event) {
    var modal = document.getElementById('modalCalificar');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
