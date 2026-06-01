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

// Limpieza automática: elimina definitivamente lo que lleve más de 30 días en la papelera
purgarPapeleraAntiguaAula(30);

$papelera = listarPapeleraModuloAula($idModulo);

$exito   = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

$tituloDelPagina = "AULAPRO | PAPELERA";
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";

function botonesPapelera($tipo, $id, $idModulo) {
    $c = "../../../controladores/profesores/aula/papelera.php";
    return '
    <form method="POST" action="'.$c.'" style="display:inline">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="accion" value="restaurar"><input type="hidden" name="tipo" value="'.$tipo.'">
      <input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="idModulo" value="'.$idModulo.'">
      <button class="recurso-btn-ico" title="Restaurar"><i class="fas fa-rotate-left"></i></button>
    </form>
    <form method="POST" action="'.$c.'" style="display:inline" onsubmit="return confirm(\'Eliminar definitivamente. Esta acción no se puede deshacer. ¿Continuar?\')">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="tipo" value="'.$tipo.'">
      <input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="idModulo" value="'.$idModulo.'">
      <button class="recurso-btn-ico peligro" title="Eliminar definitivamente"><i class="fas fa-trash"></i></button>
    </form>';
}
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-trash-can"></i> PAPELERA</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= Security::escapeHtml(htmlspecialchars($modulo['nombreModulo'])) ?> · los elementos se borran automáticamente a los 30 días</p>
  </div>
  <a href="recursos.php?id=<?= Security::escapeHtml($idModulo ) ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if ($exito): ?><div class="alerta-exito" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i><p><?= Security::escapeHtml(htmlspecialchars($exito)) ?></p></div><?php endif; ?>
<?php if ($errores): ?><div class="alerta-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-circle"></i><p><?= Security::escapeHtml(htmlspecialchars($errores)) ?></p></div><?php endif; ?>

<?php if (empty($papelera['archivos']) && empty($papelera['carpetas'])): ?>
  <div class="recurso-vacio"><i class="fas fa-trash-can"></i><p>La papelera está vacía.</p></div>
<?php else: ?>

  <?php if (!empty($papelera['carpetas'])): ?>
  <h3 style="margin-top:18px;font-size:.95rem;color:#475569;">Carpetas</h3>
  <table class="recurso-lista">
    <thead><tr><th>Nombre</th><th>Eliminada</th><th style="text-align:right;">Acciones</th></tr></thead>
    <tbody>
      <?php foreach ($papelera['carpetas'] as $c): ?>
      <tr>
        <td><div class="recurso-archivo-nombre"><span class="recurso-carpeta-icono" style="background:<?= Security::escapeHtml(htmlspecialchars($c['color'])) ?>;width:30px;height:30px;font-size:.8rem;"><i class="fas <?= Security::escapeHtml(htmlspecialchars($c['icono'])) ?>"></i></span><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></div></td>
        <td><?= Security::escapeHtml($c['fechaEliminacion'] ? date('d/m/Y H:i', strtotime($c['fechaEliminacion'])) : '—') ?></td>
        <td><div class="recurso-acciones-fila" style="justify-content:flex-end;"><?= Security::escapeHtml(botonesPapelera('carpeta', $c['idCarpeta'], $idModulo)) ?></div></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <?php if (!empty($papelera['archivos'])): ?>
  <h3 style="margin-top:18px;font-size:.95rem;color:#475569;">Archivos</h3>
  <table class="recurso-lista">
    <thead><tr><th>Nombre</th><th>Carpeta</th><th>Eliminado</th><th style="text-align:right;">Acciones</th></tr></thead>
    <tbody>
      <?php foreach ($papelera['archivos'] as $a):
        [$cls, $ico] = iconoArchivoAula($a['extension']); ?>
      <tr>
        <td><div class="recurso-archivo-nombre"><span class="recurso-archivo-icono <?= Security::escapeHtml($cls ) ?>"><i class="fas <?= Security::escapeHtml($ico ) ?>"></i></span><?= Security::escapeHtml(htmlspecialchars($a['nombreOriginal'])) ?></div></td>
        <td><?= Security::escapeHtml(htmlspecialchars($a['nombreCarpeta'] ?? '—')) ?></td>
        <td><?= Security::escapeHtml($a['fechaEliminacion'] ? date('d/m/Y H:i', strtotime($a['fechaEliminacion'])) : '—') ?></td>
        <td><div class="recurso-acciones-fila" style="justify-content:flex-end;"><?= Security::escapeHtml(botonesPapelera('archivo', $a['idArchivo'], $idModulo)) ?></div></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


