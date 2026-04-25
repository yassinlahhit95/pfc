<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['insertarNota'])) {
    $idEst = $_POST['idEstudiante'];
    $idMod = $_POST['idModulo'];
    $n1e = $_POST['nota_1ev'];
    $n1f = $_POST['nota_1final'];
    $n2e = $_POST['nota_2ev'];
    $n2f = $_POST['nota_2final'];

    if (empty($idEst)) {
        $_SESSION['error'] = "Estudiante vacio";
    } else if (empty($idMod)) {
        $_SESSION['error'] = "Modulo vacio";
    } else if (!is_numeric($n1e) || !is_numeric($n1f) || !is_numeric($n2e) || !is_numeric($n2f)) {
        $_SESSION['error'] = "Notas deben ser numero";
    } else if (calificarModuloCompleto($idEst, $idMod, $n1e, $n1f, $n2e, $n2f)) {
        if (isset($_POST['notificarEstudiante'])) {
            require_once "../../comunes/notificaciones_grades.php";
            enviarEmailNotasEstudiante($idEst);
        }
        $_SESSION['exito'] = "Calificacion guardada";
        header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
}
header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
exit;
?>
