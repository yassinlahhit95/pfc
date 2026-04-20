<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['guardarReclamacion'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $gravedad = $_POST['gravedad'];
    $fecha = $_POST['fecha'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
    } else if (empty($idProfesor)) {
        $_SESSION['error'] = "Debe seleccionar un profesor.";
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
    } else if (empty($asunto)) {
        $_SESSION['error'] = "El asunto es obligatorio.";
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
    } else if (empty($fecha)) {
        $_SESSION['error'] = "La fecha es obligatoria.";
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
    } else if (!preg_match($regexFecha, $fecha)) {
        $_SESSION['error'] = "La fecha debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
    } else {
        if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha)) {
            $_SESSION['exito'] = "Reclamación guardada correctamente.";
            header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
            header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
        }
    }
    exit;
}

header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
exit;
?>