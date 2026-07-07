<?php require 'modelos/conectar.php'; $c=obtenerConexion(); $res=mysqli_query($c, 'DESCRIBE log_secretaria_acciones'); print_r(mysqli_fetch_all($res, MYSQLI_ASSOC));
