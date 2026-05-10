<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $idProfesor = trim($_POST['idProfesor']);
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $errores = [];

    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Debe seleccionar un destinatario.";
    }
    if (empty($asunto)) {
        $errores['asunto'] = "El asunto es obligatorio.";
    }
    if (empty($descripcion)) {
        $errores['descripcion'] = "El mensaje no puede estar vacío.";
    } else if (strlen($descripcion) > 250) {
        $errores['descripcion'] = "Máximo 250 caracteres.";
    }

    if (empty($errores)) {
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
            $_SESSION['error'] = "Error interno al guardar.";
        }
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_mensaje'] = $_POST;
    }

    $urlRedireccion = "../../../vistas/profesores/mensajes/agregar.php";
    if (isset($_GET['idCiclo'])) {
        $urlRedireccion = $urlRedireccion . "?idCiclo=" . trim($_GET['idCiclo']);
    }
    header("Location: " . $urlRedireccion);
    exit;
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
?>
