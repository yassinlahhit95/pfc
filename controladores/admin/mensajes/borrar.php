<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (empty($_SESSION['idAdmin'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php"); exit;
}

if (isset($_POST['idReclamacion'])) {
    $idReclamacionParaBorrar = trim($_POST['idReclamacion']);
    
    if (eliminarMensaje($idReclamacionParaBorrar)) {
        $_SESSION['exito'] = "Reclamación eliminada.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
?>
