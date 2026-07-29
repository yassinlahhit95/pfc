<?php
declare(strict_types=1);

// Inventario/préstamos — director/secretaria only.
// Device Loans:
//   GET  /api/v1/inventory.php?action=devices        — all dispositivos + estado
//   GET  /api/v1/inventory.php?action=loans          — all préstamos (historial + en curso)
//   POST /api/v1/inventory.php {action:'prestar', idArticulo, idEstudiante}
//   POST /api/v1/inventory.php {action:'devolver', idPrestamo}
//   POST /api/v1/inventory.php {action:'add_device', nombreArticulo, numeroSerie, cantidad, fotoBase64?}
//   POST /api/v1/inventory.php {action:'edit_device', idArticulo, nombreArticulo, numeroSerie, estado, cantidad, fotoBase64?}
//   POST /api/v1/inventory.php {action:'delete_device', idArticulo}

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/inventario.php';

$auth = v1Auth();
['user_type' => $type] = $auth;

if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}
v1RequireFeature('feature_inventario');

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

    // Device loan actions
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
        try {
            $ok = registrarPrestamo($idEstudiante, $idArticulo, date('Y-m-d'));
            if (!$ok) v1Error('Could not register the loan.', 500, 'error');
            v1Ok(['message' => 'Loan registered.'], 201);
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'préstamo activo')) {
                v1Error('Student already has an active loan for this device.', 409, 'conflict');
            }
            if (str_contains($msg, 'no hay stock')) {
                v1Error('No available stock for this device.', 409, 'conflict');
            }
            v1Error('Could not register the loan.', 500, 'error');
        }
    }

    if ($action === 'devolver') {
        $idPrestamo = (int)($body['idPrestamo'] ?? 0);
        if ($idPrestamo <= 0) v1Error('idPrestamo is required.', 400, 'validation');
        $ok = devolverPrestamo($idPrestamo);
        if (!$ok) v1Error('Could not register the return.', 500, 'error');
        v1Ok(['message' => 'Return registered.']);
    }

    if ($action === 'add_device') {
        $nombre = trim($body['nombreArticulo'] ?? '');
        $numeroSerie = trim($body['numeroSerie'] ?? '');
        $cantidad = max(1, (int)($body['cantidad'] ?? 1));
        $fotoBase64 = $body['fotoBase64'] ?? null;
        
        if (empty($nombre) || empty($numeroSerie)) {
            v1Error('nombreArticulo and numeroSerie are required.', 400, 'validation');
        }
        
        $foto = null;
        if ($fotoBase64) {
            $dir = __DIR__ . '/../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $foto = uniqid('dev_') . '.jpg';

            // Validate and decode base64 strictly
            $fotoContenido = base64_decode($fotoBase64, true);
            if ($fotoContenido === false) {
                v1Error('Foto inválida (formato base64 corrupto).', 400, 'validation');
            }

            // Verify file size is reasonable (5MB limit)
            if (strlen($fotoContenido) > 5242880) {
                v1Error('Foto demasiado grande (máx 5MB).', 400, 'validation');
            }

            // Write file and verify success
            if (!file_put_contents($dir . $foto, $fotoContenido)) {
                v1Error('Error al guardar foto.', 500, 'error');
            }
        }

        $ok = insertarArticulo($nombre, $numeroSerie, $cantidad, $foto);
        if (!$ok) v1Error('Could not add device. Maybe serial number exists.', 409, 'conflict');
        v1Ok(['message' => 'Device added.'], 201);
    }

    if ($action === 'edit_device') {
        $idArticulo = (int)($body['idArticulo'] ?? 0);
        $nombre = trim($body['nombreArticulo'] ?? '');
        $numeroSerie = trim($body['numeroSerie'] ?? '');
        $estado = trim($body['estado'] ?? '');
        $cantidad = max(1, (int)($body['cantidad'] ?? 1));
        $fotoBase64 = $body['fotoBase64'] ?? null;
        
        if ($idArticulo <= 0 || empty($nombre) || empty($numeroSerie) || empty($estado)) {
            v1Error('Missing required fields.', 400, 'validation');
        }
        
        $articulo = obtenerArticuloPorId($idArticulo);
        if (!$articulo) v1Error('Device not found.', 404, 'not_found');

        $foto = null;
        if ($fotoBase64) {
            $dir = __DIR__ . '/../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $foto = uniqid('dev_') . '.jpg';

            // Validate and decode base64 strictly
            $fotoContenido = base64_decode($fotoBase64, true);
            if ($fotoContenido === false) {
                v1Error('Foto inválida (formato base64 corrupto).', 400, 'validation');
            }

            // Verify file size is reasonable (5MB limit)
            if (strlen($fotoContenido) > 5242880) {
                v1Error('Foto demasiado grande (máx 5MB).', 400, 'validation');
            }

            // Write file and verify success
            if (!file_put_contents($dir . $foto, $fotoContenido)) {
                v1Error('Error al guardar foto.', 500, 'error');
            }
        }

        // Update DB FIRST, then delete old photo (race condition fix)
        $ok = actualizarArticulo($idArticulo, $nombre, $numeroSerie, $estado, $cantidad, $foto);
        if (!$ok) {
            // Clean up new photo on DB error
            if ($foto && file_exists($dir . $foto)) {
                @unlink($dir . $foto);
            }
            v1Error('Could not update device.', 500, 'error');
        }

        // THEN delete old photo safely
        if (!empty($articulo['foto']) && file_exists($dir . $articulo['foto'])) {
            @unlink($dir . $articulo['foto']);
        }

        v1Ok(['message' => 'Device updated.']);
    }

    if ($action === 'delete_device') {
        $idArticulo = (int)($body['idArticulo'] ?? 0);
        if ($idArticulo <= 0) v1Error('idArticulo is required.', 400, 'validation');
        
        $articulo = obtenerArticuloPorId($idArticulo);
        if (!$articulo) v1Error('Device not found.', 404, 'not_found');

        $ok = eliminarArticulo($idArticulo);
        if (!$ok) v1Error('Could not delete device.', 500, 'error');
        v1Ok(['message' => 'Device deleted.']);
    }

    v1Error('Unknown action.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
