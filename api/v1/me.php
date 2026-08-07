<?php
declare(strict_types=1);

// GET /api/v1/me.php — returns the authenticated user's profile

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

[$tabla, $idCol] = V1_USER_MAP[$type];
$con = obtenerConexion();

$st = mysqli_prepare($con, "SELECT * FROM `$tabla` WHERE `$idCol` = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

if (!$row) {
    v1Error('User account not found.', 404, 'not_found');
}

// Attach cycle info for students or professors (if they are a tutor)
$ciclo = null;
$hijosCount = 0;
$hijos = [];

if ($type === 'estudiante' && !empty($row['idCiclo'])) {
    $sc = mysqli_prepare($con,
        'SELECT idCiclo, nombreCiclo, abreviaturaCiclo FROM ciclos WHERE idCiclo = ? LIMIT 1');
    mysqli_stmt_bind_param($sc, 'i', $row['idCiclo']);
    mysqli_stmt_execute($sc);
    $ciclo = mysqli_fetch_assoc(mysqli_stmt_get_result($sc)) ?: null;
} elseif ($type === 'profesor' && !empty($row['esTutor']) && !empty($row['idCicloTutor'])) {
    $sc = mysqli_prepare($con,
        'SELECT idCiclo, nombreCiclo, abreviaturaCiclo FROM ciclos WHERE idCiclo = ? LIMIT 1');
    mysqli_stmt_bind_param($sc, 'i', $row['idCicloTutor']);
    mysqli_stmt_execute($sc);
    $ciclo = mysqli_fetch_assoc(mysqli_stmt_get_result($sc)) ?: null;
} elseif ($type === 'tutor') {
    require_once __DIR__ . '/../../modelos/tutores.php';
    $hijos = listarEstudiantesPorTutor($uid);
    $hijosCount = count($hijos);
}

v1Ok([
    'user_type' => $type,
    'profile'   => v1Strip($row),
    'ciclo'     => $ciclo,
    'hijos_count' => $hijosCount,
    'hijos'     => $hijos,
]);
