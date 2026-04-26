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

    for ($indiceEstudiante = 0; $indiceEstudiante < $cantidadEstudiantesAProcesar; $indiceEstudiante = $indiceEstudiante + 1) {
        $idDeEsteEstudiante = $listaIdsEstudiantes[$indiceEstudiante];
        $nota1EvAProcesar = $listaNotas1Ev[$indiceEstudiante];
        $nota1FinalAProcesar = $listaNotas1Final[$indiceEstudiante];
        $nota2EvAProcesar = $listaNotas2Ev[$indiceEstudiante];
        $nota2FinalAProcesar = $listaNotas2Final[$indiceEstudiante];
        $observacionAProcesar = $listaObservaciones[$indiceEstudiante];

        // Validar que cada nota sea numérica o esté vacía
        $arrayTemporalNotas = array($nota1EvAProcesar, $nota1FinalAProcesar, $nota2EvAProcesar, $nota2FinalAProcesar);
        
        foreach ($arrayTemporalNotas as $notaIndividualAChequear) {
            if ($notaIndividualAChequear != "") {
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
            for ($indiceNotif = 0; $indiceNotif < $cantidadEstudiantesAProcesar; $indiceNotif = $indiceNotif + 1) {
                $idEstudianteANotificar = $listaIdsEstudiantes[$indiceNotif];
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
