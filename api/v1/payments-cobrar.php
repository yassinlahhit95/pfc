<?php
declare(strict_types=1);

// POST /api/v1/payments-cobrar.php (multipart/form-data) — director or secretaria
// records a paid transaction for a student, optionally capturing/uploading a photo
// of the payment receipt. Body fields: idEstudiante, monto, tipoPago, fechaProximoPago (optional),
// archivo (optional file, PDF/JPG/PNG, max 8MB).

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_pagos');

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('Only director and secretaria can record payments.', 403, 'forbidden');
}

$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$monto = floatval($_POST['monto'] ?? 0);
$tipoPago = trim((string)($_POST['tipoPago'] ?? ''));
$fechaProximoPago = !empty($_POST['fechaProximoPago']) ? trim((string)$_POST['fechaProximoPago']) : null;

if ($idEstudiante <= 0 || $monto <= 0 || $tipoPago === '') {
    v1Error('idEstudiante, monto, and tipoPago are required and must be valid.', 400, 'validation');
}

$tiposPermitidos = ['mensual', 'trimestral', 'semestral', 'unico'];
if (!in_array($tipoPago, $tiposPermitidos, true)) {
    v1Error('Invalid tipoPago. Must be mensual, trimestral, semestral, or unico.', 400, 'validation');
}

// Check student exists
$con = obtenerConexion();
$stEst = mysqli_prepare($con, 'SELECT idEstudiante FROM estudiantes WHERE idEstudiante = ? AND eliminado = 0');
mysqli_stmt_bind_param($stEst, 'i', $idEstudiante);
mysqli_stmt_execute($stEst);
if (!mysqli_fetch_assoc(mysqli_stmt_get_result($stEst))) {
    v1Error('Student not found.', 404, 'not_found');
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
    $archivo = 'comp_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
    $bytes   = file_get_contents($file['tmp_name']);
    $subioOk = $bytes !== false && R2Client::putObject('comprobantes/' . $archivo, $bytes, $mime);
    @unlink($file['tmp_name']);
    if (!$subioOk) {
        v1Error('Could not upload the file.', 500, 'error');
    }
}

$fechaPago = date('Y-m-d');
if (registrarCobroPago($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximoPago, $archivo)) {
    v1Ok(['message' => 'Payment registered successfully.'], 201);
}

v1Error('Could not record the payment.', 500, 'error');
