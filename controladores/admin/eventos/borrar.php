<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['idEvento'])) {
    $idEvento = (int)($_POST['idEvento'] ?? 0);
    
    if (eliminarEvento($idEvento)) {
        $_SESSION['exito'] = "Evento eliminado.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el evento.";
    }
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
?>
