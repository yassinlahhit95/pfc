<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['subirTFG'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $archivo      = $_FILES['archivoTFG'] ?? null;
    $errores      = [];

    if (empty($idEstudiante)) {
        $errores[] = "Falta el identificador del estudiante.";
    }

    if (!$archivo || $archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir el archivo (código: " . ($archivo['error'] ?? 'ninguno') . ").";
    } else {
        // Validar tamaño máximo: 10 MB
        if ($archivo['size'] > 10 * 1024 * 1024) {
            $errores[] = "El archivo supera el tamaño máximo permitido de 10 MB.";
        }

        // Validar tipo MIME real (no solo la extensión)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, ['application/pdf'])) {
            $errores[] = "Tipo de archivo no permitido. Solo se aceptan PDFs.";
        }
    }

    if (empty($errores)) {
        $extension     = 'pdf'; // se fuerza siempre PDF
        $nombreAleatorio = bin2hex(random_bytes(16)) . '.' . $extension;
        $rutaDestino   = __DIR__ . "/../../../public/uploads/pfc/" . $nombreAleatorio;

        if (!is_dir(dirname($rutaDestino))) {
            mkdir(dirname($rutaDestino), 0755, true);
        }

        // Eliminar el archivo anterior antes de guardar el nuevo
        eliminarArchivoTFG($idEstudiante);

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreAleatorio)) {
                registrarAccion('subir_tfg', 'estudiantes', $idEstudiante);
                $_SESSION['exito'] = "El TFG ha sido subido correctamente.";
                header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
                exit;
            }
            unlink($rutaDestino); // revertir subida si falla la BD
            $errores[] = "Error al actualizar el registro en la base de datos.";
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

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
