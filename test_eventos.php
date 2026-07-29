<?php
require_once __DIR__ . '/modelos/conectar.php';
$con = obtenerConexion();
try {
    $res = mysqli_query($con, "SELECT * FROM eventos WHERE 1=1 AND activo = 1 ORDER BY fechaEvento DESC LIMIT 1");
    echo "Success!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
