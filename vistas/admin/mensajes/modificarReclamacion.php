<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id_reclamacion = $_GET['idReclamacion'] ?? 0;
$reclamacion = obtenerMensajePorId($id_reclamacion);

if (!$reclamacion) {
    header("Location: lista.php");
    exit;
}

$datos = $_SESSION['datos_reclamacion'] ?? [];

$reclamacion = !empty($datos) ? $datos + $reclamacion : $reclamacion;

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

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/mensajes/actualizar.php">
        <input type="hidden" name="idReclamacion" value="<?= $id_reclamacion ?>">

        <div class="form-cols">
            <div class="campo">
                <label>Asunto</label>
                <input type="text" name="asuntoReclamacion" value="<?= $reclamacion['asuntoReclamacion'] ?? '' ?>">
                
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
                
            </div>
        </div>

        <div class="margen-arriba">
            <input type="submit" name="actualizarReclamacion" class="boton-primario" value="Guardar Cambios">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

