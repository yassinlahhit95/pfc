<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

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
    ? array_values(array_filter($listaCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
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
    foreach (listarModulosDeReto($idRetoElegido) as $_mr) {
        if (!empty($_mr['cursoAnio'])) { $cursoAnioReto = $_mr['cursoAnio']; break; }
    }
}

$titulo_pagina = "AULAPRO | NOTAS RETOS";
$seccion = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICACIONES</h1>
</div>

<div class="dashboard-grid" style="margin-bottom:24px;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));">
    <a href="verCalificaciones.php" class="tile card-soft" style="--tint:#4F46E5; text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-book" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">Módulos</span>
            <span class="tile-desc">Calificaciones ordinarias</span>
        </span>
    </a>
    <a href="verRetos.php" class="tile card-soft" style="--tint:var(--verde); text-decoration:none; border:1px solid var(--tint);">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-flag" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">Retos</span>
            <span class="tile-desc">Evaluación por retos</span>
        </span>
    </a>
    <a href="verTFG.php" class="tile card-soft" style="--tint:var(--naranja); text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-graduation-cap" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">TFG</span>
            <span class="tile-desc">Trabajos de fin de grado</span>
        </span>
    </a>
</div>

<div class="panel">
    <form method="GET" action="verRetos.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Nivel:</label>
            <select name="idNivel" onchange="this.form.submit()">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $n) { ?>
                    <option value="<?= (int)$n['idNivel'] ?>" <?= ((int)$n['idNivel'] === $idNivelFiltro) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($n['nombreNivel']) ?>
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
                </tr>
            </thead>
            <tbody>
                <?php if (empty($idRetoElegido)) { ?>
                    <tr>
                        <td colspan="3" class="vacio">Seleccione un ciclo y un reto para ver los estudiantes.</td>
                    </tr>
                <?php } elseif (empty($listaEstudiantes)) { ?>
                    <tr>
                        <td colspan="3" class="vacio">No hay estudiantes registrados en este ciclo.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEstudiantes as $est) {
                        $notaActual = $notasPorEstudiante[$est['idEstudiante']] ?? '';
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($est['nombreEstudiante']) ?></td>
                        <td><?= Security::escapeHtml($est['nombreCiclo']) ?></td>
                        <td>
                            <?php if ($notaActual !== '') { ?>
                                <span class="texto-negrita <?= $notaActual >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= Security::escapeHtml((string)$notaActual) ?>
                                </span>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
