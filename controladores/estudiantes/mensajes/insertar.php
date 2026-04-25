<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    } else {
        if (insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'estudiante')) {
            if ($idProfesor) {
                $token = obtenerTokenUsuario($idProfesor, "profesor");
                if ($token) {
                    enviarNotificacionFirebase($token, "Mensaje de Estudiante: " . $asunto, $descripcion);
                }
            }
            $_SESSION['exito'] = "Mensaje enviado correctamente.";
            header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al procesar el mensaje.";
        }
    }
    header("Location: /pfc/vistas/estudiantes/mensajes/agregar.php");
    exit;
}
header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
exit;
?>
