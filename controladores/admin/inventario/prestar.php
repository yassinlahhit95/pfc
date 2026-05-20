<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $fechaPrestamo = trim($_POST['fechaPrestamo']);

    $errores = '';

    if (empty($idArticulo)) {
        $errores = "Debe seleccionar un equipo.";
    }
    if (empty($idEstudiante)) {
        $errores = "Debe seleccionar un estudiante.";
    }
    if (empty($fechaPrestamo)) {
        $errores = "La fecha es obligatoria.";
    }

    if (!$errores) {
        if (registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo)) {
            $_SESSION['exito'] = "Préstamo registrado.";
            header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
            exit;
        }
        $_SESSION['errores'] = "Error al registrar.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_prestamo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/agregarPrestamo.php");
    exit;
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
?>
