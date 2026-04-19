<?php
session_start();
require_once "../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = $_POST['idEstudiante'];
    
    if (isset($_FILES['archivoTFG']) && $_FILES['archivoTFG']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = "../../uploads/tfg/";
        
        // Crear el directorio si no existe
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0777, true);
        }

        $nombreOriginal = $_FILES['archivoTFG']['name'];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        // Solo permitimos PDF
        if ($extension !== 'pdf') {
            $_SESSION['error'] = "Error: Solo se permiten archivos PDF.";
            header("Location: ../../vistas/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
            exit;
        }
        
        // Nombre unico: id_nombre.pdf
        $nombreArchivo = "TFG_" . $idEstudiante . "_" . time() . ".pdf";
        $rutaDestino = $directorioSubida . $nombreArchivo;

        if (move_uploaded_file($_FILES['archivoTFG']['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido con éxito.";
            } else {
                $_SESSION['error'] = "Error al guardar en la base de datos.";
            }
        } else {
            $_SESSION['error'] = "Error al mover el archivo.";
        }
    } else {
        $_SESSION['error'] = "No se ha seleccionado ningún archivo o hay un error.";
    }
}

header("Location: ../../vistas/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
exit;
?>
