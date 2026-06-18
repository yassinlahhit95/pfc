<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $idReto = (int)$_POST['idReto'];

    if ($idReto && !retoPerteneceAProfesor($idReto, $_SESSION['idProfesor'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este reto.";
        header("Location: ../../../vistas/profesores/retos/lista.php"); exit;
    }

    if ($idReto && eliminarReto($idReto)) {
        $_SESSION['exito'] = "Reto eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar el reto.";
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
