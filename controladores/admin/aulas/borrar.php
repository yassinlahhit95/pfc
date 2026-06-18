<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['idAula'])) {
    $idAula = (int)$_POST['idAula'];
    if (eliminarAula($idAula)) {
        $_SESSION['exito'] = "Aula eliminada.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el aula.";
    }
}

header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
