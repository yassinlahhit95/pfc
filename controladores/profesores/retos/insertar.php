<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['insertarReto'])) {
    $n = trim($_POST['nombreReto']);
    $fi = $_POST['fechaInicio'];
    $ff = $_POST['fechaFin'];
    $h = $_POST['horasReto'];

    if (empty($n)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (insertarReto($n, $fi, $ff, $h)) {
        $_SESSION['exito'] = "Reto insertado.";
        header("Location: /pfc/vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al insertar.";
    }
    header("Location: /pfc/vistas/profesores/retos/agregar.php");
    exit;
}
header("Location: /pfc/vistas/profesores/retos/lista.php");
exit;
?>

