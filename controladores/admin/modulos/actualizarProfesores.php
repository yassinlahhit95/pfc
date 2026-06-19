<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarProfesores'])) {
    $idModulo   = (int)($_POST['idModulo'] ?? 0);
    $idProfesor = !empty($_POST['idProfesor']) ? (int)$_POST['idProfesor'] : 0;

    limpiarProfesoresModulo($idModulo);

    $hayError = false;
    if ($idProfesor > 0 && !asociarModuloProfesor($idModulo, $idProfesor)) {
        $hayError = true;
    }

    if (!$hayError) {
        $_SESSION['exito'] = "El profesor ha sido asignado al módulo correctamente.";
    } else {
        $_SESSION['errores'] = "No se pudo asignar el profesor al módulo.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
