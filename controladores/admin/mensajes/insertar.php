<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['enviarMensaje'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada. Recarga la página e inténtalo de nuevo.";
        header("Location: ../../../vistas/admin/mensajes/agregar.php"); exit;
    }

    $idEstudianteDestino = !empty($_POST['idEstudiante']) ? (int)$_POST['idEstudiante'] : '';
    $idProfesorDestino   = !empty($_POST['idProfesor'])   ? (int)$_POST['idProfesor']   : '';
    $rolEmisorMensaje    = !empty($_POST['emisor_rol'])   ? trim($_POST['emisor_rol'])   : 'admin';
    $tipoDeDestinatario  = !empty($_POST['tipoDestinatario']) ? trim($_POST['tipoDestinatario']) : 'profesor';
    $idCicloMasivo       = !empty($_POST['idCicloMasivo']) ? (int)$_POST['idCicloMasivo'] : '';
    $asuntoMensaje       = trim($_POST['asunto']);
    $descripcionMensaje  = trim($_POST['descripcion']);

    $_SESSION['datos_mensaje'] = $_POST;

    $errores = '';
    $esMensajeMasivo = ($tipoDeDestinatario == 'estudiante' && !empty($idCicloMasivo));
    if (empty($idEstudianteDestino) && empty($idProfesorDestino) && !$esMensajeMasivo) {
        $errores = "Debe seleccionar un destinatario específico.";
    }
    if (empty($asuntoMensaje))      $errores = "El asunto del mensaje es un campo obligatorio.";
    if (empty($descripcionMensaje)) $errores = "El contenido del mensaje no puede estar vacío.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $urlRedireccion = "../../../vistas/admin/mensajes/agregar.php?tipoDestinatario=" . $tipoDeDestinatario;
        if (!empty($idCicloMasivo)) {
            $urlRedireccion .= "&idCiclo=" . $idCicloMasivo;
        }
        header("Location: " . $urlRedireccion);
        exit;
    }

    // Envío masivo a todos los estudiantes de un ciclo
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

    // Envío individual a un estudiante o profesor
    if (insertarNuevoMensaje($idEstudianteDestino, $idProfesorDestino, $asuntoMensaje, $descripcionMensaje, $rolEmisorMensaje)) {
        $idDestinatarioFinal  = !empty($idEstudianteDestino) ? $idEstudianteDestino : $idProfesorDestino;
        $rolDestinatarioFinal = !empty($idEstudianteDestino) ? 'estudiante' : 'profesor';

        if (!empty($idDestinatarioFinal)) {
            $tokenDispositivo = obtenerTokenUsuario($idDestinatarioFinal, $rolDestinatarioFinal);
            if ($tokenDispositivo) {
                enviarNotificacionFirebase($tokenDispositivo, "Nuevo Mensaje: " . $asuntoMensaje, $descripcionMensaje);
            }
        }

        unset($_SESSION['datos_mensaje']);
        $_SESSION['exito'] = "El mensaje oficial ha sido enviado correctamente.";
        header("Location: ../../../vistas/admin/mensajes/lista.php");
        exit;
    }

    $_SESSION['errores'] = "Ocurrió un error al intentar enviar el mensaje.";
    header("Location: ../../../vistas/admin/mensajes/agregar.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
