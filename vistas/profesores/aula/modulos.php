<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idCiclo = intval($_GET['idCiclo'] ?? 0);
if ($idCiclo < 1) { header("Location: index.php"); exit; }

$ciclo   = null;
$ciclos  = listarCiclosDeProfesor($idProfesor);
foreach ($ciclos as $c) { if ($c['idCiclo'] == $idCiclo) { $ciclo = $c; break; } }
if (!$ciclo) { header("Location: index.php"); exit; }

$modulos = listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo);

$tituloDelPagina = "AULAPRO | " . strtoupper($ciclo['nombreCiclo']);
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="aula-breadcrumb">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="sep">/</span>
  <span class="actual"><?= htmlspecialchars($ciclo['nombreCiclo']) ?></span>
</div>

<div class="cabecera">
  <div>
    <h1><?= htmlspecialchars(mb_strtoupper($ciclo['nombreCiclo'], 'UTF-8')) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= htmlspecialchars($ciclo['nombreNivel'] ?? '') ?> · <?= count($modulos) ?> módulos</p>
  </div>
  <a href="index.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Ciclos</a>
</div>

<?php if (empty($modulos)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;margin-top:20px;">
  <i class="fas fa-cubes" style="font-size:3rem;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
  <p class="texto-suave">No tienes módulos asignados en este ciclo.</p>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-top:20px;">
  <?php foreach ($modulos as $modulo):
    $nArchivos = contarArchivosPorModuloAula($modulo['idModulo']);
    $nTareas   = count(listarTodasTareasPorModuloAula($modulo['idModulo']));
  ?>
  <a href="modulo.php?id=<?= $modulo['idModulo'] ?>" class="ejercicio-card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
      <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#0ea5e9,#0ea5e9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;">
        <i class="fas fa-book"></i>
      </div>
      <div style="flex:1;min-width:0;">
        <p class="ejercicio-card-titulo" style="margin:0;"><?= htmlspecialchars($modulo['nombreModulo']) ?></p>
      </div>
    </div>
    <div class="ejercicio-card-footer">
      <span class="ejercicio-fecha"><i class="fas fa-file"></i> <?= $nArchivos ?> archivos</span>
      <span class="ejercicio-fecha"><i class="fas fa-tasks"></i> <?= $nTareas ?> tareas</span>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
