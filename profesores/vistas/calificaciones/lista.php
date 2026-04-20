<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";

$idProfesor = $_SESSION['idProfesor'];
$calificaciones = listarCalificacionesPorProfesor($idProfesor);

$tituloDelPagina = "Calificaciones - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Notas de Alumnos</h1>
    <a href="/pfc/profesores/vistas/calificaciones/agregar.php" class="boton-primario">Asignar Nota</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Módulo</th>
                    <th>1ª Ev</th>
                    <th>1ª Final</th>
                    <th>2ª Ev</th>
                    <th>2ª Final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($calificaciones) { ?>
                    <?php foreach ($calificaciones as $nota) { ?>
                        <tr>
                            <td><?php echo $nota['nombreEstudiante']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nombreModulo']; ?></td>
                            <td><?php echo $nota['nota_1ev']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nota_1final']; ?></td>
                            <td><?php echo $nota['nota_2ev']; ?></td>
                            <td class="texto-negrita"><?php echo $nota['nota_2final']; ?></td>
                            <td>
                                <a href="/pfc/profesores/vistas/calificaciones/editar.php?id=<?php echo $nota['idCalificacion']; ?>" class="enlace-icono azul"><i class="fas fa-edit"></i></a>
                                <a href="/pfc/profesores/controladores/calificaciones/borrar.php?id=<?php echo $nota['idCalificacion']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
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