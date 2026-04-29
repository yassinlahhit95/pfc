<?php
session_start();
require_once "../../comunes/notificaciones_grades.php";

if (isset($_POST['idCiclo']) && !empty($_POST['idCiclo'])) {
    $idCiclo = intval($_POST['idCiclo']);
    $enviados = enviarEmailNotasClase($idCiclo);
    
    if ($enviados > 0) {
        $_SESSION['exito'] = strtoupper("Se han enviado $enviados correos electrónicos con éxito.");
    } else {
        $_SESSION['error'] = strtoupper("No se pudieron enviar los correos o no hay estudiantes en este ciclo.");
    }
} else {
    $_SESSION['error'] = strtoupper("ID de ciclo no proporcionado.");
}

$url_retorno = "/pfc/vistas/admin/dashboard.php";
if (isset($_SESSION['idProfesor'])) {
    $url_retorno = "/pfc/vistas/profesores/dashboard.php";
}

header("Location: " . $url_retorno);
exit;
?>

