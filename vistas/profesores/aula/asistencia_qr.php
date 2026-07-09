<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/conectar.php";

$idProfesor = $_SESSION['idProfesor'];
$modulos = listarModulosDeProfesor($idProfesor);

$idModuloSeleccionado = intval($_GET['idModulo'] ?? ($modulos[0]['idModulo'] ?? 0));
$moduloSeleccionado = null;

foreach ($modulos as $m) {
    if ($m['idModulo'] == $idModuloSeleccionado) {
        $moduloSeleccionado = $m;
        break;
    }
}

$pinGenerado = '';
$pinExpira = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_pin']) && $moduloSeleccionado) {
    $db = obtenerConexion();
    $pinGenerado = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $expira = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    
    $sql = "UPDATE modulos SET pinAsistencia = ?, pinAsistenciaExpira = ? WHERE idModulo = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $pinGenerado, $expira, $idModuloSeleccionado);
    mysqli_stmt_execute($stmt);
    
    // Generar un evento automático en asistencias (los que no introduzcan PIN se quedarán ausentes por defecto, aunque idealmente es un job cron el que los marca. Por ahora, los que pongan PIN se marcan Presente)
    $pinExpira = $expira;
}

$tituloDelPagina = "AulaPro - Asistencia QR";
$seccionActual = "asistencia_qr";
include_once __DIR__ . "/../comunes/nav.php";
?>
<div class="cabecera">
    <div>
        <h1><i class="fas fa-qrcode"></i> Control de Asistencia</h1>
        <p class="texto-suave">Genera un PIN de asistencia para que los estudiantes lo introduzcan en sus dispositivos.</p>
    </div>
    <a href="../asistencias/registrar.php" class="boton-secundario"><i class="fas fa-clipboard-check"></i> Registro manual</a>
</div>

<div style="max-width:800px; margin:0 auto; display:grid; grid-template-columns:1fr 1fr; gap:24px;">
    <!-- Selector de módulo -->
    <div class="panel">
        <h3 style="margin-top:0;">Seleccionar Módulo</h3>
        <form method="GET">
            <select name="idModulo" class="input-form" onchange="this.form.submit()" style="margin-bottom:16px;">
                <?php foreach($modulos as $m): ?>
                    <option value="<?= $m['idModulo'] ?>" <?= $m['idModulo'] == $idModuloSeleccionado ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($m['nombreModulo']) ?> (<?= Security::escapeHtml($m['nombreCiclo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        
        <?php if ($moduloSeleccionado): ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="generar_pin" value="1">
            <button type="submit" class="boton-primario" style="width:100%; font-size:1.1rem; padding:12px;">
                <i class="fas fa-sync-alt"></i> Generar Nuevo PIN (5 Minutos)
            </button>
        </form>
        <?php endif; ?>
    </div>

    <!-- PIN Viewer -->
    <div class="panel" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
        <?php if ($pinGenerado): ?>
            <h3 style="margin:0 0 8px 0; color:var(--dim);">PIN ACTIVO</h3>
            <div style="font-size:5rem; font-weight:800; color:var(--text); letter-spacing:8px; line-height:1;"><?= $pinGenerado ?></div>
            <p style="color:var(--rojo); font-weight:600; margin-top:16px;">
                Expira a las <?= date('H:i:s', strtotime($pinExpira)) ?>
            </p>
            <p style="font-size:0.9rem; color:var(--dim); margin-top:8px;">Pide a los estudiantes que ingresen este PIN en su portal.</p>
        <?php else: ?>
            <div style="color:var(--border-2); font-size:4rem; margin-bottom:16px;"><i class="fas fa-qrcode"></i></div>
            <p style="color:var(--mut);">Genera un PIN para mostrarlo aquí.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
