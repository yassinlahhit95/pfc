<?php
session_start();
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$hayError = false;

if (isset($_POST['guardarNotas'])) {
    $idModulo = trim($_POST['idModulo'] ?? '');
    $listaIdsEstudiantes = $_POST['estudiantes'] ?? [];
    $listaNotas1Ev = $_POST['notas_1ev'] ?? [];
    $listaNotas1Final = $_POST['notas_1final'] ?? [];
    $listaNotas2Ev = $_POST['notas_2ev'] ?? [];
    $listaNotas2Final = $_POST['notas_2final'] ?? [];
    $listaObservaciones = $_POST['observaciones'] ?? [];

    $cantidadEstudiantes = count($listaIdsEstudiantes);

    for ($i = 0; $i < $cantidadEstudiantes; $i++) {
        $idEstudiante = trim($listaIdsEstudiantes[$i]);
        $nota1Ev = trim($listaNotas1Ev[$i]);
        $nota1Final = trim($listaNotas1Final[$i]);
        $nota2Ev = trim($listaNotas2Ev[$i]);
        $nota2Final = trim($listaNotas2Final[$i]);
        $observacion = trim($listaObservaciones[$i]);

        $arrayTemporalNotas = [$nota1Ev, $nota1Final, $nota2Ev, $nota2Final];
        
        foreach ($arrayTemporalNotas as $notaIndividual) {
            if (!empty($notaIndividual)) {
                if (!is_numeric($notaIndividual) || $notaIndividual < 0 || $notaIndividual > 10) {
                    $hayError = true;
                    break;
                }
            }
        }

        if (!$hayError) {
            $resultado = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1Ev, $nota1Final, $nota2Ev, $nota2Final, $observacion);
            if (!$resultado) {
                $hayError = true;
            }
        }
        
        if ($hayError) break;
    }

    if (!$hayError) {
        require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
        
        if (!empty($_POST['notificarEstudiantes'])) {
            for ($i = 0; $i < $cantidadEstudiantes; $i++) {
                enviarEmailNotasEstudiante($listaIdsEstudiantes[$i]);
            }
        }
        $_SESSION['exito'] = "Calificaciones guardadas.";
    } else {
        $_SESSION['error'] = "Error al procesar las notas. Deben ser números entre 0 y 10.";
    }

    $idCiclo = trim($_POST['idCiclo'] ?? '');
    header("Location: ../../../vistas/admin/academico/calificacionesModulos.php?idCiclo=" . $idCiclo . "&idModulo=" . $idModulo);
    exit;
}

header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
exit;
