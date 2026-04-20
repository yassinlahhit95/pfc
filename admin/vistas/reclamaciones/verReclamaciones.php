<?php
session_start();
$titulo_pagina = "Gestión de Reclamaciones - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../../modelos/reclamaciones.php";

$listaReclamaciones = listarReclamaciones();

$mensajeExito = '';
if (isset($_SESSION['exito'])) {
    $mensajeExito = $_SESSION['exito'];
}

$mensajeError = '';
if (isset($_SESSION['error'])) {
    $mensajeError = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Gestión de Reclamaciones</h1>
    </div>
    <div class="acciones-pagina">
        <a href="/pfc/admin/vistas/reclamaciones/agregarReclamacion.php" class="boton-primario">
            <i class="fas fa-plus"></i> Nueva Reclamación
        </a>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>
<?php if ($mensajeError) { ?>
    <div class="mensaje-error"><?php echo $mensajeError; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Profesor</th>
                    <th>Asunto</th>
                    <th>Gravedad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaReclamaciones)) { ?>
                    <tr><td colspan="8" class="sin-datos">No hay reclamaciones registradas</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaReclamaciones as $rec) { ?>
                    <tr>
                        <td><?php echo $rec['idReclamacion']; ?></td>
                        <td><strong><?php echo $rec['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $rec['nombreProfesor']; ?></td>
                        <td><?php echo $rec['asunto']; ?></td>
                        <td>
                            <span class="etiqueta-gravedad <?php echo $rec['gravedad']; ?>">
                                <?php echo ucfirst($rec['gravedad']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $claseBolita = 'inactivo-rojo';
                            if ($rec['estadoReclamacion'] == 'atendido') {
                                $claseBolita = 'activo-verde';
                            }
                            ?>
                            <span class="estado-bolita <?php echo $claseBolita; ?>">
                                <?php echo ucfirst($rec['estadoReclamacion']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($rec['fecha'])); ?></td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/admin/vistas/reclamaciones/agregarReclamacion.php?id=<?php echo $rec['idReclamacion']; ?>" 
                                   class="boton-icono boton-editar" title="Ver / Editar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="/pfc/admin/controladores/reclamaciones/borrar.php" class="d-inline" onsubmit="return confirm('¿Borrar reclamación?');">
                                    <input type="hidden" name="idReclamacion" value="<?php echo $rec['idReclamacion']; ?>">
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
</div>

<?php include '../comunes/footer.php'; ?>