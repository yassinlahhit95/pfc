<?php
require_once __DIR__ . "/../../../include/StudentGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/cola_emails.php";
require_once __DIR__ . "/../../../config/Config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $idFCT = (int)($_POST['idFCT'] ?? 0);
    $idEstudiante = $_SESSION['idEstudiante'];

    if ($idFCT <= 0) {
        $_SESSION['errores'] = 'Prácticas no especificadas.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $con = obtenerConexion();

    // Verify FCT ownership and get details
    $stmtFct = mysqli_prepare($con, "
        SELECT f.idFCT, f.tutorEmpresa, f.emailTutorEmpresa, e.nombreEstudiante 
        FROM fct f
        INNER JOIN estudiantes e ON f.idEstudiante = e.idEstudiante
        WHERE f.idFCT = ? AND f.idEstudiante = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmtFct, "ii", $idFCT, $idEstudiante);
    mysqli_stmt_execute($stmtFct);
    $fctData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtFct));

    if (!$fctData) {
        $_SESSION['errores'] = 'No tienes permisos para esta operación.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $emailTutor = trim($fctData['emailTutorEmpresa'] ?? '');
    $nombreTutor = trim($fctData['tutorEmpresa'] ?? 'Tutor de Empresa');
    $nombreEstudiante = trim($fctData['nombreEstudiante']);

    if (empty($emailTutor)) {
        $_SESSION['errores'] = 'El email de tu tutor de empresa no está configurado. Contacta con tu tutor docente.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    // Comprueba si hay logs pendientes de firmar
    $stmtCount = mysqli_prepare($con, "SELECT COUNT(*) FROM fct_diarios WHERE idFCT = ? AND estado = 'pendiente'");
    mysqli_stmt_bind_param($stmtCount, "i", $idFCT);
    mysqli_stmt_execute($stmtCount);
    $resCount = mysqli_stmt_get_result($stmtCount);
    $count = mysqli_fetch_row($resCount)[0] ?? 0;

    if ($count == 0) {
        $_SESSION['errores'] = 'No tienes registros pendientes de firma.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    // Generate token and assign to pending logs
    $token = bin2hex(random_bytes(32));
    
    $stmtUpd = mysqli_prepare($con, "UPDATE fct_diarios SET tokenAprobacion = ? WHERE idFCT = ? AND estado = 'pendiente'");
    mysqli_stmt_bind_param($stmtUpd, "si", $token, $idFCT);
    
    if (mysqli_stmt_execute($stmtUpd)) {
        // Construct email
        $config = Config::getInstance();
        $appUrl = rtrim($config->get('APP_URL', ''), '/');
        if (empty($appUrl)) {
            // Alternativa para desarrollo
            $appUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/pfc';
        }
        
        $link = $appUrl . "/fct_firmar.php?token=" . $token;

        $asunto = "AulaPro - Firma digital de diario de prácticas FCT / Dual - " . $nombreEstudiante;
        
        $html = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; padding: 24px; border-radius: 8px;'>
            <h2 style='color: #1e3a8a; margin-top: 0;'>Revisión de Diario de Prácticas FCT / Dual</h2>
            <p>Estimado/a <strong>" . Security::escapeHtml($nombreTutor) . "</strong>,</p>
            <p>El estudiante <strong>" . Security::escapeHtml($nombreEstudiante) . "</strong> ha solicitado la revisión y firma digital de sus registros de actividades diarias correspondientes a su formación en su empresa.</p>
            <p>Por favor, revise el listado de actividades y confirme su validez haciendo clic en el siguiente enlace de acceso directo (no requiere inicio de sesión):</p>
            <div style='margin: 24px 0; text-align: center;'>
                <a href='" . $link . "' style='background-color: #f97316; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>REVISAR Y FIRMAR DIARIO</a>
            </div>
            <p style='font-size: 0.85rem; color: #64748b;'>Si el botón superior no funciona, copie y pegue esta URL en su navegador:</p>
            <p style='font-size: 0.85rem; color: #1e3a8a; word-break: break-all;'>" . $link . "</p>
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;' />
            <p style='font-size: 0.8rem; color: #64748b;'>Este es un mensaje automático enviado por AulaPro Campus Suite en representación de su centro educativo.</p>
        </div>
        ";

        encolarEmail($emailTutor, $nombreTutor, $asunto, $html);
        $_SESSION['exito'] = 'Solicitud de firma enviada correctamente a tu tutor de empresa por correo electrónico.';
    } else {
        $_SESSION['errores'] = 'Error al generar la solicitud de firma.';
    }

    header("Location: ../../../vistas/estudiantes/fct/diario.php");
    exit;
}
