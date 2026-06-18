<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['idEstudiante'])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/estudiantes.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) {
    echo json_encode([]);
    exit;
}

$idEstudiante = (int)$_SESSION['idEstudiante'];
$datos  = obtenerEstudiantePorId($idEstudiante);
$idCiclo = (int)($datos['idCiclo'] ?? 0);

$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like  = '%' . $qEsc . '%';
$con  = obtenerConexion();
$results = [];

// Retos del ciclo del estudiante
$stmt = mysqli_prepare($con,
    "SELECT DISTINCT r.idReto, r.nombreReto
     FROM retos r
     JOIN modulo_reto mr ON r.idReto = mr.idReto
     JOIN modulos m ON mr.idModulo = m.idModulo
     WHERE m.idCiclo = ? AND r.nombreReto LIKE ?
     LIMIT 4");
mysqli_stmt_bind_param($stmt, 'is', $idCiclo, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'reto', 'label' => $row['nombreReto'], 'url' => '../retos/lista.php'];
}

// Anuncios visibles para estudiantes
$stmt = mysqli_prepare($con,
    "SELECT titulo FROM anuncios
     WHERE (dirigidoA = 'todos' OR dirigidoA = 'estudiantes')
       AND fechaExpiracion >= CURDATE()
       AND titulo LIKE ?
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'anuncio', 'label' => $row['titulo'], 'url' => '../anuncios/lista.php'];
}

// Mensajes del propio estudiante
$stmt = mysqli_prepare($con,
    "SELECT idReclamacion, asunto FROM reclamaciones
     WHERE idEstudiante = ? AND asunto LIKE ?
     ORDER BY idReclamacion DESC
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 'is', $idEstudiante, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'mensaje',
        'label' => $row['asunto'],
        'url'   => '../mensajes/detalles.php?id=' . (int)$row['idReclamacion'],
    ];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
