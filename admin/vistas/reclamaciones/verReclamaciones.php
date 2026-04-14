<?php
session_start();
$titulo_pagina = "Reclamaciones - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/reclamaciones.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloReclamacion = new reclamacion($conexionBD);

$listaReclamaciones = $modeloReclamacion->listarReclamacionesModelo();

$mensajeExito = $_SESSION['exito'] ?? '';
$mensajeError = $_SESSION['error'] ?? '';
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <div>
        <h1>Reclamaciones e Incidencias</h1>
        <p class="texto-atenuado">Seguimiento de reportes de profesores y alumnos</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/reclamaciones/agregarReclamacion.php" class="boton-azul">
            <i class="fas fa-plus"></i> Nueva Reclamación
        </a>
    </div>
</div>

<?php if ($mensajeExito) { ?>
    <div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?php echo $mensajeExito; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaReclamaciones">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario / Emisor</th>
                    <th>Descripción / Asunto</th>
                    <th class="ancho-fijo-300">Estado de Gestión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaReclamaciones)) { ?>
                    <tr><td colspan="5" class="sin-datos">No hay reclamaciones activas.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaReclamaciones as $reclamacion) { 
                        $claseFila = ($reclamacion['estadoReclamacion'] == 'resuelta') ? 'opacity-6' : '';
                    ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td><?php echo $reclamacion['idReclamacion']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($reclamacion['nombreEstudiante']); ?></strong><br>
                            <small class="texto-atenuado">Reportado por: <?php echo htmlspecialchars($reclamacion['nombreProfesor']); ?></small><br>
                            <small class="texto-atenuado"><?php echo $reclamacion['fecha']; ?></small>
                        </td>
                        <td class="texto-pequeno texto-gray lh-1-4">
                            <strong><?php echo htmlspecialchars($reclamacion['asunto']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($reclamacion['descripcion'])); ?>
                        </td>
                        <td>
                            <form action="controlador/reclamacionesControlador.php" method="POST" class="disposicion-flexible direccion-columna separacion-pequena">
                                <input type="hidden" name="accion" value="cambiar_estado">
                                <input type="hidden" name="idReclamacion" value="<?php echo $reclamacion['idReclamacion']; ?>">
                                
                                <label class="disposicion-flexible alinear-centro separacion-pequena cursor-pointer color-warning texto-pequeno texto-negrita">
                                    <input type="radio" name="nuevo_estado" value="pendiente" 
                                           <?php if($reclamacion['estadoReclamacion'] == 'pendiente') echo 'checked'; ?>
                                           onchange="this.form.submit()"> Pendiente
                                </label>

                                <label class="disposicion-flexible alinear-centro separacion-pequena cursor-pointer color-success texto-pequeno texto-negrita">
                                    <input type="radio" name="nuevo_estado" value="resuelta" 
                                           <?php if($reclamacion['estadoReclamacion'] == 'resuelta') echo 'checked'; ?>
                                           onchange="this.form.submit()"> Resuelta
                                </label>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="controlador/reclamacionesControlador.php" class="d-inline" onsubmit="return confirm('¿Eliminar reporte?');">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="idReclamacion" value="<?php echo $reclamacion['idReclamacion']; ?>">
                                <button type="submit" class="boton-icono boton-eliminar" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
