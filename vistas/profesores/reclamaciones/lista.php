<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfesor = $_SESSION['idProfesor'];
$reclamaciones = listarReclamacionesPorProfesor($idProfesor);

$tituloDelPagina = "Mis Reclamaciones - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Mis Reclamaciones Enviadas</h1>
    <a href="/pfc/vistas/profesores/reclamaciones/agregar.php" class="boton-primario">Nueva Reclamación</a>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reclamaciones) { ?>
                    <?php foreach ($reclamaciones as $rec) { ?>
                        <tr>
                            <td><?php echo $rec['nombreEstudiante']; ?></td>
                            <td class="texto-negrita"><?php echo $rec['asunto']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($rec['fecha'])); ?></td>
                            <td>
                                <?php 
                                $claseEstado = 'naranja';
                                if ($rec['estadoReclamacion'] == 'atendido') {
                                    $claseEstado = 'verde';
                                }
                                ?>
                                <span class="etiqueta-estado <?php echo $claseEstado; ?>">
                                    <?php echo ucfirst($rec['estadoReclamacion']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/pfc/vistas/profesores/reclamaciones/editar.php?id=<?php echo $rec['idReclamacion']; ?>" class="boton-icono boton-editar" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="/pfc/controladores/profesores/reclamaciones/borrar.php?id=<?php echo $rec['idReclamacion']; ?>" class="boton-icono boton-eliminar" title="Eliminar" onclick="return confirm('¿Seguro?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="sin-datos">Aún no has enviado ninguna reclamación.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
