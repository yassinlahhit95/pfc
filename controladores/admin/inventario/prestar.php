<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $fechaPrestamo = trim($_POST['fechaPrestamo']);

    $errores = [];

    if (empty($idArticulo)) {
        $errores['idArticulo'] = "Debe seleccionar un equipo.";
    }
    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($fechaPrestamo)) {
        $errores['fechaPrestamo'] = "La fecha es obligatoria.";
    }

    if (empty($errores)) {
        if (registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo)) {
            $_SESSION['exito'] = "Préstamo registrado.";
            header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
            exit;
        }
        $_SESSION['error'] = "Error al registrar.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_prestamo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/agregarPrestamo.php");
    exit;
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;


