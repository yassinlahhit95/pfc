<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// RESULTADOS DE APRENDIZAJE (RA)
// ══════════════════════════════════════════════════════════════════════

function listarRAPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM resultados_aprendizaje WHERE idModulo = ? ORDER BY codigo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// CRITERIOS DE EVALUACIÓN (CE)
// ══════════════════════════════════════════════════════════════════════

function listarCEPorRA($idRA) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM criterios_evaluacion WHERE idRA = ? ORDER BY codigo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idRA);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// CALIFICACIONES
// ══════════════════════════════════════════════════════════════════════

function obtenerCalificacionesPorModuloYEstudiante($idModulo, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT c.idCE, c.nota
            FROM calificaciones_ce c
            INNER JOIN criterios_evaluacion ce ON c.idCE = ce.idCE
            INNER JOIN resultados_aprendizaje ra ON ce.idRA = ra.idRA
            WHERE ra.idModulo = ? AND c.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $notas = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $notas[$fila['idCE']] = $fila['nota'];
    }
    return $notas;
}

// ══════════════════════════════════════════════════════════════════════
// CÁLCULO DE NOTA — media de un módulo a partir de sus RA
// ══════════════════════════════════════════════════════════════════════

// Media ponderada (por resultados_aprendizaje.porcentaje) de un conjunto de
// RA ya elegido — cada RA cuenta como la media de sus CE calificados. Un RA
// sin ningún CE calificado no aporta (no cuenta como 0). Compartida por:
//   - calcularMediaRACEModulo() (RA/CE "simple", sin pasar por el asistente)
//   - motor_calificaciones.php::_motorMediaRACE() (motor configurable, con
//     los RA ya filtrados por el tipo de evaluación del asistente)
function calcularMediaPonderadaRA(int $idEstudiante, int $idModulo, array $ras): array {
    if (empty($ras)) return ['media' => 0.0, 'huboNota' => false];

    $notasCE = obtenerCalificacionesPorModuloYEstudiante($idModulo, $idEstudiante);

    $sumaPonderada = 0.0;
    $sumaPesos = 0.0;
    $huboNota = false;
    foreach ($ras as $ra) {
        $ces = listarCEPorRA((int)$ra['idRA']);
        if (empty($ces)) continue;
        $notasDeEsteRA = [];
        foreach ($ces as $ce) {
            if (isset($notasCE[$ce['idCE']]) && $notasCE[$ce['idCE']] !== null) {
                $notasDeEsteRA[] = (float)$notasCE[$ce['idCE']];
            }
        }
        if (empty($notasDeEsteRA)) continue;
        $mediaRA = array_sum($notasDeEsteRA) / count($notasDeEsteRA);
        $peso = (float)($ra['porcentaje'] ?? 0) ?: 1.0; // porcentaje=0 (sin configurar) -> peso igual entre RAs
        $sumaPonderada += $mediaRA * $peso;
        $sumaPesos += $peso;
        $huboNota = true;
    }

    if (!$huboNota || $sumaPesos <= 0) return ['media' => 0.0, 'huboNota' => false];
    return ['media' => $sumaPonderada / $sumaPesos, 'huboNota' => true];
}

// RA/CE "simple": la nota de un módulo a partir de TODOS sus RA, sin
// necesidad de configurar nada en el asistente académico (Configuración
// Académica). Basta con activar feature_ra_ce y definir los RA/CE del
// módulo (vistas/admin/ra_ce/gestionarRA.php) para que cuenten en la nota
// final del alumno — ver modelos/calificaciones.php::_detalleModulo().
function calcularMediaRACEModulo(int $idEstudiante, int $idModulo): array {
    return calcularMediaPonderadaRA($idEstudiante, $idModulo, listarRAPorModulo($idModulo));
}
