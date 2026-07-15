<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_landing')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/blog.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/secretaria/blog/gestionBlog.php"); exit;
}

if (isset($_POST['idPost'])) {
    $idPost = (int)($_POST['idPost'] ?? 0);
    $post = obtenerPostPorId($idPost);
    if ($post && borrarPost($idPost)) {
        // La imagen de portada deja de usarse: se elimina del disco
        if (!empty($post['imagen'])) {
            $ruta = __DIR__ . '/../../../public/uploads/blog/' . basename($post['imagen']);
            if (is_file($ruta)) @unlink($ruta);
        }
        registrarAccion('borrar', 'blog', $idPost, $post['titulo']);
        $ok = true; $msg = "La entrada ha sido eliminada correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Ocurrió un error al intentar eliminar la entrada seleccionada.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/secretaria/blog/gestionBlog.php");
exit;
