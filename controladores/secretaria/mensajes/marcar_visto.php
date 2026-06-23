<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

if ($idReclamacion > 0) {
    marcarMensajeComoLeido($idReclamacion);
}

if ($_isAjaxGuardSec) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}
header("Location: ../../../vistas/secretaria/mensajes/lista.php");
exit;
