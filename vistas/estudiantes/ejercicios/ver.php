<?php
session_start();
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/ejercicios.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idEjercicio = intval($_GET['id'] ?? 0);
$ejercicio   = obtenerEjercicioPorId($idEjercicio);

if (!$ejercicio || !$ejercicio['publicado']) {
    header("Location: lista.php"); exit;
}

$entrega = obtenerEntregaPorEstudiante($idEjercicio, $idEstudiante);
$hoy     = new DateTime();
$limite  = $ejercicio['fechaLimite'] ? new DateTime($ejercicio['fechaLimite']) : null;
$tarde   = $limite && $limite < $hoy && !$entrega;

$tituloDelPagina = "AULAPRO | " . mb_strtoupper($ejercicio['titulo'], 'UTF-8');
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <?php if ($ejercicio['nombreCarpeta']): ?>
    <span class="ejercicio-card-carpeta" style="background:<?= $ejercicio['colorCarpeta'] ?>22;color:<?= $ejercicio['colorCarpeta'] ?>;margin-bottom:8px;display:inline-flex;">
      <i class="fas fa-folder" style="font-size:0.65rem;"></i>
      <?= htmlspecialchars($ejercicio['nombreCarpeta']) ?>
    </span>
    <?php endif; ?>
    <h1><?= htmlspecialchars(mb_strtoupper($ejercicio['titulo'], 'UTF-8')) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">
      <i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($ejercicio['nombreProfesor']) ?>
      <?php if ($limite): ?>
      &nbsp;·&nbsp; <i class="fas fa-clock <?= $tarde ? 'texto-rojo' : '' ?>"></i>
      <span class="<?= $tarde ? 'texto-rojo' : '' ?>">
        <?= $tarde ? 'Plazo superado' : 'Hasta ' . $limite->format('d/m/Y H:i') ?>
      </span>
      <?php endif; ?>
    </p>
  </div>
  <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<!-- ENUNCIADO -->
<?php if ($ejercicio['descripcion'] || $ejercicio['archivoAdjunto']): ?>
<div class="panel margen-arriba">
  <div class="titulo-tarjeta"><h3>Enunciado</h3></div>
  <?php if ($ejercicio['descripcion']): ?>
  <p style="white-space:pre-wrap;line-height:1.7;color:#374151;"><?= htmlspecialchars($ejercicio['descripcion']) ?></p>
  <?php endif; ?>
  <?php if ($ejercicio['archivoAdjunto']): ?>
  <div style="margin-top:16px;">
    <a href="../../../public/uploads/ejercicios/adjuntos/<?= htmlspecialchars($ejercicio['archivoAdjunto'], ENT_QUOTES) ?>"
       target="_blank" class="boton-secundario" style="display:inline-flex;">
      <i class="fas fa-paperclip"></i> Ver archivo del profesor
    </a>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- CALIFICACIÓN (si existe) -->
<?php if ($entrega && $entrega['estado'] === 'calificado'): ?>
<div class="panel margen-arriba" style="border-left:4px solid #10b981;">
  <div class="titulo-tarjeta"><h3>Tu Calificación</h3></div>
  <div class="cuadricula-estadisticas" style="margin-bottom:0;">
    <div class="tarjeta-estadistica <?= $entrega['nota'] >= 5 ? 'tarjeta-estadistica-verde' : 'tarjeta-estadistica-rojo' ?>">
      <div class="info-estadistica">
        <h3><?= $entrega['nota'] ?></h3>
        <p>Nota / 10</p>
      </div>
    </div>
  </div>
  <?php if ($entrega['comentarioProfesor']): ?>
  <div style="margin-top:16px;padding:14px 18px;background:#f0fdf4;border-radius:10px;">
    <p style="font-size:0.85rem;color:#166534;"><strong>Comentario del profesor:</strong></p>
    <p style="color:#374151;margin-top:4px;"><?= htmlspecialchars($entrega['comentarioProfesor']) ?></p>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- ENTREGA -->
<div class="panel margen-arriba">
  <div class="titulo-tarjeta caja espacio-entre-elementos alinear-centro">
    <h3>Mi Entrega</h3>
    <?php if ($entrega): ?>
    <span class="badge-estado badge-<?= $entrega['estado'] === 'calificado' ? 'calificado' : 'entregado' ?>">
      <?= $entrega['estado'] === 'calificado' ? 'Calificado' : 'Entregado' ?>
    </span>
    <?php else: ?>
    <span class="badge-estado badge-<?= $tarde ? 'tarde' : 'pendiente' ?>">
      <?= $tarde ? 'Plazo superado' : 'Pendiente' ?>
    </span>
    <?php endif; ?>
  </div>

  <?php if ($entrega): ?>
  <div style="margin-bottom:20px;">
    <p class="texto-suave" style="font-size:0.8rem;margin-bottom:8px;">
      Entregado el <?= date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) ?>
    </p>
    <?php if ($entrega['respuesta']): ?>
    <div style="background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;">
      <p style="white-space:pre-wrap;font-size:0.9rem;color:#374151;"><?= htmlspecialchars($entrega['respuesta']) ?></p>
    </div>
    <?php endif; ?>
    <?php if ($entrega['archivoEntrega']): ?>
    <div style="margin-top:10px;">
      <a href="../../../public/uploads/ejercicios/entregas/<?= htmlspecialchars($entrega['archivoEntrega'], ENT_QUOTES) ?>"
         target="_blank" class="boton-secundario btn-pequeno" style="display:inline-flex;">
        <i class="fas fa-paperclip"></i> Mi archivo adjunto
      </a>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (!$tarde || $entrega): ?>
  <form action="../../../controladores/estudiantes/ejercicios/entregar.php" method="POST" enctype="multipart/form-data" class="formulario">
    <input type="hidden" name="idEjercicio" value="<?= $idEjercicio ?>">
    <div class="campo">
      <label><?= $entrega ? 'Actualizar respuesta (opcional)' : 'Tu respuesta' ?></label>
      <textarea name="respuesta" rows="5" placeholder="Escribe tu respuesta aquí..."><?= $entrega ? htmlspecialchars($entrega['respuesta'] ?? '') : '' ?></textarea>
    </div>
    <div class="campo">
      <label><?= $entrega ? 'Actualizar archivo adjunto (opcional)' : 'Archivo adjunto (opcional)' ?></label>
      <input type="file" name="archivoEntrega" accept=".pdf,.doc,.docx,.txt,.png,.jpg,.jpeg,.zip">
    </div>
    <div style="margin-top:16px;">
      <input type="submit" name="entregar" class="boton-primario"
             value="<?= $entrega ? 'Actualizar entrega' : 'Entregar' ?>">
    </div>
  </form>
  <?php else: ?>
  <p class="texto-suave" style="color:#ef4444;"><i class="fas fa-lock"></i> El plazo de entrega ha finalizado.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
