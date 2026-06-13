<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/firebase_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Determinar el usuario actual de la sesión
$id = null;
$rol = null;

if (!empty($_SESSION['idAdmin'])) {
    $id = $_SESSION['idAdmin'];
    $rol = 'admin';
} elseif (!empty($_SESSION['idProfesor'])) {
    $id = $_SESSION['idProfesor'];
    $rol = 'profesor';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $id = $_SESSION['idEstudiante'];
    $rol = 'estudiante';
}

if (!$id) {
    die("Debes estar logueado para probar la notificación.");
}

$token = obtenerTokenUsuario($id, $rol);

if (!$token) {
    die("No se encontró un token de Firebase para tu usuario. Asegúrate de haber aceptado los permisos de notificación en el navegador y haber recargado la página.");
}

$titulo = "¡Prueba de AulaPro!";
$mensaje = "Esta es una notificación premium con el nuevo diseño glassmorphism.";

$resultado = enviarNotificacionFirebase($token, $titulo, $mensaje);

if ($resultado) {
    echo "<h1>Notificación enviada correctamente</h1>";
    echo "<p>Deberías ver un aviso en pantalla en un par de segundos.</p>";
    echo "<p>Respuesta de Firebase: <pre>$resultado</pre></p>";
    echo "<br><a href='../../vistas/admin/inicio/dashboard.php'>Volver al Dashboard</a>";
} else {
    echo "<h1>Error al enviar la notificación</h1>";
    echo "<p>Revisa los logs de error del servidor o el archivo service-account.json.</p>";
}
?>
