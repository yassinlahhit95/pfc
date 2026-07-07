<?php
ob_start(); // To prevent header errors if any
require 'vistas/profesores/aula/recursos.php';
$headers = headers_list();
print_r($headers);
