<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/modulos.php';

$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

if (!$esTutor || !$idCicloTutor) {
    header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
}

if (isset($_POST['actualizarModulo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
    }
    $idModulo = (int)($_POST['idModulo'] ?? 0);
    $nombre   = trim($_POST['nombreModulo'] ?? '');
    $horas    = (int)($_POST['horasMaximas'] ?? 0);

    if (!$idModulo || !moduloPerteneceACiclo($idModulo, $idCicloTutor)) {
        $_SESSION['errores'] = "No tienes permiso sobre este módulo.";
        header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
    }

    $errores = [];
    if (empty($nombre)) $errores[] = "El nombre del módulo es obligatorio.";
    if ($horas <= 0)    $errores[] = "Las horas máximas deben ser un número mayor que 0.";
    if (checkModuloExistente($nombre, $idCicloTutor, $idModulo)) {
        $errores[] = "Ya existe otro módulo con ese nombre en este ciclo.";
    }

    if (empty($errores)) {
        if (actualizarModulo($idModulo, $nombre, $idCicloTutor, $horas)) {
            $_SESSION['exito'] = "Módulo actualizado correctamente.";
            header("Location: ../../../vistas/profesores/modulos/lista.php"); exit;
        }
        $errores[] = "Error al actualizar el módulo.";
    }

    $_SESSION['errores'] = implode(' ', $errores);
    header("Location: ../../../vistas/profesores/modulos/editar.php?idModulo={$idModulo}"); exit;
}

header("Location: ../../../vistas/profesores/modulos/lista.php");
exit;
