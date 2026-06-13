<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../../modelos/admisiones.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../include/AdminGuard.php"; // Asegurar que solo admins acceden

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_details':
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
            break;
        }
        $detalles = obtenerPreMatriculaPorId($id);
        $archivos = obtenerArchivosPreMatricula($id);
        echo json_encode(['status' => 'success', 'data' => $detalles, 'archivos' => $archivos]);
        break;

    case 'update_status':
        $id = $_POST['id'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';

        if (empty($id) || empty($estado)) {
            echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
            break;
        }

        $res = actualizarEstadoPreMatricula($id, $estado, $observaciones);
        
        if ($res) {
            $datos = obtenerPreMatriculaPorId($id);
            if ($datos) {
                require_once __DIR__ . "/../comunes/email_helper.php";
                $email = $datos['email'];
                $nombre = $datos['nombre'];
                $ciclo = $datos['nombreCiclo'];
                $subject = "Actualización de tu solicitud de admisión - AulaPro";
                $html = "";

                if ($estado === 'ADMITIDO') {
                    // CONVERSIÓN AUTOMÁTICA A ESTUDIANTE
                    
                    // GENERAR EMAIL INSTITUCIONAL: nombre.apellido1.apellido2@aulapro.com
                    function cleanString($string) {
                        $string = str_replace(' ', '.', trim($string));
                        $string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
                        return strtolower($string);
                    }

                    $nombreLimpio = cleanString($datos['nombre']);
                    $apellidosLimpios = cleanString($datos['apellidos']);
                    $emailInstitucional = $nombreLimpio . "." . $apellidosLimpios . "@aulapro.com";

                    // Verificar si ya existe por DNI para evitar duplicados
                    $con = obtenerConexion();
                    $checkSql = "SELECT idEstudiante FROM estudiantes WHERE dniEstudiante = ?";
                    $stmtCheck = mysqli_prepare($con, $checkSql);
                    mysqli_stmt_bind_param($stmtCheck, "s", $datos['dni']);
                    mysqli_stmt_execute($stmtCheck);
                    $resCheck = mysqli_stmt_get_result($stmtCheck);
                    
                    if (mysqli_num_rows($resCheck) === 0) {
                        // Generar password temporal
                        $tempPass = bin2hex(random_bytes(4)); // 8 caracteres aleatorios
                        $passHash = password_hash($tempPass, PASSWORD_DEFAULT);
                        
                        // Determinar 'curso'
                        $sqlNivel = "SELECT idNivel FROM ciclos WHERE idCiclo = ?";
                        $stmtNivel = mysqli_prepare($con, $sqlNivel);
                        mysqli_stmt_bind_param($stmtNivel, "i", $datos['idCiclo']);
                        mysqli_stmt_execute($stmtNivel);
                        $resNivel = mysqli_stmt_get_result($stmtNivel);
                        $filaNivel = mysqli_fetch_assoc($resNivel);
                        $cursoEnum = ($filaNivel['idNivel'] == 2) ? 'Grado Superior' : 'Grado Medio';

                        $sqlInsert = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, dniEstudiante, fechaAltaEstudiante, idCiclo, curso) 
                                      VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?)";
                        $stmtIns = mysqli_prepare($con, $sqlInsert);
                        mysqli_stmt_bind_param($stmtIns, "sssssis", 
                            $datos['nombre'], 
                            $emailInstitucional, 
                            $passHash, 
                            $datos['telefono'], 
                            $datos['dni'], 
                            $datos['idCiclo'], 
                            $cursoEnum
                        );
                        mysqli_stmt_execute($stmtIns);
                    } else {
                        $tempPass = "(Usa tu contraseña actual)";
                        // Si ya existe, recuperamos su email institucional para el correo
                        $sqlRecup = "SELECT emailEstudiante FROM estudiantes WHERE dniEstudiante = ?";
                        $stmtRec = mysqli_prepare($con, $sqlRecup);
                        mysqli_stmt_bind_param($stmtRec, "s", $datos['dni']);
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
                                <p><strong>URL:</strong> <a href='https://aulapro.yassin.agency/vistas/login.php'>Acceso AulaPro</a></p>
                                <p><strong>Email Institucional:</strong> <span style='color: #4f46e5; font-weight: bold;'>$emailInstitucional</span></p>
                                <p><strong>Contraseña Temporal:</strong> $tempPass</p>
                                <p><small>* Por seguridad, cambia tu contraseña al entrar por primera vez.</small></p>
                             </div>
                             <p>Ya puedes acceder para consultar tus horarios, materiales y comunicarte con tus profesores.</p>
                             <p>¡Te esperamos!</p>";
                } else if ($estado === 'RECHAZADO') {
                    $html = "<h2>Hola $nombre,</h2>
                             <p>Lamentamos informarte que tu solicitud para el ciclo <strong>$ciclo</strong> no ha sido aceptada en esta ocasión.</p>
                             " . ($observaciones ? "<div style='color: #666; font-style: italic; border-left: 4px solid #eee; padding-left: 15px;'><strong>Motivo:</strong> $observaciones</div>" : "") . "
                             <p>Gracias por tu interés en nuestro centro.</p>";
                } else if ($estado === 'SUBSANACION') {
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
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>
