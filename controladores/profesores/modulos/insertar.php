<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/modulos.php';

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

if (!$esTutor || !$idCicloTutor) {
    header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
}

if (isset($_POST['guardarModulo'])) {
    $nombre = trim($_POST['nombreModulo'] ?? '');
    $horas  = (int)($_POST['horasMaximas'] ?? 0);

    $errores = [];
    if (empty($nombre))  $errores[] = "El nombre del módulo es obligatorio.";
    if ($horas <= 0)     $errores[] = "Las horas máximas deben ser un número mayor que 0.";
    if (checkModuloExistente($nombre, $idCicloTutor)) {
        $errores[] = "Ya existe un módulo con ese nombre en este ciclo.";
    }

    if (empty($errores)) {
        $newId = insertarModulo($nombre, $idCicloTutor, $horas);
        if ($newId) {
            $_SESSION['exito'] = "Módulo «{$nombre}» creado correctamente.";
            header("Location: ../../../vistas/profesores/aula/recursos.php?id={$newId}"); exit;
        }
        $errores[] = "Error al crear el módulo. Inténtalo de nuevo.";
    }

    $_SESSION['errores'] = implode(' ', $errores);
}

header("Location: ../../../vistas/profesores/modulos/agregar.php");
exit;
