<?php
// ══════════════════════════════════════════════════════════════════════
// MIGRACIÓN DE DATOS: calificaciones_modulos → calificaciones_periodo
// ══════════════════════════════════════════════════════════════════════
// Ejecutar UNA VEZ tras activar feature_academico_config si ya había notas
// introducidas antes de configurar el motor (para que el histórico exista en
// la nueva estructura, no solo las notas que se introduzcan a partir de
// ahora). Usa el mismo puente que sincronizarCalificacionPeriodo() —
// idempotente: se puede ejecutar varias veces sin duplicar filas
// (ON DUPLICATE KEY UPDATE dentro de sincronizarCalificacionPeriodo()).
// No borra ni modifica calificaciones_modulos.
// Acceso: solo CLI o sesión de administrador (nunca público).
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/include/Security.php';
require_once __DIR__ . '/modelos/calificaciones.php';

$esCli = php_sapi_name() === 'cli';
if (!$esCli && empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    exit("403 — Solo un administrador con sesión iniciada puede ejecutar esta migración.\n");
}

$configActiva = obtenerConfigAcademicaActiva();
if (!$configActiva) {
    exit("No hay ninguna configuración académica. Ejecuta migrate_db.php primero.\n");
}
$idConfig = (int)$configActiva['idConfig'];

$con = obtenerConexion();
$res = mysqli_query($con, "SELECT * FROM calificaciones_modulos");
$total = 0;
$migradas = 0;
while ($fila = mysqli_fetch_assoc($res)) {
    $total++;
    sincronizarCalificacionPeriodo(
        (int)$fila['idEstudiante'], (int)$fila['idModulo'], $idConfig,
        $fila['nota_1ev']    !== null ? (float)$fila['nota_1ev']    : null, $fila['estado_1ev'],
        $fila['nota_1final'] !== null ? (float)$fila['nota_1final'] : null, $fila['estado_1final'],
        $fila['nota_2ev']    !== null ? (float)$fila['nota_2ev']    : null, $fila['estado_2ev'],
        $fila['nota_2final'] !== null ? (float)$fila['nota_2final'] : null, $fila['estado_2final']
    );
    $migradas++;
}

echo "$migradas / $total filas de calificaciones_modulos sincronizadas en calificaciones_periodo.\n";
echo "calificaciones_modulos no se ha tocado (sigue siendo la fuente para el formulario de notas actual).\n";
