<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['idAula'])) {
    $idAula = (int)$_POST['idAula'];
    if (eliminarAula($idAula)) {
        $_SESSION['exito'] = "El aula ha sido eliminada correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el aula seleccionada.";
    }
}

header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
