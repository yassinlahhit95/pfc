<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }
    header("Location: ../../../vistas/profesores/ejercicios/panel.php"); exit; 
}

if (!Security::validateCSRFToken()) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida (CSRF)']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/ejercicios/panel.php"); exit;
}

$idEjercicio = intval($_POST['id'] ?? 0);
if ($idEjercicio > 0) {
    $ej = obtenerEjercicioPorId($idEjercicio);
    if ($ej && $ej['idProfesor'] == $_SESSION['idProfesor']) {
        borrarEjercicio($idEjercicio);
        if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>'Ejercicio eliminado']); exit; }
        $_SESSION['exito'] = "Ejercicio eliminado.";
    }
}
if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No se pudo eliminar el ejercicio']); exit; }
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
