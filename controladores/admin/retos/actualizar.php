<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idRetoActualizar = (int)($_POST['idReto'] ?? 0);
    $nombreRetoActualizar = trim($_POST['nombreReto']);
    $horasDelReto = trim($_POST['horasReto']);
    $fechaInicioDelReto = trim($_POST['fechaInicioReto']);
    $fechaFinDelReto = trim($_POST['fechaFinReto']);
    $idModuloAsociado = $_POST['modulosReto'] ?? '';

    $errores = '';
    if (empty($nombreRetoActualizar)) $errores = "El nombre es obligatorio.";
    if (empty($horasDelReto)) {
        $errores = "Las horas son obligatorias.";
    } elseif (!is_numeric($horasDelReto)) {
        $errores = "Las horas deben ser un número.";
    }
    if (empty($fechaInicioDelReto)) $errores = "La fecha de inicio es obligatoria.";
    if (empty($fechaFinDelReto)) {
        $errores = "La fecha de fin es obligatoria.";
    } else if (!empty($fechaInicioDelReto) && $fechaFinDelReto < $fechaInicioDelReto) {
        $errores = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicioDelReto) && !empty($fechaFinDelReto) && !empty($horasDelReto) && is_numeric($horasDelReto) && $fechaInicioDelReto <= $fechaFinDelReto) {
        $fechaInicioObj = new DateTime($fechaInicioDelReto);
        $fechaFinObj = new DateTime($fechaFinDelReto);
        $diasLaborables = 0;
        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) $diasLaborables++;
            $tempIter->modify('+1 day');
        }
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasDelReto > $maxHorasPermitidas) {
            $errores = "Las horas ($horasDelReto h) superan el máximo permitido ($maxHorasPermitidas h).";
        }
    }

    if (empty($idModuloAsociado) || !is_numeric($idModuloAsociado)) {
        $errores = "Selecciona un módulo";
    } else if (is_numeric($horasDelReto)) {
        $detalle = obtenerDetalleHorasModulo($idModuloAsociado, $idRetoActualizar);
        if ($horasDelReto > $detalle['disponibles']) {
            $errores = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin'] = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
        header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
        exit;
    }

    if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, [$idModuloAsociado])) {
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
                    registrarArchivoReto($idRetoActualizar, $fileName, "public/uploads/retos/" . $newFileName, $tipo);
                }
            }
        }

        $_SESSION['exito'] = "Reto actualizado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo actualizar el reto o no hubo cambios.";
    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>
