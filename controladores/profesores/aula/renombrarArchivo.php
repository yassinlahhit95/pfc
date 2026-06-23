<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/recursos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idArchivo   = intval($_POST['idArchivo'] ?? 0);
$nuevoNombre = trim($_POST['nombre'] ?? '');
$idModulo    = intval($_POST['idModulo'] ?? 0);

if ($idArchivo > 0 && $nuevoNombre !== '') {
    $archivo = obtenerArchivoPorId($idArchivo);
    if ($archivo && $archivo['idProfesor'] == $_SESSION['idProfesor']) {
        // Conservar la extensión original del fichero
        $ext = $archivo['extension'];
        if (strtolower(pathinfo($nuevoNombre, PATHINFO_EXTENSION)) !== strtolower($ext)) {
            $nuevoNombre .= '.' . $ext;
        }
        renombrarArchivoAula($idArchivo, $nuevoNombre);
        $_SESSION['exito'] = "El archivo ha sido renombrado correctamente.";
        $idModulo = $idModulo ?: $archivo['idModulo'];
    } else {
        $_SESSION['errores'] = "No tienes permiso para renombrar este archivo.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
header("Location: $destino");
exit;
