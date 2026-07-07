<?php require 'modelos/conectar.php'; $c=obtenerConexion(); $res=mysqli_query($c, 'SELECT count(*) as c FROM cola_emails'); print_r(mysqli_fetch_assoc($res));
