<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/asistencias.php';
require_once __DIR__ . '/../../modelos/justificacionesFalta.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/../../include/R2Client.php';

function justificanteUrl(string $archivo): string {
    $archivoNombre = basename($archivo);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $urlLocal = "$scheme://$host/public/uploads/justificantes/$archivoNombre";
    return R2Client::documentoUrl(
        __DIR__ . '/../../public/uploads/justificantes/' . $archivoNombre,
        $urlLocal,
        'justificantes/' . $archivoNombre
    );
}

function notificarJustificacionResuelta(int $idEstudiante, int $idAsistencia, bool $aprobar, string $motivoRechazo): void {
    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (!file_exists($fh)) return;
    require_once $fh;

    $titulo = $aprobar ? 'Justificación aprobada' : 'Justificación rechazada';
    $mensaje = $aprobar
        ? 'Tu justificación de falta ha sido aprobada.'
        : 'Tu justificación de falta ha sido rechazada' . ($motivoRechazo !== '' ? ": $motivoRechazo" : '.');

    $con = obtenerConexion();
    $destinatarios = [['id' => $idEstudiante, 'rol' => 'estudiante']];
    $st = mysqli_prepare($con, 'SELECT idTutor FROM estudiante_tutor WHERE idEstudiante = ?');
    mysqli_stmt_bind_param($st, 'i', $idEstudiante);
    mysqli_stmt_execute($st);
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $row) {
        $destinatarios[] = ['id' => (int)$row['idTutor'], 'rol' => 'tutor'];
    }
    foreach ($destinatarios as $d) {
        $token = obtenerTokenUsuario($d['id'], $d['rol']);
        if ($token) {
            enviarNotificacionFirebase($token, $titulo, $mensaje, 'asistencia_resuelta', ['idAsistencia' => $idAsistencia]);
        }
    }
}

