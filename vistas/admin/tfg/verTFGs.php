<?php
session_start();
$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once "../comunes/nav.php";

require_once "../../../modelos/tfg.php";

$todos_los_tfgs = listarTFGs();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$mensaje_exito = "";
if (isset($_SESSION['exito'])) { $mensaje_exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Gestión de Trabajos Fin de Grado</h1>
</div>

<?php if ($mensaje_exito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensaje_exito; ?></div>
<?php } ?>
<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Título del Proyecto</th>
                    <th>Archivo</th>
                    <th>Fecha Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todos_los_tfgs)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay TFGs registrados</td></tr>
                <?php } else { ?>
                    <?php foreach ($todos_los_tfgs as $tfg) { ?>
                    <tr>
                        <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $tfg['tituloTFG']; ?></td>
                        <td>
                            <a href="/pfc/public/uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario boton-pequeno">
                                <i class="fas fa-file-pdf"></i> Ver PDF
                            </a>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($tfg['fechaSubida'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <form action="/pfc/controladores/admin/tfg/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar permanentemente este TFG?')">
                                    <input type="hidden" name="idTFG" value="<?php echo $tfg['idTFG']; ?>">
                                    <input type="hidden" name="nombreArchivo" value="<?php echo $tfg['archivoTFG']; ?>">
                                    <button type="submit" class="boton-icono boton-eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
