<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['subirTFG'])) {
    if (!FeatureGuard::check('feature_subida_tfg')) {
        $_SESSION['errores'] = "La entrega del TFG está cerrada en este momento.";
        header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
    }
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
    }
    $idEstudiante = $_SESSION['idEstudiante']; // Siempre de la sesión (evita IDOR)
    $archivoTFG   = $_FILES['archivoTFG'] ?? null;
    $errores      = [];

    // Cuando se supera post_max_size, PHP vacía $_FILES por completo — se detecta explícitamente
    if (empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > 0) {
        $errores[] = "El archivo supera el tamaño máximo permitido (20 MB).";
    } elseif (!$archivoTFG || $archivoTFG['error'] === UPLOAD_ERR_NO_FILE) {
        $errores[] = "Debes seleccionar un archivo.";
    } elseif ($archivoTFG['error'] === UPLOAD_ERR_INI_SIZE || $archivoTFG['error'] === UPLOAD_ERR_FORM_SIZE) {
        $errores[] = "El archivo supera el tamaño máximo permitido (20 MB).";
    } elseif ($archivoTFG['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir el archivo, inténtalo de nuevo.";
    } else {
        $ext = strtolower(pathinfo($archivoTFG['name'], PATHINFO_EXTENSION));
        $mimeAllowed = ['application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $mime = mime_content_type($archivoTFG['tmp_name']);
        if (!in_array($ext, ['pdf', 'doc', 'docx']) || !in_array($mime, $mimeAllowed)) {
            $errores[] = "Solo se permiten archivos PDF o Word (.doc, .docx).";
        } elseif ($archivoTFG['size'] > 20 * 1024 * 1024) {
            $errores[] = "El archivo supera el tamaño máximo permitido (20 MB).";
        }
    }

    if (empty($errores)) {
        $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
        if (!$datosEstudiante) {
            $_SESSION['errores'] = "No se encontró el estudiante en el sistema.";
            header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
        }
        $ext           = strtolower(pathinfo($archivoTFG['name'], PATHINFO_EXTENSION));
        $nombreLimpio  = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $datosEstudiante['nombreEstudiante']));
        $nombreArchivo = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . "." . $ext;
        $rutaDestino   = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        // Delete old file from disk before saving the new one
        $tfgActual = obtenerTFGporEstudiante($idEstudiante);
        if (!empty($tfgActual['archivoTFG'])) {
            $rutaVieja = __DIR__ . "/../../../public/uploads/pfc/" . $tfgActual['archivoTFG'];
            if (file_exists($rutaVieja)) {
                unlink($rutaVieja);
            }
        }

        if (move_uploaded_file($archivoTFG['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "El TFG ha sido subido correctamente.";
            } else {
                $_SESSION['errores'] = "El archivo fue guardado pero no se pudo actualizar la base de datos.";
            }
        } else {
            $_SESSION['errores'] = "Error al guardar el archivo en el servidor.";
        }
    } else {
        $_SESSION['errores'] = implode(' ', $errores);
    }

    header("Location: ../../../vistas/estudiantes/pfc/subir.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/estudiantes/inicio/dashboard.php");
exit;
