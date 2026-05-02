<?php
session_start();
$titulo_pagina = "Modificar Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$id_reclamacion = $_GET['idReclamacion'];
$reclamacion = obtenerReclamacionPorId($id_reclamacion);

if (!$reclamacion) {
    header("Location: verReclamaciones.php");
    exit;
}

if (isset($_SESSION['datos_reclamacion'])) {
    $reclamacion = $_SESSION['datos_reclamacion'];
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reclamacion']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Reclamación</h1>
    <a href="verReclamaciones.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/reclamaciones/actualizar.php">
        <input type="hidden" name="idReclamacion" value="<?php echo $id_reclamacion; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Asunto *</label>
                <input type="text" name="asuntoReclamacion" value="<?php echo $reclamacion['asuntoReclamacion']; ?>">
                <?php if (isset($lista_de_errores['asuntoReclamacion'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['asuntoReclamacion']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="estadoReclamacion">
                    <option value="Pendiente" <?php if($reclamacion['estadoReclamacion'] == 'Pendiente') echo "selected"; ?>>Pendiente</option>
                    <option value="En Proceso" <?php if($reclamacion['estadoReclamacion'] == 'En Proceso') echo "selected"; ?>>En Proceso</option>
                    <option value="Resuelta" <?php if($reclamacion['estadoReclamacion'] == 'Resuelta') echo "selected"; ?>>Resuelta</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción *</label>
                <textarea name="descripcionReclamacion" rows="6"><?php echo $reclamacion['descripcionReclamacion']; ?></textarea>
                <?php if (isset($lista_de_errores['descripcionReclamacion'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['descripcionReclamacion']; ?></p>
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

