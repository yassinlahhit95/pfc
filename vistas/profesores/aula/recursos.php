<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idModulo     = intval($_GET['id'] ?? 0);
$carpetaActual = intval($_GET['carpeta'] ?? 0) ?: null;

// Permiso: el profesor sólo puede acceder a módulos que imparte
$misModulos = listarModulosDeProfesor($idProfesor);
$idsMios    = array_column($misModulos, 'idModulo');
if ($idModulo < 1 || !in_array($idModulo, $idsMios)) { header("Location: index.php"); exit; }

$modulo = obtenerModuloPorId($idModulo);
$idCiclo = $modulo['idCiclo'];

// Carpeta actual (si navegamos dentro de una subcarpeta) y su validez
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
    // Archivos en la raíz del módulo (sin carpeta)
    $archivos = array_values(array_filter(listarArchivosPorModuloAula($idModulo), function($a) {
        return empty($a['idCarpeta']);
    }));
}

// Todas las carpetas (para el selector de "mover")
$todasCarpetas = listarCarpetasPorModuloAula($idModulo);

// Almacenamiento del ciclo
$usado  = obtenerUsoAlmacenamientoCicloAula($idCiclo);
$limite = obtenerLimiteAlmacenamientoCicloAula($idCiclo);
$pct    = $limite > 0 ? min(100, round($usado / $limite * 100)) : 0;

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

$COLORES = ['#0ea5e9','#6366f1','#8b5cf6','#ec4899','#ef4444','#f59e0b','#22c55e','#14b8a6','#64748b'];
$ICONOS  = ['fa-folder','fa-book','fa-code','fa-file-lines','fa-flask','fa-laptop-code','fa-database','fa-paint-brush','fa-calculator','fa-globe'];

$tituloDelPagina = "AULAPRO | RECURSOS · " . strtoupper($modulo['nombreModulo']);
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-folder-open"></i> <?= Security::escapeHtml(htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8'))) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Gestión de recursos educativos</p>
  </div>
  <div class="grupo-botones">
    <a href="estadisticas.php?id=<?= Security::escapeHtml($idModulo ) ?>" class="boton-secundario" title="Estadísticas y control de lectura"><i class="fas fa-chart-bar"></i></a>
    <button type="button" class="boton-secundario" onclick="document.getElementById('hPadre').value='<?= Security::escapeHtml($carpetaActual ) ?>';AulaRecursos.abrirModal('modalCarpeta')"><i class="fas fa-folder-plus"></i> Carpeta</button>
    <button type="button" class="boton-primario" onclick="AulaRecursos.abrirModal('modalSubir')"><i class="fas fa-cloud-arrow-up"></i> Subir</button>
  </div>
</div>

<!-- Barra de almacenamiento del ciclo -->
<div class="panel" style="padding:12px 16px;margin-top:12px;">
  <div style="display:flex;justify-content:space-between;font-size:.8rem;color:#64748b;margin-bottom:6px;">
    <span><i class="fas fa-hard-drive"></i> Almacenamiento del ciclo</span>
    <span><?= Security::escapeHtml(formatearTamanioAula($usado)) ?> / <?= Security::escapeHtml(formatearTamanioAula($limite)) ?> (<?= Security::escapeHtml($pct ) ?>%)</span>
  </div>
  <div class="recurso-almacenamiento-barra"><span style="width:<?= Security::escapeHtml($pct ) ?>%"></span></div>
</div>

<!-- Migas de pan -->
<div class="recurso-breadcrumb" data-modulo="<?= Security::escapeHtml($idModulo ) ?>" data-carpeta="<?= Security::escapeHtml($carpetaActual ) ?>" data-csrf="<?= Security::generateCSRFToken() ?>">
  <a href="index.php"><i class="fas fa-home"></i></a>
  <span class="sep">/</span>
  <a href="modulos.php?idCiclo=<?= Security::escapeHtml($idCiclo ) ?>"><?= Security::escapeHtml(htmlspecialchars($modulo['nombreModulo'])) ?></a>
  <?php if ($carpetaActual): ?>
    <span class="sep">/</span>
    <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>" data-drop-carpeta="0" title="Arrastra aquí para mover a la raíz">Raíz</a>
    <?php foreach ($ruta as $r): ?>
      <span class="sep">/</span>
      <?php if ($r['idCarpeta'] == $carpetaActual): ?>
        <span class="actual"><?= Security::escapeHtml($r['nombre']) ?></span>
      <?php else: ?>
        <a href="recursos.php?id=<?= Security::escapeHtml($idModulo) ?>&carpeta=<?= Security::escapeHtml($r['idCarpeta']) ?>" data-drop-carpeta="<?= Security::escapeHtml($r['idCarpeta']) ?>"><?= Security::escapeHtml($r['nombre']) ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php if ($exito): ?><div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i><p><?= Security::escapeHtml($exito) ?></p></div><?php endif; ?>
