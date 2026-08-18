<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $idDiario = (int)($_POST['idDiario'] ?? 0);
    $idEstudiante = $_SESSION['idEstudiante'];

    if ($idDiario <= 0) {
        $_SESSION['errores'] = 'Registro no especificado.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    $con = obtenerConexion();

    // Verify ownership and status
    $stmtVerif = mysqli_prepare($con, "
        SELECT d.idDiario 
        FROM fct_diarios d
        INNER JOIN fct f ON d.idFCT = f.idFCT
        WHERE d.idDiario = ? AND f.idEstudiante = ? AND d.estado = 'pendiente'
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmtVerif, "ii", $idDiario, $idEstudiante);
    mysqli_stmt_execute($stmtVerif);
    if (!mysqli_fetch_assoc(mysqli_stmt_get_result($stmtVerif))) {
        $_SESSION['errores'] = 'No tienes permisos para eliminar este registro o ya ha sido procesado.';
        header("Location: ../../../vistas/estudiantes/fct/diario.php");
        exit;
    }

    // Elimina el log
    $stmtDel = mysqli_prepare($con, "DELETE FROM fct_diarios WHERE idDiario = ?");
    mysqli_stmt_bind_param($stmtDel, "i", $idDiario);

    if (mysqli_stmt_execute($stmtDel)) {
        $_SESSION['exito'] = 'Registro diario eliminado correctamente.';
    } else {
        $_SESSION['errores'] = 'Ocurrió un error al eliminar el registro.';
    }

    header("Location: ../../../vistas/estudiantes/fct/diario.php");
    exit;
}
