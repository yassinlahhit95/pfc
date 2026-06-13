<?php
include_once("db/socket.php");
// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}

$subs_nombre = utf8_decode($_POST['nombre']);
$subs_apellidos = utf8_decode($_POST['apellidos']);
$subs_dni = utf8_decode($_POST['dni']);
$subs_usuario = utf8_decode($_POST['usuario']);
$subs_ciclo = utf8_decode($_POST['ciclo']);
$archivonombre = "";

if (isset($_FILES["miarchivo"]['tmp_name']) && is_array($_FILES["miarchivo"]['tmp_name'])) {
    foreach($_FILES["miarchivo"]['tmp_name'] as $key => $tmp_name)
	{
		//Condicional si el fichero existe
		if($_FILES["miarchivo"]["name"][$key]) {
            // Validation: File Type and Size
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
            $max_size = 5 * 1024 * 1024; // 5MB

            $file_type = $_FILES["miarchivo"]["type"][$key];
            $file_size = $_FILES["miarchivo"]["size"][$key];

            if (!in_array($file_type, $allowed_types)) {
                die("Error: Tipo de archivo no permitido. Solo se aceptan JPG, PNG y PDF.");
            }
            if ($file_size > $max_size) {
                die("Error: El archivo es demasiado grande. El límite es 5MB.");
            }

			// Nombres de archivos de temporales
			$archivonombre = uniqid() . "_" . $_FILES["miarchivo"]["name"][$key]; 
			$fuente = $_FILES["miarchivo"]["tmp_name"][$key]; 
			
			$carpeta = 'archivos_dni/'; //Declaramos el nombre de la carpeta que guardara los archivos
			
			if(!file_exists($carpeta)){
				mkdir($carpeta, 0777) or die("Hubo un error al crear el directorio de almacenamiento");	
			}
			
			$dir=opendir($carpeta);
			$target_path = $carpeta.'/'.$archivonombre; //indicamos la ruta de destino de los archivos
			
			if(move_uploaded_file($fuente, $target_path)) {	
				//echo "Los archivos $archivonombre se han cargado de forma correcta.<br>";
				} else {	
				//echo "Se ha producido un error, por favor revise los archivos e intentelo de nuevo.<br>";
			}
			closedir($dir); //Cerramos la conexion con la carpeta destino
		}
	}
}
 
$sql = "INSERT INTO formulario_aceptacion (nombre, apellidos, dni, foto_dni, ciclo, fecha) VALUES ('" . $subs_nombre . "', '" . $subs_apellidos . "', '" . $subs_dni . "', '" . $archivonombre . "', '" . $subs_ciclo . "', NOW())";

if (mysqli_query($conn, $sql)) {
    // Generar PDF automáticamente
    require_once(__DIR__ . '/lib/pdf_generator.php');
    $datos_pdf = [
        'nombre' => utf8_encode($subs_nombre),
        'apellidos' => utf8_encode($subs_apellidos),
        'dni' => utf8_encode($subs_dni),
        'ciclo' => intval($subs_ciclo)
    ];
    generarPDF($datos_pdf, $conn);

    session_start();
$_SESSION['paso1'] = true;
$_SESSION['dni_usuario'] = $subs_dni;
header("Location: ikasgune_admision_aprobada.php?nombre=$subs_nombre&ciclo=$subs_ciclo&dni=$subs_dni");


} else {
      echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
