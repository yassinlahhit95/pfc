<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/ciclos/verCiclos.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO — Eliminación en cascada del ciclo
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['idCiclo'])) {
    $idCiclo = (int) $_POST['idCiclo'];
    $con = obtenerConexion();

    // Password required for this sensitive cascade operation
    $stmtPw = mysqli_prepare($con, "SELECT password FROM directores WHERE idDirector = ?");
    $idAdmin = (int)$_SESSION['idAdmin'];
    mysqli_stmt_bind_param($stmtPw, "i", $idAdmin);
    mysqli_stmt_execute($stmtPw);
    $resPw = mysqli_stmt_get_result($stmtPw);
    $adminRow = mysqli_fetch_assoc($resPw);
    if (!$adminRow || !password_verify($_POST['admin_password'] ?? '', $adminRow['password'])) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'msg' => 'Contraseña incorrecta.']);
            exit;
        }
        $_SESSION['errores'] = 'Contraseña incorrecta.';
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }

    // ── Retos y calificaciones de cada módulo ──
    $sql1 = "SELECT idModulo FROM modulos WHERE idCiclo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);
    $resM = mysqli_stmt_get_result($resultado);
    $idModulos = [];
    while ($r = mysqli_fetch_assoc($resM)) { $idModulos[] = (int) $r['idModulo']; }

    foreach ($idModulos as $idModulo) {
        $sql2 = "SELECT idReto FROM modulo_reto WHERE idModulo = ?";
        $resultado = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($resultado, "i", $idModulo);
        mysqli_stmt_execute($resultado);
        $resR = mysqli_stmt_get_result($resultado);
        $idRetos = [];
        while ($r2 = mysqli_fetch_assoc($resR)) { $idRetos[] = (int) $r2['idReto']; }

        foreach ($idRetos as $idReto) {
            $sql3 = "DELETE FROM calificaciones_retos WHERE idReto = ?";
            $resultado = mysqli_prepare($con, $sql3);
            mysqli_stmt_bind_param($resultado, "i", $idReto);
            mysqli_stmt_execute($resultado);

            $sql4 = "DELETE FROM modulo_reto WHERE idReto = ?";
            $resultado = mysqli_prepare($con, $sql4);
            mysqli_stmt_bind_param($resultado, "i", $idReto);
            mysqli_stmt_execute($resultado);

            $sql5 = "DELETE FROM retos WHERE idReto = ?";
            $resultado = mysqli_prepare($con, $sql5);
            mysqli_stmt_bind_param($resultado, "i", $idReto);
            mysqli_stmt_execute($resultado);
        }

        $sql6 = "DELETE FROM calificaciones_modulos WHERE idModulo = ?";
        $resultado = mysqli_prepare($con, $sql6);
        mysqli_stmt_bind_param($resultado, "i", $idModulo);
        mysqli_stmt_execute($resultado);

        $sql7 = "DELETE FROM modulo_profesor WHERE idModulo = ?";
        $resultado = mysqli_prepare($con, $sql7);
        mysqli_stmt_bind_param($resultado, "i", $idModulo);
        mysqli_stmt_execute($resultado);

        $sql8 = "DELETE FROM modulos WHERE idModulo = ?";
        $resultado = mysqli_prepare($con, $sql8);
        mysqli_stmt_bind_param($resultado, "i", $idModulo);
        mysqli_stmt_execute($resultado);
    }

    // ── Los estudiantes no se eliminan, solo se desvinculan del ciclo ──
    $sql9 = "UPDATE estudiantes SET idCiclo = NULL WHERE idCiclo = ?";
    $resultado = mysqli_prepare($con, $sql9);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);

    $sql10 = "DELETE FROM ciclo_profesor WHERE idCiclo = ?";
    $resultado = mysqli_prepare($con, $sql10);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);

    $sql11 = "DELETE FROM ciclos WHERE idCiclo = ?";
    $resultado = mysqli_prepare($con, $sql11);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    if (mysqli_stmt_execute($resultado)) {
        registrarAccion('borrar', 'ciclos', $idCiclo);
        $_SESSION['exito'] = "El ciclo formativo ha sido eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el ciclo formativo.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($isAjax) {
    header('Content-Type: application/json');
    $ok = empty($_SESSION['errores']);
    $msg = $ok ? ($_SESSION['exito'] ?? 'Ciclo eliminado correctamente') : (is_array($_SESSION['errores']) ? implode(', ', $_SESSION['errores']) : $_SESSION['errores']);
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
