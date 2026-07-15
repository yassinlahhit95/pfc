<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
ob_start();
$ajax = !empty($_POST['ajax']);

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN PREVIA
// ══════════════════════════════════════════════════════════════════════

// Si se supera post_max_size, PHP vacía $_POST — detectarlo antes de continuar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    if (ob_get_level() > 0) ob_end_clean();
    $_SESSION['errores'] = "Los archivos superan el tamaño máximo que admite el servidor. Prueba a subirlos de uno en uno o más pequeños.";
    header("Location: ../../../vistas/profesores/aula/index.php");
    exit;
}

if (!isset($_POST['subirArchivos'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    if (ob_get_level() > 0) ob_end_clean();
    $_SESSION['errores'] = "La sesión ha caducado. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/recursos.php?id=" . intval($_POST['idModulo'] ?? 0));
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idProfesor  = $_SESSION['idProfesor'];
$idModulo    = intval($_POST['idModulo'] ?? 0);
$idCarpeta   = intval($_POST['idCarpeta'] ?? 0) ?: null;
$titulo      = trim($_POST['titulo'] ?? '');

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
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $permitidos = [
        'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt',
        'xls', 'xlsx', 'ods', 'csv',
        'ppt', 'pptx', 'odp',
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'zip', 'rar'
    ];
    $LIMITE_ARCHIVO = 20 * 1024 * 1024; // 20 MB por archivo

    // Verificar cuota de almacenamiento del ciclo
    $idCiclo     = intval($modulo['idCiclo']);
    $limiteCiclo = obtenerLimiteAlmacenamientoCicloAula($idCiclo);
    $usadoCiclo  = obtenerUsoAlmacenamientoCicloAula($idCiclo);

    $subidos = 0; $errores = [];

    if (isset($_FILES['archivos']) && is_array($_FILES['archivos']['name']) && !empty($_FILES['archivos']['name'][0])) {
        $totalArchivos = count($_FILES['archivos']['name']);
        for ($i = 0; $i < $totalArchivos; $i++) {
            $errCode = $_FILES['archivos']['error'][$i];
            if ($errCode !== UPLOAD_ERR_OK) {
                if ($errCode === UPLOAD_ERR_NO_FILE) continue; // hueco vacío en la selección
                $nombreErr = $_FILES['archivos']['name'][$i] ?: ('archivo #' . ($i + 1));
                if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                    $errores[] = "$nombreErr: supera el tamaño máximo permitido.";
                } elseif ($errCode === UPLOAD_ERR_PARTIAL) {
                    $errores[] = "$nombreErr: la subida se interrumpió, inténtalo de nuevo.";
                } else {
                    $errores[] = "$nombreErr: no se pudo subir.";
                }
                continue;
            }

            $nombreOrig = $_FILES['archivos']['name'][$i];
            $ext        = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
            $tamanio    = $_FILES['archivos']['size'][$i];

            // 1. Validar extensión
            if (!in_array($ext, $permitidos)) {
                $errores[] = "$nombreOrig: tipo no permitido ($ext).";
                continue;
            }

            // 2. Validar tamaño
            if ($tamanio > $LIMITE_ARCHIVO) {
                $errores[] = "$nombreOrig: supera el límite de 20 MB.";
                continue;
            }

            // 3. Validar tipo MIME real (si fileinfo está disponible)
            $mimeReal = '';
            if (function_exists('finfo_open')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mimeReal = (string) finfo_file($finfo, $_FILES['archivos']['tmp_name'][$i]);
                    finfo_close($finfo);
                }
            }

            $mimesValidos = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png', 'gif' => 'image/gif',
                'zip' => 'application/zip', 'rar' => 'application/x-rar',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'txt' => 'text/plain'
            ];

            // Algunos servidores devuelven tipos MIME distintos para Office/ZIP; solo bloqueamos discrepancias críticas.
            if ($mimeReal !== '' && isset($mimesValidos[$ext]) && $mimesValidos[$ext] !== $mimeReal) {
                 if (strpos($mimeReal, 'executable') !== false || strpos($mimeReal, 'php') !== false) {
                     $errores[] = "$nombreOrig: contenido malicioso detectado.";
                     continue;
                 }
            }

            if (($usadoCiclo + $tamanio) > $limiteCiclo) {
                $errores[] = "$nombreOrig: se superaría el límite de almacenamiento del ciclo.";
                continue;
            }
            $usadoCiclo += $tamanio;

            // 4. Nombre aleatorio para evitar colisiones y exposición del nombre original
            $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;

            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $dir . $nombreArchivo)) {
                $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
                if (isset($imgMimes[$ext])) ImageOptimizer::optimize($dir . $nombreArchivo, $imgMimes[$ext]);
                $nombreVisible = $nombreOrig;
                if ($titulo !== '') {
                    $base   = $titulo;
                    $sufijo = '.' . $ext;
                    // Si el profesor incluyó la extensión en el título, evitar duplicarla
                    if ($ext !== '' && strtolower(substr($base, -strlen($sufijo))) === strtolower($sufijo)) {
                        $base = substr($base, 0, -strlen($sufijo));
                    }
                    $base = trim($base);
                    if ($base !== '') $nombreVisible = ($ext !== '') ? $base . '.' . $ext : $base;
                }
                // Evitar conflictos de nombre en el mismo módulo/carpeta → " (2)", " (3)"...
                $nombreVisible = nombreUnicoArchivoAula($idModulo, $idCarpeta, $nombreVisible);

                $idArchivo = insertarArchivoAula($nombreArchivo, $nombreVisible, $ext, $tamanio, '', $idCarpeta, $idModulo, $idProfesor);
                if ($idArchivo) {
                    $subidos++;
                    notificarEstudiantesCicloAula(
                        $modulo['idCiclo'], 'archivo_subido',
                        'Nuevo archivo en ' . $modulo['nombreModulo'],
                        $idProfesor . ' ha subido: ' . $nombreVisible,
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

        // Notificación push FCM (aislada, nunca debe bloquear la redirección)
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
        $detalle = !empty($errores) ? implode(' ', $errores) : "No seleccionaste ningún archivo válido.";
        $_SESSION['errores'] = "No se pudo subir ningún archivo. " . $detalle;
    }
} catch (\Throwable $e) {
    if (empty($_SESSION['errores'])) {
        $_SESSION['errores'] = "No se pudo completar la subida.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (ob_get_level() > 0) ob_end_clean();
if ($ajax) {
    header('Content-Type: application/json');
    $ok  = empty($_SESSION['errores']);
    $msg = $ok ? ($_SESSION['exito'] ?? '') : ($_SESSION['errores'] ?? '');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
} else {
    header("Location: $destino");
}
exit;
