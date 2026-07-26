<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json');
require_once __DIR__ . "/../../../modelos/admisiones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$loginUrl = rtrim(Config::getInstance()->get('APP_URL', ''), '/') . '/vistas/login.php';
$action = $_GET['action'] ?? '';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
switch ($action) {

    case 'get_details':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'El identificador de la solicitud no ha sido proporcionado.']);
            break;
        }
        $detalles = obtenerPreMatriculaPorId($id);
        $archivos = obtenerArchivosPreMatricula($id);
        foreach ($archivos as &$archivo) {
            // rutaArchivo puede ser "/public/uploads/admisiones/<f>" (subida directa)
            // o "/public/uploads/admisiones/documentos/<f>" (PDF de aceptación generado
            // en el servidor) — se deriva la clave R2 quitando el prefijo común, en vez
            // de asumir una única subcarpeta plana.
            $relativo = ltrim(preg_replace('#^/?public/uploads/#', '', $archivo['rutaArchivo']), '/');
            $archivo['rutaArchivo'] = R2Client::documentoUrl(
                __DIR__ . '/../../../public/uploads/' . $relativo,
                '/public/uploads/' . $relativo,
                $relativo
            );
        }
        unset($archivo);
        echo json_encode(['status' => 'success', 'data' => $detalles, 'archivos' => $archivos]);
        break;

    case 'update_status':
        if (!Security::validateCSRFToken()) {
            echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida.']);
            break;
        }
        $id                = (int)($_POST['id'] ?? 0);
        $estadosPermitidos = ['revisando', 'aceptada', 'rechazada', 'subsanacion'];
        $estado            = in_array($_POST['estado'] ?? '', $estadosPermitidos, true) ? $_POST['estado'] : '';
        $observaciones     = htmlspecialchars(trim($_POST['observaciones'] ?? ''), ENT_QUOTES, 'UTF-8');

        if ($id <= 0 || empty($estado)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos requeridos para completar la actualización.']);
            break;
        }

        $res = actualizarEstadoPreMatricula($id, $estado, $observaciones);

        if ($res) {
            registrarAccionSecretaria('update_status', 'admisiones', $id, $estado);
            $datos = obtenerPreMatriculaPorId($id);

            if ($datos) {
                require_once __DIR__ . "/../../comunes/email_helper.php";
                $email  = $datos['email'];
                $nombre = $datos['nombre'];
                $ciclo  = $datos['nombreCiclo'];
                $subject = "Actualización de tu solicitud de admisión - AulaPro";
                $html    = "";

                if ($estado === 'aceptada') {
                    // ── Conversión automática a estudiante ──
                    $cleanString = function(string $s): string {
                        $s = trim($s);
                        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
                        $s = preg_replace('/[^a-zA-Z0-9.]/', '', str_replace(' ', '.', $s));
                        return strtolower($s);
                    };

                    $nombreLimpio    = $cleanString($datos['nombre']);
                    $apellidosLimpio = $cleanString($datos['apellidos']);
                    $emailInstitucional = $nombreLimpio . "." . $apellidosLimpio . "@aulapro.com";

                    $con = obtenerConexion();
                    $dniCifrado = Crypto::encryptDeterministic($datos['dni']);
                    $stmtCheck = mysqli_prepare($con, "SELECT idEstudiante FROM estudiantes WHERE dniEstudiante = ?");
                    mysqli_stmt_bind_param($stmtCheck, "s", $dniCifrado);
                    mysqli_stmt_execute($stmtCheck);
                    $resCheck = mysqli_stmt_get_result($stmtCheck);

                    if (mysqli_num_rows($resCheck) === 0) {
                        $tempPass = bin2hex(random_bytes(4));
                        $passHash = password_hash($tempPass, PASSWORD_BCRYPT, ['cost' => 12]);

                        $stmtNivel = mysqli_prepare($con, "SELECT idNivel FROM ciclos WHERE idCiclo = ?");
                        mysqli_stmt_bind_param($stmtNivel, "i", $datos['idCiclo']);
                        mysqli_stmt_execute($stmtNivel);
                        $resNivel  = mysqli_stmt_get_result($stmtNivel);
                        $filaNivel = mysqli_fetch_assoc($resNivel);
                        $cursoEnum = ($filaNivel['idNivel'] == 2) ? 'Grado Superior' : 'Grado Medio';

                        $telefonoCifrado = Crypto::encrypt($datos['telefono']);
                        $stmtIns = mysqli_prepare($con,
                            "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, dniEstudiante, fechaAltaEstudiante, idCiclo, curso)
                             VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?)");
                        mysqli_stmt_bind_param($stmtIns, "sssssis",
                            $datos['nombre'], $emailInstitucional, $passHash,
                            $telefonoCifrado, $dniCifrado, $datos['idCiclo'], $cursoEnum);
                        mysqli_stmt_execute($stmtIns);
                        $idNuevoEstudiante = mysqli_insert_id($con);

                        // ── Conversión del tutor legal ──
                        if (!empty($datos['dniTutor'])) {
                            $stmtTC = mysqli_prepare($con, "SELECT idTutor FROM tutores WHERE dniTutor = ?");
                            mysqli_stmt_bind_param($stmtTC, "s", $datos['dniTutor']);
                            mysqli_stmt_execute($stmtTC);
                            $resTC = mysqli_stmt_get_result($stmtTC);

                            $idTutorFinal = null;
                            if (mysqli_num_rows($resTC) > 0) {
                                $filaT = mysqli_fetch_assoc($resTC);
                                $idTutorFinal = $filaT['idTutor'];
                            } else {
                                $passTutor = bin2hex(random_bytes(4));
                                $hashTutor = password_hash($passTutor, PASSWORD_BCRYPT, ['cost' => 12]);
                                $stmtTI = mysqli_prepare($con,
                                    "INSERT INTO tutores (nombreTutor, emailTutor, password, telefonoTutor, dniTutor) VALUES (?, ?, ?, ?, ?)");
                                mysqli_stmt_bind_param($stmtTI, "sssss",
                                    $datos['nombreTutor'], $datos['emailTutor'], $hashTutor,
                                    $datos['telefonoTutor'], $datos['dniTutor']);
                                mysqli_stmt_execute($stmtTI);
                                $idTutorFinal = mysqli_insert_id($con);

                                $subjT = "Bienvenida a AulaPro - Cuenta de Tutor Legal";
                                $htmlT = "<h3>Hola {$datos['nombreTutor']},</h3>
                                          <p>Su hijo/a <strong>{$datos['nombre']}</strong> ha sido admitido/a en AulaPro.</p>
                                          <p>Se le ha creado una cuenta de tutor para realizar el seguimiento académico.</p>
                                          <div style='background:#f3f4f6; padding:15px; border-radius:8px;'>
                                            <p><strong>URL:</strong> <a href='{$loginUrl}'>Acceso AulaPro</a></p>
                                            <p><strong>Usuario (Email):</strong> {$datos['emailTutor']}</p>
                                            <p><strong>Contraseña:</strong> $passTutor</p>
                                          </div>";
                                sendEmail($datos['emailTutor'], $subjT, $htmlT);
                            }

                            if ($idTutorFinal && $idNuevoEstudiante) {
                                $stmtR = mysqli_prepare($con,
                                    "INSERT IGNORE INTO estudiante_tutor (idEstudiante, idTutor, parentesco) VALUES (?, ?, ?)");
                                mysqli_stmt_bind_param($stmtR, "iis", $idNuevoEstudiante, $idTutorFinal, $datos['parentescoTutor']);
                                mysqli_stmt_execute($stmtR);
                            }
                        }
                    } else {
                        $tempPass = "(Usa tu contraseña actual)";
                        $stmtRec = mysqli_prepare($con, "SELECT emailEstudiante FROM estudiantes WHERE dniEstudiante = ?");
                        mysqli_stmt_bind_param($stmtRec, "s", $dniCifrado);
                        mysqli_stmt_execute($stmtRec);
                        $resRec = mysqli_stmt_get_result($stmtRec);
                        $filaRec = mysqli_fetch_assoc($resRec);
                        $emailInstitucional = $filaRec['emailEstudiante'];
                    }

                    $subject = "¡Enhorabuena! Has sido admitido en AulaPro";
                    $html = "<h2>Hola {$datos['nombre']},</h2>
                             <p>Nos complace informarte que tu solicitud de admisión para el ciclo <strong>$ciclo</strong> ha sido <strong>APROBADA</strong>.</p>
                             <p>Se ha generado tu nueva cuenta institucional para acceder a la plataforma:</p>
                             <div style='background: #f3f4f6; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                                <h3 style='margin-top: 0;'>Tus credenciales de acceso:</h3>
                                <p><strong>URL:</strong> <a href='{$loginUrl}'>Acceso AulaPro</a></p>
                                <p><strong>Email Institucional:</strong> <span style='color: #4f46e5; font-weight: bold;'>$emailInstitucional</span></p>
                                <p><strong>Contraseña Temporal:</strong> $tempPass</p>
                                <p><small>* Por seguridad, cambia tu contraseña al entrar por primera vez.</small></p>
                             </div>
                             <p>Ya puedes acceder para consultar tus horarios, materiales y comunicarte con tus profesores.</p>
                             <p>¡Te esperamos!</p>";

                } elseif ($estado === 'rechazada') {
                    $html = "<h2>Hola $nombre,</h2>
                             <p>Lamentamos informarte que tu solicitud para el ciclo <strong>$ciclo</strong> no ha sido aceptada en esta ocasión.</p>
                             " . ($observaciones ? "<div style='color: #666; font-style: italic; border-left: 4px solid #eee; padding-left: 15px;'><strong>Motivo:</strong> $observaciones</div>" : "") . "
                             <p>Gracias por tu interés en nuestro centro.</p>";

                } elseif ($estado === 'subsanacion') {
                    $subject = "Acción requerida: Documentación pendiente";
                    $html = "<h2>Hola $nombre,</h2>
                             <p>Hemos revisado tu solicitud para <strong>$ciclo</strong> y necesitamos que realices algunos cambios o aportes documentación adicional.</p>
                             <div style='background: #fffbeb; padding: 20px; border: 1px solid #fef3c7; border-radius: 10px; margin: 20px 0;'>
                                <strong>Instrucciones del centro:</strong><br>
                                $observaciones
                             </div>
                             <p>Puedes consultar el estado y subir lo solicitado en nuestro portal o respondiendo a este correo.</p>";
                }

                if ($html !== "") {
                    sendEmail($email, $subject, $html);
                }
            }

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error al actualizar la solicitud en el sistema.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'La operación solicitada no es válida.']);
        break;
}
