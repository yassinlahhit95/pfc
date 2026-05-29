<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (!isset($_POST['calificar'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$idProfesor  = $_SESSION['idProfesor'];
$idEntrega   = intval($_POST['idEntrega'] ?? 0);
$nota        = floatval(str_replace(',', '.', $_POST['nota'] ?? ''));
$mensaje     = trim($_POST['feedback'] ?? '');
$idTarea     = intval($_POST['idTarea'] ?? 0);

if ($idEntrega < 1 || $nota < 0 || $nota > 10) {
    $_SESSION['errores'] = "Nota inválida (debe ser entre 0 y 10).";
    header("Location: ../../../vistas/profesores/aula/verEntregas.php?id=$idTarea"); exit;
}

// Verificar que la tarea pertenece al profesor
$tarea = obtenerTareaPorIdAula($idTarea);
if (!$tarea || $tarea['idProfesor'] != $idProfesor) {
    header("Location: ../../../vistas/profesores/aula/index.php"); exit;
}

calificarEntregaAula($idEntrega, $nota);

// Buscar idEstudiante usando obtenerConexion()
$idEstudiante = null;
$conEnt = obtenerConexion();
$sqEnt  = mysqli_prepare($conEnt, "SELECT idEstudiante FROM aula_entregas WHERE idEntrega = ?");
mysqli_stmt_bind_param($sqEnt, "i", $idEntrega);
mysqli_stmt_execute($sqEnt);
$resEnt = mysqli_stmt_get_result($sqEnt);
$filaEnt = mysqli_fetch_assoc($resEnt);
if ($filaEnt) $idEstudiante = $filaEnt['idEstudiante'];
mysqli_close($conEnt);

// Guardar feedback como comentario si lo hay
if (!empty($mensaje)) {
    insertarComentarioAula($idEntrega, $idProfesor, 'profesor', $mensaje, null);

    if ($idEstudiante) {
        insertarNotificacionAula(
            $idEstudiante, 'estudiante', 'correccion',
            'Tarea corregida: ' . $tarea['titulo'],
            'Has recibido una calificación de ' . $nota,
            $idTarea, 'tarea'
        );
        $fh = __DIR__ . "/../../firebase/firebase_helper.php";
        if (file_exists($fh)) {
            require_once $fh;
            $token = obtenerTokenUsuario($idEstudiante, 'estudiante');
            if ($token) enviarNotificacionFirebase($token, 'Tarea corregida: ' . $tarea['titulo'], 'Nota: ' . $nota . '/10');
        }
    }
}

// Archivo de corrección
if (!empty($_FILES['archivoCorreccion']['name'])) {
    $archivo = $_FILES['archivoCorreccion'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['pdf','docx','txt']) && $archivo['error'] === UPLOAD_ERR_OK) {
        $dir = __DIR__ . "/../../../public/uploads/aula/correcciones/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $nombreArchivo = 'CORR_' . $idEntrega . '_' . date('dmY_His') . '.' . $ext;
        if (move_uploaded_file($archivo['tmp_name'], $dir . $nombreArchivo)) {
            insertarComentarioAula($idEntrega, $idProfesor, 'profesor', $mensaje ?: 'Corrección adjunta.', $nombreArchivo);
        }
    }
}

$_SESSION['exito'] = "Entrega calificada correctamente.";
header("Location: ../../../vistas/profesores/aula/verEntregas.php?id=$idTarea");
exit;
