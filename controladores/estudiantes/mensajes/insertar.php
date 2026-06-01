<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../../modelos/directores.php";
require_once "../../firebase/firebase_helper.php";
require_once __DIR__ . "/../../../include/Security.php";

if (empty($_SESSION['idEstudiante'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['enviarMensaje'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada. Recarga la página e inténtalo de nuevo.";
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php"); exit;
    }
    $idEstudiante = $_SESSION['idEstudiante']; // SIEMPRE el de la sesión (no falsificable)
    $idProfesor = trim($_POST['idProfesor'] ?? '');
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $errores = '';

    if (empty($asunto)) $errores = "El asunto es obligatorio.";
    if (empty($descripcion)) $errores = "El mensaje es obligatorio.";
    else if (strlen($descripcion) > 250) $errores = "Máximo 250 caracteres.";

    if ($errores) {
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

        $_SESSION['exito'] = "Mensaje enviado.";
        header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
        exit;
    } else {
        $_SESSION['errores'] = "Error al guardar el mensaje.";
        $_SESSION['datos_mensaje'] = $_POST;
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
?>
