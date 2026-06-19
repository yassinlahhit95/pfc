<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (!isset($_POST['guardarNotas'])) {
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idModulo = (int)trim($_POST['idModulo'] ?? 0);
$idCiclo  = (int)trim($_POST['idCiclo']  ?? 0);

if (!$idModulo || !in_array($_SESSION['idProfesor'], listarProfesoresDeModulo($idModulo))) {
    $_SESSION['errores'] = "No tienes permiso para calificar este módulo.";
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$listaIds    = $_POST['estudiantes']   ?? [];
$lista1ev    = $_POST['notas_1ev']    ?? [];
$lista1final = $_POST['notas_1final'] ?? [];
$lista2ev    = $_POST['notas_2ev']    ?? [];
$lista2final = $_POST['notas_2final'] ?? [];
$listaObs    = $_POST['observaciones'] ?? [];

$especiales  = ['NP', 'EX', 'CO'];
$validarVal  = function($v) use ($especiales) {
    $v = strtoupper(trim($v));
    if ($v === '') return true;
    if (in_array($v, $especiales)) return true;
    return is_numeric($v) && (float)$v >= 0 && (float)$v <= 10;
};

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$hayError = false;
for ($i = 0, $n = count($listaIds); $i < $n; $i++) {
    $v1ev    = trim($lista1ev[$i]    ?? '');
    $v1final = trim($lista1final[$i] ?? '');
    $v2ev    = trim($lista2ev[$i]    ?? '');
    $v2final = trim($lista2final[$i] ?? '');
    $obs     = trim($listaObs[$i]    ?? '');

    if (!$validarVal($v1ev) || !$validarVal($v1final) || !$validarVal($v2ev) || !$validarVal($v2final)) {
        $hayError = true;
        break;
    }

    $ok = actualizarOCrearNotaCompleta((int)$listaIds[$i], $idModulo, $v1ev, $v1final, $v2ev, $v2final, $obs);
    if (!$ok) { $hayError = true; break; }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (!$hayError) {
    $_SESSION['exito'] = "Calificaciones guardadas correctamente.";
} else {
    $_SESSION['errores'] = "Error: los valores deben ser 0–10 o NP / EX / CO.";
}

$qs = http_build_query(['idCiclo' => $idCiclo, 'idModulo' => $idModulo]);
header("Location: ../../../vistas/profesores/calificaciones/lista.php?$qs");
exit;
