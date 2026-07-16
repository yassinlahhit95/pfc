<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";

$listaCiclos  = listarTodosLosCiclos();
$listaNiveles = listarNiveles();

$idNivelFiltro = (int)($_GET['idNivel'] ?? 0);
$ciclosFiltrados = $idNivelFiltro
    ? array_values(array_filter($listaCiclos, fn($c) => (int)$c['idNivel'] === $idNivelFiltro))
    : $listaCiclos;

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);

if ($idNivelFiltro && $idCicloElegido && !in_array((int)$idCicloElegido, array_column($ciclosFiltrados, 'idCiclo'))) {
    $idCicloElegido = '';
}

$listaEvaluacion = listarEvaluacionTFG($idCicloElegido ?: null);

$titulo_pagina = "AULAPRO | NOTAS TFG";
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
    <a href="verRetos.php" class="tile card-soft" style="--tint:var(--verde); text-decoration:none">
        <span class="tile-sheen"></span>
        <span class="tile-ico">
            <i class="fas fa-flag" style="font-size:1.5rem"></i>
        </span>
        <span class="tile-body">
            <span class="tile-title">Retos</span>
            <span class="tile-desc">Evaluación por retos</span>
        </span>
    </a>
    <a href="verTFG.php" class="tile card-soft" style="--tint:var(--naranja); text-decoration:none; border:1px solid var(--tint);">
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
    <form method="GET" action="verTFG.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Nivel:</label>
            <select name="idNivel" onchange="document.getElementById('selectCicloTFG').value=''; this.form.submit()">
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
            <select name="idCiclo" id="selectCicloTFG" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($ciclosFiltrados as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['nombreNivel']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
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
                    <th>Año</th>
                    <th>Estado</th>
                    <th>Fecha Subida</th>
                    <th>Archivo PDF</th>
                    <th>Nota TFG</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEvaluacion)) { ?>
                    <tr>
                        <td colspan="7" class="vacio">No hay estudiantes registrados.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $evaluacion) { ?>
                    <tr>
                        <td><?= Security::escapeHtml($evaluacion['nombreEstudiante']) ?></td>
                        <td><?= Security::escapeHtml($evaluacion['abreviaturaCiclo']) ?></td>
                        <td><?= !empty($evaluacion['anioEstudio']) ? '<span class="texto-estado azul">' . Security::escapeHtml($evaluacion['anioEstudio']) . '</span>' : '<span class="texto-suave">—</span>' ?></td>
                        <td>
                            <?php if (!empty($evaluacion['archivoTFG'])) { ?>
                                <span class="indicador-estado activo-verde">ENTREGADO</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">PENDIENTE</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($evaluacion['fechaSubidaTFG'])) { ?>
                                <?= date('d/m/Y', strtotime($evaluacion['fechaSubidaTFG'])) ?>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($evaluacion['archivoTFG'])) { ?>
                                <a href="../../../controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($evaluacion['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="btn-accion btn-ver">
                                    <i class="fas fa-file-pdf"></i> Descargar
                                </a>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($evaluacion['nota'] !== null) { ?>
                                <span class="texto-negrita <?= $evaluacion['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= Security::escapeHtml($evaluacion['nota']) ?>
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
