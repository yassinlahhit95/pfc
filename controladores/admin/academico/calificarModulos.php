<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $idModuloRecibido = $_POST['idModulo'];
    $listaIdsEstudiantes = $_POST['estudiantes'];
    $listaNotas1Ev = $_POST['notas_1ev'];
    $listaNotas1Final = $_POST['notas_1final'];
    $listaNotas2Ev = $_POST['notas_2ev'];
    $listaNotas2Final = $_POST['notas_2final'];
    $listaObservaciones = $_POST['observaciones'];

    $errorDetectadoAlGuardar = false;
    $cantidadEstudiantesAProcesar = count($listaIdsEstudiantes);

    for ($i = 0; $i < $cantidadEstudiantesAProcesar; $i++) {
        $idDeEsteEstudiante = $listaIdsEstudiantes[$i];
        $nota1EvAProcesar = $listaNotas1Ev[$i];
        $nota1FinalAProcesar = $listaNotas1Final[$i];
        $nota2EvAProcesar = $listaNotas2Ev[$i];
        $nota2FinalAProcesar = $listaNotas2Final[$i];
        $observacionAProcesar = $listaObservaciones[$i];

        // Validar que cada nota sea numérica o esté vacía
        $arrayTemporalNotas = array($nota1EvAProcesar, $nota1FinalAProcesar, $nota2EvAProcesar, $nota2FinalAProcesar);
        
        foreach ($arrayTemporalNotas as $notaIndividualAChequear) {
            if (!empty($notaIndividualAChequear)) {
                if (!is_numeric($notaIndividualAChequear)) {
                    $errorDetectadoAlGuardar = true;
                } else {
                    if ($notaIndividualAChequear < 0 || $notaIndividualAChequear > 10) {
                        $errorDetectadoAlGuardar = true;
                    }
                }
            }
        }

        // Si no hay errores de validación, guardamos en base de datos
        if ($errorDetectadoAlGuardar == false) {
            $resultadoOperacion = actualizarOCrearNotaCompleta($idDeEsteEstudiante, $idModuloRecibido, $nota1EvAProcesar, $nota1FinalAProcesar, $nota2EvAProcesar, $nota2FinalAProcesar, $observacionAProcesar);
            if ($resultadoOperacion == false) {
                $errorDetectadoAlGuardar = true;
            }
        }
    }

    // Si todo salió bien, procesamos notificaciones si se solicitaron
    if ($errorDetectadoAlGuardar == false) {
        require_once "../../comunes/notificaciones_grades.php";
        
        if (isset($_POST['notificarEstudiantes']) && !empty($_POST['notificarEstudiantes'])) {
            for ($i = 0; $i < $cantidadEstudiantesAProcesar; $i++) {
                $idEstudianteANotificar = $listaIdsEstudiantes[$i];
                enviarEmailNotasEstudiante($idEstudianteANotificar);
            }
        }
        $_SESSION['exito'] = strtoupper("Calificaciones guardadas con éxito.");
    } else {
        $_SESSION['error'] = strtoupper("Hubo errores al procesar algunas notas. Asegúrese de que sean números entre 0 y 10.");
    }

    $urlRedireccion = "/pfc/vistas/admin/academico/calificacionesModulos.php?idModulo=" . $idModuloRecibido;
    header("Location: " . $urlRedireccion);
    exit;
}

header("Location: /pfc/vistas/admin/academico/calificacionesModulos.php");
exit;

