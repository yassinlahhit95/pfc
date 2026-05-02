<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);
    $dirigidoA = trim($_POST['dirigidoA']);

    $hayError = false;

    if (empty($titulo) || empty($contenido)) {
        $_SESSION['error'] = "El título y el contenido son obligatorios.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = insertarAnuncio($titulo, $contenido, $dirigidoA);
        
        if ($resultado) {
            // Obtener tokens segÃºn destinatario de forma limpia desde modelos
            $tokens = [];
            
            if ($dirigidoA == 'todos' || $dirigidoA == 'estudiantes') {
                $tokens = array_merge($tokens, obtenerTokensEstudiantes());
            }
            if ($dirigidoA == 'todos' || $dirigidoA == 'profesores') {
                $tokens = array_merge($tokens, obtenerTokensProfesores());
            }
            if ($dirigidoA == 'todos') {
                $tokens = array_merge($tokens, obtenerTokensDirectores());
            }
            
            // Enviar notificaciones push si hay tokens
            if (!empty($tokens)) {
                foreach ($tokens as $tokenDestinatario) {
                    enviarNotificacionFirebase($tokenDestinatario, "Nuevo Anuncio: " . $titulo, $contenido);
                }
            }

            $_SESSION['exito'] = "Anuncio publicado y notificado.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar el anuncio.";
        }
    }

    header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>