<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();

if (empty($_SESSION['mfa_pending']['id'])) {
    header('Location: ../login.php');
    exit;
}
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['errores']);
$csrf = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Verificación en dos pasos — AulaPro</title>
<style>
  :root { --primary:#4f46e5; --primary-strong:#3730a3; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; background:#f3f4f6; color:#1f2937;
         display:flex; min-height:100vh; align-items:center; justify-content:center; padding:20px; }
  .card { background:#fff; width:100%; max-width:400px; border-radius:16px; padding:34px 30px;
          box-shadow:0 10px 40px rgba(0,0,0,.08); text-align:center; }
  .brand { font-weight:800; font-size:18px; color:var(--primary); margin-bottom:18px; }
  .brand b { color:var(--primary-strong); }
  h1 { font-size:20px; margin:0 0 6px; }
  p.sub { margin:0 0 22px; color:#6b7280; font-size:14px; }
  input { width:100%; padding:13px; border:1px solid #d1d5db; border-radius:9px; font-size:22px;
          text-align:center; letter-spacing:8px; }
  input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.15); }
  button { width:100%; margin-top:18px; padding:12px; border:0; border-radius:9px; background:var(--primary);
           color:#fff; font-size:15px; font-weight:600; cursor:pointer; }
  button:hover { background:var(--primary-strong); }
  .alert { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:11px 14px; border-radius:9px;
           font-size:13px; margin-bottom:14px; }
  .hint { margin-top:16px; font-size:12px; color:#9ca3af; }
  a.logout { color:#6b7280; font-size:12px; text-decoration:none; }
</style>
</head>
<body>
  <form class="card" method="POST" action="../../controladores/auth/mfa_verificar.php" autocomplete="off">
    <div class="brand">Aula<b>Pro</b></div>
    <h1>Verificación en dos pasos</h1>
    <p class="sub">Introduce el código de tu app de autenticación.</p>

    <?php if (!empty($errores)): ?>
      <div class="alert"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
    <?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrf) ?>">
    <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code"
           pattern="[0-9A-Za-z\- ]*" maxlength="9" autofocus placeholder="000000">

    <button type="submit">Verificar</button>
    <p class="hint">¿Sin acceso a la app? Usa uno de tus códigos de respaldo.</p>
    <p><a class="logout" href="../../controladores/logout.php">Cancelar e iniciar sesión de nuevo</a></p>
  </form>
</body>
</html>
