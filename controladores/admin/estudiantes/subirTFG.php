<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_POST['subirTFG'])) {

    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $archivo = $_FILES['archivoTFG'] ?? null;
    
    $errores = [];

    if (empty($idEstudiante)) {
        $errores[] = "Falta ID estudiante.";
    }

    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir el archivo (Código: " . ($archivo['error'] ?? 'No file') . ").";
    } else {
        // 2. Validar tamaño (Máx 10MB)
        $maxSize = 10 * 1024 * 1024;
        if ($archivo['size'] > $maxSize) {
            $errores[] = "El archivo es demasiado grande. Máximo 10MB.";
        }

        // 3. Validar contenido real (MIME type)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['application/pdf'];
        if (!in_array($mime, $allowedMimes)) {
            $errores[] = "Tipo de archivo no permitido. Solo se aceptan PDFs.";
        }
    }

    if (empty($errores)) {
        // 4. Generar nombre aleatorio seguro
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'pdf') $extension = 'pdf'; // Forzar PDF
        
        $nombreAleatorio = bin2hex(random_bytes(16)) . "." . $extension;
        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreAleatorio;

        // Asegurar que el directorio existe
        if (!is_dir(dirname($rutaDestino))) {
            mkdir(dirname($rutaDestino), 0755, true);
        }

        // 5. Eliminar archivo anterior si existe
        eliminarArchivoTFG($idEstudiante);

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreAleatorio)) {
                $_SESSION['exito'] = "TFG subido correctamente.";
                header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
                exit;
            } else {
                unlink($rutaDestino); // Borrar si falla la DB
                $errores[] = "Error al actualizar la base de datos.";
            }
        } else {
            $errores[] = "Error al guardar el archivo en el servidor.";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = implode(" ", $errores);
    }
    
    header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
