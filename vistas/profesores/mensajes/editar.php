<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

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

<div class="encabezado-pagina">
    <h1>DETALLES DEL MENSAJE</h1>
    <a href="../../../vistas/profesores/mensajes/lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-atenuado">Enviado por:</p>
            <h3><?= $mensaje['nombreEstudiante'] ?></h3>
        </div>
        <div>
            <p class="texto-atenuado">Fecha:</p>
            <p class="texto-negrita"><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <p class="texto-negrita"><?= $mensaje['asunto'] ?></p>
        <hr class="margen-abajo" style="margin-top: 5px;">
        <p><?= nl2br($mensaje['descripcion']) ?></p>
    </div>

    <form action="../../../controladores/profesores/mensajes/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
        
        <div class="campo-formulario">
            <label for="respuesta">Tu Respuesta / Explicación:</label>
            <textarea name="respuesta" id="respuesta" rows="4" placeholder="Escribe aquí tu respuesta..." class="<?= isset($errores['respuesta']) ? 'input-error' : '' ?>"><?= $mensaje['respuesta'] ?></textarea>
            <?php if (isset($errores['respuesta'])) { ?>
                <strong class="error-campo"><?= $errores['respuesta'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarRespuesta" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR RESPUESTA
            </button>
            <button type="submit" name="marcarLeido" class="boton-secundario">
                <i class="fas fa-check"></i> MARCAR COMO LEÍDO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



