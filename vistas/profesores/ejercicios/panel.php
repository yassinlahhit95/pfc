<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/ejercicios.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idCarpetaActiva = intval($_GET['idCarpeta'] ?? 0);

$carpetas  = listarCarpetasPorProfesor($idProfesor);
$ejercicios = listarEjerciciosPorProfesor($idProfesor, $idCarpetaActiva);
$ciclos    = listarCiclosDeProfesor($idProfesor);

$tituloDelPagina = "AULAPRO | EJERCICIOS";
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1>EJERCICIOS</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Gestiona tus carpetas y ejercicios</p>
  </div>
  <div class="caja alinear-centro espacio-pequeno">
    <button onclick="document.getElementById('modalCarpeta').style.display='flex'" class="boton-secundario">
      <i class="fas fa-folder-plus"></i> Nueva Carpeta
    </button>
    <a href="agregar.php<?= Security::escapeHtml($idCarpetaActiva ? '?idCarpeta='.$idCarpetaActiva : '') ?>" class="boton-primario">
      <i class="fas fa-plus"></i> Nuevo Ejercicio
    </a>
  </div>
</div>

<?php if ($exito) { ?><div class="mensaje-exito"><?= Security::escapeHtml(htmlspecialchars($exito)) ?></div><?php } ?>
<?php if ($errores) { ?><div class="mensaje-error"><?= Security::escapeHtml(htmlspecialchars($errores)) ?></div><?php } ?>

<div class="ejercicios-layout" style="margin-top:20px;">

  <!-- CARPETAS -->
  <div class="ejercicios-carpetas-panel">
    <div class="ejercicios-carpetas-header">
      <h3>Carpetas</h3>
    </div>

    <a href="panel.php" class="carpeta-item <?= Security::escapeHtml($idCarpetaActiva === 0 ? 'activa' : '') ?>">
      <span class="carpeta-punto" style="background:#94a3b8;"></span>
      <span>Todos</span>
      <span class="carpeta-count"><?= Security::escapeHtml(count($ejercicios)) ?></span>
    </a>

    <?php foreach ($carpetas as $c): ?>
    <a href="panel.php?idCarpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>" class="carpeta-item <?= Security::escapeHtml($idCarpetaActiva == $c['idCarpeta'] ? 'activa' : '') ?>">
      <span class="carpeta-punto" style="background:<?= Security::escapeHtml(htmlspecialchars($c['color'])) ?>;"></span>
      <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></span>
      <span class="carpeta-count"><?= Security::escapeHtml($c['totalEjercicios'] ) ?></span>
    </a>
    <?php endforeach; ?>

    <?php if (empty($carpetas)): ?>
    <p class="texto-suave" style="padding:16px 18px;font-size:0.8rem;">Sin carpetas aún</p>
    <?php endif; ?>

    <!-- Borrar carpeta activa -->
    <?php if ($idCarpetaActiva > 0): ?>
    <?php $carpetaActual = obtenerCarpetaPorId($idCarpetaActiva); ?>
    <?php if ($carpetaActual && $carpetaActual['idProfesor'] =<?= Security::escapeHtml($idProfesor): ) ?>
    <div style="padding:10px 14px;border-top:1px solid #f1f5f9;">
      <a href="../../../controladores/profesores/carpetas/borrar.php?id=<?= Security::escapeHtml($idCarpetaActiva ) ?>"
         class="texto-suave" style="font-size:0.75rem;display:flex;align-items:center;gap:6px;color:#ef4444;"
         onclick="return confirm('¿Eliminar carpeta y sus ejercicios asociados?')">
        <i class="fas fa-trash"></i> Eliminar carpeta
      </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- EJERCICIOS -->
  <div>
    <?php if (empty($ejercicios)): ?>
    <div class="panel" style="text-align:center;padding:60px 20px;">
      <i class="fas fa-book-open" style="font-size:3rem;color:#e2e8f0;margin-bottom:16px;display:block;"></i>
      <p class="texto-suave">No hay ejercicios aún.</p>
      <a href="agregar.php<?= Security::escapeHtml($idCarpetaActiva ? '?idCarpeta='.$idCarpetaActiva : '') ?>" class="boton-primario" style="margin-top:16px;display:inline-flex;">
        <i class="fas fa-plus"></i> Crear primer ejercicio
      </a>
    </div>
    <?php else: ?>
    <div class="ejercicios-grid">
      <?php foreach ($ejercicios as $ej):
        $hoy = new DateTime();
        $limite = $ej['fechaLimite'] ? new DateTime($ej['fechaLimite']) : null;
        $claseUrgencia = '';
        $textoFecha = 'Sin límite';
        if ($limite) {
          $diff = $hoy->diff($limite);
          $textoFecha = $limite->format('d/m/Y H:i');
          if ($limite < $hoy) $claseUrgencia = 'urgente';
          elseif ($diff->days <= 3) $claseUrgencia = 'pronto';
        }
      ?>
      <div class="ejercicio-card">
        <?php if ($ej['nombreCarpeta']): ?>
        <span class="ejercicio-card-carpeta" style="background:<?= Security::escapeHtml(htmlspecialchars($ej['colorCarpeta'])) ?>22;color:<?= Security::escapeHtml(htmlspecialchars($ej['colorCarpeta'])) ?>;">
          <i class="fas fa-folder" style="font-size:0.65rem;"></i>
          <?= Security::escapeHtml(htmlspecialchars($ej['nombreCarpeta'])) ?>
        </span>
        <?php endif; ?>

        <p class="ejercicio-card-titulo"><?= Security::escapeHtml(htmlspecialchars($ej['titulo'])) ?></p>

        <?php if ($ej['descripcion']): ?>
        <p class="ejercicio-card-desc"><?= Security::escapeHtml(htmlspecialchars($ej['descripcion'])) ?></p>
        <?php endif; ?>

        <div class="ejercicio-card-footer">
          <span class="ejercicio-fecha <?= Security::escapeHtml($claseUrgencia ) ?>">
            <i class="fas fa-clock"></i> <?= Security::escapeHtml($textoFecha ) ?>
          </span>
          <span style="font-size:0.75rem;color:#94a3b8;font-weight:600;">
            <i class="fas fa-users"></i> <?= Security::escapeHtml($ej['totalEntregas'] ) ?> entregas
          </span>
        </div>

        <div class="caja alinear-centro espacio-pequeno" style="margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;">
          <a href="editar.php?id=<?= Security::escapeHtml($ej['idEjercicio'] ) ?>" class="boton-secundario btn-pequeno" style="flex:1;justify-content:center;">
            <i class="fas fa-edit"></i> Editar
          </a>
          <a href="entregas.php?id=<?= Security::escapeHtml($ej['idEjercicio'] ) ?>" class="boton-primario btn-pequeno" style="flex:1;justify-content:center;">
            <i class="fas fa-inbox"></i> Entregas
          </a>
          <a href="../../../controladores/profesores/ejercicios/borrar.php?id=<?= Security::escapeHtml($ej['idEjercicio'] ) ?>"
             class="btn-accion btn-eliminar" title="Eliminar"
             onclick="return confirm('¿Eliminar este ejercicio y todas sus entregas?')">
            <i class="fas fa-trash"></i>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- MODAL NUEVA CARPETA -->
