<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../include/Security.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (isset($_POST['actualizarCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);
    $nombre = trim($_POST['nombreCiclo']);
    $abreviatura = trim($_POST['abreviaturaCiclo']);
    $idNivelEducativo = trim($_POST['idNivel']);
    $precioCiclo = trim($_POST['precioCiclo']);
    $profesores = $_POST['profesores'] ?? [];

    $errores = '';
    if (empty($nombre)) $errores = "Nombre obligatorio.";
    elseif (empty($abreviatura)) $errores = "Abreviatura obligatoria.";
    elseif (!is_numeric($precioCiclo) || $precioCiclo < 0) $errores = "El precio debe ser un número válido.";
    elseif (checkCicloExistente($nombre, $abreviatura, $idCiclo)) $errores = "El nombre o la abreviatura ya están en uso.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclos'] = $_POST;
        header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
        exit;
    }

    if (actualizarCicloExistente($idCiclo, $nombre, $abreviatura, $idNivelEducativo, $profesores, $precioCiclo)) {
        $_SESSION['exito'] = "Ciclo actualizado correctamente.";
        header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
        exit;
    }
    $_SESSION['errores'] = "No se realizaron cambios o no se pudo actualizar el ciclo en la base de datos.";
    header("Location: ../../../vistas/admin/ciclos/modificarCiclos.php?idCiclo=" . $idCiclo);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>
