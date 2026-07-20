<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor = $_SESSION['idProfesor'] ?? '';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

$idCiclo = intval($_GET['idCiclo'] ?? 0);
if ($idCiclo < 1) { header("Location: index.php"); exit; }

$ciclo = null;
if ($esTutor && $idCicloTutor) {
    if ($idCiclo !== $idCicloTutor) { header("Location: index.php"); exit; }
    require_once __DIR__ . "/../../../modelos/modulos.php";
    $ciclo    = obtenerCicloPorId($idCicloTutor) ?: null;
    $modulos  = $ciclo ? listarModulosDeCicloConNombre($idCicloTutor) : [];
} else {
    $ciclos = listarCiclosDeProfesor($idProfesor);
    foreach ($ciclos as $cicloItem) { if ($cicloItem['idCiclo'] == $idCiclo) { $ciclo = $cicloItem; break; } }
    if (!$ciclo) { header("Location: index.php"); exit; }
    $modulos = listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
}
if (!$ciclo) { header("Location: index.php"); exit; }

$tituloDelPagina = "AULAPRO | " . strtoupper($ciclo['nombreCiclo']);
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="recurso-breadcrumb">
  <a href="index.php"><i class="fas fa-home"></i> Recursos</a>
  <span class="sep">/</span>
  <span class="actual"><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></span>
</div>

<div class="cabecera">
  <div>
    <h1><?= Security::escapeHtml(mb_strtoupper($ciclo['nombreCiclo'], 'UTF-8')) ?></h1>
    <p class="subtitulo-encabezado"><?= Security::escapeHtml($ciclo['nombreNivel'] ?? '') ?> · <?= Security::escapeHtml(count($modulos)) ?> módulos</p>
  </div>
  <a href="index.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Ciclos</a>
</div>

<?php if (empty($modulos)): ?>
<div class="recurso-vacio">
  <i class="fas fa-cubes"></i>
  <p>No tienes módulos asignados en este ciclo.</p>
</div>
<?php else:
  // Colores e iconos variados para las tarjetas de módulo (clases de color)
  $paleta = [
    ['clase' => 'recurso-card-azul',    'icono' => 'fa-book'],
    ['clase' => 'recurso-card-indigo',  'icono' => 'fa-code'],
    ['clase' => 'recurso-card-rosa',    'icono' => 'fa-database'],
    ['clase' => 'recurso-card-ambar',   'icono' => 'fa-laptop-code'],
    ['clase' => 'recurso-card-verde',   'icono' => 'fa-diagram-project'],
    ['clase' => 'recurso-card-teal',    'icono' => 'fa-server'],
    ['clase' => 'recurso-card-violeta', 'icono' => 'fa-palette'],
  ];
?>
<div class="aula-recursos-grid">
  <?php foreach ($modulos as $i => $modulo):
    $nArchivos = contarArchivosPorModuloAula($modulo['idModulo']);
    $paletaItem = $paleta[$i % count($paleta)];
  ?>
  <a href="recursos.php?id=<?= Security::escapeHtml($modulo['idModulo']) ?>" class="recurso-ciclo-card <?= Security::escapeHtml($paletaItem['clase']) ?>">
    <div class="recurso-ciclo-icon"><i class="fas <?= Security::escapeHtml($paletaItem['icono']) ?>"></i></div>
    <div class="recurso-ciclo-body">
      <h3><?= Security::escapeHtml($modulo['nombreModulo']) ?></h3>
      <div class="recurso-ciclo-meta">
        <span><i class="fas fa-file"></i> <?= Security::escapeHtml($nArchivos) ?> archivos</span>
      </div>
    </div>
    <i class="fas fa-chevron-right recurso-ciclo-arrow"></i>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
