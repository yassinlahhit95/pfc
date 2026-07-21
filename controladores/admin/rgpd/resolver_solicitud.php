<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/rgpd.php';

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud inválida. Inténtelo de nuevo.";
    header("Location: ../../../vistas/admin/rgpd/index.php");
    exit;
}

$idSolicitud = (int)($_POST['idSolicitud'] ?? 0);
if ($idSolicitud > 0 && resolverSolicitudRGPD($idSolicitud, (int)$_SESSION['idAdmin'])) {
    $_SESSION['exito'] = "Solicitud marcada como resuelta.";
} else {
    $_SESSION['errores'] = "No se pudo resolver la solicitud.";
}
header("Location: ../../../vistas/admin/rgpd/index.php");
exit;
