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

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
        header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
    } else if (empty($asunto)) {
        $_SESSION['error'] = "El asunto es obligatorio.";
        header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
    } else if (empty($fecha)) {
        $_SESSION['error'] = "La fecha es obligatoria.";
        header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
    } else if (!preg_match($regexFecha, $fecha)) {
        $_SESSION['error'] = "La fecha no es válida.";
        header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
    } else {
        if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha)) {
            $_SESSION['exito'] = "Reclamación enviada correctamente.";
            header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
        } else {
            $_SESSION['error'] = "Error al enviar la reclamación.";
            header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
        }
    }
    exit;
}

header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
exit;
?>