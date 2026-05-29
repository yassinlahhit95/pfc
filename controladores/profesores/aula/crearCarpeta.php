<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idProfesor = $_SESSION['idProfesor'];
$idModulo   = intval($_POST['idModulo'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$color      = trim($_POST['color'] ?? '#0ea5e9');

if ($idModulo > 0 && !empty($nombre)) {
    if (insertarCarpetaAula($nombre, $idModulo, $idProfesor, $color)) {
        $_SESSION['exito'] = "Carpeta creada.";
    } else {
        $_SESSION['errores'] = "No se pudo crear la carpeta.";
    }
}
header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
