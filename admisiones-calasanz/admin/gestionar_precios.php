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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    foreach ($_POST['precios'] as $id_ciclo => $datos) {
        $num_recibos = intval($datos['num_recibos']);
        $precio_mensual = floatval($datos['precio_mensual']);
        $pago_inicial = floatval($datos['pago_inicial']);
        
        $stmt = $db->prepare("UPDATE precios_ciclos SET num_recibos = ?, precio_mensual = ?, pago_inicial = ? WHERE id_ciclo = ?");
        $stmt->bind_param("iddi", $num_recibos, $precio_mensual, $pago_inicial, $id_ciclo);
        $stmt->execute();
    }
    $mensaje = "<div class='alert alert-success'>Precios actualizados correctamente.</div>";
}

$query = "SELECT * FROM precios_ciclos ORDER BY id_ciclo ASC";
$result = $db->query($query);
$precios = [];
while ($row = $result->fetch_assoc()) {
    $precios[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Gestión de Precios - Calasanz Santurtzi</title>
  
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
          <div class="mb-3"><i data-lucide="banknote" style="width: 80px; height: 80px; color: white;"></i></div>
          <h2 class="mb-4">Gestión de Precios por Ciclo</h2>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 py-5">
            <?php echo $mensaje; ?>
            <form method="post">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Ciclo</th>
                                <th>Num. Recibos</th>
                                <th>Precio Mensual (€)</th>
                                <th>Total (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($precios as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nombre_ciclo']); ?></td>
                                <td>
                                    <input type="number" name="precios[<?php echo $p['id_ciclo']; ?>][num_recibos]" value="<?php echo $p['num_recibos']; ?>" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="precios[<?php echo $p['id_ciclo']; ?>][precio_mensual]" value="<?php echo $p['precio_mensual']; ?>" class="form-control" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="precios[<?php echo $p['id_ciclo']; ?>][pago_inicial]" value="<?php echo $p['pago_inicial']; ?>" class="form-control" required>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-right mb-5">
                    <button type="submit" name="actualizar" class="btn btn-primary btn-lg">Actualizar todos los precios</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer bg-light">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 h-100 text-center text-lg-left my-auto">
          <ul class="list-inline mb-2">
            <li class="list-inline-item">
              <a href="https://www.calasanz.eus" target="new">Web corporativa</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
              <a href="mailto:secretaria@calasanz.eus">Contacto</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
              <a href="https://calasanz.eus/aviso-legal/" target="new">Aviso legal</a>
            </li>
            <li class="list-inline-item">&sdot;</li>
            <li class="list-inline-item">
              <a href="https://calasanz.eus/politica-de-privacidad/" target="new">Política de privacidad</a>
            </li>
          </ul>
          <p class="text-muted small mb-4 mb-lg-0">&copy; Calasanz Santurtzi 2026. Todos los derechos reservados.</p>
        </div>
        <div class="col-lg-6 h-100 text-center text-lg-right my-auto">
          <ul class="list-inline mb-0">
            <li class="list-inline-item mr-3">
              <a href="https://www.facebook.com/calasanzsanturtzi" target="new">
                <i class="fab fa-facebook fa-2x fa-fw"></i>
              </a>
            </li>
            <li class="list-inline-item mr-3">
              <a href="https://twitter.com/calasanzstz" target="new">
                <i class="fab fa-twitter-square fa-2x fa-fw"></i>
              </a>
            </li>
            <li class="list-inline-item">
              <a href="https://www.instagram.com/calasanzsanturtzi/" target="new">
                <i class="fab fa-instagram fa-2x fa-fw"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script>
    lucide.createIcons();
  </script>

</body>
</html>
