<?php
session_start();
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";

$favoritos = listarFavoritosEstudianteAula($idEstudiante);

$tituloDelPagina = "AULAPRO | FAVORITOS";
$seccionActual   = 'aula_favoritos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-star"></i> MIS FAVORITOS</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Acceso rápido a los recursos que has marcado</p>
  </div>
  <a href="recursos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Recursos</a>
</div>

<?php if (empty($favoritos)): ?>
  <div class="recurso-vacio"><i class="fas fa-star"></i><p>Todavía no tienes recursos favoritos. Márcalos con la estrella ⭐ desde cualquier módulo.</p></div>
<?php else: ?>
<table class="recurso-lista">
  <thead><tr><th>Nombre</th><th>Módulo</th><th>Profesor</th><th>Tamaño</th><th style="text-align:right;">Acciones</th></tr></thead>
  <tbody>
    <?php foreach ($favoritos as $a):
      [$cls, $ico] = iconoArchivoAula($a['extension']);
      $previa = archivoPrevisualizableAula($a['extension']);
      $verUrl = "../../../controladores/aula/verArchivo.php?id=" . $a['idArchivo'];
    ?>
    <tr>
      <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= $cls ?>"><i class="fas <?= $ico ?>"></i></span><?= htmlspecialchars($a['nombreOriginal']) ?></div></td>
      <td><?= htmlspecialchars($a['nombreModulo']) ?></td>
      <td><?= htmlspecialchars($a['nombreProfesor']) ?></td>
      <td><?= formatearTamanioAula($a['tamanio']) ?></td>
      <td style="text-align:right;">
        <div class="recurso-menu-wrap">
          <button type="button" class="recurso-menu-btn" title="Opciones" onclick="AulaRecursos.menu(this)"><i class="fas fa-ellipsis-vertical"></i></button>
          <div class="recurso-menu">
            <?php if ($previa): ?>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.verDocumento('<?= $verUrl ?>&modo=ver', <?= htmlspecialchars(json_encode($a['nombreOriginal']), ENT_QUOTES) ?>, '<?= $a['extension'] ?>')"><i class="fas fa-eye"></i> Ver</button>
            <?php endif; ?>
            <a class="recurso-menu-item" href="<?= $verUrl ?>&modo=descarga"><i class="fas fa-download"></i> Descargar</a>
            <button type="button" class="recurso-menu-item" onclick="AulaRecursos.copiarEnlace('<?= $verUrl ?>&modo=ver')"><i class="fas fa-link"></i> Copiar enlace</button>
            <a class="recurso-menu-item peligro" href="../../../controladores/estudiantes/aula/toggleFavorito.php?idArchivo=<?= $a['idArchivo'] ?>&origen=favoritos" onclick="return AulaRecursos.loaderGo()"><i class="fas fa-star"></i> Quitar de favoritos</a>
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

<script src="../../../public/js/aula-recursos.js?v=<?= @filemtime(__DIR__."/../../../public/js/aula-recursos.js") ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
