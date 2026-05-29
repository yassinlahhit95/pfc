<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

if (!isset($_POST['subirArchivos'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$idProfesor  = $_SESSION['idProfesor'];
$idModulo    = intval($_POST['idModulo'] ?? 0);
$idCarpeta   = intval($_POST['idCarpeta'] ?? 0) ?: null;
$descripcion = trim($_POST['descripcion'] ?? '');

if ($idModulo < 1) {
    $_SESSION['errores'] = "Módulo no válido.";
    header("Location: ../../../vistas/profesores/aula/index.php"); exit;
}

$modulo = obtenerModuloPorId($idModulo);
if (!$modulo) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$dir = __DIR__ . "/../../../public/uploads/aula/archivos/";
if (!is_dir($dir)) mkdir($dir, 0777, true);

$permitidos = ['pdf', 'docx', 'txt'];
$subidos = 0; $errores = [];

if (!empty($_FILES['archivos']['name'][0])) {
    $totalArchivos = count($_FILES['archivos']['name']);
    for ($i = 0; $i < $totalArchivos; $i++) {
        if ($_FILES['archivos']['error'][$i] !== UPLOAD_ERR_OK) continue;

        $nombreOrig = $_FILES['archivos']['name'][$i];
        $ext        = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
        $tamanio    = $_FILES['archivos']['size'][$i];

        if (!in_array($ext, $permitidos)) {
            $errores[] = "$nombreOrig: tipo no permitido ($ext).";
            continue;
        }
        if ($tamanio > 20 * 1024 * 1024) {
            $errores[] = "$nombreOrig: supera el límite de 20 MB.";
            continue;
        }

        $nombreArchivo = 'AULA_' . $idProfesor . '_' . date('dmY_His') . '_' . mt_rand(100,999) . '.' . $ext;
        $destino = $dir . $nombreArchivo;

        if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $destino)) {
            $idArchivo = insertarArchivoAula($nombreArchivo, $nombreOrig, $ext, $tamanio, $descripcion, $idCarpeta, $idModulo, $idProfesor);
            if ($idArchivo) {
                $subidos++;
                // Notificar estudiantes del ciclo
                notificarEstudiantesCicloAula(
                    $modulo['idCiclo'],
                    'archivo_subido',
                    'Nuevo archivo en ' . $modulo['nombreModulo'],
                    $idProfesor . ' ha subido: ' . $nombreOrig,
                    $idArchivo,
                    'archivo'
                );
                // Firebase push (si disponible)
                $firebaseHelper = __DIR__ . "/../../firebase/firebase_helper.php";
                if (file_exists($firebaseHelper)) {
                    require_once $firebaseHelper;
                    // Push se envía por lotes si se necesita; aquí registro la notificación DB
                }
            }
        } else {
            $errores[] = "$nombreOrig: error al guardar.";
        }
    }
}

if ($subidos > 0) {
    $_SESSION['exito'] = "$subidos archivo(s) subido(s) correctamente.";
    if (!empty($errores)) {
        $_SESSION['exito'] .= " (" . implode(', ', $errores) . ")";
    }
} else {
    $_SESSION['errores'] = "No se pudo subir ningún archivo. " . implode(' ', $errores);
}

header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
