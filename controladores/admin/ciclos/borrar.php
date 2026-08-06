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

    // Se requiere contraseña para esta operación sensible en cascada
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

    // Cascada completa en una única transacción: si cualquier paso falla, se revierte
    // todo (antes cada DELETE se ejecutaba de forma independiente sin comprobar su
    // resultado, por lo que un fallo a mitad de la cascada podía dejar el ciclo
    // parcialmente borrado —p.ej. retos eliminados pero módulos no— sin posibilidad
    // de deshacerlo ni de que el admin se enterase).
    mysqli_begin_transaction($con);
    $cascadaOk = true;

    $ejecutar = function(string $sql, string $tipos, ...$parametros) use ($con, &$cascadaOk) {
        if (!$cascadaOk) return;
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
        if (!mysqli_stmt_execute($stmt)) $cascadaOk = false;
    };

    // ── Retos y calificaciones de cada módulo ──
    $stmtModulos = mysqli_prepare($con, "SELECT idModulo FROM modulos WHERE idCiclo = ?");
    mysqli_stmt_bind_param($stmtModulos, "i", $idCiclo);
    mysqli_stmt_execute($stmtModulos);
    $resModulos = mysqli_stmt_get_result($stmtModulos);
    $idModulos = [];
    while ($filaModulo = mysqli_fetch_assoc($resModulos)) { $idModulos[] = (int) $filaModulo['idModulo']; }

    foreach ($idModulos as $idModulo) {
        $stmtRetos = mysqli_prepare($con, "SELECT idReto FROM modulo_reto WHERE idModulo = ?");
        mysqli_stmt_bind_param($stmtRetos, "i", $idModulo);
        mysqli_stmt_execute($stmtRetos);
        $resRetos = mysqli_stmt_get_result($stmtRetos);
        $idRetos = [];
        while ($filaReto = mysqli_fetch_assoc($resRetos)) { $idRetos[] = (int) $filaReto['idReto']; }

        foreach ($idRetos as $idReto) {
            $ejecutar("DELETE FROM calificaciones_retos WHERE idReto = ?", "i", $idReto);
            $ejecutar("DELETE FROM modulo_reto WHERE idReto = ?", "i", $idReto);
            $ejecutar("DELETE FROM retos WHERE idReto = ?", "i", $idReto);
        }

        $ejecutar("DELETE FROM calificaciones_modulos WHERE idModulo = ?", "i", $idModulo);
        $ejecutar("DELETE FROM modulo_profesor WHERE idModulo = ?", "i", $idModulo);
        $ejecutar("DELETE FROM modulos WHERE idModulo = ?", "i", $idModulo);
    }

    // ── Los estudiantes no se eliminan, solo se desvinculan del ciclo ──
    $ejecutar("UPDATE estudiantes SET idCiclo = NULL WHERE idCiclo = ?", "i", $idCiclo);
    $ejecutar("DELETE FROM ciclo_profesor WHERE idCiclo = ?", "i", $idCiclo);
    $ejecutar("DELETE FROM ciclos WHERE idCiclo = ?", "i", $idCiclo);

    if ($cascadaOk) {
        mysqli_commit($con);
        registrarAccion('borrar', 'ciclos', $idCiclo);
        $_SESSION['exito'] = "El ciclo formativo ha sido eliminado correctamente.";
    } else {
        mysqli_rollback($con);
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
