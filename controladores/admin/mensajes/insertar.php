<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviarMensaje'])) {
    $idEstudianteDestino = isset($_POST['idEstudiante']) ? trim($_POST['idEstudiante']) : null;
    $idProfesorDestino = isset($_POST['idProfesor']) ? trim($_POST['idProfesor']) : null;
    $rolEmisorMensaje = isset($_POST['emisor_rol']) ? trim($_POST['emisor_rol']) : 'admin';
    $asuntoMensaje = trim($_POST['asunto']);
    $descripcionMensaje = trim($_POST['descripcion']);
    $fechaDeEnvio = date('Y-m-d');

    if (empty($asuntoMensaje) || empty($descripcionMensaje)) {
        $hayError = true;
        $_SESSION['error'] = "Faltan datos.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php");
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
        $_SESSION['exito'] = "Mensaje enviado.";
        header("Location: ../../../vistas/admin/mensajes/lista.php");
        exit;
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al enviar.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
