<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";

if (isset($_POST['subirTFG'])) {
    $cfg = obtenerConfiguracionCentro();
    if (empty($cfg['feature_subida_tfg'])) {
        $_SESSION['errores'] = "La entrega del TFG está cerrada en este momento.";
        header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
    }
    if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['errores'] = "La sesión ha caducado. Recarga la página e inténtalo de nuevo.";
        header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
    }

    // Seguridad: SIEMPRE el id de la sesión, nunca el del formulario (evita IDOR)
    $idEstudiante = $_SESSION['idEstudiante'];
    $archivoTFG   = $_FILES['archivoTFG'] ?? null;
    $errores      = [];

    // When post_max_size is exceeded PHP empties $_FILES entirely — catch it explicitly
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
        if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
            $errores[] = "Solo se permiten archivos PDF o Word (.doc, .docx).";
        } elseif ($archivoTFG['size'] > 20 * 1024 * 1024) {
            $errores[] = "El archivo supera el tamaño máximo permitido (20 MB).";
        }
    }

    if (empty($errores)) {
        $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
        if (!$datosEstudiante) {
            $_SESSION['errores'] = "Estudiante no válido.";
            header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
        }
        $ext           = strtolower(pathinfo($archivoTFG['name'], PATHINFO_EXTENSION));
        $nombreLimpio  = preg_replace('/[^A-Za-z0-9_]/', '', str_replace(' ', '_', $datosEstudiante['nombreEstudiante']));
        $nombreArchivo = "TFG_" . $nombreLimpio . "_" . date('d-m-Y_H-i-s') . "." . $ext;
        $rutaDestino   = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        if (move_uploaded_file($archivoTFG['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido correctamente.";
            } else {
                $_SESSION['errores'] = "Error al actualizar la base de datos.";
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

header("Location: ../../../vistas/estudiantes/inicio/dashboard.php");
exit;
