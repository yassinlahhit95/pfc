<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

$hayError = false;

if (isset($_POST['guardarTFG'])) {
    $idEstudiantePfc = trim($_POST['idEstudiante']);
    $tituloNuevoTFG = trim($_POST['tituloTFG']);
    $archivoSubido = $_FILES['archivoTFG'];
    $nombreArchivoFinal = "";

    if (isset($archivoSubido) && $archivoSubido['error'] === UPLOAD_ERR_OK) {
        $timestampActual = date('d-m-Y_H-i-s');
        $nombreArchivoFinal = $timestampActual . "_" . basename($archivoSubido['name']);
        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivoFinal;
        
        if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaDestino)) {
            $hayError = true;
            $_SESSION['errores'] = "Error al guardar archivo.";
        }
    }

    if (!$hayError) {
        if (actualizarDatosTFG($idEstudiantePfc, $tituloNuevoTFG, $nombreArchivoFinal)) {
            $_SESSION['exito'] = "TFG actualizado.";
        } else {
            $hayError = true;
            $_SESSION['errores'] = "Error al actualizar.";
        }
    }
}

header("Location: ../../../vistas/admin/pfc/verTFGs.php");
exit;
?>
