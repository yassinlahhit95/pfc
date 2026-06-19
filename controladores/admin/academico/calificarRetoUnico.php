<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/retos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$idReto       = (int)($_POST['idReto'] ?? 0);
$idCiclo      = (int)($_POST['idCiclo'] ?? 0);
$nota         = str_replace(',', '.', trim($_POST['nota'] ?? ''));

if ($idEstudiante && $idReto) {
    if ($nota === '') {
        eliminarCalificacionReto($idEstudiante, $idReto);
        $_SESSION['exito'] = "La nota ha sido eliminada correctamente.";
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 10) {
        $_SESSION['errores'] = "La nota introducida debe ser un valor numérico comprendido entre 0 y 10.";
    } else {
        if (calificarReto($idEstudiante, $idReto, floatval($nota))) {
            $_SESSION['exito'] = "La nota ha sido registrada correctamente.";
        } else {
            $_SESSION['errores'] = "Ocurrió un error al intentar registrar la nota.";
        }
    }
} else {
    $_SESSION['errores'] = "Los datos suministrados no son correctos o están incompletos.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (isset($_SESSION['exito'])) {
    header("Location: ../../../vistas/admin/academico/calificacionesRetos.php?idCiclo={$idCiclo}&idReto={$idReto}");
} else {
    header("Location: ../../../vistas/admin/academico/evaluarReto.php?idEstudiante={$idEstudiante}&idReto={$idReto}&idCiclo={$idCiclo}");
}
exit;
