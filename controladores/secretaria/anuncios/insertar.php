<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_anuncios');
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/anuncios/agregarAnuncio.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/anuncios/agregarAnuncio.php"); exit;
}

$titulo    = Security::sanitize($_POST['titulo'] ?? '');
$mensaje   = Security::sanitize($_POST['mensaje'] ?? '');
$dirigidoA = Security::sanitize($_POST['dirigidoA'] ?? 'todos');

$opcValidas = ['todos', 'estudiantes', 'profesores', 'tutores'];
if (!in_array($dirigidoA, $opcValidas)) $dirigidoA = 'todos';

$errores = [];
if (empty($titulo))  $errores[] = "El título es obligatorio.";
if (empty($mensaje)) $errores[] = "El contenido es obligatorio.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/anuncios/agregarAnuncio.php");
    exit;
}

$ok = insertarAnuncio($titulo, $mensaje, $dirigidoA);

if ($ok) {
    registrarAccionSecretaria('insertar', 'anuncios', null, $titulo);

    // Notificación push a los destinatarios (paridad con el alta desde dirección)
    require_once __DIR__ . "/../../firebase/firebase_helper.php";
    $tokens = [];
    if ($dirigidoA == 'estudiantes' || $dirigidoA == 'todos') {
        require_once __DIR__ . "/../../../modelos/estudiantes.php";
        $tokens = array_merge($tokens, obtenerTokensEstudiantes());
    }
    if ($dirigidoA == 'profesores' || $dirigidoA == 'todos') {
        require_once __DIR__ . "/../../../modelos/profesores.php";
        $tokens = array_merge($tokens, obtenerTokensProfesores());
    }
    if ($dirigidoA == 'tutores' || $dirigidoA == 'todos') {
        require_once __DIR__ . "/../../../modelos/tutores.php";
        $tokens = array_merge($tokens, obtenerTokensTutores());
    }
    foreach (array_unique($tokens) as $token) {
        enviarNotificacionFirebase($token, "NUEVO ANUNCIO: " . $titulo, substr(strip_tags($mensaje), 0, 100) . "...");
    }

    $_SESSION['exito'] = "Aviso publicado correctamente.";
} else {
    $_SESSION['errores'] = "Error al publicar el aviso.";
}
header("Location: ../../../vistas/secretaria/anuncios/gestionAnuncios.php");
exit;
