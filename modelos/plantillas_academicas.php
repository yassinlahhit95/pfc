<?php
// ══════════════════════════════════════════════════════════════════════
// PLANTILLAS ACADÉMICAS — snapshots JSON reutilizables para aplicar una
// configuración académica completa de una vez (igual que landing_config usa
// JSON para plantillas de la web pública; es el único precedente de este
// patrón en el proyecto, y el más adecuado aquí: una plantilla es un bloque
// flexible que se aplica de golpe, no algo que se consulte fila a fila).
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/academico_config.php";

function listarPlantillasAcademicas(): array {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT idPlantilla, nombre, descripcion, editable, creadoEn FROM academic_templates ORDER BY nombre ASC");
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

// Vuelca la configuración académica activa (si existe) a un array listo
// para json_encode — para guardar la configuración actual del centro como
// plantilla nueva ("Custom Center").
function exportarConfigComoArray(int $idConfig): array {
    $con = obtenerConexion();

    $periodos = listarPeriodosAcademicos($idConfig);
    $tipos = listarTiposEvaluacion($idConfig);

    return [
        'config' => obtenerConfigAcademicaPorId($idConfig), // solo se usa tipoEducacion al aplicar
        'grading_policy' => obtenerPoliticaCalificacion($idConfig),
        'promotion_rule' => obtenerReglasPromocion($idConfig),
        'periods' => $periodos,
        'assessment_types' => $tipos,
        'internship_config' => obtenerConfigFCT($idConfig),
        'tfg_config' => obtenerConfigTFG($idConfig),
        'challenge_config' => obtenerConfigRetos($idConfig),
    ];
}

function guardarPlantillaAcademica(string $nombre, string $descripcion, array $configuracion, bool $editable = true): int|false {
    $con = obtenerConexion();
    $json = json_encode($configuracion, JSON_UNESCAPED_UNICODE);
    $stmt = mysqli_prepare($con,
        "INSERT INTO academic_templates (nombre, descripcion, configuracionJson, editable) VALUES (?, ?, ?, ?)");
    $editableInt = (int)$editable;
    mysqli_stmt_bind_param($stmt, "sssi", $nombre, $descripcion, $json, $editableInt);
    if (!mysqli_stmt_execute($stmt)) return false;
    return mysqli_insert_id($con);
}

// Aplica una plantilla: crea una NUEVA academic_config (y sus tablas hijas)
// a partir del JSON guardado. No toca ninguna configuración existente — así
// que aplicar una plantilla nunca destruye la configuración vigente; hay que
// activar la nueva explícitamente (poner activo=1 en la que corresponda,
// desde el asistente). Devuelve el idConfig creado, o false si falla.
function aplicarPlantillaAcademica(int $idPlantilla, string $nombreNuevaConfig): int|false {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT configuracionJson FROM academic_templates WHERE idPlantilla = ?");
    mysqli_stmt_bind_param($stmt, "i", $idPlantilla);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$fila) return false;

    $datos = json_decode($fila['configuracionJson'], true);
    if (!is_array($datos)) return false;

    mysqli_begin_transaction($con);
    try {
        $tipoEdu = $datos['config']['tipoEducacion'] ?? 'otro';
        $stmt = mysqli_prepare($con, "INSERT INTO academic_config (nombre, tipoEducacion, activo) VALUES (?, ?, 0)");
        mysqli_stmt_bind_param($stmt, "ss", $nombreNuevaConfig, $tipoEdu);
        mysqli_stmt_execute($stmt);
        $idConfig = (int)mysqli_insert_id($con);

        $gp = $datos['grading_policy'] ?? [];
        $stmt = mysqli_prepare($con, "INSERT INTO grading_policies (idConfig, escalaMin, escalaMax, notaAprobado, decimales, pesoTfgEnMedia) VALUES (?, ?, ?, ?, ?, ?)");
        $escalaMin = (float)($gp['escalaMin'] ?? 0); $escalaMax = (float)($gp['escalaMax'] ?? 10);
        $notaAprobado = (float)($gp['notaAprobado'] ?? 5); $decimales = (int)($gp['decimales'] ?? 2);
        $pesoTfg = (float)($gp['pesoTfgEnMedia'] ?? 1);
        mysqli_stmt_bind_param($stmt, "iddidd", $idConfig, $escalaMin, $escalaMax, $notaAprobado, $decimales, $pesoTfg);
        mysqli_stmt_execute($stmt);

        $pr = $datos['promotion_rule'] ?? [];
        $stmt = mysqli_prepare($con, "INSERT INTO promotion_rules (idConfig, requiereTodosModulos, notaMinimaGlobal, permiteModulosPendientes) VALUES (?, ?, ?, ?)");
        $requiereTodos = (int)($pr['requiereTodosModulos'] ?? 1); $notaMinGlobal = (float)($pr['notaMinimaGlobal'] ?? 5);
        $permitePend = (int)($pr['permiteModulosPendientes'] ?? 0);
        mysqli_stmt_bind_param($stmt, "iidi", $idConfig, $requiereTodos, $notaMinGlobal, $permitePend);
        mysqli_stmt_execute($stmt);

        // Períodos: se insertan primero los no-recuperación para poder
        // remapear idPeriodoRecuperaDe (los IDs originales no se conservan).
        $mapaPeriodos = [];
        foreach (($datos['periods'] ?? []) as $p) {
            if (($p['tipo'] ?? '') === 'recuperacion') continue;
            $stmt = mysqli_prepare($con, "INSERT INTO academic_periods (idConfig, nombre, tipo, orden, peso) VALUES (?, ?, ?, ?, ?)");
            $peso = (float)($p['peso'] ?? 100);
            mysqli_stmt_bind_param($stmt, "issid", $idConfig, $p['nombre'], $p['tipo'], $p['orden'], $peso);
            mysqli_stmt_execute($stmt);
            $mapaPeriodos[$p['idPeriodo']] = (int)mysqli_insert_id($con);
        }
        foreach (($datos['periods'] ?? []) as $p) {
            if (($p['tipo'] ?? '') !== 'recuperacion') continue;
            $idOrigenMapeado = $mapaPeriodos[$p['idPeriodoRecuperaDe']] ?? null;
            $stmt = mysqli_prepare($con, "INSERT INTO academic_periods (idConfig, nombre, tipo, orden, peso, idPeriodoRecuperaDe) VALUES (?, ?, ?, ?, ?, ?)");
            $peso = (float)($p['peso'] ?? 100);
            mysqli_stmt_bind_param($stmt, "issidi", $idConfig, $p['nombre'], $p['tipo'], $p['orden'], $peso, $idOrigenMapeado);
            mysqli_stmt_execute($stmt);
        }

        foreach (($datos['assessment_types'] ?? []) as $t) {
            $stmt = mysqli_prepare($con,
                "INSERT INTO assessment_types (idConfig, nombre, notaMaxima, peso, obligatorio, recuperable, incluirEnMedia, origen, orden)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $notaMax = (float)($t['notaMaxima'] ?? 10); $peso = (float)($t['peso'] ?? 1);
            $obligatorio = (int)($t['obligatorio'] ?? 0); $recuperable = (int)($t['recuperable'] ?? 1);
            $incluir = (int)($t['incluirEnMedia'] ?? 1); $orden = (int)($t['orden'] ?? 1);
            mysqli_stmt_bind_param($stmt, "isddiiisi", $idConfig, $t['nombre'], $notaMax, $peso, $obligatorio, $recuperable, $incluir, $t['origen'], $orden);
            mysqli_stmt_execute($stmt);
        }

        $ic = $datos['internship_config'] ?? [];
        if ($ic) {
            $stmt = mysqli_prepare($con,
                "INSERT INTO internship_config (idConfig, habilitado, horasRequeridasDefecto, metodoEvaluacion, pesoEnMedia, requiereAprobarParaTitular)
                 VALUES (?, ?, ?, ?, ?, ?)");
            $habilitado = (int)($ic['habilitado'] ?? 0); $horas = (int)($ic['horasRequeridasDefecto'] ?? 0);
            $metodo = $ic['metodoEvaluacion'] ?? 'ambos'; $pesoFct = (float)($ic['pesoEnMedia'] ?? 0);
            $requiereApto = (int)($ic['requiereAprobarParaTitular'] ?? 1);
            mysqli_stmt_bind_param($stmt, "iiisdi", $idConfig, $habilitado, $horas, $metodo, $pesoFct, $requiereApto);
            mysqli_stmt_execute($stmt);
        }

        $tc = $datos['tfg_config'] ?? [];
        if ($tc) {
            $stmt = mysqli_prepare($con,
                "INSERT INTO tfg_config (idConfig, habilitado, requiereComite, requiereDefensa, notaMinima, pesoEnMedia, permiteRecuperacion)
                 VALUES (?, ?, ?, ?, ?, ?, ?)");
            $habilitadoTfg = (int)($tc['habilitado'] ?? 1); $comite = (int)($tc['requiereComite'] ?? 0);
            $defensa = (int)($tc['requiereDefensa'] ?? 0); $notaMin = (float)($tc['notaMinima'] ?? 5);
            $pesoTfgCfg = (float)($tc['pesoEnMedia'] ?? 1); $permiteRecup = (int)($tc['permiteRecuperacion'] ?? 1);
            mysqli_stmt_bind_param($stmt, "iiiiddi", $idConfig, $habilitadoTfg, $comite, $defensa, $notaMin, $pesoTfgCfg, $permiteRecup);
            mysqli_stmt_execute($stmt);
        }

        $cc = $datos['challenge_config'] ?? [];
        if ($cc) {
            $stmt = mysqli_prepare($con,
                "INSERT INTO challenge_config (idConfig, pesoDefecto, permiteGrupal, permiteFases, requiereRubrica, evaluacionPares)
                 VALUES (?, ?, ?, ?, ?, ?)");
            $pesoDef = (float)($cc['pesoDefecto'] ?? 1); $grupal = (int)($cc['permiteGrupal'] ?? 0);
            $fases = (int)($cc['permiteFases'] ?? 0); $rubrica = (int)($cc['requiereRubrica'] ?? 0);
            $pares = (int)($cc['evaluacionPares'] ?? 0);
            mysqli_stmt_bind_param($stmt, "idiiii", $idConfig, $pesoDef, $grupal, $fases, $rubrica, $pares);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($con);
        return $idConfig;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('[AulaPro] aplicarPlantillaAcademica: ' . $e->getMessage());
        return false;
    }
}
