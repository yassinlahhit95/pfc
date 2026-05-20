<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_POST['actualizarTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $tituloTFG = trim($_POST['tituloTFG']);

    $hayError = false;

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/profesores/pfc/lista.php");
        exit;
    }

    if (empty($tituloTFG)) {
        $_SESSION['errores'] = "El título del TFG es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = actualizarDatosTFG($idEstudiante, $tituloTFG);
        if ($resultado) {
            $_SESSION['exito'] = "Datos del TFG actualizados.";
        } else {
            $_SESSION['errores'] = "Error al actualizar el TFG.";
        }
    }

    header("Location: ../../../vistas/profesores/pfc/lista.php");
    exit;
}

header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
?>
