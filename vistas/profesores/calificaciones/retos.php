<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
$idModulo = intval($_GET['idModulo'] ?? 0);
$idReto = intval($_GET['idReto'] ?? 0);

$listaDeCiclos = listarCiclosDeProfesor($idProfesor);
$listaDeModulos = [];
if ($idCiclo) {
    $listaDeModulos = listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
}

$listaDeRetos = [];
if ($idModulo) {
    $listaDeRetos = listarRetosFiltrados($idModulo);
}

$listaDeEstudiantes = [];
if ($idReto) {
    $listaDeEstudiantes = listarEstudiantesPorCiclo($idCiclo);
}

$tituloDelPagina = "AULAPRO | NOTAS DE RETOS";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICACIONES POR RETO</h1>
</div>

<div class="panel">
    <form method="GET" action="../../../vistas/profesores/calificaciones/retos.php" class="caja alinear-centro espacio-grande">
        <div class="campo relleno">
            <label for="idCiclo">1. Seleccione Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= $idCiclo == $ciclo['idCiclo'] ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label for="idModulo">2. Seleccione Modulo:</label>
            <select name="idModulo" id="idModulo" onchange="this.form.submit()" <?= empty($idCiclo) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeModulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= $idModulo == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= $modulo['nombreModulo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label for="idReto">3. Seleccione Reto:</label>
            <select name="idReto" id="idReto" onchange="this.form.submit()" <?= empty($idModulo) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeRetos as $reto) { ?>
                    <option value="<?= $reto['idReto'] ?>" <?= $idReto == $reto['idReto'] ? 'selected' : '' ?>>
                        <?= $reto['nombreReto'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div style="margin-left: 10px;">
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<?php if ($idReto) { ?>
    <div class="panel margen-arriba">
        <form action="../../../controladores/profesores/calificaciones/calificarRetos.php" method="POST">
            <input type="hidden" name="idReto" value="<?= $idReto ?>">
            <input type="hidden" name="idCiclo" value="<?= $idCiclo ?>">
            <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
            
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Nota Reto (0-10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaDeEstudiantes)) { ?>
                            <tr><td colspan="2" class="vacio">No hay estudiantes en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaDeEstudiantes as $estudiante) { 
                                $idEstudiante = $estudiante['idEstudiante'];
                                $notaRetoActual = obtenerCalificacionReto($idEstudiante, $idReto);
                            ?>
                            <tr>
                                <td>
                                    <?= strtoupper($estudiante['nombreEstudiante']) ?>
                                    <input type="hidden" name="estudiantes[]" value="<?= $idEstudiante ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas[]" value="<?= $notaRetoActual ?>" class="ancho-ajustable-nota">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($listaDeEstudiantes)) { ?>
                <div class="acciones">
                    <input type="submit" name="guardarNotasReto" class="boton-primario" value="GUARDAR NOTAS DEL RETO">
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

