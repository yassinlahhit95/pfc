<?php
declare(strict_types=1);

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/conectar.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$con = obtenerConexion();

// Fetch Niveles
$resNiveles = mysqli_query($con, "SELECT idNivel, nombreNivel FROM niveles ORDER BY nombreNivel ASC");
$niveles = [];
if ($resNiveles) {
    while ($row = mysqli_fetch_assoc($resNiveles)) {
        $niveles[] = [
            'idNivel' => (int)$row['idNivel'],
            'nombreNivel' => $row['nombreNivel']
        ];
    }
}

// Fetch Ciclos
$resCiclos = mysqli_query($con, "SELECT idCiclo, idNivel, nombreCiclo, abreviaturaCiclo FROM ciclos ORDER BY nombreCiclo ASC");
$ciclos = [];
if ($resCiclos) {
    while ($row = mysqli_fetch_assoc($resCiclos)) {
        $ciclos[] = [
            'idCiclo' => (int)$row['idCiclo'],
            'idNivel' => (int)$row['idNivel'],
            'nombreCiclo' => $row['nombreCiclo'],
            'abreviaturaCiclo' => $row['abreviaturaCiclo']
        ];
    }
}

// Fetch Grupos
$resGrupos = mysqli_query($con, "SELECT idGrupo, idCiclo, nombreGrupo, anio FROM grupos ORDER BY nombreGrupo ASC");
$grupos = [];
if ($resGrupos) {
    while ($row = mysqli_fetch_assoc($resGrupos)) {
        $grupos[] = [
            'idGrupo' => (int)$row['idGrupo'],
            'idCiclo' => (int)$row['idCiclo'],
            'nombreGrupo' => $row['nombreGrupo'],
            'anio' => $row['anio']
        ];
    }
}

v1Ok([
    'niveles' => $niveles,
    'ciclos' => $ciclos,
    'grupos' => $grupos,
]);
