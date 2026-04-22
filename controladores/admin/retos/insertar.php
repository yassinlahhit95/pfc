<?php
session_start();
require_once "../../../modelos/retos.php";
if (isset($_POST['insertarReto'])) {
    $nombre = $_POST['nombreReto'];
    $fInicio = $_POST['fechaInicio'];
    $fFin = $_POST['fechaFin'];
    $horas = $_POST['horasReto'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (!is_numeric($horas)) {
        $_SESSION['error'] = "Horas debe ser numero";
    } else if ($idReto = insertarReto($nombre, $fInicio, $fFin, $horas)) {
        $_SESSION['exito'] = "OK";
        header("Location: /pfc/vistas/admin/retos/verRetos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/retos/agregarRetos.php");
    exit;
}
header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;

