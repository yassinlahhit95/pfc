<?php
session_start();
require_once "../../../modelos/calificaciones.php";
if (isset($_POST['guardarNotas'])) {
    $idModulo = $_POST['idModulo'];
    $n1ev = $_POST['n1ev'];
    $n1f = $_POST['n1f'];
    $n2ev = $_POST['n2ev'];
    $n2f = $_POST['n2f'];
    foreach ($n1ev as $idAlumn => $valor) {
        calificarModuloCompleto($idAlumn, $idModulo, $n1ev[$idAlumn], $n1f[$idAlumn], $n2ev[$idAlumn], $n2f[$idAlumn]);
    }
    $_SESSION['exito'] = "Ok";
    header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php?idModulo=$idModulo");
    exit;
}
header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php");
exit;

