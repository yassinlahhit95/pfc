<?php
session_start();
require_once "../../modelos/ciclos.php";

if (isset($_POST['idCiclo'])) {
    $id = $_POST['idCiclo'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new ciclo();
        if ($modelo->eliminarCicloModelo($id)) {
            $_SESSION['exito'] = "Ciclo borrado correctamente";
        } else {
            $_SESSION['error'] = "Error al borrar el ciclo";
        }
    } else {
        $_SESSION['error'] = "ID de ciclo no válido";
    }
}

header("Location: ../../vistas/ciclos/verCiclos.php");
exit;
?>
