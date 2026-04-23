<?php
session_start();
$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once "../comunes/nav.php";

require_once "../../../modelos/tfg.php";

$todos_los_tfgs = listarTodosLosTFGs();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Trabajos Fin de Grado</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
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
                    <?php foreach ($todos_los_tfgs as $tfg) { ?>
                    <tr>
                        <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $tfg['nombreCiclo']; ?></td>
                        <td>
                            <a href="/pfc/public/uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario boton-pequeno">
                                <i class="fas fa-file-pdf"></i> Ver PDF
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
