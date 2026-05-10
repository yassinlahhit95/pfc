<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudianteDestino = '';
    if (!empty($_POST['idEstudiante'])) {
        $idEstudianteDestino = trim($_POST['idEstudiante']);
    }

    $idProfesorDestino = '';
    if (!empty($_POST['idProfesor'])) {
        $idProfesorDestino = trim($_POST['idProfesor']);
    }

    $rolEmisorMensaje = 'admin';
    if (!empty($_POST['emisor_rol'])) {
        $rolEmisorMensaje = trim($_POST['emisor_rol']);
    }

    $tipoDeDestinatario = 'profesor';
    if (!empty($_POST['tipoDestinatario'])) {
        $tipoDeDestinatario = trim($_POST['tipoDestinatario']);
    }

    $idCicloMasivo = '';
    if (!empty($_POST['idCicloMasivo'])) {
        $idCicloMasivo = trim($_POST['idCicloMasivo']);
    }

    $asuntoMensaje = trim($_POST['asunto']);
    $descripcionMensaje = trim($_POST['descripcion']);

    $_SESSION['datos_mensaje'] = $_POST;

    $errores = [];

    $esMensajeMasivo = ($tipoDeDestinatario == 'estudiante' && !empty($idCicloMasivo));
    if (empty($idEstudianteDestino) && empty($idProfesorDestino) && !$esMensajeMasivo) {
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

        $urlRedireccion = "../../../vistas/admin/mensajes/agregar.php?tipoDestinatario=" . $tipoDeDestinatario;
        if (!empty($idCicloMasivo)) {
            $urlRedireccion = $urlRedireccion . "&idCiclo=" . $idCicloMasivo;
        }
        header("Location: " . $urlRedireccion);
        exit;
    }

    if (!empty($idCicloMasivo) && empty($idEstudianteDestino) && $tipoDeDestinatario == 'estudiante') {
        $estudiantesDelCiclo = listarEstudiantesPorCiclo($idCicloMasivo);
        $mensajesEnviados = 0;

        foreach ($estudiantesDelCiclo as $est) {
            if (insertarNuevoMensaje($est['idEstudiante'], '', $asuntoMensaje, $descripcionMensaje, $rolEmisorMensaje)) {
                $mensajesEnviados++;
                $tokenDispositivo = obtenerTokenUsuario($est['idEstudiante'], 'estudiante');
                if ($tokenDispositivo) {
                    enviarNotificacionFirebase($tokenDispositivo, "Nuevo Mensaje: " . $asuntoMensaje, $descripcionMensaje);
                }
            }
        }

        unset($_SESSION['datos_mensaje']);
        $_SESSION['exito'] = "Mensaje enviado a " . $mensajesEnviados . " estudiantes del ciclo.";
        header("Location: ../../../vistas/admin/mensajes/lista.php");
        exit;
    }

    if (insertarNuevoMensaje($idEstudianteDestino, $idProfesorDestino, $asuntoMensaje, $descripcionMensaje, $rolEmisorMensaje)) {
        $idDestinatarioFinal = $idEstudianteDestino;
        if (empty($idDestinatarioFinal)) {
            $idDestinatarioFinal = $idProfesorDestino;
        }

        $rolDestinatarioFinal = 'profesor';
        if (!empty($idEstudianteDestino)) {
            $rolDestinatarioFinal = 'estudiante';
        }

        if (!empty($idDestinatarioFinal)) {
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
?>
