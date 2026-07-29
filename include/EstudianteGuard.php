<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/Security.php';
ob_start();
Security::initSession();

$_isAjaxGuardEst = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
               && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (empty($_SESSION['idEstudiante'])) {
    if ($_isAjaxGuardEst) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    }
    header('Location: /vistas/login.php');
    exit;
}

// Bloquear acciones hasta que se cambie la contraseña temporal
if (!empty($_SESSION['must_change_password'])) {
    if ($_isAjaxGuardEst) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes cambiar tu contraseña antes de continuar.']);
        exit;
    }
    header('Location: /vistas/cambiar_password.php');
    exit;
}

// Bloqueo Suave por Impagos (Soft-Lock) para Aula Digital
$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/vistas/estudiantes/aula/') !== false) {
    require_once __DIR__ . '/../modelos/conectar.php';
    $db = obtenerConexion();
    $idEst = $_SESSION['idEstudiante'];
    $sql = "SELECT fechaProximoPago, prorrogaHasta FROM pagos WHERE idEstudiante = ? ORDER BY fechaProximoPago DESC LIMIT 1";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEst);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $fechaProximo = $row['fechaProximoPago'];
        $prorroga = $row['prorrogaHasta'];
        $hoy = date('Y-m-d');
        
        $vencido = ($fechaProximo < $hoy);
        $prorrogaInvalida = (empty($prorroga) || $prorroga < $hoy);
        
        if ($vencido && $prorrogaInvalida) {
            if ($_isAjaxGuardEst) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'msg' => 'Acceso bloqueado por impagos.']);
                exit;
            }
            header('Location: /vistas/estudiantes/pagos_pendientes.php');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    if ($_isAjaxGuardEst) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

require_once __DIR__ . '/SuspensionGuard.php';
