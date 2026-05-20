<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$listaDeMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MENSAJERIA";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS MENSAJES</h1>
    <a href="../../../vistas/estudiantes/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO MENSAJE
    </a>
</div>

<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
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
                    <tr><td colspan="6" class="vacio">No has enviado mensajes aun.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $claseFila = ($mensaje['emisor_rol'] == 'estudiante') ? 'fila-propia' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td>
                            <b><?php 
                                if ($mensaje['emisor_rol'] == 'profesor') {
                                    echo '(PROFESOR) ' . $mensaje['nombreProfesor']; 
                                } else {
                                    echo ($mensaje['nombreProfesor']) ? '(PROFESOR) ' . $mensaje['nombreProfesor'] : 'DIRECCION (ADMIN)';
                                }
                            ?></b>
                        </td>
                        <td><p class="texto-negrita"><?= strtoupper($mensaje['asunto']) ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?= substr($mensaje['descripcion'], 0, 80) ?>...
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="indicador-estado activo-verde">VISTO</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">ENVIADO</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="../../../vistas/estudiantes/mensajes/detalles.php?id=<?= $mensaje['idReclamacion'] ?>" class="btn-accion btn-ver">
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>

