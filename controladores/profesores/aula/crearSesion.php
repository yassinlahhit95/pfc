<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
$idProfesor = $_SESSION['idProfesor'];

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idModulo = (int)($_POST['idModulo'] ?? 0);
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = $_POST['plataforma'] ?? '';

$errores = [];
if (!$idModulo) $errores[] = "El módulo es obligatorio.";
if (!$titulo) $errores[] = "El título es obligatorio.";
if (!$fechaSesion) $errores[] = "La fecha es obligatoria.";
if (!$horaSesion) $errores[] = "La hora es obligatoria.";

// Validar fecha y hora
if ($fechaSesion && $horaSesion) {
    $errFecha = validarFechaHoraSesion($fechaSesion, $horaSesion);
    if ($errFecha) $errores[] = $errFecha;
}

// Validar enlace de reunión
if ($enlaceReunion) {
    $errEnlace = validarEnlaceReunion($enlaceReunion);
    if ($errEnlace) $errores[] = $errEnlace;
}

if ($errores) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../../vistas/profesores/aula/modulos.php?idCiclo=" . (int)($_POST['idCiclo'] ?? 0));
    exit;
}

$modulo = obtenerModuloPorId($idModulo);
if (!$modulo || $modulo['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No tienes permiso para acceder a este módulo.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$idSesion = crearSesionViva($idModulo, $idProfesor, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($idSesion) {
    // Notificar a todos los estudiantes del módulo
    notificarEstudiantesPorModulo(
        $idModulo,
        'sesion_nueva',
        'Nueva sesión viva: ' . $titulo,
        'Se ha creado una nueva sesión viva en ' . $modulo['nombreModulo'] . ' para el ' . date('d/m/Y H:i', strtotime($fechaSesion . ' ' . $horaSesion)),
        $idSesion,
        'sesion'
    );

    $_SESSION['exito'] = "Sesión creada exitosamente y se notificó a los estudiantes";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
} else {
    $_SESSION['errores'] = "Error al crear la sesión. Inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
}
?>
