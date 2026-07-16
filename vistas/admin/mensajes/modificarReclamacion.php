<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_mensajes');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = (int)($_GET['idReclamacion'] ?? 0);
$reclamacion = obtenerMensajePorId($idReclamacion);

if (!$reclamacion) {
    header("Location: lista.php");
    exit;
}

// Merge with any stored form data (re-fill after error)
$datos = $_SESSION['datos_reclamacion'] ?? [];
unset($_SESSION['datos_reclamacion']);
if (!empty($datos)) {
    $reclamacion = $datos + $reclamacion;
}

$titulo_pagina = "AULAPRO | GESTIONAR MENSAJE";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1><i class="fas fa-edit"></i> GESTIONAR MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/mensajes/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">

        <div class="fila-datos">
            <div class="nombre-detalle">Asunto</div>
            <div class="valor-detalle texto-negrita"><?= Security::escapeHtml($reclamacion['asunto'] ?? '') ?></div>
        </div>

        <div class="fila-datos">
            <div class="nombre-detalle">Estado actual</div>
            <div class="valor-detalle">
                <?php if ($reclamacion['leido']): ?>
                    <span class="indicador-estado activo-verde">Leído / Atendido</span>
                <?php else: ?>
                    <span class="indicador-estado inactivo-rojo">Pendiente</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="fila-datos">
            <div class="nombre-detalle">Mensaje original</div>
            <div class="valor-detalle valor-mensaje"><?= Security::escapeHtml($reclamacion['descripcion'] ?? '') ?></div>
        </div>

        <div class="msg-respuesta-box">
            <div class="campo<?= fieldClass($errores, 'respuesta') ?>">
                <label for="respuesta">Respuesta / Nota interna</label>
                <textarea id="respuesta" name="respuesta" rows="5" class="ancho-total" maxlength="1000"
                          placeholder="Escribe tu respuesta..."><?= Security::escapeHtml($reclamacion['respuesta'] ?? '') ?></textarea>
                <?= fieldError($errores, 'respuesta') ?>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="guardarCambios" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Respuesta
            </button>
            <?php if (!$reclamacion['leido']): ?>
            <button type="submit" name="marcarLeido" class="boton-secundario">
                <i class="fas fa-check"></i> Marcar como Leído
            </button>
            <?php endif; ?>
            <a href="lista.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script src="../../../public/js/features/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/features/mensajes.js') ?>"></script>
