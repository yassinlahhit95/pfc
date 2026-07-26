<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json');
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/RateLimiter.php";
require_once __DIR__ . "/../../modelos/admisiones.php";
require_once __DIR__ . "/../../modelos/ciclos.php";
require_once __DIR__ . "/../../modelos/configuracion.php";
require_once __DIR__ . "/../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../include/R2Client.php";

// ══════════════════════════════════════════════════════════════════════
// LÍMITE DE TASA
// ══════════════════════════════════════════════════════════════════════
// Endpoint público: limitación por IP para frenar enumeración de DNIs y spam
if (!RateLimiter::allow(obtenerConexion(), 'admisiones_publico', 30, 300, 900)) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Se ha superado el límite de solicitudes. Por favor, inténtelo de nuevo más tarde.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO POR ACCIÓN
// ══════════════════════════════════════════════════════════════════════
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check_dni':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'El Documento Nacional de Identidad (DNI) es obligatorio.']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        // Solo devuelve existencia — nunca exponer datos personales a llamadas no autenticadas
        if ($registro) {
            echo json_encode(['status' => 'exists']);
        } else {
            echo json_encode(['status' => 'new']);
        }
        break;

    case 'consultar_estado':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'El Documento Nacional de Identidad (DNI) es obligatorio.']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        if ($registro) {
            // Solo campos de estado no sensibles — sin nombre, email, teléfono ni datos del tutor
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'estado' => $registro['estado'],
                    'ciclo'  => $registro['nombreCiclo'],
                    'fecha'  => date('d/m/Y', strtotime($registro['fechaSolicitud']))
                ]
            ]);
        } else {
            echo json_encode(['status' => 'not_found', 'message' => 'No se ha encontrado ninguna solicitud asociada al DNI especificado.']);
        }
        break;

    case 'step1':
        $dni      = trim($_POST['dni'] ?? '');
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellidos= trim($_POST['apellidos'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $idCiclo  = (int)($_POST['idCiclo'] ?? 0);
        $cursosAdmPermitidos = ['1º', '2º', 'Grado Medio', 'Grado Superior'];
        $curso = in_array($_POST['curso'] ?? '', $cursosAdmPermitidos, true) ? $_POST['curso'] : '1º';

        $tutorData = [
            'nombre'     => trim($_POST['nombreTutor'] ?? ''),
            'dni'        => trim($_POST['dniTutor'] ?? ''),
            'email'      => trim($_POST['emailTutor'] ?? ''),
            'telefono'   => trim($_POST['telefonoTutor'] ?? ''),
            'parentesco' => trim($_POST['parentescoTutor'] ?? '')
        ];

        if (empty($dni) || empty($nombre) || empty($apellidos) || empty($email) || $idCiclo <= 0 || empty($tutorData['nombre']) || empty($tutorData['dni'])) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor, cumplimente todos los campos obligatorios del estudiante y del tutor.']);
            break;
        }

        if (!Security::validateDNI($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del DNI/NIE del estudiante no es válido.']);
            break;
        }
        if (!Security::validateDNI($tutorData['dni'])) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del DNI/NIE del tutor no es válido.']);
            break;
        }
        if (!Security::validateEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato de la dirección de correo electrónico del estudiante no es válido.']);
            break;
        }
        if (!empty($tutorData['email']) && !Security::validateEmail($tutorData['email'])) {
            echo json_encode(['status' => 'error', 'message' => 'El formato de la dirección de correo electrónico del tutor no es válido.']);
            break;
        }
        if (!empty($telefono) && !Security::validatePhone($telefono)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del número de teléfono no es válido (debe contener entre 9 y 15 dígitos).']);
            break;
        }

        // Validar que no exista ya una solicitud con el mismo DNI o email
        if (obtenerPreMatriculaPorDni($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud de preinscripción registrada con este DNI.']);
            break;
        }

        if (obtenerPreMatriculaPorEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud de preinscripción registrada con esta dirección de correo electrónico.']);
            break;
        }

        $id = crearPreMatricula($dni, $nombre, $apellidos, $email, $telefono, $idCiclo, $curso, $tutorData);
        if ($id) {
            $_SESSION['admisiones_id'] = $id;

            require_once __DIR__ . "/../comunes/pdf_helper.php";

            $con = obtenerConexion();
            $sqlC = "SELECT nombreCiclo FROM ciclos WHERE idCiclo = ?";
            $stC = mysqli_prepare($con, $sqlC);
            mysqli_stmt_bind_param($stC, "i", $idCiclo);
            mysqli_stmt_execute($stC);
            $resC = mysqli_stmt_get_result($stC);
            $filaC = mysqli_fetch_assoc($resC);

            $pdfData = [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'email' => $email,
                'telefono' => $telefono,
                'ciclo_nombre' => $filaC['nombreCiclo'] ?? 'Desconocido',
                'curso' => $curso,
                'nombreTutor' => $tutorData['nombre'],
                'dniTutor' => $tutorData['dni'],
                'parentescoTutor' => $tutorData['parentesco']
            ];

            $pdfPath = generarPDFAceptacion($pdfData, obtenerConfiguracionCentro());
            if ($pdfPath) {
                registrarArchivoPreMatricula($id, 'DOCUMENTO_ACEPTACION', 'Formulario_Aceptacion.pdf', $pdfPath);
            }

            echo json_encode(['status' => 'success', 'idPreMatricula' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error al guardar los datos de la solicitud. Por favor, inténtelo de nuevo.']);
        }
        break;

    case 'upload':
        $idPreMatricula = (int)($_POST['idPreMatricula'] ?? 0);
        $tipoRaw = $_POST['tipoDocumento'] ?? '';
        // Lista blanca estricta del tipo de documento (nunca va a la ruta del fichero)
        $tiposPermitidos = ['DNI_FRONTAL', 'DNI_REVERSO', 'EXPEDIENTE', 'FOTO', 'OTRO'];
        $tipo = in_array($tipoRaw, $tiposPermitidos, true) ? $tipoRaw : 'OTRO';

        if ($idPreMatricula < 1 || empty($_FILES['archivo'])) {
            echo json_encode(['status' => 'error', 'message' => 'No se han recibido los datos o el archivo requerido para la subida.']);
            break;
        }

        // La solicitud debe existir: impide adjuntar ficheros a IDs arbitrarios
        if (!obtenerPreMatriculaPorId($idPreMatricula)) {
            echo json_encode(['status' => 'error', 'message' => 'La solicitud de preinscripción no fue encontrada.']);
            break;
        }
        // Verificar que el solicitante es el propietario de esta solicitud (asignado en el paso 1):
        // sin esta comprobación, cualquiera podría adjuntar archivos a un idPreMatricula ajeno.
        if (empty($_SESSION['admisiones_id']) || (int)$_SESSION['admisiones_id'] !== $idPreMatricula) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No dispone de los permisos necesarios para realizar esta acción.']);
            break;
        }

        $file = $_FILES['archivo'];

        // 1) Error de subida y tamaño máximo
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error durante la transferencia del archivo. Por favor, inténtelo de nuevo.']);
            break;
        }
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] <= 0 || $file['size'] > $maxBytes) {
            echo json_encode(['status' => 'error', 'message' => 'El archivo supera el límite de tamaño permitido de 5 MB.']);
            break;
        }

        // 2) Lista blanca de extensión + MIME real (finfo) + firma de bytes (magic numbers)
        $allowed = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'pdf'  => 'application/pdf',
        ];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset($allowed[$ext])) {
            echo json_encode(['status' => 'error', 'message' => 'Formato de archivo no permitido. Únicamente se admiten archivos en formato JPG, PNG o PDF.']);
            break;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) finfo_close($finfo);
        if ($realMime !== $allowed[$ext]) {
            echo json_encode(['status' => 'error', 'message' => 'El tipo de contenido del archivo no coincide con su extensión declarada.']);
            break;
        }
        $fh = fopen($file['tmp_name'], 'rb');
        $magic = $fh ? fread($fh, 8) : '';
        if ($fh) fclose($fh);
        $okSignature =
            ($ext === 'pdf' && strncmp($magic, '%PDF', 4) === 0) ||
            (($ext === 'jpg' || $ext === 'jpeg') && strncmp($magic, "\xFF\xD8\xFF", 3) === 0) ||
            ($ext === 'png' && strncmp($magic, "\x89PNG\x0D\x0A\x1A\x0A", 8) === 0);
        if (!$okSignature) {
            echo json_encode(['status' => 'error', 'message' => 'El archivo está dañado o no es un documento válido.']);
            break;
        }

        // 3) Nombre aleatorio: sin datos del usuario en la ruta → sin path traversal ni sobrescritura
        $newName = 'adm_' . $idPreMatricula . '_' . $tipo . '_' . bin2hex(random_bytes(16)) . '.' . $ext;
        $tmpName = $file['tmp_name'];

        if ($ext !== 'pdf') ImageOptimizer::optimize($tmpName, $allowed[$ext]); // optimizar el temporal antes de subir a R2
        $bytes   = file_get_contents($tmpName);
        $subioOk = $bytes !== false && R2Client::putObject('admisiones/' . $newName, $bytes, $allowed[$ext]);
        @unlink($tmpName);

        if ($subioOk) {
            $nombreOriginalLimpio = mb_substr(basename($file['name']), 0, 150);
            registrarArchivoPreMatricula($idPreMatricula, $tipo, $nombreOriginalLimpio, "/public/uploads/admisiones/" . $newName);
            echo json_encode(['status' => 'success', 'filename' => $nombreOriginalLimpio]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar el archivo en el servidor. Por favor, inténtelo de nuevo.']);
        }
        break;

    case 'finalize':
        $idPreMatricula = (int)($_POST['idPreMatricula'] ?? 0);
        if ($idPreMatricula <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'El identificador de la solicitud no ha sido proporcionado.']);
            break;
        }
        // Verificar que el solicitante es el propietario de esta solicitud (asignado en el paso 1)
        if (empty($_SESSION['admisiones_id']) || (int)$_SESSION['admisiones_id'] !== $idPreMatricula) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No dispone de los permisos necesarios para realizar esta acción.']);
            break;
        }
        actualizarEstadoPreMatricula($idPreMatricula, 'revisando');
        unset($_SESSION['admisiones_id']);
        echo json_encode(['status' => 'success']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'La operación solicitada no es válida.']);
        break;
}
?>
