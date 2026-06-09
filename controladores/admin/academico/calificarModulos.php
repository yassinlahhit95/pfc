<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$hayError = false;

if (isset($_POST['guardarNotas'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida (CSRF).";
        header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
        exit;
    }
    $idModulo  = trim($_POST['idModulo'] ?? '');
    $idCiclo   = trim($_POST['idCiclo']  ?? '');
    $listaIds  = $_POST['estudiantes']   ?? [];
    $lista1ev    = $_POST['notas_1ev']    ?? [];
    $lista1final = $_POST['notas_1final'] ?? [];
    $lista2ev    = $_POST['notas_2ev']    ?? [];
    $lista2final = $_POST['notas_2final'] ?? [];
    $listaObs    = $_POST['observaciones'] ?? [];

    $especiales = ['NP', 'EX', 'CO'];
    $validarVal = function($v) use ($especiales) {
        $v = strtoupper(trim($v));
        if ($v === '') return true;
        if (in_array($v, $especiales)) return true;
        return is_numeric($v) && (float)$v >= 0 && (float)$v <= 10;
    };

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

        $resultado = actualizarOCrearNotaCompleta(
            (int)$listaIds[$i], (int)$idModulo,
            $v1ev, $v1final, $v2ev, $v2final, $obs
        );
        if (!$resultado) { $hayError = true; break; }
    }

    if (!$hayError) {
        $_SESSION['exito'] = "Calificaciones guardadas correctamente.";
    } else {
        $_SESSION['errores'] = "Error: los valores deben ser 0–10 o NP / EX / CO.";
    }

    header("Location: ../../../vistas/admin/academico/calificacionesModulos.php?idCiclo={$idCiclo}&idModulo={$idModulo}");
    exit;
}

header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
exit;
