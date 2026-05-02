<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['insertarReclamacion']) || isset($_POST['enviarMensaje'])) {
    // Limpiamos los datos de entrada
    $idEstudiante = !empty($_POST['idEstudiante']) ? (int)trim($_POST['idEstudiante']) : 0;
    $idProfesor = (int)trim($_POST['idProfesor']);
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    $hayError = false;

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = "Vaya, el asunto y el mensaje son obligatorios.";
        $hayError = true;
    } else if (strlen($descripcion) > 250) {
        $_SESSION['error'] = "El mensaje es demasiado largo (mÃ¡ximo 250 caracteres).";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'profesor');
        
        if ($resultado) {
            // LÃ³gica de notificaciones
            if ($idEstudiante > 1) { 
                // Notificar al estudiante
                $tokenEstudiante = obtenerTokenUsuario($idEstudiante, "estudiante");
                if ($tokenEstudiante) {
                    enviarNotificacionFirebase($tokenEstudiante, "Mensaje del Profesor: " . $asunto, $descripcion);
                }
            } else {
                // Notificar a administraciÃ³n (directores)
                $tokensDirectores = obtenerTokensDirectores();
                foreach ($tokensDirectores as $tokenDirector) {
                    enviarNotificacionFirebase($tokenDirector, "Mensaje del Profesor a DirecciÃ³n: " . $asunto, $descripcion);
                }
            }
            
            $_SESSION['exito'] = "Listo! El mensaje se ha enviado correctamente.";
            header("Location: ../../../vistas/profesores/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, ha habido un error al guardar el mensaje.";
        }
    }
    
    header("Location: ../../../vistas/profesores/mensajes/agregar.php");
    exit;
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
?>