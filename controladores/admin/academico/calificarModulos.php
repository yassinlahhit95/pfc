<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$hayErrorValidacion = false;
$hayErrorGuardado = false;

if (isset($_POST['guardarNotas'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
        exit;
    }
    $idModulo = (int)($_POST['idModulo'] ?? 0);
    $idCiclo = (int)($_POST['idCiclo'] ?? 0);
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
            $hayErrorValidacion = true;
            break;
        }

        $resultado = actualizarOCrearNotaCompleta(
            (int)$listaIds[$i], (int)$idModulo,
            $v1ev, $v1final, $v2ev, $v2final, $obs
        );
        if (!$resultado) { $hayErrorGuardado = true; break; }
    }

    if (!$hayErrorValidacion && !$hayErrorGuardado) {
        registrarAccion('calificar_modulos', 'calificaciones_modulos', $idModulo, "Ciclo $idCiclo");
        $_SESSION['exito'] = "Las notas han sido guardadas correctamente.";
    } elseif ($hayErrorValidacion) {
        $_SESSION['errores'] = "Error: las notas deben ser valores numéricos entre 0 y 10, o bien los códigos especiales NP, EX o CO.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al guardar las notas. Inténtalo de nuevo.";
    }

    header("Location: ../../../vistas/admin/academico/calificacionesModulos.php?idCiclo={$idCiclo}&idModulo={$idModulo}");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/calificacionesModulos.php");
exit;
