<?php
// mock session
session_start();
$_SESSION['idProfesor'] = 1;
$_SESSION['esTutor'] = 0;
// Test redirect behavior
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['id'] = 3;
$_GET['carpeta'] = 1;

require 'vistas/profesores/aula/recursos.php';
