<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = (int)$_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idEntrega  = (int)($_POST['idEntrega'] ?? 0);
$nota       = $_POST['nota'] ?? '';
$comentario = trim($_POST['comentario'] ?? '');

$entrega = $idEntrega > 0 ? obtenerEntregaPorIdAula($idEntrega) : null;
if (!$entrega) {
    $_SESSION['errores'] = "Entrega no encontrada.";
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$volver = "../../../vistas/profesores/aula/tareaEntregas.php?id=" . (int)$entrega['idTarea'];

$misModulos = listarModulosDeProfesor($idProfesor);
if (!in_array((int)$entrega['idModulo'], array_column($misModulos, 'idModulo'))) {
    $_SESSION['errores'] = "No tienes permiso para calificar esta entrega.";
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
    $_SESSION['errores'] = "La nota debe ser un número entre 0 y 10.";
    header("Location: $volver");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (calificarEntregaAula($idEntrega, (float)$nota, $comentario)) {
    insertarNotificacionAula((int)$entrega['idEstudiante'], 'estudiante', 'entrega_corregida',
        'Entrega Corregida',
        "Tu entrega de «{$entrega['tituloTarea']}» ha sido corregida: " . number_format((float)$nota, 2),
        (int)$entrega['idTarea'], 'TAREA');
    $_SESSION['exito'] = "Entrega calificada correctamente.";
} else {
    $_SESSION['errores'] = "No se pudo guardar la calificación.";
}

header("Location: $volver");
exit;
