<?php
require_once 'modelos/conectar.php';
$c=obtenerConexion();
$r=$c->query('SHOW CREATE TABLE eventos');
if ($r) {
    echo $r->fetch_row()[1];
} else {
    echo $c->error;
}
