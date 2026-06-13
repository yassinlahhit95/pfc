<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idRetoActualizar = $_POST['idReto'];
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
        // Manejo de nuevos archivos adjuntos
        if (!empty($_FILES['archivosReto']['name'][0])) {
            $uploadDir = "../../../public/uploads/retos/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['archivosReto']['name'][$key];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = "reto_" . $idRetoActualizar . "_" . time() . "_" . $key . "." . $fileExt;
                $dest = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $dest)) {
                    $tipo = (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif'])) ? 'imagen' : 'pdf';
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
