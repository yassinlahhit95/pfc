<?php
session_start();
$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once "../comunes/nav.php";

require_once "../../../modelos/tfg.php";
require_once "../../../modelos/ciclos.php";

$todos_los_tfgs = listarTodosLosTFGs();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Trabajos Fin de Grado</h1>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <div class="campo-formulario">
        <label><i class="fas fa-filter"></i> FILTRAR POR CICLO:</label>
        <select id="selectFiltroCicloTFG" onchange="filtrarTabla('selectFiltroCicloTFG', 'tablaTFGs')">
            <option value="">-- Todos los Ciclos --</option>
            <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                <option value="<?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>">
                    <?php echo strtoupper($cicloFiltro['nombreCiclo']); ?>
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
                        <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $tfg['nombreCiclo']; ?></td>
                        <td>
                            <a href="/pfc/public/uploads/pfc/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario boton-pequeno" download="<?php echo $nombreDescarga; ?>">
                                <i class="fas fa-file-pdf"></i> Descargar PDF
                            </a>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($tfg['fechaSubidaTFG'])); ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

