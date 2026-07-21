<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$idsHijos = array_column($hijos, 'idEstudiante');

$exito = '';
$error = '';

// ── Subida de comprobante de pago (mismo flujo que estudiantes/pagos_pendientes.php) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
    $idEstudianteSubida = (int)($_POST['idEstudiante'] ?? 0);
    if (!in_array($idEstudianteSubida, $idsHijos, true)) {
        $error = "No tienes permiso sobre este estudiante.";
    } else {
        $file = $_FILES['comprobante'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = "Error al subir el archivo.";
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
            if (!in_array($ext, $extensionesPermitidas, true)) {
                $error = "Formato no permitido. Sube un PDF o una imagen (JPG/PNG).";
            } elseif ($file['size'] > 8 * 1024 * 1024) {
                $error = "El archivo es demasiado grande (máximo 8 MB).";
            } else {
                $db = obtenerConexion();
                $sqlPago = "SELECT idPago FROM pagos WHERE idEstudiante = ? ORDER BY fechaProximoPago DESC LIMIT 1";
                $stmtPago = mysqli_prepare($db, $sqlPago);
                mysqli_stmt_bind_param($stmtPago, "i", $idEstudianteSubida);
                mysqli_stmt_execute($stmtPago);
                $pagoRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPago));

                if (!$pagoRow) {
                    $error = "No se encontró ningún pago para este estudiante.";
                } else {
                    $dir = __DIR__ . '/../../../public/uploads/comprobantes/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);

                    $filename = "comp_" . $idEstudianteSubida . "_" . time() . "." . $ext;

                    if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                        $sqlUp = "UPDATE pagos SET comprobante = ?, estadoComprobante = 'verificando' WHERE idPago = ?";
                        $stmtUp = mysqli_prepare($db, $sqlUp);
                        mysqli_stmt_bind_param($stmtUp, "si", $filename, $pagoRow['idPago']);
                        mysqli_stmt_execute($stmtUp);
                        $exito = "Comprobante subido. Esperando verificación de Secretaría.";
                    } else {
                        $error = "Error al subir el archivo.";
                    }
                }
            }
        }
    }
}

$titulo_pagina = 'AulaPro Familias — Pagos y Recibos';
$seccion       = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
  <h1>Pagos y Recibos</h1>
</div>

<?php if ($exito): ?>
    <div style="background:var(--verde-suave); color:var(--verde-ink); padding:12px; border-radius:6px; margin-bottom:16px;">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="background:var(--rojo-suave); color:var(--rojo-ink); padding:12px; border-radius:6px; margin-bottom:16px;">
        <i class="fas fa-triangle-exclamation"></i> <?= Security::escapeHtml($error) ?>
    </div>
<?php endif; ?>

<?php if (empty($hijos)): ?>
  <div class="panel">
    <div class="panel-vacio">
      <div class="panel-vacio-icono"><i class="fas fa-credit-card"></i></div>
      <div class="panel-vacio-titulo">Sin estudiantes vinculados</div>
      <div class="panel-vacio-desc">No hay estudiantes vinculados a su cuenta.</div>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($hijos as $hijo):
    $estadoFinan = obtenerEstadoFinancieroEstudiante((int)$hijo['idEstudiante']);
    $pagos       = listarPagosPorEstudiante((int)$hijo['idEstudiante']);
    $pendiente   = max(0, $estadoFinan['restante']);

    $estadoComprobante = null;
    if ($pendiente > 0) {
        $dbPago = obtenerConexion();
        $sqlEstado = "SELECT estadoComprobante FROM pagos WHERE idEstudiante = ? ORDER BY fechaProximoPago DESC LIMIT 1";
        $stmtEstado = mysqli_prepare($dbPago, $sqlEstado);
        mysqli_stmt_bind_param($stmtEstado, "i", $hijo['idEstudiante']);
        mysqli_stmt_execute($stmtEstado);
        $filaEstado = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtEstado));
        $estadoComprobante = $filaEstado['estadoComprobante'] ?? null;
    }
  ?>
  <div class="panel margen-abajo">
    <div class="panel-titulo-seccion"><?= Security::escapeHtml($hijo['nombreEstudiante']) ?> &mdash; <span style="font-weight:400;color:var(--dim)"><?= Security::escapeHtml($hijo['nombreCiclo']) ?></span></div>

    <!-- Financial summary -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:20px">
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Total ciclo actual</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px"><?= number_format($estadoFinan['precioCiclo'], 2) ?> €</div>
      </div>
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Total pagado</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px;color:var(--verde)"><?= number_format($estadoFinan['totalPagado'], 2) ?> €</div>
      </div>
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Pendiente</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px;color:<?= $pendiente > 0 ? 'var(--rojo)' : 'var(--verde)' ?>"><?= number_format($pendiente, 2) ?> €</div>
      </div>
    </div>

    <?php if ($pendiente > 0): ?>
      <div style="background:var(--surface-2); padding:16px; border-radius:8px; border:1px solid var(--border); margin-bottom:20px;">
        <?php if ($estadoComprobante === 'verificando'): ?>
          <p style="color:var(--naranja-ink); font-weight:600; margin:0;"><i class="fas fa-clock"></i> Comprobante en revisión. Se actualizará el estado en breve.</p>
        <?php else: ?>
          <p style="font-size:.85rem; color:var(--dim); margin:0 0 12px;">Si ya se realizó el pago mediante transferencia, sube aquí el comprobante para verificación:</p>
          <form method="POST" enctype="multipart/form-data" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= (int)$hijo['idEstudiante'] ?>">
            <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required
                   style="flex:1;min-width:200px;padding:8px;border:1px dashed var(--border-2);border-radius:6px;background:var(--surface);">
            <button type="submit" class="boton-primario">Subir Comprobante</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- Payment history table -->
    <?php if (empty($pagos)): ?>
      <div class="panel-vacio" style="padding:24px 0">
        <div class="panel-vacio-icono"><i class="fas fa-receipt"></i></div>
        <div class="panel-vacio-titulo">Sin pagos registrados</div>
        <div class="panel-vacio-desc">No se han registrado pagos para este estudiante todavía.</div>
      </div>
    <?php else: ?>
    <div class="contenedor-tabla">
      <table class="tabla-datos" id="tablaPagos<?= (int)$hijo['idEstudiante'] ?>">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th style="text-align:right">Importe</th>
            <th style="text-align:right">Próx. vencimiento</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pagos as $pago): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($pago['fechaPago'])) ?></td>
              <td><?= Security::escapeHtml($pago['tipoPago']) ?></td>
              <td style="text-align:right;font-weight:600"><?= number_format((float)$pago['monto'], 2) ?> €</td>
              <td style="text-align:right;color:var(--dim)">
                <?= !empty($pago['fechaProximoPago']) ? date('d/m/Y', strtotime($pago['fechaProximoPago'])) : '—' ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
