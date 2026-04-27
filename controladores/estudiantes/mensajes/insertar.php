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
        $_SESSION['error'] = strtoupper("TODOS LOS CAMPOS SON OBLIGATORIOS.");
    } else if (strlen($descripcion) > 250) {
        $_SESSION['error'] = strtoupper("EL MENSAJE NO PUEDE SUPERAR LOS 250 CARACTERES.");
    } else {
        if (insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'estudiante')) {
            if (!empty($idProfesor)) {
                $token = obtenerTokenUsuario($idProfesor, "profesor");
                if ($token) {
                    enviarNotificacionFirebase($token, "Mensaje de Estudiante: " . $asunto, $descripcion);
                }
            } else {
                // Notificar a administración (directores)
                $conexion = obtenerConexion();
                $res = mysqli_query($conexion, "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($row['fcm_token']) {
                        enviarNotificacionFirebase($row['fcm_token'], "Mensaje de Estudiante a Dirección: " . $asunto, $descripcion);
                    }
                }
                mysqli_close($conexion);
            }
            $_SESSION['exito'] = strtoupper("MENSAJE ENVIADO CORRECTAMENTE.");
            header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL PROCESAR EL MENSAJE.");
        }
    }
    header("Location: /pfc/vistas/estudiantes/mensajes/agregar.php");
    exit;
}
header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
exit;
?>