<?php if ($errores): ?><div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-circle"></i><p><?= Security::escapeHtml($errores) ?></p></div><?php endif; ?>

<!-- Carpetas -->
<?php if (!empty($carpetas)): ?>
<div class="recurso-carpetas-grid">
  <?php foreach ($carpetas as $c): ?>
  <div class="recurso-carpeta<?= $c['fijado'] ? ' fijado' : '' ?>" data-drop-carpeta="<?= Security::escapeHtml($c['idCarpeta']) ?>"<?php if ($c['idProfesor'] == $idProfesor): ?> draggable="true" data-drag-tipo="carpeta" data-drag-id="<?= Security::escapeHtml($c['idCarpeta']) ?>"<?php endif; ?>>
    <a class="recurso-carpeta-link" draggable="false" href="recursos.php?id=<?= Security::escapeHtml($idModulo) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta']) ?>">
      <div class="recurso-carpeta-icono" style="background:<?= Security::escapeHtml($c['color']) ?>"><i class="fas <?= Security::escapeHtml($c['icono']) ?>"></i></div>
      <span class="recurso-carpeta-nombre"><?php if ($c['fijado']): ?><i class="fas fa-thumbtack recurso-pin-ind" title="Fijado"></i> <?php endif; ?><?= Security::escapeHtml($c['nombre']) ?></span>
      <span class="recurso-carpeta-meta"><?= Security::escapeHtml($c['totalSubcarpetas']) ?> carpetas · <?= Security::escapeHtml($c['totalArchivos']) ?> archivos</span>
    </a>
    <div class="recurso-carpeta-acciones">
      <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
      <div class="recurso-menu">
        <a class="recurso-menu-item" href="recursos.php?id=<?= Security::escapeHtml($idModulo) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta']) ?>"><i class="fas fa-folder-open"></i> Abrir</a>
        <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('recursos.php?id=<?= Security::escapeHtml($idModulo) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta']) ?>')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg> Copiar enlace</button>
        <?php if ($c['idProfesor'] == $idProfesor): ?>
        <button type="button" class="recurso-menu-item" onclick="AulaRecursos.editarCarpeta(<?= Security::escapeHtml($c['idCarpeta']) ?>, <?= Security::escapeHtml(json_encode($c['nombre'])) ?>, '<?= Security::escapeHtml($c['color']) ?>', '<?= Security::escapeHtml($c['icono']) ?>')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path></svg> Renombrar / Editar</button>
        <button type="button" class="recurso-menu-item" onclick="AulaRecursos.pin('carpeta', <?= Security::escapeHtml($c['idCarpeta']) ?>, this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pin"><path d="M12 17v5"></path><path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z"></path></svg> <span class="recurso-pin-label"><?= $c['fijado'] ? 'Quitar fijado' : 'Fijar' ?></span></button>
        <div class="recurso-menu-sep"></div>
<form method="POST" action="../../../controladores/profesores/aula/borrarCarpeta.php" style="margin:0" onsubmit="return confirm('¿Eliminar definitivamente esta carpeta y TODO su contenido? Esta acción no se puede deshacer.')">
          <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
          <input type="hidden" name="id" value="<?= Security::escapeHtml($c['idCarpeta']) ?>">
          <input type="hidden" name="modulo" value="<?= Security::escapeHtml($idModulo) ?>">
          <input type="hidden" name="carpeta" value="<?= Security::escapeHtml($carpetaActual) ?>">
          <button type="submit" class="recurso-menu-item peligro"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg> Eliminar</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Archivos -->
