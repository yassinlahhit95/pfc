<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!Security::validateCSRFToken()) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida']); exit; }
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

$msgExito = '';

if ($id > 0 && in_array($accion, ['restaurar','eliminar']) && in_array($tipo, ['archivo','carpeta'])) {
    if ($tipo === 'archivo') {
        $item = obtenerArchivoPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') {
                restaurarArchivoAula($id);
                $msgExito = "El archivo ha sido restaurado.";
            } else {
                eliminarDefinitivoArchivoAula($id);
                $msgExito = "El archivo ha sido eliminado definitivamente.";
            }
        }
    } else {
        $item = obtenerCarpetaAulaPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') {
                restaurarCarpetaAula($id);
                $msgExito = "La carpeta ha sido restaurada.";
            } else {
                eliminarDefinitivoCarpetaAula($id);
                $msgExito = "La carpeta ha sido eliminada definitivamente.";
            }
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($msgExito) {
    if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>$msgExito]); exit; }
    $_SESSION['exito'] = $msgExito;
} else {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No se pudo procesar la solicitud']); exit; }
}

header("Location: ../../../vistas/profesores/aula/papelera.php?id=$idModulo");
exit;
