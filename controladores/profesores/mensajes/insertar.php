<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['insertarReclamacion'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $idProfesor = $_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = "Asunto y descripción son obligatorios.";
    } else {
        if (insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'profesor')) {
            if (!empty($idEstudiante)) {
                $token = obtenerTokenUsuario($idEstudiante, "estudiante");
                if ($token) {
                    enviarNotificacionFirebase($token, "Mensaje del Profesor: " . $asunto, $descripcion);
                }
            } else {
                // Notificar a administración (directores)
                $conexion = obtenerConexion();
                $res = mysqli_query($conexion, "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($row['fcm_token']) {
                        enviarNotificacionFirebase($row['fcm_token'], "Mensaje del Profesor a Dirección: " . $asunto, $descripcion);
                    }
                }
                mysqli_close($conexion);
            }
            $_SESSION['exito'] = "Mensaje enviado con éxito.";
            header("Location: /pfc/vistas/profesores/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al enviar el mensaje.";
        }
    }
    header("Location: /pfc/vistas/profesores/mensajes/agregar.php");
    exit;
}
header("Location: /pfc/vistas/profesores/mensajes/lista.php");
exit;
?>

