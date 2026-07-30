<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/../include/Security.php';
Security::initSession();

// Debe haber una sesión iniciada (cualquier rol)
$autenticado = !empty($_SESSION['idAdmin']) || !empty($_SESSION['idProfesor'])
            || !empty($_SESSION['idEstudiante']) || !empty($_SESSION['idTutor'])
            || !empty($_SESSION['idSecretaria']);
if (!$autenticado) {
    header('Location: login.php');
    exit;
}

$obligatorio = !empty($_SESSION['must_change_password']);
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['errores']);
$csrf = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Cambiar contraseña — AulaPro</title>
<link rel="shortcut icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<style>
  :root { --primary:#4f46e5; --primary-strong:#3730a3; --bg:#f3f4f6; --txt:#1f2937; }
  * { box-sizing:border-box; }
  body { margin:0; font-family:system-ui,-apple-system,"Segoe UI",sans-serif; background:var(--bg);
         color:var(--txt); display:flex; min-height:100vh; align-items:center; justify-content:center; padding:20px; }
  .card { background:#fff; width:100%; max-width:430px; border-radius:16px; padding:34px 30px;
          box-shadow:0 10px 40px rgba(0,0,0,.08); }
  h1 { font-size:22px; margin:0 0 6px; }
  p.sub { margin:0 0 22px; color:#6b7280; font-size:14px; }
  label { display:block; font-size:13px; font-weight:600; margin:14px 0 6px; }
  input { width:100%; padding:11px 13px; border:1px solid #d1d5db; border-radius:9px; font-size:15px; }
  input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(79,70,229,.15); }
  .req { background:#f9fafb; border:1px solid #eef2ff; border-radius:9px; padding:12px 14px; margin:16px 0 4px;
         font-size:12px; color:#6b7280; line-height:1.6; }
  button { width:100%; margin-top:20px; padding:12px; border:0; border-radius:9px; background:var(--primary);
           color:#fff; font-size:15px; font-weight:600; cursor:pointer; }
  button:hover { background:var(--primary-strong); }
  .alert { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:11px 14px; border-radius:9px;
           font-size:13px; margin-bottom:14px; }
  .banner { background:#fffbeb; border:1px solid #fde68a; color:#92400e; padding:11px 14px; border-radius:9px;
            font-size:13px; margin-bottom:18px; }
  .brand { display:flex; align-items:center; gap:10px; font-weight:800; font-size:18px; color:var(--primary); margin-bottom:18px; }
  .brand b { color:var(--primary-strong); }
  .brand img { height:32px; width:auto; }
</style>
</head>
<body>
  <form class="card" method="POST" action="../controladores/auth/cambiar_password.php" autocomplete="off">
    <div class="brand"><img src="../public/imagenes/aulapro.png" alt="AulaPro"> Aula<b>Pro</b></div>
    <h1>Cambia tu contraseña</h1>
    <p class="sub">Por tu seguridad, establece una contraseña nueva y personal.</p>

      <div class="banner">Estás usando una contraseña temporal. Debes cambiarla para continuar.</div>
      <div class="alert"><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></div>
    <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrf) ?>">

    <label for="nueva">Nueva contraseña</label>
    <input type="password" id="nueva" name="nueva" required minlength="8" autocomplete="new-password">

    <label for="confirmar">Repite la contraseña</label>
    <input type="password" id="confirmar" name="confirmar" required minlength="8" autocomplete="new-password">

    <div class="req">
      Mínimo <b>8 caracteres</b>, con al menos una <b>mayúscula</b>, una <b>minúscula</b> y un <b>número</b>.
    </div>

    <button type="submit">Guardar contraseña</button>
  </form>
</body>
</html>
