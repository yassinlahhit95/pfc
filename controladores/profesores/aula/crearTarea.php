<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (!isset($_POST['guardarTarea'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$idProfesor  = $_SESSION['idProfesor'];
$idModulo    = intval($_POST['idModulo'] ?? 0);
$titulo      = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($idModulo < 1 || empty($titulo)) {
    $_SESSION['errores'] = "El título y el módulo son obligatorios.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo"); exit;
}

$modulo = obtenerModuloPorId($idModulo);
$archivoAdjunto = null;

if (!empty($_FILES['archivoTarea']['name'])) {
    $archivo = $_FILES['archivoTarea'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['pdf','docx','txt']) && $archivo['error'] === UPLOAD_ERR_OK) {
        $dir = __DIR__ . "/../../../public/uploads/aula/archivos/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $nombreArchivo = 'TAREA_' . $idProfesor . '_' . date('dmY_His') . '.' . $ext;
        if (move_uploaded_file($archivo['tmp_name'], $dir . $nombreArchivo)) {
            $archivoAdjunto = $nombreArchivo;
        }
    }
}

$idTarea = insertarTareaAula($titulo, $descripcion, $idModulo, $idProfesor, $archivoAdjunto);

if ($idTarea && $modulo) {
    $_SESSION['exito'] = "Tarea creada correctamente.";
    notificarEstudiantesCicloAula(
        $modulo['idCiclo'],
        'archivo_subido',
        'Nueva tarea: ' . $titulo,
        'Nueva tarea disponible en ' . $modulo['nombreModulo'],
        $idTarea,
        'tarea'
    );
} else {
    $_SESSION['errores'] = "No se pudo crear la tarea.";
}

header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
