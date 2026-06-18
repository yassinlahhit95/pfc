<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['guardarReto'])) {
    $nombreReto = trim($_POST['nombreReto']);
    $horasReto = trim($_POST['horasReto']);
    $fechaInicioReto = trim($_POST['fechaInicioReto']);
    $fechaFinReto = trim($_POST['fechaFinReto']);
    $idModulo = $_POST['modulosReto'] ?? '';

    $listaDeErrores = [];

    if (empty($nombreReto)) $listaDeErrores['nombreReto'] = "El nombre es obligatorio.";
    if (empty($horasReto)) {
        $listaDeErrores['horasReto'] = "Las horas son obligatorias.";
    } else if (!is_numeric($horasReto)) {
        $listaDeErrores['horasReto'] = "Las horas deben ser un número.";
    }

    $hoy = date('Y-m-d');
    if (empty($fechaInicioReto)) {
        $listaDeErrores['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    } else if ($fechaInicioReto < $hoy) {
        $listaDeErrores['fechaInicioReto'] = "La fecha no puede ser anterior a hoy.";
    }
    if (empty($fechaFinReto)) {
        $listaDeErrores['fechaFinReto'] = "La fecha de fin es obligatoria.";
    } else if (!empty($fechaInicioReto) && $fechaFinReto < $fechaInicioReto) {
        $listaDeErrores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicioReto) && !empty($fechaFinReto) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicioReto <= $fechaFinReto) {
        $fechaInicioObj = new DateTime($fechaInicioReto);
        $fechaFinObj = new DateTime($fechaFinReto);
        $diasLaborables = 0;
        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) $diasLaborables++;
            $tempIter->modify('+1 day');
        }
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasReto > $maxHorasPermitidas) {
            $listaDeErrores['horasReto'] = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
        }
    }

    if (empty($idModulo) || !is_numeric($idModulo)) {
        $listaDeErrores['modulosReto'] = "Debes seleccionar un módulo.";
    } else if (is_numeric($horasReto)) {
        $detalle = obtenerDetalleHorasModulo($idModulo);
        if ($horasReto > $detalle['disponibles']) {
            $listaDeErrores['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (!empty($listaDeErrores)) {
        $_SESSION['errores'] = $listaDeErrores;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/admin/retos/agregarRetos.php");
        exit;
    }

    if (insertarReto($nombreReto, $fechaInicioReto, $fechaFinReto, $horasReto, [$idModulo])) {
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

        $_SESSION['exito'] = "Reto creado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo crear el reto en la base de datos.";
    header("Location: ../../../vistas/admin/retos/agregarRetos.php");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>
