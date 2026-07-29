<?php
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../include/Security.php';
require_once __DIR__ . '/../../../include/MfaService.php';
// Solicitud de eliminación de datos (Art. 17) — disponible para los 5 roles.
// Nunca borra nada: crea una solicitud pendiente que revisa un admin manualmente.
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header('Location: ' . $actor['home']);
    exit;
}

require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/rgpd.php';
require_once __DIR__ . '/../../../modelos/' . $actor['modelo'];

$motivo = trim($_POST['motivo'] ?? '');
if ($motivo === '') {
    $_SESSION['errores'] = 'Indica el motivo de tu solicitud.';
    header('Location: ' . $actor['home']);
    exit;
}

$usuario = ($actor['getFn'])($actor['id']);
$campoNombre = ['idAdmin' => 'nombreDirector', 'idProfesor' => 'nombreProfesor', 'idSecretaria' => 'nombreSecretaria',
                'idEstudiante' => 'nombreEstudiante', 'idTutor' => 'nombreTutor'][$actor['sessionKey']];
$nombre = $usuario[$campoNombre] ?? ('Usuario #' . $actor['id']);
$email  = $usuario[$actor['emailField']] ?? '';

if (crearSolicitudRGPD($actor['sessionKey'], $actor['id'], $nombre, $email, $motivo)) {
    $_SESSION['exito'] = 'Tu solicitud se ha enviado. El centro se pondrá en contacto contigo.';
} else {
    $_SESSION['errores'] = 'No se pudo enviar la solicitud. Inténtalo de nuevo.';
}
header('Location: ' . $actor['home']);
exit;
