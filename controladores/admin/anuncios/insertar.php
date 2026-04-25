<?php
session_start();
require_once "../../../modelos/anuncios.php";
require_once "../../firebase/firebase_helper.php";
require_once "../../../modelos/conectar.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);
    $dirigidoA = $_POST['dirigidoA'];

    $lista_de_errores = array();

    if (empty($titulo)) {
        $lista_de_errores['tituloAnuncio'] = "El título es obligatorio.";
    }
    if (empty($contenido)) {
        $lista_de_errores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        // insertarAnuncio($titulo, $mensaje, $dirigidoA = 'todos')
        $resultado = insertarAnuncio($titulo, $contenido, $dirigidoA);
        if ($resultado) {
            // Obtener tokens según destinatario
            $conexion = obtenerConexion();
            $tokens = array();
            
            $tablas = array();
            if ($dirigidoA == 'todos') {
                $tablas = array('estudiantes', 'profesores', 'directores');
            } else if ($dirigidoA == 'estudiantes') {
                $tablas = array('estudiantes');
            } else if ($dirigidoA == 'profesores') {
                $tablas = array('profesores');
            }

            foreach ($tablas as $tabla) {
                $res = mysqli_query($conexion, "SELECT fcm_token FROM $tabla WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                if ($res) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $tokens[] = $row['fcm_token'];
                    }
                }
            }
            mysqli_close($conexion);
            
            // Enviar notificaciones push
            if (!empty($tokens)) {
                $tokenAcceso = obtenerAccessToken();
                if ($tokenAcceso) {
                    foreach ($tokens as $t) {
                        enviarNotificacionFirebase($t, "Nuevo Anuncio: " . $titulo, $contenido);
                    }
                }
            }

            $_SESSION['exito'] = "Anuncio publicado correctamente y notificación enviada a " . count($tokens) . " dispositivos.";
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
?>
