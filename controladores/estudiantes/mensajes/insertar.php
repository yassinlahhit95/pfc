<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['enviarMensaje'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php"); exit;
    }
    $idEstudiante = $_SESSION['idEstudiante']; // Siempre de la sesión (no falsificable)
    $idProfesor   = (int)($_POST['idProfesor'] ?? 0);
    $asunto       = trim($_POST['asunto']);
    $descripcion  = trim($_POST['descripcion']);
    $errores = [];

    if (empty($asunto)) $errores['asunto'] = "El asunto del mensaje es un campo obligatorio.";
    if (empty($descripcion)) $errores['descripcion'] = "El contenido del mensaje es un campo obligatorio.";
    elseif (strlen($descripcion) > 250) $errores['descripcion'] = "El mensaje no puede superar los 250 caracteres.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_mensaje'] = $_POST;
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
        exit;
    }

    $resultado = insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, 'estudiante');

    if ($resultado) {
        if (!empty($idProfesor)) {
            $tokenProfesor = obtenerTokenUsuario($idProfesor, "profesor");
            if ($tokenProfesor) enviarNotificacionFirebase($tokenProfesor, "Mensaje de Estudiante: $asunto", $descripcion);
        } else {
            $tokensDirectores = obtenerTokensDirectores();
            foreach ($tokensDirectores as $token) enviarNotificacionFirebase($token, "Mensaje de Estudiante a Dirección: $asunto", $descripcion);
        }

        $_SESSION['exito'] = "El mensaje ha sido enviado correctamente.";
        header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
        exit;
    } else {
        $_SESSION['errores'] = "No se pudo guardar el mensaje. Inténtalo de nuevo.";
        $_SESSION['datos_mensaje'] = $_POST;
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
        exit;
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
