<?php
// Bloquea el acceso cuando la instancia está suspendida por el administrador SaaS.
// Incluir DESPUÉS de la comprobación de rol en EstudianteGuard, ProfesorGuard, TutorGuard.
// NO incluir en AdminGuard para que los directores puedan seguir accediendo.

require_once __DIR__ . '/FeatureGuard.php';

// Sincroniza con FeatureGuard, que gestiona la validación del token, el TTL de caché y fail-closed.
$suspended = FeatureGuard::isSuspended();
$_SESSION['_suspended']          = $suspended;
$_SESSION['_suspension_message'] = FeatureGuard::getSuspensionMessage();

if ($suspended) {
    require __DIR__ . '/../vistas/suspendido.php';
    exit;
}
