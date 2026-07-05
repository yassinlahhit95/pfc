<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/fp_dual.php";
require_once __DIR__ . "/../../../modelos/log.php";

$idEmpresa = (int)($_GET['idEmpresa'] ?? 0);

if ($idEmpresa > 0 && eliminarEmpresa($idEmpresa)) {
    registrarAccion('eliminar', 'fp_empresas', $idEmpresa, 'Empresa eliminada');
    $_SESSION['exito'] = "La empresa ha sido eliminada correctamente.";
} else {
    $_SESSION['errores'] = "Ocurrió un error al intentar eliminar la empresa.";
}

header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
exit;
