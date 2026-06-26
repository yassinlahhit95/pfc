<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';

header('Content-Type: application/json');

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════

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
