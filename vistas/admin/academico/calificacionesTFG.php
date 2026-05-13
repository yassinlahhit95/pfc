<?php
session_start();
$titulo_pagina = "AULAPRO | EVALUACIÓN TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$listaCiclos = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$listaEvaluacion = listarEvaluacionTFG($idCicloElegido);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>EVALUACIÓN DE TRABAJOS FIN DE GRADO (TFG)</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="calificacionesTFG.php" class="disposicion-flexible alinear-centro separacion-grande envoltura-flexible">
        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por Nivel:</label>
            <select id="filtroNivelTFG" onchange="filtrarCiclosTFG()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivel) { ?>
                    <option value="<?= $nivel['idNivel'] ?>">
                        <?= $nivel['nombreNivel'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" id="selectCicloTFG" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" data-nivel="<?= $ciclo['idNivel'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="mb-15">
            <button type="button" class="boton-secundario" onclick="window.location.href = 'calificacionesTFG.php';">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($error) { ?><div class="mensaje-error"><?= $error ?></div><?php } ?>

<div class="tarjeta-blanca margen-arriba">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Estado Entrega</th>
                    <th>Nota TFG</th>
                    <th>Observaciones</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEvaluacion)) { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">No hay estudiantes registrados.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $item) { ?>
                        <tr>
                            <td><strong><?= $item['nombreEstudiante'] ?></strong></td>
                            <td><?= $item['nombreCiclo'] ?></td>
                            <td>
                                <?php if (!empty($item['archivoTFG'])) { ?>
                                    <span class="estado-bolita activo-verde" title="Subido el <?= date('d/m/Y', strtotime($item['fechaSubidaTFG'])) ?>">ENTREGADO</span>
                                    <a href="../../../public/uploads/pfc/<?= $item['archivoTFG'] ?>" target="_blank" class="color-primario ml-10"><i class="fas fa-file-pdf"></i> Ver</a>
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
                                    <i class="fas fa-edit"></i> Evaluar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para calificar (Simple overlay) -->
<div id="modalCalificar" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:2000; display:flex; align-items:center; justify-content:center;">
    <div class="tarjeta-blanca" style="max-width:500px; width:90%; position:relative;">
        <h2 id="modalTitulo">Calificar TFG</h2>
        <form action="../../../controladores/admin/pfc/calificar.php" method="POST" class="form-estandar mt-20">
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
function filtrarCiclosTFG() {
    var idNivel = document.getElementById('filtroNivelTFG').value;
    var selectCiclo = document.getElementById('selectCicloTFG');
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
}

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

// Cerrar modal si se hace clic fuera
window.onclick = function(event) {
    var modal = document.getElementById('modalCalificar');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
