<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarCarpeta'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/ejercicios/panel.php");
        exit;
    }
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $color       = trim($_POST['color'] ?? '#0ea5e9');
    $icono       = trim($_POST['icono'] ?? 'fa-folder');
    $idCiclo     = intval($_POST['idCiclo'] ?? 0);
    $idProfesor  = $_SESSION['idProfesor'];

    if (empty($nombre) || $idCiclo < 1) {
        $_SESSION['errores'] = "El nombre de la carpeta y el ciclo son campos obligatorios.";
    } else {
        if (insertarCarpeta($nombre, $descripcion, $color, $icono, $idProfesor, $idCiclo)) {
            $_SESSION['exito'] = "La carpeta ha sido creada correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo crear la carpeta.";
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
