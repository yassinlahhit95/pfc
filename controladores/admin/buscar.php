<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/AdminGuard.php';
require_once __DIR__ . '/../../modelos/conectar.php';

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
        'url'   => '/vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=' . (int)$row['idEstudiante'],
    ];
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
$stmt = mysqli_prepare($con,
    "SELECT idDirector, nombreDirector, dniDirector FROM directores
     WHERE nombreDirector LIKE ? OR dniDirector LIKE ? ORDER BY nombreDirector LIMIT 2");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $label = $row['nombreDirector'];
    if (!empty($row['dniDirector']) && stripos($row['dniDirector'], $q) !== false) {
        $label .= ' (' . $row['dniDirector'] . ')';
    }
    $results[] = [
        'type'  => 'director',
        'label' => $label,
        'url'   => '/vistas/admin/directores/verDetallesDirectores.php?idDirector=' . (int)$row['idDirector'],
    ];
}

// ── Secretarias ──
$stmt = mysqli_prepare($con,
    "SELECT idSecretaria, nombreSecretaria, dniSecretaria FROM secretarias
     WHERE nombreSecretaria LIKE ? OR dniSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 2");
mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $label = $row['nombreSecretaria'];
    if (!empty($row['dniSecretaria']) && stripos($row['dniSecretaria'], $q) !== false) {
        $label .= ' (' . $row['dniSecretaria'] . ')';
    }
    $results[] = [
        'type'  => 'secretaria',
        'label' => $label,
        'url'   => '/vistas/admin/secretarias/modificarSecretaria.php?idSecretaria=' . (int)$row['idSecretaria'],
    ];
}

// ── Archivos (recursos.php) ──
$stmt = mysqli_prepare($con,
    "SELECT a.idArchivo, a.nombre, a.idModulo, m.nombreModulo
     FROM aula_archivos a
     JOIN modulos m ON a.idModulo = m.idModulo
     WHERE a.eliminado = 0 AND a.nombre LIKE ?
     ORDER BY a.fechaSubida DESC LIMIT 3");
mysqli_stmt_bind_param($stmt, 's', $like);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $results[] = [
        'type'  => 'archivo',
        'label' => $row['nombre'] . ' ('. $row['nombreModulo'] .')',
        'url'   => '#', // Admin does not have direct access to aula/recursos.php
    ];
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode($results, JSON_UNESCAPED_UNICODE);
