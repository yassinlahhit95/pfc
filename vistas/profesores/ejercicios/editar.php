<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/ejercicios.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idEjercicio = intval($_GET['id'] ?? 0);
$ejercicio   = obtenerEjercicioPorId($idEjercicio);

if (!$ejercicio || $ejercicio['idProfesor'] != $idProfesor) {
    header("Location: panel.php"); exit;
}

$carpetas = listarCarpetasPorProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | EDITAR EJERCICIO";
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <h1>EDITAR EJERCICIO</h1>
  <a href="panel.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel margen-arriba" style="max-width:720px;">
  <form action="../../../controladores/profesores/ejercicios/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idEjercicio" value="<?= Security::escapeHtml($ejercicio['idEjercicio'] ) ?>">

    <div class="campo">
      <label>Título *</label>
      <input type="text" name="titulo" value="<?= Security::escapeHtml($ejercicio['titulo']) ?>" required maxlength="150">
    </div>

    <div class="campo">
      <label>Descripción / Enunciado</label>
      <textarea name="descripcion" rows="6"><?= Security::escapeHtml($ejercicio['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="form-cols">
      <div class="campo">
        <label>Carpeta</label>
        <select name="idCarpeta">
          <option value="">Sin carpeta</option>
          <?php foreach ($carpetas as $c): ?>
          <option value="<?= Security::escapeHtml($c['idCarpeta'] ) ?>" <?= Security::escapeHtml($ejercicio['idCarpeta'] == $c['idCarpeta'] ? 'selected' : '') ?>>
            <?= Security::escapeHtml($c['nombre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="campo">
        <label>Fecha límite</label>
        <input type="datetime-local" name="fechaLimite"
               value="<?= Security::escapeHtml($ejercicio['fechaLimite'] ? date('Y-m-d\TH:i', strtotime($ejercicio['fechaLimite'])) : '') ?>">
      </div>
    </div>

    <div class="campo">
      <label class="caja alinear-centro espacio-pequeno" style="cursor:pointer;">
        <input type="checkbox" name="publicado" value="1" <?= Security::escapeHtml($ejercicio['publicado'] ? 'checked' : '') ?>>
        <span>Publicado (visible para estudiantes)</span>
      </label>
    </div>

    <div class="caja espacio-entre-elementos alinear-centro" style="margin-top:20px;">
      <a href="panel.php" class="boton-secundario">Cancelar</a>
      <input type="submit" name="actualizarEjercicio" class="boton-primario" value="Guardar cambios">
    </div>
  </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


