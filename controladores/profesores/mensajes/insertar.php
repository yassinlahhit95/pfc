<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['insertarReclamacion']) || isset($_POST['enviarMensaje'])) {
    $idEstudiante = !empty($_POST['idEstudiante']) ? (int)$_POST['idEstudiante'] : 0;
    $idProfesor = (int)$_POST['idProfesor'];
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = strtoupper("EL ASUNTO Y EL MENSAJE SON OBLIGATORIOS.");
    } else if (strlen($descripcion) > 250) {
        $_SESSION['error'] = strtoupper("EL MENSAJE NO PUEDE SUPERAR LOS 250 CARACTERES.");
    } else {
        if (insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, 'profesor')) {
            // Lógica de notificaciones
            if ($idEstudiante > 1) { // 1 es para Dirección en este contexto o mayor a 0 para alumnos
                $token = obtenerTokenUsuario($idEstudiante, "estudiante");
                if ($token) {
                    enviarNotificacionFirebase($token, "Mensaje del Profesor: " . $asunto, $descripcion);
                }
            } else {
                // Notificar a administración (directores) si el destino es 0 o 1 (según tu lógica de vista)
                $conexion = obtenerConexion();
                $res = mysqli_query($conexion, "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                while ($row = mysqli_fetch_assoc($res)) {
                    if ($row['fcm_token']) {
                        enviarNotificacionFirebase($row['fcm_token'], "Mensaje del Profesor a Dirección: " . $asunto, $descripcion);
                    }
                }
                mysqli_close($conexion);
            }
            $_SESSION['exito'] = strtoupper("MENSAJE ENVIADO CON ÉXITO.");
            header("Location: /pfc/vistas/profesores/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL ENVIAR EL MENSAJE A LA BASE DE DATOS.");
        }
    }
    header("Location: /pfc/vistas/profesores/mensajes/agregar.php");
    exit;
}
header("Location: /pfc/vistas/profesores/mensajes/lista.php");
exit;
?>

