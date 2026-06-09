<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/ejercicios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idEjercicio = intval($_GET['id'] ?? 0);
$ejercicio   = obtenerEjercicioPorId($idEjercicio);
if (!$ejercicio || $ejercicio['idProfesor'] != $idProfesor) {
    header("Location: panel.php"); exit;
}

$entregas    = listarEntregasPorEjercicio($idEjercicio);
$estudiantes = listarEstudiantesPorCiclo($ejercicio['idCiclo']);

$entregasMap = [];
foreach ($entregas as $e) $entregasMap[$e['idEstudiante']] = $e;

$totalEntregado  = count($entregas);
$totalCalificado = count(array_filter($entregas, function($e) { return $e['estado'] === 'calificado'; }));
$totalPendiente  = count($estudiantes) - $totalEntregado;

$tituloDelPagina = "AULAPRO | ENTREGAS";
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><?= Security::escapeHtml(mb_strtoupper($ejercicio['titulo'], 'UTF-8')) ?></h1>
    <?php if ($ejercicio['nombreCarpeta']): ?>
    <span class="ejercicio-card-carpeta" style="background:<?= Security::escapeHtml($ejercicio['colorCarpeta'] ) ?>22;color:<?= Security::escapeHtml($ejercicio['colorCarpeta'] ) ?>;margin-top:6px;display:inline-flex;">
      <i class="fas fa-folder" style="font-size:0.65rem;"></i> <?= Security::escapeHtml($ejercicio['nombreCarpeta']) ?>
    </span>
    <?php endif; ?>
  </div>
  <a href="panel.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php endif; ?>

<div class="entregas-stats">
  <div class="entrega-stat-card">
    <div class="numero"><?= Security::escapeHtml(count($estudiantes)) ?></div>
    <div class="label">Alumnos</div>
  </div>
  <div class="entrega-stat-card" style="background:#dbeafe22;border:1px solid #bfdbfe;">
    <div class="numero" style="color:#1d4ed8;"><?= Security::escapeHtml($totalEntregado ) ?></div>
    <div class="label">Entregado</div>
  </div>
  <div class="entrega-stat-card" style="background:#dcfce722;border:1px solid #bbf7d0;">
    <div class="numero" style="color:#15803d;"><?= Security::escapeHtml($totalCalificado ) ?></div>
    <div class="label">Calificado</div>
  </div>
  <div class="entrega-stat-card" style="background:#fef3c722;border:1px solid #fde68a;">
    <div class="numero" style="color:#b45309;"><?= Security::escapeHtml(max(0,$totalPendiente)) ?></div>
    <div class="label">Pendiente</div>
  </div>
</div>

<div class="panel">
  <div class="contenedor-tabla">
    <table class="tabla-datos">
      <thead>
        <tr>
          <th>Estudiante</th>
          <th>Estado</th>
          <th>Fecha entrega</th>
          <th>Respuesta</th>
          <th>Archivo</th>
          <th>Nota</th>
          <th>Calificar</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $est):
          $entrega = $entregasMap[$est['idEstudiante']] ?? null;
        ?>
        <tr>
          <td class="texto-negrita"><?= Security::escapeHtml($est['nombreEstudiante']) ?></td>
          <td>
            <?php if (!$entrega): ?>
              <span class="badge-estado badge-pendiente">Pendiente</span>
            <?php elseif ($entrega['estado'] === 'calificado'): ?>
              <span class="badge-estado badge-calificado">Calificado</span>
            <?php else: ?>
              <span class="badge-estado badge-entregado">Entregado</span>
            <?php endif; ?>
          </td>
          <td class="texto-suave">
            <?= Security::escapeHtml($entrega ? date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) : '—') ?>
          </td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            <?= Security::escapeHtml($entrega && $entrega['respuesta'] ? htmlspecialchars(substr($entrega['respuesta'], 0, 80)) . (strlen($entrega['respuesta']) > 80 ? '…' : '') : '<span class="texto-suave">—</span>') ?>
          </td>
          <td>
            <?php if ($entrega && $entrega['archivoEntrega']): ?>
            <a href="../../../public/uploads/ejercicios/entregas/<?= Security::escapeHtml($entrega['archivoEntrega']) ?>"
               target="_blank" class="btn-accion btn-ver" title="Ver archivo">
              <i class="fas fa-paperclip"></i>
            </a>
            <?php else: ?><span class="texto-suave">—</span><?php endif; ?>
          </td>
          <td class="texto-negrita <?= Security::escapeHtml($entrega && $entrega['nota'] !== null ? ($entrega['nota'] >= 5 ? 'texto-verde' : 'texto-rojo') : '') ?>">
            <?= Security::escapeHtml($entrega && $entrega['nota'] !== null ? $entrega['nota'] : '<span class="texto-suave">—</span>') ?>
          </td>
          <td>
            <?php if ($entrega): ?>
            <button type="button" class="btn-accion btn-editar"
                    onclick="abrirCalificar(<?= Security::escapeHtml($est['idEstudiante'] ) ?>, '<?= Security::escapeHtml($est['nombreEstudiante']) ?>', <?= Security::escapeHtml($entrega['nota'] ?? 'null') ?>, '<?= Security::escapeHtml(addslashes($entrega['comentarioProfesor'] ?? '')) ?>')"
                    title="Calificar">
              <i class="fas fa-star"></i>
            </button>
            <?php else: ?>
            <span class="texto-suave">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL CALIFICAR -->
<div id="modalCalificar" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:440px;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 id="calificarNombre" style="font-size:1rem;font-weight:700;color:#1e293b;"></h3>
      <button onclick="document.getElementById('modalCalificar').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/ejercicios/calificar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="idEjercicio" value="<?= Security::escapeHtml($idEjercicio ) ?>">
      <input type="hidden" name="idEstudiante" id="calificarIdEst">
      <div class="campo">
        <label>Nota (0 – 10)</label>
        <input type="number" name="nota" id="calificarNota" min="0" max="10" step="0.1" required>
      </div>
      <div class="campo">
        <label>Comentario para el alumno</label>
        <textarea name="comentario" id="calificarComentario" rows="3" placeholder="Opcional"></textarea>
      </div>
      <input type="submit" name="calificar" class="boton-primario" value="Guardar Nota" style="width:100%;margin-top:8px;">
    </form>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
function abrirCalificar(idEst, nombre, nota, comentario) {
  document.getElementById('calificarIdEst').value = idEst;
  document.getElementById('calificarNombre').textContent = nombre;
  document.getElementById('calificarNota').value = nota || '';
  document.getElementById('calificarComentario').value = comentario || '';
  document.getElementById('modalCalificar').style.display = 'flex';
}
</script>


