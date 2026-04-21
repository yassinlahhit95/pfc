<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['actualizarNota'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idModulo = $_POST['idModulo'];
    $n1ev = $_POST['nota_1ev'];
    $n1f = $_POST['nota_1final'];
    $n2ev = $_POST['nota_2ev'];
    $n2f = $_POST['nota_2final'];

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
    } else if (empty($idModulo)) {
        $_SESSION['error'] = "Debe seleccionar un módulo.";
    } else if ($n1ev !== "" && !is_numeric($n1ev)) {
        $_SESSION['error'] = "La nota de la 1ª evaluación debe ser numérica.";
    } else if ($n1f !== "" && !is_numeric($n1f)) {
        $_SESSION['error'] = "La nota de la 1ª final debe ser numérica.";
    } else if ($n2ev !== "" && !is_numeric($n2ev)) {
        $_SESSION['error'] = "La nota de la 2ª evaluación debe ser numérica.";
    } else if ($n2f !== "" && !is_numeric($n2f)) {
        $_SESSION['error'] = "La nota de la 2ª final debe ser numérica.";
    } else {
        if (calificarModuloCompleto($idEstudiante, $idModulo, $n1ev, $n1f, $n2ev, $n2f)) {
            $_SESSION['exito'] = "Calificaciones actualizadas correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar las calificaciones.";
        }
    }
}

header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
exit;
?>