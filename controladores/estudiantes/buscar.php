<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
Security::initSession();
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
require_once __DIR__ . '/../../include/Crypto.php';

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
    $results[] = ['type' => 'reto', 'label' => $row['nombreReto'], 'url' => '/vistas/estudiantes/retos/lista.php'];
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
    $results[] = ['type' => 'anuncio', 'label' => $row['titulo'], 'url' => '/vistas/estudiantes/anuncios/lista.php'];
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
        'url'   => '/vistas/estudiantes/mensajes/detalles.php?id=' . (int)$row['idReclamacion'],
    ];
}

// ── Estudiantes (del mismo ciclo) ──
// dniEstudiante está cifrado (determinista, RGPD Art. 32) — se mantiene el
// scope de ciclo/auto-exclusión en SQL, filtro de nombre/DNI en PHP.
if ($idCiclo > 0) {
    $stmt = mysqli_prepare($con,
        "SELECT idEstudiante, nombreEstudiante, dniEstudiante FROM estudiantes
         WHERE idCiclo = ? AND idEstudiante != ?
         ORDER BY nombreEstudiante");
    mysqli_stmt_bind_param($stmt, 'ii', $idCiclo, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $numEst = 0;
    while ($numEst < 3 && ($row = mysqli_fetch_assoc($res))) {
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
            'url'   => '/vistas/estudiantes/chat/index.php', // Assuming direct chat access
        ];
        $numEst++;
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
            'url'   => '/vistas/estudiantes/chat/index.php',
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
        'url'   => '/vistas/estudiantes/chat/index.php',
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
        'url'   => '/vistas/estudiantes/chat/index.php',
    ];
}

// ── Sus propios pagos ──
$stmt = mysqli_prepare($con,
    "SELECT monto, fechaPago FROM pagos WHERE idEstudiante = ? ORDER BY fechaPago DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 'i', $idEstudiante);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'pago',
        'label' => 'Pago ' . $row['monto'] . '€ (' . date('d/m/Y', strtotime($row['fechaPago'])) . ')',
        'url'   => '/vistas/estudiantes/pagos/lista.php',
    ];
}

// ── Su TFG ──
if (stripos('tfg trabajo fin de grado', $q) !== false) {
    $stmtTfg = mysqli_prepare($con, "SELECT nota FROM calificaciones_tfg WHERE idEstudiante = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtTfg, 'i', $idEstudiante);
    mysqli_stmt_execute($stmtTfg);
    $resTfg = mysqli_stmt_get_result($stmtTfg);
    if (mysqli_fetch_assoc($resTfg)) {
        $results[] = ['type' => 'tfg', 'label' => 'Mi TFG', 'url' => '/vistas/estudiantes/academico/resultadosFinales.php'];
    }
}

// ── Eventos ──
$stmt = mysqli_prepare($con,
    "SELECT tituloEvento FROM eventos WHERE tituloEvento LIKE ? ORDER BY fechaEvento DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'evento', 'label' => $row['tituloEvento'], 'url' => '/vistas/estudiantes/eventos/lista.php'];
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
            'url'   => '/vistas/estudiantes/aula/recursos.php?id=' . (int)$row['idModulo'],
        ];
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode($results, JSON_UNESCAPED_UNICODE);
