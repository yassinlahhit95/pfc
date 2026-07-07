<?php
require_once 'c:/laragon/www/pfc/modelos/conectar.php';
$c = obtenerConexion();
$r = mysqli_query($c, "DESCRIBE gastos");
while ($row = mysqli_fetch_assoc($r)) {
    print_r($row);
}
