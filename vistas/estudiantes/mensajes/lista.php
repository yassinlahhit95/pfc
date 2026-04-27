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
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Visto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="5" class="sin-datos">No has enviado mensajes aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { ?>
                    <tr>
                        <td>
                            <strong><?php echo $mensaje['nombreProfesor'] ?: 'Dirección (Admin)'; ?></strong>
                        </td>
                        <td>
                            <p class="texto-negrita"><?php echo $mensaje['asunto']; ?></p>
                            <small class="texto-atenuado"><?php echo substr($mensaje['descripcion'], 0, 50); ?>...</small>
                            <?php if (!empty($mensaje['respuesta'])) { ?>
                                <div class="tarjeta-gris-suave mt-5">
                                    <small><strong>Respuesta:</strong> <?php echo $mensaje['respuesta']; ?></small>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></td>
                        <td>
                            <span class="estado-bolita <?php echo ($mensaje['estadoReclamacion'] == 'atendido' ? 'activo-verde' : 'inactivo-rojo'); ?>">
                                <?php echo ucfirst($mensaje['estadoReclamacion']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <i class="fas fa-check-double color-primario" title="Leído por el destinatario"></i>
                            <?php } else { ?>
                                <i class="fas fa-check texto-atenuado" title="Enviado"></i>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
