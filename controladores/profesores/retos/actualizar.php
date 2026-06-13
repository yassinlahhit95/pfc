<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = $_POST['idReto'];
    $nombreReto = trim($_POST['nombreReto']);
    $fechaInicio = trim($_POST['fechaInicio']);
    $fechaFin = trim($_POST['fechaFin']);
    $horasReto = trim($_POST['horasReto']);
    $modulosSeleccionados = $_POST['modulos'] ?? [];

    $errores = '';

    if (empty($idReto)) {
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    }

    if (empty($nombreReto)) $errores = "El nombre es obligatorio.";
    if (empty($fechaInicio)) $errores = "Fecha de inicio requerida";
    if (empty($fechaFin)) $errores = "La fecha de fin es obligatoria.";
    if (empty($horasReto)) {
        $errores = "Las horas son obligatorias.";
    } elseif (!is_numeric($horasReto)) {
        $errores = "Las horas deben ser un número";
    }

    if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicio <= $fechaFin) {
        $fechaInicioObj = new DateTime($fechaInicio);
        $fechaFinObj = new DateTime($fechaFin);
        $diasLaborables = 0;

        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) {
                $diasLaborables++;
            }
            $tempIter->modify('+1 day');
        }

        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasReto > $maxHorasPermitidas) {
            $errores = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
        }
    }

    if (empty($modulosSeleccionados)) {
        $errores = "Al menos un módulo";
    } else if (is_numeric($horasReto)) {
        foreach ($modulosSeleccionados as $idModulo) {
            $detalle = obtenerDetalleHorasModulo($idModulo, $idReto);
            if ($horasReto > $detalle['disponibles']) {
                $errores = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
                break;
            }
        }
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }

    $resultado = actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulosSeleccionados);
    if ($resultado) {
        // Manejo de nuevos archivos adjuntos
        if (!empty($_FILES['archivosReto']['name'][0])) {
            $uploadDir = "../../../public/uploads/retos/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['archivosReto']['name'][$key];
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = "reto_" . $idReto . "_" . time() . "_" . $key . "." . $fileExt;
                $dest = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $dest)) {
                    $tipo = (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif'])) ? 'imagen' : 'pdf';
                    registrarArchivoReto($idReto, $fileName, "public/uploads/retos/" . $newFileName, $tipo);
                }
            }
        }

        $_SESSION['exito'] = "Reto actualizado correctamente.";
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['errores'] = "Error al actualizar el reto.";
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
