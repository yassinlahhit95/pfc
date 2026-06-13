<?php
require_once 'session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = $_POST['usuario'];
    $clave_input = $_POST['password'];

    require_once 'generar_excel/conexion.php';
    $conexion = new Conexion();
    $db = $conexion->conectarse();

    $stmt = $db->prepare("SELECT id, usuario, password, rol FROM usuarios_admin WHERE usuario = ?");
    $stmt->bind_param("s", $usuario_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($clave_input, $user['password'])) {
            $_SESSION['autenticado'] = true;
            $_SESSION['rol'] = $user['rol'];
            header('Location: panel_gestion.php');
            exit;
        }
    }
    
    $error = "Usuario o contraseña incorrectos.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión | Calasanz Santurtzi</title>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      border-radius: 8px;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <!-- Created by Yassin.lahhit@outlook.com. 2026 -->

  <div class="login-card">
    <div class="text-center">
      <img src="img/logo.png" style="max-width: 200px;" class="mb-4">
      <h4 class="mb-4">Acceso al panel de gestión</h4>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="usuario">Usuario</label>
        <input type="text" name="usuario" class="form-control" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Entrar</button>
      <hr>
      <a href="../index.html" class="btn btn-secondary btn-block">Volver al inicio</a>
    </form>
  </div>
</body>
</html>
