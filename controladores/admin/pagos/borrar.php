<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $idPago = (int)($_POST['idPago'] ?? 0);
    
    $resultado = eliminarPago($idPago);
    
    if ($resultado) {
        $_SESSION['exito'] = "Pago eliminado.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el pago.";
    }
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
