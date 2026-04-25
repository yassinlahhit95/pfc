<?php
require_once "email_helper.php";
require_once __DIR__ . "/../../modelos/conectar.php";

/**
 * Genera el HTML de la tabla de notas
 */
function generarTablaNotasHTML($idEstudiante) {
    $conexion = obtenerConexion();
    
    // Obtener información del estudiante y su ciclo
    $sqlEst = "SELECT e.nombreEstudiante, e.emailEstudiante, c.nombreCiclo 
               FROM estudiantes e 
               LEFT JOIN ciclos c ON e.idCiclo = c.idCiclo 
               WHERE e.idEstudiante = $idEstudiante";
    $resEst = mysqli_query($conexion, $sqlEst);
    $estudiante = mysqli_fetch_assoc($resEst);
    
    if (!$estudiante) return false;

    // Obtener todas las calificaciones del estudiante
    $sqlNotas = "SELECT m.nombreModulo, cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final 
                 FROM calificaciones_modulos cm
                 JOIN modulos m ON cm.idModulo = m.idModulo
                 WHERE cm.idEstudiante = $idEstudiante";
    $resNotas = mysqli_query($conexion, $sqlNotas);

    $html = "
    <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
        <h2 style='color: #2c3e50;'>Hola, " . $estudiante['nombreEstudiante'] . "</h2>
        <p>A continuación te enviamos tus calificaciones finales para el ciclo: <strong>" . $estudiante['nombreCiclo'] . "</strong></p>
        
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

    $sumaNotasModulos = 0;
    $totalPesosModulos = 0;
    $haySuspenso = false;

    while ($fila = mysqli_fetch_assoc($resNotas)) {
        // Determinamos la nota del módulo siguiendo la lógica del dashboard:
        // Se promedian los campos que tengan nota > 0
        $notasDeEsteModulo = [];
        if ($fila['nota_1ev'] > 0) $notasDeEsteModulo[] = $fila['nota_1ev'];
        if ($fila['nota_1final'] > 0) $notasDeEsteModulo[] = $fila['nota_1final'];
        if ($fila['nota_2ev'] > 0) $notasDeEsteModulo[] = $fila['nota_2ev'];
        if ($fila['nota_2final'] > 0) $notasDeEsteModulo[] = $fila['nota_2final'];
        
        $notaModulo = count($notasDeEsteModulo) > 0 ? array_sum($notasDeEsteModulo) / count($notasDeEsteModulo) : 0;
        
        // Si la nota final (el último intento o el promedio) es < 5, es suspenso
        $esSuspensoModulo = ($notaModulo < 5);
        if ($esSuspensoModulo) $haySuspenso = true;

        $estado = !$esSuspensoModulo ? "<span style='color: green; font-weight: bold;'>Aprobado</span>" : "<span style='color: red; font-weight: bold;'>Suspenso</span>";
        
        $sumaNotasModulos += $notaModulo;
        $totalPesosModulos++;

        $html .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #ddd;'>" . $fila['nombreModulo'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $fila['nota_1ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $fila['nota_1final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $fila['nota_2ev'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $fila['nota_2final'] . "</td>
                    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $estado . "</td>
                </tr>";
    }

    // Media de módulos (75%)
    $mediaModulos = ($totalPesosModulos > 0) ? ($sumaNotasModulos / $totalPesosModulos) : 0;
    
    // Obtenemos media de retos (25%) para coincidir con resultadosFinales.php
    $sqlRetos = "SELECT AVG(nota) as media_retos FROM calificaciones_retos WHERE idEstudiante = $idEstudiante";
    $resRetos = mysqli_query($conexion, $sqlRetos);
    $filaRetos = mysqli_fetch_assoc($resRetos);
    $mediaRetos = isset($filaRetos['media_retos']) ? floatval($filaRetos['media_retos']) : 0;

    // Cálculo Final (75% Módulos + 25% Retos)
    $notaFinalCiclo = round(($mediaModulos * 0.75) + ($mediaRetos * 0.25), 2);

    // Un estudiante solo aprueba si su media es >= 5 Y NO tiene ningún módulo suspenso
    $aprobadoGlobal = ($notaFinalCiclo >= 5 && !$haySuspenso);
    $estadoGlobal = $aprobadoGlobal ? "<strong style='color: green;'>APROBADO</strong>" : "<strong style='color: red;'>SUSPENSO</strong>";

    $html .= "
            </tbody>
        </table>
        
        <div style='margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-left: 5px solid #3498db;'>
            <p style='margin: 0; font-size: 16px;'><strong>Resumen Académico Final:</strong></p>
            <p style='margin: 5px 0 0 0;'>Promedio Módulos (75%): <strong>" . round($mediaModulos, 2) . "</strong></p>
            <p style='margin: 5px 0 0 0;'>Promedio Retos (25%): <strong>" . round($mediaRetos, 2) . "</strong></p>
            <p style='margin: 10px 0 0 0; font-size: 18px;'>Nota Final Ciclo: <strong>$notaFinalCiclo</strong></p>
            <p style='margin: 5px 0 0 0;'>Estado Global: $estadoGlobal " . ($haySuspenso ? "<br><small style='color: red;'>(Tiene módulos pendientes)</small>" : "") . "</p>
        </div>
        
        <p style='font-size: 12px; color: #7f8c8d; margin-top: 20px;'>Este es un mensaje automático, por favor no respondas a este correo.</p>
    </div>";

    mysqli_close($conexion);
    return ['html' => $html, 'email' => $estudiante['emailEstudiante'], 'nombre' => $estudiante['nombreEstudiante']];
}

/**
 * Envía las notas a un estudiante específico
 */
function enviarEmailNotasEstudiante($idEstudiante) {
    $datos = generarTablaNotasHTML($idEstudiante);
    if ($datos) {
        return sendEmail($datos['email'], "Tus Calificaciones Finales - PFC", $datos['html']);
    }
    return false;
}

/**
 * Envía las notas a todos los estudiantes de un ciclo/clase
 */
function enviarEmailNotasClase($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT idEstudiante FROM estudiantes WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    
    $enviados = 0;
    while ($fila = mysqli_fetch_assoc($resultado)) {
        if (enviarEmailNotasEstudiante($fila['idEstudiante'])) {
            $enviados++;
        }
    }
    
    mysqli_close($conexion);
    return $enviados;
}
?>
