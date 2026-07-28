<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_modulos');
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/modulos/verModulos.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['idModulo'])) {
    $idModulo = (int)$_POST['idModulo'];
    $con = obtenerConexion();

    // Cascada completa en una única transacción: si cualquier paso falla, se revierte
    // todo (antes cada DELETE se ejecutaba de forma independiente sin comprobar su
    // resultado, igual que el mismo patrón ya corregido en ciclos/borrar.php).
    mysqli_begin_transaction($con);
    $cascadaOk = true;

    $ejecutar = function(string $sql, string $tipos, ...$parametros) use ($con, &$cascadaOk) {
        if (!$cascadaOk) return;
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
        if (!mysqli_stmt_execute($stmt)) $cascadaOk = false;
    };

    // Obtener los retos vinculados al módulo antes de borrarlo
    $stmtRetos = mysqli_prepare($con, "SELECT idReto FROM modulo_reto WHERE idModulo = ?");
    mysqli_stmt_bind_param($stmtRetos, "i", $idModulo);
    mysqli_stmt_execute($stmtRetos);
    $resRetos = mysqli_stmt_get_result($stmtRetos);
    $idRetos = [];
    while ($filaReto = mysqli_fetch_assoc($resRetos)) { $idRetos[] = (int)$filaReto['idReto']; }

    foreach ($idRetos as $idReto) {
        $ejecutar("DELETE FROM calificaciones_retos WHERE idReto = ?", "i", $idReto);
        $ejecutar("DELETE FROM modulo_reto WHERE idReto = ?", "i", $idReto);
        $ejecutar("DELETE FROM retos WHERE idReto = ?", "i", $idReto);
    }

    $ejecutar("DELETE FROM calificaciones_modulos WHERE idModulo = ?", "i", $idModulo);
    $ejecutar("DELETE FROM modulo_profesor WHERE idModulo = ?", "i", $idModulo);
    $ejecutar("DELETE FROM modulos WHERE idModulo = ?", "i", $idModulo);

    if ($cascadaOk) {
        mysqli_commit($con);
        registrarAccion('borrar', 'modulos', $idModulo);
        $_SESSION['exito'] = "El módulo ha sido eliminado correctamente.";
    } else {
        mysqli_rollback($con);
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el módulo.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($isAjax) {
    header('Content-Type: application/json');
    $ok = empty($_SESSION['errores']);
    $msg = $ok ? ($_SESSION['exito'] ?? 'Módulo eliminado correctamente') : (is_array($_SESSION['errores']) ? implode(', ', $_SESSION['errores']) : $_SESSION['errores']);
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
