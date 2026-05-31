<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idModulo   = intval($_GET['id'] ?? 0);
$misModulos = listarModulosDeProfesor($idProfesor);
$idsMios    = array_column($misModulos, 'idModulo');
if ($idModulo < 1 || !in_array($idModulo, $idsMios)) { header("Location: index.php"); exit; }

$modulo  = obtenerModuloPorId($idModulo);
$idCiclo = $modulo['idCiclo'];

$topRecursos = obtenerRecursosMasConsultadosAula($idModulo, 15);

// Control de lectura del archivo seleccionado
$idArchivoSel = intval($_GET['archivo'] ?? 0);
$lectura = [];
$archivoSel = null;
if ($idArchivoSel > 0) {
    $archivoSel = obtenerArchivoPorId($idArchivoSel);
    if ($archivoSel && $archivoSel['idModulo'] == $idModulo) {
        $lectura = obtenerControlLecturaArchivoAula($idArchivoSel, $idCiclo);
    }
}

$tituloDelPagina = "AULAPRO | ESTADÍSTICAS";
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-chart-bar"></i> ESTADÍSTICAS DE USO</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= htmlspecialchars($modulo['nombreModulo']) ?></p>
  </div>
  <a href="recursos.php?id=<?= $idModulo ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<h3 style="margin-top:18px;font-size:.95rem;color:#475569;"><i class="fas fa-fire"></i> Recursos más consultados</h3>
<?php if (empty($topRecursos)): ?>
  <div class="recurso-vacio"><i class="fas fa-chart-line"></i><p>Aún no hay datos de uso.</p></div>
<?php else: ?>
<table class="recurso-lista">
  <thead><tr><th>Recurso</th><th>Vistas</th><th>Descargas</th><th>Última consulta</th><th style="text-align:right;">Control de lectura</th></tr></thead>
  <tbody>
    <?php foreach ($topRecursos as $r):
      [$cls, $ico] = iconoArchivoAula($r['extension']); ?>
    <tr>
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= $cls ?>"><i class="fas <?= $ico ?>"></i></span><?= htmlspecialchars($r['nombreOriginal']) ?></div></td>
      <td><?= intval($r['vistas']) ?></td>
      <td><?= intval($r['descargas']) ?></td>
      <td><?= $r['ultimoAcceso'] ? date('d/m/Y H:i', strtotime($r['ultimoAcceso'])) : '—' ?></td>
      <td style="text-align:right;"><a href="estadisticas.php?id=<?= $idModulo ?>&archivo=<?= $r['idArchivo'] ?>" class="recurso-btn"><i class="fas fa-users"></i> Ver detalle</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php if ($archivoSel): ?>
<h3 style="margin-top:26px;font-size:.95rem;color:#475569;"><i class="fas fa-book-open-reader"></i> Detalle por estudiante: <?= htmlspecialchars($archivoSel['nombreOriginal']) ?></h3>
<table class="recurso-lista">
  <thead><tr><th>Estudiante</th><th>¿Lo ha visto?</th><th>¿Lo ha descargado?</th></tr></thead>
  <tbody>
    <?php foreach ($lectura as $e): ?>
    <tr>
      <td><?= htmlspecialchars($e['nombreEstudiante']) ?></td>
      <td><?php if (!empty($e['fechaVista'])): ?><span class="badge badge-verde"><i class="fas fa-eye"></i> Visto</span> <small class="texto-suave"><?= date('d/m/Y H:i', strtotime($e['fechaVista'])) ?></small><?php else: ?><span class="badge badge-gris">Sin abrir</span><?php endif; ?></td>
      <td><?php if (!empty($e['fechaDescarga'])): ?><span class="badge badge-azul"><i class="fas fa-download"></i> Descargado</span> <small class="texto-suave"><?= date('d/m/Y H:i', strtotime($e['fechaDescarga'])) ?></small><?php else: ?><span class="badge badge-gris">No</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($lectura)): ?><tr><td colspan="3" class="texto-suave">No hay estudiantes en este ciclo.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
