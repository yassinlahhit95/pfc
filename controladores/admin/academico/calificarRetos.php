<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$hayError = false;

if (isset($_POST['guardarNotasReto'])) {
    if (!Security::validateCSRFToken(null, false)) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/calificacionesRetos.php");
        exit;
    }
    $idReto = (int)($_POST['idReto'] ?? 0);
    $idCiclo = (int)($_POST['idCiclo'] ?? 0);
    $idModulo = (int)($_POST['idModulo'] ?? 0);

    $listaIdsEstudiantes = $_POST['estudiantes'] ?? [];
    $listaNotas = $_POST['notas'] ?? [];

    for ($i = 0; $i < count($listaIdsEstudiantes); $i++) {
        $idEstudiante = trim($listaIdsEstudiantes[$i]);
        $nota = trim($listaNotas[$i]);

        if (!empty($nota)) {
            if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
                $hayError = true;
            }
        }

        if (!$hayError) {
            if (empty($nota)) {
                eliminarCalificacionReto($idEstudiante, $idReto);
            } else {
                if (!calificarReto($idEstudiante, $idReto, $nota)) {
                    $hayError = true;
                }
            }
        }

        if ($hayError) break;
    }

    if ($hayError) {
        $_SESSION['errores'] = "Ocurrió un error al procesar las notas. Todas las notas deben ser valores numéricos comprendidos entre 0 y 10.";
    } else {
        registrarAccion('calificar_retos', 'calificaciones_retos', $idReto, "Ciclo $idCiclo");
        $_SESSION['exito'] = "Las notas del reto han sido guardadas correctamente.";
    }

    header("Location: ../../../vistas/admin/academico/calificacionesRetos.php?idCiclo=$idCiclo&idModulo=$idModulo&idReto=$idReto");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/calificacionesRetos.php");
exit;
