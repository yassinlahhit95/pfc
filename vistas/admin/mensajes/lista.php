<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$listaDeMensajes = listarTodosLosMensajes();

$titulo_pagina = "Gestión de Mensajería - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$exito = "";
if (isset($_SESSION['exito'])) { $exito = $_SESSION['exito']; }

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>Buzón Central de Mensajes</h1>
    <a href="/pfc/vistas/admin/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> Redactar Mensaje
    </a>
</div>

<?php if ($exito != "") { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error != "") { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaMensajes">
            <thead>
                <tr>
                    <th>Emisor</th>
                    <th>Destinatario</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="6" class="sin-datos">No hay mensajes registrados.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { ?>
                    <tr>
                        <td><strong><?php echo $mensaje['nombreEstudiante']; ?></strong></td>
                        <td><?php echo $mensaje['nombreProfesor'] ?: 'Dirección (Admin)'; ?></td>
                        <td>
                            <p class="texto-negrita"><?php echo $mensaje['asunto']; ?></p>
                            <small class="texto-atenuado"><?php echo substr($mensaje['descripcion'], 0, 40); ?>...</small>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">Leído</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">Nuevo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="/pfc/vistas/admin/mensajes/detalles.php?id=<?php echo $mensaje['idReclamacion']; ?>" class="btn-accion btn-ver" title="Ver y Gestionar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="/pfc/controladores/admin/mensajes/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este mensaje?')">
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
