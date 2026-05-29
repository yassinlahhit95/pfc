<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idTarea = intval($_GET['id'] ?? 0);
$tarea   = obtenerTareaPorIdAula($idTarea);
if (!$tarea || $tarea['idProfesor'] != $idProfesor) { header("Location: index.php"); exit; }

$entregas    = listarEntregasPorTareaAula($idTarea);
$estudiantes = listarEstudiantesPorCiclo($tarea['idCiclo']);
$entregasMap = [];
foreach ($entregas as $e) $entregasMap[$e['idEstudiante']] = $e;

$totalEst        = count($estudiantes);
$totalEntregadas = count($entregas);
$totalCorregidas = count(array_filter($entregas, function($e){ return $e['estado']==='corregida'; }));

$tituloDelPagina = "AULAPRO | ENTREGAS";
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="aula-breadcrumb">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="sep">/</span>
  <a href="modulo.php?id=<?= $tarea['idModulo'] ?>"><?= htmlspecialchars($tarea['nombreModulo']) ?></a>
  <span class="sep">/</span>
  <span class="actual">Entregas</span>
</div>

<div class="cabecera">
  <div>
    <h1><?= htmlspecialchars(mb_strtoupper($tarea['titulo'], 'UTF-8')) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= htmlspecialchars($tarea['nombreModulo']) ?></p>
  </div>
  <a href="modulo.php?id=<?= $tarea['idModulo'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<div class="entregas-stats">
  <div class="entrega-stat-card"><div class="numero"><?= $totalEst ?></div><div class="label">Alumnos</div></div>
  <div class="entrega-stat-card" style="border:1px solid #bfdbfe;background:#dbeafe22;">
    <div class="numero" style="color:#1d4ed8;"><?= $totalEntregadas ?></div><div class="label">Enviadas</div>
  </div>
  <div class="entrega-stat-card" style="border:1px solid #bbf7d0;background:#dcfce722;">
    <div class="numero" style="color:#15803d;"><?= $totalCorregidas ?></div><div class="label">Corregidas</div>
  </div>
  <div class="entrega-stat-card" style="border:1px solid #fde68a;background:#fef3c722;">
    <div class="numero" style="color:#b45309;"><?= max(0,$totalEst-$totalEntregadas) ?></div><div class="label">Pendientes</div>
  </div>
</div>

<?php if ($tarea['descripcion']): ?>
<div class="panel" style="margin-bottom:20px;border-left:4px solid #8b5cf6;">
  <p style="font-size:0.85rem;color:#374151;white-space:pre-wrap;"><?= htmlspecialchars($tarea['descripcion']) ?></p>
</div>
<?php endif; ?>

<div class="panel">
  <div class="contenedor-tabla">
    <table class="tabla-datos">
      <thead>
        <tr>
          <th>Estudiante</th>
          <th>Estado</th>
          <th>Fecha envío</th>
          <th>Versión</th>
          <th>Respuesta</th>
          <th>Archivo</th>
          <th>Nota</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $est):
          $entrega = $entregasMap[$est['idEstudiante']] ?? null;
        ?>
        <tr>
          <td class="texto-negrita"><?= htmlspecialchars($est['nombreEstudiante']) ?></td>
          <td>
            <?php if (!$entrega): ?>
              <span class="aula-estado-badge aula-estado-pendiente"><i class="fas fa-clock"></i> Pendiente</span>
            <?php elseif ($entrega['estado']==='corregida'): ?>
              <span class="aula-estado-badge aula-estado-corregida"><i class="fas fa-check-circle"></i> Corregida</span>
            <?php else: ?>
              <span class="aula-estado-badge aula-estado-enviada"><i class="fas fa-paper-plane"></i> Enviada</span>
            <?php endif; ?>
          </td>
          <td class="texto-suave"><?= $entrega ? date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) : '—' ?></td>
          <td><?= $entrega ? 'v'.$entrega['version'] : '—' ?></td>
          <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            <?= $entrega && $entrega['respuesta'] ? htmlspecialchars(substr($entrega['respuesta'],0,80)).(strlen($entrega['respuesta'])>80?'…':'') : '<span class="texto-suave">—</span>' ?>
          </td>
          <td>
            <?php if ($entrega && $entrega['archivoEntrega']): ?>
            <a href="../../../public/uploads/aula/entregas/<?= htmlspecialchars($entrega['archivoEntrega'],ENT_QUOTES) ?>"
               target="_blank" class="btn-accion btn-ver" title="Ver archivo"><i class="fas fa-paperclip"></i></a>
            <?php else: ?><span class="texto-suave">—</span><?php endif; ?>
          </td>
          <td class="texto-negrita <?= $entrega && $entrega['nota']!==null ? ($entrega['nota']>=5?'texto-verde':'texto-rojo') : '' ?>">
            <?= $entrega && $entrega['nota']!==null ? $entrega['nota'] : '<span class="texto-suave">—</span>' ?>
          </td>
          <td>
            <?php if ($entrega): ?>
            <button type="button" class="btn-accion btn-editar" title="Calificar y feedback"
                    onclick="abrirCalificar(<?= $entrega['idEntrega'] ?>, <?= $idTarea ?>, '<?= htmlspecialchars($est['nombreEstudiante'],ENT_QUOTES) ?>', <?= $entrega['nota'] ?? 'null' ?>)">
              <i class="fas fa-star"></i>
            </button>
            <?php else: ?><span class="texto-suave">—</span><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL CALIFICAR -->
<div id="modalCalif" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:460px;max-height:90vh;overflow-y:auto;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 id="califNombre" style="font-size:1rem;font-weight:700;margin:0;"></h3>
      <button onclick="document.getElementById('modalCalif').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/calificarEntrega.php" method="POST" enctype="multipart/form-data" class="formulario">
      <input type="hidden" name="idEntrega" id="califIdEntrega">
      <input type="hidden" name="idTarea" value="<?= $idTarea ?>">
      <div class="campo"><label>Nota (0 – 10)</label><input type="number" name="nota" id="califNota" min="0" max="10" step="0.1" required></div>
      <div class="campo"><label>Feedback para el alumno</label><textarea name="feedback" id="califFeedback" rows="4" placeholder="Comentario, correcciones, sugerencias..."></textarea></div>
      <div class="campo">
        <label>Archivo de corrección (PDF/DOCX/TXT)</label>
        <input type="file" name="archivoCorreccion" accept=".pdf,.docx,.txt">
      </div>
      <input type="submit" name="calificar" class="boton-primario" value="Guardar calificación" style="width:100%;margin-top:8px;">
    </form>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
function abrirCalificar(idEntrega, idTarea, nombre, nota) {
  document.getElementById('califIdEntrega').value = idEntrega;
  document.getElementById('califNombre').textContent = nombre;
  document.getElementById('califNota').value = nota || '';
  document.getElementById('modalCalif').style.display = 'flex';
}
</script>
