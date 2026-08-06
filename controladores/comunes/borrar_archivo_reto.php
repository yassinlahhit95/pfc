<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if ((empty($_SESSION['idAdmin']) && empty($_SESSION['idProfesor'])) || !empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    header("Content-Type: application/json");
    echo json_encode(['ok' => false, 'msg' => 'Acceso denegado. No tienes permiso para realizar esta acción.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idArchivo = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
// Prevenir open redirect: rechazar cualquier URL con protocolo o relativa de protocolo
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '';
if (empty($redirect) || preg_match('#^(https?:)?//#i', $redirect)) {
    $redirect = '../../vistas/admin/inicio/dashboard.php';
}
$isAjax = (isset($_POST['ajax']) || isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) {
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Inténtelo de nuevo.']);
        exit;
    }
    header("Location: " . $redirect);
    exit;
}

if ($idArchivo <= 0) {
    if ($isAjax) {
        echo json_encode(['ok' => false, 'msg' => 'El identificador del archivo no ha sido proporcionado.']);
        exit;
    }
    header("Location: " . $redirect);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$archivo = obtenerArchivoRetoPorId($idArchivo);
if ($archivo && !empty($_SESSION['idProfesor']) && empty($_SESSION['idAdmin'])) {
    if (!retoPerteneceAProfesor($archivo['idReto'], $_SESSION['idProfesor'])) {
        if ($isAjax) {
            echo json_encode(['ok' => false, 'msg' => 'Acceso denegado. No tienes permiso sobre este archivo.']);
            exit;
        }
        $_SESSION['errores'] = "Acceso denegado. No tienes permiso sobre este archivo.";
        header("Location: " . $redirect);
        exit;
    }
}
if ($archivo) {
    $rutaFisica = __DIR__ . '/../../' . ltrim($archivo['rutaArchivo'], '/');
    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }
    require_once __DIR__ . '/../../include/R2Client.php';
    $r2Key = ltrim(preg_replace('#^public/uploads/#', '', $archivo['rutaArchivo']), '/');
    R2Client::deleteObject($r2Key);

    if (eliminarArchivoReto($idArchivo)) {
        if ($isAjax) {
            echo json_encode(['ok' => true, 'msg' => 'El archivo ha sido eliminado correctamente.']);
            exit;
        }
        $_SESSION['exito'] = "El archivo ha sido eliminado correctamente.";
    } else {
        if ($isAjax) {
            echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar el archivo de la base de datos.']);
            exit;
        }
        $_SESSION['errores'] = "No se pudo eliminar el archivo de la base de datos.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($isAjax) {
    echo json_encode(['ok' => false, 'msg' => 'El archivo seleccionado no ha sido encontrado.']);
    exit;
}

header("Location: " . $redirect);
exit;
