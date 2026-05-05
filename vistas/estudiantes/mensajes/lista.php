<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$listaDeMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloPagina = "Mis Mensajes - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MIS MENSAJES</h1>
    <a href="../../../vistas/estudiantes/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO MENSAJE
    </a>
</div>

<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
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
                <?php if (empty($listaDeMensajes)) { ?>
                    <tr><td colspan="6" class="sin-datos">No has enviado mensajes aún.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $claseFila = ($mensaje['emisor_rol'] == 'estudiante') ? 'fila-propia' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td>
                            <strong><?php 
                                if ($mensaje['emisor_rol'] == 'profesor') {
                                    echo '(PROFESOR) ' . htmlspecialchars($mensaje['nombreProfesor']); 
                                } else {
                                    echo ($mensaje['nombreProfesor']) ? '(PROFESOR) ' . htmlspecialchars($mensaje['nombreProfesor']) : 'DIRECCIÓN (ADMIN)';
                                }
                            ?></strong>
                        </td>
                        <td><p class="texto-negrita"><?= htmlspecialchars(strtoupper($mensaje['asunto'])) ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?= htmlspecialchars(substr($mensaje['descripcion'], 0, 80)) ?>...
                            </div>
                        </td>
                        <td><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="estado-bolita activo-verde">VISTO</span>
                            <?php } else { ?>
                                <span class="estado-bolita inactivo-rojo">ENVIADO</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="../../../vistas/estudiantes/mensajes/detalles.php?id=<?= $mensaje['idReclamacion'] ?>" class="btn-accion btn-ver" title="Leer mensaje completo">
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



