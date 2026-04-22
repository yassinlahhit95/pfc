<?php
session_start();
require_once "../../../modelos/modulos.php";
if (isset($_POST['guardarModulo'])) {
    $id = $_POST['idModulo'];
    $nombre = $_POST['nombreModulo'];
    $idCiclo = $_POST['idCiclo'];
    $horas = $_POST['horasMaximas'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($idCiclo)) {
        $_SESSION['error'] = "Ciclo vacio";
    } else if (!is_numeric($horas)) {
        $_SESSION['error'] = "Horas debe ser numero";
    } else if (actualizarModulo($id, $nombre, $idCiclo, $horas)) {
        $_SESSION['exito'] = "OK";
        header("Location: /pfc/vistas/admin/modulos/verModulos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/modulos/modificarModulos.php?idModulo=$id");
    exit;
}
header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;

