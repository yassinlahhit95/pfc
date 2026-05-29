<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['guardarCarpeta'])) {
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $color       = trim($_POST['color'] ?? '#0ea5e9');
    $icono       = trim($_POST['icono'] ?? 'fa-folder');
    $idCiclo     = intval($_POST['idCiclo'] ?? 0);
    $idProfesor  = $_SESSION['idProfesor'];

    if (empty($nombre) || $idCiclo < 1) {
        $_SESSION['errores'] = "El nombre y el ciclo son obligatorios.";
    } else {
        if (insertarCarpeta($nombre, $descripcion, $color, $icono, $idProfesor, $idCiclo)) {
            $_SESSION['exito'] = "Carpeta creada correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo crear la carpeta.";
        }
    }
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
