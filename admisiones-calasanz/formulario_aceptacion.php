<!DOCTYPE html>
<html lang="es">

<head>

  <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Aceptación de términos y condiciones económicas | Calasanz Santurtzi</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template -->
  <link href="css/landing-page.min.css" rel="stylesheet">

</head>

<body class="bg-light">

  <?php 
  $nombreusuario =$_GET['nombre'];
  $apellidosusuario = isset($_GET['apellidos']) ? $_GET['apellidos'] : ''; 
  $dniusuario = isset($_GET['dni']) ? $_GET['dni'] : ''; 
  $ciclousuario =$_GET['ciclo'];

  require_once 'admin/generar_excel/conexion.php';
  $conexion = new Conexion();
  $db = $conexion->conectarse();

  $stmt = $db->prepare("SELECT * FROM precios_ciclos WHERE id_ciclo = ?");
  $stmt->bind_param("i", $ciclousuario);
  $stmt->execute();
  $resultado = $stmt->get_result();
  $datos_ciclo = $resultado->fetch_assoc();
  ?>

 <!-- Navigation -->
  <nav class="navbar navbar-light bg-light static-top">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="index.html"><img src="img/logo.png" style="max-width: 150px;"></a>
      <a class="btn btn-primary" href="index.html">Página principal</a>
    </div>
  </nav>

      <div class="container">
        <div class="well">
        <h4 class="mb-5" id="form"><br>Formulario de aceptación de términos y condiciones económicas</h4>
        </div>

        <div class="card" id="terminos">
          <div class="card-header">
            <strong>Aceptación de términos y condiciones económicas</strong>
          </div>
          <div class="card-body card-block">

          <p>Al realizar la matrícula para el presente curso escolar en <b>CALASANZ LANBIDE IKASTEGIA</b>, me comprometo voluntariamente a:</p>

          <ol>

          <li>Aceptar y respetar el Proyecto Educativo del Centro (PEC).</li><br>

          <li>Aceptar el Reglamento de Régimen Interno (normativa que regula la vida en nuestro Centro).</li><br>

          <li>Aceptar las actividades complementarias y/o extraescolares que el centro, de acuerdo con el Reglamento de Conciertos Educativos, pueda establecer y siempre que las mismas sean aprobadas por el Consejo Escolar del centro.</li><br>

          <li>Aceptar cuantas normas académicas pudieran surgir del Consejo Escolar y aquellos acuerdos del mismo que afecten a servicios complementarios ofrecidos en el centro.</li><br>

          <li>Al formalizar la matrícula, se abonará la mensualidad de septiembre y el seguro escolar, así como la plataforma de aprendizaje online de Office 365. En caso de baja antes del comienzo del periodo lectivo, siempre que se cubra la plaza que se deja vacante, se devolverá el importe del seguro escolar y de las plataformas digitales correspondientes y el 50% del importe de la mensualidad, asumiendo el 50% restante en concepto de gastos de tramitación.</li><br>

          <li>Condiciones Económicas:</li><br>
          
          <?php         
          if ($datos_ciclo) {
            echo htmlspecialchars($datos_ciclo['nombre_ciclo']) . " consta de " . $datos_ciclo['num_recibos'] . " recibos de " . number_format($datos_ciclo['precio_mensual'], 2, ',', '.') . "€ al mes, excepto la primera mensualidad que se paga en efectivo al formalizar la matrícula junto a las licencias digitales y seguros, y será de un total de " . number_format($datos_ciclo['pago_inicial'], 2, ',', '.') . "€.";
          } else {
            echo "Ciclo no válido.";
          }
		?>

      </ol>
        <p>*Los datos facilitados por Vd. se incluirán en un fichero responsabilidad de <b>CALASANZ SANTURTZI SOCIEDAD LIMITADA</b>, y podrán ser cedidos a las Consejerías de Educación, Empleo y Políticas Sociales del Gobierno Vasco y otros organismos oficiales, para la correcta gestión de los servicios solicitados. Siempre y cuando se cumplan los requisitos exigidos por la normativa, usted podrá ejercer sus derechos de acceso, rectificación, limitación de tratamiento, supresión (“derecho al olvido”), portabilidad, oposición y revocación, en los términos que establece la normativa vigente y aplicable en materia de protección de datos, dirigiendo su petición a la dirección postal C/ HOSPITAL BAJO 11 48980, SANTURTZI (BIZKAIA) o bien a través de correo electrónico <a href="mailto:lopd@calasanz.eus">lopd@calasanz.eus.</a></p>

        <p>Mediante la firma de este documento autorizo a <b>CALASANZ SANTURTZI</b> a publicar las fotos obtenidas en los eventos realizados y organizados por <b>CALASANZ LANBIDE IKASTEGIA</b>, en los medios de difusión o comunicación que el centro establezca.</p>
      </div>
    </div>

    <br>
    <br>
      <div class="card">
        <div class="card-header">
          <strong>Datos personales del solicitante</strong>
        </div>
        <div class="card-body card-block">
            <form action="envio_formulario_aceptacion.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">Nombre del solicitante</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="nombre" name="nombre" <?php echo "value='$nombreusuario'"?> class="form-control" readonly required>
                        <input type="text" id="usuario" name="usuario" <?php echo "value='$nombreusuario'"?> hidden>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">Apellidos del solicitante</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="apellidos" name="apellidos" <?php echo "value='$apellidosusuario'"?> class="form-control" readonly required>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">DNI o NIE del solicitante</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" id="dni" name="dni" <?php echo "value='$dniusuario'"?> class="form-control" readonly required>
                    </div>
                </div>
                <!--<div class="row form-group">
                    <div class="col col-md-3">
                        <label for="file-multiple-input" class=" form-control-label">Foto del frontal y reverso del DNI del solicitante</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="file" id="miarchivo[]" name="miarchivo[]" multiple="" class="form-control-file" required>
                    </div>
                </div>-->
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label for="text-input" class=" form-control-label">Ciclo en el que has sido admitido/a</label>
                    </div>
                    <div class="col-12 col-md-9">
                        <input type="text" 
                        value="<?php echo $datos_ciclo ? htmlspecialchars($datos_ciclo['nombre_ciclo']) : 'Ciclo no válido'; ?>" class="form-control" disabled>
                        <input type="text" id="ciclo" name="ciclo" <?php echo "value='$ciclousuario'"?> hidden>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col col-md-3">
                        <label class=" form-control-label">Aceptación de envío</label>
                    </div>
                    <div class="col col-md-9">
                        <div class="form-check">
                            <div class="radio">
                                <label for="radio1" class="form-check-label ">
                                    <input type="radio" id="radio1" name="radios" value="option1" class="form-check-input" required><b>He leído, comprendo y acepto los <a href="#terminos">términos y condiciones económicas</a></b> para la realización de la matrícula en <b>CALASANZ LANBIDE IKASTEGIA.</b>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
       <div class="card-footer">
            <button class="btn btn-block btn-lg btn-primary" type="submit">Enviar aceptación de términos y condiciones</button>
        </div>
        </form>
        </div>


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
