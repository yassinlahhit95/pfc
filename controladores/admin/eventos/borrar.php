<?php
session_start();
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['idEvento'])) {
    $idEvento = trim($_POST['idEvento']);
    
    if (eliminarEvento($idEvento)) {
        $_SESSION['exito'] = "Listo! Evento eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Vaya, ha ocurrido un error al intentar eliminar el evento.";
    }
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
