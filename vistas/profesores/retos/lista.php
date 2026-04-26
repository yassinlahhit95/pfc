<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$retos = obtenerRetosDeProfesor($idProfesor);

$tituloDelPagina = "Retos - Portal Profesores";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Gestión de Retos</h1>
    <a href="/pfc/vistas/profesores/retos/agregar.php" class="boton-primario">Nuevo Reto</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Horas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($retos) { ?>
                    <?php foreach ($retos as $reto) { ?>
                        <tr>
                            <td class="texto-negrita"><?php echo $reto['nombreReto']; ?></td>
                            <td><?php echo $reto['fechaInicio']; ?></td>
                            <td><?php echo $reto['fechaFin']; ?></td>
                            <td><?php echo $reto['horasReto']; ?> h</td>
                            <td>
                                <a href="/pfc/vistas/profesores/retos/editar.php?id=<?php echo $reto['idReto']; ?>" class="enlace-icono azul"><i class="fas fa-edit"></i></a>
                                <a href="/pfc/controladores/profesores/retos/borrar.php?id=<?php echo $reto['idReto']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">No hay retos registrados.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

