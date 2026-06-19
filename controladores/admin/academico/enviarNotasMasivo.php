<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../comunes/notificaciones_grades.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$hayError = false;

if (isset($_REQUEST['idCiclo']) && !empty($_REQUEST['idCiclo'])) {
    $idCiclo = trim($_REQUEST['idCiclo']);

    require_once __DIR__ . "/../../../modelos/estudiantes.php";
    $estudiantesEnCiclo = listarEstudiantesPorCiclo($idCiclo);

    if (empty($estudiantesEnCiclo)) {
        $_SESSION['errores'] = "No se han encontrado estudiantes matriculados en este ciclo formativo para efectuar el envío de notas.";
    } else {
        $enviados = enviarEmailNotasClase($idCiclo);

        if ($enviados > 0) {
            $_SESSION['exito'] = "Se han enviado $enviados correos electrónicos con las notas correspondientes de manera satisfactoria.";
        } else {
            $ultimoError = $_SESSION['ultimo_error_email'] ?? 'Sin respuesta del servidor';
            error_log('enviarNotasMasivo error: ' . $ultimoError);
            unset($_SESSION['ultimo_error_email']);
            $_SESSION['errores'] = "No se pudo efectuar el envío de los correos electrónicos. Por favor, verifique la configuración del servidor de correo saliente.";
        }
    }
} else {
    $_SESSION['errores'] = "El identificador del ciclo formativo no ha sido proporcionado.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/resultadosFinales.php?idCiclo=" . ($idCiclo ?? ''));
exit;
