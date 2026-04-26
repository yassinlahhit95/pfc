<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['idPago'])) {
    $id = $_POST['idPago'];
    if (eliminarPago($id)) {
        $_SESSION['exito'] = "Pago eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el pago.";
    }
}
header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;
?>

