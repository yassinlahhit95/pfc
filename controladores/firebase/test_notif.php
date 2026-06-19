<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/firebase_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$id  = null;
$rol = null;

if (!empty($_SESSION['idAdmin'])) {
    $id  = $_SESSION['idAdmin'];
    $rol = 'admin';
} elseif (!empty($_SESSION['idProfesor'])) {
    $id  = $_SESSION['idProfesor'];
    $rol = 'profesor';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $id  = $_SESSION['idEstudiante'];
    $rol = 'estudiante';
}

if (!$id || $rol !== 'admin') {
    http_response_code(403);
    die("Acceso restringido.");
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403);
    die("Acción bloqueada.");
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$token = obtenerTokenUsuario($id, $rol);

if (!$token) {
    die("No se encontró un token de Firebase para tu usuario. Asegúrate de haber aceptado los permisos de notificación en el navegador y haber recargado la página.");
}

$titulo  = "¡Prueba de AulaPro!";
$mensaje = "Esta es una notificación premium con el nuevo diseño glassmorphism.";

$resultado = enviarNotificacionFirebase($token, $titulo, $mensaje);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($resultado) {
    echo "<h1>Notificación enviada correctamente</h1>";
    echo "<p>Deberías ver un aviso en pantalla en un par de segundos.</p>";
    echo "<br><a href='../../vistas/admin/inicio/dashboard.php'>Volver al Dashboard</a>";
} else {
    echo "<h1>Error al enviar la notificación</h1>";
    echo "<p>Consulta los logs del servidor para más detalles.</p>";
}
