<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/blog/actualizar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $blogRolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/blog.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/HtmlSanitizer.php";
require_once __DIR__ . "/../../admin/blog/insertar_helpers.php";

if (isset($_POST['actualizarPost'])) {
    $idPost = (int)($_POST['idPost'] ?? 0);

    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/$blogRolBase/blog/gestionBlog.php");
        exit;
    }

    $post = obtenerPostPorId($idPost);
    if (!$post) {
        $_SESSION['errores'] = 'La entrada indicada no existe.';
        header("Location: ../../../vistas/$blogRolBase/blog/gestionBlog.php");
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

    $fechaPublicacion = $post['fechaPublicacion'] ?: date('Y-m-d H:i:s');
    if ($fechaPub !== '') {
        $ts = strtotime($fechaPub);
        if ($ts === false) $listaErrores['fechaPublicacion'] = "La fecha de publicación no es válida.";
        else $fechaPublicacion = date('Y-m-d H:i:s', $ts);
    }

    $imagenAnterior = $post['imagen'];
    $imagen = $post['imagen'];
    if (empty($listaErrores)) {
        $msgImg = '';
        $nueva = blogSubirImagen($msgImg);
        if ($nueva === null) {
            $listaErrores['imagen'] = $msgImg;
        } elseif ($nueva !== '') {
            $imagen = $nueva;
        }
        if (!empty($_POST['quitarImagen'])) $imagen = '';
    }

    if (empty($listaErrores)) {
        $slug = generarSlugBlog($titulo, $idPost);
        $contenido = HtmlSanitizer::clean($contenido);
        $ok = actualizarPost($idPost, $titulo, $slug, mb_substr($resumen, 0, 500), $contenido,
                             $imagen, mb_substr($categoria, 0, 80), mb_substr($autor, 0, 120),
                             $publicado, $destacado, $fechaPublicacion);
        if ($ok) {
            // La imagen anterior deja de usarse: se elimina del disco
            if ($imagenAnterior && $imagenAnterior !== $imagen) {
                $ruta = __DIR__ . '/../../../public/uploads/blog/' . basename($imagenAnterior);
                if (is_file($ruta)) @unlink($ruta);
            }
            registrarAccion('actualizar', 'blog', $idPost, $titulo);
            $_SESSION['exito'] = "La entrada «" . $titulo . "» se ha actualizado correctamente.";
            header("Location: ../../../vistas/$blogRolBase/blog/gestionBlog.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo actualizar la entrada debido a un error del sistema.";
    } else {
        $_SESSION['errores'] = $listaErrores;
    }

    header("Location: ../../../vistas/$blogRolBase/blog/modificarPost.php?idPost=" . $idPost);
    exit;
}

header("Location: ../../../vistas/$blogRolBase/blog/gestionBlog.php");
exit;
