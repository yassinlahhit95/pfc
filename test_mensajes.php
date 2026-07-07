<?php
require 'vendor/autoload.php';
require 'modelos/reclamaciones.php';
$mensajes = listarTodosLosMensajes(10);
print_r($mensajes);
