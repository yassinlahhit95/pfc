<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['guardarNotasReto'])) {
    $idReto = trim($_POST['idReto']);
    $idCiclo = trim($_POST['idCiclo']);
    $idModulo = trim($_POST['idModulo']);
    
    $listaIdsEstudiantes = $_POST['estudiantes'] ?? [];
    $listaNotas = $_POST['notas'] ?? [];

    for ($i = 0; $i < count($listaIdsEstudiantes); $i++) {
        $idEstudiante = trim($listaIdsEstudiantes[$i]);
        $nota = trim($listaNotas[$i]);

        if (!empty($nota)) {
            if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
                $hayError = true;
            }
        }

        if (!$hayError) {
            if ($nota === '') {
                eliminarCalificacionReto($idEstudiante, $idReto);
            } else {
                if (!calificarReto($idEstudiante, $idReto, $nota)) {
                    $hayError = true;
                }
            }
        }
        
        if ($hayError) break;
    }

    if ($hayError) {
        $_SESSION['error'] = "Error al procesar las notas. Deben estar entre 0 y 10.";
    } else {
        $_SESSION['exito'] = "Calificaciones del reto guardadas.";
    }

    header("Location: ../../../vistas/admin/academico/calificacionesRetos.php?idCiclo=$idCiclo&idModulo=$idModulo&idReto=$idReto");
    exit;
}

header("Location: ../../../vistas/admin/academico/calificacionesRetos.php");
exit;


