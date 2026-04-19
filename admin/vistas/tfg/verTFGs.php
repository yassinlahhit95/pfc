<?php
session_start();
$titulo_pagina = "Gestión de TFGs - Super Admin";
$seccion = 'tfg';
include_once "../comunes/nav.php";

require_once "../../modelos/tfg.php";
require_once "../../modelos/estudiantes.php";

$listaTFGs = listarTodosLosTFGs();

$exito = $_SESSION['exito'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Proyectos TFG</h1>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<!-- Listado de Proyectos subidos (Modo Lectura para Admin) -->
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Proyectos Entregados por Alumnos</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Título del TFG</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaTFGs)) { ?>
                    <tr><td colspan="3" class="sin-datos">No hay TFGs subidos actualmente por los estudiantes.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaTFGs as $tfg) { ?>
                    <tr>
                        <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $tfg['tituloTFG'] ? $tfg['tituloTFG'] : 'Sin título definido'; ?></td>
                        <td>
                            <a href="uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                                <i class="fas fa-file-pdf"></i> Descargar / Ver PDF
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
