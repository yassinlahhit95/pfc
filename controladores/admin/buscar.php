<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['idAdmin'])) { http_response_code(403); echo json_encode([]); exit; }
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like  = '%' . $qEsc . '%';
$con   = obtenerConexion();
$results = [];

// Estudiantes → detalle individual (por nombre o DNI)
$stmt = mysqli_prepare($con,
    "SELECT idEstudiante, nombreEstudiante, dniEstudiante FROM estudiantes
     WHERE nombreEstudiante LIKE ? OR dniEstudiante LIKE ?
     ORDER BY nombreEstudiante LIMIT 5");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
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
        'url'   => '../estudiantes/verDetallesEstudiantes.php?idEstudiante=' . (int)$row['idEstudiante'],
    ];
}

// Profesores → detalle individual (por nombre o DNI)
$stmt = mysqli_prepare($con,
    "SELECT idProfesor, nombreProfesor, dniProfesor FROM profesores
     WHERE nombreProfesor LIKE ? OR dniProfesor LIKE ?
     ORDER BY nombreProfesor LIMIT 3");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $label = $row['nombreProfesor'];
    if (!empty($row['dniProfesor']) && stripos($row['dniProfesor'], $q) !== false) {
        $label .= ' (' . $row['dniProfesor'] . ')';
    }
    $results[] = [
        'type'  => 'profesor',
        'label' => $label,
        'url'   => '../profesores/verDetallesProfesores.php?idProfesor=' . (int)$row['idProfesor'],
    ];
}

// Retos → editar
$stmt = mysqli_prepare($con,
    "SELECT idReto, nombreReto FROM retos
     WHERE nombreReto LIKE ? ORDER BY nombreReto LIMIT 4");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'reto',
        'label' => $row['nombreReto'],
        'url'   => '../retos/modificarRetos.php?idReto=' . (int)$row['idReto'],
    ];
}

// Módulos → dos acciones por módulo: Editar y Asignar profesor
$stmt = mysqli_prepare($con,
    "SELECT idModulo, nombreModulo FROM modulos
     WHERE nombreModulo LIKE ? ORDER BY nombreModulo LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $id = (int)$row['idModulo'];
    $results[] = [
        'type'    => 'modulo',
        'label'   => $row['nombreModulo'] . ' — Editar',
        'url'     => '../modulos/modificarModulos.php?idModulo=' . $id,
    ];
    $results[] = [
        'type'    => 'modulo-asignar',
        'label'   => $row['nombreModulo'] . ' — Asignar profesor',
        'url'     => '../modulos/asignarProfesorModulo.php?idModulo=' . $id,
    ];
}

// Ciclos → editar
$stmt = mysqli_prepare($con,
    "SELECT idCiclo, nombreCiclo FROM ciclos
     WHERE nombreCiclo LIKE ? ORDER BY nombreCiclo LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'ciclo',
        'label' => $row['nombreCiclo'],
        'url'   => '../ciclos/modificarCiclos.php?idCiclo=' . (int)$row['idCiclo'],
    ];
}

// Anuncios activos
$stmt = mysqli_prepare($con,
    "SELECT titulo FROM anuncios
     WHERE titulo LIKE ? AND fechaExpiracion >= CURDATE()
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'anuncio',
        'label' => $row['titulo'],
        'url'   => '../anuncios/gestionAnuncios.php',
    ];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
