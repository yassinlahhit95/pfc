<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../../modelos/estudiantes.php';

$idCiclo = (int)($_GET['idCiclo'] ?? 0);

if ($idCiclo <= 0) {
    echo json_encode([]);
    exit;
}

$estudiantes = listarEstudiantesPorCiclo($idCiclo);

// Limpiar datos sensibles antes de enviar
$resultado = array_map(function($e) {
    return [
        'idEstudiante' => $e['idEstudiante'],
        'nombreEstudiante' => $e['nombreEstudiante']
    ];
}, $estudiantes);

echo json_encode($resultado);
