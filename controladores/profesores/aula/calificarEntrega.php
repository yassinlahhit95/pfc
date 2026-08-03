<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/R2Client.php";

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

if (!Security::validateCSRFToken(null, false)) {
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

// ── Archivo de corrección (opcional) ──
$archivoCorreccion = null;
if (!empty($_FILES['archivoCorreccion']['name'])) {
    $archivo = $_FILES['archivoCorreccion'];
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        calificar_salir(false, "Error al subir el archivo de corrección.", $volver, $isAjax);
    }
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $mimeAllowed = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/octet-stream',
    ];
    $mimeReal = @mime_content_type($archivo['tmp_name']);
    if (!in_array($ext, ['pdf', 'docx', 'txt']) || ($mimeReal && !in_array($mimeReal, $mimeAllowed))) {
        calificar_salir(false, "El archivo de corrección debe ser PDF, DOCX o TXT.", $volver, $isAjax);
    }
    if ($archivo['size'] > 20 * 1024 * 1024) {
        calificar_salir(false, "El archivo de corrección supera el límite de 20 MB.", $volver, $isAjax);
    }

    $nombreCorreccion = bin2hex(random_bytes(12)) . '.' . $ext;
    $bytes   = file_get_contents($archivo['tmp_name']);
    $subioOk = $bytes !== false && R2Client::putObject('aula/correcciones/' . $nombreCorreccion, $bytes, $mimeReal ?: 'application/octet-stream');
    @unlink($archivo['tmp_name']);
    if (!$subioOk) {
        calificar_salir(false, "Error al guardar el archivo de corrección en el servidor.", $volver, $isAjax);
    }
    $archivoCorreccion = $nombreCorreccion;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (calificarEntregaAula($idEntrega, (float)$nota, $comentario, $archivoCorreccion)) {
    // Sustituye el archivo de corrección anterior (si lo había) solo tras
    // confirmar que el nuevo ya quedó guardado y la BD actualizada.
    if ($archivoCorreccion !== null && !empty($entrega['archivoCorreccion'])) {
        R2Client::deleteObject('aula/correcciones/' . $entrega['archivoCorreccion']);
        $rutaVieja = __DIR__ . "/../../../public/uploads/aula/correcciones/" . $entrega['archivoCorreccion'];
        if (is_file($rutaVieja)) @unlink($rutaVieja);
    }
    insertarNotificacionAula((int)$entrega['idEstudiante'], 'estudiante', 'entrega_corregida',
        'Entrega Corregida',
        "Tu entrega de «{$entrega['tituloTarea']}» ha sido corregida: " . number_format((float)$nota, 2),
        (int)$entrega['idTarea'], 'TAREA');
    $fh = __DIR__ . "/../../firebase/firebase_helper.php";
    if (file_exists($fh)) {
        require_once $fh;
        $tokenEstudiante = obtenerTokenUsuario((int)$entrega['idEstudiante'], 'estudiante');
        if ($tokenEstudiante) {
            enviarNotificacionFirebase(
                $tokenEstudiante,
                'Entrega calificada: ' . $entrega['tituloTarea'],
                'Tu entrega ha sido corregida: ' . number_format((float)$nota, 2) . ' sobre 10.',
                'entrega_calificada',
                ['idTarea' => (int)$entrega['idTarea']]
            );
        }
    }
    calificar_salir(true, "Entrega calificada correctamente.", $volver, $isAjax, ['idEntrega' => $idEntrega, 'nota' => (float)$nota]);
} else {
    calificar_salir(false, "No se pudo guardar la calificación.", $volver, $isAjax);
}
