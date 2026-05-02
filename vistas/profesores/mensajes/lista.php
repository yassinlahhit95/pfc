<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
unset($_SESSION['error'], $_SESSION['exito']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfesor = $_SESSION['idProfesor'];
// Obtenemos los mensajes donde el profesor es emisor o destinatario
$listaDeMensajes = listarMensajesParaProfesor($idProfesor);
// Nota: listarMensajesParaProfesor en el modelo actual solo saca los recibidos. 
// Vamos a unificar para que vea ambos en la misma tabla como admin.

$tituloDelPagina = "Buzón de Mensajes - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Buzón de Mensajes</h1>
    <a href="agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> Redactar Mensaje
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
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
                    <tr><td colspan="7" class="sin-datos">No hay mensajes registrados aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $esMio = ($mensaje['emisor_rol'] == 'profesor');
                        $claseFila = $esMio ? 'fila-propia' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td>
                            <strong><?= $esMio ? 'Tú (Profesor)' : $mensaje['nombreEstudiante'] ?></strong>
                        </td>
                        <td><?= $mensaje['nombreCiclo'] ?: '-' ?></td>
                        <td><p class="texto-negrita"><?= strtoupper($mensaje['asunto']) ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?= substr($mensaje['descripcion'], 0, 40) ?>...
                            </div>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($mensaje['fecha'])) ?><br>
                            <small class="texto-atenuado"><?= date('H:i:s', strtotime($mensaje['fecha'])) ?></small>
                        </td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">Leído</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">Nuevo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="detalles.php?id=<?= $mensaje['idReclamacion'] ?>" class="btn-accion btn-ver" title="Ver mensaje">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="../../../controladores/profesores/mensajes/borrar.php" method="POST" onsubmit="return confirm('¿Eliminar este mensaje?')">
                                    <input type="hidden" name="idReclamacion" value="<?= $mensaje['idReclamacion'] ?>">
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


