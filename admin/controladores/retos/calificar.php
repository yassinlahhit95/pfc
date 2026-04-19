<?php
session_start();
require_once "../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $idReto = $_POST['idReto'];
    $calificaciones = isset($_POST['notas']) ? $_POST['notas'] : [];

    if (!ctype_digit($idReto)) {
        $_SESSION['error'] = "ID de reto no válido.";
        header("Location: ../../vistas/retos/verRetos.php");
        exit;
    }

    $errorEncontrado = false;
    foreach ($calificaciones as $idEstudiante => $nota) {
        // Validación simple: Si la nota supera 10, la bajamos a 10
        if ($nota > 10) {
            $nota = 10;
        }

        if (!calificarReto($idEstudiante, $idReto, $nota)) {
            $errorEncontrado = true;
        }
    }

    if ($errorEncontrado) {
        $_SESSION['error'] = "Hubo errores al guardar algunas calificaciones.";
    } else {
        $_SESSION['exito'] = "Calificaciones guardadas con éxito.";
    }

    header("Location: ../../vistas/retos/calificarReto.php?id=$idReto");
    exit;
}

header("Location: ../../vistas/retos/verRetos.php");
exit;
?>
