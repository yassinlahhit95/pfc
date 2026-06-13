<?php
require_once 'admin/generar_excel/conexion.php';
$conexion = new Conexion();
$db = $conexion->conectarse();

$nombreusuario = isset($_POST['nombre']) ? $_POST['nombre'] : "";
$apellidosusuario = isset($_POST['apellidos']) ? $_POST['apellidos'] : "";
$dniusuario = isset($_POST['dni']) ? $_POST['dni'] : "";
$ciclousuario = isset($_POST['ciclo']) ? intval($_POST['ciclo']) : null;
$paso = isset($_POST['generar']) ? 'condiciones' : 'inicio';

$query = "SELECT id_ciclo, nombre_ciclo FROM precios_ciclos ORDER BY id_ciclo ASC";
$result = $db->query($query);
$ciclos = [];
while ($row = $result->fetch_assoc()) {
    $ciclos[$row['id_ciclo']] = $row['nombre_ciclo'];
}

$datos_ciclo = null;
if ($ciclousuario !== null) {
    $stmt = $db->prepare("SELECT * FROM precios_ciclos WHERE id_ciclo = ?");
    $stmt->bind_param("i", $ciclousuario);
    $stmt->execute();
    $datos_ciclo = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Formulario Aceptación</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h3>Formulario de aceptación - Paso 1</h3>

  <?php if ($paso === 'inicio'): ?>
    <form method="post" action="" class="form-horizontal">
      <div class="form-group">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombreusuario); ?>" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Apellidos:</label>
        <input type="text" name="apellidos" value="<?php echo htmlspecialchars($apellidosusuario); ?>" class="form-control" required>
      </div>
	        <div class="form-group">
        <label>DNI:</label>
        <input type="text" name="dni" value="<?php echo htmlspecialchars($dniusuario); ?>" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Ciclo:</label>
        <select name="ciclo" class="form-control" required>
          <option value="" disabled selected>Selecciona un ciclo</option>
          <?php foreach ($ciclos as $id => $nombre): ?>
            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" name="generar" class="btn btn-primary">Generar condiciones</button>
    </form>
  <?php else: ?>
    <div class="card mt-4">
      <div class="card-header"><strong>Condiciones económicas</strong></div>
      <div class="card-body">

<h4 class="mb-4">Términos y condiciones económicas</h4>
<p>Al realizar la matrícula para el presente curso escolar en <b>CALASANZ LANBIDE IKASTEGIA</b>, me comprometo voluntariamente a:</p>
<ol>
  <li>Aceptar y respetar el Proyecto Educativo del Centro (PEC).</li>
  <li>Aceptar el Reglamento de Régimen Interno (normativa que regula la vida en nuestro Centro).</li>
  <li>Aceptar las actividades complementarias y/o extraescolares que el centro, de acuerdo con el Reglamento de Conciertos Educativos, pueda establecer y siempre que las mismas sean aprobadas por el Consejo Escolar del centro.</li>
  <li>Aceptar cuantas normas académicas pudieran surgir del Consejo Escolar y aquellos acuerdos del mismo que afecten a servicios complementarios ofrecidos en el centro.</li>
  <li>Al formalizar la matrícula, se abonará la mensualidad de septiembre y el seguro escolar, así como la plataforma de aprendizaje online de Office 365. En caso de baja antes del comienzo del periodo lectivo, siempre que se cubra la plaza que se deja vacante, se devolverá el importe del seguro escolar y de las plataformas digitales correspondientes y el 50% del importe de la mensualidad, asumiendo el 50% restante en concepto de gastos de tramitación.</li>
</ol>
<p>*Los datos facilitados por Vd. se incluirán en un fichero responsabilidad de <b>CALASANZ SANTURTZI SOCIEDAD LIMITADA</b>, y podrán ser cedidos a las Consejerías de Educación, Empleo y Políticas Sociales del Gobierno Vasco y otros organismos oficiales, para la correcta gestión de los servicios solicitados. Siempre y cuando se cumplan los requisitos exigidos por la normativa, usted podrá ejercer sus derechos de acceso, rectificación, limitación de tratamiento, supresión (“derecho al olvido”), portabilidad, oposición y revocación, en los términos que establece la normativa vigente y aplicable en materia de protección de datos, dirigiendo su petición a la dirección postal C/ HOSPITAL BAJO 11 48980, SANTURTZI (BIZKAIA) o bien a través de correo electrónico <a href="mailto:lopd@calasanz.eus">lopd@calasanz.eus</a>.</p>
<p>Mediante la firma de este documento autorizo a <b>CALASANZ SANTURTZI</b> a publicar las fotos obtenidas en los eventos realizados y organizados por <b>CALASANZ LANBIDE IKASTEGIA</b>, en los medios de difusión o comunicación que el centro establezca.</p>

        <p><b>Nombre:</b> <?php echo htmlspecialchars($nombreusuario); ?></p>
        <p><b>Apellidos:</b> <?php echo htmlspecialchars($apellidosusuario); ?></p>
        <p><b>DNI:</b> <?php echo htmlspecialchars($dniusuario); ?></p>		
		<p><b>Ciclo seleccionado:</b> <?php echo $ciclos[$ciclousuario]; ?></p>
        <hr>
        <p>
        <?php
        if ($datos_ciclo) {
          echo htmlspecialchars($datos_ciclo['nombre_ciclo']) . " consta de " . $datos_ciclo['num_recibos'] . " recibos de " . number_format($datos_ciclo['precio_mensual'], 2, ',', '.') . "€ al mes, excepto la primera mensualidad que se paga en efectivo al formalizar la matrícula junto a las licencias digitales y seguros, y será de un total de " . number_format($datos_ciclo['pago_inicial'], 2, ',', '.') . "€.";
        } else {
          echo "Ciclo no válido.";
        }
        ?>
        </p>
        <form method="post" action="envio_ikasgune_aceptacion.php">
          <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombreusuario); ?>">
          <input type="hidden" name="apellidos" value="<?php echo htmlspecialchars($apellidosusuario); ?>">
          <input type="hidden" name="dni" value="<?php echo htmlspecialchars($dniusuario); ?>">		  
          <input type="hidden" name="ciclo" value="<?php echo $ciclousuario; ?>">
          <div class="form-check mt-3">
            <input type="checkbox" name="aceptar" required> Acepto las condiciones económicas presentadas.
          </div>
          <button type="submit" class="btn btn-success mt-3">Aceptar y Enviar</button>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
