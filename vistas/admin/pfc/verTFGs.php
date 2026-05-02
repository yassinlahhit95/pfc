<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_tfgs = listarTodosLosTFGs();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Gestión de Trabajos Fin de Grado</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCicloTFG" onchange="filtrarTabla('selectFiltroCicloTFG', 'tablaTFGs')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?= strtoupper($cicloFiltro['nombreCiclo']) ?>">
                    <?= strtoupper($cicloFiltro['nombreCiclo']) ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaTFGs">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Archivo</th>
                    <th>Fecha Subida</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_tfgs)) { ?>
                    <tr><td colspan="4" class="sin-datos">No hay TFGs registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_tfgs as $tfg) { 
                        $nombreLimpio = str_replace(' ', '_', $tfg['nombreEstudiante']);
                        $nombreDescarga = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . ".pdf";
                    ?>
                    <tr>
                        <td><strong><?= $tfg['nombreEstudiante'] ?></strong></td>
                        <td><?= $tfg['nombreCiclo'] ?></td>
                        <td>
                            <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="boton-secundario boton-pequeno" download="<?= $nombreDescarga ?>">
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </a>
                        </td>
                        <td><?= date('d/m/Y', strtotime($tfg['fechaSubidaTFG'])) ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


