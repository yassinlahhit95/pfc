<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false;
$msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

if (isset($_POST['idEstudiante'])) {
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $estudiante = obtenerEstudiantePorId($idEstudiante);
    if ($estudiante && softDeleteEstudiante($idEstudiante)) {
        registrarAccionSecretaria('eliminar', 'estudiantes', $idEstudiante, $estudiante['nombreEstudiante']);
        $ok = true;
        $msg = "El estudiante «" . $estudiante['nombreEstudiante'] . "» se ha movido a la papelera.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Ocurrió un error al intentar eliminar al estudiante seleccionado.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}

header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
exit;
