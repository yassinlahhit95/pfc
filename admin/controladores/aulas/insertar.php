<?php
session_start();
require_once "../../../modelos/aulas.php";

if (isset($_POST['guardarAula'])) {
    $nombre = trim($_POST['nombreAula']);

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del aula es obligatorio.";
    } else {
        if (insertarAula($nombre)) {
            $_SESSION['exito'] = "Aula registrada correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar el aula.";
        }
    }

    header("Location: ../../vistas/aulas/verAulas.php");
    exit;
}

header("Location: ../../vistas/aulas/verAulas.php");
exit;
?>