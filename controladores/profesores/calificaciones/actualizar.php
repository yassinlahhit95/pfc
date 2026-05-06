<?php
session_start();
require_once __DIR__ . "/../../../modelos/calificaciones.php";

if (isset($_POST['actualizarNota'])) {
    $idCalificacion = trim($_POST['idCalificacion']);
    $idEstudiante = trim($_POST['idEstudiante']);
    $idModulo = trim($_POST['idModulo']);
    $nota1Ev = trim($_POST['nota_1ev']);
    $nota1Final = trim($_POST['nota_1final']);
    $nota2Ev = trim($_POST['nota_2ev']);
    $nota2Final = trim($_POST['nota_2final']);

    // Sustituimos comas por puntos
    $nota1Ev = str_replace(',', '.', $nota1Ev);
    $nota1Final = str_replace(',', '.', $nota1Final);
    $nota2Ev = str_replace(',', '.', $nota2Ev);
    $nota2Final = str_replace(',', '.', $nota2Final);

    $notasCheck = [$nota1Ev, $nota1Final, $nota2Ev, $nota2Final];
    
    $errores = [];
    foreach ($notasCheck as $nota) {
        if (!empty($nota)) {
            if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
                $errores['notas'] = "Notas deben estar entre 0 y 10.";
                break;
            }
        }
    }

    if (empty($errores)) {
        $resultado = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1Ev, $nota1Final, $nota2Ev, $nota2Final, "");

        if ($resultado) {
            if (!empty($_POST['notificarEstudiante'])) {
                require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
                enviarEmailNotasEstudiante($idEstudiante);
            }
            $_SESSION['exito'] = "Calificación actualizada.";
            header("Location: ../../../vistas/profesores/calificaciones/lista.php");
            exit;
        }
        $_SESSION['error'] = "Error al guardar.";
    } else {
        $_SESSION['error'] = $errores['notas'];
    }

    header("Location: ../../../vistas/profesores/calificaciones/editar.php?id=" . $idCalificacion);
    exit;
}

header("Location: ../../../vistas/profesores/calificaciones/lista.php");
exit;
?>
