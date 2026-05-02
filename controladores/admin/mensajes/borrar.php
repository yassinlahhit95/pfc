<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReclamacion'])) {
    $idReclamacionParaBorrar = trim($_POST['idReclamacion']);
    
    if (eliminarMensaje($idReclamacionParaBorrar)) {
        $_SESSION['exito'] = "Listo! ReclamaciÃ³n eliminada sin problemas.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, no se pudo eliminar la reclamaciÃ³n.";
    }
}

header("Location: ../../../vistas/admin/reclamaciones/verReclamaciones.php");
exit;
