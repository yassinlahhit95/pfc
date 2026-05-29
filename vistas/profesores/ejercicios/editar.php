<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

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

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<div class="panel margen-arriba" style="max-width:720px;">
  <form action="../../../controladores/profesores/ejercicios/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="idEjercicio" value="<?= $ejercicio['idEjercicio'] ?>">

    <div class="campo">
      <label>Título *</label>
      <input type="text" name="titulo" value="<?= htmlspecialchars($ejercicio['titulo']) ?>" required maxlength="150">
    </div>

    <div class="campo">
      <label>Descripción / Enunciado</label>
      <textarea name="descripcion" rows="6"><?= htmlspecialchars($ejercicio['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="form-cols">
      <div class="campo">
        <label>Carpeta</label>
        <select name="idCarpeta">
          <option value="">Sin carpeta</option>
          <?php foreach ($carpetas as $c): ?>
          <option value="<?= $c['idCarpeta'] ?>" <?= $ejercicio['idCarpeta'] == $c['idCarpeta'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="campo">
        <label>Fecha límite</label>
        <input type="datetime-local" name="fechaLimite"
               value="<?= $ejercicio['fechaLimite'] ? date('Y-m-d\TH:i', strtotime($ejercicio['fechaLimite'])) : '' ?>">
      </div>
    </div>

    <div class="campo">
      <label class="caja alinear-centro espacio-pequeno" style="cursor:pointer;">
        <input type="checkbox" name="publicado" value="1" <?= $ejercicio['publicado'] ? 'checked' : '' ?>>
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
