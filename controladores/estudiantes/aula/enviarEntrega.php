<?php
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idEstudiante'])) { header("Location: ../../../vistas/login.php"); exit; }

if (!isset($_POST['enviarEntrega'])) { header("Location: ../../../vistas/estudiantes/aula/index.php"); exit; }

$idEstudiante = $_SESSION['idEstudiante'];
$idTarea      = intval($_POST['idTarea'] ?? 0);
$respuesta    = trim($_POST['respuesta'] ?? '');

$tarea = obtenerTareaPorIdAula($idTarea);
if (!$tarea || !$tarea['publicado']) {
    $_SESSION['errores'] = "Tarea no disponible.";
    header("Location: ../../../vistas/estudiantes/aula/index.php"); exit;
}

if (empty($respuesta) && empty($_FILES['archivoEntrega']['name'])) {
    $_SESSION['errores'] = "Debes escribir una respuesta o adjuntar un archivo.";
    header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
}

$archivoEntrega = null;
if (!empty($_FILES['archivoEntrega']['name'])) {
    $archivo = $_FILES['archivoEntrega'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf','docx','txt'])) {
        $_SESSION['errores'] = "Solo se permiten archivos PDF, DOCX o TXT.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
    if ($archivo['size'] > 20 * 1024 * 1024) {
        $_SESSION['errores'] = "El archivo supera el límite de 20 MB.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
    $dir = __DIR__ . "/../../../public/uploads/aula/entregas/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $nombreArchivo = 'ENT_' . $idEstudiante . '_' . $idTarea . '_' . date('dmY_His') . '.' . $ext;
    if (move_uploaded_file($archivo['tmp_name'], $dir . $nombreArchivo)) {
        $archivoEntrega = $nombreArchivo;
    } else {
        $_SESSION['errores'] = "Error al guardar el archivo.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
}

if (enviarEntregaAula($idTarea, $idEstudiante, $archivoEntrega, $respuesta)) {
    $_SESSION['exito'] = "Entrega enviada correctamente.";
    // Notificar al profesor
    insertarNotificacionAula(
        $tarea['idProfesor'], 'profesor', 'entrega_enviada',
        'Nueva entrega: ' . $tarea['titulo'],
        'Un estudiante ha enviado su entrega.',
        $idTarea, 'tarea'
    );
    // Firebase push al profesor
    $fh = __DIR__ . "/../../firebase/firebase_helper.php";
    if (file_exists($fh)) {
        require_once $fh;
        $token = obtenerTokenUsuario($tarea['idProfesor'], 'profesor');
        if ($token) {
            enviarNotificacionFirebase($token, 'Nueva entrega: ' . $tarea['titulo'], 'Un estudiante ha enviado su entrega.');
        }
    }
} else {
    $_SESSION['errores'] = "No se pudo guardar la entrega.";
}

header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea");
exit;
