<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: /pfc/vistas/profesores/mensajes/lista.php");
    exit;
}

$tituloPagina = "Gestionar Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="../../../vistas/profesores/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-atenuado">Enviado por:</p>
            <h3><?= htmlspecialchars($mensaje['nombreEstudiante']) ?></h3>
        </div>
        <div>
            <p class="texto-atenuado">Fecha:</p>
            <p class="texto-negrita"><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <p class="texto-negrita"><?= htmlspecialchars($mensaje['asunto']) ?></p>
        <hr class="mt-5 margen-abajo">
        <p><?= nl2br(htmlspecialchars($mensaje['descripcion'])) ?></p>
    </div>

    <form action="../../../controladores/profesores/mensajes/actualizar.php" method="POST">
        <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
        
        <div class="campo-formulario">
            <label>Tu Respuesta / Explicación:</label>
            <textarea name="respuesta" rows="4" placeholder="Escribe aquí tu respuesta..." class="<?= isset($errores['respuesta']) ? 'input-error' : '' ?>"><?= htmlspecialchars($mensaje['respuesta']) ?></textarea>
            <?php if (isset($errores['respuesta'])): ?>
                <strong class="error-campo"><?= $errores['respuesta'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarRespuesta" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Respuesta
            </button>
            <button type="submit" name="marcarLeido" class="boton-secundario">
                <i class="fas fa-check"></i> Solo marcar como Leído
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



