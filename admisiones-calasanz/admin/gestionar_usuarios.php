<?php
require_once 'session_config.php';
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header("Location: panel_gestion.php");
    exit;
}

require_once 'generar_excel/conexion.php';
$conexion = new Conexion();
$db = $conexion->conectarse();

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE usuarios_admin SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        if ($stmt->execute()) {
            $mensaje = "<div class='alert alert-success'>Contraseña actualizada correctamente.</div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar la contraseña.</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden.</div>";
    }
}

$query = "SELECT id, usuario, rol FROM usuarios_admin ORDER BY id ASC";
$result = $db->query($query);
$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Gestión de Usuarios - Calasanz Santurtzi</title>
  
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link href="css/landing-page.min.css" rel="stylesheet">
</head>
<body>
<!-- Created by Yassin.lahhit@outlook.com. 2026 -->

  <nav class="navbar navbar-light bg-light static-top">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="panel_gestion.php"><img src="img/logo.png" style="max-width: 150px;"></a>
      <div>
        <a class="btn btn-primary" href="panel_gestion.php">Volver al Panel</a>
        <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <section class="call-to-action text-white text-center" style="padding-top: 4rem; padding-bottom: 4rem;">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <div class="mb-3"><i data-lucide="users" style="width: 80px; height: 80px; color: white;"></i></div>
          <h2 class="mb-4">Gestión de Usuarios Admin</h2>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 py-5">
            <?php echo $mensaje; ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered bg-white">
                    <thead class="thead-dark">
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Cambiar Contraseña</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="align-middle"><strong><?php echo htmlspecialchars($u['usuario']); ?></strong></td>
                            <td class="align-middle"><?php echo htmlspecialchars($u['rol']); ?></td>
                            <td>
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <input type="password" name="new_password" placeholder="Nueva contraseña" class="form-control mr-2" required>
                                    <input type="password" name="confirm_password" placeholder="Confirmar" class="form-control mr-2" required>
                                    <button type="submit" name="actualizar_password" class="btn btn-primary">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 h-100 text-center text-lg-left my-auto">
          <ul class="list-inline mb-2">
            <li class="list-inline-item"><a href="https://www.calasanz.eus" target="new">Web corporativa</a></li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item"><a href="mailto:secretaria@calasanz.eus">Contacto</a></li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item"><a href="https://calasanz.eus/aviso-legal/" target="new">Aviso legal</a></li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item"><a href="https://calasanz.eus/politica-de-privacidad/" target="new">Política de privacidad</a></li>
          </ul>
          <p class="text-muted small mb-4 mb-lg-0">&copy; Calasanz Santurtzi 2026. Todos los derechos reservados.</p>
        </div>
        <div class="col-lg-6 h-100 text-center text-lg-right my-auto">
          <ul class="list-inline mb-0">
            <li class="list-inline-item mr-3"><a href="https://www.facebook.com/calasanzsanturtzi" target="new"><i class="fab fa-facebook fa-2x fa-fw"></i></a></li>
            <li class="list-inline-item mr-3"><a href="https://twitter.com/calasanzstz" target="new"><i class="fab fa-twitter-square fa-2x fa-fw"></i></a></li>
            <li class="list-inline-item"><a href="https://www.instagram.com/calasanzsanturtzi/" target="new"><i class="fab fa-instagram fa-2x fa-fw"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>lucide.createIcons();</script>
</body>
</html>
