<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// No usa Guards de rol: debe ser accesible cuando must_change_password está activo.
Security::initSession();
require_once __DIR__ . '/../../modelos/conectar.php';

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
// Mapa rol → tabla / columna id (lista blanca; nunca viene del usuario)
$map = [
    'idAdmin'      => ['directores',  'idDirector'],
    'idProfesor'   => ['profesores',  'idProfesor'],
    'idEstudiante' => ['estudiantes', 'idEstudiante'],
    'idTutor'      => ['tutores',     'idTutor'],
    'idSecretaria' => ['secretarias', 'idSecretaria'],
];

$sesKey = null;
foreach ($map as $k => $_) {
    if (!empty($_SESSION[$k])) { $sesKey = $k; break; }
}
if ($sesKey === null) {
    header('Location: ../../vistas/login.php');
    exit;
}

[$tabla, $idCol] = $map[$sesKey];
$idUsuario = (int)$_SESSION[$sesKey];

$dashboards = [
    'idAdmin'      => '../../vistas/admin/inicio/dashboard.php',
    'idProfesor'   => '../../vistas/profesores/inicio/dashboard.php',
    'idEstudiante' => '../../vistas/estudiantes/inicio/dashboard.php',
    'idTutor'      => '../../vistas/tutores/inicio/dashboard.php',
    'idSecretaria' => '../../vistas/secretaria/inicio/dashboard.php',
];

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../vistas/cambiar_password.php');
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtalo de nuevo.';
    header('Location: ../../vistas/cambiar_password.php');
    exit;
}

$nueva   = (string)($_POST['nueva'] ?? '');
$confirm = (string)($_POST['confirmar'] ?? '');

if ($nueva !== $confirm) {
    $_SESSION['errores'] = 'Las contraseñas no coinciden.';
    header('Location: ../../vistas/cambiar_password.php');
    exit;
}

$politica = Security::validatePassword($nueva);
if (!$politica['valid']) {
    $_SESSION['errores'] = $politica['error'];
    header('Location: ../../vistas/cambiar_password.php');
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$con  = obtenerConexion();
$hash = Security::hashPassword($nueva);

// $tabla e $idCol provienen de la lista blanca → interpolación segura
$sql  = "UPDATE `$tabla` SET `password` = ?, `must_change_password` = 0 WHERE `$idCol` = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "si", $hash, $idUsuario);

if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['must_change_password']);
    Security::touchPasswordChanged($con, $tabla, $idCol, $idUsuario);
    Security::regenerateSession();
    $_SESSION['_pwd_at'] = time() + 10;
    $_SESSION['_pwd_check'] = time();
    if (class_exists('Logger')) {
        Logger::activity('PASSWORD_CHANGED', $idUsuario, ['tabla' => $tabla]);
    }
    $_SESSION['exito'] = 'Contraseña actualizada correctamente.';
    header('Location: ' . $dashboards[$sesKey]);
    exit;
}

$_SESSION['errores'] = 'No se pudo actualizar la contraseña. Inténtalo de nuevo.';
header('Location: ../../vistas/cambiar_password.php');
exit;
