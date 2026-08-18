<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $idFCT = (int)($_POST['idFCT'] ?? 0);
    $fecha = trim($_POST['fecha'] ?? '');
    $horas = (float)($_POST['horas'] ?? 0.0);
    $actividades = trim($_POST['actividades'] ?? '');
    $idEstudiante = $_SESSION['idEstudiante'];

    if ($idFCT <= 0 || empty($fecha) || $horas <= 0.0 || empty($actividades)) {
        $_SESSION['errores'] = 'Por favor, rellene todos los campos del registro diario con valores válidos.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $con = obtenerConexion();

    // Verify FCT ownership
    $stmtVerif = mysqli_prepare($con, "SELECT idFCT FROM fct WHERE idFCT = ? AND idEstudiante = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtVerif, "ii", $idFCT, $idEstudiante);
    mysqli_stmt_execute($stmtVerif);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($stmtVerif))) {
        $_SESSION['errores'] = 'No tienes permisos para registrar actividades en estas prácticas.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    // Insert log
    $stmtIns = mysqli_prepare($con, "INSERT INTO fct_diarios (idFCT, fecha, horas, actividades, estado) VALUES (?, ?, ?, ?, 'pendiente')");
    mysqli_stmt_bind_param($stmtIns, "isds", $idFCT, $fecha, $horas, $actividades);

    if (mysqli_stmt_execute($stmtIns)) {
        $_SESSION['exito'] = 'Registro diario guardado correctamente.';
    } else {
        $_SESSION['errores'] = 'Ocurrió un error al guardar el registro: ' . mysqli_error($con);
    }

    header("Location: ../../../vistas/estudiantes/fct/diario.php");
    exit;
}
