<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$listaDeMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "Mis Mensajes - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>MIS MENSAJES</h1>
    <a href="/pfc/vistas/estudiantes/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO MENSAJE
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
                    <th>Destinatario</th>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="6" class="sin-datos">No has enviado mensajes aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $claseFila = ($mensaje['emisor_rol'] == 'estudiante') ? 'fila-propia' : '';
                    ?>
                    <tr class="<?php echo $claseFila; ?>">
                        <td>
                            <strong><?php echo $mensaje['nombreProfesor'] ?: 'Dirección (Admin)'; ?></strong>
                        </td>
                        <td><p class="texto-negrita"><?php echo strtoupper($mensaje['asunto']); ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?php echo substr($mensaje['descripcion'], 0, 80); ?>...
                            </div>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">VISTO</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">ENVIADO</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/estudiantes/mensajes/detalles.php?id=<?php echo $mensaje['idReclamacion']; ?>" class="btn-accion btn-ver" title="Leer mensaje completo">
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
