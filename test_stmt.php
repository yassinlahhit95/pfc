<?php
require_once __DIR__ . '/modelos/conectar.php';
$con = obtenerConexion();
$st = mysqli_prepare($con, "SELECT 1 AS val UNION ALL SELECT 2 UNION ALL SELECT 3");
mysqli_stmt_execute($st);

$results = [];
while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) {
    $results[] = $row;
}
print_r($results);
