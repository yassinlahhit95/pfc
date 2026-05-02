<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$listaDeMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "Mis Mensajes - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS MENSAJES</h1>
    <a href="agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO MENSAJE
    </a>
</div>

<?php if ($error) : ?>
    <div class="alerta-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito) : ?>
    <div class="alerta-exito"><?= $exito ?></div>
<?php endif; ?>

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
                <?php if (empty($listaDeMensajes)) : ?>
                    <tr><td colspan="6" class="sin-datos">No has enviado mensajes aún.</td></tr>
                <?php else : ?>
                    <?php foreach ($listaDeMensajes as $mensaje) : 
                        $claseFila = ($mensaje['emisor_rol'] == 'estudiante') ? 'fila-propia' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td>
                            <strong><?php 
                                if ($mensaje['emisor_rol'] == 'profesor') {
                                    echo '(PROFESOR) ' . $mensaje['nombreProfesor']; 
                                } else {
                                    echo ($mensaje['nombreProfesor']) ? '(PROFESOR) ' . $mensaje['nombreProfesor'] : 'DIRECCIÓN (ADMIN)';
                                }
                            ?></strong>
                        </td>
                        <td><p class="texto-negrita"><?= strtoupper($mensaje['asunto']) ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?= substr($mensaje['descripcion'], 0, 80) ?>...
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></td>
                        <td>
                            <?php if ($mensaje['leido']) : ?>
                                <span class="estado-bolita activo-verde">VISTO</span>
                            <?php else : ?>
                                <span class="estado-bolita inactivo-rojo">ENVIADO</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="detalles.php?id=<?= $mensaje['idReclamacion'] ?>" class="btn-accion btn-ver" title="Leer mensaje completo">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
