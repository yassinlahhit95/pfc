<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);
$misCiclos = listarCiclosDeProfesor($idProfesor);
$listaEvaluacion = listarEvaluacionTFGporProfesor($idProfesor, $idCicloElegido);

$tituloDelPagina = "AULAPRO | EVALUACIÓN TFG";
$seccionActual = 'notas_tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EVALUACIÓN DE TFGS (ALUMNOS ASIGNADOS)</h1>
</div>

<div class="panel">
    <form method="GET" action="tfg.php" class="caja alinear-centro espacio-grande caja-libre">
        <div class="campo relleno">
            <label>Filtrar por Ciclo:</label>
            <select name="idCiclo" id="selectCicloTFG" onchange="this.form.submit()">
                <option value="">-- Todos mis Ciclos --</option>
                <?php foreach ($misCiclos as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo'] ) ?>" <?= Security::escapeHtml(($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '') ?>>
                        [<?= Security::escapeHtml($ciclo['nombreNivel'] ) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo'] ) ?>
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
                        <td colspan="7" class="vacio">No hay estudiantes asignados en estos ciclos.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaEvaluacion as $evaluacion) { ?>
                        <tr>
                            <td><?= Security::escapeHtml($evaluacion['nombreEstudiante'] ) ?></td>
                            <td><?= Security::escapeHtml($evaluacion['abreviaturaCiclo'] ) ?></td>
                            <td>
                                <?php if (!empty($evaluacion['archivoTFG'])) { ?>
                                    <span class="indicador-estado activo-verde">ENTREGADO</span>
                                <?php } else { ?>
                                    <span class="indicador-estado inactivo-rojo">PENDIENTE</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if (!empty($evaluacion['fechaSubidaTFG'])) { ?>
                                    <?= Security::escapeHtml(date('d/m/Y', strtotime($evaluacion['fechaSubidaTFG']))) ?>
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
                                    <span class="texto-negrita <?= Security::escapeHtml($evaluacion['nota'] >= 5 ? 'texto-verde' : 'texto-rojo') ?>">
                                        <?= Security::escapeHtml($evaluacion['nota'] ) ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="texto-suave">---</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="evaluarTFG.php?idEstudiante=<?= Security::escapeHtml($evaluacion['idEstudiante'] ) ?>" class="btn-accion btn-editar">
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>
