<?php
session_start();
require_once "../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $id = $_POST['idPago'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new pago();
        if ($modelo->eliminarPagoModelo($id)) {
            $_SESSION['exito'] = "Pago eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el pago";
        }
    } else {
        $_SESSION['error'] = "ID del pago no válido";
    }
}

header("Location: ../../vistas/pagos/verPagosGeneral.php");
exit;
?>
