<?php
declare(strict_types=1);
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$usuario = v1Auth();

require_once __DIR__ . "/../../modelos/grupos.php";

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$anioEstudio = trim($_GET['anioEstudio'] ?? '');

if ($idCiclo <= 0 || empty($anioEstudio)) {
    v1Error('idCiclo y anioEstudio requeridos', 400, 'invalid_request');
}

$grupos = listarGruposPorCicloYAnio($idCiclo, $anioEstudio);
v1Ok(['grupos' => $grupos]);
