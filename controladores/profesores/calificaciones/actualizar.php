<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['actualizarNota'])) {
    $idEstudianteRecibido = $_POST['idEstudiante'];
    $idModuloRecibido = $_POST['idModulo'];
    $notaEv1Recibida = $_POST['nota_1ev'];
    $notaFinal1Recibida = $_POST['nota_1final'];
    $notaEv2Recibida = $_POST['nota_2ev'];
    $notaFinal2Recibida = $_POST['nota_2final'];

    $errorEncontrado = false;

    if (!is_numeric($notaEv1Recibida) || !is_numeric($notaFinal1Recibida) || !is_numeric($notaEv2Recibida) || !is_numeric($notaFinal2Recibida)) {
        $_SESSION['error'] = strtoupper("LAS NOTAS DEBEN SER VALORES NUMÉRICOS.");
        $errorEncontrado = true;
    } 
    
    if ($errorEncontrado == false) {
        $resultado = actualizarOCrearNotaCompleta($idEstudianteRecibido, $idModuloRecibido, $notaEv1Recibida, $notaFinal1Recibida, $notaEv2Recibida, $notaFinal2Recibida, "");
        
        if ($resultado == true) {
            if (isset($_POST['notificarEstudiante']) && !empty($_POST['notificarEstudiante'])) {
                require_once "../../comunes/notificaciones_grades.php";
                enviarEmailNotasEstudiante($idEstudianteRecibido);
            }
            $_SESSION['exito'] = strtoupper("CALIFICACIÓN ACTUALIZADA CORRECTAMENTE.");
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL GUARDAR EN LA BASE DE DATOS.");
        }
    }
}

header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
exit;
?>

