<?php
session_start();
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$estudiante = obtenerEstudiantePorId($idEstudiante);
$idCiclo    = $estudiante['idCiclo'] ?? 0;

$idModulo      = intval($_GET['id'] ?? 0);
$carpetaActual = intval($_GET['carpeta'] ?? 0) ?: null;

$tituloDelPagina = "AULAPRO | RECURSOS";
$seccionActual   = 'aula_recursos';

// ── Vista 1: listado de módulos del ciclo (no se ha elegido módulo) ──
if ($idModulo < 1) {
    $modulos = $idCiclo ? listarModulosPorCiclo($idCiclo) : [];
    include_once __DIR__ . "/../comunes/nav.php";
    ?>
    <div class="cabecera">
      <div>
        <h1><i class="fas fa-folder-open"></i> RECURSOS EDUCATIVOS</h1>
        <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Consulta los materiales de tus módulos</p>
      </div>
      <a href="favoritos.php" class="boton-secundario"><i class="fas fa-star"></i> Favoritos</a>
    </div>
    <?php if (empty($modulos)): ?>
      <div class="recurso-vacio"><i class="fas fa-folder-open"></i><p>No hay módulos disponibles.</p></div>
    <?php else:
      // Colores e iconos variados (misma lógica que la vista del profesor)
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
      <?php foreach ($modulos as $i => $m): $p = $paleta[$i % count($paleta)]; ?>
      <a href="recursos.php?id=<?= Security::escapeHtml($m['idModulo'] ) ?>" class="recurso-ciclo-card <?= Security::escapeHtml($p['clase'] ) ?>">
        <div class="recurso-ciclo-icon"><i class="fas <?= Security::escapeHtml($p['icono'] ) ?>"></i></div>
        <div class="recurso-ciclo-body">
          <h3><?= Security::escapeHtml(htmlspecialchars($m['nombreModulo'])) ?></h3>
          <div class="recurso-ciclo-meta"><span><i class="fas fa-file"></i> <?= Security::escapeHtml(contarArchivosPorModuloAula($m['idModulo'])) ?> archivos</span></div>
        </div>
        <i class="fas fa-chevron-right recurso-ciclo-arrow"></i>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php include __DIR__ . '/../comunes/footer.php'; ?>
    <?php
    exit;
}

// ── Vista 2: explorador de un módulo (sólo lectura) ──
$modulo = obtenerModuloPorId($idModulo);
if (!$modulo || $modulo['idCiclo'] != $idCiclo) { header("Location: recursos.php"); exit; }

$ruta = [];
if ($carpetaActual) {
    $carpetaInfo = obtenerCarpetaAulaPorId($carpetaActual);
    if (!$carpetaInfo || $carpetaInfo['idModulo'] != $idModulo || $carpetaInfo['eliminado']) {
        header("Location: recursos.php?id=$idModulo"); exit;
    }
    $ruta = obtenerRutaCarpetaAula($carpetaActual);
}

$carpetas = listarCarpetasPorPadreAula($idModulo, $carpetaActual);
if ($carpetaActual) {
    $archivos = listarArchivosPorCarpetaAula($carpetaActual);
} else {
    $archivos = array_values(array_filter(listarArchivosPorModuloAula($idModulo), function($a) {
        return empty($a['idCarpeta']);
    }));
}

include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-folder-open"></i> <?= Security::escapeHtml(htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8'))) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Materiales del módulo</p>
  </div>
  <a href="favoritos.php" class="boton-secundario"><i class="fas fa-star"></i> Favoritos</a>
</div>

