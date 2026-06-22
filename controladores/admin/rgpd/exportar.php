<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/rgpd.php";
require_once __DIR__ . "/../../../modelos/log.php";

$idEstudiante = (int)($_GET['idEstudiante'] ?? $_POST['idEstudiante'] ?? 0);

if ($idEstudiante <= 0) {
    $_SESSION['errores'] = "ID de estudiante no válido.";
    header("Location: ../../../vistas/admin/rgpd/index.php");
    exit;
}

$datos = exportarDatosEstudiante($idEstudiante);

if (empty($datos)) {
    $_SESSION['errores'] = "Estudiante no encontrado.";
    header("Location: ../../../vistas/admin/rgpd/index.php");
    exit;
}

registrarAccion('rgpd_exportar', 'estudiantes', $idEstudiante, 'Exportación RGPD Art.20');

$nombre = preg_replace('/[^a-z0-9_]/i', '_', $datos['perfil']['nombreEstudiante'] ?? "est_$idEstudiante");
$filename = "rgpd_export_{$nombre}_" . date('Ymd') . ".json";

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store');
echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
