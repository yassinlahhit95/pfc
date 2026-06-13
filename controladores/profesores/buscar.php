<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['idProfesor'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$idProfesor = (int)$_SESSION['idProfesor'];
$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like  = '%' . $qEsc . '%';
$con   = obtenerConexion();
$results = [];

// Estudiantes asignados al profesor (por nombre o DNI)
$stmt = mysqli_prepare($con,
    "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante, e.dniEstudiante
     FROM estudiantes e
     WHERE (e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
        OR  e.idCiclo IN (SELECT m.idCiclo FROM modulos m
                          JOIN modulo_profesor pm ON m.idModulo = pm.idModulo
                          WHERE pm.idProfesor = ?))
       AND (e.nombreEstudiante LIKE ? OR e.dniEstudiante LIKE ?)
     ORDER BY e.nombreEstudiante
     LIMIT 4");
mysqli_stmt_bind_param($stmt, 'iiss', $idProfesor, $idProfesor, $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $label = $row['nombreEstudiante'];
    if (!empty($row['dniEstudiante']) && stripos($row['dniEstudiante'], $q) !== false) {
        $label .= ' (' . $row['dniEstudiante'] . ')';
    }
    $results[] = [
        'type'  => 'estudiante',
        'label' => $label,
        'url'   => '../estudiantes/lista.php',
    ];
}

// Retos del profesor
$stmt = mysqli_prepare($con,
    "SELECT DISTINCT r.idReto, r.nombreReto
     FROM retos r
     JOIN modulo_reto mr ON r.idReto = mr.idReto
     JOIN modulo_profesor pm ON mr.idModulo = pm.idModulo
     WHERE pm.idProfesor = ? AND r.nombreReto LIKE ?
     ORDER BY r.nombreReto
     LIMIT 4");
mysqli_stmt_bind_param($stmt, 'is', $idProfesor, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'reto',
        'label' => $row['nombreReto'],
        'url'   => '../retos/lista.php',
    ];
}

// Módulos del profesor
$stmt = mysqli_prepare($con,
    "SELECT m.idModulo, m.nombreModulo
     FROM modulos m
     JOIN modulo_profesor pm ON m.idModulo = pm.idModulo
     WHERE pm.idProfesor = ? AND m.nombreModulo LIKE ?
     ORDER BY m.nombreModulo
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 'is', $idProfesor, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'modulo',
        'label' => $row['nombreModulo'],
        'url'   => '../modulos/lista.php',
    ];
}

// Anuncios activos (todos y profesores)
$stmt = mysqli_prepare($con,
    "SELECT titulo FROM anuncios
     WHERE (dirigidoA = 'todos' OR dirigidoA = 'profesores')
       AND titulo LIKE ?
       AND fechaExpiracion >= CURDATE()
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'anuncio',
        'label' => $row['titulo'],
        'url'   => '../anuncios/lista.php',
    ];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
