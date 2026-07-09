<?php
require_once __DIR__ . "/../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../modelos/conectar.php";

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

$idPago = intval($_POST['idPago'] ?? 0);

if ($idPago <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

try {
    $db = obtenerConexion();
    // Otorgar 7 días desde HOY
    $sql = "UPDATE pagos SET prorrogaHasta = DATE_ADD(CURDATE(), INTERVAL 7 DAY) WHERE idPago = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    mysqli_stmt_execute($stmt);
    
    echo json_encode(['ok' => true, 'msg' => 'Prórroga otorgada']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error en base de datos.']);
}
