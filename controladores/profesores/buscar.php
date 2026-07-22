<?php
// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();
if (empty($_SESSION['idProfesor'])) { http_response_code(403); echo json_encode([]); exit; }
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Crypto.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$idProfesor = (int)$_SESSION['idProfesor'];
$qEsc       = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like       = '%' . $qEsc . '%';
$con        = obtenerConexion();
$results    = [];

// Estudiantes asignados al profesor (por nombre o DNI)
// dniEstudiante está cifrado (determinista, RGPD Art. 32) — se mantiene el
// scope por ciclo en SQL, pero el filtro de nombre/DNI se hace en PHP tras
// descifrar (ya acotado a los estudiantes del profesor, no toda la tabla).
$stmt = mysqli_prepare($con,
    "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante, e.dniEstudiante
     FROM estudiantes e
     WHERE e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
        OR  e.idCiclo IN (SELECT m.idCiclo FROM modulos m
                          JOIN modulo_profesor pm ON m.idModulo = pm.idModulo
                          WHERE pm.idProfesor = ?)
     ORDER BY e.nombreEstudiante");
mysqli_stmt_bind_param($stmt, 'ii', $idProfesor, $idProfesor);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$numEst = 0;
while ($numEst < 4 && ($row = mysqli_fetch_assoc($res))) {
    $dniPlano = Crypto::decrypt($row['dniEstudiante']);
    $nombreMatch = stripos($row['nombreEstudiante'], $q) !== false;
    $dniMatch = !empty($dniPlano) && stripos($dniPlano, $q) !== false;
    if (!$nombreMatch && !$dniMatch) continue;
    $label = $row['nombreEstudiante'];
    if ($dniMatch) {
        $label .= ' (' . $dniPlano . ')';
    }
    $results[] = [
        'type'  => 'estudiante',
        'label' => $label,
        'url'   => '../estudiantes/lista.php',
    ];
    $numEst++;
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

// ── Otros Profesores ──
$stmt = mysqli_prepare($con,
    "SELECT idProfesor, nombreProfesor, dniProfesor FROM profesores
     WHERE idProfesor != ? AND (nombreProfesor LIKE ? OR dniProfesor LIKE ?)
     ORDER BY nombreProfesor LIMIT 3");
mysqli_stmt_bind_param($stmt, 'iss', $idProfesor, $like, $like);
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
        'url'   => '../chat/index.php',
    ];
}

// ── Directores ──
$stmt = mysqli_prepare($con,
    "SELECT idDirector, nombreDirector FROM directores
     WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 2");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'director',
        'label' => $row['nombreDirector'],
        'url'   => '../chat/index.php',
    ];
}

// ── Secretarias ──
$stmt = mysqli_prepare($con,
    "SELECT idSecretaria, nombreSecretaria FROM secretarias
     WHERE nombreSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 2");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'secretaria',
        'label' => $row['nombreSecretaria'],
        'url'   => '../chat/index.php',
    ];
}

// ── Archivos (recursos.php) subidos por el profesor ──
$stmt = mysqli_prepare($con,
    "SELECT a.idArchivo, a.nombreOriginal, a.idModulo, m.nombreModulo
     FROM aula_archivos a
     JOIN modulos m ON a.idModulo = m.idModulo
     WHERE a.idProfesor = ? AND a.eliminado = 0 AND a.nombreOriginal LIKE ?
     ORDER BY a.fechaSubida DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 'is', $idProfesor, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'archivo',
        'label' => $row['nombreOriginal'] . ' ('. $row['nombreModulo'] .')',
        'url'   => '../aula/recursos.php?id=' . (int)$row['idModulo'],
    ];
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode($results, JSON_UNESCAPED_UNICODE);
