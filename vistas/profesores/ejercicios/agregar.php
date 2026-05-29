<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/ejercicios.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);
$idCarpetaPresel = intval($_GET['idCarpeta'] ?? 0);
$carpetas = listarCarpetasPorProfesor($idProfesor);
$ciclos   = listarCiclosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | NUEVO EJERCICIO";
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <h1>NUEVO EJERCICIO</h1>
  <a href="panel.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<div class="panel margen-arriba" style="max-width:720px;">
  <form action="../../../controladores/profesores/ejercicios/insertar.php" method="POST" enctype="multipart/form-data" class="formulario">

    <div class="campo">
      <label>Título *</label>
      <input type="text" name="titulo" placeholder="Ej: Ejercicio 3 - Arrays en PHP" required maxlength="150">
    </div>

    <div class="campo">
      <label>Descripción / Enunciado</label>
      <textarea name="descripcion" rows="6" placeholder="Describe el ejercicio, instrucciones, recursos..."></textarea>
    </div>

    <div class="form-cols">
      <div class="campo">
        <label>Ciclo *</label>
        <select name="idCiclo" required>
          <option value="">-- Seleccionar ciclo --</option>
          <?php foreach ($ciclos as $c): ?>
          <option value="<?= $c['idCiclo'] ?>"><?= htmlspecialchars($c['nombreCiclo']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="campo">
        <label>Carpeta</label>
        <select name="idCarpeta">
          <option value="">Sin carpeta</option>
          <?php foreach ($carpetas as $c): ?>
          <option value="<?= $c['idCarpeta'] ?>" <?= $idCarpetaPresel == $c['idCarpeta'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-cols">
      <div class="campo">
        <label>Fecha límite de entrega</label>
        <input type="datetime-local" name="fechaLimite">
      </div>

      <div class="campo">
        <label>Archivo adjunto (PDF, DOC, imagen...)</label>
        <input type="file" name="archivoAdjunto" accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.zip">
      </div>
    </div>

    <div class="caja espacio-entre-elementos alinear-centro" style="margin-top:20px;">
      <a href="panel.php" class="boton-secundario">Cancelar</a>
      <input type="submit" name="guardarEjercicio" class="boton-primario" value="Publicar Ejercicio">
    </div>
  </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
