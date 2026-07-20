<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/ofertaCiclos/actualizar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/landingCiclos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/HtmlSanitizer.php";
require_once __DIR__ . "/../../admin/ofertaCiclos/insertar_helpers.php";

if (isset($_POST['actualizarCiclo'])) {
    $idLandingCiclo = (int)($_POST['idLandingCiclo'] ?? 0);

    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
        exit;
    }

    $ciclo = obtenerCicloLandingPorId($idLandingCiclo);
    if (!$ciclo) {
        $_SESSION['errores'] = 'El ciclo indicado no existe.';
        header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
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

    $imagenAnterior = $ciclo['imagen'];
    $imagen = $ciclo['imagen'];
    if (empty($listaErrores)) {
        $msgImg = '';
        $nueva = cicloSubirImagen($msgImg);
        if ($nueva === null) {
            $listaErrores['imagen'] = $msgImg;
        } elseif ($nueva !== '') {
            $imagen = $nueva;
        }
        if (!empty($_POST['quitarImagen'])) $imagen = '';
    }

    if (empty($listaErrores)) {
        $slug = generarSlugCiclo($titulo, $idLandingCiclo);
        $descripcion = HtmlSanitizer::clean($descripcion);
        $ok = actualizarCicloLanding($idLandingCiclo, $titulo, $slug, mb_substr($etiqueta, 0, 60),
            mb_substr($resumen, 0, 300), $descripcion, $imagen, mb_substr($precio, 0, 60),
            mb_substr($duracion, 0, 60), mb_substr($modalidad, 0, 60), $publicado, $destacado, $orden);
        if ($ok) {
            // La imagen anterior deja de usarse: se elimina del disco
            if ($imagenAnterior && $imagenAnterior !== $imagen) {
                $ruta = __DIR__ . '/../../../public/uploads/ofertaCiclos/' . basename($imagenAnterior);
                if (is_file($ruta)) @unlink($ruta);
            }
            $rolBase === 'secretaria'
                ? registrarAccionSecretaria('actualizar', 'ofertaCiclos', $idLandingCiclo, $titulo)
                : registrarAccion('actualizar', 'ofertaCiclos', $idLandingCiclo, $titulo);
            $_SESSION['exito'] = "El ciclo «" . $titulo . "» se ha actualizado correctamente.";
            header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo actualizar el ciclo debido a un error del sistema.";
    } else {
        $_SESSION['errores'] = $listaErrores;
    }

    header("Location: ../../../vistas/$rolBase/ofertaCiclos/modificar.php?idLandingCiclo=" . $idLandingCiclo);
    exit;
}

header("Location: ../../../vistas/$rolBase/ofertaCiclos/gestion.php");
exit;
