<?php
session_start();
$_SESSION['idProfesor'] = 1;
$_GET['id'] = 1;
include('vistas/profesores/aula/recursos.php');
