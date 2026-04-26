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
        header("Location: /pfc/vistas/profesores/retos/lista.php");
        exit;
    } else if (actualizarReto($id, $n, $fi, $ff, $h)) {
        $_SESSION['exito'] = "Reto actualizado.";
    } else {
        $_SESSION['error'] = "Error al actualizar.";
    }
}
header("Location: /pfc/vistas/profesores/retos/lista.php");
exit;
?>

