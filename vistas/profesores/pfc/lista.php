<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$tfgs = listarTodosLosTFGs();

$tituloDelPagina = "Gestión de TFGs - Portal Profesores";
$seccionActual = 'tfg';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Gestión de TFGs Entregados</h1>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Ciclo</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tfgs) { ?>
                    <?php foreach ($tfgs as $tfg) { ?>
                        <tr>
                            <td><strong><?php echo $tfg['nombreEstudiante']; ?></strong></td>
                            <td><?php echo $tfg['nombreCiclo']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])); ?></td>
                            <td>
                                <a href="/pfc/public/uploads/pfc/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="enlace-icono azul"><i class="fas fa-download"></i></a>
                                <a href="/pfc/controladores/profesores/pfc/borrar.php?id=<?php echo $tfg['idEstudiante']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
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

