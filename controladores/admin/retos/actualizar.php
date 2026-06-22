<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarReto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $idReto      = (int)($_POST['idReto'] ?? 0);
    $nombre      = trim($_POST['nombreReto']);
    $horas       = trim($_POST['horasReto']);
    $fechaInicio = trim($_POST['fechaInicioReto']);
    $fechaFin    = trim($_POST['fechaFinReto']);
    $idModulo    = $_POST['modulosReto'] ?? '';

    $errores = '';
    if (empty($nombre)) $errores = "El nombre del reto es un campo obligatorio.";
    if (empty($horas)) {
        $errores = "Las horas del reto son un campo obligatorio.";
    } elseif (!is_numeric($horas)) {
        $errores = "Las horas deben ser un valor numérico.";
    }
    if (empty($fechaInicio)) $errores = "La fecha de inicio es un campo obligatorio.";
    if (empty($fechaFin)) {
        $errores = "La fecha de fin es un campo obligatorio.";
    } elseif (!empty($fechaInicio) && $fechaFin < $fechaInicio) {
        $errores = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horas) && is_numeric($horas) && $fechaInicio <= $fechaFin) {
        $fechaInicioObj = new DateTime($fechaInicio);
        $fechaFinObj    = new DateTime($fechaFin);
        $diasLaborables = 0;
        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) $diasLaborables++;
            $tempIter->modify('+1 day');
        }
        $maxHoras = $diasLaborables * 6;
        if ($horas > $maxHoras) {
            $errores = "Las horas ($horas h) superan el máximo permitido ($maxHoras h).";
        }
    }

    if (empty($idModulo) || !is_numeric($idModulo)) {
        $errores = "Debe seleccionar un módulo para el reto.";
    } elseif (is_numeric($horas)) {
        $detalle = obtenerDetalleHorasModulo($idModulo, $idReto);
        if ($horas > $detalle['disponibles']) {
            $errores = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin']    = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
        header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idReto");
        exit;
    }

    if (actualizarReto($idReto, $nombre, $fechaInicio, $fechaFin, $horas, [$idModulo])) {
        registrarAccion('actualizar', 'retos', $idReto, $nombre);
        if (!empty($_FILES['archivosReto']['name'][0])) {
            $uploadDir = __DIR__ . "/../../../public/uploads/retos/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
            foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['archivosReto']['error'][$key] !== UPLOAD_ERR_OK) continue;
                $fileName = $_FILES['archivosReto']['name'][$key];
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                if (!in_array($fileExt, $permitidos)) continue;
                $newFileName = bin2hex(random_bytes(8)) . '.' . $fileExt;
                if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                    $tipo = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']) ? 'imagen' : 'pdf';
                    registrarArchivoReto($idReto, $fileName, "public/uploads/retos/" . $newFileName, $tipo);
                }
            }
        }
        $_SESSION['exito'] = "El reto ha sido actualizado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el reto o no se detectaron cambios.";
    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idReto");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
