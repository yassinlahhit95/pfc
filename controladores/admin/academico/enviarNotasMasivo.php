<?php
session_start();
require_once __DIR__ . "/../../comunes/notificaciones_grades.php";

$hayError = false;

if (isset($_POST['idCiclo']) && !empty($_POST['idCiclo'])) {
    $idCiclo = intval(trim($_POST['idCiclo']));
    
    require_once __DIR__ . "/../../../modelos/estudiantes.php";
    $estudiantesEnCiclo = listarEstudiantesPorCiclo($idCiclo);
    
    if (empty($estudiantesEnCiclo)) {
        $_SESSION['error'] = "No hay estudiantes registrados en este ciclo para enviar correos.";
    } else {
        $enviados = enviarEmailNotasClase($idCiclo);
        
        if ($enviados > 0) {
            $_SESSION['exito'] = "Se han enviado $enviados correos electrónicos correctamente.";
        } else {
            $ultimoError = $_SESSION['ultimo_error_email'] ?? 'Sin respuesta del servidor';
            $_SESSION['error'] = "Error crítico: No se pudo enviar ningún correo. Detalle: $ultimoError";
            unset($_SESSION['ultimo_error_email']);
        }
    }
} else {
    $_SESSION['error'] = "No se proporcionó el ID del ciclo.";
}

header("Location: ../../../vistas/admin/academico/resultadosFinales.php?idCiclo=" . ($idCiclo ?? ''));
exit;
?>
