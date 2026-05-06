<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudianteDestino = $_POST['idEstudiante'] ?? null;
    $idProfesorDestino = $_POST['idProfesor'] ?? null;
    $rolEmisorMensaje = $_POST['emisor_rol'] ?? 'admin';
    $asuntoMensaje = trim($_POST['asunto']);
    $descripcionMensaje = trim($_POST['descripcion']);

    $_SESSION['datos_mensaje'] = $_POST;

    $errores = [];

    if (empty($idEstudianteDestino) && empty($idProfesorDestino)) {
        $errores['destinatario'] = "Debe seleccionar un destinatario específico.";
    }
    if (empty($asuntoMensaje)) {
        $errores['asunto'] = "El asunto es obligatorio.";
    }
    if (empty($descripcionMensaje)) {
        $errores['descripcion'] = "El contenido del mensaje no puede estar vacío.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['error'] = "Por favor, corrija los errores en el formulario.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php?tipoDestinatario=" . ($idEstudianteDestino ? 'estudiante' : 'profesor'));
        exit;
    }

    if (insertarNuevoMensaje($idEstudianteDestino, $idProfesorDestino, $asuntoMensaje, $descripcionMensaje, date('Y-m-d'), $rolEmisorMensaje)) {
        $idDestinatarioFinal = $idEstudianteDestino ?: $idProfesorDestino;
        $rolDestinatarioFinal = $idEstudianteDestino ? 'estudiante' : 'profesor';

        if ($idDestinatarioFinal) {
            $tokenDispositivo = obtenerTokenUsuario($idDestinatarioFinal, $rolDestinatarioFinal);
            if ($tokenDispositivo) {
                enviarNotificacionFirebase($tokenDispositivo, "Nuevo Mensaje: " . $asuntoMensaje, $descripcionMensaje);
            }
        }

        unset($_SESSION['datos_mensaje']);
        $_SESSION['exito'] = "Mensaje oficial enviado con éxito.";
        header("Location: ../../../vistas/admin/mensajes/lista.php");
        exit;
    }

    $_SESSION['error'] = "No se pudo enviar el mensaje.";
    header("Location: ../../../vistas/admin/mensajes/agregar.php");
    exit;
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
