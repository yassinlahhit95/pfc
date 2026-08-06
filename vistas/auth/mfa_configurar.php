<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../include/Totp.php';
require_once __DIR__ . '/../../include/MfaService.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;

Security::initSession();

$actor = MfaService::sesionActual();
if (!$actor) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../../modelos/' . $actor['modelo'];

// Secreto de alta: se mantiene en sesión hasta que se confirma con un código
if (empty($_SESSION['mfa_setup_secret'])) {
    $_SESSION['mfa_setup_secret'] = Totp::generateSecret();
}
$secret = $_SESSION['mfa_setup_secret'];

$usuario = ($actor['getFn'])($actor['id']);
$label   = $usuario[$actor['emailField']] ?? ($actor['sessionKey'] . '#' . $actor['id']);
$uri     = Totp::provisioningUri($secret, $label, 'AulaPro');

$qrDataUri = (new Builder())
    ->build(writer: new SvgWriter(), data: $uri, size: 220, margin: 6)
    ->getDataUri();

$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['errores']);
$csrf = Security::generateCSRFToken();
$esObligatorio = !empty($_SESSION['mfa_setup_required']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Configurar verificación en dos pasos — AulaPro</title>
<style>
  :root { --primary:#4f46e5; --primary-strong:#3730a3; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; background:#f3f4f6; color:#1f2937;
         display:flex; min-height:100vh; align-items:center; justify-content:center; padding:20px; }
  .card { background:#fff; width:100%; max-width:470px; border-radius:16px; padding:32px 30px;
          box-shadow:0 10px 40px rgba(0,0,0,.08); }
  .brand { font-weight:800; font-size:18px; color:var(--primary); margin-bottom:14px; }
  .brand b { color:var(--primary-strong); }
  h1 { font-size:20px; margin:0 0 6px; }
  p.sub { margin:0 0 18px; color:#6b7280; font-size:14px; }
  ol { padding-left:18px; color:#374151; font-size:14px; line-height:1.6; }
  .qr { text-align:center; margin:14px 0; }
  .qr img { width:220px; height:220px; border:1px solid #eef2ff; border-radius:12px; padding:8px; background:#fff; }
  .secret { text-align:center; font-family:ui-monospace,Menlo,Consolas,monospace; font-size:13px; color:#4b5563;
            background:#f9fafb; border:1px solid #eef2ff; border-radius:8px; padding:8px; word-break:break-all; }
  label { display:block; font-size:13px; font-weight:600; margin:16px 0 6px; }
  input[name=code] { width:100%; padding:12px; border:1px solid #d1d5db; border-radius:9px; font-size:20px;
          text-align:center; letter-spacing:6px; }
  input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.15); }
  button { width:100%; margin-top:18px; padding:12px; border:0; border-radius:9px; background:var(--primary);
           color:#fff; font-size:15px; font-weight:600; cursor:pointer; }
  button:hover { background:var(--primary-strong); }
  .alert { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:11px 14px; border-radius:9px;
           font-size:13px; margin-bottom:14px; }
  .banner { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; padding:11px 14px; border-radius:9px;
            font-size:13px; margin-bottom:16px; }
</style>
</head>
<body>
  <form class="card" method="POST" action="../../controladores/auth/mfa_activar.php" autocomplete="off">
    <div class="brand">Aula<b>Pro</b></div>
    <h1>Protege tu cuenta</h1>
    <p class="sub"><?= $esObligatorio ? 'La verificación en dos pasos es obligatoria para continuar.' : 'Añade una segunda capa de seguridad a tu cuenta.' ?></p>

    <div class="banner">Escanea el código con Google Authenticator, Microsoft Authenticator o Authy.</div>

    <?php if (!empty($errores)): ?>
      <div class="alert"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
    <?php endif; ?>
    <ol>
      <li>Abre tu app de autenticación.</li>
      <li>Escanea este código QR:</li>
    </ol>
    <div class="qr"><img src="<?= Security::escapeHtml($qrDataUri) ?>" alt="Código QR MFA"></div>
    <p class="sub" style="margin:0 0 8px">¿No puedes escanear? Introduce esta clave manualmente:</p>
    <div class="secret"><?= Security::escapeHtml($secret) ?></div>

    <label for="code">Introduce el código de 6 dígitos para confirmar</label>
    <input type="text" id="code" name="code" inputmode="numeric" autocomplete="one-time-code"
           pattern="[0-9]*" maxlength="6" placeholder="000000" autofocus>

    <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrf) ?>">
    <button type="submit">Activar verificación en dos pasos</button>
  </form>
</body>
</html>
