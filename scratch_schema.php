<?php
require_once 'modelos/conectar.php';
$c=obtenerConexion();
$r=$c->query('SHOW CREATE TABLE configuracion_centro');
echo $r->fetch_row()[1];
