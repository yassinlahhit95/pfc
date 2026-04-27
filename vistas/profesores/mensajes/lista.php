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
                    <th>Estudiante</th>
                    <th>Asunto y Mensaje</th>
                    <th>Fecha</th>
                    <th>Visto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="5" class="sin-datos">No has recibido mensajes aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { ?>
                    <tr>
                        <td><strong><?php echo $mensaje['nombreEstudiante']; ?></strong></td>
                        <td>
                            <p class="texto-negrita"><?php echo $mensaje['asunto']; ?></p>
                            <p class="texto-atenuado texto-pequeno"><?php echo $mensaje['descripcion']; ?></p>
                            <?php if (!empty($mensaje['respuesta'])) { ?>
                                <div class="tarjeta-gris-suave mt-5">
                                    <small><strong>Tu Respuesta:</strong> <?php echo $mensaje['respuesta']; ?></small>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></td>
                        <td class="center-text">
                            <?php if ($mensaje['leido']) { ?>
                                <i class="fas fa-check-double color-primario" title="Leído"></i>
                            <?php } else { ?>
                                <i class="fas fa-envelope texto-rojo" title="Nuevo"></i>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/profesores/mensajes/editar.php?id=<?php echo $mensaje['idReclamacion']; ?>" class="btn-accion btn-ver" title="Responder o Marcar Leído">
                                    <i class="fas fa-reply"></i>
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

