<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (isset($_POST['idReto'])) {
    if (!Security::validateCSRFToken()) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/retos/lista.php"); exit;
    }
    $idReto = (int)$_POST['idReto'];

    $_esTutor      = !empty($_SESSION['esTutor']);
    $_idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
    $_autorizado   = $_esTutor && $_idCicloTutor
        ? retoPerteneceACiclo($idReto, $_idCicloTutor)
        : retoPerteneceAProfesor($idReto, $_SESSION['idProfesor']);
    if ($idReto && !$_autorizado) {
        $msg = "No tienes permiso sobre este reto.";
        $_SESSION['errores'] = $msg;
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'msg' => $msg]);
            exit;
        }
        header("Location: ../../../vistas/profesores/retos/lista.php"); exit;
    }

    if ($idReto && eliminarReto($idReto)) {
        $ok = true; $msg = "Reto eliminado.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Error al eliminar el reto.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
