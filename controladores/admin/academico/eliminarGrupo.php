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

    if ($idGrupo <= 0) {
        $_SESSION['errores'] = 'Grupo no especificado.';
        header("Location: ../../../vistas/admin/academico/gestionarGrupos.php");
        exit;
    }

    if (eliminarGrupo($idGrupo)) {
        $_SESSION['exito'] = 'Grupo eliminado con éxito.';
    } else {
        $_SESSION['errores'] = 'Error al eliminar el grupo.';
    }

    header("Location: ../../../vistas/admin/academico/gestionarGrupos.php");
    exit;
}
