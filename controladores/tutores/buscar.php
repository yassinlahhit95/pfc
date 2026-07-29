<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
// ══════════════════════════════════════════════════════════════════════
// BÚSQUEDA GLOBAL DE TUTORES (topbar)
// Devuelve JSON [{type, label, url}] para dashboard-shell.js
// ══════════════════════════════════════════════════════════════════════
Security::initSession();
if (empty($_SESSION['idTutor'])) { http_response_code(403); echo json_encode([]); exit; }
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../include/Crypto.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$idTutor = (int)$_SESSION['idTutor'];
$qEsc    = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like    = '%' . $qEsc . '%';
$con     = obtenerConexion();
$results = [];

// ── Hijos del tutor (por nombre o DNI) ──
// dniEstudiante está cifrado (determinista, RGPD Art. 32) — se filtra en PHP
// tras descifrar, ya acotado a los hijos del tutor.
$hijos = listarEstudiantesPorTutor($idTutor);
foreach ($hijos as $hijo) {
    $dniPlano = Crypto::decrypt($hijo['dniEstudiante'] ?? '');
    $nombreMatch = stripos($hijo['nombreEstudiante'], $q) !== false;
    $dniMatch = !empty($dniPlano) && stripos($dniPlano, $q) !== false;
    if (!$nombreMatch && !$dniMatch) continue;
    $label = $hijo['nombreEstudiante'];
    if ($dniMatch) {
        $label .= ' (' . $dniPlano . ')';
    }
    $results[] = [
        'type'  => 'estudiante',
        'label' => $label,
        'url'   => '/vistas/tutores/estudiantes/expediente.php?id=' . (int)$hijo['idEstudiante'],
    ];
}

// ── Pagos de los hijos ──
$idsHijos = array_column($hijos, 'idEstudiante');
if (!empty($idsHijos)) {
    $placeholders = implode(',', array_fill(0, count($idsHijos), '?'));
    $types = str_repeat('i', count($idsHijos)) . 's';
    $stmt = mysqli_prepare($con,
        "SELECT p.idPago, p.monto, p.fechaPago, e.nombreEstudiante
         FROM pagos p
         JOIN estudiantes e ON e.idEstudiante = p.idEstudiante
         WHERE p.idEstudiante IN ($placeholders) AND e.nombreEstudiante LIKE ?
         ORDER BY p.fechaPago DESC LIMIT 3");
    $params = array_merge($idsHijos, [$like]);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        $results[] = [
            'type'  => 'pago',
            'label' => 'Pago ' . $row['monto'] . '€ — ' . $row['nombreEstudiante'] . ' (' . date('d/m/Y', strtotime($row['fechaPago'])) . ')',
            'url'   => '/vistas/tutores/pagos/misPagos.php',
        ];
    }
}

// ── Anuncios dirigidos a tutores ──
$stmt = mysqli_prepare($con,
    "SELECT titulo FROM anuncios
     WHERE (dirigidoA = 'todos' OR dirigidoA = 'tutores')
       AND fechaExpiracion >= CURDATE()
       AND titulo LIKE ?
     LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'anuncio', 'label' => $row['titulo'], 'url' => '/vistas/tutores/anuncios/lista.php'];
}

// ── Eventos ──
$stmt = mysqli_prepare($con,
    "SELECT tituloEvento FROM eventos
     WHERE tituloEvento LIKE ? ORDER BY fechaEvento DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = ['type' => 'evento', 'label' => $row['tituloEvento'], 'url' => '/vistas/tutores/eventos/lista.php'];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
