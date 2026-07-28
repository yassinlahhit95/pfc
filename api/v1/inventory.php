<?php
declare(strict_types=1);

// Inventario/préstamos — director/secretaria only.
// Device Loans:
//   GET  /api/v1/inventory.php?action=devices        — all dispositivos + estado
//   GET  /api/v1/inventory.php?action=loans          — all préstamos (historial + en curso)
//   POST /api/v1/inventory.php {action:'prestar', idArticulo, idEstudiante}
//   POST /api/v1/inventory.php {action:'devolver', idPrestamo}
// Inventory Items (generic items with quantity):
//   GET  /api/v1/inventory.php?action=items[&limit=X&offset=Y] — paginated items
//   GET  /api/v1/inventory.php?action=item&id=X                — single item
//   POST /api/v1/inventory.php {action:'create_item', nombreArticulo, descripcion?, cantidad?}
//   POST /api/v1/inventory.php {action:'update_item', idInventario, nombreArticulo, descripcion?, cantidad?}
//   POST /api/v1/inventory.php {action:'delete_item', idInventario}

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

    if ($action === 'items') {
        $limit = (int)($_GET['limit'] ?? 100);
        $offset = (int)($_GET['offset'] ?? 0);
        $limit = min($limit, 500); // cap at 500
        v1Ok(['items' => listarInventario($limit, $offset)]);
    }

    if ($action === 'item') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) v1Error('id is required.', 400, 'validation');
        $item = obtenerInventarioPorId($id);
        if (!$item) v1Error('Item not found.', 404, 'not_found');
        v1Ok(['item' => $item]);
    }

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

    if ($action === 'add_device') {
        $nombre = trim($body['nombreArticulo'] ?? '');
        $numeroSerie = trim($body['numeroSerie'] ?? '');
        $fotoBase64 = $body['fotoBase64'] ?? null;
        
        if (empty($nombre) || empty($numeroSerie)) {
            v1Error('nombreArticulo and numeroSerie are required.', 400, 'validation');
        }
        
        $foto = null;
        if ($fotoBase64) {
            $dir = __DIR__ . '/../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = uniqid('dev_') . '.jpg';
            file_put_contents($dir . $foto, base64_decode($fotoBase64));
        }

        $ok = insertarArticulo($nombre, $numeroSerie, $foto);
        if (!$ok) v1Error('Could not add device. Maybe serial number exists.', 409, 'conflict');
        v1Ok(['message' => 'Device added.'], 201);
    }

    if ($action === 'edit_device') {
        $idArticulo = (int)($body['idArticulo'] ?? 0);
        $nombre = trim($body['nombreArticulo'] ?? '');
        $numeroSerie = trim($body['numeroSerie'] ?? '');
        $estado = trim($body['estado'] ?? '');
        $fotoBase64 = $body['fotoBase64'] ?? null;
        
        if ($idArticulo <= 0 || empty($nombre) || empty($numeroSerie) || empty($estado)) {
            v1Error('Missing required fields.', 400, 'validation');
        }
        
        $articulo = obtenerArticuloPorId($idArticulo);
        if (!$articulo) v1Error('Device not found.', 404, 'not_found');

        $foto = null;
        if ($fotoBase64) {
            $dir = __DIR__ . '/../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = uniqid('dev_') . '.jpg';
            file_put_contents($dir . $foto, base64_decode($fotoBase64));
            
            // Delete old photo if it exists
            if (!empty($articulo['foto']) && file_exists($dir . $articulo['foto'])) {
                @unlink($dir . $articulo['foto']);
            }
        }

        $ok = actualizarArticulo($idArticulo, $nombre, $numeroSerie, $estado, $foto);
        if (!$ok) v1Error('Could not update device.', 500, 'error');
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

    // Inventory item actions
    if ($action === 'create_item') {
        $nombre = $body['nombreArticulo'] ?? '';
        $descripcion = $body['descripcion'] ?? null;
        $cantidad = (int)($body['cantidad'] ?? 0);

        if (empty($nombre)) {
            v1Error('nombreArticulo is required.', 400, 'validation');
        }

        $id = crearInventario($nombre, $descripcion, $cantidad);
        if (!$id) v1Error('Could not create item.', 500, 'error');
        v1Ok(['id' => $id, 'message' => 'Item created.'], 201);
    }

    if ($action === 'update_item') {
        $id = (int)($body['idInventario'] ?? 0);
        $nombre = $body['nombreArticulo'] ?? '';
        $descripcion = $body['descripcion'] ?? null;
        $cantidad = isset($body['cantidad']) ? (int)$body['cantidad'] : null;

        if ($id <= 0) v1Error('idInventario is required.', 400, 'validation');
        if (empty($nombre)) v1Error('nombreArticulo is required.', 400, 'validation');

        $item = obtenerInventarioPorId($id);
        if (!$item) v1Error('Item not found.', 404, 'not_found');

        $ok = actualizarInventario($id, $nombre, $descripcion, $cantidad);
        if (!$ok) v1Error('Could not update item.', 500, 'error');
        v1Ok(['message' => 'Item updated.']);
    }

    if ($action === 'delete_item') {
        $id = (int)($body['idInventario'] ?? 0);
        if ($id <= 0) v1Error('idInventario is required.', 400, 'validation');

        $item = obtenerInventarioPorId($id);
        if (!$item) v1Error('Item not found.', 404, 'not_found');

        $ok = eliminarInventario($id);
        if (!$ok) v1Error('Could not delete item.', 500, 'error');
        v1Ok(['message' => 'Item deleted.']);
    }

    v1Error('Unknown action.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
