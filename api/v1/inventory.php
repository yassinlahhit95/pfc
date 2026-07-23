<?php
declare(strict_types=1);

// Inventario/préstamos — director/secretaria only.
// GET  /api/v1/inventory.php?action=devices — all dispositivos + estado
// GET  /api/v1/inventory.php?action=loans   — all préstamos (historial + en curso)
// POST /api/v1/inventory.php {action:'prestar', idArticulo, idEstudiante}
// POST /api/v1/inventory.php {action:'devolver', idPrestamo}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/inventario.php';

$auth = v1Auth();
['user_type' => $type] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'devices') v1Ok(['devices' => listarArticulos()]);
    if ($action === 'loans') v1Ok(['loans' => listarTodosLosPrestamos()]);
    v1Error('Unknown action.', 400, 'validation');
}

if ($method === 'POST') {
    $body = v1Body();
    $action = $body['action'] ?? '';

    if ($action === 'prestar') {
        $idArticulo   = (int)($body['idArticulo'] ?? 0);
        $idEstudiante = (int)($body['idEstudiante'] ?? 0);
        if ($idArticulo <= 0 || $idEstudiante <= 0) {
            v1Error('idArticulo and idEstudiante are required.', 400, 'validation');
        }
        $articulo = obtenerArticuloPorId($idArticulo);
        if (!$articulo) v1Error('Device not found.', 404, 'not_found');
        if ($articulo['estado'] !== 'disponible') {
            v1Error('This device is not available.', 409, 'validation');
        }
        $ok = registrarPrestamo($idEstudiante, $idArticulo, date('Y-m-d'));
        if (!$ok) v1Error('Could not register the loan.', 500, 'error');
        v1Ok(['message' => 'Loan registered.'], 201);
    }

    if ($action === 'devolver') {
        $idPrestamo = (int)($body['idPrestamo'] ?? 0);
        if ($idPrestamo <= 0) v1Error('idPrestamo is required.', 400, 'validation');
        $ok = devolverPrestamo($idPrestamo);
        if (!$ok) v1Error('Could not register the return.', 500, 'error');
        v1Ok(['message' => 'Return registered.']);
    }

    v1Error('Unknown action.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
