<?php
session_start();
$titulo_pagina = "Modificar Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id_reclamacion = $_GET['idReclamacion'] ?? 0;
$reclamacion = obtenerReclamacionPorId(intval($id_reclamacion));

if (!$reclamacion) {
    header("Location: lista.php");
    exit;
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reclamacion'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reclamacion']);

$reclamacion = !empty($datos) ? array_merge($reclamacion, $datos) : $reclamacion;
?>

<div class="encabezado-pagina">
    <h1>Modificar Reclamación</h1>
    <a href="lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/mensajes/actualizar.php">
        <input type="hidden" name="idReclamacion" value="<?= $id_reclamacion ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asuntoReclamacion" value="<?= $reclamacion['asuntoReclamacion'] ?? '' ?>">
                <?php if (isset($lista_de_errores['asuntoReclamacion'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['asuntoReclamacion'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="estadoReclamacion">
                    <option value="Pendiente" <?= ($reclamacion['estadoReclamacion'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                    <option value="En Proceso" <?= ($reclamacion['estadoReclamacion'] == 'En Proceso') ? 'selected' : '' ?>>En Proceso</option>
                    <option value="Resuelta" <?= ($reclamacion['estadoReclamacion'] == 'Resuelta') ? 'selected' : '' ?>>Resuelta</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción *</label>
                <textarea name="descripcionReclamacion" rows="6"><?= $reclamacion['descripcionReclamacion'] ?? '' ?></textarea>
                <?php if (isset($lista_de_errores['descripcionReclamacion'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['descripcionReclamacion'] ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReclamacion" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


