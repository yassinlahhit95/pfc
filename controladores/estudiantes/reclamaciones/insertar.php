<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['insertarReclamacion'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($asunto)) {
        $_SESSION['error'] = "Asunto vacio";
    } else if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual)) {
        $_SESSION['exito'] = "Enviada";
        header("Location: /pfc/vistas/estudiantes/reclamaciones/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/estudiantes/reclamaciones/agregar.php");
    exit;
}
header("Location: /pfc/vistas/estudiantes/reclamaciones/lista.php");
exit;
?>