<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$tfgs = listarTodosLosTFGs();

$tituloDelPagina = "Gestión TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Gestión de Trabajos Fin de Grado</h1>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Título del TFG</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tfgs) { ?>
                    <?php foreach ($tfgs as $tfg) { ?>
                        <tr>
                            <td class="texto-negrita"><?php echo $tfg['nombreEstudiante']; ?></td>
                            <td><?php echo $tfg['tituloTFG']; ?></td>
                            <td>
                                <a href="/pfc/admin/uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            </td>
                            <td>
                                <a href="vistas/tfg/editar.php?id=<?php echo $tfg['idEstudiante']; ?>" class="enlace-icono azul"><i class="fas fa-edit"></i></a>
                                <a href="controladores/tfg/borrar.php?id=<?php echo $tfg['idEstudiante']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="sin-datos">No hay TFGs subidos todavía.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>