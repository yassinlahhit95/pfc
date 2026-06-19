<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (!isset($_POST['enviarMensaje'])) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/mensajes/agregar.php"); exit;
}

$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$idProfesor   = $_SESSION['idProfesor']; // SIEMPRE el de la sesión (no falsificable)
$asunto       = trim($_POST['asunto']);
$descripcion  = trim($_POST['descripcion']);
$errores      = '';

if (empty($idEstudiante)) $errores = "Debe seleccionar un destinatario.";
if (empty($asunto))       $errores = "El asunto es obligatorio.";
if (empty($descripcion))  $errores = "El mensaje no puede estar vacío.";
else if (strlen($descripcion) > 250) $errores = "Máximo 250 caracteres.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_mensaje'] = $_POST;
    $urlRedireccion = "../../../vistas/profesores/mensajes/agregar.php";
    if (isset($_GET['idCiclo'])) {
        $urlRedireccion .= "?idCiclo=" . trim($_GET['idCiclo']);
    }
    header("Location: " . $urlRedireccion);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$resultado = insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, 'profesor');

if ($resultado) {
    $tokenEstudiante = obtenerTokenUsuario($idEstudiante, "estudiante");
    if ($tokenEstudiante) {
        enviarNotificacionFirebase($tokenEstudiante, "Mensaje de Profesor: " . $asunto, $descripcion);
    }

    $_SESSION['exito'] = "Mensaje enviado correctamente.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
} else {
    $_SESSION['errores'] = "No se pudo enviar el mensaje. Inténtalo de nuevo.";
    $_SESSION['datos_mensaje'] = $_POST;
}

$urlRedireccion = "../../../vistas/profesores/mensajes/agregar.php";
if (isset($_GET['idCiclo'])) {
    $urlRedireccion .= "?idCiclo=" . trim($_GET['idCiclo']);
}
header("Location: " . $urlRedireccion);
exit;
