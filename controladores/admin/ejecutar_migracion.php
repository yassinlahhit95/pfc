<?php
require_once __DIR__ . "/../../include/AdminGuard.php";
require_once __DIR__ . "/../../modelos/conectar.php";
$con = obtenerConexion();

$migraciones = [];

// Migration 1: esTutor and idCicloTutor on profesores
$res = mysqli_query($con, "SHOW COLUMNS FROM profesores LIKE 'esTutor'");
if (mysqli_num_rows($res) == 0) {
    $sql = "ALTER TABLE profesores
            ADD COLUMN esTutor tinyint(1) NOT NULL DEFAULT 0,
            ADD COLUMN idCicloTutor int(11) DEFAULT NULL,
            ADD CONSTRAINT fk_prof_ciclo_tutor FOREIGN KEY (idCicloTutor) REFERENCES ciclos (idCiclo) ON DELETE SET NULL";
    if (mysqli_query($con, $sql)) {
        $migraciones[] = ['ok', 'Columnas esTutor e idCicloTutor añadidas a la tabla profesores.'];
    } else {
        $migraciones[] = ['error', 'Error al añadir columnas: ' . mysqli_error($con)];
    }
} else {
    $migraciones[] = ['skip', 'Columnas esTutor / idCicloTutor ya existen en profesores.'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Migraciones BD</title>
<style>
body{font-family:monospace;padding:30px;background:#111;color:#eee;}
.ok{color:#4ade80;} .error{color:#f87171;} .skip{color:#94a3b8;}
a{color:#818cf8;} h2{color:#c4b5fd;}
</style>
</head>
<body>
<h2>Resultado de Migraciones</h2>
<?php foreach ($migraciones as [$tipo, $msg]): ?>
<p class="<?= $tipo ?>">[<?= strtoupper($tipo) ?>] <?= htmlspecialchars($msg) ?></p>
<?php endforeach; ?>
<p><a href="../../vistas/admin/inicio/dashboard.php">← Volver al panel</a></p>
</body>
</html>
