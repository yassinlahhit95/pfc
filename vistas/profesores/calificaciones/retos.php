<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: /pfc/index.php");
    exit;
}

$tituloPagina = "Notas de Retos - Portal Profesores";
$seccionActual = 'notas_retos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
$idModulo = intval($_GET['idModulo'] ?? 0);
$idReto = intval($_GET['idReto'] ?? 0);

$listaDeCiclos = obtenerCiclosDeProfesor($idProfesor);
$listaDeModulos = [];
if ($idCiclo) {
    $listaDeModulos = obtenerModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
}

$listaDeRetos = [];
if ($idModulo) {
    $listaDeRetos = listarRetosFiltrados($idModulo);
}

$listaDeEstudiantes = [];
if ($idReto) {
    $listaDeEstudiantes = listarEstudiantesPorCiclo($idCiclo);
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <h1>Calificaciones por Reto</h1>
</div>

<div class="tarjeta-blanca">
    <form method="GET" action="/pfc/vistas/profesores/calificaciones/retos.php" class="disposicion-flexible alinear-centro separacion-grande">
        <div class="campo-formulario flexible-rellenar">
            <label>1. Seleccione Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= $idCiclo == $ciclo['idCiclo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>2. Seleccione Módulo:</label>
            <select name="idModulo" onchange="this.form.submit()" <?= empty($idCiclo) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeModulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= $idModulo == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($modulo['nombreModulo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario flexible-rellenar">
            <label>3. Seleccione Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?= empty($idModulo) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($listaDeRetos as $reto) { ?>
                    <option value="<?= $reto['idReto'] ?>" <?= $idReto == $reto['idReto'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($reto['nombreReto']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>
</div>

<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>

<?php if ($idReto) { ?>
    <div class="tarjeta-blanca margen-arriba">
        <form action="/pfc/controladores/profesores/calificaciones/calificarRetos.php" method="POST">
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
                            <tr><td colspan="2" class="sin-datos">No hay estudiantes en este ciclo</td></tr>
                        <?php } else { ?>
                            <?php foreach ($listaDeEstudiantes as $estudiante) { 
                                $idEstudiante = $estudiante['idEstudiante'];
                                $notaRetoActual = obtenerCalificacionReto($idEstudiante, $idReto);
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars(strtoupper($estudiante['nombreEstudiante'])) ?></strong>
                                    <input type="hidden" name="estudiantes[]" value="<?= $idEstudiante ?>">
                                </td>
                                <td>
                                    <input type="text" name="notas[]" value="<?= htmlspecialchars($notaRetoActual) ?>" class="ancho-ajustable-nota">
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($listaDeEstudiantes)) { ?>
                <div class="form-acciones">
                    <button type="submit" name="guardarNotasReto" class="boton-primario">
                        <i class="fas fa-save"></i> Guardar Notas del Reto
                    </button>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
