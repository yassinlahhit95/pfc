<?php
session_start();
require_once "../../../modelos/inventario.php";
if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = $_POST['idArticulo'];
    $idEstudiante = $_POST['idEstudiante'];
    $fecha = $_POST['fechaPrestamo'];
    if (empty($idArticulo)) {
        $_SESSION['error'] = "Recurso obligatorio";
    } else if (empty($idEstudiante)) {
        $_SESSION['error'] = "Estudiante obligatorio";
    } else if (empty($fecha)) {
        $_SESSION['error'] = "Fecha obligatoria";
    } else if (registrarPrestamo($idEstudiante, $idArticulo, $fecha)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/inventario/agregarPrestamo.php");
    exit;
}
header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
exit;

