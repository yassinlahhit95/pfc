<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $id = $_POST['idReto'];
    $n = trim($_POST['nombreReto']);
    $fi = $_POST['fechaInicio'];
    $ff = $_POST['fechaFin'];
    $h = $_POST['horasReto'];

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/retos/verRetos.php");
        exit;
    } else if (empty($n)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (!is_numeric($h)) {
        $_SESSION['error'] = "Horas debe ser numero";
    } else if (actualizarReto($id, $n, $fi, $ff, $h)) {
        $_SESSION['exito'] = "Actualizado";
        header("Location: /pfc/vistas/admin/retos/verRetos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/retos/modificarRetos.php?idReto=$id");
    exit;
}
header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;
?>
