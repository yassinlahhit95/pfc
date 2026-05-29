<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { http_response_code(403); exit; }

$idTarea = intval($_GET['id'] ?? 0);

if ($idTarea > 0) {
    $tarea = obtenerTareaPorIdAula($idTarea);
    if ($tarea && $tarea['idProfesor'] == $_SESSION['idProfesor']) {
        togglePublicadoTareaAula($idTarea);
        http_response_code(200);
    } else {
        http_response_code(403);
    }
} else {
    http_response_code(400);
}
