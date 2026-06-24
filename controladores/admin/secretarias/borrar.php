<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';
require_once __DIR__ . '/../../../modelos/log.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
       && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false;
$msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
    exit;
}

if (!empty($_POST['idSecretaria'])) {
    $id = (int)$_POST['idSecretaria'];
    if (eliminarSecretaria($id)) {
        registrarAccion('borrar', 'secretarias', $id);
        $ok  = true;
        $msg = "Secretaria eliminada correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Error al eliminar la secretaria.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/secretarias/verSecretarias.php");
exit;
