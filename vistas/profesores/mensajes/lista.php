<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfesor = $_SESSION['idProfesor'];
// Obtenemos los mensajes donde el profesor es emisor o destinatario
$listaDeMensajes = listarMensajesParaProfesor($idProfesor);
// Nota: listarMensajesParaProfesor en el modelo actual solo saca los recibidos. 
// Vamos a unificar para que vea ambos en la misma tabla como admin.

$tituloDelPagina = "BuzÃ³n de Mensajes - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>BuzÃ³n de Mensajes</h1>
    <a href="/pfc/vistas/profesores/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> Redactar Mensaje
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Emisor / Receptor</th>
                    <th>Ciclo</th>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                    <th>Fecha y Hora</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="7" class="sin-datos">No hay mensajes registrados aÃºn.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $esMio = ($mensaje['emisor_rol'] == 'profesor');
                        $claseFila = $esMio ? 'fila-propia' : '';
                    ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td>
                            <strong><?php echo $esMio ? 'TÃº (Profesor)' : $mensaje['nombreEstudiante']; ?></strong>
                        </td>
                        <td><?php echo $mensaje['nombreCiclo'] ?: '-'; ?></td>
                        <td><p class="texto-negrita"><?php echo strtoupper($mensaje['asunto']); ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?php echo substr($mensaje['descripcion'], 0, 40); ?>...
                            </div>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?><br>
                            <small class="texto-atenuado"><?php echo date('H:i:s', strtotime($mensaje['fecha'])); ?></small>
                        </td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">LeÃ­do</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">Nuevo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/profesores/mensajes/detalles.php?id=<?php echo $mensaje['idReclamacion']; ?>" class="btn-accion btn-ver" title="Ver mensaje">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="/pfc/controladores/profesores/mensajes/borrar.php" method="POST" onsubmit="return confirm('Â¿Eliminar este mensaje?')">
                                    <input type="hidden" name="idReclamacion" value="<?php echo $mensaje['idReclamacion']; ?>">
                                    <button type="submit" class="btn-accion btn-eliminar">
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
