<?php
// ══════════════════════════════════════════════════════════════════════
// RÚBRICAS — capa de acceso a datos genérica, reutilizable desde retos,
// TFG o FCT según el campo `ambito`. Ver migrate_db.php sección 12 para el
// esquema (rubrics/rubric_criteria). No incluye vistas de calificación por
// rúbrica (que un profesor puntúe cada criterio) — eso es una funcionalidad
// de UI más amplia, fuera del alcance de "hacer el motor de notas
// configurable" (igual que FCT en modelos/fct.php).
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/conectar.php";

function listarRubricasPorAmbito(string $ambito): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM rubrics WHERE ambito = ? AND activo = 1 ORDER BY nombre ASC");
    mysqli_stmt_bind_param($stmt, "s", $ambito);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = [];
    while ($fila = mysqli_fetch_assoc($res)) $out[] = $fila;
    return $out;
}

function obtenerRubricaConCriterios(int $idRubrica): ?array {
    $rubrica = dbFetchOne("SELECT * FROM rubrics WHERE idRubrica = ?", "i", $idRubrica);
    if (!$rubrica) return null;

    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM rubric_criteria WHERE idRubrica = ? ORDER BY orden ASC");
    mysqli_stmt_bind_param($stmt, "i", $idRubrica);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $criterios = [];
    while ($fila = mysqli_fetch_assoc($res)) $criterios[] = $fila;

    $rubrica['criterios'] = $criterios;
    return $rubrica;
}

function insertarRubrica(string $ambito, string $nombre): int|false {
    if (!in_array($ambito, ['reto', 'tfg', 'fct'], true)) return false;
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO rubrics (ambito, nombre) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $ambito, $nombre);
    if (!mysqli_stmt_execute($stmt)) return false;
    return mysqli_insert_id($con);
}

function insertarCriterioRubrica(int $idRubrica, string $descripcion, float $pesoCriterio, float $notaMaxima, int $orden): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO rubric_criteria (idRubrica, descripcion, pesoCriterio, notaMaxima, orden) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isddi", $idRubrica, $descripcion, $pesoCriterio, $notaMaxima, $orden);
    return mysqli_stmt_execute($stmt);
}

function eliminarRubrica(int $idRubrica): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE rubrics SET activo = 0 WHERE idRubrica = ?");
    mysqli_stmt_bind_param($stmt, "i", $idRubrica);
    return mysqli_stmt_execute($stmt);
}

// Calcula la nota (0-10, o en la escala que usen los criterios si notaMaxima
// varía) a partir de puntuaciones por criterio: [idCriterio => puntuacion].
// Puntuación ponderada por rubric_criteria.pesoCriterio, normalizada a la
// notaMaxima de cada criterio para que se puedan mezclar criterios con
// escalas distintas (p.ej. uno sobre 10 y otro sobre 5).
function calcularNotaPorRubrica(array $criterios, array $puntuaciones): ?float {
    $sumaPonderada = 0.0;
    $sumaPesos = 0.0;
    foreach ($criterios as $criterio) {
        $idCriterio = $criterio['idCriterio'];
        if (!isset($puntuaciones[$idCriterio]) || $puntuaciones[$idCriterio] === null) continue;
        $notaMaxima = (float)($criterio['notaMaxima'] ?: 10);
        $normalizada = ((float)$puntuaciones[$idCriterio] / $notaMaxima) * 10; // a escala 0-10
        $peso = (float)($criterio['pesoCriterio'] ?: 1);
        $sumaPonderada += $normalizada * $peso;
        $sumaPesos += $peso;
    }
    return $sumaPesos > 0 ? $sumaPonderada / $sumaPesos : null;
}
