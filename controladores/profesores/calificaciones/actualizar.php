<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['actualizarNota'])) {
    $id = $_POST['idCalificacion']; // Capturamos el ID para la redirección
    $idEstudianteRecibido = $_POST['idEstudiante'];
    $idModuloRecibido = $_POST['idModulo'];
    $notaEv1Recibida = $_POST['nota_1ev'];
    $notaFinal1Recibida = $_POST['nota_1final'];
    $notaEv2Recibida = $_POST['nota_2ev'];
    $notaFinal2Recibida = $_POST['nota_2final'];

    $errorEncontrado = false;

    // Sustituimos comas por puntos para que PHP lo trate como float
    $notaEv1Recibida = str_replace(',', '.', $notaEv1Recibida);
    $notaFinal1Recibida = str_replace(',', '.', $notaFinal1Recibida);
    $notaEv2Recibida = str_replace(',', '.', $notaEv2Recibida);
    $notaFinal2Recibida = str_replace(',', '.', $notaFinal2Recibida);

    $notasCheck = array($notaEv1Recibida, $notaFinal1Recibida, $notaEv2Recibida, $notaFinal2Recibida);
    
    foreach ($notasCheck as $nota) {
        if ($nota !== "" && !is_numeric($nota)) {
            $_SESSION['error'] = strtoupper("LAS NOTAS DEBEN SER VALORES NUMÉRICOS.");
            $errorEncontrado = true;
            break;
        }
        if ($nota !== "" && ($nota < 0 || $nota > 10)) {
            $_SESSION['error'] = strtoupper("LAS CALIFICACIONES DEBEN ESTAR ENTRE 0.00 Y 10.00.");
            $errorEncontrado = true;
            break;
        }
    }
    
    if ($errorEncontrado == true) {
        header("Location: /pfc/vistas/profesores/calificaciones/editar.php?id=" . $id);
        exit;
    }

    if ($errorEncontrado == false) {
        $resultado = actualizarOCrearNotaCompleta($idEstudianteRecibido, $idModuloRecibido, $notaEv1Recibida, $notaFinal1Recibida, $notaEv2Recibida, $notaFinal2Recibida, "");
        
        if ($resultado == true) {
            if (isset($_POST['notificarEstudiante']) && !empty($_POST['notificarEstudiante'])) {
                require_once "../../comunes/notificaciones_grades.php";
                enviarEmailNotasEstudiante($idEstudianteRecibido);
            }
            $_SESSION['exito'] = strtoupper("CALIFICACIÓN ACTUALIZADA CORRECTAMENTE.");
            header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL GUARDAR EN LA BASE DE DATOS.");
            header("Location: /pfc/vistas/profesores/calificaciones/editar.php?id=" . $id);
            exit;
        }
    }
}

header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
exit;
?>

