<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['idEvento'])) {
    $idEvento = trim($_POST['idEvento']);
    
    if (eliminarEvento($idEvento)) {
        $_SESSION['exito'] = "Evento eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
?>
