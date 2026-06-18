<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";


if (isset($_POST['calificarTFG'])) {
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $nota = trim($_POST['nota']);
    $nota = str_replace(',', '.', $nota);
    $observaciones = trim($_POST['observaciones']);

    $errores = '';

    if (!is_numeric($nota)) {
        $errores = "La nota debe ser un número.";
    } elseif ($nota < 0 || $nota > 10) {
        $errores = "La nota debe estar entre 0 y 10.";
    }

    if (!$errores) {
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
        $_SESSION['errores'] = $errores;
    }
}

header("Location: ../../../vistas/admin/academico/evaluarTFG.php?idEstudiante=" . (int)($idEstudiante ?? 0));
exit;
?>
