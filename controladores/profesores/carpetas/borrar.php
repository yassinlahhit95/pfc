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
    header("Location: ../../../vistas/profesores/ejercicios/panel.php");
    exit;
}

$idCarpeta  = intval($_POST['id'] ?? 0);
$idProfesor = $_SESSION['idProfesor'];

if ($idCarpeta > 0) {
    $carpeta = obtenerCarpetaPorId($idCarpeta);
    if ($carpeta && $carpeta['idProfesor'] == $idProfesor) {
        borrarCarpeta($idCarpeta);
        if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>'Carpeta eliminada']); exit; }
        $_SESSION['exito'] = "Carpeta eliminada.";
    }
}
if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No se pudo eliminar']); exit; }
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;

