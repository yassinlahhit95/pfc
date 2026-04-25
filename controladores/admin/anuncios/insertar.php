<?php
session_start();
require_once "../../../modelos/anuncios.php";
require_once "../../firebase/firebase_helper.php";
require_once "../../../modelos/conectar.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $lista_de_errores = [];

    if (empty($titulo)) {
        $lista_de_errores['tituloAnuncio'] = "El título es obligatorio.";
    }
    if (empty($contenido)) {
        $lista_de_errores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarAnuncio($titulo, $contenido);
        if ($resultado) {
            // Obtener todos los tokens para enviar notificación global
            $conexion = obtenerConexion();
            $tokens = [];
            
            $tablas = ['estudiantes', 'profesores', 'directores'];
            foreach ($tablas as $tabla) {
                $res = mysqli_query($conexion, "SELECT fcm_token FROM $tabla WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                if ($res) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $tokens[] = $row['fcm_token'];
                    }
                }
            }
            mysqli_close($conexion);
            
            // Enviar notificaciones
            foreach ($tokens as $token) {
                enviarNotificacionFirebase($token, "Nuevo Anuncio: " . $titulo, $contenido);
            }

            $_SESSION['exito'] = "Anuncio publicado correctamente y notificación enviada.";
            header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
exit;
