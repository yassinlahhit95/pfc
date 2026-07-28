<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_modulos');
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$ok  = false;
$msg = 'Datos no recibidos';

if (isset($_POST['idModulo'])) {
    if (!Security::validateCSRFToken()) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/modulos/verModulos.php"); exit;
    }
    $idModulo   = (int)$_POST['idModulo'];
    $idProfesor = !empty($_POST['idProfesor']) ? (int)$_POST['idProfesor'] : 0;

    // Snapshot antes de limpiar, para saber si $idProfesor es realmente un
    // profesor nuevo en este módulo (y por tanto a quien notificar) o el
    // mismo que ya estaba.
    $idsAntes = array_map('intval', listarProfesoresDeModulo($idModulo));

    limpiarProfesoresModulo($idModulo);

    $hayError = false;
    if ($idProfesor > 0 && !asociarModuloProfesor($idModulo, $idProfesor)) {
        $hayError = true;
    }

    if (!$hayError) {
        registrarAccion('actualizar_profesor', 'modulos', $idModulo, "Profesor #$idProfesor");
        if ($idProfesor > 0 && !in_array($idProfesor, $idsAntes, true)) {
            $moduloInfo = obtenerModuloPorId($idModulo);
            if ($moduloInfo) {
                crearNotificacion($idProfesor, 'profesor', 'modulo_asignado',
                    'Se te ha asignado un nuevo módulo: ' . $moduloInfo['nombreModulo'],
                    '../../../vistas/profesores/inicio/dashboard.php');
            }
        }
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
