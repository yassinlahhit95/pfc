<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $idPago = $_POST['idPago'];
    
    $resultado = eliminarPago($idPago);
    
    if ($resultado) {
        $_SESSION['exito'] = "Listo! El pago ha sido eliminado.";
    } else {
        $_SESSION['error'] = "Vaya, no se ha podido eliminar el pago.";
    }
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>