<?php
session_start();
require_once "../../modelos/ciclos.php";

if (isset($_POST['idCiclo'])) {
    $idDelCiclo = $_POST['idCiclo'];
    
    if (empty($idDelCiclo) || !ctype_digit($idDelCiclo)) {
        $_SESSION['error'] = "ID de ciclo no válido.";
        header("Location: ../../vistas/ciclos/verCiclos.php");
        exit;
    }

    if (eliminarCicloPorId($idDelCiclo)) {
        $_SESSION['mensaje'] = "Ciclo eliminado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido eliminar el ciclo.";
    }
}

header("Location: ../../vistas/ciclos/verCiclos.php");
exit;
?>
