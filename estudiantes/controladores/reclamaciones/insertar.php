<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['insertarReclamacion'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $gravedad = $_POST['gravedad'];
    $fecha = $_POST['fecha'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($idProfesor)) {
        $_SESSION['error'] = "Debe seleccionar un profesor.";
        header("Location: ../../vistas/reclamaciones/agregar.php");
    } else if (empty($asunto)) {
        $_SESSION['error'] = "El asunto es obligatorio.";
        header("Location: ../../vistas/reclamaciones/agregar.php");
    } else if (empty($fecha)) {
        $_SESSION['error'] = "La fecha es obligatoria.";
        header("Location: ../../vistas/reclamaciones/agregar.php");
    } else if (!preg_match($regexFecha, $fecha)) {
        $_SESSION['error'] = "La fecha debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/reclamaciones/agregar.php");
    } else {
        if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha)) {
            $_SESSION['exito'] = "Reclamación enviada correctamente.";
            header("Location: ../../vistas/reclamaciones/lista.php");
        } else {
            $_SESSION['error'] = "Error al enviar la reclamación.";
            header("Location: ../../vistas/reclamaciones/agregar.php");
        }
    }
    exit;
}

header("Location: ../../vistas/reclamaciones/lista.php");
exit;
?>