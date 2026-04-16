<?php
session_start();
require_once "../../modelos/reclamaciones.php";

if (isset($_POST['guardarReclamacion'])) {
    unset($_SESSION['errores'], $_SESSION['datos_reclamaciones']);

    $errores = [];
    
    $idEstudiante = $_POST['idEstudiante'] ?? '';
    $idProfesor = $_POST['idProfesor'] ?? '';
    $asunto = trim($_POST['asunto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $gravedad = $_POST['gravedad'] ?? '';
    $fecha = $_POST['fecha'] ?? '';

    if (empty($idEstudiante)) { 
        $errores['idEstudiante'] = "Debe seleccionar un estudiante."; 
    } elseif (!is_numeric($idEstudiante) || !ctype_digit($idEstudiante) || !preg_match('/^[0-9]+$/', $idEstudiante)) {
        $errores['idEstudiante'] = "El ID del estudiante debe ser numérico.";
    }

    if (empty($idProfesor)) { 
        $errores['idProfesor'] = "Debe seleccionar un profesor."; 
    } elseif (!is_numeric($idProfesor) || !ctype_digit($idProfesor) || !preg_match('/^[0-9]+$/', $idProfesor)) {
        $errores['idProfesor'] = "El ID del profesor debe ser numérico.";
    }
    if (empty($asunto)) { $errores['asunto'] = "El asunto es obligatorio."; }
    if (empty($descripcion)) { $errores['descripcion'] = "La descripción es obligatoria."; }
    if (empty($gravedad)) { $errores['gravedad'] = "La gravedad es obligatoria."; }
    if (empty($fecha)) { $errores['fecha'] = "La fecha es obligatoria."; }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reclamaciones'] = $_POST;
        header("Location: ../../vistas/reclamaciones/agregarReclamacion.php");
        exit;
    }

    $modeloReclamacion = new reclamacion();
    if ($modeloReclamacion->insertarReclamacionModelo($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha)) {
        $_SESSION['exito'] = "Reclamación registrada correctamente.";
    } else {
        $_SESSION['error'] = "Error al registrar la reclamación.";
    }
    
    header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
    exit;
}
?>
