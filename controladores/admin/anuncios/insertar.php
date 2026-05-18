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
    $dirigidoA = $_POST['dirigidoA'];

    $listaErrores = [];
    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título es obligatorio.";
    }
    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    if (empty($listaErrores)) {
        $resultado = insertarAnuncio($titulo, $contenido, $dirigidoA);

        if ($resultado) {
            // NOTIFICACIONES PUSH FIREBASE
            $tokens = [];
            if ($dirigidoA == 'estudiantes' || $dirigidoA == 'todos') {
                $tokens = array_merge($tokens, obtenerTokensEstudiantes());
            }
            if ($dirigidoA == 'profesores' || $dirigidoA == 'todos') {
                require_once __DIR__ . "/../../../modelos/profesores.php";
                $tokens = array_merge($tokens, obtenerTokensProfesores());
            }

            $tokens = array_unique($tokens); // Evitar duplicados
            foreach ($tokens as $token) {
                enviarNotificacionFirebase($token, "NUEVO ANUNCIO: " . $titulo, substr(strip_tags($contenido), 0, 100) . "...");
            }

            $_SESSION['exito'] = "Anuncio publicado y notificado a " . count($tokens) . " dispositivos.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo publicar el anuncio.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/agregarAnuncios.php");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>
