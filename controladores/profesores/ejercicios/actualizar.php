<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (!isset($_POST['actualizarEjercicio'])) {
    header("Location: ../../../vistas/profesores/ejercicios/panel.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idEjercicio = intval($_POST['idEjercicio'] ?? 0);
$ejercicio   = obtenerEjercicioPorId($idEjercicio);

if (!$ejercicio || $ejercicio['idProfesor'] != $_SESSION['idProfesor']) {
    header("Location: ../../../vistas/profesores/ejercicios/panel.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$titulo      = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$idCarpeta   = intval($_POST['idCarpeta'] ?? 0);
$fechaLimite = trim($_POST['fechaLimite'] ?? '');
$publicado   = isset($_POST['publicado']) ? 1 : 0;

if (empty($titulo)) {
    $_SESSION['errores'] = "El título es obligatorio.";
    header("Location: ../../../vistas/profesores/ejercicios/editar.php?id=$idEjercicio");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (actualizarEjercicio($idEjercicio, $titulo, $descripcion, $idCarpeta ?: null, $fechaLimite ?: null, $publicado)) {
    $_SESSION['exito'] = "Ejercicio actualizado.";
} else {
    $_SESSION['errores'] = "No se pudo actualizar.";
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
