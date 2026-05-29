<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['actualizarEjercicio'])) {
    $idEjercicio = intval($_POST['idEjercicio'] ?? 0);
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCarpeta   = intval($_POST['idCarpeta'] ?? 0);
    $fechaLimite = trim($_POST['fechaLimite'] ?? '');
    $publicado   = isset($_POST['publicado']) ? 1 : 0;

    $ejercicio = obtenerEjercicioPorId($idEjercicio);
    if (!$ejercicio || $ejercicio['idProfesor'] != $_SESSION['idProfesor']) {
        header("Location: ../../../vistas/profesores/ejercicios/panel.php");
        exit;
    }

    if (empty($titulo)) {
        $_SESSION['errores'] = "El título es obligatorio.";
        header("Location: ../../../vistas/profesores/ejercicios/editar.php?id=$idEjercicio");
        exit;
    }

    if (actualizarEjercicio($idEjercicio, $titulo, $descripcion, $idCarpeta ?: null, $fechaLimite ?: null, $publicado)) {
        $_SESSION['exito'] = "Ejercicio actualizado.";
    } else {
        $_SESSION['errores'] = "No se pudo actualizar.";
    }
    header("Location: ../../../vistas/profesores/ejercicios/panel.php");
    exit;
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
