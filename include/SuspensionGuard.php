<?php
/**
 * SuspensionGuard — blocks access when the instance is suspended by the SaaS admin.
 * Include AFTER the role-auth check in EstudianteGuard, ProfesorGuard, TutorGuard.
 * Do NOT include in AdminGuard so directors can still log in.
 */

// Cache in session for 5 minutes to avoid a DB hit on every page load
if (!isset($_SESSION['_suspension_checked']) || $_SESSION['_suspension_checked'] < time() - 300) {
    require_once __DIR__ . '/../modelos/conectar.php';
    $con       = obtenerConexion();
    $res       = mysqli_query($con, "SELECT instance_status, suspension_message FROM configuracion_centro WHERE idConfig = 1 LIMIT 1");
    $cfg       = $res ? mysqli_fetch_assoc($res) : null;
    $suspended = $cfg && ($cfg['instance_status'] ?? 'active') === 'suspended';

    $_SESSION['_suspension_checked'] = time();
    $_SESSION['_suspended']          = $suspended;
    $_SESSION['_suspension_message'] = $cfg['suspension_message'] ?? '';
}

if (!empty($_SESSION['_suspended'])) {
    require __DIR__ . '/../vistas/suspendido.php';
    exit;
}
