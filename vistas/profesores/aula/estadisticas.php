<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor = $_SESSION['idProfesor'] ?? '';

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
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= Security::escapeHtml($modulo['nombreModulo']) ?></p>
  </div>
  <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<h3 style="margin-top:18px;font-size:.95rem;color:var(--dim);"><i class="fas fa-fire"></i> Recursos más consultados</h3>
<?php if (empty($topRecursos)): ?>
  <div class="recurso-vacio" style="padding: 60px 20px; text-align: center; background:var(--surface); border-radius: 12px; border: 1px dashed var(--border-2);">
    <div class="recurso-vacio-ilus" style="font-size: 3rem; color: var(--mut); margin-bottom: 16px;"><i class="fas fa-chart-line"></i></div>
    <h3 style="margin:0 0 8px; color: var(--text); font-size: 1.1rem;">Aún no hay datos de uso</h3>
    <p style="margin:0; color: var(--dim); font-size: 0.95rem;">Tus estudiantes todavía no han interactuado con los archivos de este módulo.</p>
  </div>
<?php else: ?>
<table class="recurso-lista">
  <thead><tr><th>Recurso</th><th>Vistas</th><th>Descargas</th><th>Última consulta</th><th style="text-align:right;">Control de lectura</th></tr></thead>
  <tbody>
    <?php foreach ($topRecursos as $r):
      [$cls, $ico] = iconoArchivoAula($r['extension']); ?>
    <tr>
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= Security::escapeHtml($cls ) ?>"><i class="fas <?= Security::escapeHtml($ico ) ?>"></i></span><?= Security::escapeHtml($r['nombreOriginal']) ?></div></td>
      <td><?= Security::escapeHtml(intval($r['vistas'])) ?></td>
      <td><?= Security::escapeHtml(intval($r['descargas'])) ?></td>
      <td><?= Security::escapeHtml($r['ultimoAcceso'] ? date('d/m/Y H:i', strtotime($r['ultimoAcceso'])) : '—') ?></td>
      <td style="text-align:right;"><a href="estadisticas.php?id=<?= Security::escapeHtml($idModulo ) ?>&archivo=<?= Security::escapeHtml($r['idArchivo'] ) ?>" class="recurso-btn"><i class="fas fa-users"></i> Ver detalle</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<?php if ($archivoSel): ?>
<h3 style="margin-top:26px;font-size:.95rem;color:var(--dim);"><i class="fas fa-book-open-reader"></i> Detalle por estudiante: <?= Security::escapeHtml($archivoSel['nombreOriginal']) ?></h3>
<table class="recurso-lista">
  <thead><tr><th>Estudiante</th><th>¿Lo ha visto?</th><th>¿Lo ha descargado?</th></tr></thead>
  <tbody>
    <?php foreach ($lectura as $e): ?>
    <tr>
      <td><?= Security::escapeHtml($e['nombreEstudiante']) ?></td>
      <td><?php if (!empty($e['fechaVista'])): ?><span class="badge badge-verde"><i class="fas fa-eye"></i> Visto</span> <small class="texto-suave"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($e['fechaVista']))) ?></small><?php else: ?><span class="badge badge-gris">Sin abrir</span><?php endif; ?></td>
      <td><?php if (!empty($e['fechaDescarga'])): ?><span class="badge badge-azul"><i class="fas fa-download"></i> Descargado</span> <small class="texto-suave"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($e['fechaDescarga']))) ?></small><?php else: ?><span class="badge badge-gris">No</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($lectura)): ?><tr><td colspan="3" style="text-align:center; padding:40px 20px; color:var(--mut);"><i class="fas fa-user-graduate" style="font-size:2rem; margin-bottom:12px; display:block; opacity:0.5;"></i>No hay estudiantes matriculados en este ciclo o no hay datos registrados.</td></tr><?php endif; ?>
  </tbody>
</table>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


