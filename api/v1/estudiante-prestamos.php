<?php
declare(strict_types=1);

// GET /api/v1/estudiante-prestamos.php?idEstudiante=X
// Retorna préstamos activos de un estudiante

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$usuario = v1Auth();
v1RequireFeature('feature_inventario');

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
if (!$idEstudiante) {
    v1Error('idEstudiante requerido', 400, 'invalid_request');
}

require_once __DIR__ . '/../../modelos/conectar.php';

$con = obtenerConexion();
$sql = "
    SELECT p.idPrestamo, p.idDispositivo, d.nombreDispositivo, p.fechaPrestamo
    FROM prestamos p
    JOIN dispositivos d ON p.idDispositivo = d.idDispositivo
    WHERE p.idEstudiante = ? AND p.estadoPrestamo = 'en curso' AND p.deleted_at IS NULL
    ORDER BY p.fechaPrestamo DESC
";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
mysqli_stmt_execute($stmt);
$prestamos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

v1Ok([
    'activos' => count($prestamos),
    'prestamos' => array_map(fn($p) => [
        'idPrestamo' => (int)$p['idPrestamo'],
        'idDispositivo' => (int)$p['idDispositivo'],
        'nombreDispositivo' => $p['nombreDispositivo'],
        'fechaPrestamo' => $p['fechaPrestamo'],
    ], $prestamos),
]);
