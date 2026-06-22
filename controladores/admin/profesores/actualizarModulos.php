<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarModulos'])) {
    $idProfesor = (int)($_POST['idProfesor'] ?? 0);
    $modulos    = $_POST['modulos'] ?? [];

    limpiarModulosProfesor($idProfesor);

    $hayError = false;
    foreach ($modulos as $idModulo) {
        if (!asociarModuloProfesor($idModulo, $idProfesor)) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        registrarAccion('actualizar_modulos', 'profesores', $idProfesor);
        $_SESSION['exito'] = "Los módulos del profesor han sido actualizados correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar asignar los módulos al profesor.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
