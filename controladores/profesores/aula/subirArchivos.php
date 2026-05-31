<?php
session_start();
// Buffer de salida: garantiza que ninguna salida accidental (avisos del helper
// de Firebase, etc.) impida el header("Location: ...") final.
ob_start();

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!isset($_POST['subirArchivos'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }

$idProfesor  = $_SESSION['idProfesor'];
$idModulo    = intval($_POST['idModulo'] ?? 0);
$idCarpeta   = intval($_POST['idCarpeta'] ?? 0) ?: null;
$descripcion = trim($_POST['descripcion'] ?? '');

// Destino por defecto; se concreta en cuanto validamos el módulo
$destino = "../../../vistas/profesores/aula/index.php";

try {
    if ($idModulo < 1) {
        $_SESSION['errores'] = "Módulo no válido.";
        throw new RuntimeException('modulo_invalido');
    }
    $modulo = obtenerModuloPorId($idModulo);
    if (!$modulo) {
        $_SESSION['errores'] = "Módulo no encontrado.";
        throw new RuntimeException('modulo_inexistente');
    }

    $destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
    if ($idCarpeta) $destino .= "&carpeta=$idCarpeta";

    $dir = __DIR__ . "/../../../public/uploads/aula/archivos/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // Tipos permitidos: PDF, Word, Excel, PowerPoint, imágenes y otros documentos académicos
    $permitidos = [
        'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt',
        'xls', 'xlsx', 'ods', 'csv',
        'ppt', 'pptx', 'odp',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'zip', 'rar'
    ];
    $LIMITE_ARCHIVO = 20 * 1024 * 1024; // 20 MB por archivo

    // Control de almacenamiento por ciclo (#13)
    $idCiclo     = intval($modulo['idCiclo']);
    $limiteCiclo = obtenerLimiteAlmacenamientoCicloAula($idCiclo);
    $usadoCiclo  = obtenerUsoAlmacenamientoCicloAula($idCiclo);

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
            if ($tamanio > $LIMITE_ARCHIVO) {
                $errores[] = "$nombreOrig: supera el límite de 20 MB.";
                continue;
            }
            if (($usadoCiclo + $tamanio) > $limiteCiclo) {
                $errores[] = "$nombreOrig: se superaría el límite de almacenamiento del ciclo.";
                continue;
            }
            $usadoCiclo += $tamanio; // reservar el espacio para los siguientes del lote

            $nombreArchivo = 'AULA_' . $idProfesor . '_' . date('dmY_His') . '_' . mt_rand(100,999) . '.' . $ext;
            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $dir . $nombreArchivo)) {
                $idArchivo = insertarArchivoAula($nombreArchivo, $nombreOrig, $ext, $tamanio, $descripcion, $idCarpeta, $idModulo, $idProfesor);
                if ($idArchivo) {
                    $subidos++;
                    notificarEstudiantesCicloAula(
                        $modulo['idCiclo'], 'archivo_subido',
                        'Nuevo archivo en ' . $modulo['nombreModulo'],
                        $idProfesor . ' ha subido: ' . $nombreOrig,
                        $idArchivo, 'archivo'
                    );
                }
            } else {
                $errores[] = "$nombreOrig: error al guardar.";
            }
        }
    }

    if ($subidos > 0) {
        $_SESSION['exito'] = "$subidos archivo(s) subido(s) correctamente.";
        if (!empty($errores)) $_SESSION['exito'] .= " (" . implode(', ', $errores) . ")";

        // Push FCM en tiempo real (#7). Aislado: nunca debe impedir la redirección.
        try {
            $firebaseHelper = __DIR__ . "/../../firebase/firebase_helper.php";
            if (file_exists($firebaseHelper)) {
                require_once $firebaseHelper;
                if (function_exists('enviarNotificacionFirebase')) {
                    $tituloPush  = 'Nuevos recursos · ' . $modulo['nombreModulo'];
                    $mensajePush = $subidos . ' recurso(s) publicado(s) el ' . date('d/m/Y H:i');
                    foreach (obtenerTokensFCMPorCicloAula($modulo['idCiclo']) as $token) {
                        @enviarNotificacionFirebase($token, $tituloPush, $mensajePush);
                    }
                }
            }
        } catch (\Throwable $e) { /* el push es opcional, se ignora cualquier fallo */ }
    } else {
        $_SESSION['errores'] = "No se pudo subir ningún archivo. " . implode(' ', $errores);
    }
} catch (\Throwable $e) {
    if (empty($_SESSION['errores'])) {
        $_SESSION['errores'] = "No se pudo completar la subida.";
    }
}

// Descartar cualquier salida acumulada y redirigir SIEMPRE
if (ob_get_level() > 0) ob_end_clean();
header("Location: $destino");
exit;
