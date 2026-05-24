<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$mis_ciclos = listarCiclosDeProfesor($idProfesor);
$listaEvaluacion = listarEvaluacionTFGporProfesor($idProfesor, $idCicloElegido);

$titulo_pagina = "AULAPRO | EVALUACIÓN TFG";
$seccion = 'notas_tfg';
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
                <?php foreach ($mis_ciclos as $c) { ?>
                    <option value="<?= $c['idCiclo'] ?>" <?= ($idCicloElegido == $c['idCiclo']) ? 'selected' : '' ?>>
                        [<?= $c['nombreNivel'] ?>] <?= $c['nombreCiclo'] ?>
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
                    <?php foreach ($listaEvaluacion as $item) { ?>
                        <tr>
                            <td><?= $item['nombreEstudiante'] ?></td>
                            <td><?= $item['abreviaturaCiclo'] ?></td>
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
                                    <a href="../../../public/uploads/pfc/<?= $item['archivoTFG'] ?>" target="_blank" class="btn-accion btn-ver">
                                        <i class="fas fa-file-pdf"></i> Descargar
                                    </a>
                                <?php } else { ?>
                                    <span class="texto-suave">---</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($item['nota'] !== null) { ?>
                                    <span class="texto-negrita <?= $item['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>">
                                        <?= $item['nota'] ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="texto-suave">---</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="evaluarTFG.php?idEstudiante=<?= $item['idEstudiante'] ?>" class="btn-accion btn-editar">
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
