<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$code = http_response_code();
if (!in_array($code, [400, 403, 404, 500])) {
    $code = isset($_SERVER['REDIRECT_STATUS']) ? (int)$_SERVER['REDIRECT_STATUS'] : 404;
}
http_response_code($code);

$messages = [
    400 => ['icono' => 'fa-triangle-exclamation',  'titulo' => 'Solicitud incorrecta',       'descripcion' => 'La solicitud no se pudo procesar.'],
    403 => ['icono' => 'fa-lock',                  'titulo' => 'Acceso restringido',          'descripcion' => 'No tienes permiso para ver esta página.'],
    404 => ['icono' => 'fa-magnifying-glass',      'titulo' => 'Página no encontrada',        'descripcion' => 'La URL que buscas no existe o fue eliminada.'],
    500 => ['icono' => 'fa-screwdriver-wrench',    'titulo' => 'Error interno del servidor',  'descripcion' => 'Algo salió mal en el servidor. Inténtalo más tarde.'],
];
$msg = $messages[$code] ?? $messages[404];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Roboto',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f1f5f9;overflow:hidden;}
        .fondo{position:fixed;inset:0;background:linear-gradient(145deg,#0f0e1f 0%,#1e1b4b 40%,#2d2a80 72%,#4338ca 100%);z-index:0;}
        .orb{position:absolute;border-radius:50%;filter:blur(90px);opacity:.15;pointer-events:none;}
        .orb-1{width:500px;height:500px;background:#6366f1;top:-100px;right:-100px;}
        .orb-2{width:300px;height:300px;background:#818cf8;bottom:-60px;left:-60px;}
        .grid{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px);background-size:32px 32px;}
        .tarjeta{position:relative;z-index:1;background:rgba(255,255,255,.07);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:56px 48px;text-align:center;max-width:440px;width:90%;animation:aparecer .6s ease forwards;}
        .logo{height:46px;width:auto;border-radius:10px;margin-bottom:36px;}
        .icono{font-size:4rem;margin-bottom:16px;line-height:1;color:#818cf8;}
        .titulo{font-size:1.35rem;font-weight:700;color:#ffffff;margin-bottom:12px;letter-spacing:.01em;}
        .descripcion{font-size:.95rem;color:#94a3b8;line-height:1.6;margin-bottom:36px;}
        .boton{display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#4f46e5,#4338ca);color:#fff;border-radius:12px;font-weight:600;font-size:.95rem;text-decoration:none;transition:transform .2s,box-shadow .2s;}
        .boton:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(79,70,229,.4);}
        @keyframes aparecer{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    </style>
</head>
<body>
<div class="fondo">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="grid"></div>
</div>
<div class="tarjeta">
    <img src="/public/imagenes/aulapro.jpeg" alt="AulaPro" class="logo"
         onerror="this.style.display='none'">
    <div class="icono"><i class="fas <?= $msg['icono'] ?>"></i></div>
    <p class="titulo"><?= $msg['titulo'] ?></p>
    <p class="descripcion"><?= $msg['descripcion'] ?></p>
    <?php
    $homeUrl = '/vistas/login.php';
    if (!empty($_SESSION['idAdmin']))      $homeUrl = '/vistas/admin/inicio/dashboard.php';
    elseif (!empty($_SESSION['idProfesor']))   $homeUrl = '/vistas/profesores/inicio/dashboard.php';
    elseif (!empty($_SESSION['idEstudiante'])) $homeUrl = '/vistas/estudiantes/inicio/dashboard.php';
    elseif (!empty($_SESSION['idSecretaria'])) $homeUrl = '/vistas/secretaria/inicio/dashboard.php';
    elseif (!empty($_SESSION['idTutor']))      $homeUrl = '/vistas/tutores/inicio/dashboard.php';
    ?>
    <a href="<?= htmlspecialchars($homeUrl) ?>" class="boton">Volver al inicio</a>
</div>
</body>
</html>
<?php exit; ?>
