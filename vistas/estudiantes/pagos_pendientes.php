<?php
require_once __DIR__ . "/../../include/EstudianteGuard.php"; // No bloqueará aquí porque no tiene "/aula/" en la ruta.
require_once __DIR__ . "/../../modelos/conectar.php";

$db = obtenerConexion();
$idEstudiante = $_SESSION['idEstudiante'];

// Obtener info del pago
$sql = "SELECT * FROM pagos WHERE idEstudiante = ? ORDER BY fechaProximoPago ASC LIMIT 1";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pago = mysqli_fetch_assoc($res);

if (!$pago || ($pago['fechaProximoPago'] >= date('Y-m-d') && (empty($pago['prorrogaHasta']) || $pago['prorrogaHasta'] >= date('Y-m-d')))) {
    // Si no está vencido, regresarlo al dashboard
    header("Location: /vistas/estudiantes/inicio/dashboard.php");
    exit;
}

$exito = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
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
            $dir = __DIR__ . '/../../public/uploads/comprobantes/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = "comp_" . $idEstudiante . "_" . time() . "." . $ext;

            if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
                $sqlUp = "UPDATE pagos SET comprobante = ?, estadoComprobante = 'verificando' WHERE idPago = ?";
                $stmtUp = mysqli_prepare($db, $sqlUp);
                mysqli_stmt_bind_param($stmtUp, "si", $filename, $pago['idPago']);
                mysqli_stmt_execute($stmtUp);
                $pago['estadoComprobante'] = 'verificando';
                $exito = "Comprobante subido. Esperando verificación de Secretaría.";
            } else {
                $error = "Error al subir el archivo.";
            }
        }
    }
}

$tituloDelPagina = "AulaPro - Pagos Pendientes";
include_once __DIR__ . "/comunes/nav.php";
?>

<div style="max-width:600px; margin: 40px auto; background:var(--surface); padding:30px; border-radius:12px; box-shadow:var(--shadow-md);">
    <div style="text-align:center; color:var(--rojo); margin-bottom:20px;">
        <i class="fas fa-lock" style="font-size:4rem;"></i>
    </div>
    <h2 style="text-align:center; color:var(--text); margin-bottom:10px;">Acceso Restringido</h2>
    <p style="text-align:center; color:var(--dim); margin-bottom:24px;">Tu acceso al Aula Digital ha sido suspendido temporalmente debido a un pago vencido el <b><?= date('d/m/Y', strtotime($pago['fechaProximoPago'])) ?></b>.</p>

    <?php if ($exito): ?>
        <div style="background:var(--verde-suave); color:var(--verde-ink); padding:12px; border-radius:6px; margin-bottom:20px;">
            <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:var(--rojo-suave); color:var(--rojo-ink); padding:12px; border-radius:6px; margin-bottom:20px;">
            <i class="fas fa-triangle-exclamation"></i> <?= Security::escapeHtml($error) ?>
        </div>
    <?php endif; ?>

    <div style="background:var(--surface-2); padding:20px; border-radius:8px; border:1px solid var(--border);">
        <h3 style="margin-top:0; color:var(--text); font-size:1.1rem;">Resolver Situación</h3>
        <?php if ($pago['estadoComprobante'] === 'verificando'): ?>
            <p style="color:var(--naranja-ink); font-weight:600;"><i class="fas fa-clock"></i> Tu comprobante está en revisión. Se restablecerá tu acceso pronto.</p>
        <?php else: ?>
            <p style="font-size:0.9rem; color:var(--dim); margin-bottom:16px;">Si ya realizaste el pago mediante transferencia, por favor sube el comprobante aquí para verificación rápida:</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <div style="margin-bottom:16px;">
                    <input type="file" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" required style="width:100%; padding:10px; border:1px dashed var(--border-2); border-radius:6px; background:var(--surface);">
                </div>
                <button type="submit" style="width:100%; background:var(--accent); color:var(--accent-ink); padding:12px; border:none; border-radius:6px; font-weight:600; cursor:pointer;">
                    Subir Comprobante
                </button>
            </form>
        <?php endif; ?>

        <div style="margin-top:20px; text-align:center;">
            <p style="font-size:0.85rem; color:var(--dim);">Si tienes un inconveniente y necesitas una prórroga, comunícate con Secretaría.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>
