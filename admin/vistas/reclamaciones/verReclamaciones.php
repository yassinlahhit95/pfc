<?php
session_start();
$titulo_pagina = "Reclamaciones - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../modelos/reclamaciones.php";

$listaReclamaciones = listarReclamaciones();

$mensajeExito = $_SESSION['exito'] ?? '';
$mensajeError = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Reclamaciones e Incidencias</h1>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/reclamaciones/agregarReclamacion.php" class="boton-primario">
            Nueva Reclamación
        </a>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><p><?php echo $mensajeExito; ?></p></div>
<?php } ?>
<?php if ($mensajeError) { ?>
    <div class="mensaje-error"><p><?php echo $mensajeError; ?></p></div>
<?php } ?>

<div class="contenedor-tabla">
    <table class="tabla-datos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Estudiante</th>
                <th>Profesor</th>
                <th>Asunto</th>
                <th>Gravedad</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaReclamaciones)) { ?>
                <tr><td colspan="7" class="sin-datos">No hay reclamaciones registradas</td></tr>
            <?php } else { ?>
                <?php foreach ($listaReclamaciones as $reclamacion) { 
                    $claseGravedad = '';
                    if ($reclamacion['gravedad'] == 'leve') $claseGravedad = 'activo-verde';
                    if ($reclamacion['gravedad'] == 'grave') $claseGravedad = 'inactivo-rojo';
                    if ($reclamacion['gravedad'] == 'muy grave') $claseGravedad = 'inactivo-rojo';
                ?>
                <tr>
                    <td><?php echo $reclamacion['idReclamacion']; ?></td>
                    <td><strong><?php echo $reclamacion['nombreEstudiante']; ?></strong></td>
                    <td><?php echo $reclamacion['nombreProfesor']; ?></td>
                    <td><?php echo $reclamacion['asunto']; ?></td>
                    <td>
                        <span class="estado-bolita <?php echo $claseGravedad; ?>">
                            <?php echo ucfirst($reclamacion['gravedad']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($reclamacion['fecha'])); ?></td>
                    <td>
                        <div class="botones-accion">
                            <form action="controladores/reclamaciones/borrar.php" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta reclamación?');">
                                <input type="hidden" name="idReclamacion" value="<?php echo $reclamacion['idReclamacion']; ?>">
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

<?php include '../comunes/footer.php'; ?>
