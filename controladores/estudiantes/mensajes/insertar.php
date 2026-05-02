<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    // Saneamos las entradas
    $idEstudiante = trim($_POST['idEstudiante']);
    $idProfesor = trim($_POST['idProfesor']);
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    $hayError = false;

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = "Vaya, todos los campos son obligatorios.";
        $hayError = true;
    } else if (strlen($descripcion) > 250) {
        $_SESSION['error'] = "El mensaje es demasiado largo (mÃ¡ximo 250 caracteres).";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'estudiante');
        
        if ($resultado) {
            if (!empty($idProfesor)) {
                // Notificar al profesor
                $tokenProfesor = obtenerTokenUsuario($idProfesor, "profesor");
                if ($tokenProfesor) {
                    enviarNotificacionFirebase($tokenProfesor, "Mensaje de Estudiante: " . $asunto, $descripcion);
                }
            } else {
                // Notificar a administraciÃ³n (directores)
                $tokensDirectores = obtenerTokensDirectores();
                foreach ($tokensDirectores as $tokenDirector) {
                    enviarNotificacionFirebase($tokenDirector, "Mensaje de Estudiante a DirecciÃ³n: " . $asunto, $descripcion);
                }
            }
            
            $_SESSION['exito'] = "Listo! El mensaje se ha enviado correctamente.";
            header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, ha habido un error al guardar el mensaje.";
        }
    }
    
    header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
    exit;
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
?>