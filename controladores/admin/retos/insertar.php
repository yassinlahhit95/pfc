<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarReto'])) {
    $nombre      = trim($_POST['nombreReto']);
    $horas       = trim($_POST['horasReto']);
    $fechaInicio = trim($_POST['fechaInicioReto']);
    $fechaFin    = trim($_POST['fechaFinReto']);
    $idModulo    = $_POST['modulosReto'] ?? '';

    $errores = [];
    if (empty($nombre)) $errores['nombreReto'] = "El nombre del reto es un campo obligatorio.";
    if (empty($horas)) {
        $errores['horasReto'] = "Las horas del reto son un campo obligatorio.";
    } elseif (!is_numeric($horas)) {
        $errores['horasReto'] = "Las horas deben ser un valor numérico.";
    }

    $hoy = date('Y-m-d');
    if (empty($fechaInicio)) {
        $errores['fechaInicioReto'] = "La fecha de inicio es un campo obligatorio.";
    } elseif ($fechaInicio < $hoy) {
        $errores['fechaInicioReto'] = "La fecha de inicio no puede ser anterior a hoy.";
    }
    if (empty($fechaFin)) {
        $errores['fechaFinReto'] = "La fecha de fin es un campo obligatorio.";
    } elseif (!empty($fechaInicio) && $fechaFin < $fechaInicio) {
        $errores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
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
            $errores['horasReto'] = "Las horas ($horas h) superan el máximo permitido ($maxHoras h).";
        }
    }

    if (empty($idModulo) || !is_numeric($idModulo)) {
        $errores['modulosReto'] = "Debe seleccionar un módulo para el reto.";
    } elseif (is_numeric($horas)) {
        $detalle = obtenerDetalleHorasModulo($idModulo);
        if ($horas > $detalle['disponibles']) {
            $errores['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/admin/retos/agregarRetos.php");
        exit;
    }

    if (insertarReto($nombre, $fechaInicio, $fechaFin, $horas, [$idModulo])) {
        $idNuevoReto = mysqli_insert_id(obtenerConexion());

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
                    registrarArchivoReto($idNuevoReto, $fileName, "public/uploads/retos/" . $newFileName, $tipo);
                }
            }
        }

        $_SESSION['exito'] = "El reto ha sido creado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar crear el reto en la base de datos.";
    header("Location: ../../../vistas/admin/retos/agregarRetos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
