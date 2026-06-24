<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['entregar'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/ejercicios/lista.php");
        exit;
    }
    $idEjercicio  = intval($_POST['idEjercicio'] ?? 0);
    $respuesta    = trim($_POST['respuesta'] ?? '');
    $idEstudiante = $_SESSION['idEstudiante'];

    $ej              = obtenerEjercicioPorId($idEjercicio);
    $datosEstudiante = obtenerEstudiantePorId($idEstudiante);

    // Reject if exercise doesn't exist, isn't published, or belongs to a different ciclo (IDOR guard)
    if (!$ej || !$ej['publicado']
        || (int)$ej['idCiclo'] !== (int)($datosEstudiante['idCiclo'] ?? -1)) {
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
        $mime = mime_content_type($archivo['tmp_name']);
        $mimePermitidos = [
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain', 'image/png', 'image/jpeg',
            'application/zip', 'application/x-zip-compressed',
        ];
        if (!in_array($mime, $mimePermitidos)) {
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
