<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
$idEstudiante = $_SESSION['idEstudiante'] ?? '';

require_once __DIR__ . "/../../../modelos/aula.php";

$favoritos = listarFavoritosEstudianteAula($idEstudiante);

$exito   = $_SESSION['exito'] ?? null;   unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? null; unset($_SESSION['errores']);

$tituloDelPagina = "AULAPRO | FAVORITOS";
$seccionActual   = 'aula_favoritos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-star"></i> MIS FAVORITOS</h1>
    <p class="subtitulo-encabezado">Acceso rápido a los recursos que has marcado</p>
  </div>
  <a href="recursos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Recursos</a>
</div>

<?php if ($exito): ?>
<div class="alerta alerta-exito" style="margin-bottom:var(--gap);"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta alerta-error" style="margin-bottom:var(--gap);"><i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml(is_array($errores) ? implode(', ', $errores) : $errores) ?></div>
<?php endif; ?>

<?php if (empty($favoritos)): ?>
  <div class="recurso-vacio"><i class="fas fa-star"></i><p>Todavía no tienes recursos favoritos. Márcalos con la estrella ⭐ desde cualquier módulo.</p></div>
<?php else: ?>
<table class="recurso-lista" data-csrf="<?= Security::generateCSRFToken() ?>">
  <thead><tr><th>Nombre</th><th>Módulo</th><th>Profesor</th><th>Tamaño</th><th style="text-align:right;">Acciones</th></tr></thead>
  <tbody>
    <?php foreach ($favoritos as $archivo):
      [$cls, $ico] = iconoArchivoAula($archivo['extension']);
      $previa = archivoPrevisualizableAula($archivo['extension']);
      $verUrl = "../../../controladores/aula/verArchivo.php?id=" . $archivo['idArchivo'];
    ?>
    <tr data-archivo-id="<?= Security::escapeHtml($archivo['idArchivo']) ?>">
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= Security::escapeHtml($cls) ?>"><i class="fas <?= Security::escapeHtml($ico) ?>"></i></span><?= Security::escapeHtml($archivo['nombreOriginal']) ?></div></td>
      <td><?= Security::escapeHtml($archivo['nombreModulo']) ?></td>
      <td><?= Security::escapeHtml($archivo['nombreProfesor']) ?></td>
      <td><?= Security::escapeHtml(formatearTamanioAula($archivo['tamanio'])) ?></td>
      <td style="text-align:right;">
        <div class="recurso-menu-wrap">
          <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
          <div class="recurso-menu">
            <?php if ($previa): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= Security::escapeHtml($verUrl) ?>&modo=ver', <?= Security::escapeHtml(json_encode($archivo['nombreOriginal'])) ?>, '<?= Security::escapeHtml($archivo['extension']) ?>')"><i class="fas fa-eye"></i> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= Security::escapeHtml($verUrl) ?>&modo=descarga"><i class="fas fa-download"></i> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= Security::escapeHtml($verUrl) ?>&modo=ver')"><i class="fas fa-link"></i> Copiar enlace</button>
            <button type="button" class="recurso-menu-item peligro" data-quitar-en-desmarcar onclick="AulaRecursos.favorito(<?= Security::escapeHtml($archivo['idArchivo']) ?>, this)"><i class="fas fa-star"></i> Quitar de favoritos</button>
          </div>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

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

