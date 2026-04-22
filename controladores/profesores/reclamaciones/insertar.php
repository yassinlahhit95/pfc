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
        $_SESSION['error'] = "El asunto es obligatorio.";
    } else if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual)) {
        $_SESSION['exito'] = "Reclamación enviada.";
        header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al enviar.";
    }
    header("Location: /pfc/vistas/profesores/reclamaciones/agregar.php");
    exit;
}
header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
exit;
?>