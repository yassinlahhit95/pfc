<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

if ($idEstudiante <= 0) {
    $_SESSION['errores'] = 'Estudiante no válido.';
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
if (!$estudiante) {
    $_SESSION['errores'] = 'Estudiante no encontrado.';
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

$ok = softDeleteEstudiante($idEstudiante);

if ($ok) {
    registrarAccionSecretaria('eliminar', 'estudiantes', $idEstudiante, $estudiante['nombreEstudiante']);
    $_SESSION['exito'] = "El estudiante «" . $estudiante['nombreEstudiante'] . "» se ha movido a la papelera.";
} else {
    $_SESSION['errores'] = 'Error al mover el estudiante a la papelera.';
}

header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
exit;
