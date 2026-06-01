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
    <a href="papelera.php?id=<?= Security::escapeHtml($idModulo ) ?>" class="boton-secundario" title="Papelera"><i class="fas fa-trash-can"></i></a>
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
<div class="recurso-breadcrumb" data-modulo="<?= Security::escapeHtml($idModulo ) ?>">
  <a href="index.php"><i class="fas fa-home"></i></a>
  <span class="sep">/</span>
  <a href="modulos.php?idCiclo=<?= Security::escapeHtml($idCiclo ) ?>"><?= Security::escapeHtml(htmlspecialchars($modulo['nombreModulo'])) ?></a>
  <?php if ($carpetaActual): ?>
    <span class="sep">/</span>
    <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>" data-drop-carpeta="0" title="Arrastra aquí para mover a la raíz">Raíz</a>
    <?php foreach ($ruta as $r): ?>
      <span class="sep">/</span>
      <?php if ($r['idCarpeta'] =<?= Security::escapeHtml($carpetaActual): ) ?>
        <span class="actual"><?= Security::escapeHtml(htmlspecialchars($r['nombre'])) ?></span>
      <?php else: ?>
        <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($r['idCarpeta'] ) ?>" data-drop-carpeta="<?= Security::escapeHtml($r['idCarpeta'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($r['nombre'])) ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php if ($exito): ?><div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i><p><?= Security::escapeHtml(htmlspecialchars($exito)) ?></p></div><?php endif; ?>
<?php if ($errores): ?><div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-circle"></i><p><?= Security::escapeHtml(htmlspecialchars($errores)) ?></p></div><?php endif; ?>

<!-- Carpetas -->
<?php if (!empty($carpetas)): ?>
<div class="recurso-carpetas-grid">
  <?php foreach ($carpetas as $c): ?>
  <div class="recurso-carpeta<?= Security::escapeHtml($c['fijado'] ? ' fijado' : '') ?>" data-drop-carpeta="<?= Security::escapeHtml($c['idCarpeta'] ) ?>"<?php if ($c['idProfesor'] =<?= Security::escapeHtml($idProfesor): ) ?> draggable="true" data-drag-tipo="carpeta" data-drag-id="<?= Security::escapeHtml($c['idCarpeta'] ) ?>"<?php endif; ?>>
    <a class="recurso-carpeta-link" draggable="false" href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>">
      <div class="recurso-carpeta-icono" style="background:<?= Security::escapeHtml(htmlspecialchars($c['color'])) ?>"><i class="fas <?= Security::escapeHtml(htmlspecialchars($c['icono'])) ?>"></i></div>
      <span class="recurso-carpeta-nombre"><?php if ($c['fijado']): ?><i class="fas fa-thumbtack recurso-pin-ind" title="Fijado"></i> <?php endif; ?><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></span>
      <span class="recurso-carpeta-meta"><?= Security::escapeHtml($c['totalSubcarpetas'] ) ?> carpetas · <?= Security::escapeHtml($c['totalArchivos'] ) ?> archivos</span>
    </a>
    <div class="recurso-carpeta-acciones">
      <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
      <div class="recurso-menu">
        <a class="recurso-menu-item" href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>"><i class="fas fa-folder-open"></i> Abrir</a>
        <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>')"><i class="fas fa-link"></i> Copiar enlace</button>
        <?php if ($c['idProfesor'] =<?= Security::escapeHtml($idProfesor): ) ?>
        <button type="button" class="recurso-menu-item" onclick="AulaRecursos.editarCarpeta(<?= Security::escapeHtml($c['idCarpeta'] ) ?>, <?= Security::escapeHtml(htmlspecialchars(json_encode($c['nombre']), ENT_QUOTES)) ?>, '<?= Security::escapeHtml($c['color'] ) ?>', '<?= Security::escapeHtml($c['icono'] ) ?>')"><i class="fas fa-pen"></i> Renombrar / Editar</button>
        <a class="recurso-menu-item" href="../../../controladores/profesores/aula/togglePin.php?tipo=carpeta&id=<?= Security::escapeHtml($c['idCarpeta'] ) ?>&modulo=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($carpetaActual ) ?>" onclick="return AulaRecursos.loaderGo()"><i class="fas fa-thumbtack"></i> <?= Security::escapeHtml($c['fijado'] ? 'Quitar fijado' : 'Fijar') ?></a>
        <div class="recurso-menu-sep"></div>
        <a class="recurso-menu-item peligro" href="../../../controladores/profesores/aula/borrarCarpeta.php?id=<?= Security::escapeHtml($c['idCarpeta'] ) ?>&modulo=<?= Security::escapeHtml($idModulo ) ?>" onclick="return confirm('¿Mover esta carpeta y su contenido a la papelera?') && AulaRecursos.loaderGo()"><i class="fas fa-trash"></i> Eliminar</a>
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
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= Security::escapeHtml($verUrl ) ?>&modo=ver', <?= Security::escapeHtml(htmlspecialchars(json_encode($a['nombreOriginal']), ENT_QUOTES)) ?>, '<?= Security::escapeHtml($a['extension'] ) ?>')"><i class="fas fa-eye"></i> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= Security::escapeHtml($verUrl ) ?>&modo=descarga"><i class="fas fa-download"></i> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= Security::escapeHtml($verUrl ) ?>&modo=ver')"><i class="fas fa-link"></i> Copiar enlace</button>
            <?php if ($esMio): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.renombrar(<?= Security::escapeHtml($a['idArchivo'] ) ?>, <?= Security::escapeHtml(htmlspecialchars(json_encode(pathinfo($a['nombreOriginal'], PATHINFO_FILENAME)), ENT_QUOTES)) ?>)"><i class="fas fa-i-cursor"></i> Renombrar</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.nuevaVersion(<?= Security::escapeHtml($a['idArchivo'] ) ?>, <?= Security::escapeHtml(htmlspecialchars(json_encode($a['nombreOriginal']), ENT_QUOTES)) ?>)"><i class="fas fa-pen-to-square"></i> Editar</button>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.mover(<?= Security::escapeHtml($a['idArchivo'] ) ?>)"><i class="fas fa-folder-tree"></i> Mover</button>
            <a class="recurso-menu-item" href="../../../controladores/profesores/aula/togglePin.php?tipo=archivo&id=<?= Security::escapeHtml($a['idArchivo'] ) ?>&modulo=<?= Security::escapeHtml($idModulo ) ?>&carpeta=<?= Security::escapeHtml($carpetaActual ) ?>" onclick="return AulaRecursos.loaderGo()"><i class="fas fa-thumbtack"></i> <?= Security::escapeHtml($a['fijado'] ? 'Quitar fijado' : 'Fijar') ?></a>
            <div class="recurso-menu-sep"></div>
            <a class="recurso-menu-item peligro" href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= Security::escapeHtml($a['idArchivo'] ) ?>&modulo=<?= Security::escapeHtml($idModulo ) ?>" onclick="return confirm('¿Mover este archivo a la papelera?') && AulaRecursos.loaderGo()"><i class="fas fa-trash"></i> Eliminar</a>
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
      <span class="modal-label" style="margin-top:12px;">Descripción (opcional)</span>
      <input type="text" name="descripcion" class="modal-input" style="width:100%;">
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
    <div class="recurso-spinner"></div>
    <p>Procesando…</p>
  </div>
</div>
<!-- Aviso flotante (toast) -->
<div id="recursoToast" class="recurso-toast"></div>

<script src="../../../public/js/aula-recursos.js?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/js/aula-recursos.js")) ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>


