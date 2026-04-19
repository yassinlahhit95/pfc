<?php
session_start();
require_once "../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    unset($_SESSION['errores'], $_SESSION['datos_aulas']);

    $errores = [];
    $nombre = trim($_POST['nombreAula'] ?? '');

    if (empty($nombre)) {
        $errores['nombreAula'] = "El nombre del aula es obligatorio.";
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aulas'] = $_POST;
        header("Location: ../../vistas/aulas/verAulas.php");
        exit;
    }

    if (insertarAula($nombre)) {
        $_SESSION['exito'] = "Aula guardada correctamente.";
    } else {
        $_SESSION['error'] = "Error al guardar el aula.";
    }
    
    header("Location: ../../vistas/aulas/verAulas.php");
    exit;
}
?>
