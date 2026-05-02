<?php
require_once __DIR__ . "/email_helper.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";
require_once __DIR__ . "/../../modelos/retos.php";

/**
 * Genera el HTML de la tabla de notas con nombres de variables claros
 */
function generarTablaNotasHTML($idEstudianteRecibido) {
    // Obtener información del estudiante y su ciclo desde el modelo
    $datosEstudiante = obtenerEstudiantePorId($idEstudianteRecibido);
    
    if (empty($datosEstudiante)) {
        return false;
    }

    // Obtener todas las calificaciones del estudiante desde el modelo
    $listaCalificaciones = listarCalificacionesPorEstudiante($idEstudianteRecibido);

    $nombreEstudianteMayusculas = strtoupper($datosEstudiante['nombreEstudiante']);
    $nombreCicloMayusculas = strtoupper($datosEstudiante['nombreCiclo']);

    $contenidoCorreoHTML = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
        <h2 style='color: #2c3e50;'>" . $nombreEstudianteMayusculas . "</h2>
        <p>Calificaciones finales para el ciclo: <strong>" . $nombreCicloMayusculas . "</strong></p>
        
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

    foreach ($listaCalificaciones as $datosDelModulo) {
        $listaNotasTemporales = [];
        if (is_numeric($datosDelModulo['nota_1ev']) && $datosDelModulo['nota_1ev'] > 0) { $listaNotasTemporales[] = $datosDelModulo['nota_1ev']; }
        if (is_numeric($datosDelModulo['nota_1final']) && $datosDelModulo['nota_1final'] > 0) { $listaNotasTemporales[] = $datosDelModulo['nota_1final']; }
        if (is_numeric($datosDelModulo['nota_2ev']) && $datosDelModulo['nota_2ev'] > 0) { $listaNotasTemporales[] = $datosDelModulo['nota_2ev']; }
        if (is_numeric($datosDelModulo['nota_2final']) && $datosDelModulo['nota_2final'] > 0) { $listaNotasTemporales[] = $datosDelModulo['nota_2final']; }
        
        $notaFinalDelModulo = 0;
        $cantidadNotasModulo = count($listaNotasTemporales);
        if ($cantidadNotasModulo > 0) {
            $notaFinalDelModulo = array_sum($listaNotasTemporales) / $cantidadNotasModulo;
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

        $nombreModuloMayus = strtoupper($datosDelModulo['nombreModulo']);

        $contenidoCorreoHTML .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . $nombreModuloMayus . "</td>
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
    
    // Obtener promedio de retos desde el modelo
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

/**
 * Envía las notas a un estudiante específico
 */
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

/**
 * Envía las notas a todos los estudiantes de un ciclo
 */
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
?>