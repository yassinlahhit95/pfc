<?php
require 'modelos/conectar.php';
$c = obtenerConexion();
$r = mysqli_query($c, 'DESCRIBE articulos');
print_r(mysqli_fetch_all($r, MYSQLI_ASSOC));
