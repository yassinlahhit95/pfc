<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/regionalExporters.php");
        exit;
    }

    $idCiclo = (int)($_POST['idCiclo'] ?? 0);
    $archivoJson = trim($_POST['archivoCurriculum'] ?? '');
    $idConfig = (int)($_POST['idConfig'] ?? 0);

    if ($idCiclo <= 0 || empty($archivoJson)) {
        $_SESSION['errores'] = 'Por favor, selecciona un ciclo formativo y un plan de estudios válido.';
        header("Location: ../../../vistas/admin/academico/regionalExporters.php");
        exit;
    }

    $filePath = __DIR__ . "/../../../public/data/curriculums/" . basename($archivoJson);
    if (!file_exists($filePath)) {
        $_SESSION['errores'] = 'El archivo de plan de estudios no existe: ' . Security::escapeHtml($archivoJson);
        header("Location: ../../../vistas/admin/academico/regionalExporters.php");
        exit;
    }

    $jsonContent = file_get_contents($filePath);
    $data = json_decode($jsonContent, true);
    if (!is_array($data)) {
        $_SESSION['errores'] = 'El archivo de plan de estudios tiene un formato inválido.';
        header("Location: ../../../vistas/admin/academico/regionalExporters.php");
        exit;
    }

    $con = obtenerConexion();
    mysqli_begin_transaction($con);

    try {
        // Obtiene el primer tipo de evaluación de tipo 'ra_ce' para esta configuración
        $stmtTipo = mysqli_prepare($con, "SELECT idTipo FROM assessment_types WHERE idConfig = ? AND origen = 'ra_ce' ORDER BY orden LIMIT 1");
        mysqli_stmt_bind_param($stmtTipo, "i", $idConfig);
        mysqli_stmt_execute($stmtTipo);
        $resTipo = mysqli_stmt_get_result($stmtTipo);
        $idTipoEval = ($row = mysqli_fetch_assoc($resTipo)) ? (int)$row['idTipo'] : null;

        $modulosImportados = 0;
        $rasImportados = 0;
        $cesImportados = 0;

        foreach ($data as $modData) {
            $codigoMod = trim($modData['codigoModulo'] ?? '');
            if (empty($codigoMod)) continue;

            // Buscar el módulo correspondiente en la base de datos para este ciclo
            $stmtMod = mysqli_prepare($con, "SELECT idModulo FROM modulos WHERE idCiclo = ? AND codigoModulo = ? LIMIT 1");
            mysqli_stmt_bind_param($stmtMod, "is", $idCiclo, $codigoMod);
            mysqli_stmt_execute($stmtMod);
            $resMod = mysqli_stmt_get_result($stmtMod);
            $rowMod = mysqli_fetch_assoc($resMod);

            if (!$rowMod) continue; // Omitir módulos no configurados en este ciclo de la base de datos
            $idModulo = (int)$rowMod['idModulo'];

            // Borrar los RA y CE antiguos de este módulo para evitar duplicados
            // Como la FK de criterios_evaluacion tiene ON DELETE CASCADE, borrar de resultados_aprendizaje ya elimina los CE.
            $stmtClear = mysqli_prepare($con, "DELETE FROM resultados_aprendizaje WHERE idModulo = ?");
            mysqli_stmt_bind_param($stmtClear, "i", $idModulo);
            mysqli_stmt_execute($stmtClear);

            $modulosImportados++;

            foreach (($modData['ras'] ?? []) as $ra) {
                $codigoRA = trim($ra['codigo'] ?? '');
                $descRA = trim($ra['descripcion'] ?? '');
                $porcentaje = (float)($ra['porcentaje'] ?? 0);

                $stmtInsRA = mysqli_prepare($con, "INSERT INTO resultados_aprendizaje (idModulo, codigo, descripcion, porcentaje, idTipo) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtInsRA, "issdi", $idModulo, $codigoRA, $descRA, $porcentaje, $idTipoEval);
                mysqli_stmt_execute($stmtInsRA);
                $idRA = (int)mysqli_insert_id($con);
                $rasImportados++;

                foreach (($ra['ces'] ?? []) as $ce) {
                    $codigoCE = trim($ce['codigo'] ?? '');
                    $descCE = trim($ce['descripcion'] ?? '');

                    $stmtInsCE = mysqli_prepare($con, "INSERT INTO criterios_evaluacion (idRA, codigo, descripcion) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmtInsCE, "iss", $idRA, $codigoCE, $descCE);
                    mysqli_stmt_execute($stmtInsCE);
                    $cesImportados++;
                }
            }
        }

        mysqli_commit($con);
        $_SESSION['exito'] = "Plan de estudios oficial importado con éxito: {$modulosImportados} módulos emparejados, {$rasImportados} RAs creados, {$cesImportados} CEs creados.";
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('[AulaPro Importer] Failed to import: ' . $e->getMessage());
        $_SESSION['errores'] = 'Ocurrió un error al importar los datos: ' . $e->getMessage();
    }

    header("Location: ../../../vistas/admin/academico/regionalExporters.php");
    exit;
}
