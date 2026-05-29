<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['guardarEjercicio'])) {
    $titulo      = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $idCarpeta   = intval($_POST['idCarpeta'] ?? 0);
    $idCiclo     = intval($_POST['idCiclo'] ?? 0);
    $fechaLimite = trim($_POST['fechaLimite'] ?? '');
    $idProfesor  = $_SESSION['idProfesor'];

    if (empty($titulo) || $idCiclo < 1) {
        $_SESSION['errores'] = "El título y el ciclo son obligatorios.";
        header("Location: ../../../vistas/profesores/ejercicios/agregar.php");
        exit;
    }

    $archivoAdjunto = null;
    if (!empty($_FILES['archivoAdjunto']['name'])) {
        $archivo = $_FILES['archivoAdjunto'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf','doc','docx','txt','png','jpg','jpeg','zip'];
        if (!in_array($ext, $permitidos)) {
            $_SESSION['errores'] = "Tipo de archivo no permitido.";
            header("Location: ../../../vistas/profesores/ejercicios/agregar.php");
            exit;
        }
        $nombreArchivo = 'EJ_' . $idProfesor . '_' . date('dmY_His') . '.' . $ext;
        $destino = __DIR__ . "/../../../public/uploads/ejercicios/adjuntos/" . $nombreArchivo;
        if (!is_dir(dirname($destino))) mkdir(dirname($destino), 0777, true);
        if (move_uploaded_file($archivo['tmp_name'], $destino)) {
            $archivoAdjunto = $nombreArchivo;
        }
    }

    $id = insertarEjercicio($titulo, $descripcion, $idCarpeta ?: null, $idProfesor, $idCiclo, $fechaLimite ?: null, $archivoAdjunto);
    if ($id) {
        $_SESSION['exito'] = "Ejercicio creado correctamente.";
        header("Location: ../../../vistas/profesores/ejercicios/panel.php?idCarpeta=" . ($idCarpeta ?: ''));
    } else {
        $_SESSION['errores'] = "No se pudo guardar el ejercicio.";
        header("Location: ../../../vistas/profesores/ejercicios/agregar.php");
    }
    exit;
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
