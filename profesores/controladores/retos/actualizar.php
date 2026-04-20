<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $id = $_POST['idReto'];
    $nombre = trim($_POST['nombreReto']);
    $fInicio = $_POST['fechaInicio'];
    $fFin = $_POST['fechaFin'];
    $horas = $_POST['horasReto'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($id)) {
        header("Location: ../../vistas/retos/lista.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del reto es obligatorio.";
        header("Location: ../../vistas/retos/lista.php");
    } else if (empty($fInicio)) {
        $_SESSION['error'] = "La fecha de inicio es obligatoria.";
        header("Location: ../../vistas/retos/lista.php");
    } else if (!preg_match($regexFecha, $fInicio)) {
        $_SESSION['error'] = "La fecha de inicio debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/retos/lista.php");
    } else if (empty($fFin)) {
        $_SESSION['error'] = "La fecha de fin es obligatoria.";
        header("Location: ../../vistas/retos/lista.php");
    } else if (!preg_match($regexFecha, $fFin)) {
        $_SESSION['error'] = "La fecha de fin debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/retos/lista.php");
    } else if (!empty($horas) && !is_numeric($horas)) {
        $_SESSION['error'] = "Las horas deben ser un valor numérico.";
        header("Location: ../../vistas/retos/lista.php");
    } else {
        if (actualizarReto($id, $nombre, $fInicio, $fFin, $horas)) {
            $_SESSION['exito'] = "Reto actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar el reto.";
        }
    }
    exit;
}

header("Location: ../../vistas/retos/lista.php");
exit;
?>