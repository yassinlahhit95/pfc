<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/RateLimiter.php";
require_once __DIR__ . "/../../modelos/admisiones.php";
require_once __DIR__ . "/../../modelos/ciclos.php";

// Endpoint PÚBLICO: limitación por IP para frenar enumeración de DNIs y spam.
if (!RateLimiter::allow(obtenerConexion(), 'admisiones_publico', 30, 300, 900)) {
    http_response_code(429);
    echo json_encode(['status' => 'error', 'message' => 'Demasiadas peticiones. Inténtalo más tarde.']);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check_dni':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'DNI no proporcionado']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        // Return only existence — never expose PII to unauthenticated callers.
        if ($registro) {
            echo json_encode(['status' => 'exists']);
        } else {
            echo json_encode(['status' => 'new']);
        }
        break;

    case 'consultar_estado':
        $dni = $_POST['dni'] ?? '';
        if (empty($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'DNI no proporcionado']);
            break;
        }
        $registro = obtenerPreMatriculaPorDni($dni);
        if ($registro) {
            // Only non-sensitive status fields — no name, email, phone or tutor data.
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'estado' => $registro['estado'],
                    'ciclo'  => $registro['nombreCiclo'],
                    'fecha'  => date('d/m/Y', strtotime($registro['fechaSolicitud']))
                ]
            ]);
        } else {
            echo json_encode(['status' => 'not_found', 'message' => 'No se ha encontrado ninguna solicitud con ese DNI']);
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
            echo json_encode(['status' => 'error', 'message' => 'Faltan campos obligatorios del alumno o del tutor']);
            break;
        }

        if (!Security::validateEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del correo electrónico no es válido.']);
            break;
        }
        if (!empty($tutorData['email']) && !Security::validateEmail($tutorData['email'])) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del correo del tutor no es válido.']);
            break;
        }
        if (!empty($telefono) && !Security::validatePhone($telefono)) {
            echo json_encode(['status' => 'error', 'message' => 'El formato del teléfono no es válido.']);
            break;
        }

        // Validar que no exista ya una solicitud con el mismo DNI o email
        if (obtenerPreMatriculaPorDni($dni)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud registrada con este DNI.']);
            break;
        }

        if (obtenerPreMatriculaPorEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Ya existe una solicitud registrada con este correo electrónico.']);
            break;
        }

        $id = crearPreMatricula($dni, $nombre, $apellidos, $email, $telefono, $idCiclo, $curso, $tutorData);
        if ($id) {
            $_SESSION['admisiones_id'] = $id;
            // Generar PDF de confirmación de solicitud
            require_once __DIR__ . "/../comunes/pdf_helper.php";
            
            // Obtener nombre del ciclo para el PDF
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

            $pdfPath = generarPDFAceptacion($pdfData);
            if ($pdfPath) {
                registrarArchivoPreMatricula($id, 'DOCUMENTO_ACEPTACION', 'Formulario_Aceptacion.pdf', $pdfPath);
            }

            echo json_encode(['status' => 'success', 'idPreMatricula' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar los datos']);
        }
        break;

    case 'upload':
        $idPreMatricula = (int)($_POST['idPreMatricula'] ?? 0);
        $tipoRaw = $_POST['tipoDocumento'] ?? '';
        // Lista blanca estricta del tipo de documento (nunca va a la ruta del fichero)
        $tiposPermitidos = ['DNI_FRONTAL', 'DNI_REVERSO', 'EXPEDIENTE', 'FOTO', 'OTRO'];
        $tipo = in_array($tipoRaw, $tiposPermitidos, true) ? $tipoRaw : 'OTRO';

        if ($idPreMatricula < 1 || empty($_FILES['archivo'])) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos para la subida']);
            break;
        }

        // La solicitud debe existir: impide adjuntar ficheros a IDs arbitrarios.
        if (!obtenerPreMatriculaPorId($idPreMatricula)) {
            echo json_encode(['status' => 'error', 'message' => 'Solicitud no encontrada']);
            break;
        }

        $file = $_FILES['archivo'];

        // 1) Error de subida y tamaño máximo
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 'error', 'message' => 'Error en la subida del archivo']);
            break;
        }
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] <= 0 || $file['size'] > $maxBytes) {
            echo json_encode(['status' => 'error', 'message' => 'El archivo supera el tamaño permitido (5 MB)']);
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
            echo json_encode(['status' => 'error', 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG o PDF.']);
            break;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) finfo_close($finfo);
        if ($realMime !== $allowed[$ext]) {
            echo json_encode(['status' => 'error', 'message' => 'El contenido no coincide con la extensión del archivo.']);
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
            echo json_encode(['status' => 'error', 'message' => 'Archivo corrupto o no válido.']);
            break;
        }

        // 3) Nombre aleatorio: sin datos del usuario en la ruta → sin path traversal ni sobrescritura.
        $newName = 'adm_' . $idPreMatricula . '_' . $tipo . '_' . bin2hex(random_bytes(16)) . '.' . $ext;
        $destDir = __DIR__ . '/../../public/uploads/admisiones/';
        if (!is_dir($destDir)) { @mkdir($destDir, 0755, true); }
        $dest = $destDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            @chmod($dest, 0644);
            $nombreOriginalLimpio = mb_substr(basename($file['name']), 0, 150);
            registrarArchivoPreMatricula($idPreMatricula, $tipo, $nombreOriginalLimpio, "/public/uploads/admisiones/" . $newName);
            echo json_encode(['status' => 'success', 'filename' => $nombreOriginalLimpio]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo']);
        }
        break;

    case 'finalize':
        $idPreMatricula = (int)($_POST['idPreMatricula'] ?? 0);
        if ($idPreMatricula <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            break;
        }
        // Verify the caller owns this application (set during step1).
        if (empty($_SESSION['admisiones_id']) || (int)$_SESSION['admisiones_id'] !== $idPreMatricula) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
            break;
        }
        actualizarEstadoPreMatricula($idPreMatricula, 'EN_REVISION');
        unset($_SESSION['admisiones_id']);
        echo json_encode(['status' => 'success']);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>
