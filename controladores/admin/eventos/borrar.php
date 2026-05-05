<?php
session_start();
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['idEvento'])) {
    $idEvento = trim($_POST['idEvento']);
    
    if (eliminarEvento($idEvento)) {
        $_SESSION['exito'] = "Evento eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;


