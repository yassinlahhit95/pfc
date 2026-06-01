<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: /pfc/vistas/profesores/mensajes/lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | EDITAR MENSAJE";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>DETALLES DEL MENSAJE</h1>
    <a href="../../../vistas/profesores/mensajes/lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <div class="caja espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-suave">Enviado por:</p>
            <h3><?= Security::escapeHtml($mensaje['nombreEstudiante'] ) ?></h3>
        </div>
        <div>
            <p class="texto-suave">Fecha:</p>
            <p class="texto-negrita"><?= Security::escapeHtml(date('d/m/Y', strtotime($mensaje['fecha']))) ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <p class="texto-negrita"><?= Security::escapeHtml($mensaje['asunto'] ) ?></p>
        <hr class="margen-abajo" style="margin-top: 5px;">
        <p><?= Security::escapeHtml(nl2br($mensaje['descripcion'])) ?></p>
    </div>

    <form action="../../../controladores/profesores/mensajes/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReclamacion" value="<?= Security::escapeHtml($idReclamacion ) ?>">
        
        <div class="campo">
            <label for="respuesta">Tu Respuesta / Explicación:</label>
            <textarea name="respuesta" id="respuesta" rows="5" placeholder="Escribe tu respuesta..."><?= Security::escapeHtml($mensaje['respuesta'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarRespuesta" class="boton-primario" value="GUARDAR RESPUESTA">
            <input type="submit" name="marcarLeido" class="boton-secundario" value="MARCAR COMO LEIDO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