<?php if (empty($archivos) && empty($carpetas)): ?>
  <div class="recurso-vacio">
    <div class="recurso-vacio-ilus"><i class="fas fa-cloud-arrow-up"></i></div>
    <h3>Esta carpeta está vacía</h3>
    <p>Sube tus primeros recursos o crea una carpeta para organizarlos.<br>También puedes arrastrar archivos sobre las carpetas para moverlos.</p>
    <div class="recurso-vacio-acciones">
      <button type="button" class="boton-primario" onclick="AulaRecursos.abrirModal('modalSubir')"><i class="fas fa-cloud-arrow-up"></i> Subir archivos</button>
      <button type="button" class="boton-secundario" onclick="document.getElementById('hPadre').value='<?= Security::escapeHtml($carpetaActual ) ?>';AulaRecursos.abrirModal('modalCarpeta')"><i class="fas fa-folder-plus"></i> Nueva carpeta</button>
    </div>
  </div>
<?php elseif (!empty($archivos)): ?>
<table class="recurso-lista">
  <thead>
    <tr><th>Nombre</th><th>Fecha</th><th>Profesor</th><th>Tamaño</th><th style="text-align:right;">Acciones</th></tr>
  </thead>
  <tbody>
    <?php foreach ($archivos as $a):
        [$cls, $ico] = iconoArchivoAula($a['extension']);
        $previa = archivoPrevisualizableAula($a['extension']);
        $esMio  = $a['idProfesor'] == $idProfesor;
        $verUrl = "../../../controladores/aula/verArchivo.php?id=" . $a['idArchivo'];
    ?>
    <tr<?php if ($esMio): ?> draggable="true" data-drag-tipo="archivo" data-drag-id="<?= Security::escapeHtml($a['idArchivo'] ) ?>"<?php endif; ?>>
      <td>
        <div class="recurso-archivo-nombre">
          <span class="recurso-archivo-icono <?= Security::escapeHtml($cls ) ?>"><i class="fas <?= Security::escapeHtml($ico ) ?>"></i></span>
          <span><?php if ($a['fijado']): ?><i class="fas fa-thumbtack recurso-pin-ind" title="Fijado"></i> <?php endif; ?><?= Security::escapeHtml(htmlspecialchars($a['nombreOriginal'])) ?><?php if ($a['version'] > 1): ?><span class="recurso-version-badge" title="Versión <?= Security::escapeHtml($a['version'] ) ?>">v<?= Security::escapeHtml($a['version'] ) ?></span><?php endif; ?></span>
        </div>
      </td>
      <td><?= Security::escapeHtml(date('d/m/Y', strtotime($a['fechaSubida']))) ?></td>
      <td><?= Security::escapeHtml(htmlspecialchars($a['nombreProfesor'])) ?></td>
      <td><?= Security::escapeHtml(formatearTamanioAula($a['tamanio'])) ?></td>
      <td style="text-align:right;">
        <div class="recurso-menu-wrap">
          <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
          <div class="recurso-menu">
            <?php if ($previa): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= Security::escapeHtml($verUrl ) ?>&modo=ver', <?= Security::escapeHtml(json_encode($a['nombreOriginal'])) ?>, '<?= Security::escapeHtml($a['extension'] ) ?>')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= Security::escapeHtml($verUrl ) ?>&modo=descarga"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" x2="12" y1="15" y2="3"></line></svg> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= Security::escapeHtml($verUrl ) ?>&modo=ver')"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg> Copiar enlace</button>
            <?php if ($esMio): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.renombrar(<?= Security::escapeHtml($a['idArchivo'] ) ?>, <?= Security::escapeHtml(json_encode(pathinfo($a['nombreOriginal'], PATHINFO_FILENAME))) ?>)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path><path d="m15 5 4 4"></path></svg> Renombrar</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.nuevaVersion(<?= Security::escapeHtml($a['idArchivo'] ) ?>, <?= Security::escapeHtml(json_encode($a['nombreOriginal'])) ?>)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path></svg> Editar</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.mover(<?= Security::escapeHtml($a['idArchivo'] ) ?>)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-tree"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"></path></svg> Mover</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.pin('archivo', <?= Security::escapeHtml($a['idArchivo'] ) ?>, this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pin"><path d="M12 17v5"></path><path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z"></path></svg> <span class="recurso-pin-label"><?= Security::escapeHtml($a['fijado'] ? 'Quitar fijado' : 'Fijar') ?></span></button>
            <div class="recurso-menu-sep"></div>
<form method="POST" action="../../../controladores/profesores/aula/borrarArchivo.php" style="margin:0" onsubmit="return confirm('¿Eliminar definitivamente este archivo? Esta acción no se puede deshacer.')">
              <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
              <input type="hidden" name="id" value="<?= Security::escapeHtml($a['idArchivo'] ) ?>">
              <input type="hidden" name="modulo" value="<?= Security::escapeHtml($idModulo ) ?>">
              <input type="hidden" name="carpeta" value="<?= Security::escapeHtml($carpetaActual ) ?>">
              <button type="submit" class="recurso-menu-item peligro"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg> Eliminar</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<!-- ════════════ MODALES ════════════ -->

<!-- Nueva carpeta -->
<div id="modalCarpeta" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:480px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-folder-plus"></i> Nueva carpeta</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalCarpeta')">✕</button></div>
    <form method="POST" action="../../../controladores/profesores/aula/crearCarpeta.php" style="padding:18px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
      <input type="hidden" name="idPadre" id="hPadre" value="<?= Security::escapeHtml($carpetaActual ) ?>">
      <span class="modal-label">Nombre</span>
      <input type="text" name="nombre" class="modal-input" placeholder="Ej: Tema 1" required style="width:100%;">
      <span class="modal-label" style="margin-top:12px;">Color</span>
      <div class="selector-colores" data-target="colorCarpeta">
        <?php foreach ($COLORES as $i => $col): ?><span class="swatch <?= Security::escapeHtml($i===0?'activo':'') ?>" data-color="<?= Security::escapeHtml($col ) ?>" style="background:<?= Security::escapeHtml($col ) ?>"></span><?php endforeach; ?>
      </div>
      <input type="hidden" name="color" id="colorCarpeta" value="<?= Security::escapeHtml($COLORES[0] ) ?>">
      <span class="modal-label" style="margin-top:12px;">Icono</span>
      <div class="selector-iconos" data-target="iconoCarpeta">
        <?php foreach ($ICONOS as $i => $ic): ?><button type="button" class="icono-op <?= Security::escapeHtml($i===0?'activo':'') ?>" data-icono="<?= Security::escapeHtml($ic ) ?>"><i class="fas <?= Security::escapeHtml($ic ) ?>"></i></button><?php endforeach; ?>
      </div>
      <input type="hidden" name="icono" id="iconoCarpeta" value="<?= Security::escapeHtml($ICONOS[0] ) ?>">
      <div style="text-align:right;margin-top:18px;"><button type="submit" class="boton-primario"><i class="fas fa-check"></i> Crear</button></div>
    </form>
  </div>
</div>

<!-- Editar carpeta -->
<div id="modalEditarCarpeta" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:480px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-pen"></i> Editar carpeta</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalEditarCarpeta')">✕</button></div>
    <form method="POST" action="../../../controladores/profesores/aula/editarCarpeta.php" style="padding:18px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
      <input type="hidden" name="idCarpeta" id="edCarpetaId">
      <span class="modal-label">Nombre</span>
      <input type="text" name="nombre" id="edCarpetaNombre" class="modal-input" required style="width:100%;">
      <span class="modal-label" style="margin-top:12px;">Color</span>
      <div class="selector-colores" data-target="edColorCarpeta">
        <?php foreach ($COLORES as $col): ?><span class="swatch" data-color="<?= Security::escapeHtml($col ) ?>" style="background:<?= Security::escapeHtml($col ) ?>"></span><?php endforeach; ?>
      </div>
      <input type="hidden" name="color" id="edColorCarpeta" value="<?= Security::escapeHtml($COLORES[0] ) ?>">
      <span class="modal-label" style="margin-top:12px;">Icono</span>
      <div class="selector-iconos" data-target="edIconoCarpeta">
        <?php foreach ($ICONOS as $ic): ?><button type="button" class="icono-op" data-icono="<?= Security::escapeHtml($ic ) ?>"><i class="fas <?= Security::escapeHtml($ic ) ?>"></i></button><?php endforeach; ?>
      </div>
      <input type="hidden" name="icono" id="edIconoCarpeta" value="<?= Security::escapeHtml($ICONOS[0] ) ?>">
      <div style="text-align:right;margin-top:18px;"><button type="submit" class="boton-primario"><i class="fas fa-check"></i> Guardar</button></div>
    </form>
  </div>
</div>

<!-- Subir archivos -->
<div id="modalSubir" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:520px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-cloud-arrow-up"></i> Subir archivos</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalSubir')">✕</button></div>
    <form method="POST" action="../../../controladores/profesores/aula/subirArchivos.php" enctype="multipart/form-data" style="padding:18px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="subirArchivos" value="1">
      <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
      <input type="hidden" name="idCarpeta" value="<?= Security::escapeHtml($carpetaActual ) ?>">
      <span class="modal-label">Archivos (máx. 20 MB c/u)</span>
      <input type="file" name="archivos[]" multiple required class="modal-input" style="width:100%;"
             data-max-size="20971520"
             accept=".pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.ods,.csv,.ppt,.pptx,.odp,.jpg,.jpeg,.png,.gif,.webp,.svg,.zip,.rar">
      <p class="texto-suave" style="font-size:.75rem;margin-top:6px;">PDF, Word, Excel, PowerPoint, imágenes y otros documentos académicos.</p>
      <span class="modal-label" style="margin-top:12px;">Título (opcional)</span>
      <input type="text" name="titulo" class="modal-input" style="width:100%;" placeholder="Si lo dejas vacío, se conserva el nombre del archivo">
      <p class="texto-suave" style="font-size:.72rem;margin-top:6px;">Si subes varios archivos con el mismo título se numerarán automáticamente (Título, Título (2)…).</p>
      <div style="text-align:right;margin-top:18px;"><button type="submit" class="boton-primario"><i class="fas fa-upload"></i> Subir</button></div>
    </form>
  </div>
</div>

<!-- Renombrar archivo -->
<div id="modalRenombrar" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:440px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-i-cursor"></i> Renombrar archivo</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalRenombrar')">✕</button></div>
    <form method="POST" action="../../../controladores/profesores/aula/renombrarArchivo.php" style="padding:18px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
      <input type="hidden" name="idArchivo" id="rnId">
      <span class="modal-label">Nuevo nombre</span>
      <input type="text" name="nombre" id="rnNombre" class="modal-input" required style="width:100%;">
      <div style="text-align:right;margin-top:18px;"><button type="submit" class="boton-primario"><i class="fas fa-check"></i> Renombrar</button></div>
    </form>
  </div>
</div>

<!-- Editar archivo (reemplazar, conservando versiones) -->
<div id="modalVersion" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:460px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-pen-to-square"></i> Editar archivo</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalVersion')">✕</button></div>
    <form method="POST" action="../../../controladores/profesores/aula/subirVersion.php" enctype="multipart/form-data" style="padding:18px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
      <input type="hidden" name="idArchivo" id="verId">
      <p class="texto-suave" style="font-size:.8rem;">Reemplazar: <strong id="verNombre"></strong>. La versión anterior se conserva en el historial.</p>
      <span class="modal-label" style="margin-top:10px;">Nuevo archivo (máx. 20 MB)</span>
      <input type="file" name="archivo" required class="modal-input" style="width:100%;"
             data-max-size="20971520"
             accept=".pdf,.doc,.docx,.txt,.rtf,.odt,.xls,.xlsx,.ods,.csv,.ppt,.pptx,.odp,.jpg,.jpeg,.png,.gif,.webp,.svg,.zip,.rar">
      <div style="text-align:right;margin-top:18px;"><button type="submit" class="boton-primario"><i class="fas fa-check"></i> Guardar cambios</button></div>
    </form>
  </div>
</div>

<!-- Mover archivo -->
<div id="modalMover" class="recurso-visor-overlay">
  <div class="recurso-visor" style="height:auto;max-width:440px;">
    <div class="recurso-visor-cabecera"><h3><i class="fas fa-folder-tree"></i> Mover archivo</h3><button class="recurso-visor-cerrar" onclick="AulaRecursos.cerrarModal('modalMover')">✕</button></div>
    <div style="padding:18px;">
      <span class="modal-label">Carpeta de destino</span>
      <select id="mvCarpeta" class="modal-input" style="width:100%;">
        <option value="0">— Raíz del módulo —</option>
        <?php foreach ($todasCarpetas as $c): ?><option value="<?= Security::escapeHtml($c['idCarpeta'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></option><?php endforeach; ?>
      </select>
      <div style="text-align:right;margin-top:18px;"><button type="button" class="boton-primario" onclick="AulaRecursos.confirmarMover(<?= Security::escapeHtml($idModulo ) ?>)"><i class="fas fa-check"></i> Mover</button></div>
    </div>
  </div>
</div>

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

<!-- Indicador de carga (operaciones asíncronas) -->
<div id="recursoLoader" class="recurso-loader">
  <div class="recurso-loader-caja">
    <div class="loader">
      <div>
        <ul>
          <li>
            <svg fill="currentColor" viewBox="0 0 90 120">
              <path d="M90,0 L90,120 L11,120 C4.92486775,120 0,115.075132 0,109 L0,11 C0,4.92486775 4.92486775,0 11,0 L90,0 Z M71.5,81 L18.5,81 C17.1192881,81 16,82.1192881 16,83.5 C16,84.8254834 17.0315359,85.9100387 18.3356243,85.9946823 L18.5,86 L71.5,86 C72.8807119,86 74,84.8807119 74,83.5 C74,82.1745166 72.9684641,81.0899613 71.6643757,81.0053177 L71.5,81 Z M71.5,57 L18.5,57 C17.1192881,57 16,58.1192881 16,59.5 C16,60.8254834 17.0315359,61.9100387 18.3356243,61.9946823 L18.5,62 L71.5,62 C72.8807119,62 74,60.8807119 74,59.5 C74,58.1192881 72.8807119,57 71.5,57 Z M71.5,33 L18.5,33 C17.1192881,33 16,34.1192881 16,35.5 C16,36.8254834 17.0315359,37.9100387 18.3356243,37.9946823 L18.5,38 L71.5,38 C72.8807119,38 74,36.8807119 74,35.5 C74,34.1192881 72.8807119,33 71.5,33 Z"></path>
            </svg>
          </li>
          <li><svg fill="currentColor" viewBox="0 0 90 120"><path d="M90,0 L90,120 L11,120 C4.92486775,120 0,115.075132 0,109 L0,11 C0,4.92486775 4.92486775,0 11,0 L90,0 Z M71.5,81 L18.5,81 C17.1192881,81 16,82.1192881 16,83.5 C16,84.8254834 17.0315359,85.9100387 18.3356243,85.9946823 L18.5,86 L71.5,86 C72.8807119,86 74,84.8807119 74,83.5 C74,82.1745166 72.9684641,81.0899613 71.6643757,81.0053177 L71.5,81 Z M71.5,57 L18.5,57 C17.1192881,57 16,58.1192881 16,59.5 C16,60.8254834 17.0315359,61.9100387 18.3356243,61.9946823 L18.5,62 L71.5,62 C72.8807119,62 74,60.8807119 74,59.5 C74,58.1192881 72.8807119,57 71.5,57 Z M71.5,33 L18.5,33 C17.1192881,33 16,34.1192881 16,35.5 C16,36.8254834 17.0315359,37.9100387 18.3356243,37.9946823 L18.5,38 L71.5,38 C72.8807119,38 74,36.8807119 74,35.5 C74,34.1192881 72.8807119,33 71.5,33 Z"></path></svg></li>
          <li><svg fill="currentColor" viewBox="0 0 90 120"><path d="M90,0 L90,120 L11,120 C4.92486775,120 0,115.075132 0,109 L0,11 C0,4.92486775 4.92486775,0 11,0 L90,0 Z M71.5,81 L18.5,81 C17.1192881,81 16,82.1192881 16,83.5 C16,84.8254834 17.0315359,85.9100387 18.3356243,85.9946823 L18.5,86 L71.5,86 C72.8807119,86 74,84.8807119 74,83.5 C74,82.1745166 72.9684641,81.0899613 71.6643757,81.0053177 L71.5,81 Z M71.5,57 L18.5,57 C17.1192881,57 16,58.1192881 16,59.5 C16,60.8254834 17.0315359,61.9100387 18.3356243,61.9946823 L18.5,62 L71.5,62 C72.8807119,62 74,60.8807119 74,59.5 C74,58.1192881 72.8807119,57 71.5,57 Z M71.5,33 L18.5,33 C17.1192881,33 16,34.1192881 16,35.5 C16,36.8254834 17.0315359,37.9100387 18.3356243,37.9946823 L18.5,38 L71.5,38 C72.8807119,38 74,36.8807119 74,35.5 C74,34.1192881 72.8807119,33 71.5,33 Z"></path></svg></li>
          <li><svg fill="currentColor" viewBox="0 0 90 120"><path d="M90,0 L90,120 L11,120 C4.92486775,120 0,115.075132 0,109 L0,11 C0,4.92486775 4.92486775,0 11,0 L90,0 Z M71.5,81 L18.5,81 C17.1192881,81 16,82.1192881 16,83.5 C16,84.8254834 17.0315359,85.9100387 18.3356243,85.9946823 L18.5,86 L71.5,86 C72.8807119,86 74,84.8807119 74,83.5 C74,82.1745166 72.9684641,81.0899613 71.6643757,81.0053177 L71.5,81 Z M71.5,57 L18.5,57 C17.1192881,57 16,58.1192881 16,59.5 C16,60.8254834 17.0315359,61.9100387 18.3356243,61.9946823 L18.5,62 L71.5,62 C72.8807119,62 74,60.8807119 74,59.5 C74,58.1192881 72.8807119,57 71.5,57 Z M71.5,33 L18.5,33 C17.1192881,33 16,34.1192881 16,35.5 C16,36.8254834 17.0315359,37.9100387 18.3356243,37.9946823 L18.5,38 L71.5,38 C72.8807119,38 74,36.8807119 74,35.5 C74,34.1192881 72.8807119,33 71.5,33 Z"></path></svg></li>
          <li><svg fill="currentColor" viewBox="0 0 90 120"><path d="M90,0 L90,120 L11,120 C4.92486775,120 0,115.075132 0,109 L0,11 C0,4.92486775 4.92486775,0 11,0 L90,0 Z M71.5,81 L18.5,81 C17.1192881,81 16,82.1192881 16,83.5 C16,84.8254834 17.0315359,85.9100387 18.3356243,85.9946823 L18.5,86 L71.5,86 C72.8807119,86 74,84.8807119 74,83.5 C74,82.1745166 72.9684641,81.0899613 71.6643757,81.0053177 L71.5,81 Z M71.5,57 L18.5,57 C17.1192881,57 16,58.1192881 16,59.5 C16,60.8254834 17.0315359,61.9100387 18.3356243,61.9946823 L18.5,62 L71.5,62 C72.8807119,62 74,60.8807119 74,59.5 C74,58.1192881 72.8807119,57 71.5,57 Z M71.5,33 L18.5,33 C17.1192881,33 16,34.1192881 16,35.5 C16,36.8254834 17.0315359,37.9100387 18.3356243,37.9946823 L18.5,38 L71.5,38 C72.8807119,38 74,36.8807119 74,35.5 C74,34.1192881 72.8807119,33 71.5,33 Z"></path></svg></li>
        </ul>
      </div>
      <span>Procesando...</span>
    </div>
  </div>
</div>
<!-- Zona de arrastre (subir archivos desde el equipo) -->
<div id="recursoDropZone" class="recurso-dropzone">
  <div class="recurso-dropzone-caja">
    <div class="recurso-dropzone-icono"><i class="fas fa-cloud-arrow-up"></i></div>
    <h3>Suelta los archivos para subirlos</h3>
    <p>Se añadirán a la carpeta actual</p>
  </div>
</div>

<!-- Aviso flotante (toast) -->
<div id="recursoToast" class="recurso-toast"></div>

<script src="../../../public/js/aula-recursos.js?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/js/aula-recursos.js")) ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
