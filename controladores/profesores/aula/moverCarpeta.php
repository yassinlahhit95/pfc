<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/recursos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];
$id         = intval($_POST['id'] ?? 0);              // carpeta a mover
$destino    = intval($_POST['destino'] ?? 0) ?: null; // nuevo padre (0 = raíz)
$idModulo   = intval($_POST['modulo'] ?? 0);
$regresar   = intval($_POST['regresar'] ?? 0);        // carpeta actual a la que volver

if ($id > 0) {
    $carpeta = obtenerCarpetaAulaPorId($id);
    if ($carpeta && $carpeta['idProfesor'] == $idProfesor) {
        if (moverCarpetaAula($id, $destino)) {
            $_SESSION['exito'] = "La carpeta ha sido movida correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo mover la carpeta a ese destino.";
        }
        $idModulo = $idModulo ?: $carpeta['idModulo'];
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$dest = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($regresar) $dest .= "&carpeta=$regresar";
header("Location: $dest");
exit;
