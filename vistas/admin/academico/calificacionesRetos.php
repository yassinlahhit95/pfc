<?php
session_start();

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idCicloElegido = $_GET['idCiclo'] ?? 0;
$idRetoElegido = $_GET['idReto'] ?? 0;

$listaCiclos = listarTodosLosCiclos();
$listaRetos = $idCicloElegido ? listarRetosPorCiclo($idCicloElegido) : [];
$listaEstudiantes = [];
if ($idCicloElegido && $idRetoElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idCicloElegido);
}

$titulo_pagina = "AULAPRO | NOTAS RETOS";
$seccion = 'notas_retos';
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
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaCiclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= $ciclo['nombreNivel'] ?>] <?= $ciclo['nombreCiclo'] ?>
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
                            <a href="evaluarReto.php?idEstudiante=<?= $est['idEstudiante'] ?>&idReto=<?= $idRetoElegido ?>&idCiclo=<?= $idCicloElegido ?>" class="btn-accion btn-editar">
                                <i class="fas fa-edit"></i> Evaluar
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
