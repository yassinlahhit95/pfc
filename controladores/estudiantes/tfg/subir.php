<?php
session_start();
require_once "../../../modelos/tfg.php";

// Información clara: Esperamos datos POST con archivo
if (isset($_POST['subirTFG'])) {
    $idDelEstudiante = $_POST['idEstudiante'];

    // Verificar que el ID coincida con la sesión
    if (empty($idDelEstudiante)) {
        $_SESSION['error'] = "ID de estudiante no proporcionado.";
        header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
    } else if ($_SESSION['idEstudiante'] != $idDelEstudiante) {
        $_SESSION['error'] = "Error: No estás autenticado correctamente.";
        header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
    } else {
        // Verificar si se subió un archivo
        if (isset($_FILES['archivoTFG']) && $_FILES['archivoTFG']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = $_FILES['archivoTFG']['name'];
            $tipoArchivo = $_FILES['archivoTFG']['type'];
            $rutaTemporal = $_FILES['archivoTFG']['tmp_name'];

            // Solo permitir PDFs
            if ($tipoArchivo != 'application/pdf') {
                $_SESSION['error'] = "Error: Solo se permiten archivos PDF.";
                header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
                exit;
            }

            // Renombrar archivo para evitar duplicados
            $nombreFinal = "tfg_" . $idDelEstudiante . "_" . time() . ".pdf";
            $destino = "../../../public/uploads/tfg/" . $nombreFinal;

            // Crear carpeta si no existe
            if (!is_dir("../../../public/uploads/tfg/")) {
                mkdir("../../../public/uploads/tfg/", 0777, true);
            }

            if (move_uploaded_file($rutaTemporal, $destino)) {
                // Actualizar la base de datos
                if (actualizarTFG($idDelEstudiante, $nombreFinal)) {
                    $_SESSION['exito'] = "Tu TFG ha sido subido correctamente.";
                } else {
                    $_SESSION['error'] = "Error al registrar el archivo en el sistema.";
                }
            } else {
                $_SESSION['error'] = "Error al mover el archivo al servidor.";
            }
        } else {
            $_SESSION['error'] = "No se ha seleccionado ningún archivo o hay un error de subida.";
        }
    }
    header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
    exit;
}

header("Location: /pfc/vistas/estudiantes/dashboard.php");
exit;
?>