<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idCicloElegido = $_GET['idCiclo'] ?? 0;
$idRetoElegido = $_GET['idReto'] ?? 0;

$listaCiclos = listarCiclosDeProfesor($idProfesor);
$listaRetos = $idCicloElegido ? listarRetosPorCicloDeProfesor($idCicloElegido, $idProfesor) : [];
$listaEstudiantes = [];
if ($idCicloElegido && $idRetoElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idCicloElegido);
}

$tituloDelPagina = "AULAPRO | NOTAS RETOS";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUACIÓN DE RETOS</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesRetos.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Mis Ciclos --</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>Seleccione Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?= empty($idCicloElegido) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar Reto --</option>
                <?php foreach ($listaRetos as $reto) { ?>
                    <option value="<?= $reto['idReto'] ?>" <?= ($idRetoElegido == $reto['idReto']) ? 'selected' : '' ?>>
                        <?= $reto['nombreReto'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= $exito ?></div><?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= $errores ?></div><?php } ?>

<div class="panel margen-arriba">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Nota Reto</th>
                    <th>Evaluar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($idRetoElegido)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">Seleccione un ciclo y un reto para ver los estudiantes.</td>
                    </tr>
                <?php } elseif (empty($listaEstudiantes)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">No hay estudiantes registrados en este ciclo.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEstudiantes as $est) {
                        $notaActual = obtenerCalificacionReto($est['idEstudiante'], $idRetoElegido);
                    ?>
                    <tr>
                        <td><?= $est['nombreEstudiante'] ?></td>
                        <td><?= $est['nombreCiclo'] ?></td>
                        <td>
                            <?php if ($notaActual !== '') { ?>
                                <span class="texto-negrita <?= $notaActual >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= $notaActual ?>
                                </span>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn-accion btn-editar" onclick="toggleFormCalificar('form-<?= $est['idEstudiante'] ?>')">
                                <i class="fas fa-edit"></i> Evaluar
                            </button>
                            <div id="form-<?= $est['idEstudiante'] ?>" style="display: none; margin-top: 10px;">
                                <form action="../../../controladores/profesores/academico/calificarRetoUnico.php" method="POST" class="formulario">
                                    <input type="hidden" name="idEstudiante" value="<?= $est['idEstudiante'] ?>">
                                    <input type="hidden" name="idReto" value="<?= $idRetoElegido ?>">
                                    <input type="hidden" name="idCiclo" value="<?= $idCicloElegido ?>">
                                    <div class="campo">
                                        <label>Nota (0-10):</label>
                                        <input type="text" name="nota" value="<?= $notaActual ?>" placeholder="Ej: 7.5">
                                    </div>
                                    <input type="submit" name="guardarNota" class="boton-primario" value="Guardar Nota">
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>
