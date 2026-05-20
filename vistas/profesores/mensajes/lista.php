<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfesor = $_SESSION['idProfesor'];
$listaDeMensajes = listarMensajesParaProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | MENSAJERIA";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>BUZON DE MENSAJES</h1>
    <a href="../../../vistas/profesores/mensajes/agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> REDACTAR MENSAJE
    </a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
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
                    <tr><td colspan="7" class="vacio">No hay mensajes registrados aun.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) { 
                        $esMio = ($mensaje['emisor_rol'] == 'profesor');
                        $claseFila = $esMio ? 'fila-propia' : '';
                    ?>
                    <tr class="<?= $claseFila ?>">
                        <td>
                            <b><?php 
                                if ($mensaje['emisor_rol'] == 'profesor') {
                                    echo 'T (Profesor)';
                                } elseif ($mensaje['emisor_rol'] == 'admin') {
                                    echo 'DIRECCIÓN (ADMIN)';
                                } else {
                                    echo $mensaje['nombreEstudiante'] ?? 'Estudiante';
                                }
                            ?></b>
                        </td>
                        <td><?= $mensaje['abreviaturaCiclo'] ?? '-' ?></td>
                        <td><p class="texto-negrita"><?= strtoupper($mensaje['asunto']) ?></p></td>
                        <td>
                            <div class="cuerpo-mensaje-tabla">
                                <?= substr($mensaje['descripcion'], 0, 40) ?>...
                            </div>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($mensaje['fecha'])) ?><br>
                            <span class="texto-suave"><?= date('H:i:s', strtotime($mensaje['fecha'])) ?></span>
                        </td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="indicador-estado activo-verde">Leido</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">Nuevo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="botones-accion">
                                <a href="../../../vistas/profesores/mensajes/detalles.php?id=<?= $mensaje['idReclamacion'] ?>" class="btn-accion btn-ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="../../../controladores/profesores/mensajes/borrar.php" method="POST" onsubmit="return confirm('Eliminar este mensaje?')">
                                    <input type="hidden" name="idReclamacion" value="<?= $mensaje['idReclamacion'] ?>">
                                    <input type="submit" class="btn-accion btn-eliminar" value="Borrar">
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

<?php include __DIR__ . '/../comunes/footer.php'; ?>

