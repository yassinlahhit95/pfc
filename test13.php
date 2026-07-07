<?php require 'modelos/conectar.php'; $c=obtenerConexion(); $res=mysqli_query($c, 'DESCRIBE cola_emails'); print_r(mysqli_fetch_all($res, MYSQLI_ASSOC));
