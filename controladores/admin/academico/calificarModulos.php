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

        if (!empty($nota1Ev) && (!is_numeric($nota1Ev) || $nota1Ev < 0 || $nota1Ev > 10)) { $hayError = true; }
        if (!$hayError && !empty($nota1Final) && (!is_numeric($nota1Final) || $nota1Final < 0 || $nota1Final > 10)) { $hayError = true; }
        if (!$hayError && !empty($nota2Ev) && (!is_numeric($nota2Ev) || $nota2Ev < 0 || $nota2Ev > 10)) { $hayError = true; }
        if (!$hayError && !empty($nota2Final) && (!is_numeric($nota2Final) || $nota2Final < 0 || $nota2Final > 10)) { $hayError = true; }

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
        
        $correosEnviados = 0;
        $intentosCorreos = 0;

        if (!empty($_POST['notificarEstudiantes'])) {
            for ($i = 0; $i < $cantidadEstudiantes; $i++) {
                $intentosCorreos++;
                if (enviarEmailNotasEstudiante($listaIdsEstudiantes[$i])) {
                    $correosEnviados++;
                }
            }
        }

        if ($intentosCorreos > 0) {
            if ($correosEnviados === $intentosCorreos) {
                $_SESSION['exito'] = "Calificaciones guardadas y todos los correos enviados ($correosEnviados).";
            } elseif ($correosEnviados > 0) {
                $_SESSION['exito'] = "Calificaciones guardadas. Se enviaron $correosEnviados correos, pero algunos fallaron.";
            } else {
                $_SESSION['error'] = "Notas guardadas, pero NO se pudo enviar ningún correo. Revisa la configuración de Brevo.";
            }
        } else {
            $_SESSION['exito'] = "Calificaciones guardadas correctamente.";
        }
    } else {
        $_SESSION['error'] = "Error al procesar las notas. Deben ser números entre 0 y 10.";
    }

    $idCiclo = trim($_POST['idCiclo'] ?? '');
    header("Location: ../../../vistas/admin/academico/calificacionesModulos.php?idCiclo=" . $idCiclo . "&idModulo=" . $idModulo);
    exit;
}

header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
exit;
?>
