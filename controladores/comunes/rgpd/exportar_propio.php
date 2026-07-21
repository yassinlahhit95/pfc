<?php
// Exportación RGPD Art. 20 de los propios datos — disponible para los 5 roles.
// Cada rol pasa primero por SU guard real (CSRF, suspensión, etc.) antes de
// tocar la lógica compartida de abajo.
require_once __DIR__ . '/../../../include/Security.php';
require_once __DIR__ . '/../../../include/MfaService.php';
Security::initSession();

$actor = MfaService::sesionActual();
if (!$actor) {
    header('Location: ../../../vistas/login.php');
    exit;
}

$guardPorRol = [
    'idAdmin'      => 'AdminGuard.php',
    'idProfesor'   => 'ProfesorGuard.php',
    'idSecretaria' => 'SecretariaGuard.php',
    'idEstudiante' => 'EstudianteGuard.php',
    'idTutor'      => 'TutorGuard.php',
];
require_once __DIR__ . '/../../../include/' . $guardPorRol[$actor['sessionKey']];

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header('Location: ' . $actor['home']);
    exit;
}

require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/rgpd.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($actor['sessionKey'] === 'idEstudiante') {
    $datos = exportarDatosEstudiante($actor['id']);
} else {
    $datos = exportarDatosPropios($actor['tabla'], $actor['idCol'], $actor['id']);
}

if (empty($datos)) {
    $_SESSION['errores'] = 'No se pudieron obtener tus datos.';
    header('Location: ' . $actor['home']);
    exit;
}

if (function_exists('registrarAccion') && $actor['sessionKey'] === 'idAdmin') {
    registrarAccion('rgpd_exportar_propio', $actor['tabla'], $actor['id'], 'Autoexportación RGPD Art.20');
} elseif (function_exists('registrarAccionSecretaria') && $actor['sessionKey'] === 'idSecretaria') {
    registrarAccionSecretaria('rgpd_exportar_propio', $actor['tabla'], $actor['id'], 'Autoexportación RGPD Art.20');
}

$campoNombre = ['idAdmin' => 'nombreDirector', 'idProfesor' => 'nombreProfesor', 'idSecretaria' => 'nombreSecretaria',
                'idEstudiante' => 'nombreEstudiante', 'idTutor' => 'nombreTutor'][$actor['sessionKey']];
$nombre = preg_replace('/[^a-z0-9_]/i', '_', $datos['perfil'][$campoNombre] ?? ('usuario_' . $actor['id']));
$filename = "rgpd_mis_datos_{$nombre}_" . date('Ymd') . ".json";

header('Content-Type: application/json; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store');
echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
exit;
