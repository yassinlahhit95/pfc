<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_POST['calificarTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $nota = trim($_POST['nota']);
    $nota = str_replace(',', '.', $nota);
    $observaciones = trim($_POST['observaciones']);

    $errores = [];

    if (!is_numeric($nota)) {
        $errores['nota'] = "La nota debe ser un número.";
    } elseif ($nota < 0 || $nota > 10) {
        $errores['nota'] = "La nota debe estar entre 0 y 10.";
    }

    if (empty($errores)) {
        $resultado = guardarCalificacionTFG($idEstudiante, $nota, $observaciones);

        if ($resultado) {
            if (!empty($_POST['notificarEstudiante'])) {
                require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
                require_once __DIR__ . "/../../firebase/firebase_helper.php";
                enviarEmailCalificacionTFG($idEstudiante);
                $tokenEstudiante = obtenerTokenUsuario($idEstudiante, 'estudiante');
                if ($tokenEstudiante) {
                    enviarNotificacionFirebase($tokenEstudiante, "Calificación TFG", "Tu TFG ha sido calificado con un " . $nota . " sobre 10.");
                }
            }
            $_SESSION['exito'] = "Calificación del TFG guardada correctamente.";
        } else {
            $_SESSION['errores'] = "Error al guardar la calificación.";
        }
    } else {
        $_SESSION['errores'] = $errores['nota'];
    }
}

$origen = $_POST['origen'] ?? '';
if ($origen === 'calificacionesTFG') {
    header("Location: ../../../vistas/profesores/calificaciones/tfg.php");
} else {
    header("Location: ../../../vistas/profesores/pfc/lista.php");
}
exit;
?>
