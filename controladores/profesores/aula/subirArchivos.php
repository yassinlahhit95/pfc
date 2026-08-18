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
require_once __DIR__ . "/../../../include/R2Client.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN PREVIA
// ══════════════════════════════════════════════════════════════════════

// Si se supera post_max_size, PHP vacía $_POST — detectarlo antes de continuar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    if (ob_get_level() > 0) ob_end_clean();
    $_SESSION['errores'] = "Los archivos superan el tamaño máximo que admite el servidor (post_max_size). Prueba a subirlos de uno en uno o más pequeños.";
    $ref = $_SERVER['HTTP_REFERER'] ?? '../../../vistas/profesores/aula/index.php';
    header("Location: $ref");
    exit;
}

if (!isset($_POST['subirArchivos'])) { header("Location: ../../../vistas/profesores/aula/index.php"); exit; }
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) {
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

    $idCiclo = intval($modulo['idCiclo']);
    $misModulos = listarModulosDeProfesor($idProfesor);
    $esTutorCiclo = (!empty($_SESSION['esTutor']) && !empty($_SESSION['idCicloTutor']) && $_SESSION['idCicloTutor'] == $idCiclo);
    if (!in_array($idModulo, array_column($misModulos, 'idModulo')) && !$esTutorCiclo) {
        $_SESSION['errores'] = "No tienes permiso para gestionar recursos de este módulo.";
        throw new RuntimeException('modulo_no_autorizado');
    }

    $destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
    if ($idCarpeta) $destino .= "&carpeta=$idCarpeta";

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

            // Content-Type canónico a GUARDAR según la extensión — siempre este, nunca
            // el detectado por sniffing, para que un fichero manipulado no pueda
            // guardarse/servirse con un Content-Type influido por el atacante
            // (riesgo de confusión de tipo / XSS inline).
            $mimesCanonicos = [
                'pdf'  => 'application/pdf',
                'doc'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'txt'  => 'text/plain',
                'rtf'  => 'application/rtf',
                'odt'  => 'application/vnd.oasis.opendocument.text',
                'xls'  => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
                'csv'  => 'text/csv',
                'ppt'  => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'odp'  => 'application/vnd.oasis.opendocument.presentation',
                'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'png'  => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
                'zip'  => 'application/zip',
                'rar'  => 'application/x-rar-compressed',
            ];

            // Variantes de MIME *detectado* aceptables por extensión — varias versiones
            // de libmagic informan los formatos Office/zip/rar de forma distinta, así
            // que la validación necesita una lista blanca real por extensión, no un único valor esperado.
            $mimesAceptados = [
                'pdf'  => ['application/pdf'],
                'doc'  => ['application/msword', 'application/x-ole-storage', 'application/vnd.ms-office'],
                'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
                'txt'  => ['text/plain'],
                'rtf'  => ['text/rtf', 'application/rtf'],
                'odt'  => ['application/vnd.oasis.opendocument.text', 'application/zip'],
                'xls'  => ['application/vnd.ms-excel', 'application/x-ole-storage', 'application/vnd.ms-office'],
                'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
                'ods'  => ['application/vnd.oasis.opendocument.spreadsheet', 'application/zip'],
                'csv'  => ['text/csv', 'text/plain'],
                'ppt'  => ['application/vnd.ms-powerpoint', 'application/x-ole-storage', 'application/vnd.ms-office'],
                'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
                'odp'  => ['application/vnd.oasis.opendocument.presentation', 'application/zip'],
                'jpg'  => ['image/jpeg'], 'jpeg' => ['image/jpeg'],
                'png'  => ['image/png'], 'gif' => ['image/gif'], 'webp' => ['image/webp'],
                'zip'  => ['application/zip'],
                'rar'  => ['application/x-rar', 'application/x-rar-compressed', 'application/vnd.rar'],
            ];

            // Rechazar ante CUALQUIER discrepancia (no solo una lista negra de palabras clave)
            // — si se puede detectar un MIME y no es una de las variantes válidas conocidas
            // para esa extensión, la subida se rechaza directamente.
            if ($mimeReal !== '' && isset($mimesAceptados[$ext]) && !in_array($mimeReal, $mimesAceptados[$ext], true)) {
                $errores[] = "$nombreOrig: el contenido no coincide con la extensión ($ext).";
                continue;
            }

            if (($usadoCiclo + $tamanio) > $limiteCiclo) {
                $errores[] = "$nombreOrig: se superaría el límite de almacenamiento del ciclo.";
                continue;
            }
            $usadoCiclo += $tamanio;

            // 4. Nombre aleatorio para evitar colisiones y exposición del nombre original
            $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
            $tmpName       = $_FILES['archivos']['tmp_name'][$i];

            $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
            if (isset($imgMimes[$ext])) ImageOptimizer::optimize($tmpName, $imgMimes[$ext]); // optimizar el temporal ANTES de subir a R2

            $contentType = $mimesCanonicos[$ext] ?? 'application/octet-stream';
            $bytes       = file_get_contents($tmpName);
            $subioOk     = $bytes !== false && R2Client::putObject('aula/archivos/' . $nombreArchivo, $bytes, $contentType);
            @unlink($tmpName);

            if ($subioOk) {
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
                if (function_exists('enviarNotificacionesFirebaseSimultaneas')) {
                    $tokens = obtenerTokensFCMPorCicloAula($modulo['idCiclo']);
                    $tituloPush  = 'Nuevos recursos · ' . $modulo['nombreModulo'];
                    $mensajePush = $subidos . ' recurso(s) publicado(s) el ' . date('d/m/Y H:i');
                    enviarNotificacionesFirebaseSimultaneas($tokens, $tituloPush, $mensajePush, 'aula_archivo_nuevo', ['idModulo' => (int)$modulo['idModulo']]);
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
