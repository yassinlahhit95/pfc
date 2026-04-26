<?php
require_once "email_helper.php";
require_once __DIR__ . "/../../modelos/conectar.php";

/**
 * Genera el HTML de la tabla de notas con nombres de variables claros
 */
function generarTablaNotasHTML($idEstudianteRecibido) {
    $conexionBaseDatos = obtenerConexion();
    
    // Obtener información del estudiante y su ciclo
    $consultaEstudiante = "SELECT estudiantes.nombreEstudiante, estudiantes.emailEstudiante, ciclos.nombreCiclo 
                           FROM estudiantes 
                           LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                           WHERE estudiantes.idEstudiante = $idEstudianteRecibido";
    
    $resultadoEstudiante = mysqli_query($conexionBaseDatos, $consultaEstudiante);
    $datosEstudiante = mysqli_fetch_assoc($resultadoEstudiante);
    
    if (empty($datosEstudiante)) {
        return false;
    }

    // Obtener todas las calificaciones del estudiante
    $consultaNotas = "SELECT modulos.nombreModulo, cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final 
                      FROM calificaciones_modulos cm
                      JOIN modulos ON cm.idModulo = modulos.idModulo
                      WHERE cm.idEstudiante = $idEstudianteRecibido";
    
    $resultadoNotas = mysqli_query($conexionBaseDatos, $consultaNotas);

    $nombreEstudianteMayusculas = strtoupper($datosEstudiante['nombreEstudiante']);
    $nombreCicloMayusculas = strtoupper($datosEstudiante['nombreCiclo']);

    $contenidoCorreoHTML = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
        <h2 style='color: #2c3e50;'>Hola, " . $nombreEstudianteMayusculas . "</h2>
        <p>A continuación te enviamos tus calificaciones finales para el ciclo: <strong>" . $nombreCicloMayusculas . "</strong></p>
        
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

    while ($datosDelModulo = mysqli_fetch_assoc($resultadoNotas)) {
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
        $contadorTotalModulos = $contadorTotalModulos + 1;

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
    
    // Obtener promedio de retos
    $consultaRetos = "SELECT AVG(nota) as promedio_retos FROM calificaciones_retos WHERE idEstudiante = $idEstudianteRecibido";
    $resultadoRetos = mysqli_query($conexionBaseDatos, $consultaRetos);
    $datosRetos = mysqli_fetch_assoc($resultadoRetos);
    
    $promedioRetos = 0;
    if (isset($datosRetos['promedio_retos']) && is_numeric($datosRetos['promedio_retos'])) {
        $promedioRetos = $datosRetos['promedio_retos'];
    }

    $notaFinalCalculada = ($promedioFinalModulos * 0.75) + ($promedioRetos * 0.25);
    $notaFinalFormateada = round($notaFinalCalculada, 2);

    $estadoGlobalTexto = "APROBADO";
    $colorGlobalTexto = "green";
    if ($notaFinalCalculada < 5 || $existeAlgundoSuspenso == true) {
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

    mysqli_close($conexionBaseDatos);
    
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
    $conexionBaseDatos = obtenerConexion();
    $consultaTodosAlumnos = "SELECT idEstudiante FROM estudiantes WHERE idCiclo = $idDelCicloElegido";
    $resultadoAlumnos = mysqli_query($conexionBaseDatos, $consultaTodosAlumnos);
    
    $contadorCorreosEnviados = 0;
    while ($datosAlumnoIndividual = mysqli_fetch_assoc($resultadoAlumnos)) {
        $idDeEsteAlumno = $datosAlumnoIndividual['idEstudiante'];
        if (enviarEmailNotasEstudiante($idDeEsteAlumno)) {
            $contadorCorreosEnviados = $contadorCorreosEnviados + 1;
        }
    }
    
    mysqli_close($conexionBaseDatos);
    return $contadorCorreosEnviados;
}
?>
