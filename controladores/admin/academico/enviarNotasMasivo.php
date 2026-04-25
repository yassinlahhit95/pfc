<?php
session_start();
require_once "../../comunes/notificaciones_grades.php";

if (isset($_POST['idCiclo'])) {
    $idCiclo = intval($_POST['idCiclo']);
    $enviados = enviarEmailNotasClase($idCiclo);
    
    if ($enviados > 0) {
        $_SESSION['exito'] = "Se han enviado $enviados correos electrónicos con éxito.";
    } else {
        $_SESSION['error'] = "No se pudieron enviar los correos o no hay estudiantes en este ciclo.";
    }
} else {
    $_SESSION['error'] = "ID de ciclo no proporcionado.";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
