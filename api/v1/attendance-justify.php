<?php
declare(strict_types=1);

// POST /api/v1/attendance-justify.php (multipart/form-data) — estudiante or
// tutor (scoped to their own linked children) justifies an ausente/retraso.
// Body fields: idAsistencia, motivo, archivo (optional file, PDF/JPG/PNG, max 8MB).
// Mirrors controladores/{estudiantes,tutores}/asistencias/justificar.php's
// exact validation rules and upload pattern (server-side MIME detection,
// random filename, R2 key `justificantes/just_<idEstudiante>_<random>.<ext>`).

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/justificacionesFalta.php';
require_once __DIR__ . '/../../modelos/tutores.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'estudiante' && $type !== 'tutor') {
    v1Error('Only estudiantes and tutores can submit justifications.', 403, 'forbidden');
}

$idAsistencia = (int)($_POST['idAsistencia'] ?? 0);
$motivo       = trim((string)($_POST['motivo'] ?? ''));
if ($idAsistencia <= 0 || $motivo === '') {
    v1Error('idAsistencia and motivo are required.', 400, 'validation');
}

$con = obtenerConexion();
$st = mysqli_prepare($con, 'SELECT idEstudiante, estado FROM asistencias WHERE idAsistencia = ?');
mysqli_stmt_bind_param($st, 'i', $idAsistencia);
mysqli_stmt_execute($st);
$asistencia = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if (!$asistencia) v1Error('Attendance record not found.', 404, 'not_found');

$idEstudiante = (int)$asistencia['idEstudiante'];

if ($type === 'estudiante') {
    if ($idEstudiante !== $uid) v1Error('You do not have access to this record.', 403, 'forbidden');
} else {
    $hijos  = listarEstudiantesPorTutor($uid);
    $esHijo = in_array($idEstudiante, array_map(fn($h) => (int)$h['idEstudiante'], $hijos), true);
    if (!$esHijo) v1Error('You do not have access to this student.', 403, 'forbidden');
}

if (!in_array($asistencia['estado'], ['ausente', 'retraso'], true)) {
    v1Error('This record cannot be justified.', 400, 'validation');
}

$existente = obtenerJustificacionPorAsistencia($idAsistencia);
if ($existente && $existente['estado'] !== 'rechazada') {
    v1Error('A justification already exists for this record.', 409, 'validation');
}

$archivo = null;
if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['archivo'];
    if ($file['size'] > 8 * 1024 * 1024) {
        v1Error('File too large (max 8 MB).', 400, 'validation');
    }
    $mime = mime_content_type($file['tmp_name']);
    $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($mimeExtMap[$mime])) {
        v1Error('Unsupported file type. Upload a PDF or an image (JPG/PNG).', 400, 'validation');
    }
    require_once __DIR__ . '/../../include/ImageOptimizer.php';
    require_once __DIR__ . '/../../include/R2Client.php';
    if (in_array($mime, ['image/jpeg', 'image/png'], true)) {
        ImageOptimizer::optimize($file['tmp_name'], $mime);
    }
    $archivo = 'just_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
    $bytes   = file_get_contents($file['tmp_name']);
    $subioOk = $bytes !== false && R2Client::putObject('justificantes/' . $archivo, $bytes, $mime);
    @unlink($file['tmp_name']);
    if (!$subioOk) {
        v1Error('Could not upload the file.', 500, 'error');
    }
}

if (!crearJustificacionFalta($idAsistencia, $idEstudiante, $motivo, $archivo, $asistencia['estado'])) {
    v1Error('Could not submit the justification.', 500, 'error');
}
v1Ok(['message' => 'Justification submitted.'], 201);
