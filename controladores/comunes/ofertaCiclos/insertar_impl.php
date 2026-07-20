<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/ofertaCiclos/insertar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/landingCiclos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/HtmlSanitizer.php";
require_once __DIR__ . "/../../admin/ofertaCiclos/insertar_helpers.php";

if (isset($_POST['guardarCiclo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/$rolBase/ofertaCiclos/agregar.php");
        exit;
    }

    $titulo      = trim($_POST['titulo'] ?? '');
    $etiqueta    = trim($_POST['etiqueta'] ?? '');
    $resumen     = trim($_POST['resumen'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio      = trim($_POST['precio'] ?? '');
    $duracion    = trim($_POST['duracion'] ?? '');
    $modalidad   = trim($_POST['modalidad'] ?? '');
    $publicado   = !empty($_POST['publicado']) ? 1 : 0;
    $destacado   = !empty($_POST['destacado']) ? 1 : 0;
    $orden       = (int)($_POST['orden'] ?? 0);

    $listaErrores = [];
    if ($titulo === '')      $listaErrores['titulo'] = "El título es un campo obligatorio.";
    if ($descripcion === '') $listaErrores['descripcion'] = "La descripción es un campo obligatorio.";

    $imagen = '';
    if (empty($listaErrores)) {
        $msgImg = '';
        $imagen = cicloSubirImagen($msgImg);
        if ($imagen === null) $listaErrores['imagen'] = $msgImg;
    }

    if (empty($listaErrores)) {
        $slug = generarSlugCiclo($titulo);
        $descripcion = HtmlSanitizer::clean($descripcion);
        $idLandingCiclo = insertarCicloLanding($titulo, $slug, mb_substr($etiqueta, 0, 60),
            mb_substr($resumen, 0, 300), $descripcion, $imagen, mb_substr($precio, 0, 60),
            mb_substr($duracion, 0, 60), mb_substr($modalidad, 0, 60), $publicado, $destacado, $orden);
        if ($idLandingCiclo) {
            $rolBase === 'secretaria'
                ? registrarAccionSecretaria('insertar', 'ofertaCiclos', $idLandingCiclo, $titulo)
                : registrarAccion('insertar', 'ofertaCiclos', $idLandingCiclo, $titulo);
            $_SESSION['exito'] = $publicado
                ? "El ciclo «" . $titulo . "» se ha publicado correctamente."
                : "El ciclo «" . $titulo . "» se ha guardado como borrador.";
            header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo guardar el ciclo debido a un error del sistema.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_ciclo'] = $_POST;
    }

    header("Location: ../../../vistas/$rolBase/ofertaCiclos/agregar.php");
    exit;
}

header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
exit;
