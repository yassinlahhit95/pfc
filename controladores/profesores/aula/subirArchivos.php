<?php
require_once __DIR__ . "/../../../include/Security.php";
// Buffer de salida: garantiza que ninguna salida accidental (avisos del helper
// de Firebase, etc.) impida el header("Location: ...") final.
ob_start();

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

// Si los archivos superan post_max_size de PHP, $_POST llega vacío: avisar en vez de fallar en silencio.
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

$idProfesor  = $_SESSION['idProfesor'];
$idModulo    = intval($_POST['idModulo'] ?? 0);
$idCarpeta   = intval($_POST['idCarpeta'] ?? 0) ?: null;
$titulo      = trim($_POST['titulo'] ?? '');

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

            // 1. Validar extensión básica
            if (!in_array($ext, $permitidos)) {
                $errores[] = "$nombreOrig: tipo no permitido ($ext).";
                continue;
            }

            // 2. Validar tamaño
            if ($tamanio > $LIMITE_ARCHIVO) {
                $errores[] = "$nombreOrig: supera el límite de 20 MB.";
                continue;
            }

            // 3. Validar contenido real (MIME type) — sólo si la extensión fileinfo está disponible
            $mimeReal = '';
            if (function_exists('finfo_open')) {
                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mimeReal = (string) finfo_file($finfo, $_FILES['archivos']['tmp_name'][$i]);
                    finfo_close($finfo);
                }
            }

            // Mapeo simple para validación de contenido
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

            // Validación de MIME (si está en nuestro mapa estricto)
            if ($mimeReal !== '' && isset($mimesValidos[$ext]) && $mimesValidos[$ext] !== $mimeReal) {
                 // Nota: Algunos servidores pueden devolver mimes ligeramente diferentes para Office/Zips
                 // Solo bloqueamos si es una discrepancia crítica (ej: .txt que es un .exe)
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

            // 4. Nombre aleatorio seguro
            $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
            
            if (move_uploaded_file($_FILES['archivos']['tmp_name'][$i], $dir . $nombreArchivo)) {
                // Nombre visible: el título indicado (con la extensión real) o el nombre original del archivo
                $nombreVisible = $nombreOrig;
                if ($titulo !== '') {
                    $base   = $titulo;
                    $sufijo = '.' . $ext;
                    // Si el profesor escribió la misma extensión en el título, se la quitamos para no duplicarla
                    if ($ext !== '' && strtolower(substr($base, -strlen($sufijo))) === strtolower($sufijo)) {
                        $base = substr($base, 0, -strlen($sufijo));
                    }
                    $base = trim($base);
                    if ($base !== '') $nombreVisible = ($ext !== '') ? $base . '.' . $ext : $base;
                }
                // Evitar conflictos: mismo nombre + extensión en la misma ubicación → " (2)", " (3)"...
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
        $detalle = !empty($errores) ? implode(' ', $errores) : "No seleccionaste ningún archivo válido.";
        $_SESSION['errores'] = "No se pudo subir ningún archivo. " . $detalle;
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
