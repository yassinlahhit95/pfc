<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaCiclos  = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaCiclos, fn($ciclo) => (int)$ciclo['idNivel'] === $idNivelFiltro))
    : $listaCiclos;

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);
$idRetoElegido = (int)($_GET['idReto'] ?? 0);

if ($idNivelFiltro && $idCicloElegido && !in_array((int)$idCicloElegido, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idCicloElegido = 0;
    $idRetoElegido  = 0;
}

$listaRetos = $idCicloElegido ? listarRetosPorCiclo($idCicloElegido) : [];
$listaEstudiantes = [];
$notasPorEstudiante = [];
$cursoAnioReto = '';
if ($idCicloElegido && $idRetoElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idCicloElegido);
    $notasPorEstudiante = listarCalificacionesRetoPorEstudiantes(array_column($listaEstudiantes, 'idEstudiante'), $idRetoElegido);
    foreach (listarModulosDeReto($idRetoElegido) as $modulo) {
        if (!empty($modulo['cursoAnio'])) { $cursoAnioReto = $modulo['cursoAnio']; break; }
    }
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
            <label>Filtrar por Nivel:</label>
            <select name="idNivel" onchange="this.form.submit()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivel) { ?>
                    <option value="<?= (int)$nivel['idNivel'] ?>" <?= ((int)$nivel['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($ciclosFiltrados as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['nombreNivel']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label>Seleccione Reto:</label>
            <select name="idReto" onchange="this.form.submit()" <?= empty($idCicloElegido) ? 'disabled' : '' ?>>
                <option value="">-- Seleccionar Reto --</option>
                <?php foreach ($listaRetos as $reto) { ?>
                    <option value="<?= (int)$reto['idReto'] ?>" <?= ((int)$idRetoElegido === (int)$reto['idReto']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($reto['nombreReto']) ?>
                    </option>
                <?php } ?>
            </select>
            <?php if ($cursoAnioReto): ?>
            <p class="texto-suave" style="font-size:.8rem;margin-top:5px;"><i class="fas fa-layer-group"></i> Módulo de <strong><?= Security::escapeHtml($cursoAnioReto) ?></strong> año</p>
            <?php endif; ?>
        </div>

</form>
</div>


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
                    <?php foreach ($listaEstudiantes as $estudiante) {
                        $notaActual = $notasPorEstudiante[$estudiante['idEstudiante']] ?? '';
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></td>
                        <td><?= Security::escapeHtml($estudiante['nombreCiclo']) ?></td>
                        <td>
                            <?php if ($notaActual !== '') { ?>
                                <span class="texto-negrita <?= $notaActual >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= Security::escapeHtml((string)$notaActual) ?>
                                </span>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="evaluarReto.php?idEstudiante=<?= (int)$estudiante['idEstudiante'] ?>&idReto=<?= (int)$idRetoElegido ?>&idCiclo=<?= (int)$idCicloElegido ?>" class="btn-accion btn-editar">
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
