<?php
declare(strict_types=1);

// POST /api/v1/payments-comprobante.php (multipart/form-data) — estudiante or
// tutor (scoped to their own/linked child's payment) uploads a proof-of-payment
// receipt for an existing pago. Body fields: idPago, archivo (file, required,
// PDF/JPG/PNG, max 8MB). Mirrors attendance-justify.php's exact upload pattern
// (server-side MIME detection, ImageOptimizer for images, random filename),
// applied to the comprobante flow that vistas/estudiantes/pagos_pendientes.php
// and vistas/tutores/pagos/misPagos.php already do on web.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';
require_once __DIR__ . '/../../modelos/tutores.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_pagos');

if ($type !== 'estudiante' && $type !== 'tutor') {
    v1Error('Only estudiantes and tutores can upload a comprobante.', 403, 'forbidden');
}

$idPago = (int)($_POST['idPago'] ?? 0);
if ($idPago <= 0) {
    v1Error('idPago is required.', 400, 'validation');
}

$pago = obtenerPagoPorId($idPago);
if (!$pago) v1Error('Payment not found.', 404, 'not_found');
$idEstudiante = (int)$pago['idEstudiante'];

if ($type === 'estudiante') {
    if ($idEstudiante !== $uid) v1Error('You do not have access to this payment.', 403, 'forbidden');
} else {
    $hijos  = listarEstudiantesPorTutor($uid);
    $esHijo = in_array($idEstudiante, array_map(fn($h) => (int)$h['idEstudiante'], $hijos), true);
    if (!$esHijo) v1Error('You do not have access to this student.', 403, 'forbidden');
}

if (empty($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    v1Error('archivo is required.', 400, 'validation');
}

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
$archivo = 'comp_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
$bytes   = file_get_contents($file['tmp_name']);
$subioOk = $bytes !== false && R2Client::putObject('comprobantes/' . $archivo, $bytes, $mime);
@unlink($file['tmp_name']);
if (!$subioOk) {
    v1Error('Could not upload the file.', 500, 'error');
}

if (!subirComprobantePago($idPago, $archivo)) {
    v1Error('Could not save the comprobante.', 500, 'error');
}
v1Ok(['message' => 'Comprobante uploaded. Pending verification.'], 201);
