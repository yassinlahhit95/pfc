<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['idProfesor']) && !isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}
?>