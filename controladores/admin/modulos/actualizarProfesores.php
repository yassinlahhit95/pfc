<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$ok  = false;
$msg = 'Datos no recibidos';

if (isset($_POST['idModulo'])) {
    $idModulo   = (int)$_POST['idModulo'];
    $idProfesor = !empty($_POST['idProfesor']) ? (int)$_POST['idProfesor'] : 0;

    limpiarProfesoresModulo($idModulo);

    $hayError = false;
    if ($idProfesor > 0 && !asociarModuloProfesor($idModulo, $idProfesor)) {
        $hayError = true;
    }

    if (!$hayError) {
        registrarAccion('actualizar_profesor', 'modulos', $idModulo, "Profesor #$idProfesor");
        $ok  = true;
        $msg = 'Profesor asignado correctamente.';
        $_SESSION['exito'] = $msg;
    } else {
        $msg = 'No se pudo asignar el profesor al módulo.';
        $_SESSION['errores'] = $msg;
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
