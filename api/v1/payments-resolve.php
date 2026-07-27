<?php
declare(strict_types=1);

// POST /api/v1/payments-resolve.php (director/secretaria only)
//   { idPago, aprobar: bool, motivoRechazo? } — motivoRechazo required when
//   aprobar is false. Approves/rejects a comprobante uploaded by a student/
//   tutor (payments-comprobante.php) — the step that was previously missing
//   entirely: estadoComprobante used to get stuck on 'verificando' forever.
//   Sends a push to the student and any linked tutores.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';
require_once __DIR__ . '/../../modelos/log.php';
require_once __DIR__ . '/_payments_shared.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_pagos');

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('Only director/secretaria can resolve a comprobante.', 403, 'forbidden');
}

$body = v1Body();
$idPago = (int)($body['idPago'] ?? 0);
$aprobar = $body['aprobar'] ?? null;
$motivoRechazo = trim((string)($body['motivoRechazo'] ?? ''));

if ($idPago <= 0 || !is_bool($aprobar)) {
    v1Error('idPago and aprobar are required.', 400, 'validation');
}
if (!$aprobar && $motivoRechazo === '') {
    v1Error('motivoRechazo is required when rejecting.', 400, 'validation');
}

$pago = obtenerPagoPorId($idPago);
if (!$pago) v1Error('Payment not found.', 404, 'not_found');
if ($pago['estadoComprobante'] !== 'verificando') {
    v1Error('This comprobante has already been resolved or none was uploaded.', 409, 'validation');
}

$ok = resolverComprobantePago($idPago, $aprobar, $aprobar ? null : $motivoRechazo);
if (!$ok) v1Error('Could not resolve the comprobante.', 500, 'error');

$accion = $aprobar ? 'aprobar_comprobante' : 'rechazar_comprobante';
if ($type === 'director') {
    registrarAccion($accion, 'pagos', $idPago, $motivoRechazo, $uid);
} else {
    registrarAccionSecretaria($accion, 'pagos', $idPago, $motivoRechazo, $uid);
}

notificarComprobantePagoResuelto((int)$pago['idEstudiante'], $idPago, $aprobar, $motivoRechazo);

v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
