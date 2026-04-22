<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['guardarReclamacion'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
    } else if (empty($asunto)) {
        $_SESSION['error'] = "El asunto es obligatorio.";
    } else if (insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual)) {
        $_SESSION['exito'] = "Reclamación guardada correctamente.";
        header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar en la base de datos.";
    }
    header("Location: /pfc/vistas/admin/reclamaciones/detallesReclamacion.php");
    exit;
}

header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
exit;
?>