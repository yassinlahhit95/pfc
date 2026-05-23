<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";

if (isset($_POST['idCiclo'])) {
    $idCiclo = (int) $_POST['idCiclo'];
    $con = obtenerConexion();

    // sacamos todos los modulos de este ciclo
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

    // los estudiantes no se borran, solo quitamos el ciclo
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
        $_SESSION['exito'] = "Ciclo eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar.";
    }

    mysqli_close($con);
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
