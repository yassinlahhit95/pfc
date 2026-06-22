<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarTFG'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
        exit;
    }
    $idEstudiante       = (int)trim($_POST['idEstudiante']);
    $tituloNuevoTFG     = trim($_POST['tituloTFG']);
    $archivoSubido      = $_FILES['archivoTFG'] ?? null;
    $nombreArchivoFinal = "";
    $hayError           = false;

    if (!$idEstudiante) {
        $hayError = true;
        $_SESSION['errores'] = "El identificador del estudiante no es válido.";
    } elseif ($archivoSubido && $archivoSubido['error'] === UPLOAD_ERR_OK) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $archivoSubido['tmp_name']);
        finfo_close($finfo);

        if ($mime !== 'application/pdf' || strtolower(pathinfo($archivoSubido['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            $hayError = true;
            $_SESSION['errores'] = "Solo se aceptan archivos en formato PDF.";
        } else {
            $nombreArchivoFinal = date('d-m-Y_H-i-s') . '_' . bin2hex(random_bytes(8)) . '.pdf';
            $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivoFinal;
            if (!move_uploaded_file($archivoSubido['tmp_name'], $rutaDestino)) {
                $hayError = true;
                $_SESSION['errores'] = "Error al guardar el archivo en el servidor.";
            }
        }
    }

    if (!$hayError) {
        if (actualizarDatosTFG($idEstudiante, $tituloNuevoTFG, $nombreArchivoFinal)) {
            $_SESSION['exito'] = "Los datos del TFG han sido actualizados correctamente.";
        } else {
            $_SESSION['errores'] = "Ocurrió un error al intentar actualizar los datos del TFG.";
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
exit;
