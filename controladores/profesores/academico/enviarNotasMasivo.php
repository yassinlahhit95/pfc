<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/profesores/academico/resultadosFinales.php");
    exit;
}
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/academico/resultadosFinales.php");
    exit;
}

$hayError = false;

if (isset($_POST['idCiclo']) && !empty($_POST['idCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);

    require_once __DIR__ . "/../../../modelos/estudiantes.php";
    $estudiantesEnCiclo = listarEstudiantesPorCiclo($idCiclo);

    if (empty($estudiantesEnCiclo)) {
        $_SESSION['errores'] = "No se han encontrado estudiantes matriculados en este ciclo formativo para efectuar el envío de notas.";
    } else {
        $enviados = enviarEmailNotasClase((int)$idCiclo);

        if ($enviados > 0) {
            registrarAccion('enviar_notas_masivo_profesor', 'ciclos', (int)$idCiclo, "$enviados emails enviados");
            $_SESSION['exito'] = "Se han enviado $enviados correos electrónicos con éxito.";
        } else {
            $_SESSION['errores'] = "No se pudieron enviar los correos electrónicos. Por favor, verifique la configuración del servidor de correo saliente.";
        }
    }
} else {
    $_SESSION['errores'] = "El identificador del ciclo formativo no ha sido proporcionado.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/academico/resultadosFinales.php?idCiclo=" . ($idCiclo ?? ''));
exit;
