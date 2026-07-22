<?php
// ══════════════════════════════════════════════════════════════════════
// MIGRACIÓN ÚNICA: cifra en su sitio los datos personales existentes en
// texto plano (RGPD Art. 32 — ver noDeploy/migrations/003_encrypt_pii_columns.sql
// y el include/Crypto.php nuevo). Idempotente — Crypto::isEncrypted() se
// salta cualquier valor ya cifrado, así que se puede re-ejecutar sin riesgo
// (p.ej. tras corregir un fallo a mitad de camino).
//
// Orden de despliegue: 1) aplicar el ALTER TABLE del archivo de migración,
// 2) desplegar este script junto con Crypto.php y los cambios de los
// modelos, 3) ejecutar este script cuando convenga — las lecturas siguen
// funcionando durante toda la ventana porque Crypto::decrypt() devuelve
// tal cual cualquier valor sin prefijo de versión reconocido (texto plano
// aún no migrado).
//
// Solo lectura salvo por los UPDATE explícitos de cada lote. CLI únicamente.
// ══════════════════════════════════════════════════════════════════════
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Solo se puede ejecutar desde CLI.\n");
}

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/include/Crypto.php';
require_once __DIR__ . '/modelos/conectar.php';

$con = obtenerConexion();

// tabla => [ 'idCol' => ..., 'campos' => ['columna' => 'det'|'rnd', ...] ]
$tablas = [
    'directores'  => ['idCol' => 'idDirector',   'campos' => [
        'dniDirector' => 'det', 'fechaNacimientoDirector' => 'rnd', 'direccionDirector' => 'rnd',
        'telefonoDirector' => 'rnd', 'observacionesDirector' => 'rnd', 'mfa_secret' => 'rnd', 'mfa_backup_codes' => 'rnd',
    ]],
    'estudiantes' => ['idCol' => 'idEstudiante', 'campos' => [
        'dniEstudiante' => 'det', 'fechaNacimientoEstudiante' => 'rnd', 'direccionEstudiante' => 'rnd',
        'telefonoEstudiante' => 'rnd', 'observacionesEstudiante' => 'rnd', 'mfa_secret' => 'rnd', 'mfa_backup_codes' => 'rnd',
    ]],
    'profesores'  => ['idCol' => 'idProfesor',   'campos' => ['mfa_secret' => 'rnd', 'mfa_backup_codes' => 'rnd']],
    'secretarias' => ['idCol' => 'idSecretaria', 'campos' => ['mfa_secret' => 'rnd', 'mfa_backup_codes' => 'rnd']],
    'tutores'     => ['idCol' => 'idTutor',      'campos' => ['mfa_secret' => 'rnd', 'mfa_backup_codes' => 'rnd']],
    'fct'         => ['idCol' => 'idFCT',        'campos' => [
        'tutorEmpresa' => 'rnd', 'emailTutorEmpresa' => 'rnd', 'telefonoEmpresa' => 'rnd', 'observaciones' => 'rnd',
    ]],
];

$batchSize = 200;

foreach ($tablas as $tabla => $cfg) {
    $idCol = $cfg['idCol'];
    $cols  = array_keys($cfg['campos']);
    $totalCifrados = 0;
    $totalOmitidos = 0;
    $totalFilas    = 0;
    $lastId        = 0;

    echo "=== $tabla ===\n";

    while (true) {
        // Paginación por clave (no OFFSET) — estable aunque se estén
        // actualizando filas durante el propio recorrido, y evita el coste
        // de un OFFSET creciente en tablas grandes.
        $sql  = "SELECT `$idCol`, `" . implode('`, `', $cols) . "` FROM `$tabla` WHERE `$idCol` > ? ORDER BY `$idCol` ASC LIMIT ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $lastId, $batchSize);
        mysqli_stmt_execute($stmt);
        $res   = mysqli_stmt_get_result($stmt);
        $filas = mysqli_fetch_all($res, MYSQLI_ASSOC);
        if (!$filas) break;

        mysqli_begin_transaction($con);
        try {
            foreach ($filas as $fila) {
                $totalFilas++;
                $lastId = max($lastId, (int)$fila[$idCol]);

                $sets  = [];
                $vals  = [];
                $types = '';
                foreach ($cfg['campos'] as $col => $modo) {
                    $val = $fila[$col];
                    if ($val === null || $val === '' || Crypto::isEncrypted($val)) {
                        $totalOmitidos++;
                        continue;
                    }
                    $vals[]  = $modo === 'det' ? Crypto::encryptDeterministic($val) : Crypto::encrypt($val);
                    $sets[]  = "`$col` = ?";
                    $types  .= 's';
                }
                if (!$sets) continue;

                $types .= 'i';
                $vals[] = $fila[$idCol];
                $upd = mysqli_prepare($con, "UPDATE `$tabla` SET " . implode(', ', $sets) . " WHERE `$idCol` = ?");
                mysqli_stmt_bind_param($upd, $types, ...$vals);
                mysqli_stmt_execute($upd);
                $totalCifrados += count($sets);
            }
            mysqli_commit($con);
        } catch (\Throwable $e) {
            mysqli_rollback($con);
            echo "[ERROR] lote hasta id=$lastId en $tabla: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "  procesadas $totalFilas filas (id <= $lastId)...\n";
    }

    echo "  $tabla: $totalCifrados valor(es) cifrado(s), $totalOmitidos ya cifrado(s)/vacío(s) omitido(s).\n";
}

echo "\nMigración de cifrado completada.\n";
exit(0);
