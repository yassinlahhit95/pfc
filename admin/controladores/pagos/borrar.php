<?php
session_start();
require_once "../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $idDelPago = $_POST['idPago'];
    
    if (empty($idDelPago) || !ctype_digit($idDelPago)) {
        $_SESSION['error'] = "ID de pago no válido.";
        header("Location: ../../vistas/pagos/verPagosGeneral.php");
        exit;
    }

    if (borrarPago($idDelPago)) {
        $_SESSION['mensaje'] = "Pago eliminado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido eliminar el pago.";
    }
}

header("Location: ../../vistas/pagos/verPagosGeneral.php");
exit;
?>
