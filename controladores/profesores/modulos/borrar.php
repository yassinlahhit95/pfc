<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/modulos.php';

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
       && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'Sin permiso';

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

if ($esTutor && $idCicloTutor && isset($_POST['idModulo'])) {
    if (!Security::validateCSRFToken()) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
    }
    $idModulo = (int)$_POST['idModulo'];
    if ($idModulo && moduloPerteneceACiclo($idModulo, $idCicloTutor)) {
        $con = obtenerConexion();

        // Cascade: retos → calificaciones_retos → modulo_reto, luego calificaciones_modulos, modulo_profesor
        $res = mysqli_prepare($con, "SELECT idReto FROM modulo_reto WHERE idModulo = ?");
        mysqli_stmt_bind_param($res, "i", $idModulo);
        mysqli_stmt_execute($res);
        $retosRes = mysqli_stmt_get_result($res);
        while ($r = mysqli_fetch_assoc($retosRes)) {
            $idR = (int)$r['idReto'];
            foreach ([
                "DELETE FROM calificaciones_retos WHERE idReto = ?",
                "DELETE FROM modulo_reto WHERE idReto = ?",
                "DELETE FROM retos WHERE idReto = ?",
            ] as $sql) {
                $st = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($st, "i", $idR);
                mysqli_stmt_execute($st);
            }
        }

        foreach ([
            "DELETE FROM calificaciones_modulos WHERE idModulo = ?",
            "DELETE FROM modulo_profesor WHERE idModulo = ?",
            "DELETE FROM modulos WHERE idModulo = ?",
        ] as $sql) {
            $st = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($st, "i", $idModulo);
            mysqli_stmt_execute($st);
        }

        $ok  = true;
        $msg = "Módulo eliminado correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "No tienes permiso sobre este módulo.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/profesores/modulos/lista.php");
exit;
