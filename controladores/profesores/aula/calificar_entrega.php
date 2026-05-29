<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idEntrega = (int)($_POST['id'] ?? 0);
$nota = (float)($_POST['nota'] ?? 0);
$comentario = Security::sanitize($_POST['comentario'] ?? '');

if ($nota < 0 || $nota > 10) {
    $_SESSION['errores'] = 'La calificación debe estar entre 0 y 10';
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

if (empty($comentario)) {
    $_SESSION['errores'] = 'El comentario es requerido';
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

// Get entrega and verify ownership
$con = obtenerConexion();
$sql = "SELECT e.*, t.idProfesor FROM aula_entregas e
        JOIN aula_tareas t ON e.idTarea = t.idTarea
        WHERE e.idEntrega = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $idEntrega);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$entrega = mysqli_fetch_assoc($res);
mysqli_close($con);

if (!$entrega || $entrega['idProfesor'] != $idProfesor) {
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$archivoCorreccion = null;

if (isset($_FILES['archivo_correccion']) && $_FILES['archivo_correccion']['size'] > 0) {
    $archivo = $_FILES['archivo_correccion'];
    $permitidas = ['pdf', 'doc', 'docx', 'txt'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidas)) {
        $_SESSION['errores'] = 'Tipo de archivo no permitido para corrección';
        header("Location: ../../../vistas/profesores/aula/tareas.php");
        exit;
    }

    if ($archivo['size'] > 10 * 1024 * 1024) {
        $_SESSION['errores'] = 'Archivo de corrección muy grande (máx 10MB)';
        header("Location: ../../../vistas/profesores/aula/tareas.php");
        exit;
    }

    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $ruta = __DIR__ . "/../../../public/uploads/aula/correcciones/$nombreArchivo";

    if (!file_exists(dirname($ruta))) {
        mkdir(dirname($ruta), 0755, true);
    }

    if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
        $archivoCorreccion = $nombreArchivo;
    }
}

// Update entrega with grade
$con = obtenerConexion();
$sql = "UPDATE aula_entregas SET nota = ?, comentarioCalificacion = ?, archivoCorreccion = ? WHERE idEntrega = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "dssi", $nota, $comentario, $archivoCorreccion, $idEntrega);
$actualizado = mysqli_stmt_execute($stmt);
mysqli_close($con);

if ($actualizado) {
    $_SESSION['exito'] = 'Entrega calificada exitosamente';
    Logger::activity('ENTREGA_CALIFICADA', $idProfesor, ['idEntrega' => $idEntrega, 'nota' => $nota]);

    // Notify student
    insertarNotificacionAula($entrega['idEstudiante'], 'estudiante', 'ENTREGA_CALIFICADA', 'Tu entrega fue calificada', "Tu entrega ha sido evaluada con una calificación de $nota/10", $idEntrega, 'ENTREGA');

    header("Location: ../../../vistas/profesores/aula/entregas.php?id=" . $entrega['idTarea']);
} else {
    $_SESSION['errores'] = 'Error al calificar la entrega. Intenta de nuevo.';
    Logger::error('Error calificando entrega', ['profesor' => $idProfesor, 'entrega' => $idEntrega]);
    header("Location: ../../../vistas/profesores/aula/tareas.php");
}
?>
