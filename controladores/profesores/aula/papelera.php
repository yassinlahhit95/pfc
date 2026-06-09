<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
// Operaciones de la papelera de reciclaje (#12): restaurar o eliminar
// definitivamente archivos y carpetas. Sólo el profesor propietario.
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$accion     = $_POST['accion']   ?? '';   // restaurar | eliminar
$tipo       = $_POST['tipo']     ?? '';   // archivo | carpeta
$id         = intval($_POST['id'] ?? 0);
$idModulo   = intval($_POST['idModulo'] ?? 0);

if ($id > 0 && in_array($accion, ['restaurar','eliminar']) && in_array($tipo, ['archivo','carpeta'])) {
    if ($tipo === 'archivo') {
        $item = obtenerArchivoPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') { restaurarArchivoAula($id);  $_SESSION['exito'] = "Archivo restaurado."; }
            else                         { eliminarDefinitivoArchivoAula($id); $_SESSION['exito'] = "Archivo eliminado definitivamente."; }
        }
    } else { // carpeta
        $item = obtenerCarpetaAulaPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            if ($accion === 'restaurar') { restaurarCarpetaAula($id);  $_SESSION['exito'] = "Carpeta restaurada."; }
            else                         { eliminarDefinitivoCarpetaAula($id); $_SESSION['exito'] = "Carpeta eliminada definitivamente."; }
        }
    }
}

header("Location: ../../../vistas/profesores/aula/papelera.php?id=$idModulo");
exit;
