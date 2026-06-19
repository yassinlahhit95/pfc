<?php
// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Acción bloqueada']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../modelos/estudiantes.php';

$idCiclo = (int)($_GET['idCiclo'] ?? 0);

if ($idCiclo <= 0) {
    echo json_encode([]);
    exit;
}

$estudiantes = listarEstudiantesPorCiclo($idCiclo);

// Solo se exponen nombre e ID — se omiten datos sensibles del resto de columnas
$resultado = array_map(fn($e) => [
    'idEstudiante'     => $e['idEstudiante'],
    'nombreEstudiante' => $e['nombreEstudiante'],
], $estudiantes);

echo json_encode($resultado);
