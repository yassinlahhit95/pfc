<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['calificar'])) {
    $idEjercicio  = intval($_POST['idEjercicio'] ?? 0);
    $idEstudiante = intval($_POST['idEstudiante'] ?? 0);
    $nota         = floatval(str_replace(',', '.', $_POST['nota'] ?? ''));
    $comentario   = trim($_POST['comentario'] ?? '');

    $ej = obtenerEjercicioPorId($idEjercicio);
    if (!$ej || $ej['idProfesor'] != $_SESSION['idProfesor']) {
        header("Location: ../../../vistas/profesores/ejercicios/panel.php");
        exit;
    }

    if ($nota < 0 || $nota > 10) {
        $_SESSION['errores'] = "La nota debe estar entre 0 y 10.";
    } elseif (calificarEntrega($idEjercicio, $idEstudiante, $nota, $comentario)) {
        $_SESSION['exito'] = "Calificación guardada.";
    } else {
        $_SESSION['errores'] = "Error al guardar la calificación.";
    }
    header("Location: ../../../vistas/profesores/ejercicios/entregas.php?id=$idEjercicio");
    exit;
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
