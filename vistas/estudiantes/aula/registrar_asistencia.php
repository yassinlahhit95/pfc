<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

$exito = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pin'])) {
    $pin = trim($_POST['pin']);
    $db = obtenerConexion();
    
    // Buscar si hay un módulo activo con este PIN
    $sql = "SELECT idModulo, idCiclo FROM modulos WHERE pinAsistencia = ? AND pinAsistenciaExpira >= NOW() LIMIT 1";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $pin);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    if ($modulo = mysqli_fetch_assoc($res)) {
        if ($modulo['idCiclo'] == $estudianteActual['idCiclo']) {
            // Registrar asistencia
            $hoy = date('Y-m-d');
            $idModulo = $modulo['idModulo'];
            
            // Comprobar si ya fichó hoy
            $sqlCheck = "SELECT idAsistencia FROM asistencias WHERE idEstudiante = ? AND idModulo = ? AND fecha = ?";
            $stmtCheck = mysqli_prepare($db, $sqlCheck);
            mysqli_stmt_bind_param($stmtCheck, "iis", $idEstudiante, $idModulo, $hoy);
            mysqli_stmt_execute($stmtCheck);
            $resCheck = mysqli_stmt_get_result($stmtCheck);
            
            if (mysqli_fetch_assoc($resCheck)) {
                $error = "Ya has registrado tu asistencia para este módulo hoy.";
            } else {
                $sqlInsert = "INSERT INTO asistencias (idEstudiante, idModulo, fecha, estado) VALUES (?, ?, ?, 'presente')";
                $stmtInsert = mysqli_prepare($db, $sqlInsert);
                mysqli_stmt_bind_param($stmtInsert, "iis", $idEstudiante, $idModulo, $hoy);
                mysqli_stmt_execute($stmtInsert);
                $exito = "¡Asistencia registrada exitosamente!";
            }
        } else {
            $error = "Este PIN pertenece a un módulo en el que no estás matriculado.";
        }
    } else {
        $error = "PIN inválido o expirado.";
    }
}

$tituloDelPagina = "AulaPro - Registrar Asistencia";
$seccionActual = "asistencia_qr";
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1><i class="fas fa-qrcode"></i> Registrar Asistencia</h1>
    <p class="subtitulo-encabezado">Introduce el PIN mostrado por tu profesor en clase.</p>
</div>

<div style="max-width:500px; margin:40px auto; background:var(--surface); padding:32px; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); text-align:center;">
    
    <div style="font-size:3rem; color:var(--azul); margin-bottom:16px;"><i class="fas fa-mobile-alt"></i></div>
    
    <?php if ($exito): ?>
        <div style="background:var(--verde-suave); color:var(--verde-ink); padding:12px; border-radius:6px; margin-bottom:20px; font-weight:600;">
            <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:var(--rojo-suave); color:var(--rojo-ink); padding:12px; border-radius:6px; margin-bottom:20px; font-weight:600;">
            <i class="fas fa-triangle-exclamation"></i> <?= Security::escapeHtml($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div style="margin-bottom:24px;">
            <input type="text" name="pin" required maxlength="4" placeholder="0000" style="width:200px; font-size:3rem; text-align:center; letter-spacing:8px; border:2px solid var(--border-2); border-radius:12px; padding:16px; color:var(--text); font-weight:700;" autocomplete="off">
        </div>
        <button type="submit" class="boton-primario" style="width:100%; font-size:1.2rem; padding:16px; border-radius:12px;">
            Registrar Presente
        </button>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
