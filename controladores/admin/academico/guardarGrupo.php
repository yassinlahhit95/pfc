<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/grupos.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/gestionarGrupos.php");
        exit;
    }

    $idGrupo = (int)($_POST['idGrupo'] ?? 0);
    $nombreGrupo = trim($_POST['nombreGrupo'] ?? '');
    $idCiclo = (int)($_POST['idCiclo'] ?? 0);
    $anioEstudio = trim($_POST['anioEstudio'] ?? '');

    if (empty($nombreGrupo) || $idCiclo <= 0 || empty($anioEstudio)) {
        $_SESSION['errores'] = 'Todos los campos son obligatorios.';
        header("Location: ../../../vistas/admin/academico/gestionarGrupos.php");
        exit;
    }

    if ($idGrupo > 0) {
        // Actualizar
        if (actualizarGrupo($idGrupo, $nombreGrupo, $idCiclo, $anioEstudio)) {
            $_SESSION['exito'] = 'Grupo actualizado con éxito.';
        } else {
            $_SESSION['errores'] = 'Error al actualizar el grupo.';
        }
    } else {
        // Insert
        if (insertarGrupo($nombreGrupo, $idCiclo, $anioEstudio)) {
            $_SESSION['exito'] = 'Grupo creado con éxito.';
        } else {
            $_SESSION['errores'] = 'Error al crear el grupo.';
        }
    }

    header("Location: ../../../vistas/admin/academico/gestionarGrupos.php");
    exit;
}