function attendanceAttachJustifications(array $rows): array {
    $ids = [];
    foreach ($rows as $r) {
        if (in_array($r['estado'], ['ausente', 'retraso'], true)) $ids[] = (int)$r['idAsistencia'];
    }
    if (!$ids) {
        foreach ($rows as &$r) $r['justificacion'] = null;
        return $rows;
    }
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $st = mysqli_prepare($con, "SELECT * FROM justificaciones_falta WHERE idAsistencia IN ($ph) ORDER BY idJustificacion DESC");
    mysqli_stmt_bind_param($st, $types, ...$ids);
    mysqli_stmt_execute($st);
    $byAsistencia = [];
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $j) {
        $aid = (int)$j['idAsistencia'];
        if (!isset($byAsistencia[$aid])) {
            if (!empty($j['archivo'])) $j['archivo_url'] = justificanteUrl($j['archivo']);
            $byAsistencia[$aid] = $j;
        }
    }
    foreach ($rows as &$r) {
        $r['justificacion'] = $byAsistencia[(int)$r['idAsistencia']] ?? null;
    }
    return $rows;
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'resolve' && $type === 'profesor') {
        $pending = listarJustificacionesPendientesPorProfesor($uid);
        foreach ($pending as &$j) {
            if (!empty($j['archivo'])) $j['archivo_url'] = justificanteUrl($j['archivo']);
        }
        unset($j);
        v1Ok(['pending' => $pending]);
    }

    if ($type === 'estudiante') {
        $rows = listarAsistenciasFiltradas(null, null, $uid, null, null, null);
        v1Ok(['attendance' => attendanceAttachJustifications($rows)]);
    }

    if ($type === 'tutor') {
        $hijos = listarEstudiantesPorTutor($uid);
        $rows = [];
        foreach ($hijos as $h) {
            $rows = array_merge($rows, listarAsistenciasFiltradas(null, null, (int)$h['idEstudiante'], null, null, null));
        }
        v1Ok(['attendance' => attendanceAttachJustifications($rows)]);
    }

    if ($type === 'profesor') {
        $idModulo = (int)($_GET['idModulo'] ?? 0);
        if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
        $misModulos = listarModulosDeProfesor($uid);
        if (!in_array($idModulo, array_column($misModulos, 'idModulo'), true)) {
            v1Error('You do not teach this module.', 403, 'forbidden');
        }
        $fecha = $_GET['fecha'] ?? null;
        if ($fecha !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $fecha = null;
        $rows = listarAsistenciasFiltradas(null, $idModulo, null, $fecha, $fecha, null);
        v1Ok([
            'attendance' => attendanceAttachJustifications($rows),
            'roster' => listarEstudiantesDeModulo($idModulo),
        ]);
    }

    if ($type === 'secretaria' || $type === 'director') {
        $idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
        if ($idEstudiante > 0) {
            $rows = listarAsistenciasFiltradas(null, null, $idEstudiante, null, null, null);
            v1Ok(['attendance' => attendanceAttachJustifications($rows)]);
        } else {
            $filtros = [
                'nivel' => (int)($_GET['nivel'] ?? 0),
                'idCiclo' => (int)($_GET['ciclo'] ?? 0),
                'anio' => trim($_GET['anio'] ?? ''),
                'grupo' => (int)($_GET['grupo'] ?? 0),
                'q' => trim($_GET['q'] ?? ''),
                'estado' => trim($_GET['estado'] ?? ''),
                'fechaDesde' => trim($_GET['fechaDesde'] ?? ''),
                'fechaHasta' => trim($_GET['fechaHasta'] ?? '')
            ];
            $limit = min(max((int)($_GET['limit'] ?? 20), 1), 100);
            $offset = max((int)($_GET['offset'] ?? 0), 0);
            
            $result = listarAsistenciasFiltradasV2($filtros, $limit, $offset);
            v1Ok([
                'attendance' => attendanceAttachJustifications($result['rows']),
                'total' => $result['total'],
                'limit' => $limit,
                'offset' => $offset
            ]);
        }
    }

    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if (!$action) {
        $body = v1Body();
        $action = $body['action'] ?? '';
    }
    
    // Alternativa según la forma del payload si no se especifica ninguna acción
    if (!$action) {
        if (isset($_POST['idAsistencia']) || isset($body['idAsistencia'])) $action = 'justify';
        elseif (isset($_POST['idJustificacion']) || isset($body['idJustificacion'])) $action = 'resolve';
        else $action = 'mark';
    }

    if ($action === 'justify') {
        $rolesPermitidos = ['profesor', 'secretaria', 'director', 'estudiante', 'tutor'];
        if (!in_array($type, $rolesPermitidos, true)) v1Error('Este rol no puede justificar faltas.', 403, 'forbidden');
        
        $esStaff = in_array($type, ['profesor', 'secretaria', 'director']);
        
        $idAsistencia = (int)($_POST['idAsistencia'] ?? 0);
        $motivo = trim((string)($_POST['motivo'] ?? ''));
        if ($idAsistencia <= 0 || $motivo === '') v1Error('idAsistencia and motivo are required.', 400, 'validation');
        
        $con = obtenerConexion();
        $st = mysqli_prepare($con, 'SELECT idEstudiante, idModulo, estado FROM asistencias WHERE idAsistencia = ?');
        mysqli_stmt_bind_param($st, 'i', $idAsistencia);
        mysqli_stmt_execute($st);
        $asistencia = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        if (!$asistencia) v1Error('Attendance record not found.', 404, 'not_found');
        $idEstudiante = (int)$asistencia['idEstudiante'];
        
        if ($type === 'profesor') {
            $stEst = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
            mysqli_stmt_bind_param($stEst, 'i', $idEstudiante);
            mysqli_stmt_execute($stEst);
            $estData = mysqli_fetch_assoc(mysqli_stmt_get_result($stEst));
            $idCicloEstudiante = $estData ? (int)$estData['idCiclo'] : 0;
        
            $stTutor = mysqli_prepare($con, 'SELECT esTutor, idCicloTutor FROM profesores WHERE idProfesor = ?');
            mysqli_stmt_bind_param($stTutor, 'i', $uid);
            mysqli_stmt_execute($stTutor);
            $tutorInfo = mysqli_fetch_assoc(mysqli_stmt_get_result($stTutor));
        
            $esTutor = $tutorInfo && (int)$tutorInfo['esTutor'] === 1;
            $idCicloTutor = $tutorInfo ? (int)$tutorInfo['idCicloTutor'] : 0;
        
            if (!$esTutor) v1Error('Solo los profesores tutores pueden justificar faltas.', 403, 'forbidden');
            if ($idCicloTutor !== $idCicloEstudiante) v1Error('Solo puedes justificar faltas de alumnos de tu tutoría.', 403, 'forbidden');
        } elseif ($type === 'estudiante') {
            if ($idEstudiante !== $uid) v1Error('You can only justify your own attendance.', 403, 'forbidden');
        } elseif ($type === 'tutor') {
            $hijos = listarEstudiantesPorTutor($uid);
            if (!in_array($idEstudiante, array_column($hijos, 'idEstudiante'))) v1Error('You can only justify for your linked children.', 403, 'forbidden');
        }
        
        if (!in_array($asistencia['estado'], ['ausente', 'retraso'], true)) v1Error('This record cannot be justified.', 400, 'validation');
        $existente = obtenerJustificacionPorAsistencia($idAsistencia);
        if ($existente && $existente['estado'] !== 'rechazada') v1Error('A justification already exists.', 409, 'validation');
        
        $archivo = null;
        if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['archivo'];
            if ($file['size'] > 8 * 1024 * 1024) v1Error('File too large.', 400, 'validation');
            $mime = mime_content_type($file['tmp_name']);
            $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!isset($mimeExtMap[$mime])) v1Error('Unsupported file type.', 400, 'validation');
            require_once __DIR__ . '/../../include/ImageOptimizer.php';
            if (in_array($mime, ['image/jpeg', 'image/png'], true)) ImageOptimizer::optimize($file['tmp_name'], $mime);
            $archivo = 'just_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
            $bytes = file_get_contents($file['tmp_name']);
            if (R2Client::putObject('justificantes/' . $archivo, $bytes, $mime)) @unlink($file['tmp_name']);
            else v1Error('Upload failed.', 500, 'error');
        }
        
        $idJustificacion = crearJustificacionFalta($idAsistencia, $idEstudiante, $motivo, $archivo, $asistencia['estado'], $type, $esStaff ? $uid : null);
        if ($idJustificacion === false) v1Error('Submit failed.', 500, 'error');
        
        if ($esStaff) {
            resolverJustificacionFalta($idJustificacion, $idAsistencia, true, $uid, '', $asistencia['estado'], $type);
            notificarJustificacionResuelta($idEstudiante, $idAsistencia, true, '');
            v1Ok(['message' => 'Justification submitted and approved.'], 201);
        }
        v1Ok(['message' => 'Justification submitted.'], 201);
    }

    if ($action === 'resolve') {
        if ($type !== 'profesor') v1Error('Only profesores can resolve justifications.', 403, 'forbidden');
        $body = v1Body();
        $idJustificacion = (int)($body['idJustificacion'] ?? 0);
        $aprobar = $body['aprobar'] ?? null;
        $motivoRechazo = trim((string)($body['motivoRechazo'] ?? ''));

        if ($idJustificacion <= 0 || !is_bool($aprobar)) v1Error('Invalid args.', 400, 'validation');
        if (!$aprobar && $motivoRechazo === '') v1Error('motivoRechazo required.', 400, 'validation');

        $justificacion = justificacionPerteneceAProfesor($idJustificacion, $uid);
        if (!$justificacion) v1Error('Not found.', 404, 'not_found');
        if ($justificacion['estado'] !== 'pendiente') v1Error('Already resolved.', 409, 'validation');

        if (!resolverJustificacionFalta($idJustificacion, (int)$justificacion['idAsistencia'], $aprobar, $uid, $motivoRechazo, $justificacion['estadoOriginal'])) {
            v1Error('Resolution failed.', 500, 'error');
        }
        notificarJustificacionResuelta((int)$justificacion['idEstudiante'], (int)$justificacion['idAsistencia'], $aprobar, $motivoRechazo);
        v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
    }

    if ($action === 'mark') {
        if ($type !== 'profesor') v1Error('Only profesores can register attendance.', 403, 'forbidden');
        $body = v1Body();
        $idModulo = (int)($body['idModulo'] ?? 0);
        $fecha = (string)($body['fecha'] ?? '');
        $registros = $body['registros'] ?? null;

        if ($idModulo <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !is_array($registros)) v1Error('Invalid args.', 400, 'validation');

        $dateTime = DateTime::createFromFormat('Y-m-d', $fecha);
        if ($dateTime && $dateTime->format('N') > 5) v1Error('No weekends.', 400, 'weekend_blocked');

        $misModulos = listarModulosDeProfesor($uid);
        if (!in_array($idModulo, array_column($misModulos, 'idModulo'), true)) v1Error('Forbidden module.', 403, 'forbidden');

        if (!guardarAsistenciasMasivo($idModulo, $uid, $fecha, $registros)) v1Error('Save failed.', 500, 'error');
        v1Ok(['message' => 'Attendance saved.']);
    }

    v1Error('Action not supported.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
