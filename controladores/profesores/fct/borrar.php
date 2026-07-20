<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_fct');
require_once __DIR__ . "/../../../modelos/fct.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

header('Content-Type: application/json');

$idProfesor = (int)$_SESSION['idProfesor'];
$idFCT = (int)($_POST['idFCT'] ?? 0);
$fct = $idFCT ? obtenerFCTPorId($idFCT) : null;

if (!$fct) {
    echo json_encode(['ok' => false, 'msg' => 'FCT no encontrada.']);
    exit;
}

$misEstudiantes = array_column(listarEstudiantesDeProfesor($idProfesor), null, 'idEstudiante');
if (!isset($misEstudiantes[(int)$fct['idEstudiante']])) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes acceso a esta FCT.']);
    exit;
}

$ok = eliminarFCT($idFCT);
echo json_encode(['ok' => $ok, 'msg' => $ok ? 'FCT eliminada.' : 'No se pudo eliminar.']);
