<?php
require_once 'session_config.php';
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Insertar Alumnado - Calasanz Santurtzi</title>
  
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
          <div class="mb-3"><i data-lucide="user-plus" style="width: 80px; height: 80px; color: white;"></i></div>
          <h2 class="mb-4">Insertar Alumnado</h2>
          <p class="lead">Lista de admisiones</p>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 py-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Datos del nuevo alumno</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Introduce la información del alumnado para darle de alta en el sistema.</p>
                    <form action="generar_excel/insert_admisiones.php" method="post">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" placeholder="Introduce el nombre aquí" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Apellidos</label>
                            <input type="text" class="form-control" placeholder="Introduce los apellidos aquí" name="apellidos" required>
                        </div>
                        <div class="form-group">
                            <label>DNI / NIE</label>
                            <input type="text" class="form-control" placeholder="Ej.: 12345678A" maxlength="9" name="dni" pattern="(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))" data-toggle="tooltip" data-placement="top" title="Solamente números y letra. Máximo 9 dígitos." required>
                        </div>
                        <div class="form-group">
                            <label>Ciclo formativo</label>
                            <select class="form-control" name="ciclo" required>
                                <option value="" disabled selected>- Selecciona un ciclo -</option>
                                <option value="1">Laboratorio Clínico y Biomédico</option>
                                <option value="2">Imagen para el Diagnóstico y Medicina Nuclear</option>
                                <option value="4">Documentación y Administración Sanitaria</option>
                                <option value="8">Educación Infantil</option>
                                <option value="7">Integración Social</option>
                                <option value="10">Administración y Finanzas</option>
                                <option value="11">Marketing y Publicidad</option>
                                <option value="9">Gestión Administrativa</option>
                                <option value="3">Farmacia y Parafarmacia</option>
                                <option value="0">Cuidados Auxiliares de Enfermería</option>
                                <option value="6">Atención a Personas en Situación de Dependencia</option>
                                <option value="5">Formación Profesional Básica</option> 
                                <option value="12">Radioterapia y dosimetría</option> 
                                <option value="13">Emergencias Sanitarias</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i data-lucide="check-circle" class="mr-2" style="width:18px; height:18px;"></i>
                                Insertar alumnado
                            </button>
                        </div>
                    </form>
                </div>
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
