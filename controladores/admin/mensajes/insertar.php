<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEstudiante = $_POST['idEstudiante'] ?? null;
    $idProfesor = $_POST['idProfesor'] ?? null;
    $emisor_rol = $_POST['emisor_rol'] ?? 'admin';
    $asunto = trim($_POST['asunto']);
    $descripcion = trim($_POST['descripcion']);
    $fechaActual = date('Y-m-d');

    if (empty($asunto) || empty($descripcion)) {
        $_SESSION['error'] = "Asunto y contenido son obligatorios.";
    } else {
        if (insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, $fechaActual, $emisor_rol)) {
            // Notificar al destinatario si existe
            $destId = $idEstudiante ?: $idProfesor;
            $destRol = $idEstudiante ? 'estudiante' : 'profesor';

            if ($destId) {
                $token = obtenerTokenUsuario($destId, $destRol);
                if ($token) {
                    enviarNotificacionFirebase($token, "Nuevo Mensaje: " . $asunto, $descripcion);
                }
            }
            $_SESSION['exito'] = "Mensaje enviado con éxito.";
            header("Location: /pfc/vistas/admin/mensajes/lista.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar el mensaje en la base de datos.";
        }
    }
    header("Location: /pfc/vistas/admin/mensajes/agregar.php");
    exit;
}
header("Location: /pfc/vistas/admin/mensajes/lista.php");
exit;
?>

