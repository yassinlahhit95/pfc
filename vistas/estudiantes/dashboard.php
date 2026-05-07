<?php
session_start();

if (empty($_SESSION['idEstudiante'])) {
    header("Location: ../login.php");
    exit;
}

header("Location: inicio/dashboard.php");
exit;
?>
