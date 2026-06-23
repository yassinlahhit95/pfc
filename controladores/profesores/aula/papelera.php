<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/papelera.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];
$accion     = $_POST['accion']   ?? '';   // restaurar | eliminar
$tipo       = $_POST['tipo']     ?? '';   // archivo | carpeta
$id         = intval($_POST['id'] ?? 0);
$idModulo   = intval($_POST['idModulo'] ?? 0);

if ($id > 0 && in_array($accion, ['restaurar','eliminar']) && in_array($tipo, ['archivo','carpeta'])) {
    if ($tipo === 'archivo') {
        $item = obtenerArchivoPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') {
                restaurarArchivoAula($id);
                $_SESSION['exito'] = "El archivo ha sido restaurado.";
            } else {
                eliminarDefinitivoArchivoAula($id);
                $_SESSION['exito'] = "El archivo ha sido eliminado definitivamente.";
            }
        }
    } else {
        $item = obtenerCarpetaAulaPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') {
                restaurarCarpetaAula($id);
                $_SESSION['exito'] = "La carpeta ha sido restaurada.";
            } else {
                eliminarDefinitivoCarpetaAula($id);
                $_SESSION['exito'] = "La carpeta ha sido eliminada definitivamente.";
            }
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/aula/papelera.php?id=$idModulo");
exit;
