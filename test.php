<?php
require 'modelos/conectar.php';
$c = obtenerConexion();
$c->query('ALTER TABLE profesores ADD COLUMN esTutor TINYINT(1) DEFAULT 0');
$c->query('ALTER TABLE profesores ADD COLUMN idCicloTutor INT DEFAULT NULL');
echo $c->error ? $c->error : 'Success';
