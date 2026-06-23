<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if ((empty($_SESSION['idAdmin']) && empty($_SESSION['idProfesor'])) || !empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    header("Content-Type: application/json");
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No dispone de los permisos necesarios para realizar esta acción.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idArchivo = (int)($_GET['id'] ?? 0);
// Prevenir open redirect: rechazar cualquier URL con protocolo o relativa de protocolo
$redirect = $_GET['redirect'] ?? '';
if (empty($redirect) || preg_match('#^(https?:)?//#i', $redirect)) {
    $redirect = '../../vistas/admin/inicio/dashboard.php';
}
$isAjax = (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));

if ($idArchivo <= 0) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'El identificador del archivo no ha sido proporcionado.']);
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
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No dispone de los permisos necesarios sobre este archivo.']);
            exit;
        }
        $_SESSION['errores'] = "Acceso denegado. No dispone de los privilegios necesarios sobre el archivo seleccionado.";
        header("Location: " . $redirect);
        exit;
    }
}
if ($archivo) {
    $rutaFisica = __DIR__ . '/../../' . ltrim($archivo['rutaArchivo'], '/');
    if (file_exists($rutaFisica)) {
        unlink($rutaFisica);
    }

    if (eliminarArchivoReto($idArchivo)) {
        if ($isAjax) {
            echo json_encode(['status' => 'success', 'message' => 'El archivo ha sido eliminado correctamente.']);
            exit;
        }
        $_SESSION['exito'] = "El archivo ha sido eliminado correctamente.";
    } else {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error en el sistema al intentar eliminar el archivo de la base de datos.']);
            exit;
        }
        $_SESSION['errores'] = "No se pudo eliminar el registro del archivo de la base de datos.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($isAjax) {
    echo json_encode(['status' => 'error', 'message' => 'El archivo seleccionado no ha sido encontrado.']);
    exit;
}

header("Location: " . $redirect);
exit;
