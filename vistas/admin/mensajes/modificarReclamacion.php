<?php
session_start();

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id_reclamacion = $_GET['idReclamacion'] ?? 0;
$reclamacion = obtenerMensajePorId(intval($id_reclamacion));

if (!$reclamacion) {
    header("Location: lista.php");
    exit;
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reclamacion'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reclamacion']);

$reclamacion = !empty($datos) ? array_merge($reclamacion, $datos) : $reclamacion;

$titulo_pagina = "AULAPRO | MODIFICAR RECLAMACIÓN";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR RECLAMACIÓN</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/mensajes/actualizar.php">
        <input type="hidden" name="idReclamacion" value="<?= $id_reclamacion ?>">

        <div class="form-cols">
            <div class="campo">
                <label>Asunto</label>
                <input type="text" name="asuntoReclamacion" value="<?= $reclamacion['asuntoReclamacion'] ?? '' ?>">
                <?php if (isset($errores['asuntoReclamacion'])) { ?>
                    <strong class="error-campo"><?= $errores['asuntoReclamacion'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label>Estado</label>
                <select name="estadoReclamacion">
                    <option value="Pendiente" <?= ($reclamacion['estadoReclamacion'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                    <option value="En Proceso" <?= ($reclamacion['estadoReclamacion'] == 'En Proceso') ? 'selected' : '' ?>>En Proceso</option>
                    <option value="Resuelta" <?= ($reclamacion['estadoReclamacion'] == 'Resuelta') ? 'selected' : '' ?>>Resuelta</option>
                </select>
            </div>

            <div class="campo campo-ancho-total">
                <label>Descripción</label>
                <textarea name="descripcionReclamacion" rows="6"><?= $reclamacion['descripcionReclamacion'] ?? '' ?></textarea>
                <?php if (isset($errores['descripcionReclamacion'])) { ?>
                    <strong class="error-campo"><?= $errores['descripcionReclamacion'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <input type="submit" name="actualizarReclamacion" class="boton-primario" value="Guardar Cambios">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




