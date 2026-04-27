<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfesor = $_SESSION['idProfesor'];
$listaDeMensajes = listarMensajesParaProfesor($idProfesor);

$tituloDelPagina = "Buzón de Mensajes - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Buzón de Mensajes</h1>
    <a href="/pfc/vistas/profesores/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-paper-plane"></i> Redactar Nuevo
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
                    <th>Asunto</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay mensajes registrados aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        // Si el rol emisor es profesor, significa que lo envió el propio usuario actual
                        $claseFila = ($mensaje['emisor_rol'] == 'profesor') ? 'fila-propia' : '';
                    ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td>
                            <strong><?php echo ($mensaje['emisor_rol'] == 'profesor') ? ($mensaje['nombreEstudiante'] ?: 'Dirección (Admin)') : $mensaje['nombreEstudiante']; ?></strong>
                            <br><small class="texto-atenuado"><?php echo ($mensaje['emisor_rol'] == 'profesor') ? '(Enviado por ti)' : '(Recibido)'; ?></small>
                        </td>
                        <td><p class="texto-negrita"><?php echo strtoupper($mensaje['asunto']); ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?php echo nl2br($mensaje['descripcion']); ?>
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">LEÍDO</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">NUEVO</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/profesores/mensajes/detalles.php?id=<?php echo $mensaje['idReclamacion']; ?>" class="btn-accion btn-ver" title="Leer mensaje completo">
                                    <i class="fas fa-eye"></i>
                                </a>
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

