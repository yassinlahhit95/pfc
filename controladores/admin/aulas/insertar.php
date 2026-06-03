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
    header("Location: ../../../vistas/admin/aulas/agregarAula.php");
    exit;
}

if (isset($_POST['guardarAula'])) {
    $planta    = (int)($_POST['planta'] ?? -1);
    $numero    = (int)($_POST['numero'] ?? 0);
    $nombre    = trim($_POST['nombreAula'] ?? '');
    $tipo      = $_POST['tipoAula'] ?? 'teoria';
    $capacidad = ($_POST['capacidad'] ?? '') !== '' ? (int)$_POST['capacidad'] : null;
    $activa    = isset($_POST['activa']) ? 1 : 0;
    $nombre    = $nombre !== '' ? $nombre : null;

    $tiposValidos = ['teoria', 'laboratorio', 'taller', 'otro'];

    $errores = '';
    if ($planta < 0 || $planta > 5)            $errores = "La planta debe estar entre 0 y 5.";
    elseif ($numero < 1 || $numero > 99)       $errores = "El número de aula debe estar entre 1 y 99.";
    elseif (!in_array($tipo, $tiposValidos))   $errores = "Tipo de aula no válido.";
    elseif (checkAulaExistente($planta, $numero)) $errores = "Ya existe un aula con esa planta y número.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_aula'] = $_POST;
        header("Location: ../../../vistas/admin/aulas/agregarAula.php");
        exit;
    }

    if (insertarAula($planta, $numero, $nombre, $tipo, $capacidad, $activa)) {
        $_SESSION['exito'] = "Aula creada correctamente.";
        header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo crear el aula.";
    $_SESSION['datos_aula'] = $_POST;
    header("Location: ../../../vistas/admin/aulas/agregarAula.php");
    exit;
}

header("Location: ../../../vistas/admin/aulas/gestionAulas.php");
exit;
