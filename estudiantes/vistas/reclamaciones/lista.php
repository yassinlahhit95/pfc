<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id = $_SESSION['idEstudiante'];
$reclamaciones = listarReclamacionesPorEstudiante($id);

$tituloDelPagina = "Mis Reclamaciones - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mis Reclamaciones</h1>
    <a href="vistas/reclamaciones/agregar.php" class="boton-primario">Nueva Reclamación</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Historial de Reclamaciones</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Asunto</th>
                    <th>Profesor</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Gravedad</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reclamaciones) { ?>
                    <?php foreach ($reclamaciones as $rec) { ?>
                        <tr>
                            <td class="texto-negrita"><?php echo $rec['asunto']; ?></td>
                            <td><?php echo $rec['nombreProfesor']; ?></td>
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
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="sin-datos">
                            <i class="fas fa-exclamation-triangle"></i> No has realizado ninguna reclamación.
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>