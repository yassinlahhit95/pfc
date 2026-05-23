<?php
require_once __DIR__ . "/email_helper.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../modelos/tfg.php";

// Función para montar el HTML de las notas que se mandan por correo.
// El CSS va en linea para que se vea bien en el correo.
function generarTablaNotasHTML($idEstudianteRecibido) {
    // Pillamos los datos del alumno primero
    $datosEstudiante = obtenerEstudiantePorId($idEstudianteRecibido);

    if (empty($datosEstudiante)) {
        return false;
    }

    // Buscamos todas sus notas en la bd
    $listaCalificaciones = listarCalificacionesPorEstudiante($idEstudianteRecibido);

    $nombreEstudiante = $datosEstudiante['nombreEstudiante'];
    $nombreCiclo = $datosEstudiante['nombreCiclo'];

    $contenidoCorreoHTML = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
        <h2 style='color: #2c3e50;'>" . $nombreEstudiante . "</h2>
        <p>Calificaciones finales para el ciclo: <strong>" . $nombreCiclo . "</strong></p>

        <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
            <thead>
                <tr style='background-color: #3498db; color: white;'>
                    <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Módulo</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>1ª Ev</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>1ª Final</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>2ª Ev</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>2ª Final</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>Estado</th>
                </tr>
            </thead>
            <tbody>";

    $sumaTotalNotasModulos = 0;
    $contadorTotalModulos = 0;
    $existeAlgundoSuspenso = false;

    // Empezamos a recorrer los módulos para rellenar la tabla
    foreach ($listaCalificaciones as $datosDelModulo) {
        $nota1ev = null;
        if (isset($datosDelModulo['nota_1ev']) && is_numeric($datosDelModulo['nota_1ev'])) {
            $nota1ev = floatval($datosDelModulo['nota_1ev']);
        }

        $nota1final = null;
        if (isset($datosDelModulo['nota_1final']) && is_numeric($datosDelModulo['nota_1final'])) {
            $nota1final = floatval($datosDelModulo['nota_1final']);
        }

        $nota2ev = null;
        if (isset($datosDelModulo['nota_2ev']) && is_numeric($datosDelModulo['nota_2ev'])) {
            $nota2ev = floatval($datosDelModulo['nota_2ev']);
        }

        $nota2final = null;
        if (isset($datosDelModulo['nota_2final']) && is_numeric($datosDelModulo['nota_2final'])) {
            $nota2final = floatval($datosDelModulo['nota_2final']);
        }

        $notaDefinitiva1 = $nota1ev;
        if ($nota1final !== null) {
            $notaDefinitiva1 = max($nota1ev, $nota1final);
        }

        $notaDefinitiva2 = $nota2ev;
        if ($nota2final !== null) {
            $notaDefinitiva2 = max($nota2ev, $nota2final);
        }

        $sumaEvaluaciones = 0;
        $evaluacionesConNota = 0;
        if ($notaDefinitiva1 !== null) {
            $sumaEvaluaciones += $notaDefinitiva1;
            $evaluacionesConNota++;
        }
        if ($notaDefinitiva2 !== null) {
            $sumaEvaluaciones += $notaDefinitiva2;
            $evaluacionesConNota++;
        }

        $notaFinalDelModulo = 0;
        if ($evaluacionesConNota > 0) {
            $notaFinalDelModulo = $sumaEvaluaciones / $evaluacionesConNota;
        }

        $textoDelEstado = "APROBADO";
        $colorDelEstado = "green";
        if ($notaFinalDelModulo < 5) {
            $textoDelEstado = "SUSPENSO";
            $colorDelEstado = "red";
            $existeAlgundoSuspenso = true;
        }

        $sumaTotalNotasModulos = $sumaTotalNotasModulos + $notaFinalDelModulo;
        $contadorTotalModulos++;

        $nombreModulo = $datosDelModulo['nombreModulo'];

        $contenidoCorreoHTML .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . $nombreModulo . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $datosDelModulo['nota_1ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $datosDelModulo['nota_1final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $datosDelModulo['nota_2ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $datosDelModulo['nota_2final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: $colorDelEstado; font-weight: bold;'>" . $textoDelEstado . "</td>
                </tr>";
    }

    $promedioFinalModulos = 0;
    if ($contadorTotalModulos > 0) {
        $promedioFinalModulos = $sumaTotalNotasModulos / $contadorTotalModulos;
    }

    $promedioRetos = obtenerPromedioRetosEstudiante($idEstudianteRecibido);

    $notaFinalCalculada = ($promedioFinalModulos * 0.75) + ($promedioRetos * 0.25);
    $notaFinalFormateada = round($notaFinalCalculada, 2);

    $estadoGlobalTexto = "APROBADO";
    $colorGlobalTexto = "green";
    if ($notaFinalCalculada < 5 || $existeAlgundoSuspenso) {
        $estadoGlobalTexto = "SUSPENSO";
        $colorGlobalTexto = "red";
    }

    $contenidoCorreoHTML .= "
            </tbody>
        </table>

        <div style='margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-left: 5px solid #3498db;'>
            <p style='margin: 0; font-size: 16px;'><strong>RESUMEN ACADÉMICO FINAL:</strong></p>
            <p style='margin: 5px 0 0 0;'>Promedio Módulos (75%): <strong>" . round($promedioFinalModulos, 2) . "</strong></p>
            <p style='margin: 5px 0 0 0;'>Promedio Retos (25%): <strong>" . round($promedioRetos, 2) . "</strong></p>
            <p style='margin: 10px 0 0 0; font-size: 18px;'>NOTA FINAL CICLO: <strong>$notaFinalFormateada</strong></p>
            <p style='margin: 5px 0 0 0;'>ESTADO GLOBAL: <strong style='color: $colorGlobalTexto;'>$estadoGlobalTexto</strong></p>
        </div>

        <p style='font-size: 12px; color: #7f8c8d; margin-top: 20px;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
    </div>";

    $datosParaEnviar = [];
    $datosParaEnviar['html'] = $contenidoCorreoHTML;
    $datosParaEnviar['email'] = $datosEstudiante['emailEstudiante'];
    $datosParaEnviar['nombre'] = $datosEstudiante['nombreEstudiante'];

    return $datosParaEnviar;
}

function enviarEmailNotasEstudiante($idEstudianteAEnviar) {
    $datosFinales = generarTablaNotasHTML($idEstudianteAEnviar);
    if (!empty($datosFinales)) {
        $correoDestino = $datosFinales['email'];
        $asuntoMensaje = "Tus Calificaciones Finales - PFC";
        $cuerpoHTML = $datosFinales['html'];

        return sendEmail($correoDestino, $asuntoMensaje, $cuerpoHTML);
    }
    return false;
}

function enviarEmailNotasClase($idDelCicloElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idDelCicloElegido);

    $contadorCorreosEnviados = 0;
    foreach ($listaEstudiantes as $datosAlumnoIndividual) {
        $idDeEsteAlumno = $datosAlumnoIndividual['idEstudiante'];
        if (enviarEmailNotasEstudiante($idDeEsteAlumno)) {
            $contadorCorreosEnviados++;
        }
    }

    return $contadorCorreosEnviados;
}

function generarEmailCalificacionTFGHTML($idEstudiante) {
    $datosEstudiante = obtenerEstudiantePorId($idEstudiante);

    if (empty($datosEstudiante)) {
        return false;
    }

    $calificacion = obtenerCalificacionTFG($idEstudiante);

    if (empty($calificacion)) {
        return false;
    }

    $nombreEstudiante = $datosEstudiante['nombreEstudiante'];
    $nota = floatval($calificacion['nota']);
    $observaciones = $calificacion['observaciones'];

    $estadoTexto = "APROBADO";
    $colorEstado = "green";
    if ($nota < 5) {
        $estadoTexto = "SUSPENSO";
        $colorEstado = "red";
    }

    $contenidoHTML = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
        <h2 style='color: #2c3e50;'>" . $nombreEstudiante . "</h2>
        <p>Tu Trabajo Fin de Grado (TFG) ha sido calificado.</p>

        <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
            <thead>
                <tr style='background-color: #3498db; color: white;'>
                    <th style='padding: 10px; border: 1px solid #ddd; text-align: left;'>Concepto</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>Nota</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>Trabajo Fin de Grado (TFG)</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'><strong>" . number_format($nota, 2) . "</strong></td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: $colorEstado; font-weight: bold;'>" . $estadoTexto . "</td>
                </tr>
            </tbody>
        </table>";

    if (!empty($observaciones)) {
        $contenidoHTML .= "
        <div style='margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 5px solid #3498db;'>
            <p style='margin: 0;'><strong>Observaciones del profesor:</strong></p>
            <p style='margin: 5px 0 0 0;'>" . $observaciones . "</p>
        </div>";
    }

    $contenidoHTML .= "
        <p style='font-size: 12px; color: #7f8c8d; margin-top: 20px;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
    </div>";

    $datosParaEnviar = [];
    $datosParaEnviar['html'] = $contenidoHTML;
    $datosParaEnviar['email'] = $datosEstudiante['emailEstudiante'];
    $datosParaEnviar['nombre'] = $datosEstudiante['nombreEstudiante'];

    return $datosParaEnviar;
}

function enviarEmailCalificacionTFG($idEstudiante) {
    $datosFinales = generarEmailCalificacionTFGHTML($idEstudiante);
    if (!empty($datosFinales)) {
        $correoDestino = $datosFinales['email'];
        $asuntoMensaje = "Calificación de tu TFG - PFC";
        $cuerpoHTML = $datosFinales['html'];
        return sendEmail($correoDestino, $asuntoMensaje, $cuerpoHTML);
    }
    return false;
}
?>
