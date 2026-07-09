<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/admin/estudiantes/papelera.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/estudiantes/papelera.php");
    exit;
}

$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

if ($idEstudiante <= 0) {
    $_SESSION['errores'] = 'Estudiante no válido.';
    header("Location: ../../../vistas/admin/estudiantes/papelera.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
if (!$estudiante) {
    $_SESSION['errores'] = 'Estudiante no encontrado.';
    header("Location: ../../../vistas/admin/estudiantes/papelera.php");
    exit;
}

$ok = restaurarEstudiante($idEstudiante);

if ($ok) {
    registrarAccion('restaurar', 'estudiantes', $idEstudiante, $estudiante['nombreEstudiante']);
    $_SESSION['exito'] = "El estudiante «" . $estudiante['nombreEstudiante'] . "» ha sido restaurado correctamente.";
} else {
    $_SESSION['errores'] = 'Error al restaurar el estudiante.';
}

header("Location: ../../../vistas/admin/estudiantes/papelera.php");
exit;
