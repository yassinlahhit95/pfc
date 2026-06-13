<?php
$fase = isset($_GET['fase']) ? intval($_GET['fase']) : 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Lo sentimos | Calasanz Santurtzi</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template -->
  <link href="css/landing-page.min.css" rel="stylesheet">

</head>

<body>

 <!-- Navigation -->
  <nav class="navbar navbar-light bg-light static-top">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="index.html"><img src="img/logo.png" style="max-width: 150px;"></a>
      <a class="btn btn-primary" href="index.html">Página principal</a>
    </div>
  </nav>

  <!-- Masthead -->
  <header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
           <img src="img/cross.png" width="20%">
          <h2 class="mb-5">¡Lo sentimos!</h2>
          <?php if ($fase === 1): ?>
            <h5 class="mb-5">Tu solicitud no ha sido aceptada en esta fase. Te invitamos a volver a intentarlo en la segunda fase.</h5>
          <?php elseif ($fase === 2): ?>
            <h5 class="mb-5">Tu solicitud no ha sido seleccionada para la segunda fase. Espera a la tercera fase.</h5>
          <?php elseif ($fase === 3): ?>
			<h5 class="mb-3">
			Tu solicitud no ha sido seleccionada.
			</h5>
			<h5 class="mb-5" style="font-weight:bold; color:#FFFFFF; font-size:1.2em;">
			¡Puedes apuntarte a la lista de espera más abajo!
			</h5>
			<div style="text-align:center; margin-top:10px;">
			<span style="font-size:2em; color:#FFFFFF;">⬇️⬇️⬇️</span>
			</div>
		  <?php elseif ($fase === 4): ?>
			<h5 class="mb-3">
			¡Puedes apuntarte a la lista de espera!
			</h5>
			</div>
          <?php else: ?>
            <h5 class="mb-5">Fase de admisiones cerrada.<br/></h5>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

	<?php if ($fase === 3): ?>
    <?php include('formulario_lista_espera_completo.php'); ?>
	<?php elseif ($fase === 4): ?>
	<?php include('formulario_lista_espera_completo.php'); ?>
  <?php endif; ?>

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
          <p class="text-muted small mb-4 mb-lg-0">&copy; Calasanz Santurtzi 2025. Todos los derechos reservados.</p>
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

</body>

</html>
