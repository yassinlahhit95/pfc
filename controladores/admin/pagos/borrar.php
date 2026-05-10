<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $idPago = trim($_POST['idPago']);
    
    $resultado = eliminarPago($idPago);
    
    if ($resultado) {
        $_SESSION['exito'] = "Pago eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
