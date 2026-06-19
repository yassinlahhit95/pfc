<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['entregar'])) {
    $idEjercicio  = intval($_POST['idEjercicio'] ?? 0);
    $respuesta    = trim($_POST['respuesta'] ?? '');
    $idEstudiante = $_SESSION['idEstudiante'];

    $ej = obtenerEjercicioPorId($idEjercicio);
    if (!$ej || !$ej['publicado']) {
        header("Location: ../../../vistas/estudiantes/ejercicios/lista.php");
        exit;
    }

    $archivoEntrega = null;
    if (!empty($_FILES['archivoEntrega']['name'])) {
        $archivo    = $_FILES['archivoEntrega'];
        $ext        = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf','doc','docx','txt','png','jpg','jpeg','zip'];
        if (!in_array($ext, $permitidos)) {
            $_SESSION['errores'] = "Tipo de archivo no permitido.";
            header("Location: ../../../vistas/estudiantes/ejercicios/ver.php?id=$idEjercicio");
            exit;
        }
        $nombreArchivo = 'ENT_' . $idEstudiante . '_' . $idEjercicio . '_' . date('dmY_His') . '.' . $ext;
        $destino = __DIR__ . "/../../../public/uploads/ejercicios/entregas/" . $nombreArchivo;
        if (!is_dir(dirname($destino))) mkdir(dirname($destino), 0755, true);
        if (move_uploaded_file($archivo['tmp_name'], $destino)) {
            $archivoEntrega = $nombreArchivo;
        }
    }

    if (empty($respuesta) && !$archivoEntrega) {
        $_SESSION['errores'] = "Debes escribir una respuesta o adjuntar un archivo.";
        header("Location: ../../../vistas/estudiantes/ejercicios/ver.php?id=$idEjercicio");
        exit;
    }

    if (insertarOActualizarEntrega($idEjercicio, $idEstudiante, $respuesta, $archivoEntrega)) {
        $_SESSION['exito'] = "La entrega ha sido realizada correctamente.";
    } else {
        $_SESSION['errores'] = "No se pudo guardar la entrega.";
    }
    header("Location: ../../../vistas/estudiantes/ejercicios/ver.php?id=$idEjercicio");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/estudiantes/ejercicios/lista.php");
exit;
