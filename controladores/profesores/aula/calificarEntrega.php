<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = (int)$_SESSION['idProfesor'];
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function calificar_salir($ok, $msg, $volver, $isAjax, $extra = []) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra));
        exit;
    }
    if ($ok) { $_SESSION['exito'] = $msg; } else { $_SESSION['errores'] = $msg; }
    header("Location: $volver");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    calificar_salir(false, 'Método no permitido.', '../../../vistas/profesores/aula/tareas.php', $isAjax);
}

if (!Security::validateCSRFToken()) {
    calificar_salir(false, 'Solicitud inválida. Inténtelo de nuevo.', '../../../vistas/profesores/aula/tareas.php', $isAjax);
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idEntrega  = (int)($_POST['idEntrega'] ?? 0);
$nota       = $_POST['nota'] ?? '';
$comentario = trim($_POST['comentario'] ?? '');

$entrega = $idEntrega > 0 ? obtenerEntregaPorIdAula($idEntrega) : null;
if (!$entrega) {
    calificar_salir(false, "Entrega no encontrada.", '../../../vistas/profesores/aula/tareas.php', $isAjax);
}

$volver = "../../../vistas/profesores/aula/tareaEntregas.php?id=" . (int)$entrega['idTarea'];

$misModulos = listarModulosDeProfesor($idProfesor);
if (!in_array((int)$entrega['idModulo'], array_column($misModulos, 'idModulo'))) {
    calificar_salir(false, "No tienes permiso para calificar esta entrega.", '../../../vistas/profesores/aula/tareas.php', $isAjax);
}

if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
    calificar_salir(false, "La nota debe ser un número entre 0 y 10.", $volver, $isAjax);
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (calificarEntregaAula($idEntrega, (float)$nota, $comentario)) {
    insertarNotificacionAula((int)$entrega['idEstudiante'], 'estudiante', 'entrega_corregida',
        'Entrega Corregida',
        "Tu entrega de «{$entrega['tituloTarea']}» ha sido corregida: " . number_format((float)$nota, 2),
        (int)$entrega['idTarea'], 'TAREA');
    calificar_salir(true, "Entrega calificada correctamente.", $volver, $isAjax, ['idEntrega' => $idEntrega, 'nota' => (float)$nota]);
} else {
    calificar_salir(false, "No se pudo guardar la calificación.", $volver, $isAjax);
}
