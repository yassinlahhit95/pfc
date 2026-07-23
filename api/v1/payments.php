<?php
declare(strict_types=1);

// GET /api/v1/payments.php — director/secretaria only (read-only for now;
// registering/editing payments stays web-only in this first mobile pass).
//   ?idCiclo=  — filter by ciclo (optional)
//   ?pending=1 — students with outstanding balance instead of the payment list

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/pagos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if (isset($_GET['pending'])) {
    v1Ok(['pending' => listarEstudiantesConPagosPendientes()]);
}

$idCiclo = isset($_GET['idCiclo']) ? (int)$_GET['idCiclo'] : null;
$payments = $idCiclo ? listarPagosFiltrados($idCiclo) : listarTodosLosPagos();
v1Ok(['payments' => $payments]);
