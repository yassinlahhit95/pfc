<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/AdminGuard.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Crypto.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$q = trim(strip_tags($_GET['q'] ?? ''));
if (mb_strlen($q) < 2 || mb_strlen($q) > 100) { echo json_encode([]); exit; }

$qEsc = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
$like  = '%' . $qEsc . '%';
$con   = obtenerConexion();
$results = [];

// ══════════════════════════════════════════════════════════════════════
// BÚSQUEDA POR ENTIDAD
// ══════════════════════════════════════════════════════════════════════

// ── Estudiantes (por nombre o DNI) ──
// dniEstudiante está cifrado (determinista, RGPD Art. 32) — un LIKE sobre el
// cifrado no tiene sentido, así que se trae el candidato completo ordenado
// por nombre y se filtra en PHP tras descifrar el DNI. Aceptable a escala de
// un solo centro (cientos de alumnos), se corta en cuanto hay 5 resultados.
$stmt = mysqli_prepare($con, "SELECT idEstudiante, nombreEstudiante, dniEstudiante FROM estudiantes ORDER BY nombreEstudiante");
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$numEst = 0;
while ($numEst < 5 && ($row = mysqli_fetch_assoc($res))) {
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
        'url'   => '/vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=' . (int)$row['idEstudiante'],
    ];
    $numEst++;
}

// ── Profesores (por nombre o DNI) ──
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
        'url'   => '/vistas/admin/profesores/verDetallesProfesores.php?idProfesor=' . (int)$row['idProfesor'],
    ];
}

// ── Retos ──
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
        'url'   => '/vistas/admin/retos/modificarRetos.php?idReto=' . (int)$row['idReto'],
    ];
}

// ── Módulos (editar y asignar profesor) ──
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
        'url'     => '/vistas/admin/modulos/modificarModulos.php?idModulo=' . $id,
    ];
    $results[] = [
        'type'    => 'modulo-asignar',
        'label'   => $row['nombreModulo'] . ' — Asignar profesor',
        'url'     => '/vistas/admin/modulos/asignarProfesorModulo.php?idModulo=' . $id,
    ];
}

// ── Ciclos ──
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
        'url'   => '/vistas/admin/ciclos/modificarCiclos.php?idCiclo=' . (int)$row['idCiclo'],
    ];
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
        'url'   => '/vistas/admin/anuncios/gestionAnuncios.php',
    ];
}

// ── Directores ──
// dniDirector está cifrado (determinista) — mismo enfoque que estudiantes arriba.
$stmt = mysqli_prepare($con, "SELECT idDirector, nombreDirector, dniDirector FROM directores ORDER BY nombreDirector");
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$numDir = 0;
while ($numDir < 2 && ($row = mysqli_fetch_assoc($res))) {
    $dniPlano = Crypto::decrypt($row['dniDirector']);
    $nombreMatch = stripos($row['nombreDirector'], $q) !== false;
    $dniMatch = !empty($dniPlano) && stripos($dniPlano, $q) !== false;
    if (!$nombreMatch && !$dniMatch) continue;
    $label = $row['nombreDirector'];
    if ($dniMatch) {
        $label .= ' (' . $dniPlano . ')';
    }
    $results[] = [
        'type'  => 'director',
        'label' => $label,
        'url'   => '/vistas/admin/directores/verDetallesDirectores.php?idDirector=' . (int)$row['idDirector'],
    ];
    $numDir++;
}

// ── Secretarias ──
$stmt = mysqli_prepare($con,
    "SELECT idSecretaria, nombreSecretaria, emailSecretaria FROM secretarias
     WHERE nombreSecretaria LIKE ? OR emailSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 2");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $label = $row['nombreSecretaria'];
    if (!empty($row['emailSecretaria']) && stripos($row['emailSecretaria'], $q) !== false) {
        $label .= ' (' . $row['emailSecretaria'] . ')';
    }
    $results[] = [
        'type'  => 'secretaria',
        'label' => $label,
        'url'   => '/vistas/admin/secretarias/modificarSecretaria.php?idSecretaria=' . (int)$row['idSecretaria'],
    ];
}

// ── Archivos (recursos.php) ──
$stmt = mysqli_prepare($con,
    "SELECT a.idArchivo, a.nombreOriginal, a.idModulo, m.nombreModulo
     FROM aula_archivos a
     JOIN modulos m ON a.idModulo = m.idModulo
     WHERE a.eliminado = 0 AND a.nombreOriginal LIKE ?
     ORDER BY a.fechaSubida DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'archivo',
        'label' => $row['nombreOriginal'] . ' ('. $row['nombreModulo'] .')',
        'url'   => '#', // Admin does not have direct access to aula/recursos.php
    ];
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode($results, JSON_UNESCAPED_UNICODE);
