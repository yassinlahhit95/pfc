<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";
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

    $errores = [];
    $esMensajeMasivo = ($tipoDeDestinatario == 'estudiante' && !empty($idCicloMasivo));
    if (empty($idEstudianteDestino) && empty($idProfesorDestino) && !$esMensajeMasivo) {
        $errores['destinatario'] = "Debe seleccionar un destinatario específico.";
    }
    if (empty($asuntoMensaje))      $errores['asunto'] = "El asunto del mensaje es un campo obligatorio.";
    if (empty($descripcionMensaje)) $errores['descripcion'] = "El contenido del mensaje no puede estar vacío.";

    if (!empty($errores)) {
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

        $enviosFCM = [];
        foreach ($estudiantesDelCiclo as $est) {
            $idMensajeMasivo = insertarNuevoMensaje($est['idEstudiante'], '', $asuntoMensaje, $descripcionMensaje, $rolEmisorMensaje);
            if ($idMensajeMasivo) {
                $mensajesEnviados++;
                $tokenDispositivo = obtenerTokenUsuario($est['idEstudiante'], 'estudiante');
                if ($tokenDispositivo) {
                    $enviosFCM[] = [
                        'token' => $tokenDispositivo,
                        'titulo' => "Nuevo Mensaje: " . $asuntoMensaje,
                        'mensaje' => $descripcionMensaje,
                        'tipo' => 'message',
                        'extra' => ['idReclamacion' => $idMensajeMasivo]
                    ];
                }
            }
        }

        if (!empty($enviosFCM)) {
            require_once __DIR__ . "/../../firebase/firebase_helper.php";
            if (function_exists('enviarNotificacionesFirebasePersonalizadasSimultaneas')) {
                enviarNotificacionesFirebasePersonalizadasSimultaneas($enviosFCM);
            } else {
                foreach ($enviosFCM as $envio) {
                    enviarNotificacionFirebase($envio['token'], $envio['titulo'], $envio['mensaje'], $envio['tipo'], $envio['extra']);
                }
            }
        }

        unset($_SESSION['datos_mensaje']);
        registrarAccion('insertar_masivo', 'mensajes', null, "Ciclo #$idCicloMasivo · $asuntoMensaje");
        $_SESSION['exito'] = "Mensaje enviado a " . $mensajesEnviados . " estudiantes del ciclo.";
        header("Location: ../../../vistas/admin/mensajes/lista.php");
        exit;
    }

    // Envío individual a un estudiante o profesor
    $idMensajeIndividual = insertarNuevoMensaje($idEstudianteDestino, $idProfesorDestino, $asuntoMensaje, $descripcionMensaje, $rolEmisorMensaje);
    if ($idMensajeIndividual) {
        $idDestinatarioFinal  = !empty($idEstudianteDestino) ? $idEstudianteDestino : $idProfesorDestino;
        $rolDestinatarioFinal = !empty($idEstudianteDestino) ? 'estudiante' : 'profesor';

        if (!empty($idDestinatarioFinal)) {
            $tokenDispositivo = obtenerTokenUsuario($idDestinatarioFinal, $rolDestinatarioFinal);
            if ($tokenDispositivo) {
                enviarNotificacionFirebase($tokenDispositivo, "Nuevo Mensaje: " . $asuntoMensaje, $descripcionMensaje, 'message', ['idReclamacion' => $idMensajeIndividual]);
            }
        }

        unset($_SESSION['datos_mensaje']);
        registrarAccion('insertar', 'mensajes', null, $asuntoMensaje);
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
