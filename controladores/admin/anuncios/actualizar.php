<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_anuncios');
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarAnuncio'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
        exit;
    }
    $idAnuncio = (int)($_POST['idAnuncio'] ?? 0);
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    // ── Validación ──
    $listaErrores = [];
    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título del anuncio es un campo obligatorio.";
    }
    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido del anuncio es un campo obligatorio.";
    }

    if (empty($listaErrores)) {
        $anuncioActual = obtenerAnuncioPorId($idAnuncio);
        $fechaExpiracion = $anuncioActual['fechaExpiracion'];
        $dirigidoA = $anuncioActual['dirigidoA'];

        $resultado = actualizarAnuncio($idAnuncio, $titulo, $contenido, $fechaExpiracion, $dirigidoA);

        if ($resultado) {
            registrarAccion('actualizar', 'anuncios', $idAnuncio, $titulo);
            $_SESSION['exito'] = "El anuncio ha sido actualizado correctamente.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        }

        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el anuncio.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$idAnuncio");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
