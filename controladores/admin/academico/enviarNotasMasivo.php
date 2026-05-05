<?php
session_start();
require_once __DIR__ . "/../../comunes/notificaciones_grades.php";

$hayError = false;

if (isset($_POST['idCiclo']) && !empty($_POST['idCiclo'])) {
    $idCiclo = intval(trim($_POST['idCiclo']));
    $enviados = enviarEmailNotasClase($idCiclo);
    
    if ($enviados > 0) {
        $_SESSION['exito'] = "Se han enviado $enviados correos electrónicos.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al enviar correos o no hay estudiantes en este ciclo.";
    }
} else {
    $hayError = true;
    $_SESSION['error'] = "No se proporcionó el ID del ciclo.";
}

$urlRetorno = "/pfc/vistas/admin/dashboard.php";
if (isset($_SESSION['idProfesor'])) {
    $urlRetorno = "/pfc/vistas/profesores/dashboard.php";
}

header("Location: " . $urlRetorno);
exit;
