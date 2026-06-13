<?php
session_start();
$nombreusuario = $_GET['nombre'];
$apellidosusuario = isset($_GET['apellidos']) ? $_GET['apellidos'] : '';
$ciclousuario = $_GET['ciclo'];
$dniusuario = isset($_GET['dni']) ? $_GET['dni'] : '';

require_once 'admin/generar_excel/conexion.php';
$conexion = new Conexion();
$db = $conexion->conectarse();

$stmt = $db->prepare("SELECT * FROM precios_ciclos WHERE id_ciclo = ?");
$stmt->bind_param("i", $ciclousuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos_ciclo = $resultado->fetch_assoc();
$pago_inicial = $datos_ciclo ? number_format($datos_ciclo['pago_inicial'], 2, ',', '.') . "€" : "Ciclo desconocido";

if (isset($_GET['paso1'])) {
  $_SESSION['paso1'] = true;
}
if (isset($_GET['paso2'])) {
  $_SESSION['paso2'] = true;
}
if (isset($_GET['paso3'])) {
  $_SESSION['paso3'] = true;
}

// Verificar si el paso 1 ya está completado en la base de datos
$stmt_paso1 = $db->prepare("SELECT COUNT(*) FROM formulario_aceptacion WHERE dni = ?");
$stmt_paso1->bind_param("s", $dniusuario);
$stmt_paso1->execute();
$stmt_paso1->bind_result($count_paso1);
$stmt_paso1->fetch();
$stmt_paso1->close();

if ($count_paso1 > 0) {
  $_SESSION['paso1'] = true;
}

// Verificar pasos 2 y 3 en la tabla admisiones
if (!isset($_SESSION['dni_usuario']) && !empty($dniusuario)) {
    $_SESSION['dni_usuario'] = $dniusuario;
}

if (isset($_SESSION['dni_usuario'])) {
    $stmt_pasos = $db->prepare("SELECT Paso2, Paso3 FROM admisiones WHERE DNI = ?");
    if ($stmt_pasos) {
        $stmt_pasos->bind_param("s", $_SESSION['dni_usuario']);
        $stmt_pasos->execute();
        $stmt_pasos->bind_result($p2, $p3);
        if ($stmt_pasos->fetch()) {
            if ($p2 == 1) $_SESSION['paso2'] = true;
            if ($p3 == 1) $_SESSION['paso3'] = true;
        }
        $stmt_pasos->close();
    }
}

function getPasoImage($paso, $estado) {
  return "img/pasos/P{$paso}{$estado}.png";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admisiones - Confirmación de Admisiones</title>
  
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/landing-page.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f8;
      color: #1b1e2e;
      margin: 0;
      padding: 0;
    }
    .container-steps {
      max-width: 1000px;
      margin: 2rem auto;
      text-align: center;
    }
    h2 {
      color: #013c65;
      font-size: 2rem;
    }
    .personal-data {
      margin-top: 1rem;
      font-size: 1.1rem;
      color: #2c2c2c;
      background: #ffffff;
      border-radius: 10px;
      padding: 1rem;
      display: inline-block;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .steps {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1.5rem;
      margin-top: 2rem;
    }
    .step {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      padding: 1.5rem;
      max-width: 220px;
    }
    .step img {
      width: 100%;
      border-radius: 100%;
      margin-bottom: 1rem;
      cursor: pointer;
    }
    .step h3 {
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
    }
    .step p {
      font-size: 0.9rem;
      color: #444;
    }
    .logout-btn {
      margin-top: 2rem;
      display: inline-block;
      background: #333;
      color: #fff;
      padding: 0.7rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
    }
  </style>
  <script>
    function irPaso1() {
      window.location.href = "formulario_aceptacion.php?nombre=<?php echo urlencode($nombreusuario); ?>&apellidos=<?php echo urlencode($apellidosusuario); ?>&dni=<?php echo urlencode($dniusuario); ?>&ciclo=<?php echo urlencode($ciclousuario); ?>";
    }
    function irPaso2() {
      fetch('activar_paso2.php').then(() => {
        window.open('https://web2.alexiaedu.com/ACWeb/paginas/publicas/FormularioMatriculacionGenerico.aspx?token=I5bZmb9ZUY5hZojjdvQJDw%3D%3D', '_blank');
        window.location.reload();
      });
    }
    function irPaso3() {
      fetch('activar_paso3.php').then(() => {
        window.open('https://calasanz.eus/calendario-matriculacion', '_blank');
        window.location.reload();
      });
    }
    function irPaso4() {
		window.open("https://www.calasanz.eus", "_blank");
		window.location.href = "finalizar_proceso.php";
}

  </script>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-light bg-light static-top">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="index.html"><img src="img/logo.png" style="max-width: 150px;"></a>
      <a class="btn btn-primary" href="index.html">Página principal</a>
    </div>
  </nav>

  <div class="container-steps">
    <div class="container">
    <h2>¡Enhorabuena! Completa los 4 pasos para finalizar el proceso de matriculación</h2>
    <div class="personal-data">
      <p><strong>Alumno/a:</strong> <?php echo $nombreusuario . " " . $apellidosusuario; ?></p>
      <p><strong>Ciclo formativo:</strong> <?php echo $datos_ciclo ? htmlspecialchars($datos_ciclo['nombre_ciclo']) : "Ciclo desconocido"; ?></p>
    </div>

    <div class="steps">
      <!-- Paso 1 -->
      <div class="step">
        <img src="<?php echo getPasoImage(1, isset($_SESSION['paso1']) ? 'Fin' : 'Activo'); ?>" alt="Paso 1" onclick="<?php if (!isset($_SESSION['paso1'])) echo 'irPaso1()'; ?>">
        <h3>Paso 1: Acepta los términos</h3>
        <?php if (!isset($_SESSION['paso1'])): ?>
          <p><a href="formulario_aceptacion.php?nombre=<?php echo urlencode($nombreusuario); ?>&apellidos=<?php echo urlencode($apellidosusuario); ?>&dni=<?php echo urlencode($dniusuario); ?>&ciclo=<?php echo urlencode($ciclousuario); ?>">Aceptar condiciones económicas</a></p>
        <?php endif; ?>
      </div>

      <!-- Paso 2 -->
      <div class="step">
        <img src="<?php echo getPasoImage(2, isset($_SESSION['paso1']) ? (isset($_SESSION['paso2']) ? 'Fin' : 'Activo') : 'Espera'); ?>" alt="Paso 2" onclick="<?php if (isset($_SESSION['paso1'])) echo 'irPaso2()'; ?>">
        <h3>Paso 2: Completa tus datos</h3>
		<p>Si ya has sido alumno/a del centro, no hace falta que completes este paso. Únicamente pincha sobre él y cierra la pestaña de PREINSCRIPCIÓN para continuar.</p>
        <?php if (!isset($_SESSION['paso1'])): ?>
          <p>Completa el paso anterior para continuar</p>
        <?php endif; ?>
      </div>

      <!-- Paso 3 -->
      <div class="step">
        <img src="<?php echo getPasoImage(3, isset($_SESSION['paso2']) ? (isset($_SESSION['paso3']) ? 'Fin' : 'Activo') : 'Espera'); ?>" alt="Paso 3" onclick="<?php if (isset($_SESSION['paso2'])) echo 'irPaso3()'; ?>">
        <h3>Paso 3: Reserva tu cita</h3>
		<p>Será necesario acudir al centro con:</p>
		<p>-2 fotocopias del DNI</p>
		<p>-fotocopia del expediente</p>
		<p>-Importe de la matrícula en efectivo: <?php echo $pago_inicial; ?></p></p>
        <?php if (!isset($_SESSION['paso2'])): ?>
          <p>Completa el paso anterior para continuar</p>
        <?php endif; ?>
      </div>

      <!-- Paso 4 -->
      <div class="step">
        <img src="<?php echo getPasoImage(4, isset($_SESSION['paso3']) ? 'Activo' : 'Espera'); ?>" alt="Paso 4" onclick="<?php if (isset($_SESSION['paso3'])) echo 'irPaso4()'; ?>">
        <h3>Paso 4: Consulta novedades</h3>
        <?php if (!isset($_SESSION['paso3'])): ?>
          <p>Completa el paso anterior para continuar</p>
        <?php endif; ?>
      </div>

      <style>
        .steps {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
          gap: 1.5rem;
          margin-top: 2rem;
        }
        .step {
          background: #fff;
          border-radius: 12px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.08);
          padding: 1.5rem;
          display: flex;
          flex-direction: column;
          align-items: center;
          height: 100%;
          min-height: 300px;
        }
      </style>

    </div>
  </div>
</div>
</body>
</html>
