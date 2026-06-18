<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['idModulo'])) {
    $idModulo = (int) $_POST['idModulo'];
    $con = obtenerConexion();

    // buscamos los retos que tiene este modulo
    $sql1 = "SELECT idReto FROM modulo_reto WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $idRetos = [];
    while ($r = mysqli_fetch_assoc($res)) { $idRetos[] = (int) $r['idReto']; }

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
        $_SESSION['exito'] = "Módulo eliminado.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el módulo.";
    }

    
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>
