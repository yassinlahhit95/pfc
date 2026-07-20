<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_fct');
require_once __DIR__ . "/../../../modelos/fct.php";

header('Content-Type: application/json');

$idFCT = (int)($_POST['idFCT'] ?? 0);
if (!$idFCT) {
    echo json_encode(['ok' => false, 'msg' => 'FCT no encontrada.']);
    exit;
}

$ok = eliminarFCT($idFCT);
echo json_encode(['ok' => $ok, 'msg' => $ok ? 'FCT eliminada.' : 'No se pudo eliminar.']);
