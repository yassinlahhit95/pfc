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
        $_SESSION['error'] = "Vaya, el tÃ­tulo del TFG es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = actualizarDatosTFG($idEstudiante, $tituloTFG);
        if ($resultado) {
            $_SESSION['exito'] = "Listo! Datos del TFG actualizados correctamente.";
        } else {
            $_SESSION['error'] = "Vaya, ha habido un error al actualizar los datos del TFG.";
        }
    }

    header("Location: ../../../vistas/profesores/pfc/lista.php");
    exit;
}

header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
?>