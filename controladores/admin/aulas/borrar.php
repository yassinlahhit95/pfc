<?php
session_start();
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
    header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
    exit;
}

if (isset($_POST['idAula'])) {
    $idAula = (int)$_POST['idAula'];
    if (eliminarAula($idAula)) {
        $_SESSION['exito'] = "Aula eliminada.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el aula.";
    }
}

header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
