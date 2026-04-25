<?php
session_start();
require_once "../../../modelos/conectar.php";
require_once "../../firebase/firebase_helper.php";

if (isset($_POST['enviarGlobal'])) {
    $titulo = trim($_POST['titulo']);
    $mensaje = trim($_POST['mensaje']);
    $dirigidoA = $_POST['dirigidoA'];
    
    $conexion = obtenerConexion();
    $tokens = [];
    
    // Obtener tokens de estudiantes
    if ($dirigidoA == 'todos' || $dirigidoA == 'estudiantes') {
        $res = mysqli_query($conexion, "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != ''");
        while ($row = mysqli_fetch_assoc($res)) {
            $tokens[] = $row['fcm_token'];
        }
    }
    
    // Obtener tokens de profesores
    if ($dirigidoA == 'todos' || $dirigidoA == 'profesores') {
        $res = mysqli_query($conexion, "SELECT fcm_token FROM profesores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
        while ($row = mysqli_fetch_assoc($res)) {
            $tokens[] = $row['fcm_token'];
        }
    }

    // Obtener tokens de administradores (directores) para pruebas
    if ($dirigidoA == 'todos') {
        $res = mysqli_query($conexion, "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
        while ($row = mysqli_fetch_assoc($res)) {
            $tokens[] = $row['fcm_token'];
        }
    }
    
    mysqli_close($conexion);
    
    if (empty($tokens)) {
        $_SESSION['error'] = "No hay usuarios con tokens registrados para enviar notificaciones.";
    } else {
        $enviados = 0;
        $errores = 0;
        
        // Verificamos si podemos obtener un token de acceso (si existe el json)
        $tokenAcceso = obtenerAccessToken();
        
        if (!$tokenAcceso) {
            $_SESSION['error'] = "El sistema de notificaciones no está configurado (falta service-account.json). El mensaje no se pudo enviar por Push.";
        } else {
            foreach ($tokens as $token) {
                $respuesta = enviarNotificacionFirebase($token, $titulo, $mensaje);
                if ($respuesta && strpos($respuesta, '"name":') !== false) {
                    $enviados++;
                } else {
                    $errores++;
                }
            }
            
            if ($enviados > 0) {
                $_SESSION['exito'] = "Notificación enviada con éxito a $enviados dispositivos.";
            }
            if ($errores > 0) {
                $_SESSION['error'] = "Hubo errores en $errores envíos. Revisa el log de PHP.";
            }
        }
    }
    
    header("Location: /pfc/vistas/admin/mensajes/enviar_global.php");
    exit;
}

header("Location: /pfc/vistas/admin/dashboard.php");
exit;
?>
