<?php
session_start();
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$hayError = false;

if (isset($_POST['insertarNota'])) {
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $idModulo = trim($_POST['idModulo'] ?? '');
    $nota1Ev = trim($_POST['nota_1ev'] ?? '');
    $nota1Final = trim($_POST['nota_1final'] ?? '');
    $nota2Ev = trim($_POST['nota_2ev'] ?? '');
    $nota2Final = trim($_POST['nota_2final'] ?? '');

    if (!is_numeric($nota1Ev) || !is_numeric($nota1Final) || !is_numeric($nota2Ev) || !is_numeric($nota2Final)) {
        $hayError = true;
        $_SESSION['error'] = "Notas deben ser números.";
    } 
    
    if (!$hayError) {
        $resultado = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1Ev, $nota1Final, $nota2Ev, $nota2Final, "");
        
        if ($resultado) {
            if (!empty($_POST['notificarEstudiante'])) {
                require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
                enviarEmailNotasEstudiante($idEstudiante);
            }
            $_SESSION['exito'] = "Calificación guardada.";
            header("Location: ../../../vistas/profesores/calificaciones/lista.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Error al guardar.";
        }
    }
}

header("Location: ../../../vistas/profesores/calificaciones/lista.php");
exit;
