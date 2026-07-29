      <?php endforeach; ?>
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../include/MfaService.php';
Security::initSession();

$actor = MfaService::sesionActual();
if (!$actor) {
    header('Location: ../login.php');
    exit;
}
$codes = $_SESSION['mfa_backup_plain'] ?? null;
if (!$codes || !is_array($codes)) {
    header('Location: ' . $actor['home']);
    exit;
}
unset($_SESSION['mfa_backup_plain']); // se muestran una sola vez
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Códigos de respaldo — AulaPro</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
<style>
  :root { --primary:#4f46e5; --primary-strong:#3730a3; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; background:#f3f4f6; color:#1f2937;
         display:flex; min-height:100vh; align-items:center; justify-content:center; padding:20px; }
  .card { background:#fff; width:100%; max-width:440px; border-radius:16px; padding:32px 30px;
          box-shadow:0 10px 40px rgba(0,0,0,.08); }
  .brand { font-weight:800; font-size:18px; color:var(--primary); margin-bottom:14px; }
  .brand b { color:var(--primary-strong); }
  h1 { font-size:20px; margin:0 0 6px; }
  p.sub { margin:0 0 18px; color:#6b7280; font-size:14px; }
  .warn { background:#fffbeb; border:1px solid #fde68a; color:#92400e; padding:11px 14px; border-radius:9px;
          font-size:13px; margin-bottom:16px; }
  .codes { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:6px 0 18px; }
  .codes span { font-family:ui-monospace,Menlo,Consolas,monospace; font-size:16px; letter-spacing:2px;
                background:#f9fafb; border:1px solid #eef2ff; border-radius:8px; padding:10px; text-align:center; }
  a.btn { display:block; text-align:center; margin-top:8px; padding:12px; border-radius:9px; background:var(--primary);
          color:#fff; font-size:15px; font-weight:600; text-decoration:none; }
  a.btn:hover { background:var(--primary-strong); }
</style>
</head>
<body>
  <div class="card">
    <div class="brand">Aula<b>Pro</b></div>
    <h1>Guarda tus códigos de respaldo</h1>
    <p class="sub">Te permiten acceder si pierdes el teléfono. Cada uno sirve una sola vez.</p>
    <div class="warn"><i class="fas fa-triangle-exclamation"></i> No se volverán a mostrar. Guárdalos en un lugar seguro <b>ahora</b>.</div>
    <div class="codes">
        <span><?= Security::escapeHtml($codigo) ?></span>
    </div>
    <a class="btn" href="<?= Security::escapeHtml($actor['home']) ?>">Ya los he guardado, continuar</a>
  </div>
</body>
</html>
