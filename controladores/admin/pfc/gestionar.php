<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$hayError = false;

if (isset($_POST['guardarTFG'])) {
    $idEstudiantePfc = (int)trim($_POST['idEstudiante']);
    $tituloNuevoTFG = trim($_POST['tituloTFG']);
    $archivoSubido = $_FILES['archivoTFG'] ?? null;
    $nombreArchivoFinal = "";

    if (!$idEstudiantePfc) {
        $hayError = true;
        $_SESSION['errores'] = "Falta ID estudiante.";
    } elseif ($archivoSubido && $archivoSubido['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $archivoSubido['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf' || strtolower(pathinfo($archivoSubido['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            $hayError = true;
            $_SESSION['errores'] = "Solo se aceptan archivos PDF.";
        } else {
            $nombreArchivoFinal = date('d-m-Y_H-i-s') . '_' . bin2hex(random_bytes(8)) . '.pdf';
            $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivoFinal;
            if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaDestino)) {
                $hayError = true;
                $_SESSION['errores'] = "Error al guardar archivo.";
            }
        }
    }

    if (!$hayError) {
        if (actualizarDatosTFG($idEstudiantePfc, $tituloNuevoTFG, $nombreArchivoFinal)) {
            $_SESSION['exito'] = "TFG actualizado.";
        } else {
            $_SESSION['errores'] = "Error al actualizar.";
        }
    }
}

header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
exit;
?>