<div class="recurso-breadcrumb" data-csrf="<?= Security::generateCSRFToken() ?>">
  <a href="recursos.php"><i class="fas fa-home"></i></a>
  <span class="sep">/</span>
  <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>"><?= Security::escapeHtml(htmlspecialchars($modulo['nombreModulo'])) ?></a>
  <?php foreach ($ruta as $r): ?>
    <span class="sep">/</span>
    <?php if ($r['idCarpeta'] == $carpetaActual): ?>
      <span class="actual"><?= Security::escapeHtml(htmlspecialchars($r['nombre'])) ?></span>
    <?php else: ?>
      <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($r['idCarpeta'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($r['nombre'])) ?></a>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<?php if (!empty($carpetas)): ?>
<div class="recurso-carpetas-grid">
  <?php foreach ($carpetas as $c): ?>
  <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>" class="recurso-carpeta">
    <div class="recurso-carpeta-icono" style="background:<?= Security::escapeHtml(htmlspecialchars($c['color'])) ?>"><i class="fas <?= Security::escapeHtml(htmlspecialchars($c['icono'])) ?>"></i></div>
    <span class="recurso-carpeta-nombre"><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></span>
    <span class="recurso-carpeta-meta"><?= Security::escapeHtml($c['totalSubcarpetas'] ) ?> carpetas · <?= Security::escapeHtml($c['totalArchivos'] ) ?> archivos</span>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($archivos) && empty($carpetas)): ?>
  <div class="recurso-vacio"><i class="fas fa-folder-open"></i><p>No hay materiales en esta carpeta.</p></div>
<?php elseif (!empty($archivos)): ?>
<table class="recurso-lista">
  <thead><tr><th>Nombre</th><th>Fecha</th><th>Profesor</th><th>Tamaño</th><th style="text-align:right;">Acciones</th></tr></thead>
  <tbody>
    <?php foreach ($archivos as $a):
      [$cls, $ico] = iconoArchivoAula($a['extension']);
      $previa = archivoPrevisualizableAula($a['extension']);
      $fav    = esFavoritoAula($idEstudiante, $a['idArchivo']);
      $verUrl = "../../../controladores/aula/verArchivo.php?id=" . $a['idArchivo'];
    ?>
    <tr>
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= Security::escapeHtml($cls ) ?>"><i class="fas <?= Security::escapeHtml($ico ) ?>"></i></span><?= Security::escapeHtml(htmlspecialchars($a['nombreOriginal'])) ?></div></td>
      <td><?= Security::escapeHtml(date('d/m/Y', strtotime($a['fechaSubida']))) ?></td>
      <td><?= Security::escapeHtml(htmlspecialchars($a['nombreProfesor'])) ?></td>
      <td><?= Security::escapeHtml(formatearTamanioAula($a['tamanio'])) ?></td>
      <td style="text-align:right;">
        <div class="recurso-menu-wrap">
          <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
          <div class="recurso-menu">
            <?php if ($previa): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= Security::escapeHtml($verUrl ) ?>&modo=ver', <?= Security::escapeHtml(htmlspecialchars(json_encode($a['nombreOriginal']), ENT_QUOTES)) ?>, '<?= Security::escapeHtml($a['extension'] ) ?>')"><i class="fas fa-eye"></i> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= Security::escapeHtml($verUrl ) ?>&modo=descarga"><i class="fas fa-download"></i> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= Security::escapeHtml($verUrl ) ?>&modo=ver')"><i class="fas fa-link"></i> Copiar enlace</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.favorito(<?= Security::escapeHtml($a['idArchivo'] ) ?>, this, 'recursos')"><i class="fas fa-star" style="color:<?= Security::escapeHtml($fav ? '#f59e0b' : '') ?>"></i> <?= Security::escapeHtml($fav ? 'Quitar de favoritos' : 'Añadir a favoritos') ?></button>
          </div>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<!-- Visor de documentos -->
<div id="modalVisor" class="recurso-visor-overlay">
  <div class="recurso-visor">
    <div class="recurso-visor-cabecera">
      <h3 id="visorTitulo"></h3>
      <div style="display:flex;gap:8px;">
        <a id="visorDescargar" class="boton-secundario btn-pequeno" href="#"><i class="fas fa-download"></i></a>
        <button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalVisor')">✕</button>
      </div>
    </div>
    <div class="recurso-visor-cuerpo" id="visorCuerpo"></div>
  </div>
</div>

<div id="recursoLoader" class="recurso-loader"><div class="recurso-loader-caja"><div class="recurso-spinner"></div><p>Procesando…</p></div></div>
<div id="recursoToast" class="recurso-toast"></div>

<script src="../../../public/js/aula-recursos.js?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/js/aula-recursos.js")) ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>


