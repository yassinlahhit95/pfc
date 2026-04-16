<?php
session_start();
require_once "../../modelos/inventario.php";

if (isset($_POST['registrarPrestamo'])) {
    $idArticulo = $_POST['idArticulo'] ?? '';
    $idEstudiante = $_POST['idEstudiante'] ?? '';
    $fecha = $_POST['fechaPrestamo'] ?? date('Y-m-d');

    if (empty($idArticulo) || empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un dispositivo y un estudiante";
    } elseif (!is_numeric($idArticulo) || !ctype_digit($idArticulo) || !preg_match('/^[0-9]+$/', $idArticulo)) {
        $_SESSION['error'] = "ID de artículo no válido";
    } elseif (!is_numeric($idEstudiante) || !ctype_digit($idEstudiante) || !preg_match('/^[0-9]+$/', $idEstudiante)) {
        $_SESSION['error'] = "ID de estudiante no válido";
    } else {
        $modelo = new inventario();
        if ($modelo->realizarPrestamoModelo($idArticulo, $idEstudiante, $fecha)) {
            $_SESSION['exito'] = "Préstamo registrado";
        } else {
            $_SESSION['error'] = "Error al registrar el préstamo";
        }
    }
}

header("Location: ../../vistas/inventario/gestionarPrestamos.php");
exit;
?>
