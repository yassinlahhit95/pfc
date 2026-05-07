<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../login.php");
    exit;
}

header("Location: inicio/dashboard.php");
exit;
?>
