<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

$hayError = false;
$errores = [];

if (isset($_POST['enviarMensaje'])) {
    $idEstudianteDestino = isset($_POST['idEstudiante']) ? trim($_POST['idEstudiante']) : null;
    $idProfesorDestino = isset($_POST['idProfesor']) ? trim($_POST['idProfesor']) : null;
    $rolEmisorMensaje = isset($_POST['emisor_rol']) ? trim($_POST['emisor_rol']) : 'admin';
    $asuntoMensaje = trim($_POST['asunto']);
    $descripcionMensaje = trim($_POST['descripcion']);
    $fechaDeEnvio = date('Y-m-d');

    // Guardamos datos para repoblar el formulario
    $_SESSION['datos_mensaje'] = $_POST;

    if (empty($idEstudianteDestino) && empty($idProfesorDestino)) {
        $errores['destinatario'] = "Debe seleccionar un destinatario específico.";
        $hayError = true;
    }

    if (empty($asuntoMensaje)) {
        $errores['asunto'] = "El asunto es obligatorio.";
        $hayError = true;
    }

    if (empty($descripcionMensaje)) {
        $errores['descripcion'] = "El contenido del mensaje no puede estar vacío.";
        $hayError = true;
    }

    if ($hayError) {
        $_SESSION['errores'] = $errores;
        $_SESSION['error'] = "Por favor, corrija los errores en el formulario.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php?tipoDestinatario=" . ($idEstudianteDestino ? 'estudiante' : 'profesor'));
        exit;
    }

    if (insertarNuevoMensaje($idEstudianteDestino, $idProfesorDestino, $asuntoMensaje, $descripcionMensaje, $fechaDeEnvio, $rolEmisorMensaje)) {
        // Notificar al destinatario si existe
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
    } else {
        $_SESSION['error'] = "Ocurrió un error técnico al intentar enviar el mensaje.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
