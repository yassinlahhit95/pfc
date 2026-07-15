<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

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

$titulo_pagina = "AULAPRO | GESTIÓN TFG";
$seccion = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>GESTIÓN Y EVALUACIÓN DE TFGs</h1>
</div>

<div class="panel">
    <form method="GET" action="calificacionesTFG.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Nivel:</label>
            <select name="idNivel" onchange="document.getElementById('selectCicloTFG').value=''; this.form.submit()">
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

    <div class="campo relleno" style="margin-top:12px;">
        <label>Filtrar por Año:</label>
        <select id="filtroAnioTFG" onchange="filtrarTFGPorAnio()">
            <option value="">-- Todos los Años --</option>
            <option value="1º">1º Año</option>
            <option value="2º">2º Año</option>
        </select>
    </div>
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
                    <th>Evaluar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaEvaluacion)) { ?>
                    <tr>
                        <td colspan="8" class="vacio">No hay estudiantes registrados.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $item) { ?>
                    <tr class="fila-anio-tfg" data-anio="<?= Security::escapeHtml($item['anioEstudio'] ?? '') ?>">
                        <td><?= Security::escapeHtml($item['nombreEstudiante']) ?></td>
                        <td><?= Security::escapeHtml($item['abreviaturaCiclo']) ?></td>
                        <td><?= !empty($item['anioEstudio']) ? '<span class="texto-estado azul">' . Security::escapeHtml($item['anioEstudio']) . '</span>' : '<span class="texto-suave">—</span>' ?></td>
                        <td>
                            <?php if (!empty($item['archivoTFG'])) { ?>
                                <span class="indicador-estado activo-verde">ENTREGADO</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">PENDIENTE</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($item['fechaSubidaTFG'])) { ?>
                                <?= date('d/m/Y', strtotime($item['fechaSubidaTFG'])) ?>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if (!empty($item['archivoTFG'])) { ?>
                                <a href="../../../controladores/comunes/verTFG.php?id=<?= Security::escapeHtml($item['idEstudiante'] ) ?>&modo=descarga" target="_blank" class="btn-accion btn-ver">
                                    <i class="fas fa-file-pdf"></i> Descargar
                                </a>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($item['nota'] !== null) { ?>
                                <span class="texto-negrita <?= $item['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                    <?= Security::escapeHtml($item['nota']) ?>
                                </span>
                            <?php } else { ?>
                                <span class="texto-suave">---</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="evaluarTFG.php?idEstudiante=<?= Security::escapeHtml($item['idEstudiante']) ?>" class="btn-accion btn-editar">
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
<script>
function filtrarTFGPorAnio() {
    var anio = document.getElementById('filtroAnioTFG').value;
    document.querySelectorAll('.fila-anio-tfg').forEach(function(fila) {
        fila.style.display = (anio === '' || fila.getAttribute('data-anio') === anio) ? '' : 'none';
    });
}
</script>

