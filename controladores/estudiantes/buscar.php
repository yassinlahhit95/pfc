<?php
// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/estudiantes.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) {
    echo json_encode([]);
    exit;
}

$idEstudiante = (int)$_SESSION['idEstudiante'];
$datos        = obtenerEstudiantePorId($idEstudiante);
$idCiclo      = (int)($datos['idCiclo'] ?? 0);

$qEsc    = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like    = '%' . $qEsc . '%';
$con     = obtenerConexion();
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

// ── Estudiantes (del mismo ciclo) ──
if ($idCiclo > 0) {
    $stmt = mysqli_prepare($con,
        "SELECT idEstudiante, nombreEstudiante, dniEstudiante FROM estudiantes
         WHERE idCiclo = ? AND idEstudiante != ? AND (nombreEstudiante LIKE ? OR dniEstudiante LIKE ?)
         ORDER BY nombreEstudiante LIMIT 3");
    mysqli_stmt_bind_param($stmt, 'iiss', $idCiclo, $idEstudiante, $like, $like);
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
            'url'   => '../chat/index.php', // Assuming direct chat access
        ];
    }
}

// ── Profesores (de su ciclo) ──
if ($idCiclo > 0) {
    $stmt = mysqli_prepare($con,
        "SELECT DISTINCT p.idProfesor, p.nombreProfesor, p.dniProfesor 
         FROM profesores p
         JOIN modulo_profesor mp ON p.idProfesor = mp.idProfesor
         JOIN modulos m ON mp.idModulo = m.idModulo
         WHERE m.idCiclo = ? AND (p.nombreProfesor LIKE ? OR p.dniProfesor LIKE ?)
         ORDER BY p.nombreProfesor LIMIT 3");
    mysqli_stmt_bind_param($stmt, 'iss', $idCiclo, $like, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $label = $row['nombreProfesor'];
        $results[] = [
            'type'  => 'profesor',
            'label' => $label,
            'url'   => '../chat/index.php',
        ];
    }
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

// ── Archivos (recursos.php) de su ciclo ──
if ($idCiclo > 0) {
    $stmt = mysqli_prepare($con,
        "SELECT a.idArchivo, a.nombreOriginal, a.idModulo, m.nombreModulo
         FROM aula_archivos a
         JOIN modulos m ON a.idModulo = m.idModulo
         WHERE m.idCiclo = ? AND a.eliminado = 0 AND a.nombreOriginal LIKE ?
         ORDER BY a.fechaSubida DESC LIMIT 3");
    mysqli_stmt_bind_param($stmt, 'is', $idCiclo, $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = [
            'type'  => 'archivo',
            'label' => $row['nombreOriginal'] . ' ('. $row['nombreModulo'] .')',
            'url'   => '../aula/recursos.php?id=' . (int)$row['idModulo'],
        ];
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode($results, JSON_UNESCAPED_UNICODE);
