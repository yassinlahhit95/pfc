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
      <td>
        <div class="recurso-acciones-fila" style="justify-content:flex-end;">
          <a class="recurso-accion favorito activo" title="Quitar de favoritos" href="../../../controladores/estudiantes/aula/toggleFavorito.php?idArchivo=<?= $a['idArchivo'] ?>&origen=favoritos"><i class="fas fa-star"></i></a>
          <?php if ($previa): ?>
          <button class="recurso-accion ver" title="Ver" onclick="AulaRecursos.verDocumento('<?= $verUrl ?>&modo=ver','<?= htmlspecialchars(addslashes($a['nombreOriginal']),ENT_QUOTES) ?>','<?= $a['extension'] ?>')"><i class="fas fa-eye"></i></button>
          <?php endif; ?>
          <a class="recurso-accion descargar" title="Descargar" href="<?= $verUrl ?>&modo=descarga"><i class="fas fa-download"></i></a>
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

<script src="../../../public/js/aula-recursos.js"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
