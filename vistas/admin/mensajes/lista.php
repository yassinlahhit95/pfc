<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$listaDeMensajes = listarTodosLosMensajes();

$titulo_pagina = "AULAPRO | GESTIÓN DE MENSAJERÍA";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>BUZÓN CENTRAL DE MENSAJES</h1>
    <a href="agregar.php" class="boton-primario">
        <i class="fas fa-plus"></i> NUEVO MENSAJE
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
                    <tr><td colspan="6" class="vacio">No hay mensajes registrados.</td></tr>
                <?php } else { ?>
                    <?php foreach ($listaDeMensajes as $mensaje) {
                        $emisor = "";
                        $receptor = "";

                        if ($mensaje['emisor_rol'] == 'admin') {
                            $emisor = "Dirección (Admin)";
                            if ($mensaje['idEstudiante']) $receptor = "(Alumno) " . $mensaje['nombreEstudiante'];
                            elseif ($mensaje['idProfesor']) $receptor = "(Profesor) " . $mensaje['nombreProfesor'];
                            else $receptor = "General";
                        } elseif ($mensaje['emisor_rol'] == 'estudiante') {
                            $emisor = "(Alumno) " . $mensaje['nombreEstudiante'];
                            $receptor = "Dirección (Admin)";
                        } elseif ($mensaje['emisor_rol'] == 'profesor') {
                            $emisor = "(Profesor) " . $mensaje['nombreProfesor'];
                            $receptor = "Dirección (Admin)";
                        }
                    ?>
                    <tr>
                        <td><b><?= $emisor ?></b></td>
                        <td><?= $receptor ?></td>
                        <td>
                            <p class="texto-negrita"><?= $mensaje['asunto'] ?></p>
                            <span class="texto-suave"><?= substr($mensaje['descripcion'], 0, 40) ?>...</span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></td>
                        <td>
                            <?php if ($mensaje['leido']) { ?>
                                <span class="indicador-estado activo-verde">Leído</span>
                            <?php } else { ?>
                                <span class="indicador-estado inactivo-rojo">Nuevo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="detalles.php?id=<?= $mensaje['idReclamacion'] ?>"><i class="fas fa-eye"></i> Ver</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="borrarMensaje.php?id=<?= $mensaje['idReclamacion'] ?>" onclick="return confirm('¿Eliminar este mensaje?')"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
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
<script>
iniciarPaginacion('tablaMensajes', 15);
</script>

