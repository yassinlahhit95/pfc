<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
$idEstudiante = $_SESSION['idEstudiante'] ?? '';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$estudiante = obtenerEstudiantePorId($idEstudiante);
$idCiclo    = $estudiante['idCiclo'] ?? 0;

$idModulo      = intval($_GET['id'] ?? 0);
$carpetaActual = intval($_GET['carpeta'] ?? 0) ?: null;

$titulo_pagina = "Recursos";
$seccionActual   = 'aula_recursos';

// ── Vista 1: listado de módulos del ciclo (no se ha elegido módulo) ──
if ($idModulo < 1) {
    $modulos = $idCiclo ? listarModulosPorCiclo($idCiclo) : [];
    include_once __DIR__ . "/../comunes/nav.php";
    ?>
    <div class="cabecera">
      <div>
        <h1><i class="fas fa-folder-open"></i> RECURSOS EDUCATIVOS</h1>
        <p class="subtitulo-encabezado">Consulta los materiales de tus módulos</p>
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
      <?php foreach ($modulos as $i => $moduloItem): $paletaItem = $paleta[$i % count($paleta)]; ?>
      <a href="recursos.php?id=<?= Security::escapeHtml($moduloItem['idModulo']) ?>" class="recurso-ciclo-card <?= Security::escapeHtml($paletaItem['clase']) ?>">
        <div class="recurso-ciclo-icon"><i class="fas <?= Security::escapeHtml($paletaItem['icono']) ?>"></i></div>
        <div class="recurso-ciclo-body">
          <h3><?= Security::escapeHtml($moduloItem['nombreModulo']) ?></h3>
          <div class="recurso-ciclo-meta"><span><i class="fas fa-file"></i> <?= Security::escapeHtml(contarArchivosPorModuloAula($moduloItem['idModulo'])) ?> archivos</span></div>
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
if (!$modulo || $modulo['idCiclo'] != $idCiclo) { echo "<script>window.location.replace('recursos.php');</script>"; exit; }

$ruta = [];
if ($carpetaActual) {
    $carpetaInfo = obtenerCarpetaAulaPorId($carpetaActual);
    if (!$carpetaInfo || $carpetaInfo['idModulo'] != $idModulo || $carpetaInfo['eliminado']) {
        echo "<script>window.location.replace('recursos.php?id=$idModulo');</script>"; exit;
    }
    $ruta = obtenerRutaCarpetaAula($carpetaActual);
}

$carpetas = listarCarpetasPorPadreAula($idModulo, $carpetaActual);
if ($carpetaActual) {
    $archivos = listarArchivosPorCarpetaAula($carpetaActual);
} else {
    $archivos = array_values(array_filter(listarArchivosPorModuloAula($idModulo), function($archivo) {
        return empty($archivo['idCarpeta']);
    }));
}

// Breadcrumb dinámico: módulo + cadena de carpetas (renderizado por el
// componente global vistas/comunes/breadcrumb.php, incluido dentro de nav.php)
$breadcrumbSectionUrl = 'recursos.php';
$breadcrumbExtra = [
    ['label' => $modulo['nombreModulo'], 'url' => 'recursos.php?id=' . (int)$idModulo],
];
foreach ($ruta as $carpetaMiga) {
    $breadcrumbExtra[] = [
        'label' => $carpetaMiga['nombre'],
        'url'   => 'recursos.php?id=' . (int)$idModulo . '&carpeta=' . (int)$carpetaMiga['idCarpeta'],
    ];
}

include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-folder-open"></i> <?= Security::escapeHtml(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
    <p class="subtitulo-encabezado">Materiales del módulo</p>
  </div>
  <a href="favoritos.php" class="boton-secundario"><i class="fas fa-star"></i> Favoritos</a>
</div>

<?php if (!empty($carpetas)): ?>
<div class="recurso-carpetas-grid">
  <?php foreach ($carpetas as $carpeta): ?>
  <a href="recursos.php?id=<?= Security::escapeHtml($idModulo) ?>&carpeta=<?= Security::escapeHtml($carpeta['idCarpeta']) ?>" class="recurso-carpeta">
    <div class="recurso-carpeta-icono" style="background:<?= Security::escapeHtml($carpeta['color']) ?>"><i class="fas <?= Security::escapeHtml($carpeta['icono']) ?>"></i></div>
    <span class="recurso-carpeta-nombre"><?= Security::escapeHtml($carpeta['nombre']) ?></span>
    <span class="recurso-carpeta-meta"><?= Security::escapeHtml($carpeta['totalSubcarpetas']) ?> carpetas · <?= Security::escapeHtml($carpeta['totalArchivos']) ?> archivos</span>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($archivos) && empty($carpetas)): ?>
  <div class="recurso-vacio"><i class="fas fa-folder-open"></i><p>No hay materiales en esta carpeta.</p></div>
<?php elseif (!empty($archivos)): ?>
<div class="contenedor-tabla">
<table class="recurso-lista">
  <thead><tr><th>Nombre</th><th>Fecha</th><th>Profesor</th><th>Tamaño</th><th style="text-align:right;">Acciones</th></tr></thead>
  <tbody>
    <?php foreach ($archivos as $archivo):
      [$cls, $ico] = iconoArchivoAula($archivo['extension']);
      $previa = archivoPrevisualizableAula($archivo['extension']);
      $fav    = esFavoritoAula($idEstudiante, $archivo['idArchivo']);
      $verUrl = "../../../controladores/aula/verArchivo.php?id=" . $archivo['idArchivo'];
    ?>
    <tr>
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= Security::escapeHtml($cls) ?>"><i class="fas <?= Security::escapeHtml($ico) ?>"></i></span><?= Security::escapeHtml($archivo['nombreOriginal']) ?></div></td>
      <td><?= Security::escapeHtml(date('d/m/Y', strtotime($archivo['fechaSubida']))) ?></td>
      <td><?= Security::escapeHtml($archivo['nombreProfesor']) ?></td>
      <td><?= Security::escapeHtml(formatearTamanioAula($archivo['tamanio'])) ?></td>
      <td style="text-align:right;">
        <div class="recurso-menu-wrap">
          <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
          <div class="recurso-menu">
            <?php if ($previa): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= Security::escapeHtml($verUrl) ?>&modo=ver', <?= Security::escapeHtml(Security::jsonEncodeSafe($archivo['nombreOriginal'])) ?>, '<?= Security::escapeHtml($archivo['extension']) ?>')"><i class="fas fa-eye"></i> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= Security::escapeHtml($verUrl) ?>&modo=descarga"><i class="fas fa-download"></i> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= Security::escapeHtml($verUrl) ?>&modo=ver')"><i class="fas fa-link"></i> Copiar enlace</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.favorito(<?= Security::escapeHtml($archivo['idArchivo']) ?>, this)"><i class="<?= $fav ? 'fas' : 'far' ?> fa-star"></i> <span class="recurso-favorito-label"><?= Security::escapeHtml($fav ? 'Quitar de favoritos' : 'Añadir a favoritos') ?></span></button>
          </div>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
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

<script src="../../../public/js/features/aula-recursos.js?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/js/features/aula-recursos.js")) ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>

