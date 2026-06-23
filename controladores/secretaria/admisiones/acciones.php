<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$action = $_GET['action'] ?? '';

if ($action === 'update_status') {
    $idPreMatricula = (int)($_POST['idPreMatricula'] ?? 0);
    $estado         = Security::sanitize($_POST['estado'] ?? '');
    $observaciones  = Security::sanitize($_POST['observaciones'] ?? '');

    $estadosValidos = ['EN_REVISION', 'ADMITIDO', 'RECHAZADO', 'SUBSANACION'];
    $ok = false;
    $msg = '';

    if ($idPreMatricula <= 0) {
        $msg = "ID de solicitud no válido.";
    } elseif (!in_array($estado, $estadosValidos)) {
        $msg = "Estado no válido.";
    } else {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con,
            "UPDATE pre_matriculas SET estado = ?, observaciones = ? WHERE idPreMatricula = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $estado, $observaciones, $idPreMatricula);
        $ok  = mysqli_stmt_execute($stmt);
        $msg = $ok ? "Estado actualizado correctamente." : "Error al actualizar el estado.";
    }

    if ($_isAjaxGuardSec) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => $ok, 'msg' => $msg]);
        exit;
    }

    if ($ok) {
        $_SESSION['exito'] = $msg;
    } else {
        $_SESSION['errores'] = $msg;
    }
    header("Location: ../../../vistas/secretaria/admisiones/listado.php");
    exit;
}

header("Location: ../../../vistas/secretaria/admisiones/listado.php");
exit;
