<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
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

    // Obtener los retos vinculados al módulo antes de borrarlo
    $sql1 = "SELECT idReto FROM modulo_reto WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $idRetos = [];
    while ($r = mysqli_fetch_assoc($res)) { $idRetos[] = (int)$r['idReto']; }

    foreach ($idRetos as $idReto) {
        $sql2 = "DELETE FROM calificaciones_retos WHERE idReto = ?";
        $resultado = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($resultado, "i", $idReto);
        mysqli_stmt_execute($resultado);

        $sql3 = "DELETE FROM modulo_reto WHERE idReto = ?";
        $resultado = mysqli_prepare($con, $sql3);
        mysqli_stmt_bind_param($resultado, "i", $idReto);
        mysqli_stmt_execute($resultado);

        $sql4 = "DELETE FROM retos WHERE idReto = ?";
        $resultado = mysqli_prepare($con, $sql4);
        mysqli_stmt_bind_param($resultado, "i", $idReto);
        mysqli_stmt_execute($resultado);
    }

    $sql5 = "DELETE FROM calificaciones_modulos WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql5);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);

    $sql6 = "DELETE FROM modulo_profesor WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql6);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);

    $sql7 = "DELETE FROM modulos WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql7);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    if (mysqli_stmt_execute($resultado)) {
        registrarAccion('borrar', 'modulos', $idModulo);
        $_SESSION['exito'] = "El módulo ha sido eliminado correctamente.";
    } else {
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
