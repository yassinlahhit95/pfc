<?php
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . "/../../../include/Security.php";
Security::initSession();
if (empty($_SESSION['idSecretaria'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}
require_once __DIR__ . "/../../../modelos/conectar.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? 0);
if (!$idEstudiante) {
    echo json_encode([]);
    exit;
}

$con = obtenerConexion();
$stmt = mysqli_prepare($con, "
    SELECT a.idAsistencia, a.fecha, a.estado, m.nombreModulo
    FROM asistencias a
    JOIN modulos m ON m.idModulo = a.idModulo
    WHERE a.idEstudiante = ?
      AND a.estado IN ('ausente', 'retraso')
      AND NOT EXISTS (
          SELECT 1 FROM justificaciones_falta jf
          WHERE jf.idAsistencia = a.idAsistencia
            AND jf.estado IN ('pendiente', 'aprobada')
      )
    ORDER BY a.fecha DESC
");
mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$out = [];
while ($row = mysqli_fetch_assoc($res)) {
    $out[] = $row;
}
header('Content-Type: application/json');
echo json_encode($out);
