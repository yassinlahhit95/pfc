<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();

// Ensure authenticated user (admin, teacher, or registrar)
if (!isset($_SESSION['idAdmin']) && !isset($_SESSION['idProfesor'])) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

require_once __DIR__ . "/../../modelos/grupos.php";

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$anioEstudio = trim($_GET['anioEstudio'] ?? '');

if ($idCiclo <= 0 || empty($anioEstudio)) {
    echo json_encode([]);
    exit;
}

$grupos = listarGruposPorCicloYAnio($idCiclo, $anioEstudio);
echo json_encode($grupos);
exit;
