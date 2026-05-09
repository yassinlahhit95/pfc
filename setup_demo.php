<?php
/**
 * Script de instalación de usuarios DEMO.
 * Ejecutar UNA VEZ desde el navegador: https://yassin.agency/setup_demo.php
 * Borrar el archivo después.
 */
require_once __DIR__ . '/modelos/conectar.php';

$con = obtenerConexion();
$hoy = date('Y-m-d');
$pass = 'demo2024';
$resultados = [];

// ── 1. ADMIN (director) ──────────────────────────────────────────────────────
$emailAdmin = 'admin@aulapro.com';
$existeAdmin = mysqli_fetch_assoc(mysqli_query($con, "SELECT idDirector FROM directores WHERE emailDirector = '$emailAdmin'"));

if ($existeAdmin) {
    // Actualiza solo la contraseña si ya existe
    mysqli_query($con, "UPDATE directores SET password = '$pass' WHERE emailDirector = '$emailAdmin'");
    $resultados[] = ['rol' => 'Admin', 'estado' => '✅ Ya existía — contraseña actualizada a <strong>demo2024</strong>'];
} else {
    $stmt = mysqli_prepare($con,
        "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoProfesor, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector, password)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    // Prueba sin columna telefonoProfesor (puede no existir en directores)
    $stmt = mysqli_prepare($con,
        "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector, password)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    if ($stmt) {
        $nombre = 'Admin Demo';
        $dni    = 'DEMO00001A';
        $tel    = '600000001';
        $fnac   = '1990-01-01';
        $dir    = 'Calle Demo 1';
        $ciudad = 'Madrid';
        $cp     = '28001';
        $obs    = 'Cuenta de demostración';
        mysqli_stmt_bind_param($stmt, 'sssssssssss', $nombre, $emailAdmin, $dni, $tel, $hoy, $fnac, $dir, $ciudad, $cp, $obs, $pass);
        $ok = mysqli_stmt_execute($stmt);
        $resultados[] = ['rol' => 'Admin', 'estado' => $ok ? '✅ Creado correctamente' : '❌ Error: ' . mysqli_error($con)];
    } else {
        $resultados[] = ['rol' => 'Admin', 'estado' => '❌ Error preparando consulta: ' . mysqli_error($con)];
    }
}

// ── 2. PROFESOR ──────────────────────────────────────────────────────────────
$emailProf = 'profesor@aulapro.com';
$existeProf = mysqli_fetch_assoc(mysqli_query($con, "SELECT idProfesor FROM profesores WHERE emailProfesor = '$emailProf'"));

if ($existeProf) {
    mysqli_query($con, "UPDATE profesores SET password = '$pass' WHERE emailProfesor = '$emailProf'");
    $resultados[] = ['rol' => 'Profesor', 'estado' => '✅ Ya existía — contraseña actualizada a <strong>demo2024</strong>'];
} else {
    $stmt = mysqli_prepare($con,
        "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor, password)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $nombre = 'Profesor Demo';
        $dni    = 'DEMO00002B';
        $tel    = '600000002';
        $fnac   = '1985-06-15';
        $dir    = 'Calle Demo 2';
        $ciudad = 'Barcelona';
        $cp     = '08001';
        $obs    = 'Cuenta de demostración';
        mysqli_stmt_bind_param($stmt, 'sssssssssss', $nombre, $emailProf, $tel, $dni, $dir, $fnac, $hoy, $ciudad, $cp, $obs, $pass);
        $ok = mysqli_stmt_execute($stmt);
        $resultados[] = ['rol' => 'Profesor', 'estado' => $ok ? '✅ Creado correctamente' : '❌ Error: ' . mysqli_error($con)];
    } else {
        $resultados[] = ['rol' => 'Profesor', 'estado' => '❌ Error preparando consulta: ' . mysqli_error($con)];
    }
}

// ── 3. ESTUDIANTE ────────────────────────────────────────────────────────────
$emailEst = 'estudiante@aulapro.com';
$existeEst = mysqli_fetch_assoc(mysqli_query($con, "SELECT idEstudiante FROM estudiantes WHERE emailEstudiante = '$emailEst'"));

if ($existeEst) {
    mysqli_query($con, "UPDATE estudiantes SET password = '$pass' WHERE emailEstudiante = '$emailEst'");
    $resultados[] = ['rol' => 'Estudiante', 'estado' => '✅ Ya existía — contraseña actualizada a <strong>demo2024</strong>'];
} else {
    // Busca el primer ciclo disponible
    $cicloRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT idCiclo FROM ciclos ORDER BY idCiclo ASC LIMIT 1"));
    $idCiclo  = $cicloRow['idCiclo'] ?? 1;

    $stmt = mysqli_prepare($con,
        "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, password)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $nombre = 'Estudiante Demo';
        $dni    = 'DEMO00003C';
        $tel    = '600000003';
        $fnac   = '2000-03-20';
        $dir    = 'Calle Demo 3';
        $ciudad = 'Valencia';
        $cp     = '46001';
        $obs    = 'Cuenta de demostración';
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', $nombre, $emailEst, $tel, $fnac, $dni, $hoy, $dir, $ciudad, $cp, $obs, $idCiclo, $pass);
        $ok = mysqli_stmt_execute($stmt);
        $resultados[] = ['rol' => 'Estudiante (ciclo #' . $idCiclo . ')', 'estado' => $ok ? '✅ Creado correctamente' : '❌ Error: ' . mysqli_error($con)];
    } else {
        $resultados[] = ['rol' => 'Estudiante', 'estado' => '❌ Error preparando consulta: ' . mysqli_error($con)];
    }
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Setup Demo — AulaPro</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 600px; margin: 60px auto; padding: 0 20px; background: #f9fafb; color: #111; }
    h1 { font-size: 1.4rem; color: #4f46e5; margin-bottom: 4px; }
    p.sub { color: #6b7280; font-size: 0.9rem; margin-bottom: 28px; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    th { background: #4f46e5; color: #fff; padding: 12px 16px; text-align: left; font-size: 0.85rem; font-weight: 600; }
    td { padding: 12px 16px; border-bottom: 1px solid #f3f4f6; font-size: 0.9rem; }
    tr:last-child td { border-bottom: none; }
    .aviso { margin-top: 24px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; padding: 14px 16px; font-size: 0.85rem; color: #92400e; }
    .credenciales { margin-top: 20px; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 16px; font-size: 0.88rem; }
    code { background: #e0e7ff; padding: 2px 6px; border-radius: 4px; font-family: monospace; }
  </style>
</head>
<body>
  <h1>🚀 Setup Demo — AulaPro</h1>
  <p class="sub">Resultado de la inserción de usuarios de demostración.</p>

  <table>
    <tr><th>Rol</th><th>Resultado</th></tr>
    <?php foreach ($resultados as $r): ?>
    <tr><td><?= htmlspecialchars($r['rol']) ?></td><td><?= $r['estado'] ?></td></tr>
    <?php endforeach; ?>
  </table>

  <div class="credenciales">
    <strong>Credenciales de acceso:</strong><br><br>
    Admin → <code>admin@aulapro.com</code> / <code>demo2024</code><br>
    Profesor → <code>profesor@aulapro.com</code> / <code>demo2024</code><br>
    Estudiante → <code>estudiante@aulapro.com</code> / <code>demo2024</code>
  </div>

  <div class="aviso">
    ⚠️ <strong>Borra este archivo una vez comprobado:</strong> <code>setup_demo.php</code>
  </div>
</body>
</html>
