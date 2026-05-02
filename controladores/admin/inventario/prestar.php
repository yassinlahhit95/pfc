<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = trim($_POST['idArticulo'] ?? '');
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $fechaPrestamo = trim($_POST['fechaPrestamo'] ?? '');

    $hayError = false;
    $errores = [];

    if (empty($idArticulo)) {
        $errores['idArticulo'] = "Debe seleccionar un equipo.";
        $hayError = true;
    }
    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Debe seleccionar un estudiante.";
        $hayError = true;
    }
    if (empty($fechaPrestamo)) {
        $errores['fechaPrestamo'] = "La fecha es obligatoria.";
        $hayError = true;
    }

    if (!$hayError) {
        if (registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo)) {
            $_SESSION['exito'] = "Listo! PrÃ©stamo registrado correctamente.";
            header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, ha ocurrido un error al registrar el prÃ©stamo.";
        }
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_prestamo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/agregarPrestamo.php");
    exit;
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
