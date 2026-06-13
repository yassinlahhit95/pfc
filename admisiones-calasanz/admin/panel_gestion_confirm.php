<?php
require_once 'session_config.php';
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Panel de administración | Calasanz Santurtzi</title>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

  <!-- Custom styles for this template -->
  <link href="css/landing-page.min.css" rel="stylesheet">

</head>

<body>

  <!-- Navigation -->
  <nav class="navbar navbar-light bg-light static-top">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="../index.html"><img src="img/logo.png" style="max-width: 150px;"></a>
      <div>
        <a class="btn btn-primary" href="../index.html">Página principal</a>
        <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
      </div>
    </div>
  </nav>

  <section class="call-to-action text-white text-center">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <h2 class="mb-4">Panel de administración | Calasanz Santurtzi</h2>
        </div>
      </div>
    </div>
  </section>
  
<section class="testimonials text-center bg-light">
    <div class="container">   
      <div class="alert alert-success" role="alert">
        <strong>¡Operación realizada con éxito!</strong> Nueva admisión dada de alta correctamente.
      </div>
      <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success_fase'): ?>
        <div class="alert alert-success" role="alert">
          <strong>¡Éxito!</strong> La fase de denegación ha sido actualizada correctamente.
        </div>
        <?php elseif ($_GET['status'] == 'error_permissions'): ?>
        <div class="alert alert-danger" role="alert">
          <strong>¡Error!</strong> No hay permisos de escritura en el archivo de denegación.
        </div>
        <?php elseif ($_GET['status'] == 'error_write'): ?>
        <div class="alert alert-danger" role="alert">
          <strong>¡Error!</strong> No se pudo escribir en el archivo.
        </div>
        <?php elseif ($_GET['status'] == 'error_regex'): ?>
        <div class="alert alert-danger" role="alert">
          <strong>¡Error!</strong> No se encontró la línea de configuración en el archivo.
        </div>
        <?php endif; ?>
      <?php endif; ?>
      <?php
        $denegada_content = file_get_contents('../admision_denegada.php');
        preg_match('/: (\d+);/', $denegada_content, $matches);
        $current_fase = isset($matches[1]) ? intval($matches[1]) : 3;
      ?>
        <div class="row">
        <!-- Búsqueda por DNI -->
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <div class="mb-3"><i data-lucide="search" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Buscar ficha por DNI<br>Lista de espera</h5>
            <p class="font-weight-light mb-0">Accede a la información del alumnado introduciendo su DNI</p><br>
            <input type="button" value="Buscar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='panel_gestion.php';">
          </div>
        </div>

        <!-- Búsqueda por ciclo -->
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <div class="mb-3"><i data-lucide="filter" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Buscar fichas por ciclo<br>Lista de espera</h5>
            <p class="font-weight-light mb-0">Accede a las fichas de la lista de espera ordenadas por ciclo</p><br>
            <input type="button" value="Filtrar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='panel_gestion.php';">
          </div>
        </div>

        <!-- Exportar a Excel -->
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <div class="mb-3"><i data-lucide="download" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Exportar datos a Excel<br>Lista de espera</h5>
            <p class="font-weight-light mb-0">Exporta todo el contenido de la base de datos a un documento de Excel</p><br>
            <input type="button" value="Exportar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='generar_excel/generate_espera.php';">
          </div>
        </div>
        
        <!-- Insertar alumnado lista de admisiones -->
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <div class="mb-3"><i data-lucide="user-plus" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Insertar alumnado<br>Lista de admisiones</h5>
            <p class="font-weight-light mb-0">Introduce la información del alumnado para darle de alta</p><br>
            <input type="button" value="Insertar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='insertar_alumnado.php';">
          </div>
        </div>
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto">
            <div class="mb-3"><i data-lucide="mail" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Enviar correo electrónico</h5>
            <p class="font-weight-light mb-12">Introduce la dirección del alumnado para enviar el mensaje</p><br>
            <input type="button" value="Enviar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='panel_gestion.php';">
          </div>
        </div>
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto">
            <div class="mb-3"><i data-lucide="refresh-ccw" style="width: 80px; height: 80px; color: #007bff;"></i></div>
            <h5>Cambiar fase denegación</h5>
            <p class="font-weight-light mb-0">Selecciona la fase activa para las denegaciones</p><br>
            <input type="button" value="Cambiar ahora" class="btn btn-block btn-lg btn-primary" onclick="location.href='panel_gestion.php';">
          </div>
        </div>
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto">
            <div class="mb-3"><i data-lucide="banknote" style="width: 80px; height: 80px; color: #28a745;"></i></div>
            <h5>Gestión de precios</h5>
            <p class="font-weight-light mb-0">Edita los precios y recibos de cada ciclo formativo</p><br>
            <input type="button" value="Gestionar precios" class="btn btn-block btn-lg btn-success" onclick="location.href='gestionar_precios.php';">
          </div>
        </div>
        <div class="col-lg-4">
          <div class="testimonial-item mx-auto">
            <div class="mb-3"><i data-lucide="users" style="width: 80px; height: 80px; color: #17a2b8;"></i></div>
            <h5>Gestión de usuarios</h5>
            <p class="font-weight-light mb-0">Cambia las contraseñas del panel de administración</p><br>
            <input type="button" value="Gestionar usuarios" class="btn btn-block btn-lg btn-info" onclick="location.href='gestionar_usuarios.php';">
          </div>
        </div>
        <?php endif; ?>
      </div>
      </div>
    </div>



  </section>
  

  <!-- Footer -->
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
