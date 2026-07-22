<?php
// ══════════════════════════════════════════════════════════════════════
// BÚSQUEDA GLOBAL DE SECRETARÍA (topbar)
// Devuelve JSON [{type, label, url}] para dashboard-shell.js
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();
if (empty($_SESSION['idSecretaria'])) { http_response_code(403); echo json_encode([]); exit; }
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); echo json_encode([]); exit; }

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Crypto.php';

$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like = '%' . $qEsc . '%';
$con  = obtenerConexion();
$results = [];

// ── Estudiantes (por nombre o DNI) ──
// dniEstudiante está cifrado (determinista, RGPD Art. 32) — se filtra en PHP
// tras descifrar en vez de con LIKE sobre el cifrado.
$stmt = mysqli_prepare($con, "SELECT idEstudiante, nombreEstudiante, dniEstudiante FROM estudiantes ORDER BY nombreEstudiante");
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$numEst = 0;
while ($numEst < 6 && ($row = mysqli_fetch_assoc($res))) {
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
        'url'   => '/vistas/secretaria/estudiantes/verDetallesEstudiantes.php?idEstudiante=' . (int)$row['idEstudiante'],
    ];
    $numEst++;
}

// ── Anuncios activos ──
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
        'url'   => '/vistas/secretaria/anuncios/gestionAnuncios.php',
    ];
}

// ── Eventos ──
$stmt = mysqli_prepare($con,
    "SELECT idEvento, tituloEvento FROM eventos
     WHERE tituloEvento LIKE ? ORDER BY fechaEvento DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'evento',
        'label' => $row['tituloEvento'],
        'url'   => '/vistas/secretaria/eventos/gestionEventos.php',
    ];
}

echo json_encode($results, JSON_UNESCAPED_UNICODE);
