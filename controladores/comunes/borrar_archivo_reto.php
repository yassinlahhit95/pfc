<?php
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";

// Solo permitir si es admin o profesor
if ((empty($_SESSION['idAdmin']) && empty($_SESSION['idProfesor'])) || !empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$idArchivo = $_GET['id'] ?? '';
$idReto    = $_GET['idReto'] ?? '';
// Prevenir open redirect: rechazar cualquier URL con protocolo o relativa de protocolo
$redirect = $_GET['redirect'] ?? '';
if (empty($redirect) || preg_match('#^(https?:)?//#i', $redirect)) {
    $redirect = '../../vistas/admin/inicio/dashboard.php';
}
$isAjax = (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));

if (empty($idArchivo)) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
        exit;
    }
    header("Location: " . $redirect);
    exit;
}

$archivo = obtenerArchivoRetoPorId($idArchivo);
if ($archivo && !empty($_SESSION['idProfesor']) && empty($_SESSION['idAdmin'])) {
    if (!retoPerteneceAProfesor($archivo['idReto'], $_SESSION['idProfesor'])) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
            exit;
        }
        $_SESSION['errores'] = "No tienes permiso sobre este archivo.";
        header("Location: " . $redirect);
        exit;
    }
}
if ($archivo) {
    // Borrar archivo físico
    $rutaFisica = "../../" . $archivo['rutaArchivo'];
    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }
    
    // Borrar de la BD
    if (eliminarArchivoReto($idArchivo)) {
        if ($isAjax) {
            echo json_encode(['status' => 'success', 'message' => 'Archivo eliminado']);
            exit;
        }
        $_SESSION['exito'] = "Archivo eliminado correctamente.";
    } else {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'Error en BD']);
            exit;
        }
        $_SESSION['errores'] = "No se pudo eliminar el archivo de la base de datos.";
    }
}

if ($isAjax) {
    echo json_encode(['status' => 'error', 'message' => 'Archivo no encontrado']);
    exit;
}

header("Location: " . $redirect);
exit;
?>
