<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$notas = listarCalificacionesGeneral();

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Notas de Módulos</h1>
    <a href="vistas/calificaciones/agregar.php" class="boton-primario">Asignar Nota</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Módulo</th>
                    <th>1ª Ev</th>
                    <th>1ª Fin</th>
                    <th>2ª Ev</th>
                    <th>2ª Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($notas) { ?>
                    <?php foreach ($notas as $nota) { ?>
                        <tr>
                            <td><?php echo $nota['nombreEstudiante']; ?></td>
                            <td><?php echo $nota['nombreModulo']; ?></td>
                            <td><?php echo $nota['nota_1ev']; ?></td>
                            <td><?php echo $nota['nota_1final']; ?></td>
                            <td><?php echo $nota['nota_2ev']; ?></td>
                            <td><?php echo $nota['nota_2final']; ?></td>
                            <td>
                                <a href="vistas/calificaciones/editar.php?id=<?php echo $nota['idCalificacion']; ?>" class="enlace-icono azul"><i class="fas fa-edit"></i></a>
                                <a href="controladores/calificaciones/borrar.php?id=<?php echo $nota['idCalificacion']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="sin-datos">No hay calificaciones registradas.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>