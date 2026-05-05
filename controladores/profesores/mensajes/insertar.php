<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudiante = !empty($_POST['idEstudiante']) ? (int)trim($_POST['idEstudiante']) : 0;
    $idProfesor = (int)trim($_POST['idProfesor']);
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

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
        $resultado = insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'profesor');
        
        if ($resultado) {
            // Notificaciones
            if ($idEstudiante > 1) { 
                $token = obtenerTokenUsuario($idEstudiante, "estudiante");
                if ($token) enviarNotificacionFirebase($token, "Mensaje de Profesor: " . $asunto, $descripcion);
            } else {
                $tokens = obtenerTokensDirectores();
                foreach ($tokens as $t) enviarNotificacionFirebase($token, "Mensaje de Profesor: " . $asunto, $descripcion);
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
    
    header("Location: ../../../vistas/profesores/mensajes/agregar.php" . (isset($_GET['idCiclo']) ? "?idCiclo=".$_GET['idCiclo'] : ""));
    exit;
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
