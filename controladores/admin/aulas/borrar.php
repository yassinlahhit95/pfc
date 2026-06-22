<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/aulas.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/aulas/gestionAulas.php"); exit;
}

if (isset($_POST['idAula'])) {
    $idAula = (int)$_POST['idAula'];
    if (eliminarAula($idAula)) {
        registrarAccion('borrar', 'aulas', $idAula);
        $ok = true; $msg = "El aula ha sido eliminada correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Ocurrió un error al intentar eliminar el aula seleccionada.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
