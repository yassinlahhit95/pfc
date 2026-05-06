<?php
session_start();
require_once __DIR__ . "/../../../modelos/calificaciones.php";

if (isset($_POST['guardarNotas'])) {
    $idModulo = trim($_POST['idModulo']);
    $idCiclo = trim($_POST['idCiclo']); 
    $estudiantes = $_POST['estudiantes'];
    
    // Recogemos los arrays de notas del formulario con nombres claros
    $notas_1ra_evaluacion = $_POST['notas_1ev'];
    $notas_1ra_final      = $_POST['notas_1final'];
    $notas_2da_evaluacion = $_POST['notas_2ev'];
    $notas_2da_final      = $_POST['notas_2final'];
    $todas_las_observaciones = $_POST['observaciones'];

    $hayError = false;

    // Recorremos los alumnos para guardar sus notas
    foreach ($estudiantes as $indice => $idEstudiante) {
        $idEstudiante = trim($idEstudiante);
        $nota1 = trim($notas_1ra_evaluacion[$indice]);
        $nota2 = trim($notas_1ra_final[$indice]);
        $nota3 = trim($notas_2da_evaluacion[$indice]);
        $nota4 = trim($notas_2da_final[$indice]);
        $comentario = trim($todas_las_observaciones[$indice]);

        // Validamos que si hay nota, sea un número entre 0 y 10
        $notas_del_alumno = [$nota1, $nota2, $nota3, $nota4];
        foreach ($notas_del_alumno as $nota) {
            if (!empty($nota) && (!is_numeric($nota) || $nota < 0 || $nota > 10)) {
                $hayError = true;
                break;
            }
        }

        if (!$hayError) {
            $resultado = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1, $nota2, $nota3, $nota4, $comentario);
            if (!$resultado) {
                $hayError = true;
            }
        }

        if ($hayError) break;
    }

    if (!$hayError) {
        // Enviar correos si se marca la opción
        if (isset($_POST['notificarEstudiantes']) && !empty($_POST['notificarEstudiantes'])) {
            require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
            foreach ($estudiantes as $idParaEnvio) {
                enviarEmailNotasEstudiante(trim($idParaEnvio));
            }
        }
        $_SESSION['exito'] = "Notas guardadas.";
    } else {
        $_SESSION['error'] = "Las notas deben ser números entre 0 y 10.";
    }

    header("Location: ../../../vistas/profesores/calificaciones/agregar.php?idCiclo=$idCiclo&idModulo=$idModulo");
    exit;
}

header("Location: ../../../vistas/profesores/calificaciones/agregar.php");
exit;
?>
