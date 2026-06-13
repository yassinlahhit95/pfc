<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Enhorabuena | Calasanz Santurtzi</title>

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

<?php $ciclousuario =$_GET['ciclo'];?>

  <!-- Masthead -->
  <header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
           <img src="img/check.png" width="20%">
          <h2 class="mb-5">¡Tu formulario ha sido enviado correctamente!</h2>
          
          <h5 class="mb-5">Te recordamos que has sido admitido en el ciclo de<br> 

          <?php 
          switch ($ciclousuario) {
    case 0:
        echo "Cuidados Auxiliares de Enfermería";
        break;
    case 1:
        echo "Laboratorio Clínico y Biomédico";
        break;
    case 2:
        echo "Imagen para el Diagnóstico y Medicina Nuclear";
        break;
    case 3:
        echo "Farmacia y Parafarmacia";
        break;
    case 4:
        echo "Documentación y Administración Sanitaria";
        break;
    case 5:
        echo "Formación Profesional Básica";
        break;
    case 6:
        echo "Atención a Personas en Situación de Dependencia";
        break;
    case 7:
        echo "Integración Social";
        break;
    case 8:
        echo "Educación Infantil";
        break;
    case 9:
        echo "Gestión Administrativa";
        break;
    case 10:
        echo "Administración y Finanzas";
        break;
    case 11:
        echo "Marketing y Publicidad";
        break;
    case 12:
        echo "Radioterapia y Dosimetría";
        break;
	case 13:
        echo "Emergencias Sanitarias";
        break;
          } ?> 

         </h5>

          <a class="btn btn-primary" href="#siguiente">Siguientes pasos</a>

        </div>
      </div>
    </div>
  </header>

  <!-- Testimonials -->

  <section class="testimonials text-center bg-light" id="siguiente">
    <div class="container">
      <h2 class="mb-5">¿Y ahora qué tengo que hacer?</h2>
      <h5 class="mb-5">No te preocupes. Sigue los pasos que te indicamos más abajo para proceder con la matrícula del ciclo que quieres cursar.<br>
      Si tienes dudas con alguno de los pasos requeridos recuerda que puedes acceder a nuestra <a href="#ayuda">sección de ayuda.</a><br>
	  <b><u>Es importante que completes los 4 pasos antes de terminar</u></b></h5>

      <div class="row">
        <!-- Comienzo circulo 1 -->
        <div class="col-lg-3">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <img class="img-fluid rounded-circle mb-3" src="img/acepto_disabled.png" alt="Entrega el documento">
            <h5 style="color:gray">Paso 1: Confirma la aceptación de términos y condiciones económicas</h5>
            <p class="font-weight-light mb-0" style="color:gray">Desde este enlace podrás acceder al formulario para aceptar los términos y condiciones del servicio.</p>
          </div>
        </div>
        <!-- Fin circulo 1 -->
        <!-- Comienzo circulo 2 -->
        <div class="col-lg-3">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <img class="img-fluid rounded-circle mb-3" src="img/rellena.png" alt="Rellena este documento">
            <h5>Paso 2: Completa este formulario con todos los datos</h5>
            <p class="font-weight-light mb-0">Desde <a href="https://bit.ly/matriculacion_calasanz_santurtzi" target="new"><b>este enlace</b></a> podrás acceder al formulario para completar los datos requeridos para la matriculación.</p>
          </div>
        </div>
        <!-- Fin circulo 2 -->
        <!-- Comienzo circulo 3 -->
        <div class="col-lg-3">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <img class="img-fluid rounded-circle mb-3" src="img/cita.png" alt="Entrega el documento">
            <h5>Paso 3: Reserva una cita presencial para acudir al centro</h5>
            <p class="font-weight-light mb-0">
              Desde <a href='https://calasanz.eus/calendario-matriculacion' target='new'><b>este enlace</b></a> podrás reservar una cita en la fecha y hora que mejor se adapte a tu agenda.</p>
          </div>
        </div>
        <!-- Fin circulo 3 -->
        <!-- Comienzo circulo 4 -->
        <div class="col-lg-3">
          <div class="testimonial-item mx-auto mb-5 mb-lg-0">
            <img class="img-fluid rounded-circle mb-3" src="img/web.png" alt="Te contactamos">
            <h5>Paso 4: Permanece atento a <a href="http://www.calasanz.eus" target="new">nuestra web</a> para más novedades</h5>
            <p class="font-weight-light mb-0">En breves publicaremos fechas de comienzo de curso, listado de libros y demás información relevante.</p>
          </div>
        </div>
        <!-- Fin circulo 4 -->
      </div>
    </div>
    
  </section>

  <!-- Image Showcases -->
  <section class="showcase" id="ayuda">
    <div class="container-fluid p-0">
      <div class="row no-gutters">

        <div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('img/bg-showcase-1.jpg');"></div>
        <div class="col-lg-6 order-lg-1 my-auto showcase-text">
          <h2>¿Necesitas ayuda con tu matrícula online?</h2>
          <p class="lead mb-0">Te dejamos varios <b>enlaces</b> con los que poder completar cada uno de los pasos requeridos para que no te requiera ninguna dificultad.</p><br>
          <p class="lead mb-0">Paso 1: <b><a href="mailto:tics@calasanz.eus?Subject=Soporte%20formulario%20matriculación">como completar el formulario de matriculación.</a></b></p>
          <p class="lead mb-0">Paso 2: <b><a href="mailto:tics@calasanz.eus?Subject=Soporte%20formulario%20aceptación">como completar el formulario de aceptación de términos y condiciones.</a></b></p>
          <p class="lead mb-0">Paso 3: <b><a href="mailto:tics@calasanz.eus?Subject=Soporte%20reservas">como reservar una cita para acudir presencialmente al centro.</a></b></p>
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

</body>

</html>
