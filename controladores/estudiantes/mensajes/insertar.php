<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
require_once "../../../modelos/directores.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['enviarMensaje'])) {
    $idEst = trim($_POST['idEstudiante']);
    $idProf = trim($_POST['idProfesor']);
    $asu = trim($_POST['asunto']);
    $desc = trim($_POST['descripcion']);
    $hoy = date('Y-m-d');

    $errs = [];

    if (empty($asu)) $errs['asunto'] = "El asunto es obligatorio.";
    if (empty($desc)) $errs['descripcion'] = "El mensaje es obligatorio.";
    else if (strlen($desc) > 250) $errs['descripcion'] = "Máximo 250 caracteres.";

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        $_SESSION['datos_mensaje'] = $_POST;
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
        exit;
    }

    $res = insertarNuevoMensaje($idEst, $idProf, $asu, $desc, $hoy, 'estudiante');
    
    if ($res) {
        if (!empty($idProf)) {
            $tokProf = obtenerTokenUsuario($idProf, "profesor");
            if ($tokProf) enviarNotificacionFirebase($tokProf, "Mensaje de Estudiante: $asu", $desc);
        } else {
            $toksDirs = obtenerTokensDirectores();
            foreach ($toksDirs as $tok) enviarNotificacionFirebase($tok, "Mensaje de Estudiante a Dirección: $asu", $desc);
        }
        
        $_SESSION['exito'] = "Mensaje enviado.";
        header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar el mensaje.";
        $_SESSION['datos_mensaje'] = $_POST;
        header("Location: ../../../vistas/estudiantes/mensajes/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
?>