<div id="modalCarpeta" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:32px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:24px;">
      <h2 style="font-size:1.1rem;font-weight:700;color:#1e293b;">Nueva Carpeta</h2>
      <button onclick="document.getElementById('modalCarpeta').style.display='none'" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/carpetas/insertar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="campo">
        <label>Nombre *</label>
        <input type="text" name="nombre" placeholder="Ej: Ejercicios PHP" required maxlength="100">
      </div>
      <div class="campo">
        <label>Ciclo *</label>
        <select name="idCiclo" required>
          <option value="">-- Seleccionar --</option>
          <?php foreach ($ciclos as $c): ?>
          <option value="<?= Security::escapeHtml($c['idCiclo'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($c['nombreCiclo'])) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="campo">
        <label>Color</label>
        <div class="caja alinear-centro espacio-pequeno" style="flex-wrap:wrap;gap:8px;margin-top:6px;">
          <?php foreach (['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444','#ec4899','#0ea5e9','#14b8a6'] as $col): ?>
          <label style="cursor:pointer;">
            <input type="radio" name="color" value="<?= Security::escapeHtml($col ) ?>" <?= Security::escapeHtml($col === '#0ea5e9' ? 'checked' : '') ?> style="display:none;">
            <span style="display:block;width:26px;height:26px;border-radius:50%;background:<?= Security::escapeHtml($col ) ?>;border:2px solid transparent;transition:border-color 0.15s;"
                  onclick="this.previousElementSibling.checked=true;document.querySelectorAll('[name=color]+span').forEach(s=>s.style.borderColor='transparent');this.style.borderColor='#1e293b';"></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="campo">
        <label>Descripción</label>
        <input type="text" name="descripcion" placeholder="Opcional" maxlength="255">
      </div>
      <input type="hidden" name="icono" value="fa-folder">
      <input type="submit" name="guardarCarpeta" class="boton-primario" value="Crear Carpeta" style="width:100%;margin-top:8px;">
    </form>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


