<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$reclamaciones = listarReclamaciones();

$tituloDelPagina = "Reclamaciones - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Gestión de Reclamaciones</h1>
    <a href="vistas/reclamaciones/agregar.php" class="boton-primario">Nueva Reclamación</a>
</div>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Gravedad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reclamaciones) { ?>
                    <?php foreach ($reclamaciones as $rec) { ?>
                        <tr>
                            <td><?php echo $rec['nombreEstudiante']; ?></td>
                            <td class="texto-negrita"><?php echo $rec['asunto']; ?></td>
                            <td><?php echo $rec['fecha']; ?></td>
                            <td>
                                <?php 
                                $claseEstado = 'naranja';
                                if ($rec['estadoReclamacion'] == 'atendido') {
                                    $claseEstado = 'verde';
                                }
                                ?>
                                <span class="etiqueta-estado <?php echo $claseEstado; ?>">
                                    <?php echo $rec['estadoReclamacion']; ?>
                                </span>
                            </td>
                            <td><?php echo $rec['gravedad']; ?></td>
                            <td>
                                <a href="vistas/reclamaciones/editar.php?id=<?php echo $rec['idReclamacion']; ?>" class="enlace-icono azul"><i class="fas fa-edit"></i></a>
                                <a href="controladores/reclamaciones/borrar.php?id=<?php echo $rec['idReclamacion']; ?>" class="enlace-icono rojo"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">No hay reclamaciones registradas.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>