<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = $_POST['idArticulo'];
    $idEstudiante = $_POST['idEstudiante'];
    $fecha = $_POST['fechaPrestamo'];

    $errores = [];
    if (empty($idArticulo)) {
        $errores['idArticulo'] = "Seleccione un recurso.";
    }
    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Seleccione un estudiante.";
    }
    if (empty($fecha)) {
        $errores['fechaPrestamo'] = "La fecha es obligatoria.";
    }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
        header("Location: ../../vistas/inventario/agregarPrestamo.php");
        exit;
    }

    if (registrarPrestamo($idEstudiante, $idArticulo, $fecha)) {
        $_SESSION['exito'] = "Préstamo registrado";
    } else {
        $_SESSION['error'] = "Error al registrar el préstamo";
    }
}

header("Location: ../../vistas/inventario/gestionarPrestamos.php");
exit;
?>