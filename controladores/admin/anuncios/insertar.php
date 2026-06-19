<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);
    $dirigidoA = $_POST['dirigidoA'];

    // ── Validación ──
    $listaErrores = [];
    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título del anuncio es un campo obligatorio.";
    }
    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido del anuncio es un campo obligatorio.";
    }

    if (empty($listaErrores)) {
        $resultado = insertarAnuncio($titulo, $contenido, $dirigidoA);

        if ($resultado) {
            // ── Notificación push a dispositivos ──
            $tokens = [];
            if ($dirigidoA == 'estudiantes' || $dirigidoA == 'todos') {
                $tokens = array_merge($tokens, obtenerTokensEstudiantes());
            }
            if ($dirigidoA == 'profesores' || $dirigidoA == 'todos') {
                $tokens = array_merge($tokens, obtenerTokensProfesores());
            }

            $tokens = array_unique($tokens);
            foreach ($tokens as $token) {
                enviarNotificacionFirebase($token, "NUEVO ANUNCIO: " . $titulo, substr(strip_tags($contenido), 0, 100) . "...");
            }

            $_SESSION['exito'] = "El anuncio ha sido publicado y notificado correctamente a " . count($tokens) . " dispositivos.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo publicar el anuncio debido a un error del sistema.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/agregarAnuncios.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
