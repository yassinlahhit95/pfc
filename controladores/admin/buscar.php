<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['idAdmin'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../../modelos/conectar.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like  = '%' . $qEsc . '%';
$con   = obtenerConexion();
$results = [];

// Estudiantes
$stmt = mysqli_prepare($con, "SELECT nombreEstudiante FROM estudiantes WHERE nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 4");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'estudiante', 'label' => $row['nombreEstudiante'], 'url' => '../estudiantes/verEstudiantes.php'];
}

// Profesores
$stmt = mysqli_prepare($con, "SELECT nombreProfesor FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'profesor', 'label' => $row['nombreProfesor'], 'url' => '../profesores/verProfesores.php'];
}

// Retos
$stmt = mysqli_prepare($con, "SELECT nombreReto FROM retos WHERE nombreReto LIKE ? ORDER BY nombreReto LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'reto', 'label' => $row['nombreReto'], 'url' => '../retos/verRetos.php'];
}

// Módulos
$stmt = mysqli_prepare($con, "SELECT nombreModulo FROM modulos WHERE nombreModulo LIKE ? ORDER BY nombreModulo LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'modulo', 'label' => $row['nombreModulo'], 'url' => '../modulos/verModulos.php'];
}

// Anuncios activos
$stmt = mysqli_prepare($con, "SELECT titulo FROM anuncios WHERE titulo LIKE ? AND fechaExpiracion >= CURDATE() LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'anuncio', 'label' => $row['titulo'], 'url' => '../anuncios/gestionAnuncios.php'];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
