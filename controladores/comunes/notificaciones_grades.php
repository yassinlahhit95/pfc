<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/email_helper.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../modelos/tfg.php";

// ══════════════════════════════════════════════════════════════════════
// GENERACIÓN DE HTML PARA CORREOS
// ══════════════════════════════════════════════════════════════════════
function generarTablaNotasHTML($idEstudianteRecibido) {
    $datosEstudiante = obtenerEstudiantePorId($idEstudianteRecibido);

    if (empty($datosEstudiante)) {
        return false;
    }

    $listaCalificaciones = listarCalificacionesPorEstudiante($idEstudianteRecibido);

    $nombreEstudiante = htmlspecialchars($datosEstudiante['nombreEstudiante'], ENT_QUOTES, 'UTF-8');
    $nombreCiclo      = htmlspecialchars($datosEstudiante['nombreCiclo'] ?? '', ENT_QUOTES, 'UTF-8');

    // El CSS va en línea para compatibilidad máxima con clientes de correo
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

    $sumaTotalNotasModulos  = 0;
    $contadorTotalModulos   = 0;
    $existeAlgundoSuspenso  = false;

    foreach ($listaCalificaciones as $datosDelModulo) {
        $nota1ev = isset($datosDelModulo['nota_1ev']) && is_numeric($datosDelModulo['nota_1ev'])
            ? floatval($datosDelModulo['nota_1ev']) : null;
        $nota1final = isset($datosDelModulo['nota_1final']) && is_numeric($datosDelModulo['nota_1final'])
            ? floatval($datosDelModulo['nota_1final']) : null;
        $nota2ev = isset($datosDelModulo['nota_2ev']) && is_numeric($datosDelModulo['nota_2ev'])
            ? floatval($datosDelModulo['nota_2ev']) : null;
        $nota2final = isset($datosDelModulo['nota_2final']) && is_numeric($datosDelModulo['nota_2final'])
            ? floatval($datosDelModulo['nota_2final']) : null;

        $notaDefinitiva1 = $nota1final !== null ? max($nota1ev, $nota1final) : $nota1ev;
        $notaDefinitiva2 = $nota2final !== null ? max($nota2ev, $nota2final) : $nota2ev;

        $sumaEvaluaciones   = 0;
        $evaluacionesConNota = 0;
        if ($notaDefinitiva1 !== null) { $sumaEvaluaciones += $notaDefinitiva1; $evaluacionesConNota++; }
        if ($notaDefinitiva2 !== null) { $sumaEvaluaciones += $notaDefinitiva2; $evaluacionesConNota++; }

        $notaFinalDelModulo = $evaluacionesConNota > 0 ? $sumaEvaluaciones / $evaluacionesConNota : 0;

        $textoDelEstado = "APROBADO";
        $colorDelEstado = "green";
        if ($notaFinalDelModulo < 5) {
            $textoDelEstado        = "SUSPENSO";
            $colorDelEstado        = "red";
            $existeAlgundoSuspenso = true;
        }

        $sumaTotalNotasModulos += $notaFinalDelModulo;
        $contadorTotalModulos++;

        $nombreModuloEsc = htmlspecialchars($datosDelModulo['nombreModulo'], ENT_QUOTES, 'UTF-8');
        $contenidoCorreoHTML .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . $nombreModuloEsc . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . (float)$datosDelModulo['nota_1ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . (float)$datosDelModulo['nota_1final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . (float)$datosDelModulo['nota_2ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . (float)$datosDelModulo['nota_2final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: $colorDelEstado; font-weight: bold;'>" . $textoDelEstado . "</td>
                </tr>";
    }

    $promedioFinalModulos = $contadorTotalModulos > 0 ? $sumaTotalNotasModulos / $contadorTotalModulos : 0;
    $promedioRetos        = obtenerPromedioRetosEstudiante($idEstudianteRecibido);
    $notaFinalCalculada   = ($promedioFinalModulos * 0.75) + ($promedioRetos * 0.25);
    $notaFinalFormateada  = round($notaFinalCalculada, 2);

    $estadoGlobalTexto = "APROBADO";
    $colorGlobalTexto  = "green";
    if ($notaFinalCalculada < 5 || $existeAlgundoSuspenso) {
        $estadoGlobalTexto = "SUSPENSO";
        $colorGlobalTexto  = "red";
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

    return [
        'html'   => $contenidoCorreoHTML,
        'email'  => $datosEstudiante['emailEstudiante'],
        'nombre' => $datosEstudiante['nombreEstudiante'],
    ];
}

// ══════════════════════════════════════════════════════════════════════
// GENERACIÓN DE HTML PARA CALIFICACIÓN TFG
// ══════════════════════════════════════════════════════════════════════
function generarEmailCalificacionTFGHTML($idEstudiante) {
    $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
    if (empty($datosEstudiante)) return false;

    $calificacion = obtenerCalificacionTFG($idEstudiante);
    if (empty($calificacion)) return false;

    $nombreEstudiante = htmlspecialchars($datosEstudiante['nombreEstudiante'], ENT_QUOTES, 'UTF-8');
    $nota             = floatval($calificacion['nota']);
    $observaciones    = htmlspecialchars($calificacion['observaciones'] ?? '', ENT_QUOTES, 'UTF-8');

    $estadoTexto = $nota < 5 ? "SUSPENSO" : "APROBADO";
    $colorEstado = $nota < 5 ? "red" : "green";

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

    return [
        'html'   => $contenidoHTML,
        'email'  => $datosEstudiante['emailEstudiante'],
        'nombre' => $datosEstudiante['nombreEstudiante'],
    ];
}

// ══════════════════════════════════════════════════════════════════════
// FUNCIONES DE ENVÍO
// ══════════════════════════════════════════════════════════════════════
function enviarEmailNotasEstudiante($idEstudianteAEnviar) {
    $datosFinales = generarTablaNotasHTML($idEstudianteAEnviar);
    if (!empty($datosFinales)) {
        require_once __DIR__ . '/../../modelos/configuracion.php';
        $cfg = obtenerConfiguracionCentro();
        $nombreCentro = $cfg['nombreCentro'] ?? 'AulaPro';
        return sendEmail($datosFinales['email'], "$nombreCentro | Tus Calificaciones Finales", $datosFinales['html']);
    }
    return false;
}

function enviarEmailNotasClase($idDelCicloElegido) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idDelCicloElegido);
    $contadorCorreosEnviados = 0;
    foreach ($listaEstudiantes as $datosAlumno) {
        if (enviarEmailNotasEstudiante($datosAlumno['idEstudiante'])) {
            $contadorCorreosEnviados++;
        }
    }
    return $contadorCorreosEnviados;
}

// Async variant: builds each email and inserts into cola_emails instead of sending inline.
// The cron job (cron/procesar_cola_emails.php) picks them up and delivers them.
function encolarEmailsNotasClase(int $idCiclo): int {
    require_once __DIR__ . '/../../modelos/cola_emails.php';
    require_once __DIR__ . '/../../modelos/configuracion.php';
    $cfg = obtenerConfiguracionCentro();
    $nombreCentro = $cfg['nombreCentro'] ?? 'AulaPro';
    $listaEstudiantes = listarEstudiantesPorCiclo($idCiclo);
    $encolados = 0;
    foreach ($listaEstudiantes as $datosAlumno) {
        $datos = generarTablaNotasHTML($datosAlumno['idEstudiante']);
        if (!$datos) continue;
        if (encolarEmail($datos['email'], $datos['nombre'], "$nombreCentro | Tus Calificaciones Finales", $datos['html'])) {
            $encolados++;
        }
    }
    return $encolados;
}

function enviarEmailCalificacionTFG($idEstudiante) {
    $datosFinales = generarEmailCalificacionTFGHTML($idEstudiante);
    if (!empty($datosFinales)) {
        require_once __DIR__ . '/../../modelos/configuracion.php';
        $cfg = obtenerConfiguracionCentro();
        $nombreCentro = $cfg['nombreCentro'] ?? 'AulaPro';
        return sendEmail($datosFinales['email'], "$nombreCentro | Calificación de tu Proyecto Final TFG", $datosFinales['html']);
    }
    return false;
}
