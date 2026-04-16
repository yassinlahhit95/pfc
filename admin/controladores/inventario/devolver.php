<?php
session_start();
require_once "../../modelos/inventario.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (!is_numeric($id) || !ctype_digit((string)$id) || !preg_match('/^[0-9]+$/', (string)$id)) {
        $_SESSION['error'] = "ID de préstamo no válido";
    } else {
        $modelo = new inventario();
        if ($modelo->devolverPrestamoModelo($id)) {
            $_SESSION['exito'] = "Dispositivo devuelto correctamente";
        } else {
            $_SESSION['error'] = "Error al procesar la devolución";
        }
    }
}

header("Location: ../../vistas/inventario/gestionarPrestamos.php");
exit;
?>
