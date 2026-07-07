<?php require 'modelos/conectar.php'; $c=obtenerConexion(); $res=mysqli_query($c, 'DESCRIBE historial_secretarias'); print_r(mysqli_fetch_all($res, MYSQLI_ASSOC));
