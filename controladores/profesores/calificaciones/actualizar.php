<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (!isset($_POST['actualizarNota'])) {
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idCalificacion = (int)($_POST['idCalificacion'] ?? 0);
$idEstudiante   = (int)($_POST['idEstudiante'] ?? 0);
$idModulo       = (int)($_POST['idModulo'] ?? 0);

if (!$idModulo || !in_array($_SESSION['idProfesor'], listarProfesoresDeModulo($idModulo))) {
    $_SESSION['errores'] = "No tienes permiso para calificar este módulo.";
    header("Location: ../../../vistas/profesores/calificaciones/lista.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nota1Ev    = str_replace(',', '.', trim($_POST['nota_1ev']));
$nota1Final = str_replace(',', '.', trim($_POST['nota_1final']));
$nota2Ev    = str_replace(',', '.', trim($_POST['nota_2ev']));
$nota2Final = str_replace(',', '.', trim($_POST['nota_2final']));

$errores = [];

if (!empty($nota1Ev)    && (!is_numeric($nota1Ev)    || $nota1Ev    < 0 || $nota1Ev    > 10)) $errores[] = "La nota de 1ª evaluación debe estar entre 0 y 10.";
if (!empty($nota1Final) && (!is_numeric($nota1Final) || $nota1Final < 0 || $nota1Final > 10)) $errores[] = "La nota final de 1ª debe estar entre 0 y 10.";
if (!empty($nota2Ev)    && (!is_numeric($nota2Ev)    || $nota2Ev    < 0 || $nota2Ev    > 10)) $errores[] = "La nota de 2ª evaluación debe estar entre 0 y 10.";
if (!empty($nota2Final) && (!is_numeric($nota2Final) || $nota2Final < 0 || $nota2Final > 10)) $errores[] = "La nota final de 2ª debe estar entre 0 y 10.";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (empty($errores)) {
    $notaActual    = obtenerCalificacionPorId($idCalificacion);
    $observaciones = $notaActual['observaciones'] ?? '';
    $resultado     = actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1Ev, $nota1Final, $nota2Ev, $nota2Final, $observaciones);

    if ($resultado) {
        if (!empty($_POST['notificarEstudiante'])) {
            require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
            enviarEmailNotasEstudiante($idEstudiante);
        }
        $_SESSION['exito'] = "Calificación actualizada.";
        header("Location: ../../../vistas/profesores/calificaciones/lista.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudieron guardar las calificaciones.";
} else {
    $_SESSION['errores'] = implode(' ', $errores);
}

header("Location: ../../../vistas/profesores/calificaciones/editar.php?id=" . $idCalificacion);
exit;
