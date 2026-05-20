<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
$idModulo = intval($_GET['idModulo'] ?? 0);

$mis_ciclos = listarCiclosDeProfesor($idProfesor);
$mis_modulos = [];
if ($idCiclo > 0) {
    $mis_modulos = listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
} else {
    $mis_modulos = listarModulosDeProfesor($idProfesor);
}

$calificaciones = listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo, $idModulo);

$tituloDelPagina = "AULAPRO | CALIFICACIONES";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NOTAS DE ALUMNOS</h1>
    <a href="agregar.php" class="boton-primario">ASIGNAR NOTA</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel margen-abajo">
    <form method="GET" action="" class="caja al-final espacio-grande">
        <div class="campo relleno">
            <label for="idCiclo">Filtrar por Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="0">-- Todos mis Ciclos --</option>
                <?php foreach ($mis_ciclos as $c) { ?>
                    <option value="<?= $c['idCiclo'] ?>" <?= $idCiclo == $c['idCiclo'] ? 'selected' : '' ?>>
                        <?= $c['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="idModulo">Filtrar por Modulo:</label>
            <select name="idModulo" id="idModulo" onchange="this.form.submit()">
                <option value="0">-- Todos mis Modulos --</option>
                <?php foreach ($mis_modulos as $m) { ?>
                    <option value="<?= $m['idModulo'] ?>" <?= $idModulo == $m['idModulo'] ? 'selected' : '' ?>>
                        <?= $m['nombreModulo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div style="margin-bottom: 15px;">
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaNotasProf">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Módulo</th>
                    <th>1º Ev</th>
                    <th>1º Final</th>
                    <th>2º Ev</th>
                    <th>2º Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($calificaciones) { ?>
                    <?php foreach ($calificaciones as $nota) { ?>
                        <tr>
                            <td><?= $nota['nombreEstudiante'] ?></td>
                            <td class="texto-negrita"><?= $nota['nombreModulo'] ?></td>
                            <td><?= $nota['nota_1ev'] ?? '---' ?></td>
                            <td class="texto-negrita"><?= $nota['nota_1final'] ?? '---' ?></td>
                            <td><?= $nota['nota_2ev'] ?? '---' ?></td>
                            <td class="texto-negrita"><?= $nota['nota_2final'] ?? '---' ?></td>
                            <td>
                                <div class="botones-accion">
                                    <?php if ($nota['idCalificacion']) { ?>
                                        <a href="editar.php?id=<?= $nota['idCalificacion'] ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i></a>
                                    <?php } else { ?>
                                        <a href="agregar.php?idCiclo=<?= $idCiclo ?>&idModulo=<?= $nota['idModulo'] ?>" class="btn-accion btn-ver" style="background: #27ae60;"><i class="fas fa-plus"></i></a>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay calificaciones que coincidan con los filtros.</td>
                    </tr>
                <?php } ?>
            </tbody>
            </table>
            </div>
            </div>

            <?php include __DIR__ . '/../comunes/footer.php'; ?>

