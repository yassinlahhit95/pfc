<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');
require_once __DIR__ . "/../../../modelos/blog.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/insertar_helpers.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarPost'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/blog/agregarPost.php");
        exit;
    }

    $titulo    = trim($_POST['titulo'] ?? '');
    $resumen   = trim($_POST['resumen'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $autor     = trim($_POST['autor'] ?? '');
    $publicado = !empty($_POST['publicado']) ? 1 : 0;
    $destacado = !empty($_POST['destacado']) ? 1 : 0;
    $fechaPub  = trim($_POST['fechaPublicacion'] ?? '');

    $listaErrores = [];
    if ($titulo === '')    $listaErrores['titulo'] = "El título es un campo obligatorio.";
    if ($contenido === '') $listaErrores['contenido'] = "El contenido es un campo obligatorio.";

    $fechaPublicacion = date('Y-m-d H:i:s');
    if ($fechaPub !== '') {
        $ts = strtotime($fechaPub);
        if ($ts === false) $listaErrores['fechaPublicacion'] = "La fecha de publicación no es válida.";
        else $fechaPublicacion = date('Y-m-d H:i:s', $ts);
    }

    $imagen = '';
    if (empty($listaErrores)) {
        $msgImg = '';
        $imagen = blogSubirImagenPortada($msgImg);
        if ($imagen === null) $listaErrores['imagen'] = $msgImg;
    }

    if (empty($listaErrores)) {
        $slug = generarSlugBlog($titulo);
        $idPost = insertarPost($titulo, $slug, mb_substr($resumen, 0, 500), $contenido,
                               $imagen, mb_substr($categoria, 0, 80), mb_substr($autor, 0, 120),
                               $publicado, $destacado, $fechaPublicacion);
        if ($idPost) {
            registrarAccion('insertar', 'blog', $idPost, $titulo);
            $_SESSION['exito'] = $publicado
                ? "La entrada «" . $titulo . "» se ha publicado correctamente."
                : "La entrada «" . $titulo . "» se ha guardado como borrador.";
            header("Location: ../../../vistas/admin/blog/gestionBlog.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo guardar la entrada debido a un error del sistema.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_post'] = $_POST;
    }

    header("Location: ../../../vistas/admin/blog/agregarPost.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/blog/gestionBlog.php");
exit;
