<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/RateLimiter.php";
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
    $idEstudianteRl = (int)$_SESSION['idEstudiante'];
    $conRl = obtenerConexion();
    if (!RateLimiter::allow($conRl, "tfg_upload:{$idEstudianteRl}", 5, 3600, 300)) {
        $_SESSION['errores'] = "Has subido demasiados archivos en poco tiempo. Espera un momento e inténtalo de nuevo.";
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
        $mimeAllowed = [
            'application/pdf', 
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip', 
            'application/octet-stream'
        ];
        $mime = @mime_content_type($archivoTFG['tmp_name']);
        if (!in_array($ext, ['pdf', 'doc', 'docx']) || ($mime && !in_array($mime, $mimeAllowed))) {
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
        $mimesPorExt   = ['pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $mimeReal      = $mimesPorExt[$ext] ?? 'application/octet-stream';

        // Guardar ruta/clave del archivo anterior ANTES de cualquier operación de escritura.
        // El orden correcto es: subir nuevo → actualizar BD → borrar antiguo.
        $tfgActual = obtenerTFGporEstudiante($idEstudiante);
        $rutaVieja = (!empty($tfgActual['archivoTFG']))
            ? __DIR__ . "/../../../public/uploads/pfc/" . $tfgActual['archivoTFG']
            : null;
        $r2KeyVieja = !empty($tfgActual['archivoTFG']) ? 'pfc/' . $tfgActual['archivoTFG'] : null;

        require_once __DIR__ . "/../../../include/R2Client.php";
        $bytes = file_get_contents($archivoTFG['tmp_name']);
        $subioOk = $bytes !== false && R2Client::putObject('pfc/' . $nombreArchivo, $bytes, $mimeReal);
        @unlink($archivoTFG['tmp_name']);

        if ($subioOk) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                // BD actualizada correctamente — ahora es seguro eliminar el archivo anterior
                // (en cualquiera de los dos almacenamientos: no hay backfill, así que el
                // archivo anterior puede seguir en disco local o ya estar en R2).
                if ($rutaVieja && file_exists($rutaVieja)) {
                    @unlink($rutaVieja);
                }
                if ($r2KeyVieja) {
                    R2Client::deleteObject($r2KeyVieja);
                }
                $_SESSION['exito'] = "El TFG ha sido subido correctamente.";
            } else {
                // Fallo en BD — el nuevo archivo es un huérfano, elimínarlo; el anterior sigue en BD
                R2Client::deleteObject('pfc/' . $nombreArchivo);
                $_SESSION['errores'] = "Error al actualizar la base de datos. El TFG anterior sigue activo.";
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
