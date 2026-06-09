<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/calificaciones.php";

if (isset($_POST['actualizarNota'])) {
    $idCalificacion = trim($_POST['idCalificacion']);
    $idEstudiante = trim($_POST['idEstudiante']);
    $idModulo = trim($_POST['idModulo']);
    $nota1Ev = trim($_POST['nota_1ev']);
    $nota1Final = trim($_POST['nota_1final']);
    $nota2Ev = trim($_POST['nota_2ev']);
    $nota2Final = trim($_POST['nota_2final']);

    $nota1Ev = str_replace(',', '.', $nota1Ev);
    $nota1Final = str_replace(',', '.', $nota1Final);
    $nota2Ev = str_replace(',', '.', $nota2Ev);
    $nota2Final = str_replace(',', '.', $nota2Final);

    $errores = '';

    if (!empty($nota1Ev) && (!is_numeric($nota1Ev) || $nota1Ev < 0 || $nota1Ev > 10)) {
        $errores = "La nota debe estar entre 0 y 10.";
    }
    if (!empty($nota1Final) && (!is_numeric($nota1Final) || $nota1Final < 0 || $nota1Final > 10)) {
        $errores = "La nota debe estar entre 0 y 10.";
    }
    if (!empty($nota2Ev) && (!is_numeric($nota2Ev) || $nota2Ev < 0 || $nota2Ev > 10)) {
        $errores = "La nota debe estar entre 0 y 10.";
    }
    if (!empty($nota2Final) && (!is_numeric($nota2Final) || $nota2Final < 0 || $nota2Final > 10)) {
        $errores = "La nota debe estar entre 0 y 10.";
    }

    if (!$errores) {
        $notaActual = obtenerCalificacionPorId($idCalificacion);
        $observaciones = $notaActual['observaciones'] ?? '';
        $resultado = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1Ev, $nota1Final, $nota2Ev, $nota2Final, $observaciones);

        if ($resultado) {
            if (!empty($_POST['notificarEstudiante'])) {
                require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
                enviarEmailNotasEstudiante($idEstudiante);
            }
            $_SESSION['exito'] = "Calificación actualizada.";
            header("Location: ../../../vistas/profesores/calificaciones/lista.php");
            exit;
        }
        $_SESSION['errores'] = "Error al guardar.";
    } else {
        $_SESSION['errores'] = $errores;
    }

    header("Location: ../../../vistas/profesores/calificaciones/editar.php?id=" . $idCalificacion);
    exit;
}

header("Location: ../../../vistas/profesores/calificaciones/lista.php");
exit;
?>
