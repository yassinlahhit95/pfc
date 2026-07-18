<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    header("Location: ../../../vistas/admin/tutores/verTutores.php");
    exit;
}

if (!empty($_POST['idTutor'])) {
    $idTutor = (int)$_POST['idTutor'];

    if (eliminarTutor($idTutor)) {
        registrarAccion('borrar', 'tutores', $idTutor);
        $ok = true; $msg = "Tutor eliminado correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Error al eliminar el tutor.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/tutores/verTutores.php");
exit;
